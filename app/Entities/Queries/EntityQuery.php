<?php

namespace DailyRecipe\Entities\Queries;

use DailyRecipe\Auth\Permissions\PermissionService;
use DailyRecipe\Entities\EntityProvider;

abstract class EntityQuery
{
    protected function permissionService(): PermissionService
    {
        return app()->make(PermissionService::class);
    }

    protected function entityProvider(): EntityProvider
    {
        return app()->make(EntityProvider::class);
    }
}
