<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;

class Avis extends Model
{

    public $timestamps = true;
    protected $table = 'pa_avis';
    public $guarded = ['id'];


    public function user()
    {
        return $this->belongsTo(User::class, 'userid');
    }


    public function membre()
    {
        return $this->belongsTo(Membre::class, 'userid');
    }


    public function scopePublished($query)
    {
        return $query->where('state', '=', 1);
    }

    public function note()
    {
        $note = 0;
        $nb_note = 0;

        if ($this->vote1 != 0) {
            $note = $this->vote1;
            $nb_note = 1;
        }
        if ($this->vote2 != 0) {
            $note = $note + $this->vote2;
            $nb_note = 2;
        }
        if ($this->vote3 != 0) {
            $note = $note + $this->vote3;
            $nb_note = 3;
        }
        if ($note != 0) {
            $note = $note / $nb_note;
        }

        return round($note, 0, PHP_ROUND_HALF_UP);
    }
}
