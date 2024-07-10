<?php

namespace App\Http\Controllers\Api;

use App\Helpers\QueryHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Request;

class AdminArticleController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('itemsPerPage', 15);

        $query = Article::query()->where('state', '!=', 2);

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

        $data = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'lang'  => 'required|string'
        ]);

        $article = Article::create([
            'title' => $data['title'],
            'slug' => str($data['title'])->slug(),
            'content' => $data['content'],
            'lang' => $data['lang'],
            'state' => 0,
            'view_name' => 'article.lire',
            'publish_up' => now()->toDateTimeString(),
            'publish_down' => '2099-01-01 00:00:00',
            'user_id' => $user->id,
            'url_pattern' => '{tags}/{slug}.html',
            'rules' => ''
        ]);

        if ($article) {
            $article->update(['group_lang_id' => $article->id]);
            $article->urlsite()->create(['url' => $article->slug . '.html']);
        }

        return $this->sendResponse($article, 'The article has been added!');
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
    public function destroy(Article $article)
    {
        if ($article->state == 0 || $article->state == 1) {
            $article->update(['state' => 2]);
            return $this->sendResponse($article, 'The article has been placed in the trash');
        }

        $article->delete();
        return $this->sendResponse($article, 'The article has been deleted');
    }
}
