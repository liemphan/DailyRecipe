<?php

namespace DailyRecipe\Entities\Tools;

use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Models\Recipemenus;

class ShelfContext
{
    protected $KEY_SHELF_CONTEXT_ID = 'context_bookshelf_id';

    /**
     * Get the current bookshelf context for the given book.
     */
    public function getContextualShelfForBook(Recipe $book): ?Recipemenus
    {
        $contextBookshelfId = session()->get($this->KEY_SHELF_CONTEXT_ID, null);

        if (!is_int($contextBookshelfId)) {
            return null;
        }

        $shelf = Recipemenus::visible()->find($contextBookshelfId);
        $shelfContainsBook = $shelf && $shelf->contains($book);

        return $shelfContainsBook ? $shelf : null;
    }

    /**
     * Store the current contextual shelf ID.
     */
    public function setShelfContext(int $shelfId)
    {
        session()->put($this->KEY_SHELF_CONTEXT_ID, $shelfId);
    }

    /**
     * Clear the session stored shelf context id.
     */
    public function clearShelfContext()
    {
        session()->forget($this->KEY_SHELF_CONTEXT_ID);
    }
}
