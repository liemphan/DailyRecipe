<?php

use DailyRecipe\Http\Controllers\Api;
use DailyRecipe\Http\Controllers\AttachmentController;
use DailyRecipe\Http\Controllers\AuditLogController;
use DailyRecipe\Http\Controllers\Auth;
use DailyRecipe\Http\Controllers\RecipeController;
use DailyRecipe\Http\Controllers\RecipeExportController;
use DailyRecipe\Http\Controllers\RecipemenuController;
use DailyRecipe\Http\Controllers\RecipeSortController;

use DailyRecipe\Http\Controllers\CommentController;
use DailyRecipe\Http\Controllers\FavouriteController;
use DailyRecipe\Http\Controllers\HomeController;
use DailyRecipe\Http\Controllers\Images;
use DailyRecipe\Http\Controllers\MaintenanceController;
use DailyRecipe\Http\Controllers\PageController;
use DailyRecipe\Http\Controllers\ReportController;
use DailyRecipe\Http\Controllers\PageExportController;
use DailyRecipe\Http\Controllers\RecipeRevisionController;
use DailyRecipe\Http\Controllers\RecipeTemplateController;
use DailyRecipe\Http\Controllers\RecycleBinController;
use DailyRecipe\Http\Controllers\RoleController;
use DailyRecipe\Http\Controllers\SearchController;
use DailyRecipe\Http\Controllers\SettingController;
use DailyRecipe\Http\Controllers\StatusController;
use DailyRecipe\Http\Controllers\TagController;
use DailyRecipe\Http\Controllers\UserApiTokenController;
use DailyRecipe\Http\Controllers\UserController;
use DailyRecipe\Http\Controllers\UserProfileController;
use DailyRecipe\Http\Controllers\UserSearchController;
use DailyRecipe\Http\Middleware\VerifyCsrfToken;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use DailyRecipe\Http\Controllers\IdentifiedIngredientsController;

Route::get('/status', [StatusController::class, 'show']);
Route::get('/robots.txt', [HomeController::class, 'robots']);

