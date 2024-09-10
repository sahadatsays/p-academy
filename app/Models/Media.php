<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    use HasFactory;

    protected $table = 'zt_medias';

    public function linkTo()
    {
        return $this->morphTo();
    }
    public function scopeKey($query, $key)
    {
        return $query->where('key', '=', $key);
    }

    /**
     * Get the user that owns the Media
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
