<?php

namespace App\Http\Controllers\Api;

use App\Helpers\QueryHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Url301Resource;
use App\Models\Url301;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminUrl301Controller extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('itemsPerPage', 15);

        $query = Url301::query();

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
            $columns = ['alias'];
            $query = QueryHelper::searchAll($query, $search, $columns);
        }
        // Pagination
        $urlSites = $query->latest()->paginate($perPage);

        return Url301Resource::collection($urlSites);
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
        if ($request->alias) {
            $query->where('alias', 'LIKE', '%' . $request->alias . '%');
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
        $validator = Validator::make($request->all(), [
            'alias' => 'required|string',
            'urlsite_id' => 'required',
            'reason' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('Form validation error.', $validator->errors(), 422);
        }

        $url = Url301::create([
            'urlsite_id' => $request->urlsite_id,
            'reason' => $request->reason,
            'alias' => $request->alias
        ]);

        return $this->sendResponse($url, 'Votre alias a été ajouté');
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
        $url = Url301::findOrFail($id);
        $url->delete();
        return $this->sendResponse('', 'Votre alias a été supprimé');
    }
}
