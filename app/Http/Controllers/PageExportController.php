<?php

namespace DailyRecipe\Http\Controllers;

use DailyRecipe\Entities\Repos\PageRepo;
use DailyRecipe\Entities\Tools\ExportFormatter;
use DailyRecipe\Entities\Tools\PageContent;
use DailyRecipe\Entities\Tools\RecipeContents;
use DailyRecipe\Exceptions\NotFoundException;
use Throwable;

class PageExportController extends Controller
{
    protected $pageRepo;
    protected $exportFormatter;

    /**
     * PageExportController constructor.
     */
    public function __construct(PageRepo $pageRepo, ExportFormatter $exportFormatter)
    {
        $this->pageRepo = $pageRepo;
        $this->exportFormatter = $exportFormatter;
        $this->middleware('can:content-export');
    }

    /**
     * Exports a page to a PDF.
     * https://github.com/barryvdh/laravel-dompdf.
     *
     * @throws NotFoundException
     * @throws Throwable
     */
    public function pdf(string $recipeSlug, string $pageSlug)
    {
        $page = $this->pageRepo->getBySlug($recipeSlug, $pageSlug);
        $page->html = (new RecipeContents($page))->render();
        $pdfContent = $this->exportFormatter->pageToPdf($page);

        return $this->downloadResponse($pdfContent, $pageSlug . '.pdf');
    }

    /**
     * Export a page to a self-contained HTML file.
     *
     * @throws NotFoundException
     * @throws Throwable
     */
    public function html(string $recipeSlug, string $pageSlug)
    {
        $page = $this->pageRepo->getBySlug($recipeSlug, $pageSlug);
        $page->html = (new PageContent($page))->render();
        $containedHtml = $this->exportFormatter->pageToContainedHtml($page);

        return $this->downloadResponse($containedHtml, $pageSlug . '.html');
    }

    /**
     * Export a page to a simple plaintext .txt file.
     *
     * @throws NotFoundException
     */
    public function plainText(string $recipeSlug, string $pageSlug)
    {
        $page = $this->pageRepo->getBySlug($recipeSlug, $pageSlug);
        $pageText = $this->exportFormatter->pageToPlainText($page);

        return $this->downloadResponse($pageText, $pageSlug . '.txt');
    }

    /**
     * Export a page to a simple markdown .md file.
     *
     * @throws NotFoundException
     */
    public function markdown(string $recipeSlug, string $pageSlug)
    {
        $page = $this->pageRepo->getBySlug($recipeSlug, $pageSlug);
        $pageText = $this->exportFormatter->pageToMarkdown($page);

        return $this->downloadResponse($pageText, $pageSlug . '.md');
    }
}
