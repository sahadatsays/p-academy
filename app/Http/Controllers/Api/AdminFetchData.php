<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\GroupResource;
use App\Http\Resources\OperatorResource;
use App\Models\Article;
use App\Models\Group;
use App\Models\Language;
use App\Models\Menu;
use App\Models\Operator;
use App\Models\Patag;
use App\Models\Tag;
use App\Models\TagTranslations;
use App\Models\Urlsite;
use Illuminate\Http\Request;

class AdminFetchData extends ApiController
{
    public function fetchOperator() 
    {
        return OperatorResource::collection(Operator::query()->published()->orderBy('nom')->get());
    }

    public function fetchGroupData() 
    {
        $groups = Group::query()->where('id', '!=', 1)->get();
        return GroupResource::collection($groups);
    }

    public function fetchTags() 
    {
        $tags = Tag::query()->get(['id', 'name']);
        return $tags;
    }

    public function fetchPATags() 
    {
        $tags = Patag::query()->get(['id', 'name']);
        return $tags;
    }

    public function fetchLanguages()
    {
        return Language::query()->activated()->get(['id', 'english_name', 'default_locale']);
    }

    public function fetchMenus()
    {
        return Menu::query()->published()->get(['id', 'name']);
    }

    public function fetchUrls()
    {
        $options = array();
       
        foreach ( TagTranslations::query()->get() as $t )
        {
            $url = $t->urlsite;
            if ( $url ) $options[] = array( 'id' => "$url->id", 'text' => 'T - ' . $t->title );
        }

        foreach ( Article::query()->get() as $a )
        {
            $url = $a->urlsite;
            if ( $url ) $options[] = array( 'id' => "$url->id", 'text' => 'A - ' . $a->title );
        }

        return $this->sendResponse($options, 'fetch');
    }

    public function fetchSiteUrls(Request $request) 
    {
        if($request->search) {
            $searchResult = Urlsite::select('id', 'url')->where('url', 'LIKE', '%' . $request->search . '%')->limit(50)->get();
           if (count($searchResult) > 0) {
            return $searchResult;
           }
        }
        return Urlsite::select('id', 'url')->inRandomOrder()->limit(50)->get();
    }

}
