<?php

namespace DailyRecipe\Entities;

use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Tools\ShelfContext;
use Illuminate\View\View;

class BreadcrumbsViewComposer
{
    protected $entityContextManager;

    /**
     * BreadcrumbsViewComposer constructor.
     *
     * @param ShelfContext $entityContextManager
     */
    public function __construct(ShelfContext $entityContextManager)
    {
        $this->entityContextManager = $entityContextManager;
    }

    /**
     * Modify data when the view is composed.
     *
     * @param View $view
     */
    public function compose(View $view)
    {
        $crumbs = $view->getData()['crumbs'];
        $firstCrumb = $crumbs[0] ?? null;
        if ($firstCrumb instanceof Recipe) {
            $shelf = $this->entityContextManager->getContextualShelfForBook($firstCrumb);
            if ($shelf) {
                array_unshift($crumbs, $shelf);
                $view->with('crumbs', $crumbs);
            }
        }
    }
}
