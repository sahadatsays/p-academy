<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransfertPpa extends Model
{
    use HasFactory;

    protected $table = 'pa_transferts_ppa';
    protected $guarded = ['id'];
}
