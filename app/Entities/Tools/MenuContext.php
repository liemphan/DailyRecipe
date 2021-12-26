<?php

namespace DailyRecipe\Entities\Tools;

use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Models\Recipemenu;

class MenuContext
{
    protected $KEY_MENU_CONTEXT_ID = 'context_recipemenu_id';

    /**
     * Get the current recipemenu context for the given recipe.
     */
    public function getContextualMenuForRecipe(Recipe $recipe): ?Recipemenu
    {
        $contextRecipemenuId = session()->get($this->KEY_MENU_CONTEXT_ID, null);

        if (!is_int($contextRecipemenuId)) {
            return null;
        }

        $menu = Recipemenu::visible()->find($contextRecipemenuId);
        $menuContainsRecipe = $menu && $menu->contains($recipe);

        return $menuContainsRecipe ? $menu : null;
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
