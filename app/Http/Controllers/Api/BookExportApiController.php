<?php

namespace DailyRecipe\Http\Controllers\Api;

use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Tools\ExportFormatter;
use Throwable;

class BookExportApiController extends ApiController
{
    protected $exportFormatter;

    public function __construct(ExportFormatter $exportFormatter)
    {
        $this->exportFormatter = $exportFormatter;
        $this->middleware('can:content-export');
    }

    /**
     * Export a book as a PDF file.
     *
     * @throws Throwable
     */
    public function exportPdf(int $id)
    {
        $book = Recipe::visible()->findOrFail($id);
        $pdfContent = $this->exportFormatter->bookToPdf($book);

        return $this->downloadResponse($pdfContent, $book->slug . '.pdf');
    }

    /**
     * Export a book as a contained HTML file.
     *
     * @throws Throwable
     */
    public function exportHtml(int $id)
    {
        $book = Recipe::visible()->findOrFail($id);
        $htmlContent = $this->exportFormatter->bookToContainedHtml($book);

        return $this->downloadResponse($htmlContent, $book->slug . '.html');
    }

    /**
     * Export a book as a plain text file.
     */
    public function exportPlainText(int $id)
    {
        $book = Recipe::visible()->findOrFail($id);
        $textContent = $this->exportFormatter->bookToPlainText($book);

        return $this->downloadResponse($textContent, $book->slug . '.txt');
    }

    /**
     * Export a book as a markdown file.
     */
    public function exportMarkdown(int $id)
    {
        $book = Recipe::visible()->findOrFail($id);
        $markdown = $this->exportFormatter->bookToMarkdown($book);

        return $this->downloadResponse($markdown, $book->slug . '.md');
    }
}
