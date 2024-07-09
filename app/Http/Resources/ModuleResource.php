<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModuleResource extends JsonResource
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
            'status'        => $this->state,
            'showTtitle'    => $this->show_title ? true : false,
            'position'      => $this->position,
            'order'         => $this->order,
            'createdBy'     => new UserResource($this->user),
            'rules'         => ModuleRulesResource::collection($this->modulerules),
            'translation'   => ModuleTranslationResource::collection($this->translations),
            'createdAt'     => Carbon::parse($this->created_at)->toDateTimeString(),
            'updatedAt'     => Carbon::parse($this->updated_at)->toDateTimeString()
        ];
    }
}
