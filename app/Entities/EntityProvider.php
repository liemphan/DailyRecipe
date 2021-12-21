<?php

namespace DailyRecipe\Entities;

use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Models\Recipemenus;
use DailyRecipe\Entities\Models\Chapter;
use DailyRecipe\Entities\Models\Entity;
use DailyRecipe\Entities\Models\Page;
use DailyRecipe\Entities\Models\PageRevision;

/**
 * Class EntityProvider.
 *
 * Provides access to the core entity models.
 * Wrapped up in this provider since they are often used together
 * so this is a neater alternative to injecting all in individually.
 */
class EntityProvider
{
    /**
     * @var Recipemenus
     */
    public $bookshelf;

    /**
     * @var Recipe
     */
    public $book;

    /**
     * @var Chapter
     */
    public $chapter;

    /**
     * @var Page
     */
    public $page;

    /**
     * @var PageRevision
     */
    public $pageRevision;

    public function __construct()
    {
        $this->bookshelf = new Recipemenus();
        $this->book = new Recipe();
        $this->chapter = new Chapter();
        $this->page = new Page();
        $this->pageRevision = new PageRevision();
    }

    /**
     * Fetch all core entity types as an associated array
     * with their basic names as the keys.
     *
     * @return array<Entity>
     */
    public function all(): array
    {
        return [
            'bookshelf' => $this->bookshelf,
            'book'      => $this->book,
            'chapter'   => $this->chapter,
            'page'      => $this->page,
        ];
    }

    /**
     * Get an entity instance by it's basic name.
     */
    public function get(string $type): Entity
    {
        $type = strtolower($type);

        return $this->all()[$type];
    }

    /**
     * Get the morph classes, as an array, for a single or multiple types.
     */
    public function getMorphClasses(array $types): array
    {
        $morphClasses = [];
        foreach ($types as $type) {
            $model = $this->get($type);
            $morphClasses[] = $model->getMorphClass();
        }

        return $morphClasses;
    }
}
