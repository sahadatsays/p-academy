<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatagResource extends JsonResource
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
            'name'          => $this->name,
            'type'          => $this->type,
            'status'        => $this->state ? true : false,
            'parent'        => new PatagResource($this->parent),
            'article_counts'=> $this->articles()->count(),
            'createdAt'     => Carbon::parse($this->created_at)->toDateTimeString(),
            'updatedAt'     => Carbon::parse($this->updated_at)->toDateTimeString()
        ];
    }
}
