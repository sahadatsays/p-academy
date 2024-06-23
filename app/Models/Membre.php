<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Membre extends Model
{
    public $timestamps = true;
    protected $table = 'pa_membres';
    public $incrementing = false;
    public $guarded = ['id'];

    protected $dates = ['trial_ends_at', 'subscription_ends_at'];

    /**
     * Get the user that owns the Membre
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all of the commandes for the Membre
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function commandes(): HasMany
    {
        return $this->hasMany(Commande::class, 'user_id');
    }

    /**
     * Get all of the transfertsppaEnvoye for the Membre
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transfertsppaEnvoye(): HasMany
    {
        return $this->hasMany(TransfertPpa::class, 'expediteur');
    }

    /**
     * Get all of the transfertsppaRecu for the Membre
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transfertsppaRecu(): HasMany
    {
        return $this->hasMany(TransfertPpa::class, 'destinataire');
    }

    public function allTransfertPpa()
    {
        return $this->transfertsppaEnvoye->merge($this->transfertsppaRecu);
    }

    /**
     * Get all of the affiliations for the Membre
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function affiliations(): HasMany
    {
        return $this->hasMany(Affiliation::class, 'user_id');
    }

    /**
     * @param $room_id
     * @return @object
     */
    public function affilie($room_id)
    {
        return $this->affiliations()->where('statut', 'Compte Affilié')->where('room_id', $room_id)->first();
    }

    /**
     * Get all of the messagesrecus for the Membre
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messagesrecus()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Get all of the messagesenvoyes for the Membre
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messagesenvoyes()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * The vods that belong to the Membre
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function vods()
    {
        return $this->belongsToMany('\App\Models\Video', 'pa_users_vods', 'user_id', 'video_id');
    }

    public function vodsSerie()
    {
        return $this->belongsToMany('\App\Models\VideoSerie', 'pa_users_vods_series', 'user_id', 'serie_id');
    }
    public function vodachetee($video_id)
    {
        return $this->vods()->where('id', $video_id)->first();
    }
    public function serieachetee($serie_id)
    {
        return $this->vodsSerie()->where('id', $serie_id)->first();
    }
}
