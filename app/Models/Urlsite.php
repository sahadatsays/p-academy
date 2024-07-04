<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Urlsite extends Model
{
    use HasFactory;

    protected $table = 'zt_urls';
    protected $guarded = ['id'];

    public function linkto()
    {
        return $this->morphTo();
    }

    /**
     * Get all of the menus for the Urlsite
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }
}
