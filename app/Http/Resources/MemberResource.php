<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
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
            'username' => $this->username,
            'name' => $this->name,
            'email' => $this->email,
            'groupes' => [],
            'activated' => $this->activated ? true : false,
            'createdAt' => Carbon::parse($this->created_at)->toDateString(),
            'lastActivity' => Carbon::parse($this->last_activity)->toDateString(),
            'firstName' => $this->first_name,
            'lastName' => $this->last_name
        ];
    }
}
