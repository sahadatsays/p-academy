<?php

namespace App\Http\Controllers\API;

use App\Helpers\QueryHelper;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\PatagResource;
use App\Models\Patag;
use Illuminate\Http\Request;

class AdminPATagController extends ApiController
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
        } else {
            $query = $query->orderBy('id', 'desc');
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
        $paTags = $query->with('parent')->paginate($perPage);

        return PatagResource::collection($paTags);
    }

    private function sortBy($query, $key, $order)
    {   
        if ($key == 'parent_name') {
            return $query->orderBy('parent_id', $order);
        }
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
        $data = $request->validate([
            'parent_id' => 'required',
            'name' => 'required|string',
            // 'title' => 'required|string',
            'type' => 'required|string'
        ]);

        $data['old_id'] = 0;
        $patag = Patag::create($data);
        return $this->sendResponse($patag, 'PaTag has been created.');
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
        $paTag = Patag::find($id);
        if ($paTag != null) {
            return $this->sendResponse($paTag, 'Fetch data for edit');
        }

        return $this->sendError('PATag not found!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $patag = Patag::find($id);
        if ($patag == null) {
            return $this->sendError('PA Tag not found');
        }

        $data = $request->validate([
            'parent_id' => 'required',
            'name' => 'required|string',
            // 'title' => 'required|string',
            'type' => 'required|string'
        ]);

        $data['old_id'] = 0;
        $patag = $patag->update($data);
        return $this->sendResponse($patag, 'PaTag has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $patag = Patag::find($id);
        if ($patag == null) {
            return $this->sendError('PA Tag not found!');
        }
        $patag->childs()->delete();
        $patag->delete();

        return $this->sendResponse(null, 'Pa Tag has been deleted with the childs!');
    }
}
