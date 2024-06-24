<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleTracking extends Model
{
    use HasFactory;

    protected $table = 'zt_modules_tracking';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
