<?php

namespace App\Http\Controllers\Api;

use App\Helpers\QueryHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\AffiliationResource;
use App\Models\Affiliation;
use Illuminate\Http\Request;

class AdminAffiliationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('itemsPerPage', 15);

        $query = Affiliation::query()
            ->join('zt_users', 'pa_affiliations.user_id', '=', 'zt_users.id');

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
            $columns = ['zt_users.id', 'zt_users.username', 'room', 'pseudo_room', 'statut'];
            $query = QueryHelper::searchAll($query, $search, $columns);
        }

        // Pagination
        $affiliation = $query->paginate($perPage);

        return AffiliationResource::collection($affiliation);
    }

    private function sortBy($query, $key, $order)
    {

        if ($key == 'userid') {
            return $query->orderBy('zt_users.id', $order);
        }

        if ($key == 'username') {
            return $query->orderBy('zt_users.username', $order);
        }

        if ($key == 'user.createdAt') {
            return $query->orderBy('zt_users.created_at', $order);
        }

        if ($key == 'pseudo_room') {
            return $query->orderBy('pseudo_room', $order);
        }

        if ($key == 'status') {
            return $query->orderBy('statut', $order);
        }
        return $query->orderBy($key, $order);
    }

    private function filters($query, $request)
    {
        if ($request->get('userid')) {
            $query->where('zt_users.id', $request->userid);
        }

        if ($request->get('room')) {
            $query->where('room', 'LIKE', '%' . $request->room . '%');
        }

        if ($request->get('pseudo_room')) {
            $query->where('pseudo_room', 'LIKE', '%' . $request->pseudo_room . '%');
        }

        if ($request->get('status')) {
            $query->where('statut', 'LIKE', '%' . $request->status . '%');
        }

        if ($request->get('username')) {
            $query->where('zt_users.username', 'LIKE', '%' . $request->username . '%');
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
