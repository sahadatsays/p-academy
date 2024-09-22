<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Url301Resource extends JsonResource
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
            'alias' =>  env('APP_URL') . $this->alias,
            'hits' => $this->hits,
            'reason' => $this->reason,
            'url' => env('APP_URL') . $this->urlsite->url,
            'url_id' => $this->urlsite->id,
            'visit_at' => Carbon::parse($this->visit_at)->toDateTimeString(),
            'bot_visit' => $this->botvisit_at === "0000-00-00 00:00:00" ? 'Never' : Carbon::parse($this->botvisit_visit)->toDateTimeString()
        ];
    }
}
