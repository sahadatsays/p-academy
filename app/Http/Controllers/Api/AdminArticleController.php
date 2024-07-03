<?php

namespace App\Http\Controllers\Api;

use App\Helpers\QueryHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

class AdminArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('itemsPerPage', 15);

        $query = Article::query();

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
            $columns = ['id', 'title'];
            $query = QueryHelper::searchAll($query, $search, $columns);
        }

        // Pagination
        $articles = $query->with('user')->paginate($perPage);

        return ArticleResource::collection($articles);
    }

    private function sortBy($query, $key, $order)
    {
        if ($key == 'updated_at') {
            return $query->orderBy('updated_at', $order);
        }

        if ($key == 'createdAt') {
            return $query->orderBy('created_at', $order);
        }

        if ($key == 'createdBy') {
            return $query->withAggregate('user', 'username')->orderBy('user_username', $order);
        }

        if ($key == 'createdAt') {
            return $query->orderBy('created_at', $order);
        }

        if ($key == 'tags') {
            return $query->whereHas('tags', function ($query) use ($order) {
                $query->orderBy('name', $order);
            });
        }

        return $query->orderBy($key, $order);
    }

    private function filters($query, $request)
    {
        if ($request->get('id')) {
            $query->where('id', $request->id);
        }

        if ($request->get('activated') == 'Active') {
            $query->where('state', 1);
        }

        if ($request->get('activated') == 'Inactive') {
            $query->where('state', 0);
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
        $user = auth()->user();

        dd($user);
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
