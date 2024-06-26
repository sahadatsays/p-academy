<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');
Route::get('admin/{any?}/', [AdminController::class, 'app'])->where('any', '.*')->name('app');
