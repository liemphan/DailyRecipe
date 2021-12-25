<?php

namespace DailyRecipe\Http\Controllers;

use DailyRecipe\Actions\ActivityType;
use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Repos\RecipeRepo;
use DailyRecipe\Entities\Tools\RecipeContents;
use DailyRecipe\Exceptions\SortOperationException;
use DailyRecipe\Facades\Activity;
use Illuminate\Http\Request;

class RecipeSortController extends Controller
{
    protected $bookRepo;

    public function __construct(RecipeRepo $bookRepo)
    {
        $this->bookRepo = $bookRepo;
    }

    /**
     * Shows the view which allows pages to be re-ordered and sorted.
     */
    public function show(string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('book-update', $book);

        $bookChildren = (new RecipeContents($book))->getTree(false);

        $this->setPageTitle(trans('entities.recipes_sort_named', ['bookName'=>$book->getShortName()]));

        return view('recipes.sort', ['book' => $book, 'current' => $book, 'bookChildren' => $bookChildren]);
    }

    /**
     * Shows the sort box for a single book.
     * Used via AJAX when loading in extra recipes to a sort.
     */
    public function showItem(string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $bookChildren = (new RecipeContents($book))->getTree();

        return view('recipes.parts.sort-box', ['book' => $book, 'bookChildren' => $bookChildren]);
    }

    /**
     * Sorts a book using a given mapping array.
     */
    public function update(Request $request, string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $this->checkOwnablePermission('book-update', $book);

        // Return if no map sent
        if (!$request->filled('sort-tree')) {
            return redirect($book->getUrl());
        }

        $sortMap = collect(json_decode($request->get('sort-tree')));
        $bookContents = new RecipeContents($book);
        $booksInvolved = collect();

        try {
            $booksInvolved = $bookContents->sortUsingMap($sortMap);
        } catch (SortOperationException $exception) {
            $this->showPermissionError();
        }

        // Rebuild permissions and add activity for involved recipes.
        $booksInvolved->each(function (Recipe $book) {
            Activity::addForEntity($book, ActivityType::BOOK_SORT);
        });

        return redirect($book->getUrl());
    }
}
