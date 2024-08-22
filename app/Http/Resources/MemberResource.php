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
            'id'            => $this->id,
            'user'          => new UserResource($this->user),
            'sex'           => $this->sexe,
            'avatar'        => $this->avatar,
            'birthDate'     => $this->naissance,
            'phone'         => $this->tel,
            'addressFirst'  => $this->add1,
            'addressSecond' => $this->add2,
            'postCode'      => $this->codepostal,
            'city'          => $this->ville,
            'country'       => $this->pays,
            'ppa'           => $this->ppa,
            'pevbc'         => $this->pevbc,
            'punibet'       => $this->punibet,
            'web'           => $this->web,
            'skype'         => $this->skype,
            'gplus'         => $this->gplus,
            'urlSignup'    => $this->url_signup,
            'createdAt'    => Carbon::parse($this->created_at)->toDateTimeString(),
        ];
    }
}
