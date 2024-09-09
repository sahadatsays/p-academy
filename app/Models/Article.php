<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Video;
use App\Models\Coach;
use App\Models\Forum\Topic;
use Cocur\Slugify\Slugify;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Article extends Model implements HasMedia
{
    use InteractsWithMedia, Sluggable;

    protected $table = 'zt_articles';

    protected $guarded = ['id'];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    /**
     * Get the user that owns the Article
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function urlsite()
    {
        return $this->morphOne(Urlsite::class, 'linkto');
    }

    public function comments()
    {
        return $this->HasMany(Comment::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'zt_tags_articles', 'article_id', 'tag_id');
    }


    public function patags()
    {
        return $this->belongsToMany('App\Models\Patag', 'pa_tags_articles', 'article_id', 'patag_id');
    }

    public function palikes()
    {
        return $this->belongsToMany('\Kelio\Zetatori\User', 'pa_articles_likes', 'article_id', 'user_id');
    }


    public function data()
    {
        return $this->HasMany('\Kelio\Zetatori\ArticleData');
    }


    public function customdata($metakey)
    {
        return $this->data()->where('metakey', '=', $metakey);
    }


    public function relatifs()
    {
        $ids =  $this->data()->whereRaw('left(metakey,10)="relatif_id"')->orderBy('metakey')->pluck('metavalue');

        $articles = Article::whereIn('id', $ids)
            ->with([
                'urlsite',
                'medias' => function ($query) {
                    $query->where('key', '=', 'index')->where('state', '=', 1);
                }
            ])
            ->published()->get();

        return $articles;
    }


    public function medias()
    {
        return $this->morphMany('\Kelio\Zetatori\Media', 'linkto')->where('state', '=', 1);
    }

    public function translatedArticles()
    {
        return Article::where('id', '<>', $this->id)->where('group_lang_id', '=', $this->group_lang_id);
    }

    public function scopeLanguage($query, $lang)
    {
        return $query->where('lang', '=', $lang);
    }

    public function scopePublished($query)
    {
        return $query->where('state', '=', 1);
    }

    public function scopeOrder($query)
    {
        return $query->orderBy('order', 'asc');
    }


    public function next()
    {
        $tag = $this->tags()->orderBy('order')->first();
        if ($tag) {
            $article_next = $tag->articles()->published()
                ->where('lang', '=', $this->lang)
                ->where('order', '>=', $this->order)
                ->where('id', '!=', $this->id)
                ->orderBy('order')->first();

            if ($article_next) {
                return $article_next->urlsite ? $article_next->urlsite->url : "";
            }
        }
    }

    public function previous()
    {
        $tag = $this->tags()->orderBy('order')->first();
        if ($tag) {
            $article_prev = $tag->articles()->published()
                ->where('lang', '=', $this->lang)
                ->where('order', '<=', $this->order)
                ->where('id', '!=', $this->id)
                ->orderBy('order', 'desc')->first();

            if ($article_prev) {
                return $article_prev->urlsite ? $article_prev->urlsite->url : "";
            }
        }
    }

    // $id est l'id d'un tag
    //$tree est un tableau
    private function buildTreeTag($tree, $id, $lang)
    {
        $tag = Tag::find($id);
        if ($tag->in_url == 1) {
            $tagtrans   = $tag->translations()->language($lang)->first();
            $tagtransFR = $tag->translations()->language('fr_FR')->first();
            if (isset($tagtrans->title))
                array_unshift($tree, $tagtrans->slug);
            elseif (isset($tagtransFR->title))
                array_unshift($tree, $tagtransFR->slug);
            else
                array_unshift($tree, str()->slug($tag->name, '-'));
        }
        if ($tag->parent_id != 0)
            $tree = $this->buildTreetag($tree, $tag->parent_id, $lang);
        else {
            // si on est sur le tag racine on ajoute l'ordre de ce tag en debut de tab pour éviter de refaire une requete et on l'enlevera plus tard
            array_unshift($tree, $tag->order);
        }
        return $tree;
    }


    public function getTagsSegmentsUrl()
    {
        // on select les tags articles qui sont finaux ou bien dont les enfants en sont pas selectionnés pour cet article
        $sql   = "select zt_tags.id from zt_tags, zt_tags_articles ta  left join zt_tags as childs on ta.tag_id = childs.parent_id
     where ta.tag_id = zt_tags.id and ta.article_id = " . $this->id . " and childs.id not in (select tag_id from zt_tags_articles where article_id =" . $this->id . " )
     union
     select zt_tags.id from zt_tags_articles ta,zt_tags
     where  ta.tag_id = zt_tags.id and ta.article_id = " . $this->id . " and ta.tag_id not in (select id from zt_tags where parent_id =ta.tag_id )
      ";

        $tags  = DB::select($sql);
        $trees = array();
        $cpt   = 0;
        // Pour chaque tag on va remonter son arborescence et regarder si on doit afficher le parent dans l'url
        // on va aussi regarder l'ordre du tag racine de la branche
        foreach ($tags as $t) {
            $tmptree = array();
            $tmptree = $this->buildTreeTag($tmptree, $t->id, $this->lang);
            if (!empty($tmptree)) {
                // on vire la premiere case du tableau qui est l'ordre
                $trees[$cpt]['order'] = array_shift($tmptree);
                $trees[$cpt]['tree']  = $tmptree;
                $cpt++;
            }
        }
        // on tri le tableau par ordre
        uasort($trees, function ($a, $b) {
            if ($a['order'] == $b['order'])
                return 0;
            return ($a['order'] > $b['order']) ? -1 : 1;
        });
        // on construit la chaine de segment d'url
        $slug = new Slugify();
        $tagsSegmentsUrl = "";
        foreach ($trees as $t) {
            foreach ($t['tree'] as $tagslug) {
                $tagsSegmentsUrl .= $tagslug . '/';
            }
        }
        return $tagsSegmentsUrl;
    }


    public function refreshUrl($reason)
    {

        if ($this->url_pattern != 'custom url') {
            $url    = str_replace('{tags}/', $this->getTagsSegmentsUrl(), $this->url_pattern);
            $url    = str_replace('{id}', $this->id, $url);
            $url    = str_replace('{slug}', $this->slug, $url);

            if (config('ZT.multilang')) 
            {
                $url    = substr($this->lang, 0, 2) . '/' . $url;
            }
            if ($this->urlsite) {
                $oldurl = $this->urlsite->url;
                // si url change
                if ($oldurl != $url) {
                    $url301                       = new Url301;
                    $url301->alias                = $oldurl;
                    $url301->urlsite_id           = $this->urlsite->id;
                    $url301->urlsite->botvisit_at = "0000-00-00 00:00:00";
                    $url301->reason = $reason;
                    $url301->save();

                    $this->urlsite->url         = $url;
                    $this->urlsite->botvisit_at = '0000-00-00 00:00:00';
                    $this->urlsite->save();
                }
            } else {
                $urlsite = new Urlsite(array('url' => $url));
                $urlsite = $this->urlsite()->save($urlsite);
            }
        }
    }




    //Pa ---------------------
    public function video()
    {
        $data = $this->customdata('video_id')->first();
        if (!$data or $data->metavalue == null or $data->metavalue == 0 or !is_numeric($data->metavalue)) return false;
        return Video::find($data->metavalue);
    }

    public function tournoi()
    {
        $data = $this->customdata('tournoi_id')->first();
        if (!$data or $data->metavalue == null or $data->metavalue == 0 or !is_numeric($data->metavalue)) return false;
        return App\Models\Tournoi::find($data->metavalue);
    }

    public function acces()
    {
        return $this->patags()->where('parent_id', 300)->first();
    }

    public function coach()
    {
        $data = $this->customdata('coach_id')->first();
        if (!$data or $data->metavalue == null or $data->metavalue == 0 or !is_numeric($data->metavalue)) return false;
        return Coach::find($data->metavalue);
    }

    public function cast()
    {
        return $this->hasOne('\App\Models\Cast', 'id_article');
    }


    public function avis()
    {
        return $this->HasMany('\App\Models\Avis', 'id_contenu');
    }


    public function getTopicId()
    {

        $id = Cache::remember('topic_' . $this->id, 10, function () {
            $data = $this->customdata('topic_id')->first();
            if (!$data or $data->metavalue == null or $data->metavalue == 0) return false;
            return $data->metavalue;
        });
        return $id;
    }


    public function topic()
    {
        $data = $this->customdata('topic_id')->first();
        if (!$data or $data->metavalue == null or $data->metavalue == 0) return false;
        if (strpos($data->metavalue, '/') != false) {
            $arr = explode('/', $data->metavalue);
            $topic_id = $arr[0];
        } else $topic_id = $data->metavalue;
        return Topic::find($topic_id);
    }


    public function getPostCount()
    {

        $count = \Cache::remember('counttopic' . $this->id, 10, function () {
            $topic = $this->topic();
            return $topic ? $topic->posts_count : 0;
        });
        return $count;
    }


    public function quizz()
    {
        $data = $this->customdata('quizz_id')->first();
        if (!$data or $data->metavalue == null or $data->metavalue == 0 or !is_numeric($data->metavalue)) return false;
        return \App\Models\QuizzEvent::find($data->metavalue);
    }


    public function solution()
    {
        $data = $this->customdata('solution_id')->first();
        if (!$data or $data->metavalue == null or $data->metavalue == 0 or !is_numeric($data->metavalue)) return false;
        $a = Article::find($data->metavalue);
        if (!$a) return false;
        $u = $a->urlsite;
        if (!$u) return false;
        return $u->url;
    }

    //fin class
}
