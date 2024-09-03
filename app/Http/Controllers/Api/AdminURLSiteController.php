<?php

namespace App\Http\Controllers\Api;

use App\Helpers\QueryHelper;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\URLSiteResource;
use App\Models\Urlsite;
use Illuminate\Http\Request;

class AdminURLSiteController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('itemsPerPage', 15);

        $query = Urlsite::query();

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

        return URLSiteResource::collection($urlSites);
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $url = Urlsite::findOrFail($id);
        $url->delete();
        return $this->sendResponse(null, 'Url has been deleted!');
    }
}
