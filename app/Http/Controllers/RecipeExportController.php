<?php

namespace DailyRecipe\Http\Controllers;

use DailyRecipe\Entities\Repos\RecipeRepo;
use DailyRecipe\Entities\Tools\ExportFormatter;
use Throwable;

class RecipeExportController extends Controller
{
    protected $recipeRepo;
    protected $exportFormatter;

    /**
     * RecipeExportController constructor.
     */
    public function __construct(RecipeRepo $recipeRepo, ExportFormatter $exportFormatter)
    {
        $this->recipeRepo = $recipeRepo;
        $this->exportFormatter = $exportFormatter;
        $this->middleware('can:content-export');
    }

    /**
     * Export a recipe as a PDF file.
     *
     * @throws Throwable
     */
    public function pdf(string $recipeSlug)
    {
        $recipe = $this->recipeRepo->getBySlug($recipeSlug);
        $pdfContent = $this->exportFormatter->recipeToPdf($recipe);

        return $this->downloadResponse($pdfContent, $recipeSlug . '.pdf');
    }

    /**
     * Export a recipe as a contained HTML file.
     *
     * @throws Throwable
     */
    public function html(string $recipeSlug)
    {
        $recipe = $this->recipeRepo->getBySlug($recipeSlug);
        $htmlContent = $this->exportFormatter->recipeToContainedHtml($recipe);

        return $this->downloadResponse($htmlContent, $recipeSlug . '.html');
    }

    /**
     * Export a recipe as a plain text file.
     */
    public function plainText(string $recipeSlug)
    {
        $recipe = $this->recipeRepo->getBySlug($recipeSlug);
        $textContent = $this->exportFormatter->recipeToPlainText($recipe);

        return $this->downloadResponse($textContent, $recipeSlug . '.txt');
    }

    /**
     * Export a recipe as a markdown file.
     */
    public function markdown(string $recipeSlug)
    {
        $recipe = $this->recipeRepo->getBySlug($recipeSlug);
        $textContent = $this->exportFormatter->recipeToMarkdown($recipe);

        return $this->downloadResponse($textContent, $recipeSlug . '.md');
    }
}
