<?php

namespace App\Http\Controllers\API;

use App\Helpers\QueryHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Models\Menu;
use Illuminate\Http\Request;

class AdminMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request  $request)
    {
        $perPage = $request->input('itemsPerPage', 25);

        $query = Menu::query();

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
            $columns = [];
            $query = QueryHelper::searchAll($query, $search, $columns);
        }

        // Pagination
        $menus = $query->with('urlSite')->paginate($perPage);

        return MenuResource::collection($menus);
    }

    private function sortBy($query, $key, $order)
    {

        if ($key == 'user.username') {
            return $query->orderBy('username', $order);
        }

        if ($key == 'user.name') {
            return $query->orderBy('first_name', $order);
        }

        if ($key == 'user.email') {
            return $query->orderBy('email', $order);
        }

        if ($key == 'user.createdAt') {
            return $query->orderBy('created_at', $order);
        }

        if ($key == 'user.activatedAt') {
            return $query->orderBy('activated_at', $order);
        }
        return $query->orderBy($key, $order);
    }

    private function filters($query, $request)
    {
        // $request->only(['id', 'username', 'name', 'email', 'activated'])
        if ($request->get('id')) {
            $query->where('zt_users.id', $request->id);
        }

        if ($request->get('username')) {
            $query->where('username', 'LIKE', '%' . $request->username . '%');
        }

        if ($request->get('name')) {
            $query->where('first_name', 'LIKE', '%' . $request->name . '%')->orWhere('last_name', 'LIKE', '%' . $request->name . '%');
        }

        if ($request->get('email')) {
            $query->where('email', 'LIKE', '%' . $request->email . '%');
        }

        if ($request->get('activated') == 'Active') {
            $query->where('activated', 1);
        }

        if ($request->get('activated') == 'Inactive') {
            $query->where('activated', 0);
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
