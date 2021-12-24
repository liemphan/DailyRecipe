<?php

namespace DailyRecipe\Entities\Tools;

use DailyRecipe\Entities\Models\Book;
use DailyRecipe\Entities\Models\Recipemenu;

class MenuContext
{
    protected $KEY_MENU_CONTEXT_ID = 'context_recipemenu_id';

    /**
     * Get the current recipemenu context for the given book.
     */
    public function getContextualMenuForBook(Book $book): ?Recipemenu
    {
        $contextRecipemenuId = session()->get($this->KEY_MENU_CONTEXT_ID, null);

        if (!is_int($contextRecipemenuId)) {
            return null;
        }

        $menu = Recipemenu::visible()->find($contextRecipemenuId);
        $menuContainsBook = $menu && $menu->contains($book);

        return $menuContainsBook ? $menu : null;
    }

    /**
     * Store the current contextual menu ID.
     */
    public function setMenuContext(int $menuId)
    {
        session()->put($this->KEY_MENU_CONTEXT_ID, $menuId);
    }

    /**
     * Clear the session stored menu context id.
     */
    public function clearMenuContext()
    {
        session()->forget($this->KEY_MENU_CONTEXT_ID);
    }
}