// Authenticated routes...
Route::middleware('auth')->group(function () {

    // Secure images routing
    Route::get('/uploads/images/{path}', [Images\ImageController::class, 'showImage'])
        ->where('path', '.*$');

    // API docs routes
    Route::redirect('/api', '/api/docs');
    Route::get('/api/docs', [Api\ApiDocsController::class, 'display']);

    Route::get('/pages/recently-updated', [RecipeController::class, 'showRecentlyUpdated']);

    // Menus
    Route::get('/create-menu', [RecipemenuController::class, 'create']);
    Route::get('/menus/', [RecipemenuController::class, 'index']);
    Route::post('/menus/', [RecipemenuController::class, 'store']);
    Route::get('/menus/{slug}/edit', [RecipemenuController::class, 'edit']);
    Route::get('/menus/{slug}/delete', [RecipemenuController::class, 'showDelete']);
    Route::get('/menus/{slug}', [RecipemenuController::class, 'show']);
    Route::put('/menus/{slug}', [RecipemenuController::class, 'update']);
    Route::delete('/menus/{slug}', [RecipemenuController::class, 'destroy']);
    Route::get('/menus/{slug}/permissions', [RecipemenuController::class, 'showPermissions']);
    Route::put('/menus/{slug}/permissions', [RecipemenuController::class, 'permissions']);
    Route::post('/menus/{slug}/copy-permissions', [RecipemenuController::class, 'copyPermissions']);

    // Recipe Creation
    Route::get('/menus/{menuSlug}/create-recipe', [RecipeController::class, 'create']);
    Route::post('/menus/{menuSlug}/create-recipe', [RecipeController::class, 'store']);
    Route::get('/create-recipe', [RecipeController::class, 'create']);

    // Recipes
    Route::get('/recipes/', [RecipeController::class, 'index']);
    Route::post('/recipes/', [RecipeController::class, 'store']);
    Route::get('/recipes/{slug}/edit', [RecipeController::class, 'edit']);
    Route::put('/recipes/{slug}', [RecipeController::class, 'update']);
    Route::delete('/recipes/{id}', [RecipeController::class, 'destroy']);
    Route::get('/recipes/{slug}/sort-item', [RecipeSortController::class, 'showItem']);
    Route::get('/recipes/{slug}', [RecipeController::class, 'showContent']);
    Route::get('/recipes/{recipeSlug}/permissions', [RecipeController::class, 'showPermissions']);

    Route::put('/recipes/{recipeSlug}/permissions', [RecipeController::class, 'permissions']);
    Route::get('/recipes/{slug}/delete', [RecipeController::class, 'showDelete']);
    Route::get('/recipes/{recipeSlug}/sort', [RecipeSortController::class, 'show']);
    Route::put('/recipes/{recipeSlug}/sort', [RecipeSortController::class, 'update']);
    Route::get('/recipes/{recipeSlug}/export/html', [RecipeExportController::class, 'html']);
    Route::get('/recipes/{recipeSlug}/export/pdf', [RecipeExportController::class, 'pdf']);
    Route::get('/recipes/{recipeSlug}/export/markdown', [RecipeExportController::class, 'markdown']);
    Route::get('/recipes/{recipeSlug}/export/zip', [RecipeExportController::class, 'zip']);
    Route::get('/recipes/{recipeSlug}/export/plaintext', [RecipeExportController::class, 'plainText']);


    Route::get('/recipes/{recipeSlug}/draft', [RecipeController::class, 'editDraft']);
    Route::post('/recipes/{recipeSlug}/draft', [RecipeController::class, 'storeContent']);
    Route::get('/recipes/{recipeSlug}/content', [RecipeController::class, 'showContent']);
    Route::get('/recipes/{recipeSlug}/content/edit', [RecipeController::class, 'editContent']);
    Route::put('/recipes/{recipeSlug}/content', [RecipeController::class, 'updateContent']);
    Route::post('/recipes/{recipeSlug}/content-guest-page', [RecipeController::class, 'createAsGuest']);

    // Pages
   // Route::get('/recipes/{recipeSlug}/create-page', [PageController::class, 'create']);
   // Route::post('/recipes/{recipeSlug}/create-guest-page', [PageController::class, 'createAsGuest']);
  //  Route::get('/recipes/{recipeSlug}/draft/{pageId}', [PageController::class, 'editDraft']);
   // Route::post('/recipes/{recipeSlug}/draft/{pageId}', [PageController::class, 'store']);
   // Route::get('/recipes/{recipeSlug}/page/{pageSlug}', [PageController::class, 'show']);
    Route::get('/recipes/{recipeSlug}/page/{pageSlug}/export/pdf', [PageExportController::class, 'pdf']);
    Route::get('/recipes/{recipeSlug}/page/{pageSlug}/export/html', [PageExportController::class, 'html']);
    Route::get('/recipes/{recipeSlug}/page/{pageSlug}/export/markdown', [PageExportController::class, 'markdown']);
    Route::get('/recipes/{recipeSlug}/page/{pageSlug}/export/plaintext', [PageExportController::class, 'plainText']);
 //   Route::get('/recipes/{recipeSlug}/page/{pageSlug}/edit', [PageController::class, 'edit']);
//    Route::get('/recipes/{recipeSlug}/page/{pageSlug}/move', [PageController::class, 'showMove']);
//    Route::put('/recipes/{recipeSlug}/page/{pageSlug}/move', [PageController::class, 'move']);
//    Route::get('/recipes/{recipeSlug}/page/{pageSlug}/copy', [PageController::class, 'showCopy']);
//    Route::post('/recipes/{recipeSlug}/page/{pageSlug}/copy', [PageController::class, 'copy']);
//    Route::get('/recipes/{recipeSlug}/page/{pageSlug}/delete', [PageController::class, 'showDelete']);
//    Route::get('/recipes/{recipeSlug}/draft/{pageId}/delete', [PageController::class, 'showDeleteDraft']);

    Route::get('/recipes/{recipeSlug}/content/{pageSlug}/permissions', [RecipeController::class, 'showPermissionsContent']);
    Route::put('/recipes/{recipeSlug}/content/{pageSlug}/permissions', [RecipeController::class, 'permissionsContent']);
  //  Route::put('/recipes/{recipeSlug}/page/{pageSlug}', [PageController::class, 'update']);
   // Route::delete('/recipes/{recipeSlug}/page/{pageSlug}', [PageController::class, 'destroy']);

    Route::delete('/recipes/{recipeSlug}/draft/{pageId}', [RecipeController::class, 'destroyDraft']);

    // Revisions
    Route::get('/recipes/{recipeSlug}/revisions', [RecipeRevisionController::class, 'index']);
    Route::get('/recipes/{recipeSlug}/content/revisions/{revId}', [RecipeRevisionController::class, 'show']);
    Route::get('/recipes/{recipeSlug}/content/revisions/{revId}/changes', [RecipeRevisionController::class, 'changes']);
    Route::put('/recipes/{recipeSlug}/content/revisions/{revId}/restore', [RecipeRevisionController::class, 'restore']);
    Route::delete('/recipes/{recipeSlug}/content/revisions/{revId}/delete', [RecipeRevisionController::class, 'destroy']);
//
//    // Chapters
//    Route::get('/recipes/{recipeSlug}/chapter/{chapterSlug}/create-page', [PageController::class, 'create']);
//    Route::post('/recipes/{recipeSlug}/chapter/{chapterSlug}/create-guest-page', [PageController::class, 'createAsGuest']);
//    Route::get('/recipes/{recipeSlug}/create-chapter', [ChapterController::class, 'create']);
//    Route::post('/recipes/{recipeSlug}/create-chapter', [ChapterController::class, 'store']);
//    Route::get('/recipes/{recipeSlug}/chapter/{chapterSlug}', [ChapterController::class, 'show']);
//    Route::put('/recipes/{recipeSlug}/chapter/{chapterSlug}', [ChapterController::class, 'update']);
//    Route::get('/recipes/{recipeSlug}/chapter/{chapterSlug}/move', [ChapterController::class, 'showMove']);
//    Route::put('/recipes/{recipeSlug}/chapter/{chapterSlug}/move', [ChapterController::class, 'move']);
//    Route::get('/recipes/{recipeSlug}/chapter/{chapterSlug}/edit', [ChapterController::class, 'edit']);
//    Route::get('/recipes/{recipeSlug}/chapter/{chapterSlug}/permissions', [ChapterController::class, 'showPermissions']);
//    Route::get('/recipes/{recipeSlug}/chapter/{chapterSlug}/export/pdf', [ChapterExportController::class, 'pdf']);
//    Route::get('/recipes/{recipeSlug}/chapter/{chapterSlug}/export/html', [ChapterExportController::class, 'html']);
//    Route::get('/recipes/{recipeSlug}/chapter/{chapterSlug}/export/markdown', [ChapterExportController::class, 'markdown']);
//    Route::get('/recipes/{recipeSlug}/chapter/{chapterSlug}/export/plaintext', [ChapterExportController::class, 'plainText']);
//    Route::put('/recipes/{recipeSlug}/chapter/{chapterSlug}/permissions', [ChapterController::class, 'permissions']);
//    Route::get('/recipes/{recipeSlug}/chapter/{chapterSlug}/delete', [ChapterController::class, 'showDelete']);
//    Route::delete('/recipes/{recipeSlug}/chapter/{chapterSlug}', [ChapterController::class, 'destroy']);

    // User Profile routes
    Route::get('/user/{slug}', [UserProfileController::class, 'show']);

    // Image routes
    Route::get('/images/gallery', [Images\GalleryImageController::class, 'list']);
    Route::post('/images/gallery', [Images\GalleryImageController::class, 'create']);
    Route::get('/images/drawio', [Images\DrawioImageController::class, 'list']);
    Route::get('/images/drawio/base64/{id}', [Images\DrawioImageController::class, 'getAsBase64']);
    Route::post('/images/drawio', [Images\DrawioImageController::class, 'create']);
    Route::get('/images/edit/{id}', [Images\ImageController::class, 'edit']);
    Route::put('/images/{id}', [Images\ImageController::class, 'update']);
    Route::delete('/images/{id}', [Images\ImageController::class, 'destroy']);

    // Attachments routes
    Route::get('/attachments/{id}', [AttachmentController::class, 'get']);
    Route::post('/attachments/upload', [AttachmentController::class, 'upload']);
    Route::post('/attachments/upload/{id}', [AttachmentController::class, 'uploadUpdate']);
    Route::post('/attachments/link', [AttachmentController::class, 'attachLink']);
    Route::put('/attachments/{id}', [AttachmentController::class, 'update']);
    Route::get('/attachments/edit/{id}', [AttachmentController::class, 'getUpdateForm']);
    Route::get('/attachments/get/page/{pageId}', [AttachmentController::class, 'listForPage']);
    Route::put('/attachments/sort/page/{pageId}', [AttachmentController::class, 'sortForPage']);
    Route::delete('/attachments/{id}', [AttachmentController::class, 'delete']);

    // AJAX routes
    Route::put('/ajax/content/{id}/save-draft', [RecipeController::class, 'saveDraft']);
    Route::get('/ajax/content/{id}', [RecipeController::class, 'getPageAjax']);
    Route::delete('/ajax/page/{id}', [PageController::class, 'ajaxDestroy']);

    // Tag routes
    Route::get('/tags', [TagController::class, 'index']);
    Route::get('/ajax/tags/suggest/names', [TagController::class, 'getNameSuggestions']);
    Route::get('/ajax/tags/suggest/values', [TagController::class, 'getValueSuggestions']);

    Route::get('/ajax/search/entities', [SearchController::class, 'searchEntitiesAjax']);

    // Comments
    Route::post('/comment/{pageId}', [CommentController::class, 'savePageComment']);
    Route::put('/comment/{id}', [CommentController::class, 'update']);
    Route::delete('/comment/{id}', [CommentController::class, 'destroy']);

    // Links
    Route::get('/link/{id}', [RecipeController::class, 'redirectFromLink']);

    // Search
    Route::get('/search', [SearchController::class, 'search']);
    Route::get('/search/recipe/{recipeId}', [SearchController::class, 'searchRecipe']);
    Route::get('/search/chapter/{recipeId}', [SearchController::class, 'searchChapter']);
    Route::get('/search/entity/siblings', [SearchController::class, 'searchSiblings']);

    // User Search
    Route::get('/search/users/select', [UserSearchController::class, 'forSelect']);

    // Template System
    Route::get('/templates', [RecipeTemplateController::class, 'list']);
    Route::get('/templates/{templateId}', [RecipeTemplateController::class, 'get']);

    // Favourites
    Route::get('/favourites', [FavouriteController::class, 'index']);
    Route::post('/favourites/add', [FavouriteController::class, 'add']);
    Route::post('/favourites/remove', [FavouriteController::class, 'remove']);

    // Other Pages
    Route::get('/', [HomeController::class, 'index']);
    Route::get('/home', [HomeController::class, 'index']);
    Route::get('/custom-head-content', [HomeController::class, 'customHeadContent']);

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::post('/settings', [SettingController::class, 'update']);

    // Maintenance
    Route::get('/settings/maintenance', [MaintenanceController::class, 'index']);
    Route::delete('/settings/maintenance/cleanup-images', [MaintenanceController::class, 'cleanupImages']);
    Route::post('/settings/maintenance/send-test-email', [MaintenanceController::class, 'sendTestEmail']);

    // Recycle Bin
    Route::get('/settings/recycle-bin', [RecycleBinController::class, 'index']);
    Route::post('/settings/recycle-bin/empty', [RecycleBinController::class, 'empty']);
    Route::get('/settings/recycle-bin/{id}/destroy', [RecycleBinController::class, 'showDestroy']);
    Route::delete('/settings/recycle-bin/{id}', [RecycleBinController::class, 'destroy']);
    Route::get('/settings/recycle-bin/{id}/restore', [RecycleBinController::class, 'showRestore']);
    Route::post('/settings/recycle-bin/{id}/restore', [RecycleBinController::class, 'restore']);

    // Audit Log
    Route::get('/settings/audit', [AuditLogController::class, 'index']);

    // Users
    Route::get('/settings/users', [UserController::class, 'index']);
    Route::get('/settings/users/create', [UserController::class, 'create']);
    Route::get('/settings/users/{id}/delete', [UserController::class, 'delete']);
    Route::patch('/settings/users/{id}/switch-recipes-view', [UserController::class, 'switchRecipesView']);
    Route::patch('/settings/users/{id}/switch-menus-view', [UserController::class, 'switchMenusView']);
    Route::patch('/settings/users/{id}/switch-menu-view', [UserController::class, 'switchMenuView']);
    Route::patch('/settings/users/{id}/change-sort/{type}', [UserController::class, 'changeSort']);
    Route::patch('/settings/users/{id}/update-expansion-preference/{key}', [UserController::class, 'updateExpansionPreference']);
    Route::patch('/settings/users/toggle-dark-mode', [UserController::class, 'toggleDarkMode']);
    Route::post('/settings/users/create', [UserController::class, 'store']);
    Route::get('/settings/users/{id}', [UserController::class, 'edit']);
    Route::put('/settings/users/{id}', [UserController::class, 'update']);
    Route::delete('/settings/users/{id}', [UserController::class, 'destroy']);

    // User API Tokens
    Route::get('/settings/users/{userId}/create-api-token', [UserApiTokenController::class, 'create']);
    Route::post('/settings/users/{userId}/create-api-token', [UserApiTokenController::class, 'store']);
    Route::get('/settings/users/{userId}/api-tokens/{tokenId}', [UserApiTokenController::class, 'edit']);
    Route::put('/settings/users/{userId}/api-tokens/{tokenId}', [UserApiTokenController::class, 'update']);
    Route::get('/settings/users/{userId}/api-tokens/{tokenId}/delete', [UserApiTokenController::class, 'delete']);
    Route::delete('/settings/users/{userId}/api-tokens/{tokenId}', [UserApiTokenController::class, 'destroy']);

    // Roles
    Route::get('/settings/roles', [RoleController::class, 'list']);
    Route::get('/settings/roles/new', [RoleController::class, 'create']);
    Route::post('/settings/roles/new', [RoleController::class, 'store']);
    Route::get('/settings/roles/delete/{id}', [RoleController::class, 'showDelete']);
    Route::delete('/settings/roles/delete/{id}', [RoleController::class, 'delete']);
    Route::get('/settings/roles/{id}', [RoleController::class, 'edit']);
    Route::put('/settings/roles/{id}', [RoleController::class, 'update']);
});

