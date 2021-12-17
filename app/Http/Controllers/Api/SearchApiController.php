<?php

namespace DailyRecipe\Http\Controllers\Api;

use DailyRecipe\Entities\Models\Entity;
use DailyRecipe\Entities\Tools\SearchOptions;
use DailyRecipe\Entities\Tools\SearchRunner;
use Illuminate\Http\Request;

class SearchApiController extends ApiController
{
    protected $searchRunner;

    protected $rules = [
        'all' => [
            'query'  => ['required'],
            'page'   => ['integer', 'min:1'],
            'count'  => ['integer', 'min:1', 'max:100'],
        ],
    ];

    public function __construct(SearchRunner $searchRunner)
    {
        $this->searchRunner = $searchRunner;
    }

    /**
     * Run a search query against all main content types (shelves, books, chapters & pages)
     * in the system. Takes the same input as the main search bar within the DailyRecipe
     * interface as a 'query' parameter. See https://www.dailyrecipeapp.com/docs/user/searching/
     * for a full list of search term options. Results contain a 'type' property to distinguish
     * between: bookshelf, book, chapter & page.
     *
     * The paging parameters and response format emulates a standard listing endpoint
     * but standard sorting and filtering cannot be done on this endpoint. If a count value
     * is provided this will only be taken as a suggestion. The results in the response
     * may currently be up to 4x this value.
     */
    public function all(Request $request)
    {
        $this->validate($request, $this->rules['all']);

        $options = SearchOptions::fromString($request->get('query') ?? '');
        $page = intval($request->get('page', '0')) ?: 1;
        $count = min(intval($request->get('count', '0')) ?: 20, 100);

        $results = $this->searchRunner->searchEntities($options, 'all', $page, $count);

        /** @var Entity $result */
        foreach ($results['results'] as $result) {
            $result->setVisible([
                'id', 'name', 'slug', 'book_id',
                'chapter_id', 'draft', 'template',
                'created_at', 'updated_at',
                'tags', 'type',
            ]);
            $result->setAttribute('type', $result->getType());
        }

        return response()->json([
            'data'  => $results['results'],
            'total' => $results['total'],
        ]);
    }
}
