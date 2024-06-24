<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleRules extends Model
{
    use HasFactory;

    protected $table = 'zt_modules_rules';
    protected $guarded = ['id'];


    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
