<?php

namespace DailyRecipe\Entities\Tools;

use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Models\RecipeChild;

use DailyRecipe\Entities\Models\Entity;
use DailyRecipe\Entities\Tools\Markdown\CustomListItemRenderer;
use DailyRecipe\Entities\Tools\Markdown\CustomStrikeThroughExtension;
use DailyRecipe\Exceptions\ImageUploadException;
use DailyRecipe\Exceptions\SortOperationException;
use DailyRecipe\Facades\Theme;
use DailyRecipe\Theming\ThemeEvents;
use DailyRecipe\Uploads\ImageRepo;
use DailyRecipe\Uploads\ImageService;
use DailyRecipe\Util\HtmlContentFilter;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use League\CommonMark\Block\Element\ListItem;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use stdClass;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;
use DOMXPath;


class RecipeContents
{
    /**
     * @var Recipe
     */
    protected $recipe;

    /**
     * RecipeContents constructor.
     */
    public function __construct(Recipe $recipe)
    {
        $this->recipe = $recipe;
    }

    /**
     * Get the current priority of the last item
     * at the top-level of the recipe.
     */
    public function getLastPriority(): int
    {
        $maxPage = Recipe::visible()->where('id', '=', $this->recipe->id)
            ->where('draft', '=', false)->max('priority');

//        $maxChapter = Chapter::visible()->where('recipe_id', '=', $this->recipe->id)
//            ->max('priority');

        return max($maxPage, 1);
    }

    /**
     * Get the contents as a sorted collection tree.
     */
    public function getTree(bool $showDrafts = false, bool $renderPages = false): Collection
    {
        $pages = $this->getPages($showDrafts, $renderPages);
        // $chapters = Chapter::visible()->where('recipe_id', '=', $this->recipe->id)->get();
        $all = collect()->concat($pages);
        // $chapterMap = $chapters->keyBy('id');
        $lonePages = collect();

//        $pages->groupBy('chapter_id')->each(function ($pages, $chapter_id) use ($chapterMap, &$lonePages) {
//            $chapter = $chapterMap->get($chapter_id);
//            if ($chapter) {
//                $chapter->setAttribute('visible_pages', collect($pages)->sortBy($this->recipeChildSortFunc()));
//            } else {
//                $lonePages = $lonePages->concat($pages);
//            }
//        });

//        $chapters->whereNull('visible_pages')->each(function (Chapter $chapter) {
//            $chapter->setAttribute('visible_pages', collect([]));
//        });

        $all->each(function (Entity $entity) use ($renderPages) {
            $entity->setRelation('recipe', $this->recipe);

            if ($renderPages && $entity instanceof Recipe) {
                $entity->html = (new RecipeContents($entity))->render();
            }
        });

        return collect($pages)->concat($lonePages)->sortBy($this->recipeChildSortFunc());
    }

    /**
     * Function for providing a sorting score for an entity in relation to the
     * other items within the recipe.
     */
    protected function recipeChildSortFunc(): callable
    {
        return function (Entity $entity) {
            if (isset($entity['draft']) && $entity['draft']) {
                return -100;
            }

            return $entity['priority'] ?? 0;
        };
    }

    /**
     * Get the visible pages within this recipe.
     */
    protected function getPages(bool $showDrafts = false, bool $getPageContent = false): Collection
    {
        $query = Recipe::visible()
            ->select($getPageContent ? Recipe::$contentAttributes : Recipe::$listAttributes)
            ->where('id', '=', $this->recipe->id);

        if (!$showDrafts) {
            $query->where('draft', '=', false);
        }

        return $query->get();
    }

