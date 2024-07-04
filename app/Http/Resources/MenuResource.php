<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
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
            'order'         => $this->order,
            'parent'        => new MenuResource($this->parent),
            'url_externe'   => $this->url_externe,
            'targetBlank'   => $this->target_blank ? true : false,
            'status'        => $this->state,
            'urlSite'       => new URLSiteResource($this->urlSite),
            'updated'       => Carbon::parse($this->updated_at)->toDateTimeString(),
            'created'       => Carbon::parse($this->created_at)->toDateTimeString(),
        ];
    }
}
