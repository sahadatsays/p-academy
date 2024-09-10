<?php

namespace App\Http\Controllers\Api;

use App\Helpers\QueryHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Article;
use App\Models\Language;
use App\Models\Tag;
use App\Models\TagTranslations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminTagController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('itemsPerPage', 15);

        $query = Tag::query()
            ->leftJoin('zt_v_nbarticles_by_tag', 'zt_tags.id', '=', 'zt_v_nbarticles_by_tag.tag_id')
            ->select([
                'zt_tags.*',
                DB::Raw('IFNULL(zt_v_nbarticles_by_tag.nbarticles,0) as nbarticles')
            ]);

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
        $tags = $query->with('translations')->paginate($perPage);

        return TagResource::collection($tags);
    }

    private function sortBy($query, $key, $order)
    {
        if ($key == 'updated_at') {
            return $query->orderBy('updated_at', $order);
        }

        if ($key == 'createdAt') {
            return $query->orderBy('created_at', $order);
        }

        if ($key == 'createdAt') {
            return $query->orderBy('created_at', $order);
        }

        if ($key == 'parent.name') {
            return $query->orderBy('parent_id', $order);
        }

        return $query->orderBy($key, $order);
    }

    private function filters($query, $request)
    {
        if ($request->get('id')) {
            $query->where('id', $request->id);
        }

        if ($request->get('name')) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
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
            'name' => 'required|string',
            'title' => 'required|string',
            'lang' => 'required',
        ]);

        $data['parent_id'] = $request->input('parent_id', 0);
        $data['view_name'] = 'blog.html';
        $data['in_url'] = $request->input('in_url', 0);
        $data['index_page'] = $request->input('index_page', 0);
        $data['sort_by'] = 'publish_up desc';
        $data['rules'] = 'admin';
        $data['per_page'] = 15;

        $tag = Tag::create($data);

        // Tag Translations
        $tagTranslationData = [
            'tag_id'        => $tag->id,
            'lang'          => $request->input('lang'),
            'title'         => $request->input('title'),
            'slug'          => str()->slug($request->input('title')),
            'content1'      => $request->input('content1'),
            'content2'      => $request->input('content2'),
        ];

        $traslation = TagTranslations::create($tagTranslationData);

        $tag->refreshUrl('add tag');

        return $this->sendResponse($tag, 'Tag has been created.');
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
        $tag = Tag::findOrFail($id);

        if ($tag == null) {
            return $this->sendError('Tag not found.');
        }
        $translations = $tag->translations()->get();

        return $this->sendResponse(['tag' => $tag, 'translations' => $translations], 'fetch tag');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tag = Tag::findOrFail($id);

        if ($tag == null) {
            return $this->sendError('Tag not found.');
        }

        $data = $request->validate([
            'name' => 'required|string',
            'view_name' => 'nullable|string',
            'sort_by' => 'nullable',
            'rules' => 'nullable',
            'per_page' => 'nullable',
            'parent_id' => 'nullable',
            'pa_menu_hide' => 'nullable',
            'order' => 'nullable',
            'index_page' => 'nullable',
            'in_url' => 'nullable',
        ]);

        $data['parent_id'] = $request->input('parent_id', 0) ?? 0;
        $data['view_name'] = 'blog.html';
        $data['in_url'] = $request->input('in_url') == "on" ? 1 : 0;
        $data['index_page'] = $request->input('index_page') == "on" ? 1 : 0;
        $data['sort_by'] = $request->input('sort_by')  ?? 'publish_up desc';
        $data['rules'] = $request->input('rules') ?? 'admin';
        $data['per_page'] = $request->input('per_page') ?? 15;

        $tag->update($data);

        // Tag Translations
        foreach ($request->input('translations') as $translation) {

            $tagTranslationData = [
                // 'tag_id'        => $translation['id'],
                'lang'          => $translation['lang'],
                'title'         => $translation['title'],
                'slug'          => str()->slug($translation['title']),
                'content1'      => $translation['content1'],
                'content2'      => $translation['content2'],
                'metakeywords'      => $translation['metakeywords'],
                'metadescription'      => $translation['metadescription'],
                'page_title'      => $translation['page_title'],
                'pa_menu_titre'      => $translation['pa_menu_titre'],
            ];

            $translation = $tag->translations()->where('lang', $translation['lang'])->first();

            if ($translation != null) {
                $translation->update($tagTranslationData);
            } else {
                TagTranslations::create($tagTranslationData);
            }
        }

        $tag->refreshUrl('add tag');

        return $this->sendResponse($tag, 'Tag has been updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->delete_childs($id);
        return $this->sendResponse([], 'Deleted has been done.');
    }

    private function delete_childs($id)
    {
        $childs = Tag::where('parent_id', '=', $id)->get();
        foreach ($childs as $c) {
            $this->delete_childs($c->id);
        }

        $todelete = Tag::find($id);
        // on del les urls mode blog
        foreach ($todelete->translations() as $key) {
            $key->urlsite()->delete();
        }
        // on del les trad
        $todelete->translations()->delete();
        // on stocke les ids des articles tagués pour pouvoir recalculer leur url après les avoir détagués
        $recalc = array();
        foreach ($todelete->articles as $a) {
            $recalc[] = $a->id;
        }

        // on detag
        $todelete->articles()->detach($id);


        // on del le tag
        $todelete->delete();
        // on recaclule les URL des articles liés à ce tag
        foreach ($recalc as $r) {
            Article::find($r)->refreshUrl('Tag delete');
        }
    }
}
