<?php

namespace DailyRecipe\Http\Controllers;

use DailyRecipe\Entities\Repos\ChapterRepo;
use DailyRecipe\Entities\Tools\ExportFormatter;
use DailyRecipe\Exceptions\NotFoundException;
use Throwable;

class ChapterExportController extends Controller
{
    protected $chapterRepo;
    protected $exportFormatter;

    /**
     * ChapterExportController constructor.
     */
    public function __construct(ChapterRepo $chapterRepo, ExportFormatter $exportFormatter)
    {
        $this->chapterRepo = $chapterRepo;
        $this->exportFormatter = $exportFormatter;
        $this->middleware('can:content-export');
    }

    /**
     * Exports a chapter to pdf.
     *
     * @throws NotFoundException
     * @throws Throwable
     */
    public function pdf(string $recipeSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($recipeSlug, $chapterSlug);
        $pdfContent = $this->exportFormatter->chapterToPdf($chapter);

        return $this->downloadResponse($pdfContent, $chapterSlug . '.pdf');
    }

    /**
     * Export a chapter to a self-contained HTML file.
     *
     * @throws NotFoundException
     * @throws Throwable
     */
    public function html(string $recipeSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($recipeSlug, $chapterSlug);
        $containedHtml = $this->exportFormatter->chapterToContainedHtml($chapter);

        return $this->downloadResponse($containedHtml, $chapterSlug . '.html');
    }

    /**
     * Export a chapter to a simple plaintext .txt file.
     *
     * @throws NotFoundException
     */
    public function plainText(string $recipeSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($recipeSlug, $chapterSlug);
        $chapterText = $this->exportFormatter->chapterToPlainText($chapter);

        return $this->downloadResponse($chapterText, $chapterSlug . '.txt');
    }

    /**
     * Export a chapter to a simple markdown file.
     *
     * @throws NotFoundException
     */
    public function markdown(string $recipeSlug, string $chapterSlug)
    {
        // TODO: This should probably export to a zip file.
        $chapter = $this->chapterRepo->getBySlug($recipeSlug, $chapterSlug);
        $chapterText = $this->exportFormatter->chapterToMarkdown($chapter);

        return $this->downloadResponse($chapterText, $chapterSlug . '.md');
    }
}
