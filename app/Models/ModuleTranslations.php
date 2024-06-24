<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleTranslations extends Model
{
    use HasFactory;
    protected $table = 'zt_modules_translations';
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function scopeLanguage($query, $lang)
    {
        return $query->where('lang', '=', $lang);
    }
}
