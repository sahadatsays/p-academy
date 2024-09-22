<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Url301 extends Model
{
    use HasFactory;
    protected $table = 'zt_urls301';

     protected $guarded = array('id');

       public function urlsite()
    {
       return $this->belongsTo(Urlsite::class)->withDefault(); 
    }
}
