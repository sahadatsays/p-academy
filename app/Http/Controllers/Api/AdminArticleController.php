<?php

namespace App\Http\Controllers\Api;

use App\Helpers\LangHelper;
use App\Helpers\QueryHelper;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\UserResource;
use App\Models\Article;
use App\Models\Media;
use App\Models\Patag;
use App\Models\Tag;
use App\Models\Url301;
use App\Models\User;
use Cocur\Slugify\Slugify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
    public function edit(Article $article)
    {
        $user = Auth::user();
        $lang_actives = LangHelper::getLangsActives();

        if (!$article) return $this->sendError('Article Not found');

        $categories_article = $article->tags()->pluck('id');
        $tags_article = $article->patags()->pluck('id');

        $categories = Tag::query()
            ->get(['id', 'name', 'parent_id']);

        $tags = Patag::query()
            ->with([
                'childs' => function ($query) {
                    $query->select('id', 'name', 'parent_id');
                }
            ])
            ->get(['id', 'name', 'parent_id'])
            ->toArray();

        $urlpattern = [];
        $tagsSegmentsUrl = $article->getTagsSegmentsUrl();
        $urlpattern[0]['pattern'] = "{tags}/{slug}.html";
        $urlpattern[1]['pattern'] = "{tags}/{id}-{slug}.html";
        $urlpattern[2]['pattern'] = "{slug}.html";
        $urlpattern[3]['pattern'] = "{id}-{slug}.html";
        $urlpattern[4]['pattern'] = "{tags}/{id}-{slug}.html";
        $urlpattern[5]['pattern'] = "custom url";

        $urlpattern[0]['url'] = $tagsSegmentsUrl . $article->slug . ".html";

        $urlpattern[1]['url'] = $tagsSegmentsUrl . $article->id . '-' . $article->slug . ".html";

        $urlpattern[2]['url'] = $article->slug . ".html";

        $urlpattern[3]['url'] = $article->id . '-' . $article->slug . ".html";

        $urlpattern[5]['url'] = "";

        if ($article->urlsite) {
            $urlpattern[5]['url'] = $article->slug . ".html";
        }

        $urlpattern[4]['url'] = str_replace('{tags}/', $tagsSegmentsUrl, $urlpattern[4]['pattern']);
        $urlpattern[4]['url'] = str_replace('{id}', $article->id, $urlpattern[4]['url']);
        $urlpattern[4]['url'] = str_replace('{slug}', $article->slug, $urlpattern[4]['url']);


        // on gère les custom key
        $listekey = DB::select('select distinct metakey from zt_articles_customdata where metakey not in 
            (select distinct metakey from zt_articles_customdata where article_id = ' . $article->id . ')');

        $author = User::find($article->user_id);

        $data = [
            'author' => new UserResource($author),
            'listekey' => $listekey,
            'urlpattern' => $urlpattern,
            'article_categories' => $categories_article,
            'tags' => $tags,
            'article' => $article,
            'tags_article' => $tags_article,
            'categoriesTree' => $this->buildTree($categories),
            'lang_actives' => $lang_actives,
            'root_url' => url('/')
        ];
        return $this->sendResponse($data, '');
    }

    public function buildTree($items, $parentId = null)
    {
        $branch = [];

        foreach ($items as $item) {
            if ($item->parent_id == $parentId) {
                $children = $this->buildTree($items, $item->id);
                if ($children) {
                    $item->children = $children;
                }
                $branch[] = $item;
            }
        }

        return $branch;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $slug = new Slugify();
        $rules = array(
            'title' => 'required|max:150',
            'content' => 'required',
        );

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails()) {
            return $this->sendError('Form Error', $validation->messages());
        }
        $article = Article::query()->with(['urlsite'])->first($id);
        $reason = array();
        if ($article->lang != $request->input('lang')) $reason[] = 'lang change';
        if ($article->slug != $request->input('slug')) $reason[] = 'slug change';
        $reason = implode(',', $reason);

        $article->title = $request->input('title');

        if ($request->input('slug') == "") {
            $article->slug = str($article->title)->slug();
        } else {
            $article->slug = $request->input('slug');
        }

        $article->page_title = $request->input('page_title');
        $article->content = $request->input('content');
        $article->state = $request->input('state');
        $article->user_id = $request->input('user_id');
        $article->publish_up = $request->input('publish_up');
        $article->publish_down = $request->input('publish_down');
        $article->created_at = $request->input('created');
        $article->updated_at = $request->input('updated');
        $article->lang = $request->input('lang');
        $article->order = $request->input('order');
        $article->rules = $request->input('rules') ?? '';
        $article->view_name = $request->input('view_name');
        $article->group_lang_id = $request->input('group_lang_id');
        $article->metakeywords = $request->input('metakeywords');
        $article->metadescription = $request->input('metadescription');

        $typeurl = $request->input('typeurl');

        if (!isset($typeurl)) $typeurl = 0;

        $article->url_pattern = $request->input('pattern' . $typeurl);
        $article->save();

        if (config('ZT.multilang')) {
            $url = substr($article->lang, 0, 2) . '/' . $request->input('url' . $typeurl);
        } else {
            $url = $request->input('url' . $typeurl);
        }

        if ($typeurl == 5) {
            $article->urlsite->url = $url;
            $article->push();
        } else {
            $article->refreshUrl($reason);
        }

        $oldurl = $request->input('oldurl');

        if (isset($oldurl) and $oldurl != "") {
            $alias = $article->urlsite->oldurl;
            if ($alias) {
                if ($alias->alias != $oldurl) {
                    $alias->alias = $oldurl;
                    $alias->save();
                }
            } else {
                $url301 = new Url301();
                $url301->alias = $oldurl;
                $url301->urlsite_id = $article->urlsite->id;
                $url301->urlsite->botvisit_at = "0000-00-00 00:00:00";
                $url301->reason = 'old URL';
                $url301->save();
            }
        }

        return $this->sendResponse([], 'Votre article a été modifié');
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

    public function setTagToArticle(Request $request)
    {
        if (!$request->tag_id  || !$request->article_id) {
            return $this->sendError('Article or Tag id not found!');
        }
        $article = Article::findOrFail($request->article_id);

        $exists = $article->tags()->where('id', $request->tag_id)->first();

        if (!$exists) {
            $article->tags()->attach($request->tag_id);
        } else {
            $article->tags()->detach($request->tag_id);
        }

        return $this->sendResponse([], 'Attach and Detach Success !');
    }

    public function addMedia(Request $request, Article $article)
    {
        $validator = Validator::make($request->all(), [
            'media' => 'required|mimes:jpg,jpeg,bmp,png,gif'
        ]);

        if ($validator->fails()) {
            return $this->sendError('', $validator->messages());
        }

        if ($request->hasFile('media')) {
            $file = $request->file('media');
            
            $filename = time().'-'. $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $fileType = $file->getMimeType();
            $path = $file->storeAs('', $filename, 's3');
            
            $media = Media::create([
                'size' => $fileSize,
                'media_file_name' => $filename,
                'linkto_type' => Article::class,
                'linkto_id' => $article->id,
                'media_content_type' => $fileType,
                'media_file_size' => $fileSize,
                'state' => 1,
                'key' => 'index',
                'user_id' => auth()->id()
            ]);
            
            return $this->sendResponse([
                'id' => $media->id,
                'size' => $media->size,
                // 'url' => Storage::disk('s3')->url($path),
            ], 'Media has been uploaded.');
        }
    }
}
