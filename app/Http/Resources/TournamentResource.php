<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TournamentResource extends JsonResource
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
            'title'         => $this->titre,
            'since'         => $this->buyin,
            'type_tournament' => $this->typetournoi,
            'password'      => $this->password,
            'added_op'      => $this->added_op,
            'start_date'    => Carbon::parse($this->date_debut)->toDateTimeString(),
            'end_date'      => Carbon::parse($this->date_fin)->toDateTimeString(),
            'article_id'    => $this->article_id,
            'operator'      => new OperatorResource($this->operator)
        ];
    }
}
