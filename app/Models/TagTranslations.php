<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TagTranslations extends Model
{
    use HasFactory;

    protected $table = 'zt_tags_translations';
    protected $guarded = ['id'];

    /**
     * Get the user that owns the TagTranslations
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tag that owns the TagTranslations
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }

    // Language Scope
    public function scopeLanguage($query, $lang)
    {
        return $query->where('lang', '=', $lang);
    }

    public function urlsite()
    {
        return $this->morphOne(Urlsite::class, 'linkto');
    }

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
            else {
                $slug = str()->slug($tag->name, '-') . '/';
                array_unshift($tree, $slug);
            }
        }
        if ($tag->parent_id != 0)
            $tree = $this->buildTreetag($tree, $tag->parent_id, $lang);
        return $tree;
    }

    public function getTagsSegmentsUrl()
   {
      // on va remonter son arborescence et regarder si on doit afficher le parent dans l'url
      $tmptree = array();

      $tmptree = $this->buildTreeTag($tmptree,$this->tag->id, $this->lang);

      // on construit la chaine de segment d'url
      $tagsSegmentsUrl = "";

      foreach ($tmptree as $slug)
      {
        $tagsSegmentsUrl .= $slug . '/';
      }
      
      return $tagsSegmentsUrl;
   }
   

   public function refreshUrl($reason)
   {
      if($this->tag->index_page == 1)
      {
        $url    = $this->getTagsSegmentsUrl();
        if (substr($url,-1)=='/') $url = substr($url,0,-1);
        //$url    = str_replace('{id}', $this->id, $url);
        //$url    .= Str::slug($this->title);
        // if (MLG) $url    = substr($this->lang, 0, 2) . '/' . $url;

        if (isset($this->urlsite->url)) $oldurl = $this->urlsite->url; else $oldurl = "";


        // si url change
        if ($oldurl != $url)
        {
           // si un robot est passé
           //if ( $oldurl != "" and $this->urlsite->botvisit_at != "0000-00-00 00:00:00")
           if ($oldurl != "")   
           {
              $url301                       = new Url301();
              $url301->alias                = $oldurl;
              $url301->urlsite_id           = $this->urlsite->id;
              $url301->urlsite->botvisit_at = "0000-00-00 00:00:00";
              $url301->reason = $reason;
              $url301->save();
      
              $this->urlsite->url         = $url;
              $this->urlsite->botvisit_at = '0000-00-00 00:00:00';
              $this->urlsite->save();
           }
           else
           {
              $urlsite = new Urlsite;
              $urlsite->url = $url;
              $this->urlsite()->save($urlsite);

           }

          }
      } 
  }
}
