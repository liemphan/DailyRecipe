<?php

namespace DailyRecipe\Auth\Permissions;

use DailyRecipe\Auth\Role;
use DailyRecipe\Auth\User;
use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Models\RecipeChild;
use DailyRecipe\Entities\Models\Recipemenu;
use DailyRecipe\Entities\Models\Entity;
use DailyRecipe\Model;
use DailyRecipe\Traits\HasCreatorAndUpdater;
use DailyRecipe\Traits\HasOwner;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Throwable;

class PermissionService
{
    /**
     * @var ?array
     */
    protected $userRoles = null;

    /**
     * @var ?User
     */
    protected $currentUserModel = null;

    /**
     * @var Connection
     */
    protected $db;

    /**
     * @var array
     */
    protected $entityCache;

    /**
     * PermissionService constructor.
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * Set the database connection.
     */
    public function setConnection(Connection $connection)
    {
        $this->db = $connection;
    }

    /**
     * Prepare the local entity cache and ensure it's empty.
     *
     * @param Entity[] $entities
     */
    protected function readyEntityCache(array $entities = [])
    {
        $this->entityCache = [];

        foreach ($entities as $entity) {
            $class = get_class($entity);
            if (!isset($this->entityCache[$class])) {
                $this->entityCache[$class] = collect();
            }
            $this->entityCache[$class]->put($entity->id, $entity);
        }
    }

    /**
     * Get a recipe via ID, Checks local cache.
     */
    protected function getRecipe(int $recipeId): ?Recipe
    {
        if (isset($this->entityCache[Recipe::class]) && $this->entityCache[Recipe::class]->has($recipeId)) {
            return $this->entityCache[Recipe::class]->get($recipeId);
        }

        return Recipe::query()->withTrashed()->find($recipeId);
    }

//    /**
//     * Get a chapter via ID, Checks local cache.
//     */
//    protected function getChapter(int $chapterId): ?Chapter
//    {
//        if (isset($this->entityCache[Chapter::class]) && $this->entityCache[Chapter::class]->has($chapterId)) {
//            return $this->entityCache[Chapter::class]->get($chapterId);
//        }
//
//        return Chapter::query()
//            ->withTrashed()
//            ->find($chapterId);
//    }

    /**
     * Get the roles for the current logged in user.
     */
    protected function getCurrentUserRoles(): array
    {
        if (!is_null($this->userRoles)) {
            return $this->userRoles;
        }

        if (auth()->guest()) {
            $this->userRoles = [Role::getSystemRole('public')->id];
        } else {
            $this->userRoles = $this->currentUser()->roles->pluck('id')->values()->all();
        }

        return $this->userRoles;
    }

    /**
     * Re-generate all entity permission from scratch.
     */
    public function buildJointPermissions()
    {
        JointPermission::query()->truncate();
        $this->readyEntityCache();

        // Get all roles (Should be the most limited dimension)
        $roles = Role::query()->with('permissions')->get()->all();

        // Chunk through all recipes
        $this->recipeFetchQuery()->chunk(5, function (EloquentCollection $recipes) use ($roles) {
            $this->buildJointPermissionsForRecipes($recipes, $roles);
        });

        // Chunk through all recipemenus
        Recipemenu::query()->withTrashed()->select(['id', 'restricted', 'owned_by'])
            ->chunk(50, function (EloquentCollection $menus) use ($roles) {
                $this->buildJointPermissionsForMenus($menus, $roles);
            });
    }

    /**
     * Get a query for fetching a recipe with it's children.
     */
    protected function recipeFetchQuery(): Builder
    {
        return Recipe::query()->withTrashed()
            ->select(['id', 'restricted', 'owned_by'])->with([
//                'chapters' => function ($query) {
//                    $query->withTrashed()->select(['id', 'restricted', 'owned_by', 'recipe_id']);
//                },
//                'pages' => function ($query) {
//                    $query->withTrashed()->select(['id', 'restricted', 'owned_by', 'recipe_id', 'chapter_id']);
//                },
            ]);
    }

    /**
     * Build joint permissions for the given menu and role combinations.
     *
     * @throws Throwable
     */
    protected function buildJointPermissionsForMenus(EloquentCollection $menus, array $roles, bool $deleteOld = false)
    {
        if ($deleteOld) {
            $this->deleteManyJointPermissionsForEntities($menus->all());
        }
        $this->createManyJointPermissions($menus->all(), $roles);
    }

