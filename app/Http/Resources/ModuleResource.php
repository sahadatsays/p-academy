<?php

namespace App\Http\Resources;

use App\Models\Language;
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
        $lang = $this->translations()->first()->lang ?? '';
        if ($lang) {
            $lang = Language::where('default_locale', $lang)->first()->english_name ?? '';
        }
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'status'        => $this->state,
            'showTtitle'    => $this->show_title ? true : false,
            'position'      => $this->position,
            'order'         => $this->order,
            'createdBy'     => new UserResource($this->user),
            'rules'         => $this->rules,
            'translations'   => ModuleTranslationResource::collection($this->translations),
            'translation'   => $lang,
            'createdAt'     => Carbon::parse($this->created_at)->toDateTimeString(),
            'updatedAt'     => Carbon::parse($this->updated_at)->toDateTimeString()
        ];
    }
}
