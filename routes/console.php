<?php

use App\Models\Media;
use App\Models\Urlsite;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('fixed:media', function () {
    $medias = Media::where('linkto_type', 'Kelio\Zetatori\Article')->update(['linkto_type' => 'App\Models\Article']);
    $this->comment('Model Location Update: ' . $medias);
});

Artisan::command('fixed:urls', function () {
    $siteUls = Urlsite::where('linkto_type', 'LIKE', '%'.'Kelio' . '%')->get();
    foreach($siteUls as $url) {
        $explode = explode('\\', $url->linkto_type);
        $model = end($explode);
        $updated_type = 'App\\Models\\' . $model;
        $url->update(['linkto_type' => $updated_type]);
        $this->comment($updated_type);
    }
});

Artisan::command('fixed:all', function () {
    Artisan::call('fixed:media');
    Artisan::call('fixed:urls');

    $this->comment('Fixed All Database columns data');
});
