<?php

namespace DailyRecipe\Entities\Tools;

use DailyRecipe\Entities\Models\Recipe;

use DailyRecipe\Entities\Tools\Markdown\HtmlToMarkdown;
use DailyRecipe\Uploads\ImageService;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Exception;
use Throwable;

class ExportFormatter
{
    protected $imageService;
    protected $pdfGenerator;

    /**
     * ExportService constructor.
     */
    public function __construct(ImageService $imageService, PdfGenerator $pdfGenerator)
    {
        $this->imageService = $imageService;
        $this->pdfGenerator = $pdfGenerator;
    }

    /**
     * Convert a page to a self-contained HTML file.
     * Includes required CSS & image content. Images are base64 encoded into the HTML.
     *
     * @throws Throwable
     */
    public function pageToContainedHtml(Page $page)
    {
        $page->html = (new PageContent($page))->render();
        $pageHtml = view('pages.export', [
            'page' => $page,
            'format' => 'html',
        ])->render();

        return $this->containHtml($pageHtml);
    }

    /**
     * Convert a chapter to a self-contained HTML file.
     *
     * @throws Throwable
     */
    public function chapterToContainedHtml(Chapter $chapter)
    {
        $pages = $chapter->getVisiblePages();
        $pages->each(function ($page) {
            $page->html = (new PageContent($page))->render();
        });
        $html = view('chapters.export', [
            'chapter' => $chapter,
            'pages' => $pages,
            'format' => 'html',
        ])->render();

        return $this->containHtml($html);
    }

    /**
     * Convert a recipe to a self-contained HTML file.
     *
     * @throws Throwable
     */
    public function recipeToContainedHtml(Recipe $recipe)
    {
        $recipeTree = (new RecipeContents($recipe))->getTree(false, true);
        $html = view('recipes.export', [
            'recipe' => $recipe,
            'recipeChildren' => $recipeTree,
            'format' => 'html',
        ])->render();

        return $this->containHtml($html);
    }

    /**
     * Convert a page to a PDF file.
     *
     * @throws Throwable
     */
    public function pageToPdf(Page $page)
    {
        $page->html = (new PageContent($page))->render();
        $html = view('pages.export', [
            'page' => $page,
            'format' => 'pdf',
        ])->render();

        return $this->htmlToPdf($html);
    }

    /**
     * Convert a chapter to a PDF file.
     *
     * @throws Throwable
     */
    public function chapterToPdf(Chapter $chapter)
    {
        $pages = $chapter->getVisiblePages();
        $pages->each(function ($page) {
            $page->html = (new PageContent($page))->render();
        });

        $html = view('chapters.export', [
            'chapter' => $chapter,
            'pages' => $pages,
            'format' => 'pdf',
        ])->render();

        return $this->htmlToPdf($html);
    }

    /**
     * Convert a recipe to a PDF file.
     *
     * @throws Throwable
     */
    public function recipeToPdf(Recipe $recipe)
    {
        $recipeTree = (new RecipeContents($recipe))->getTree(false, true);
        $html = view('recipes.export', [
            'recipe' => $recipe,
            'recipeChildren' => $recipeTree,
            'format' => 'pdf',
        ])->render();

        return $this->htmlToPdf($html);
    }

    /**
     * Convert normal web-page HTML to a PDF.
     *
     * @throws Exception
     */
    protected function htmlToPdf(string $html): string
    {
        $html = $this->containHtml($html);
        $html = $this->replaceIframesWithLinks($html);
        return $this->pdfGenerator->fromHtml($html);
    }

    /**
     * Within the given HTML content, replace any iframe elements
     * with anchor links within paragraph blocks.
     */
    protected function replaceIframesWithLinks(string $html): string
    {
        libxml_use_internal_errors(true);

        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xPath = new DOMXPath($doc);


        $iframes = $xPath->query('//iframe');
        /** @var DOMElement $iframe */
        foreach ($iframes as $iframe) {
            $link = $iframe->getAttribute('src');
            if (strpos($link, '//') === 0) {
                $link = 'https:' . $link;
            }

            $anchor = $doc->createElement('a', $link);
            $anchor->setAttribute('href', $link);
            $paragraph = $doc->createElement('p');
            $paragraph->appendChild($anchor);
            $iframe->replaceWith($paragraph);
        }

        return $doc->saveHTML();
    }

