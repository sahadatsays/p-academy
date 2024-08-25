<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'bank' => $this->bank,
            'item_id' => $this->item_id,
            'item_id' => $this->item_id,
            'prix' => $this->prix,
            'created_at' => Carbon::parse($this->created_at)->toDateTimeString(),
        ];
    }
}
