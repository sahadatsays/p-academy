<?php

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'middleware' => 'api'], function () {
    Route::post('login', [App\Http\Controllers\Api\Auth\AdminAuthController::class, 'login'])->name('login');

    Route::group(['middleware' => ['auth:api', 'checkAdmin']], function () {
        Route::post('logout', [App\Http\Controllers\Api\Auth\AdminAuthController::class, 'logout'])->name('api.logout');
        
        Route::get('user', function (Request $request) {
            return new UserResource($request->user());
        });

        Route::get('articles/fetch/medias/{article}', [App\Http\Controllers\Api\AdminArticleController::class, 'fetchArticleMedias']);
        Route::post('articles/set/media', [App\Http\Controllers\Api\AdminArticleController::class, 'addMedia']);
        Route::post('articles/set/media/key', [App\Http\Controllers\Api\AdminArticleController::class, 'mediaKeyChange']);
        Route::get('articles/delete/media/{media}', [App\Http\Controllers\Api\AdminArticleController::class, 'deleteMedia']);
        Route::post('articles/set/tag', [App\Http\Controllers\Api\AdminArticleController::class, 'setTagToArticle']);
        Route::post('affiliations/update-status', [App\Http\Controllers\Api\AdminAffiliationController::class, 'postAffiliationStatut']);
        Route::get('get-profile', [App\Http\Controllers\API\Auth\AdminAuthController::class, 'getProfile'])->name('get.profile');
        Route::post('profile-update', [App\Http\Controllers\API\Auth\AdminAuthController::class, 'updateProfile'])->name('updateProfile');
        Route::put('members/status/{user}', [App\Http\Controllers\Api\AdminMemberController::class, 'updateStatus'])->name('member_status.update');
        Route::resource('members', App\Http\Controllers\Api\AdminMemberController::class)->except(['edit', 'create', 'store', 'destroy']);
        Route::resource('tournaments', App\Http\Controllers\Api\AdminTournamentController::class)->except(['create', 'edit', 'destroy']);
        Route::resource('orders', App\Http\Controllers\Api\AdminOrderController::class)->except(['create', 'edit', 'destroy', 'show']);
        Route::resource('affiliations', App\Http\Controllers\Api\AdminAffiliationController::class)->except(['create', 'edit', 'destroy', 'show']);
        Route::resource('users', App\Http\Controllers\Api\AdminUserController::class)->except(['create', 'destroy', 'show']);
        Route::resource('articles', App\Http\Controllers\Api\AdminArticleController::class)->except(['create', 'show']);
        Route::resource('tags', App\Http\Controllers\Api\AdminTagController::class)->except(['create', 'show']);
        Route::resource('menus', App\Http\Controllers\Api\AdminMenuController::class)->except(['create', 'show']);
        Route::put('modules/translation/update/{translation}', [App\Http\Controllers\Api\AdminModuleController::class, 'updateTranslation']);
        Route::get('modules/publish/{module}', [App\Http\Controllers\Api\AdminModuleController::class, 'publish']);
        Route::resource('modules', App\Http\Controllers\Api\AdminModuleController::class)->except(['create', 'show']);
        Route::resource('patags', App\Http\Controllers\Api\AdminPATagController::class)->except(['create', 'show']);
        Route::resource('siteurls', App\Http\Controllers\Api\AdminURLSiteController::class)->except(['create', 'edit', 'show']);
        Route::resource('url-301', App\Http\Controllers\Api\AdminUrl301Controller::class)->except(['create', 'edit', 'show']);
        Route::get('siteurls-404', [App\Http\Controllers\Api\AdminUrl404Controller::class, 'index']);

        /**
         * Data Fetching For select options
         */
        Route::get('fetch/operator', [App\Http\Controllers\Api\AdminFetchData::class, 'fetchOperator'])->name('fetch.operators');
        Route::get('fetch/groups', [App\Http\Controllers\Api\AdminFetchData::class, 'fetchGroupData'])->name('fetch.groups');
        Route::get('fetch/tags', [App\Http\Controllers\Api\AdminFetchData::class, 'fetchTags'])->name('fetch.tags');
        Route::get('fetch/patags', [App\Http\Controllers\Api\AdminFetchData::class, 'fetchPATags'])->name('fetch.patags');
        Route::get('fetch/langs', [App\Http\Controllers\Api\AdminFetchData::class, 'fetchLanguages'])->name('fetch.langs');
        Route::get('fetch/menus', [App\Http\Controllers\Api\AdminFetchData::class, 'fetchMenus'])->name('fetch.menus');
        Route::get('fetch/urls', [App\Http\Controllers\Api\AdminFetchData::class, 'fetchUrls'])->name('fetch.urls');
        Route::get('fetch/site/urls', [App\Http\Controllers\Api\AdminFetchData::class, 'fetchSiteUrls'])->name('fetch.site.urls');
    });
});