// MFA routes
Route::middleware('mfa-setup')->group(function () {
    Route::get('/mfa/setup', [Auth\MfaController::class, 'setup']);
    Route::get('/mfa/totp/generate', [Auth\MfaTotpController::class, 'generate']);
    Route::post('/mfa/totp/confirm', [Auth\MfaTotpController::class, 'confirm']);
    Route::get('/mfa/backup_codes/generate', [Auth\MfaBackupCodesController::class, 'generate']);
    Route::post('/mfa/backup_codes/confirm', [Auth\MfaBackupCodesController::class, 'confirm']);
});
Route::middleware('guest')->group(function () {
    Route::get('/mfa/verify', [Auth\MfaController::class, 'verify']);
    Route::post('/mfa/totp/verify', [Auth\MfaTotpController::class, 'verify']);
    Route::post('/mfa/backup_codes/verify', [Auth\MfaBackupCodesController::class, 'verify']);
});
Route::delete('/mfa/{method}/remove', [Auth\MfaController::class, 'remove'])->middleware('auth');

// Social auth routes
Route::get('/login/service/{socialDriver}', [Auth\SocialController::class, 'login']);
Route::get('/login/service/{socialDriver}/callback', [Auth\SocialController::class, 'callback']);
Route::post('/login/service/{socialDriver}/detach', [Auth\SocialController::class, 'detach'])->middleware('auth');
Route::get('/register/service/{socialDriver}', [Auth\SocialController::class, 'register']);

