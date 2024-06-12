<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    protected $table = 'zt_groups';

    protected $guarded = array('id');

    public function users()
    {
        return $this->belongsToMany(UserGroup::class);
    }
}