    /**
     * Build joint permissions for the given recipe and role combinations.
     *
     * @throws Throwable
     */
    protected function buildJointPermissionsForRecipes(EloquentCollection $recipes, array $roles, bool $deleteOld = false)
    {
        $entities = clone $recipes;

        /** @var Recipe $recipe */
//        foreach ($recipes->all() as $recipe) {
////            foreach ($recipe->getRelation('chapters') as $chapter) {
////                $entities->push($chapter);
////            }
////            foreach ($recipe->getRelation('pages') as $page) {
////                $entities->push($page);
////            }
//        }

        if ($deleteOld) {
            $this->deleteManyJointPermissionsForEntities($entities->all());
        }
        $this->createManyJointPermissions($entities->all(), $roles);
    }

    /**
     * Rebuild the entity jointPermissions for a particular entity.
     *
     * @throws Throwable
     */
    public function buildJointPermissionsForEntity(Entity $entity)
    {
        $entities = [$entity];
        if ($entity instanceof Recipe) {
            $recipes = $this->recipeFetchQuery()->where('id', '=', $entity->id)->get();
            $this->buildJointPermissionsForRecipes($recipes, Role::query()->get()->all(), true);

            return;
        }

        /** @var RecipeChild $entity */
        if ($entity->recipe) {
            $entities[] = $entity->recipe;
        }

//        if ($entity instanceof Page && $entity->chapter_id) {
//            $entities[] = $entity->chapter;
//        }

//        if ($entity instanceof Chapter) {
//            foreach ($entity->pages as $page) {
//                $entities[] = $page;
//            }
//        }

        $this->buildJointPermissionsForEntities($entities);
    }

    /**
     * Rebuild the entity jointPermissions for a collection of entities.
     *
     * @throws Throwable
     */
    public function buildJointPermissionsForEntities(array $entities)
    {
        $roles = Role::query()->get()->values()->all();
        $this->deleteManyJointPermissionsForEntities($entities);
        $this->createManyJointPermissions($entities, $roles);
    }

    /**
     * Build the entity jointPermissions for a particular role.
     */
    public function buildJointPermissionForRole(Role $role)
    {
        $roles = [$role];
        $this->deleteManyJointPermissionsForRoles($roles);

        // Chunk through all recipes
        $this->recipeFetchQuery()->chunk(20, function ($recipes) use ($roles) {
            $this->buildJointPermissionsForRecipes($recipes, $roles);
        });

        // Chunk through all recipemenus
        Recipemenu::query()->select(['id', 'restricted', 'owned_by'])
            ->chunk(50, function ($menus) use ($roles) {
                $this->buildJointPermissionsForMenus($menus, $roles);
            });
    }

    /**
     * Delete the entity jointPermissions attached to a particular role.
     */
    public function deleteJointPermissionsForRole(Role $role)
    {
        $this->deleteManyJointPermissionsForRoles([$role]);
    }

    /**
     * Delete all of the entity jointPermissions for a list of entities.
     *
     * @param Role[] $roles
     */
    protected function deleteManyJointPermissionsForRoles($roles)
    {
        $roleIds = array_map(function ($role) {
            return $role->id;
        }, $roles);
        JointPermission::query()->whereIn('role_id', $roleIds)->delete();
    }

    /**
     * Delete the entity jointPermissions for a particular entity.
     *
     * @param Entity $entity
     *
     * @throws Throwable
     */
    public function deleteJointPermissionsForEntity(Entity $entity)
    {
        $this->deleteManyJointPermissionsForEntities([$entity]);
    }

    /**
     * Delete all of the entity jointPermissions for a list of entities.
     *
     * @param Entity[] $entities
     *
     * @throws Throwable
     */
    protected function deleteManyJointPermissionsForEntities(array $entities)
    {
        if (count($entities) === 0) {
            return;
        }

        $this->db->transaction(function () use ($entities) {
            foreach (array_chunk($entities, 1000) as $entityChunk) {
                $query = $this->db->table('joint_permissions');
                foreach ($entityChunk as $entity) {
                    $query->orWhere(function (QueryBuilder $query) use ($entity) {
                        $query->where('entity_id', '=', $entity->id)
                            ->where('entity_type', '=', $entity->getMorphClass());
                    });
                }
                $query->delete();
            }
        });
    }

