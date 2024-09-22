<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class URLSiteResource extends JsonResource
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
            'url'           => env('APP_URL') . $this->url,
            'hits'          => $this->hits,
            'type'          => $this->linkto_type,
            'createdAt'     => Carbon::parse($this->created_at)->toDateTimeString()
        ];
    }
}
