<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'lib'           => $this->lib,
            'prix'          => $this->prix,
            'user'          => new UserResource($this->user),
            'paiement'      => $this->paiement,
            'date'          => Carbon::parse($this->created_at)->toDateTimeString()
        ];
    }
}
