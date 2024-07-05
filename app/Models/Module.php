<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Module extends Model
{
    use HasFactory;
    protected $table = 'zt_modules';
    protected $guarded = ['id'];

    /**
     * Get the user that owns the Module
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

  public function translations()
  {
    return $this->hasMany(ModuleTranslations::class);
  }


  public function scopePublished($query)
  {
    return $query->where('state', '=', 1);
  }


  public function scopePosition($query, $position)
  {
    return $query->wherePosition($position);
  }


  public function modulerules()
  {
    return $this->HasMany(ModuleRules::class, 'module_id');
  }

  public function moduletracking()
  {
    return $this->HasMany(ModuleTracking::class);
  }
}
