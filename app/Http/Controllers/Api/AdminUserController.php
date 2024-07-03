<?php

namespace App\Http\Controllers\Api;

use App\Helpers\QueryHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('itemsPerPage', 15);

        $query = User::query();

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
            $columns = ['zt_users.id', 'zt_users.username', 'zt_users.email', 'zt_users.first_name', 'zt_users.last_name'];
            $query = QueryHelper::searchAll($query, $search, $columns);
        }

        // Pagination
        $users = $query->paginate($perPage);

        return UserResource::collection($users);
    }

    private function sortBy($query, $key, $order)
    {
        if ($key == 'firstName') {
            return $query->orderBy('first_name', $order);
        }

        if ($key == 'createdAt') {
            return $query->orderBy('created_at', $order);
        }

        if ($key == 'lastLogin') {
            return $query->orderBy('last_login', $order);
        }
        if ($key == 'lastLogin') {
            return $query->orderBy('last_login', $order);
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
