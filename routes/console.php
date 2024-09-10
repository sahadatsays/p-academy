<?php

use App\Models\Media;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('fixed:media', function () {
    $medias = Media::where('linkto_type', 'Kelio\Zetatori\Article')->update(['linkto_type' => 'App\Models\Article']);
    $this->comment('Model Location Update: ' . $medias);
});
