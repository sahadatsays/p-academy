<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
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
            'name'          => str()->ucfirst($this->name),
            'permissions'   => $this->permissions,
            'createdAt'     => Carbon::parse($this->created_at)->toDateTimeString(),
            'pivot'         => $this->pivot,
        ];
    }
}