// Login/Logout routes
Route::get('/login', [Auth\LoginController::class, 'getLogin']);
Route::post('/login', [Auth\LoginController::class, 'login']);
Route::post('/logout', [Auth\LoginController::class, 'logout']);
Route::get('/register', [Auth\RegisterController::class, 'getRegister']);
Route::get('/register/confirm', [Auth\ConfirmEmailController::class, 'show']);
Route::get('/register/confirm/awaiting', [Auth\ConfirmEmailController::class, 'showAwaiting']);
Route::post('/register/confirm/resend', [Auth\ConfirmEmailController::class, 'resend']);
Route::get('/register/confirm/{token}', [Auth\ConfirmEmailController::class, 'confirm']);
Route::post('/register', [Auth\RegisterController::class, 'postRegister']);

// SAML routes
Route::post('/saml2/login', [Auth\Saml2Controller::class, 'login']);
Route::post('/saml2/logout', [Auth\Saml2Controller::class, 'logout']);
Route::get('/saml2/metadata', [Auth\Saml2Controller::class, 'metadata']);
Route::get('/saml2/sls', [Auth\Saml2Controller::class, 'sls']);
Route::post('/saml2/acs', [Auth\Saml2Controller::class, 'startAcs'])->withoutMiddleware([
    StartSession::class,
    ShareErrorsFromSession::class,
    VerifyCsrfToken::class,
]);
Route::get('/saml2/acs', [Auth\Saml2Controller::class, 'processAcs']);