    /**
     * Create & Save entity jointPermissions for many entities and roles.
     *
     * @param Entity[] $entities
     * @param Role[] $roles
     *
     * @throws Throwable
     */
    protected function createManyJointPermissions(array $entities, array $roles)
    {
        $this->readyEntityCache($entities);
        $jointPermissions = [];

        // Fetch Entity Permissions and create a mapping of entity restricted statuses
        $entityRestrictedMap = [];
        $permissionFetch = EntityPermission::query();
        foreach ($entities as $entity) {
            $entityRestrictedMap[$entity->getMorphClass() . ':' . $entity->id] = boolval($entity->getRawAttribute('restricted'));
            $permissionFetch->orWhere(function ($query) use ($entity) {
                $query->where('restrictable_id', '=', $entity->id)->where('restrictable_type', '=', $entity->getMorphClass());
            });
        }
        $permissions = $permissionFetch->get();

        // Create a mapping of explicit entity permissions
        $permissionMap = [];
        foreach ($permissions as $permission) {
            $key = $permission->restrictable_type . ':' . $permission->restrictable_id . ':' . $permission->role_id . ':' . $permission->action;
            $isRestricted = $entityRestrictedMap[$permission->restrictable_type . ':' . $permission->restrictable_id];
            $permissionMap[$key] = $isRestricted;
        }

        // Create a mapping of role permissions
        $rolePermissionMap = [];
        foreach ($roles as $role) {
            foreach ($role->permissions as $permission) {
                $rolePermissionMap[$role->getRawAttribute('id') . ':' . $permission->getRawAttribute('name')] = true;
            }
        }

        // Create Joint Permission Data
        foreach ($entities as $entity) {
            foreach ($roles as $role) {
                foreach ($this->getActions($entity) as $action) {
                    $jointPermissions[] = $this->createJointPermissionData($entity, $role, $action, $permissionMap, $rolePermissionMap);
                }
            }
        }

        $this->db->transaction(function () use ($jointPermissions) {
            foreach (array_chunk($jointPermissions, 1000) as $jointPermissionChunk) {
                $this->db->table('joint_permissions')->insert($jointPermissionChunk);
            }
        });
    }

    /**
     * Get the actions related to an entity.
     */
    protected function getActions(Entity $entity): array
    {
        $baseActions = ['view', 'update', 'delete'];
        if ($entity instanceof Recipe) {
            $baseActions[] = 'page-create';
        }
//        if ($entity instanceof Recipe) {
//            $baseActions[] = 'chapter-create';
//        }

        return $baseActions;
    }

    /**
     * Create entity permission data for an entity and role
     * for a particular action.
     */
    protected function createJointPermissionData(Entity $entity, Role $role, string $action, array $permissionMap, array $rolePermissionMap): array
    {
        $permissionPrefix = (strpos($action, '-') === false ? ($entity->getType() . '-') : '') . $action;
        $roleHasPermission = isset($rolePermissionMap[$role->getRawAttribute('id') . ':' . $permissionPrefix . '-all']);
        $roleHasPermissionOwn = isset($rolePermissionMap[$role->getRawAttribute('id') . ':' . $permissionPrefix . '-own']);
        $explodedAction = explode('-', $action);
        $restrictionAction = end($explodedAction);

        if ($role->system_name === 'admin') {
            return $this->createJointPermissionDataArray($entity, $role, $action, true, true);
        }

        if ($entity->restricted) {
            $hasAccess = $this->mapHasActiveRestriction($permissionMap, $entity, $role, $restrictionAction);

            return $this->createJointPermissionDataArray($entity, $role, $action, $hasAccess, $hasAccess);
        }

        if ($entity instanceof Recipe || $entity instanceof Recipemenu) {
            return $this->createJointPermissionDataArray($entity, $role, $action, $roleHasPermission, $roleHasPermissionOwn);
        }

        // For chapters and pages, Check if explicit permissions are set on the Recipe.
        $recipe = $this->getRecipe($entity->recipe_id);
        $hasExplicitAccessToParents = $this->mapHasActiveRestriction($permissionMap, $recipe, $role, $restrictionAction);
        $hasPermissiveAccessToParents = !$recipe->restricted;

        // For pages with a chapter, Check if explicit permissions are set on the Chapter
//        if ($entity instanceof Page && intval($entity->chapter_id) !== 0) {
//            $chapter = $this->getChapter($entity->chapter_id);
//            $hasPermissiveAccessToParents = $hasPermissiveAccessToParents && !$chapter->restricted;
//            if ($chapter->restricted) {
//                $hasExplicitAccessToParents = $this->mapHasActiveRestriction($permissionMap, $chapter, $role, $restrictionAction);
//            }
//        }

        return $this->createJointPermissionDataArray(
            $entity,
            $role,
            $action,
            ($hasExplicitAccessToParents || ($roleHasPermission && $hasPermissiveAccessToParents)),
            ($hasExplicitAccessToParents || ($roleHasPermissionOwn && $hasPermissiveAccessToParents))
        );
    }

