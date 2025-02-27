<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $table = "zt_languages";

    public function scopeActivated($query)
    {
        return $query->where('active', '=', 1);
    }
}
