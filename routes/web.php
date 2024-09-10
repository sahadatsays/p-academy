<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ResetController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::view('/', 'welcome');
Route::get('test', function () {
    try {
        $s3data = Storage::disk('s3')->files();
        foreach($s3data as $file) {
            dump(Storage::disk('s3')->url($file));
        }
    dump($s3data);
    } catch (\Throwable $th) {
        dump($th->getMessage());
    }
});
Route::get('reset', [ResetController::class, 'dbReset'])->name('reset');
Route::get('admin/{any?}/', [AdminController::class, 'app'])->where('any', '.*')->name('app');
Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);
Route::get('phpinfo', function () {
    phpinfo();
});
