<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        =>  $this->id,
            'username' => $this->username,
            'name' => $this->name(),
            'firstName' =>  $this->first_name,
            'lastName'  =>  $this->last_name,
            'email'     =>  $this->email,
            'groupes' => GroupResource::collection($this->groups),
            'activated' => $this->activated ? true : false,
            'createdAt' => Carbon::parse($this->created_at)->toDateTimeString(),
            'lastActivity' => Carbon::parse($this->last_activity)->toDateTimeString(),
            'lastLogin' => Carbon::parse($this->last_login)->toDateTimeString(),
        ];
    }
}
