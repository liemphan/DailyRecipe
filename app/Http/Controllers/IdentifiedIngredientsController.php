<?php

namespace DailyRecipe\Http\Controllers;


use Activity;
use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Queries\RecentlyViewed;
use DailyRecipe\Entities\Queries\TopFavourites;

class IdentifiedIngredientsController extends Controller
{
    /**
     * Shows the last revisions for this page.
     *
     * @throws NotFoundException
     */
    public function index()
    {
        $activity = Activity::latest(10);
        $draftPages = [];

        if ($this->isSignedIn()) {
            $draftPages = Recipe::visible()
                ->where('draft', '=', true)
                ->where('created_by', '=', user()->id)
                ->orderBy('updated_at', 'desc')
                //->with('recipe')
                ->take(6)
                ->get();
        }

        $recentFactor = count($draftPages) > 0 ? 0.5 : 1;
        $recents = $this->isSignedIn() ?
            (new RecentlyViewed())->run(12 * $recentFactor, 1)
            : Recipe::visible()->orderBy('created_at', 'desc')->take(12 * $recentFactor)->get();
        $favourites = (new TopFavourites())->run(6);
        $recentlyUpdatedPages = Recipe::visible()/*->with('recipe')*/
        ->where('draft', false)
            ->orderBy('updated_at', 'desc')
            ->take($favourites->count() > 0 ? 5 : 10)
            ->select(Recipe::$listAttributes)
            ->get();

        $homepageOptions = ['default', 'recipes', 'recipemenus', 'content', 'identified'];
        $homepageOption = setting('app-homepage-type', 'default');
        if (!in_array($homepageOption, $homepageOptions)) {
            $homepageOption = 'default';
        }

        $commonData = [
            'activity' => $activity,
            'recents' => $recents,
            'recentlyUpdatedPages' => $recentlyUpdatedPages,
            'draftPages' => $draftPages,
            'favourites' => $favourites,
        ];


        return view('search.identified.webcam', $commonData);
    }
}