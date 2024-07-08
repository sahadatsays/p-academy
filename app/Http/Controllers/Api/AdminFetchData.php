<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupResource;
use App\Http\Resources\OperatorResource;
use App\Models\Group;
use App\Models\Language;
use App\Models\Operator;
use App\Models\Tag;
use Illuminate\Http\Request;

class AdminFetchData extends Controller
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

    public function fetchLanguages()
    {
        return Language::query()->activated()->get(['id', 'english_name', 'default_locale']);
    }

}
