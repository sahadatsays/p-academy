<?php

namespace App\Http\Controllers\API;

use App\Helpers\QueryHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\PatagResource;
use App\Models\Patag;
use Illuminate\Http\Request;

class AdminPATagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('itemsPerPage', 15);

        $query = Patag::query();
        
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
            $columns = ['id', 'name'];
            $query = QueryHelper::searchAll($query, $search, $columns);
        }
        // Pagination
        $paTags = $query->paginate($perPage);

        return PatagResource::collection($paTags);
    }

    private function sortBy($query, $key, $order)
    {

        return $query->orderBy($key, $order);
    }

    private function filters($query, $request)
    {
        if ($request->id) {
            $query->where('id', $request->id);
        }
        
        if ($request->name) {
            $query->where('name', 'LIKE', '%'. $request->name . '%');
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
        //
    }
}