    /**
     * Check for an active restriction in an entity map.
     */
    protected function mapHasActiveRestriction(array $entityMap, Entity $entity, Role $role, string $action): bool
    {
        $key = $entity->getMorphClass() . ':' . $entity->getRawAttribute('id') . ':' . $role->getRawAttribute('id') . ':' . $action;

        return $entityMap[$key] ?? false;
    }

    /**
     * Create an array of data with the information of an entity jointPermissions.
     * Used to build data for bulk insertion.
     */
    protected function createJointPermissionDataArray(Entity $entity, Role $role, string $action, bool $permissionAll, bool $permissionOwn): array
    {
        return [
            'role_id' => $role->getRawAttribute('id'),
            'entity_id' => $entity->getRawAttribute('id'),
            'entity_type' => $entity->getMorphClass(),
            'action' => $action,
            'has_permission' => $permissionAll,
            'has_permission_own' => $permissionOwn,
            'owned_by' => $entity->getRawAttribute('owned_by'),
        ];
    }

    /**
     * Checks if an entity has a restriction set upon it.
     *
     * @param HasCreatorAndUpdater|HasOwner $ownable
     */
    public function checkOwnableUserAccess(Model $ownable, string $permission): bool
    {
        $explodedPermission = explode('-', $permission);

        $baseQuery = $ownable->newQuery()->where('id', '=', $ownable->id);
        $action = end($explodedPermission);
        $user = $this->currentUser();

        $nonJointPermissions = ['restrictions', 'image', 'attachment', 'comment'];

        // Handle non entity specific jointPermissions
        if (in_array($explodedPermission[0], $nonJointPermissions)) {
            $allPermission = $user && $user->can($permission . '-all');
            $ownPermission = $user && $user->can($permission . '-own');
            $ownerField = ($ownable instanceof Entity) ? 'owned_by' : 'created_by';
            $isOwner = $user && $user->id === $ownable->$ownerField;

            return $allPermission || ($isOwner && $ownPermission);
        }

        // Handle abnormal create jointPermissions
        if ($action === 'create') {
            $action = $permission;
        }

        $hasAccess = $this->entityRestrictionQuery($baseQuery, $action)->count() > 0;
        $this->clean();

        return $hasAccess;
    }

    /**
     * Checks if a user has the given permission for any items in the system.
     * Can be passed an entity instance to filter on a specific type.
     */
    public function checkUserHasPermissionOnAnything(string $permission, ?string $entityClass = null): bool
    {
        $userRoleIds = $this->currentUser()->roles()->select('id')->pluck('id')->toArray();
        $userId = $this->currentUser()->id;

        $permissionQuery = JointPermission::query()
            ->where('action', '=', $permission)
            ->whereIn('role_id', $userRoleIds)
            ->where(function (Builder $query) use ($userId) {
                $this->addJointHasPermissionCheck($query, $userId);
            });

        if (!is_null($entityClass)) {
            $entityInstance = app($entityClass);
            $permissionQuery = $permissionQuery->where('entity_type', '=', $entityInstance->getMorphClass());
        }

        $hasPermission = $permissionQuery->count() > 0;
        $this->clean();

        return $hasPermission;
    }

    /**
     * The general query filter to remove all entities
     * that the current user does not have access to.
     */
    protected function entityRestrictionQuery(Builder $query, string $action): Builder
    {
        $q = $query->where(function ($parentQuery) use ($action) {
            $parentQuery->whereHas('jointPermissions', function ($permissionQuery) use ($action) {
                $permissionQuery->whereIn('role_id', $this->getCurrentUserRoles())
                    ->where('action', '=', $action)
                    ->where(function (Builder $query) {
                        $this->addJointHasPermissionCheck($query, $this->currentUser()->id);
                    });
            });
        });

        $this->clean();

        return $q;
    }

    /**
     * Limited the given entity query so that the query will only
     * return items that the user has permission for the given ability.
     */
    public function restrictEntityQuery(Builder $query, string $ability = 'view'): Builder
    {
        $this->clean();

        return $query->where(function (Builder $parentQuery) use ($ability) {
            $parentQuery->whereHas('jointPermissions', function (Builder $permissionQuery) use ($ability) {
                $permissionQuery->whereIn('role_id', $this->getCurrentUserRoles())
                    ->where('action', '=', $ability)
                    ->where(function (Builder $query) {
                        $this->addJointHasPermissionCheck($query, $this->currentUser()->id);
                    });
            });
        });
    }

