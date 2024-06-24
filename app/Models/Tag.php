<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'zt_tags';

    public function childs()
    {
        return $this->hasMany(Tag::class, 'parent_id');
    }


    public function parent()
    {
        return $this->belongsTo(Tag::class, 'parent_id', 'id');
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'zt_tags_articles');
    }


    public function articlesForIndex($lang)
    {
        $grouplangs = array();
        $articlesId = array();

        // 1. on prend tous les articles de la langue demandée
        $first = $this->articles()->published()->language($lang)->get();
        foreach ($first as $f) {
            if ($f->rules != "") {
                if (Auth::check()) {
                    $articlesId[] = $f->id;
                    $grouplangs[] = $f->group_lang_id;
                }
            } else {
                $articlesId[] = $f->id;
                $grouplangs[] = $f->group_lang_id;
            }
        }

        // 2. on prend tous les articles de la langue secondaire ( fr_FR si en_US demandée, en_US sinon )
        if ($lang == 'en_US') $secondlang = "fr_FR";
        else $secondlang = "en_US";
        if (!empty($grouplangs)) $second = $this->articles()->published()->language($secondlang)->whereNotIn('group_lang_id', $grouplangs)->get();
        else $second = $this->articles()->published()->language($secondlang)->get();
        foreach ($second as $s) {
            if ($s->rules != "") {
                if (Auth::check()) {
                    $articlesId[] = $s->id;
                    $grouplangs[] = $s->group_lang_id;
                }
            } else {
                $articlesId[] = $s->id;
                $grouplangs[] = $s->group_lang_id;
            }
        }

        // 3 si lang != FR ou EN on prend fr en 3ème langue
        if ($lang != 'en_US' and $lang != 'fr_FR') {
            if (!empty($grouplangs)) $third = $this->articles()->published()->language("fr_FR")->whereNotIn('group_lang_id', $grouplangs)->get();
            else $third = $this->articles()->published()->language("fr_FR")->get();
            //$third = $this->articles()->published()->language("fr_FR")->whereNotIn('group_lang_id', $grouplangs)->get();
            foreach ($third as $t) {
                if ($t->rules != "") {
                    if (Auth::check()) {
                        $articlesId[] = $t->id;
                    }
                } else {
                    $articlesId[] = $t->id;
                }
            }
        }

        $tri = explode(' ', $this->sort_by);
        if (!empty($articlesId)) $articles = $this->articles()->published()->whereIn('id', $articlesId)->orderBy($tri[0], $tri[1]);
        else $articles = Null;

        /*
      $articles = $articles->filter(function($article)
      {
          if ($article->rules!="")
          {
            if ( Auth::check() and Auth::user()->hasAccess($tag->rules)) return $article;
          }
      });
      */
        return $articles;
    }


    public function old_AllArticlesForIndex($lang = "fr_FR")
    {
        $articlesId = array();
        $grouplangs = array();


        // 1. on prend tous les articles du tag pour la langue
        $first = $this->articles()->published()->language($lang)->get();
        foreach ($first as $f) {
            if ($f->rules != "") {
                if (Auth::check()) {
                    $articlesId[] = $f->id;
                    $grouplangs[] = $f->group_lang_id;
                }
            } else {
                $articlesId[] = $f->id;
                $grouplangs[] = $f->group_lang_id;
            }
        }

        // 2. on prend tous les articles des sous-tags de profondeur 1
        $tagsenfants = $this->childs()->get();

        foreach ($tagsenfants as $t) {

            $second = $t->articles()->published()->language($lang)->get();

            foreach ($second as $a) {

                if ($a->rules != "") {
                    if (Auth::check()) {
                        $articlesId[] = $a->id;
                    }
                } else {
                    $articlesId[] = $a->id;
                }
            }
        }


        $tri = explode(' ', $this->sort_by);

        return Article::published()->whereIn('id', $articlesId)->limit(abs($this->per_page))->orderBy($tri[0], $tri[1]);
    }


    private function getArticlesIDForHome($lang)
    {
        $user = auth()->user();

        $ids = [];
        $tri = explode(' ', $this->sort_by);

        $articles = $this->articles()->published()->language($lang)->take(20)->orderBy($tri[0], $tri[1])->get();


        // \Log::info( $articles->toArray() );

        foreach ($articles as $a) {
            if ($a->rules != "") {
                if ($user) {
                    if ($a->rules != "admin") $ids[] = $a->id;
                    else {
                        if ($user->isAdmin()) $ids[] = $a->id;
                    }
                }
            } else {
                $ids[] = $a->id;
            }
        }

        return $ids;
    }

    public function getArticlesID($lang)
    {
        $user = auth()->user();
        $ids = [];
        $articles = $this->articles()->published()->language($lang)->get();

        foreach ($articles as $a) {
            if ($a->rules != "") {
                if ($user) {
                    if ($a->rules != "admin") $ids[] = $a->id;
                    else {
                        if ($user->isAdmin()) $ids[] = $a->id;
                    }
                }
            } else {
                $ids[] = $a->id;
            }
        }

        foreach ($this->childs()->get() as $t) {
            $ids = array_unique(array_merge($ids, $t->getArticlesID($lang)));
        }

        return $ids;
    }

    public function getArticlesIDForIndex($lang, $home = '')
    {
        $tag = $this;
        if ($home == 'home')
            $ids = Cache::remember('allarticles_forTheHOME_' . $this->id, 10, function () use ($tag, $lang) {
                return $tag->getArticlesIDForHome($lang);
            });
        else
            $ids = Cache::remember('allarticles_' . $this->id, 10, function () use ($tag, $lang) {
                return $tag->getArticlesID($lang);
            });
        return $ids;
    }

    public function AllArticlesForIndex($lang = "fr_FR")
    {
        $ids = $this->getArticlesIDForIndex($lang);
        $tri = explode(' ', $this->sort_by);
        return Article::with('data')->published()->whereIn('id', $ids)->limit(abs($this->per_page))->orderBy($tri[0], $tri[1]);
    }

    public function AllArticlesForHome($lang = "fr_FR")
    {
        $ids = $this->getArticlesIDForIndex($lang, 'home');
        $tri = explode(' ', $this->sort_by);
        return Article::with('data')->published()->whereIn('id', $ids)->limit(abs($this->per_page))->orderBy($tri[0], $tri[1]);
    }


    public function medias()
    {
        return $this->morphMany(Media::class, 'linkto')->where('state', '=', 1);
    }

    public function translations()
    {
        return $this->hasMany('\Kelio\Zetatori\TagTranslations');
    }

    public function buildTreeChilds($tree, $id)
    {
        $tag = Tag::find($id);

        foreach ($tag->childs()->get() as $c) {
            $tree = $this->buildTreeChilds($tree, $c->id);
        }

        if ($tag->in_url == 1) {
            $tree[] = $id;
        }
        return $tree;
    }

    public function refreshUrl($reason)
    {
        $tree = array();

        $tree = $this->buildTreeChilds($tree, $this->id);

        foreach ($tree as $tagid) {
            foreach (Tag::find($tagid)->translations()->get() as $t) {
                $t->refreshUrl($reason);
            }
        }
    }


    public function avis()
    {
        return $this->HasMany('\App\Models\Avis', 'id_contenu');
    }
}
