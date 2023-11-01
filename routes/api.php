<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AjaxController;
use App\Http\Controllers\AdminPaginationController;
use App\Http\Controllers\Api\ApiLinkController;
use App\Http\Controllers\Api\ApiAnalyticsController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v2')->group(function () {
    Route::post('link_avail_check', [AjaxController::class, 'checkLinkAvailability'])->name('api_link_check');
    Route::post('admin/toggle_api_active', [AjaxController::class, 'toggleAPIActive'])->name('api_toggle_api_active');
    Route::post('admin/generate_new_api_key', [AjaxController::class, 'generateNewAPIKey'])->name('api_generate_new_api_key');
    Route::post('admin/edit_api_quota', [AjaxController::class, 'editAPIQuota'])->name('api_edit_quota');
    Route::post('admin/toggle_user_active', [AjaxController::class, 'toggleUserActive'])->name('api_toggle_user_active');
    Route::post('admin/change_user_role', [AjaxController::class, 'changeUserRole'])->name('api_change_user_role');
    Route::post('admin/add_new_user', [AjaxController::class, 'addNewUser'])->name('api_add_new_user');
    Route::post('admin/delete_user', [AjaxController::class, 'deleteUser'])->name('api_delete_user');
    Route::post('admin/toggle_link', [AjaxController::class, 'toggleLink'])->name('api_toggle_link');
    Route::post('admin/delete_link', [AjaxController::class, 'deleteLink'])->name('api_delete_link');
    Route::post('admin/edit_link_long_url', [AjaxController::class, 'editLinkLongUrl'])->name('api_edit_link_long_url');

    Route::get('admin/get_admin_users', [AdminPaginationController::class, 'paginateAdminUsers'])->name('api_get_admin_users');
    Route::get('admin/get_admin_links', [AdminPaginationController::class, 'paginateAdminLinks'])->name('api_get_admin_links');
    Route::get('admin/get_user_links', [AdminPaginationController::class, 'paginateUserLinks'])->name('api_get_user_links');
});

Route::prefix('v2')->group(function () {
    /* API shorten endpoints */
    Route::get('action/shorten', [ApiLinkController::class, 'shortenLink'])->name('api_shorten_url');
    Route::post('action/shorten', [ApiLinkController::class, 'shortenLink'])->name('api_shorten_url');
    Route::post('action/shorten_bulk', [ApiLinkController::class, 'shortenLinksBulk'])->name('api_shorten_url_bulk');

    /* API lookup endpoints */
    Route::get('action/lookup', [ApiLinkController::class, 'lookupLink'])->name('api_lookup_url');
    Route::post('action/lookup', [ApiLinkController::class, 'lookupLink'])->name('api_lookup_url');

    /* API data endpoints */
    Route::get('data/link', [ApiAnalyticsController::class, 'lookupLinkStats'])->name('api_link_analytics');
    Route::post('data/link', [ApiAnalyticsController::class, 'lookupLinkStats'])->name('api_link_analytics');
});
