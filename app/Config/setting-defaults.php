<?php

/**
 * Default system settings.
 *
 * Changes to these config files are not supported by DailyRecipe and may break upon updates.
 * Configuration should be altered via the `.env` file or environment variables.
 * Do not edit this file unless you're happy to maintain any changes yourself.
 */

return [

    'app-name'             => 'DailyRecipe',
    'app-logo'             => '',
    'app-name-header'      => true,
    'app-editor'           => 'wysiwyg',
    'app-color'            => '#206ea7',
    'app-color-light'      => 'rgba(32,110,167,0.15)',
    'bookshelf-color'      => '#a94747',
    'book-color'           => '#077b70',
    'chapter-color'        => '#af4d0d',
    'page-color'           => '#206ea7',
    'page-draft-color'     => '#7e50b1',
    'app-custom-head'      => false,
    'registration-enabled' => false,

    // User-level default settings
    'user' => [
        'dark-mode-enabled'     => env('APP_DEFAULT_DARK_MODE', false),
        'bookshelves_view_type' => env('APP_VIEWS_BOOKSHELVES', 'grid'),
        'bookshelf_view_type'   => env('APP_VIEWS_BOOKSHELF', 'grid'),
        'books_view_type'       => env('APP_VIEWS_BOOKS', 'grid'),
    ],

];
