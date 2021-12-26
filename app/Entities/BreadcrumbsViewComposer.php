<?php

namespace DailyRecipe\Entities;

use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Tools\MenuContext;
use Illuminate\View\View;

class BreadcrumbsViewComposer
{
    protected $entityContextManager;

    /**
     * BreadcrumbsViewComposer constructor.
     *
     * @param MenuContext $entityContextManager
     */
    public function __construct(MenuContext $entityContextManager)
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
            $menu = $this->entityContextManager->getContextualMenuForRecipe($firstCrumb);
            if ($menu) {
                array_unshift($crumbs, $menu);
                $view->with('crumbs', $crumbs);
            }
        }
    }
}
