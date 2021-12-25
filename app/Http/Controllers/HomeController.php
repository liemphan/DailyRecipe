<?php

namespace DailyRecipe\Http\Controllers;

use Activity;
use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Models\Page;
use DailyRecipe\Entities\Queries\RecentlyViewed;
use DailyRecipe\Entities\Queries\TopFavourites;
use DailyRecipe\Entities\Repos\RecipeRepo;
use DailyRecipe\Entities\Repos\RecipemenuRepo;
use DailyRecipe\Entities\Tools\PageContent;

class HomeController extends Controller
{
    /**
     * Display the homepage.
     */
    public function index()
    {
        $activity = Activity::latest(10);
        $draftPages = [];

        if ($this->isSignedIn()) {
            $draftPages = Page::visible()
                ->where('draft', '=', true)
                ->where('created_by', '=', user()->id)
                ->orderBy('updated_at', 'desc')
                ->with('book')
                ->take(6)
                ->get();
        }

        $recentFactor = count($draftPages) > 0 ? 0.5 : 1;
        $recents = $this->isSignedIn() ?
            (new RecentlyViewed())->run(12 * $recentFactor, 1)
            : Recipe::visible()->orderBy('created_at', 'desc')->take(12 * $recentFactor)->get();
        $favourites = (new TopFavourites())->run(6);
        $recentlyUpdatedPages = Page::visible()->with('book')
            ->where('draft', false)
            ->orderBy('updated_at', 'desc')
            ->take($favourites->count() > 0 ? 5 : 10)
            ->select(Page::$listAttributes)
            ->get();

        $homepageOptions = ['default', 'recipes', 'recipemenus', 'page'];
        $homepageOption = setting('app-homepage-type', 'default');
        if (!in_array($homepageOption, $homepageOptions)) {
            $homepageOption = 'default';
        }

        $commonData = [
            'activity'             => $activity,
            'recents'              => $recents,
            'recentlyUpdatedPages' => $recentlyUpdatedPages,
            'draftPages'           => $draftPages,
            'favourites'           => $favourites,
        ];

        // Add required list ordering & sorting for recipes & menus views.
        if ($homepageOption === 'recipemenus' || $homepageOption === 'recipes') {
            $key = $homepageOption;
            $view = setting()->getForCurrentUser($key . '_view_type');
            $sort = setting()->getForCurrentUser($key . '_sort', 'name');
            $order = setting()->getForCurrentUser($key . '_sort_order', 'asc');

            $sortOptions = [
                'name'       => trans('common.sort_name'),
                'created_at' => trans('common.sort_created_at'),
                'updated_at' => trans('common.sort_updated_at'),
            ];

            $commonData = array_merge($commonData, [
                'view'        => $view,
                'sort'        => $sort,
                'order'       => $order,
                'sortOptions' => $sortOptions,
            ]);
        }

        if ($homepageOption === 'recipemenus') {
            $menus = app(RecipemenuRepo::class)->getAllPaginated(18, $commonData['sort'], $commonData['order']);
            $data = array_merge($commonData, ['menus' => $menus]);

            return view('home.menus', $data);
        }

        if ($homepageOption === 'recipes') {
            $bookRepo = app(RecipeRepo::class);
            $books = $bookRepo->getAllPaginated(18, $commonData['sort'], $commonData['order']);
            $data = array_merge($commonData, ['recipes' => $books]);

            return view('home.recipes', $data);
        }

        if ($homepageOption === 'page') {
            $homepageSetting = setting('app-homepage', '0:');
            $id = intval(explode(':', $homepageSetting)[0]);
            /** @var Page $customHomepage */
            $customHomepage = Page::query()->where('draft', '=', false)->findOrFail($id);
            $pageContent = new PageContent($customHomepage);
            $customHomepage->html = $pageContent->render(false);

            return view('home.specific-page', array_merge($commonData, ['customHomepage' => $customHomepage]));
        }

        return view('home.default', $commonData);
    }

    /**
     * Get custom head HTML, Used in ajax calls to show in editor.
     */
    public function customHeadContent()
    {
        return view('common.custom-head');
    }

    /**
     * Show the view for /robots.txt.
     */
    public function robots()
    {
        $sitePublic = setting('app-public', false);
        $allowRobots = config('app.allow_robots');

        if ($allowRobots === null) {
            $allowRobots = $sitePublic;
        }

        return response()
            ->view('misc.robots', ['allowRobots' => $allowRobots])
            ->header('Content-Type', 'text/plain');
    }

    /**
     * Show the route for 404 responses.
     */
    public function notFound()
    {
        return response()->view('errors.404', [], 404);
    }
}
