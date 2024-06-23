<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operator extends Model
{
    use HasFactory;
    protected $table = 'pa_operateurs';
    protected $guarded = ['id'];

    public function scopePublished($query)
    {
        return $query->where('state', '=', 1);
    }

    public function scopeAffiliable($query)
    {
        return $query->where('affiliation', '=', 1);
    }
}
