<?php

use DailyRecipe\Http\Controllers\Api\ApiDocsController;
use DailyRecipe\Http\Controllers\Api\AttachmentApiController;
use DailyRecipe\Http\Controllers\Api\BookApiController;
use DailyRecipe\Http\Controllers\Api\BookExportApiController;
use DailyRecipe\Http\Controllers\Api\BookshelfApiController;
use DailyRecipe\Http\Controllers\Api\ChapterApiController;
use DailyRecipe\Http\Controllers\Api\ChapterExportApiController;
use DailyRecipe\Http\Controllers\Api\PageApiController;
use DailyRecipe\Http\Controllers\Api\PageExportApiController;
use DailyRecipe\Http\Controllers\Api\SearchApiController;
use Illuminate\Support\Facades\Route;

/**
 * Routes for the BookStack API.
 * Routes have a uri prefix of /api/.
 * Controllers are all within app/Http/Controllers/Api.
 */
Route::get('docs.json', [ApiDocsController::class, 'json']);

Route::get('attachments', [AttachmentApiController::class, 'list']);
Route::post('attachments', [AttachmentApiController::class, 'create']);
Route::get('attachments/{id}', [AttachmentApiController::class, 'read']);
Route::put('attachments/{id}', [AttachmentApiController::class, 'update']);
Route::delete('attachments/{id}', [AttachmentApiController::class, 'delete']);

Route::get('recipes', [BookApiController::class, 'list']);
Route::post('recipes', [BookApiController::class, 'create']);
Route::get('recipes/{id}', [BookApiController::class, 'read']);
Route::put('recipes/{id}', [BookApiController::class, 'update']);
Route::delete('recipes/{id}', [BookApiController::class, 'delete']);

Route::get('recipes/{id}/export/html', [BookExportApiController::class, 'exportHtml']);
Route::get('recipes/{id}/export/pdf', [BookExportApiController::class, 'exportPdf']);
Route::get('recipes/{id}/export/plaintext', [BookExportApiController::class, 'exportPlainText']);
Route::get('recipes/{id}/export/markdown', [BookExportApiController::class, 'exportMarkdown']);

Route::get('chapters', [ChapterApiController::class, 'list']);
Route::post('chapters', [ChapterApiController::class, 'create']);
Route::get('chapters/{id}', [ChapterApiController::class, 'read']);
Route::put('chapters/{id}', [ChapterApiController::class, 'update']);
Route::delete('chapters/{id}', [ChapterApiController::class, 'delete']);

Route::get('chapters/{id}/export/html', [ChapterExportApiController::class, 'exportHtml']);
Route::get('chapters/{id}/export/pdf', [ChapterExportApiController::class, 'exportPdf']);
Route::get('chapters/{id}/export/plaintext', [ChapterExportApiController::class, 'exportPlainText']);
Route::get('chapters/{id}/export/markdown', [ChapterExportApiController::class, 'exportMarkdown']);

Route::get('pages', [PageApiController::class, 'list']);
Route::post('pages', [PageApiController::class, 'create']);
Route::get('pages/{id}', [PageApiController::class, 'read']);
Route::put('pages/{id}', [PageApiController::class, 'update']);
Route::delete('pages/{id}', [PageApiController::class, 'delete']);

Route::get('pages/{id}/export/html', [PageExportApiController::class, 'exportHtml']);
Route::get('pages/{id}/export/pdf', [PageExportApiController::class, 'exportPdf']);
Route::get('pages/{id}/export/plaintext', [PageExportApiController::class, 'exportPlainText']);
Route::get('pages/{id}/export/markdown', [PageExportApiController::class, 'exportMarkDown']);

Route::get('search', [SearchApiController::class, 'all']);

Route::get('menus', [BookshelfApiController::class, 'list']);
Route::post('menus', [BookshelfApiController::class, 'create']);
Route::get('menus/{id}', [BookshelfApiController::class, 'read']);
Route::put('menus/{id}', [BookshelfApiController::class, 'update']);
Route::delete('menus/{id}', [BookshelfApiController::class, 'delete']);
