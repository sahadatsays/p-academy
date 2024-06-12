<?php

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin'], function () {
    Route::post('login', [App\Http\Controllers\Api\Auth\AdminLoginController::class, 'login'])->name('login');

    Route::group(['middleware' => ['auth:api', 'checkAdmin']], function () {
        Route::get('user', function (Request $request) {
            return new UserResource($request->user());
        });
    });
});

