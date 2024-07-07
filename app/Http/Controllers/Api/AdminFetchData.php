<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\GroupResource;
use App\Http\Resources\OperatorResource;
use App\Models\Group;
use App\Models\Operator;
use Illuminate\Http\Request;

class AdminFetchData extends Controller
{
    public function fetchOperator() {
        return OperatorResource::collection(Operator::query()->published()->orderBy('nom')->get());
    }

    public function fetchGroupData() {
        $groups = Group::query()->where('id', '!=', 1)->get();
        return GroupResource::collection($groups);
    }
}
