<?php

namespace App\Http\Controllers;

use App\Models\Urlsite;
use Illuminate\Http\Request;

class ResetController extends Controller
{
    public function dbReset() {
        Urlsite::where('linkto_type', 'Kelio\Zetatori\Article')
            ->update(['linkto_type' => 'App\Models\Article']);
    }
}
