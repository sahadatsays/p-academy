<?php

use App\Http\Controllers\Api\AdminMemberController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin'], function () {
    Route::post('login', [App\Http\Controllers\Api\Auth\AdminLoginController::class, 'login'])->name('login');

    Route::group(['middleware' => ['auth:api', 'checkAdmin']], function () {
        Route::get('user', function (Request $request) {
            return new UserResource($request->user());
        });

        Route::resource('members', App\Http\Controllers\Api\AdminMemberController::class)->except(['edit', 'create']);
        Route::resource('tournament', App\Http\Controllers\Api\AdminTournamentController::class);
    });
});

