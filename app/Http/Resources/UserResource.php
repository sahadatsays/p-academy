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
            'activatedAt' => $this->activated_at != null ? Carbon::parse($this->activated_at)->toDateTimeString() : null,
            'createdAt' => $this->created_at != null ? Carbon::parse($this->created_at)->format('d F Y - H:i') : null,
            'lastActivity' => $this->last_activity != null ? Carbon::parse($this->last_activity)->format('d F Y - H:i') : null,
            'lastLogin' => $this->last_login != null ? Carbon::parse($this->last_login)->toDateTimeString() : null,
        ];
    }
}
