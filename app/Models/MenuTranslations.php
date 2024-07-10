<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuTranslations extends Model
{
    use HasFactory;
    protected $guarded = array('id');
    protected $table = 'zt_menus_translations';

    /**
     * Get the user that owns the MenuTranslations
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the menu that owns the MenuTranslations
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function scopeLanguage($query, $lang)
    {
        return $query->where('lang', '=', $lang);
    }
}
