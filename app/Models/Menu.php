<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'zt_menus';

    protected $guarded = ['id'];

    /**
     * Get the urlSite that owns the Menu
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function urlSite(): BelongsTo
    {
        return $this->belongsTo(Urlsite::class, 'urlsite_id');
    }

    /**
     * Get all of the childs for the Menu
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childs(): HasMany
    {
        return $this->hasMany(Menu::class, 'menu_id');
    }

    /**
     * Get the parent that owns the Menu
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id', 'id');
    }

    // Scope
    public function scopePublished($query)
    {
        return $query->where('status', 1);
    }
}
