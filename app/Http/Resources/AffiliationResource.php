<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AffiliationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'user'          => new UserResource($this->user),
            'pseudo_room'   => $this->pseudo_room,
            'room'          => $this->room,
            'statut'        => $this->statut,
            'createdAt'     => Carbon::parse($this->created_at)->toDateTimeString()
        ];
    }
}
