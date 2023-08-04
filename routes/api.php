<?php

use Illuminate\Support\Facades\Route;

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

Route::prefix('api/v2')->group(function () {

    /* API shorten endpoints */
    Route::post('action/shorten', [ApiLinkController::class, 'shortenLink'])->name('v2_api_shorten_url_post');
    Route::get('action/shorten', [ApiLinkController::class, 'shortenLink'])->name('v2_api_shorten_url_get');
    Route::post('action/shorten_bulk', [ApiLinkController::class, 'shortenLinksBulk'])->name('v2_api_shorten_url_bulk_post');

    /* API lookup endpoints */
    Route::post('action/lookup', [ApiLinkController::class, 'lookupLink'])->name('v2_api_lookup_url_post');
    Route::get('action/lookup', [ApiLinkController::class, 'lookupLink'])->name('v2_api_lookup_url_get');

    /* API data endpoints */
    Route::get('data/link', [ApiAnalyticsController::class, 'lookupLinkStats'])->name('v2_api_link_analytics_get');
    Route::post('data/link', [ApiAnalyticsController::class, 'lookupLinkStats'])->name('v2_api_link_analytics_post');

    /*API delete endpoints */
    Route::get('admin/delete_link', [ApiLinkController::class, 'deleteLink'])->name('v2_api_delete_link_get');
});