// OIDC routes
Route::post('/oidc/login', [Auth\OidcController::class, 'login']);
Route::get('/oidc/callback', [Auth\OidcController::class, 'callback']);

// User invitation routes
Route::get('/register/invite/{token}', [Auth\UserInviteController::class, 'showSetPassword']);
Route::post('/register/invite/{token}', [Auth\UserInviteController::class, 'setPassword']);

// Password reset link request routes...
Route::get('/password/email', [Auth\ForgotPasswordController::class, 'showLinkRequestForm']);
Route::post('/password/email', [Auth\ForgotPasswordController::class, 'sendResetLinkEmail']);

// Password reset routes...
Route::get('/password/reset/{token}', [Auth\ResetPasswordController::class, 'showResetForm']);
Route::post('/password/reset', [Auth\ResetPasswordController::class, 'reset']);

Route::fallback([HomeController::class, 'notFound'])->name('fallback');


Route::get('/search/identified/ingredients', [IdentifiedIngredientsController::class, 'index']);

// Report
Route::get('/recipes/{recipeSlug}/report', [ReportController::class, 'showReport']);
Route::post('/recipes/{recipeSlug}/storeReport/', [ReportController::class, 'store']);

//Report List
Route::get('/settings/reportlist', [ReportController::class, 'reportList']);
Route::get('/recipes/{slug}/{id}/deactive', [ReportController::class, 'showDelete']);
//Route::post('/recipes/{slug}/deactive', [ReportController::class, 'showDelete']);