    /**
     * Sort the recipes content using the given map.
     * The map is a single-dimension collection of objects in the following format:
     *   {
     *     +"id": "294" (ID of item)
     *     +"sort": 1 (Sort order index)
     *     +"parentChapter": false (ID of parent chapter, as string, or false)
     *     +"type": "page" (Entity type of item)
     *     +"recipe": "1" (Id of recipe to place item in)
     *   }.
     *
     * Returns a list of recipes that were involved in the operation.
     *
     * @throws SortOperationException
     */
    public function sortUsingMap(Collection $sortMap): Collection
    {
        // Load models into map
        $this->loadModelsIntoSortMap($sortMap);
        $recipesInvolved = $this->getRecipesInvolvedInSort($sortMap);

        // Perform the sort
        $sortMap->each(function ($mapItem) {
            $this->applySortUpdates($mapItem);
        });

        // Update permissions and activity.
        $recipesInvolved->each(function (Recipe $recipe) {
            $recipe->rebuildPermissions();
        });

        return $recipesInvolved;
    }

    /**
     * Using the given sort map item, detect changes for the related model
     * and update it if required.
     */
    protected function applySortUpdates(stdClass $sortMapItem)
    {
        /** @var RecipeChild $model */
        $model = $sortMapItem->model;

        $priorityChanged = intval($model->priority) !== intval($sortMapItem->sort);
        $recipeChanged = intval($model->recipe_id) !== intval($sortMapItem->recipe);
        //  $chapterChanged = ($model instanceof Page) && intval($model->chapter_id) !== $sortMapItem->parentChapter;

        if ($recipeChanged) {
            $model->changeRecipe($sortMapItem->recipe);
        }

//        if ($chapterChanged) {
//            $model->chapter_id = intval($sortMapItem->parentChapter);
//            $model->save();
//        }

        if ($priorityChanged) {
            $model->priority = intval($sortMapItem->sort);
            $model->save();
        }
    }

    /**
     * Load models from the database into the given sort map.
     */
    protected function loadModelsIntoSortMap(Collection $sortMap): void
    {
        $keyMap = $sortMap->keyBy(function (stdClass $sortMapItem) {
            return $sortMapItem->type . ':' . $sortMapItem->id;
        });
        $pageIds = $sortMap->where('type', '=', 'page')->pluck('id');
        //  $chapterIds = $sortMap->where('type', '=', 'chapter')->pluck('id');

        $pages = Recipe::visible()->whereIn('id', $pageIds)->get();
        //      $chapters = Chapter::visible()->whereIn('id', $chapterIds)->get();

        foreach ($pages as $page) {
            $sortItem = $keyMap->get('page:' . $page->id);
            $sortItem->model = $page;
        }

//        foreach ($chapters as $chapter) {
//            $sortItem = $keyMap->get('chapter:' . $chapter->id);
//            $sortItem->model = $chapter;
//        }
    }

    /**
     * Get the recipes involved in a sort.
     * The given sort map should have its models loaded first.
     *
     * @throws SortOperationException
     */
    protected function getRecipesInvolvedInSort(Collection $sortMap): Collection
    {
        $recipeIdsInvolved = collect([$this->recipe->id]);
        $recipeIdsInvolved = $recipeIdsInvolved->concat($sortMap->pluck('recipe'));
        $recipeIdsInvolved = $recipeIdsInvolved->concat($sortMap->pluck('model.recipe_id'));
        $recipeIdsInvolved = $recipeIdsInvolved->unique()->toArray();

        $recipes = Recipe::hasPermission('update')->whereIn('id', $recipeIdsInvolved)->get();

        if (count($recipes) !== count($recipeIdsInvolved)) {
            throw new SortOperationException('Could not find all recipes requested in sort operation');
        }

        return $recipes;
    }

    /**
     * Update the content of the page with new provided HTML.
     */
    public function setNewHTML(string $html)
    {
        $html = $this->extractBase64ImagesFromHtml($html);
        $this->recipe->html = $this->formatHtml($html);
        $this->recipe->text = $this->toPlainText();
        $this->recipe->markdown = '';
    }

    /**
     * Update the content of the page with new provided Markdown content.
     */
    public function setNewMarkdown(string $markdown)
    {
        $markdown = $this->extractBase64ImagesFromMarkdown($markdown);
        $this->recipe->markdown = $markdown;
        $html = $this->markdownToHtml($markdown);
        $this->recipe->html = $this->formatHtml($html);
        $this->recipe->text = $this->toPlainText();
    }

