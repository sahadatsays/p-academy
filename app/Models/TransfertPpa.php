<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransfertPpa extends Model
{
    public $timestamps = true;
    protected $table = 'pa_transferts_ppa';
    protected $guarded = array('id');

}
