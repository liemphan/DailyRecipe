<?php

namespace DailyRecipe\Http\Controllers\Api;

use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Tools\ExportFormatter;
use Throwable;

class RecipeExportApiController extends ApiController
{
    protected $exportFormatter;

    public function __construct(ExportFormatter $exportFormatter)
    {
        $this->exportFormatter = $exportFormatter;
        $this->middleware('can:content-export');
    }

    /**
     * Export a recipe as a PDF file.
     *
     * @throws Throwable
     */
    public function exportPdf(int $id)
    {
        $recipe = Recipe::visible()->findOrFail($id);
        $pdfContent = $this->exportFormatter->recipeToPdf($recipe);

        return $this->downloadResponse($pdfContent, $recipe->slug . '.pdf');
    }

    /**
     * Export a recipe as a contained HTML file.
     *
     * @throws Throwable
     */
    public function exportHtml(int $id)
    {
        $recipe = Recipe::visible()->findOrFail($id);
        $htmlContent = $this->exportFormatter->recipeToContainedHtml($recipe);

        return $this->downloadResponse($htmlContent, $recipe->slug . '.html');
    }

    /**
     * Export a recipe as a plain text file.
     */
    public function exportPlainText(int $id)
    {
        $recipe = Recipe::visible()->findOrFail($id);
        $textContent = $this->exportFormatter->recipeToPlainText($recipe);

        return $this->downloadResponse($textContent, $recipe->slug . '.txt');
    }

    /**
     * Export a recipe as a markdown file.
     */
    public function exportMarkdown(int $id)
    {
        $recipe = Recipe::visible()->findOrFail($id);
        $markdown = $this->exportFormatter->recipeToMarkdown($recipe);

        return $this->downloadResponse($markdown, $recipe->slug . '.md');
    }
}
