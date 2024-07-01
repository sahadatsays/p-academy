<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');
Route::get('admin/{any?}/', [AdminController::class, 'app'])->where('any', '.*')->name('app');
Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
Route::get('phpinfo', function () {
    phpinfo();
});
