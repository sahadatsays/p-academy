<?php

namespace App\Http\Resources;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransfertPpaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'expediteur' => $this->expediteur == 0 ? ' Poker Académie' : User::find($this->expediteur)->username,
            'destinataire' => $this->destinataire == 0 ? ' Poker Académie' : User::find($this->destinataire)->username,
            'ppa' => $this->ppa,
            'motif' => $this->motif,
            'categ' => $this->categ,
            'createdAt' => Carbon::parse($this->created_at)->toDateTimeString()
        ];
    }
}
