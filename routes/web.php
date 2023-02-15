<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SamlController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\StatsController;

use App\Http\Controllers\AjaxController;
use App\Http\Controllers\AdminPaginationController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* Optional endpoints */
if (env('POLR_ALLOW_ACCT_CREATION')) {
    Route::get('/signup', [UserController::class, 'displaySignupPage'])->name('signup');
    Route::post('/signup', [UserController::class, 'performSignup'])->name('psignup');
}

/* Optional SAML endpoints */
if (in_array('SAML', explode(',', env('POLR_LOGIN_MODES')))) {
    Route::get('/saml/sp/acs', [SamlController::class, 'processLogin'])->name('acs');
    Route::post('/saml/sp/acs', [SamlController::class, 'processLogin'])->name('pacs');
    Route::get('/saml/sp/metadata', [SamlController::class, 'sendMetadata'])->name('metadata');
    Route::get('/saml/sp/sls', [SamlController::class, 'processLogout'])->name('slo');
    Route::post('/saml/sp/sls', [SamlController::class, 'processLogout'])->name('pslo');
}

/* GET endpoints */

Route::get('/', [IndexController::class, 'showIndexPage'])->name('index');
Route::get('/logout', [UserController::class, 'performLogoutUser'])->name('logout');
Route::get('/login', [UserController::class, 'displayLoginPage'])->name('login');
Route::get('/about-polr', [StaticPageController::class, 'displayAbout'])->name('about');

Route::get('/lost_password', [UserController::class, 'displayLostPasswordPage'])->name('lost_password');
Route::get('/activate/{username}/{recovery_key}', [UserController::class, 'performActivation'])->name('activate');
Route::get('/reset_password/{username}/{recovery_key}', [UserController::class, 'performPasswordReset'])->name('reset_password');

Route::get('/admin', [AdminController::class, 'displayAdminPage'])->name('admin');

Route::get('/setup', [SetupController::class, 'displaySetupPage'])->name('setup');
Route::post('/setup', [SetupController::class, 'performSetup'])->name('psetup');
Route::get('/setup/finish', [SetupController::class, 'finishSetup'])->name('setup_finish');

Route::get('/{short_url}', [LinkController::class, 'performRedirect']);
Route::get('/{short_url}/{secret_key}', [LinkController::class, 'performRedirect']);

Route::get('/admin/stats/{short_url}', [StatsController::class, 'displayStats']);

/* POST endpoints */

Route::post('/login', [UserController::class, 'performLogin'])->name('plogin');
Route::post('/shorten', [LinkController::class, 'performShorten'])->name('pshorten');
Route::post('/lost_password', [UserController::class, 'performSendPasswordResetCode'])->name('plost_password');
Route::post('/reset_password/{username}/{recovery_key}', [UserController::class, 'performPasswordReset'])->name('preset_password');

Route::post('/admin/action/change_password', [AdminController::class, 'changePassword'])->name('change_password');

Route::prefix('api/v2')->group(function () {
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