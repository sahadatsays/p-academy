<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Urlsite extends Model
{
    use HasFactory;

    protected $table = 'zt_urls';
    protected $guarded = ['id'];

    public function linkto()
    {
        return $this->morphTo();
    }
}