    /**
     * Extend the given page query to ensure draft items are not visible
     * unless created by the given user.
     */
    public function enforceDraftVisibilityOnQuery(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query->where('draft', '=', false)
                ->orWhere(function (Builder $query) {
                    $query->where('draft', '=', true)
                        ->where('owned_by', '=', $this->currentUser()->id);
                });
        });
    }

    /**
     * Add restrictions for a generic entity.
     */
    public function enforceEntityRestrictions(Entity $entity, Builder $query, string $action = 'view'): Builder
    {
        if ($entity instanceof Recipe) {
            // Prevent drafts being visible to others.
            $this->enforceDraftVisibilityOnQuery($query);
        }

        return $this->entityRestrictionQuery($query, $action);
    }

    /**
     * Filter items that have entities set as a polymorphic relation.
     *
     * @param Builder|QueryBuilder $query
     */
    public function filterRestrictedEntityRelations($query, string $tableName, string $entityIdColumn, string $entityTypeColumn, string $action = 'view')
    {
        $tableDetails = ['tableName' => $tableName, 'entityIdColumn' => $entityIdColumn, 'entityTypeColumn' => $entityTypeColumn];

        $q = $query->where(function ($query) use ($tableDetails, $action) {
            $query->whereExists(function ($permissionQuery) use (&$tableDetails, $action) {
                /** @var Builder $permissionQuery */
                $permissionQuery->select(['role_id'])->from('joint_permissions')
                    ->whereColumn('joint_permissions.entity_id', '=', $tableDetails['tableName'] . '.' . $tableDetails['entityIdColumn'])
                    ->whereColumn('joint_permissions.entity_type', '=', $tableDetails['tableName'] . '.' . $tableDetails['entityTypeColumn'])
                    ->where('action', '=', $action)
                    ->whereIn('role_id', $this->getCurrentUserRoles())
                    ->where(function (QueryBuilder $query) {
                        $this->addJointHasPermissionCheck($query, $this->currentUser()->id);
                    });
            });
        });

        $this->clean();

        return $q;
    }

    /**
     * Add conditions to a query to filter the selection to related entities
     * where view permissions are granted.
     */
    public function filterRelatedEntity(string $entityClass, Builder $query, string $tableName, string $entityIdColumn): Builder
    {
        $tableDetails = ['tableName' => $tableName, 'entityIdColumn' => $entityIdColumn];
        $morphClass = app($entityClass)->getMorphClass();

        $q = $query->where(function ($query) use ($tableDetails, $morphClass) {
            $query->where(function ($query) use (&$tableDetails, $morphClass) {
                $query->whereExists(function ($permissionQuery) use (&$tableDetails, $morphClass) {
                    /** @var Builder $permissionQuery */
                    $permissionQuery->select('id')->from('joint_permissions')
                        ->whereColumn('joint_permissions.entity_id', '=', $tableDetails['tableName'] . '.' . $tableDetails['entityIdColumn'])
                        ->where('entity_type', '=', $morphClass)
                        ->where('action', '=', 'view')
                        ->whereIn('role_id', $this->getCurrentUserRoles())
                        ->where(function (QueryBuilder $query) {
                            $this->addJointHasPermissionCheck($query, $this->currentUser()->id);
                        });
                });
            })->orWhere($tableDetails['entityIdColumn'], '=', 0);
        });

        $this->clean();

        return $q;
    }

    /**
     * Add the query for checking the given user id has permission
     * within the join_permissions table.
     *
     * @param QueryBuilder|Builder $query
     */
    protected function addJointHasPermissionCheck($query, int $userIdToCheck)
    {
        $query->where('has_permission', '=', true)->orWhere(function ($query) use ($userIdToCheck) {
            $query->where('has_permission_own', '=', true)
                ->where('owned_by', '=', $userIdToCheck);
        });
    }

    /**
     * Get the current user.
     */
    private function currentUser(): User
    {
        if (is_null($this->currentUserModel)) {
            $this->currentUserModel = user();
        }

        return $this->currentUserModel;
    }

    /**
     * Clean the cached user elements.
     */
    private function clean(): void
    {
        $this->currentUserModel = null;
        $this->userRoles = null;
    }
}
