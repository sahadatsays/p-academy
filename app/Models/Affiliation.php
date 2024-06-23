<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Affiliation extends Model
{
    public $timestamps = true;
    protected $table = 'pa_affiliations';
    protected $guarded = array('id');

    /**
     * Get the user that owns the Affiliation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the membre that owns the Affiliation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function membre()
    {
        return $this->belongsTo(Membre::class, 'user_id');
    }
}
