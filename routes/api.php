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

        Route::resource('members', App\Http\Controllers\Api\AdminMemberController::class)->except(['edit', 'create', 'store', 'destroy']);
        Route::resource('tournaments', App\Http\Controllers\Api\AdminTournamentController::class)->except(['create', 'edit', 'destroy', 'show']);
        Route::resource('orders', App\Http\Controllers\Api\AdminOrderController::class)->except(['create', 'edit', 'destroy', 'show']);
        Route::resource('affiliations', App\Http\Controllers\Api\AdminAffiliationController::class)->except(['create', 'edit', 'destroy', 'show']);
        Route::resource('users', App\Http\Controllers\Api\AdminUserController::class)->except(['create', 'edit', 'destroy', 'show']);
        Route::resource('articles', App\Http\Controllers\Api\AdminArticleController::class)->except(['create', 'edit', 'destroy', 'show']);
        Route::resource('tags', App\Http\Controllers\Api\AdminTagController::class)->except(['create', 'edit', 'destroy', 'show']);
    });
});