    /**
     * Convert the given Markdown content to a HTML string.
     */
    protected function markdownToHtml(string $markdown): string
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new TaskListExtension());
        $environment->addExtension(new CustomStrikeThroughExtension());
        $environment = Theme::dispatch(ThemeEvents::COMMONMARK_ENVIRONMENT_CONFIGURE, $environment) ?? $environment;
        $converter = new CommonMarkConverter([], $environment);

        $environment->addBlockRenderer(ListItem::class, new CustomListItemRenderer(), 10);

        return $converter->convertToHtml($markdown);
    }

    /**
     * Convert all base64 image data to saved images.
     */
    protected function extractBase64ImagesFromHtml(string $htmlText): string
    {
        if (empty($htmlText) || strpos($htmlText, 'data:image') === false) {
            return $htmlText;
        }

        $doc = $this->loadDocumentFromHtml($htmlText);
        $container = $doc->documentElement;
        $body = $container->childNodes->item(0);
        $childNodes = $body->childNodes;
        $xPath = new DOMXPath($doc);

        // Get all img elements with image data blobs
        $imageNodes = $xPath->query('//img[contains(@src, \'data:image\')]');
        foreach ($imageNodes as $imageNode) {
            $imageSrc = $imageNode->getAttribute('src');
            $newUrl = $this->base64ImageUriToUploadedImageUrl($imageSrc);
            $imageNode->setAttribute('src', $newUrl);
        }

        // Generate inner html as a string
        $html = '';
        foreach ($childNodes as $childNode) {
            $html .= $doc->saveHTML($childNode);
        }

        return $html;
    }

    /**
     * Convert all inline base64 content to uploaded image files.
     */
    protected function extractBase64ImagesFromMarkdown(string $markdown)
    {
        $matches = [];
        preg_match_all('/!\[.*?]\(.*?(data:image\/.*?)[)"\s]/', $markdown, $matches);

        foreach ($matches[1] as $base64Match) {
            $newUrl = $this->base64ImageUriToUploadedImageUrl($base64Match);
            $markdown = str_replace($base64Match, $newUrl, $markdown);
        }

        return $markdown;
    }

    /**
     * Parse the given base64 image URI and return the URL to the created image instance.
     * Returns an empty string if the parsed URI is invalid or causes an error upon upload.
     */
    protected function base64ImageUriToUploadedImageUrl(string $uri): string
    {
        $imageRepo = app()->make(ImageRepo::class);
        $imageInfo = $this->parseBase64ImageUri($uri);

        // Validate extension and content
        if (empty($imageInfo['data']) || !ImageService::isExtensionSupported($imageInfo['extension'])) {
            return '';
        }

        // Validate that the content is not over our upload limit
        $uploadLimitBytes = (config('app.upload_limit') * 1000000);
        if (strlen($imageInfo['data']) > $uploadLimitBytes) {
            return '';
        }

        // Save image from data with a random name
        $imageName = 'embedded-image-' . Str::random(8) . '.' . $imageInfo['extension'];

        try {
            $image = $imageRepo->saveNewFromData($imageName, $imageInfo['data'], 'gallery', $this->recipe->id);
        } catch (ImageUploadException $exception) {
            return '';
        }

        return $image->url;
    }

    /**
     * Parse a base64 image URI into the data and extension.
     *
     * @return array{extension: string, data: string}
     */
    protected function parseBase64ImageUri(string $uri): array
    {
        [$dataDefinition, $base64ImageData] = explode(',', $uri, 2);
        $extension = strtolower(preg_split('/[\/;]/', $dataDefinition)[1] ?? '');

        return [
            'extension' => $extension,
            'data' => base64_decode($base64ImageData) ?: '',
        ];
    }

    /**
     * Formats a page's html to be tagged correctly within the system.
     */
    protected function formatHtml(string $htmlText): string
    {
        if (empty($htmlText)) {
            return $htmlText;
        }

        $doc = $this->loadDocumentFromHtml($htmlText);
        $container = $doc->documentElement;
        $body = $container->childNodes->item(0);
        $childNodes = $body->childNodes;
        $xPath = new DOMXPath($doc);

        // Set ids on top-level nodes
        $idMap = [];
        foreach ($childNodes as $index => $childNode) {
            [$oldId, $newId] = $this->setUniqueId($childNode, $idMap);
            if ($newId && $newId !== $oldId) {
                $this->updateLinks($xPath, '#' . $oldId, '#' . $newId);
            }
        }

        // Set ids on nested header nodes
        $nestedHeaders = $xPath->query('//body//*//h1|//body//*//h2|//body//*//h3|//body//*//h4|//body//*//h5|//body//*//h6');
        foreach ($nestedHeaders as $nestedHeader) {
            [$oldId, $newId] = $this->setUniqueId($nestedHeader, $idMap);
            if ($newId && $newId !== $oldId) {
                $this->updateLinks($xPath, '#' . $oldId, '#' . $newId);
            }
        }

        // Ensure no duplicate ids within child items
        $idElems = $xPath->query('//body//*//*[@id]');
        foreach ($idElems as $domElem) {
            [$oldId, $newId] = $this->setUniqueId($domElem, $idMap);
            if ($newId && $newId !== $oldId) {
                $this->updateLinks($xPath, '#' . $oldId, '#' . $newId);
            }
        }

        // Generate inner html as a string
        $html = '';
        foreach ($childNodes as $childNode) {
            $html .= $doc->saveHTML($childNode);
        }

        return $html;
    }

    /**
     * Update the all links to the $old location to instead point to $new.
     */
    protected function updateLinks(DOMXPath $xpath, string $old, string $new)
    {
        $old = str_replace('"', '', $old);
        $matchingLinks = $xpath->query('//body//*//*[@href="' . $old . '"]');
        foreach ($matchingLinks as $domElem) {
            $domElem->setAttribute('href', $new);
        }
    }

    /**
     * Set a unique id on the given DOMElement.
     * A map for existing ID's should be passed in to check for current existence.
     * Returns a pair of strings in the format [old_id, new_id].
     */
    protected function setUniqueId(DOMNode $element, array &$idMap): array
    {
        if (!$element instanceof DOMElement) {
            return ['', ''];
        }

        // Stop if there's an existing valid id that has not already been used.
        $existingId = $element->getAttribute('id');
        if (strpos($existingId, 'bkmrk') === 0 && !isset($idMap[$existingId])) {
            $idMap[$existingId] = true;

            return [$existingId, $existingId];
        }

        // Create a unique id for the element
        // Uses the content as a basis to ensure output is the same every time
        // the same content is passed through.
        $contentId = 'bkmrk-' . mb_substr(strtolower(preg_replace('/\s+/', '-', trim($element->nodeValue))), 0, 20);
        $newId = urlencode($contentId);
        $loopIndex = 0;

        while (isset($idMap[$newId])) {
            $newId = urlencode($contentId . '-' . $loopIndex);
            $loopIndex++;
        }

        $element->setAttribute('id', $newId);
        $idMap[$newId] = true;

        return [$existingId, $newId];
    }

    /**
     * Get a plain-text visualisation of this page.
     */
    protected function toPlainText(): string
    {
        $html = $this->render(true);

        return html_entity_decode(strip_tags($html));
    }

    /**
     * Render the page for viewing.
     */
    public function render(bool $blankIncludes = false): string
    {
        $content = $this->recipe->html ?? '';

        if (!config('app.allow_content_scripts')) {
            $content = HtmlContentFilter::removeScripts($content);
        }

        if ($blankIncludes) {
            $content = $this->blankPageIncludes($content);
        } else {
            $content = $this->parsePageIncludes($content);
        }

        return $content;
    }

    /**
     * Parse the headers on the page to get a navigation menu.
     */
    public function getNavigation(string $htmlContent): array
    {
        if (empty($htmlContent)) {
            return [];
        }

        $doc = $this->loadDocumentFromHtml($htmlContent);
        $xPath = new DOMXPath($doc);
        $headers = $xPath->query('//h1|//h2|//h3|//h4|//h5|//h6');

        return $headers ? $this->headerNodesToLevelList($headers) : [];
    }

    /**
     * Convert a DOMNodeList into an array of readable header attributes
     * with levels normalised to the lower header level.
     */
    protected function headerNodesToLevelList(DOMNodeList $nodeList): array
    {
        $tree = collect($nodeList)->map(function (DOMElement $header) {
            $text = trim(str_replace("\xc2\xa0", '', $header->nodeValue));
            $text = mb_substr($text, 0, 100);

            return [
                'nodeName' => strtolower($header->nodeName),
                'level' => intval(str_replace('h', '', $header->nodeName)),
                'link' => '#' . $header->getAttribute('id'),
                'text' => $text,
            ];
        })->filter(function ($header) {
            return mb_strlen($header['text']) > 0;
        });

        // Shift headers if only smaller headers have been used
        $levelChange = ($tree->pluck('level')->min() - 1);
        $tree = $tree->map(function ($header) use ($levelChange) {
            $header['level'] -= ($levelChange);

            return $header;
        });

        return $tree->toArray();
    }

    /**
     * Remove any page include tags within the given HTML.
     */
    protected function blankPageIncludes(string $html): string
    {
        return preg_replace("/{{@\s?([0-9].*?)}}/", '', $html);
    }

    /**
     * Parse any include tags "{{@<page_id>#section}}" to be part of the page.
     */
    protected function parsePageIncludes(string $html): string
    {
        $matches = [];
        preg_match_all("/{{@\s?([0-9].*?)}}/", $html, $matches);

        foreach ($matches[1] as $index => $includeId) {
            $fullMatch = $matches[0][$index];
            $splitInclude = explode('#', $includeId, 2);

            // Get page id from reference
            $recipeId = intval($splitInclude[0]);
            if (is_nan($recipeId)) {
                continue;
            }

            // Find page and skip this if page not found
            /** @var ?Recipe $matchedPage */
            $matchedPage = Recipe::visible()->find($recipeId);
            if ($matchedPage === null) {
                $html = str_replace($fullMatch, '', $html);
                continue;
            }

            // If we only have page id, just insert all page html and continue.
            if (count($splitInclude) === 1) {
                $html = str_replace($fullMatch, $matchedPage->html, $html);
                continue;
            }

            // Create and load HTML into a document
            $innerContent = $this->fetchSectionOfPage($matchedPage, $splitInclude[1]);
            $html = str_replace($fullMatch, trim($innerContent), $html);
        }

        return $html;
    }

    /**
     * Fetch the content from a specific section of the given page.
     */
    protected function fetchSectionOfPage(Recipe $page, string $sectionId): string
    {
        $topLevelTags = ['table', 'ul', 'ol', 'pre'];
        $doc = $this->loadDocumentFromHtml($page->html);

        // Search included content for the id given and blank out if not exists.
        $matchingElem = $doc->getElementById($sectionId);
        if ($matchingElem === null) {
            return '';
        }

        // Otherwise replace the content with the found content
        // Checks if the top-level wrapper should be included by matching on tag types
        $innerContent = '';
        $isTopLevel = in_array(strtolower($matchingElem->nodeName), $topLevelTags);
        if ($isTopLevel) {
            $innerContent .= $doc->saveHTML($matchingElem);
        } else {
            foreach ($matchingElem->childNodes as $childNode) {
                $innerContent .= $doc->saveHTML($childNode);
            }
        }
        libxml_clear_errors();

        return $innerContent;
    }

    /**
     * Create and load a DOMDocument from the given html content.
     */
    protected function loadDocumentFromHtml(string $html): DOMDocument
    {
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $html = '<body>' . $html . '</body>';
        $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

        return $doc;
    }
}
