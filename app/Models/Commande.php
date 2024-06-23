<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Order model.
 */
class Commande extends Model {
	public $timestamps = true;

	protected $table = 'pa_commandes';

	protected $guarded = ['id'];

    /**
     * Get the member that owns the Commande
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Membre::class);
    }
}
