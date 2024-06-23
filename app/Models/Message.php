<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'zt_messages';
    protected $guarded = array('id');

    /**
     * Get the sender that owns the Message
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver that owns the Message
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Scope a query to only Read message.
     */
    public function scopeRead($query)
    {
        return $query->where('state', '=', 1);
    }

    /**
     * Scope a query to only unRead messages
     */
    public function scopeUnRead($query)
    {
        return $query->where('state', '=', 0);
    }
}
