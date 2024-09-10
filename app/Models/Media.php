<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $table = 'zt_medias';
    protected $guarded = ['id'];
    protected $appends = ['url'];

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

    public function getUrlAttribute() {
        return Storage::disk('s3')->url($this->media_file_name);
    }
}
