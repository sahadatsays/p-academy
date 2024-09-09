<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use URL;
use DB;

class Coach extends Model
{
    public $timestamps = true;
    protected $table = 'pa_coachs';
    protected $guarded = array('id');


    public function user()
    {
        return $this->belongsTo(User::class, 'userid');
    }

    public function scopePublished($query)
    {
        return $query->where('actif', '=', 1);
    }

    public function article()
    {
        return $this->belongsTo(Article::class, 'lien_fiche');
    }


    public function videos()
    {

        $articles = Article::published()->whereHas('patags', function ($query) {
            $query->where('id', '=', $this->patags_id);
        });

        return $articles;
    }

    public function note()
    {
        $article = $this->article()->first();

        if (! $article) return 0;

        $avis = Avis::where('state', '=', '1')->where('id_contenu', '=', $article->id)->get();

        $nbavis = 0;
        $total = 0;
        foreach ($avis as $a) {
            $nba = 0;

            if ($a->vote1 != 0) $nba++;
            if ($a->vote2 != 0) $nba++;
            if ($a->vote3 != 0) $nba++;

            if ($nba > 0) {
                $total += ($a->vote1 + $a->vote2 + $a->vote3) / $nba;
                $nbavis++;
            }
        }

        if ($nbavis == 0) return 0;

        return round($total / $nbavis, 1);
    }
}
