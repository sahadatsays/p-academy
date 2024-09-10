<?php

namespace App\Http\Resources;

use App\Models\Language;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
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
            'type'          => $this->type,
            'viewName'      => $this->view_name,
            'order'         => $this->order,
            'indexPage'     => $this->index_page,
            'inUrl'         => $this->in_url,
            'createdAt'     => Carbon::parse($this->created_at)->toDateTimeString(),
            'updatedAt'     => Carbon::parse($this->updated_at)->toDateTimeString(),
            'parent'        => new TagResource($this->parent),
            'nbarticles'    => $this->nbarticles ?? 0,
            'translation'   => Language::where('default_locale', $this->translations()->first()->lang ?? '')->first()->english_name ?? '',
            'languages'     => []
        ];
    }
}