    /**
     * Bundle of the contents of a html file to be self-contained.
     *
     * @throws Exception
     */
    protected function containHtml(string $htmlContent): string
    {
        $imageTagsOutput = [];
        preg_match_all("/\<img.*?src\=(\'|\")(.*?)(\'|\").*?\>/i", $htmlContent, $imageTagsOutput);

        // Replace image src with base64 encoded image strings
        if (isset($imageTagsOutput[0]) && count($imageTagsOutput[0]) > 0) {
            foreach ($imageTagsOutput[0] as $index => $imgMatch) {
                $oldImgTagString = $imgMatch;
                $srcString = $imageTagsOutput[2][$index];
                $imageEncoded = $this->imageService->imageUriToBase64($srcString);
                if ($imageEncoded === null) {
                    $imageEncoded = $srcString;
                }
                $newImgTagString = str_replace($srcString, $imageEncoded, $oldImgTagString);
                $htmlContent = str_replace($oldImgTagString, $newImgTagString, $htmlContent);
            }
        }

        $linksOutput = [];
        preg_match_all("/\<a.*href\=(\'|\")(.*?)(\'|\").*?\>/i", $htmlContent, $linksOutput);

        // Replace image src with base64 encoded image strings
        if (isset($linksOutput[0]) && count($linksOutput[0]) > 0) {
            foreach ($linksOutput[0] as $index => $linkMatch) {
                $oldLinkString = $linkMatch;
                $srcString = $linksOutput[2][$index];
                if (strpos(trim($srcString), 'http') !== 0) {
                    $newSrcString = url($srcString);
                    $newLinkString = str_replace($srcString, $newSrcString, $oldLinkString);
                    $htmlContent = str_replace($oldLinkString, $newLinkString, $htmlContent);
                }
            }
        }

        // Replace any relative links with system domain
        return $htmlContent;
    }

    /**
     * Converts the page contents into simple plain text.
     * This method filters any bad looking content to provide a nice final output.
     */
    public function pageToPlainText(Recipe $page): string
    {
        $html = (new RecipeContents($page))->render();
        $text = strip_tags($html);
        // Replace multiple spaces with single spaces
        $text = preg_replace('/\ {2,}/', ' ', $text);
        // Reduce multiple horrid whitespace characters.
        $text = preg_replace('/(\x0A|\xA0|\x0A|\r|\n){2,}/su', "\n\n", $text);
        $text = html_entity_decode($text);
        // Add title
        $text = $page->name . "\n\n" . $text;

        return $text;
    }

    /**
     * Convert a chapter into a plain text string.
     */
    public function chapterToPlainText(Chapter $chapter): string
    {
        $text = $chapter->name . "\n\n";
        $text .= $chapter->description . "\n\n";
        foreach ($chapter->getVisiblePages() as $page) {
            $text .= $this->pageToPlainText($page);
        }

        return $text;
    }

    /**
     * Convert a recipe into a plain text string.
     */
    public function recipeToPlainText(Recipe $recipe): string
    {
        $recipeTree = (new RecipeContents($recipe))->getTree(false, false);
        $text = $recipe->name . "\n\n";
        foreach ($recipeTree as $recipeChild) {
            if ($recipeChild->isA('chapter')) {
                $text .= $this->chapterToPlainText($recipeChild);
            } else {
                $text .= $this->pageToPlainText($recipeChild);
            }
        }

        return $text;
    }

    /**
     * Convert a page to a Markdown file.
     */
    public function pageToMarkdown(Recipe $page): string
    {
        if ($page->markdown) {
            return '# ' . $page->name . "\n\n" . $page->markdown;
        }

        return '# ' . $page->name . "\n\n" . (new HtmlToMarkdown($page->html))->convert();
    }

    /**
     * Convert a chapter to a Markdown file.
     */
    public function chapterToMarkdown(Chapter $chapter): string
    {
        $text = '# ' . $chapter->name . "\n\n";
        $text .= $chapter->description . "\n\n";
        foreach ($chapter->pages as $page) {
            $text .= $this->pageToMarkdown($page) . "\n\n";
        }

        return $text;
    }

    /**
     * Convert a recipe into a plain text string.
     */
    public function recipeToMarkdown(Recipe $recipe): string
    {
        $recipeTree = (new RecipeContents($recipe))->getTree(false, true);
        $text = '# ' . $recipe->name . "\n\n";
        foreach ($recipeTree as $recipeChild) {
//            if ($recipeChild instanceof Chapter) {
//                $text .= $this->chapterToMarkdown($recipeChild);
//            } else {
                $text .= $this->pageToMarkdown($recipeChild);
//            }
        }

        return $text;
    }
}
