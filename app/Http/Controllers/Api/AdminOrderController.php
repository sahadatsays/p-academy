<?php

namespace App\Http\Controllers\Api;

use App\Helpers\QueryHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('itemsPerPage', 15);

        $query = Order::query()
            ->join('zt_users', 'pa_commandes.user_id', '=', 'zt_users.id')
            ->select('zt_users.id as user_id', 'pa_commandes.*');

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
            $columns = ['lib'];
            $query = QueryHelper::searchAll($query, $search, $columns);
        }

        // Pagination
        $orders = $query->with('member')->paginate($perPage);

        return OrderResource::collection($orders);
    }

    private function sortBy($query, $key, $order)
    {
        if ($key == 'id') {
            return $query->orderBy('id', $order);
        }

        if ($key == 'lib') {
            return $query->orderBy('lib', $order);
        }

        if ($key == 'prix') {
            return $query->orderBy('prix', $order);
        }

        if ($key == 'user_id') {
            return $query->orderBy('zt_users.id', $order);
        }

        if ($key == 'username') {
            return $query->orderBy('zt_users.username', $order);
        }
        if ($key == 'email') {
            return $query->orderBy('zt_users.email', $order);
        }

        if ($key == 'paiement') {
            return $query->orderBy('paiement', $order);
        }

        if ($key == 'date') {
            return $query->orderBy('date', $order);
        }
        return $query->orderBy($key, $order);
    }

    private function filters($query, $request)
    {
        if ($request->get('id')) {
            $query->where('id', $request->id);
        }

        if ($request->get('lib')) {
            $query->where('lib', 'LIKE', '%' . $request->lib . '%');
        }

        if ($request->get('user_id')) {
            $query->where('zt_users.id', $request->user_id);
        }
        if ($request->get('username')) {
            $query->where('zt_users.username', 'LIKE', '%'. $request->username . '%');
        }
        if ($request->get('email')) {
            $query->where('zt_users.email', 'LIKE', '%'. $request->email . '%');
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
