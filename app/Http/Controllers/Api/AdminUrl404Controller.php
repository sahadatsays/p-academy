<?php

namespace App\Http\Controllers\Api;

use App\Helpers\QueryHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\URL404RResource;
use App\Models\Url404;
use Illuminate\Http\Request;

class AdminUrl404Controller extends Controller
{
    public function index (Request $request) 
    {
        $perPage = $request->input('itemsPerPage', 15);

        $query = Url404::query();

        // sorting query
        if ($request->get('sortBy')) {
            $sortBy = json_decode($request->get('sortBy'));
            $query = $this->sortBy($query, $sortBy->key, $sortBy->order);
        }

        // filters by columns
        $query = $this->filters($query, $request);

        // search
        if ($request->has('search')) {
            $search = $request->get('search');
            $columns = ['url'];
            $query = QueryHelper::searchAll($query, $search, $columns);
        }
        // Pagination
        $urlSites = $query->paginate($perPage);

        return URL404RResource::collection($urlSites);
    }

    private function sortBy($query, $key, $order)
    {
        if ($key == 'createdAt') {
            return $query->orderBy('created_at', $order);
        }

        if ($key == 'updatedAt') {
            return $query->orderBy('updated_at', $order);
        }
        return $query->orderBy($key, $order);
    }

    private function filters($query, $request)
    {
        if ($request->url) {
            $query->where('url', 'LIKE', '%' . $request->url . '%');
        }
        return $query;
    }
}
