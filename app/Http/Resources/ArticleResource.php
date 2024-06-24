<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
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
            'title'         => $this->title,
            'slug'          => $this->slug,
            'page_title'    => $this->page_title,
            'content'       => $this->content,
            'metakeywords'  => $this->metakeywords,
            'metadescription'=> $this->metadescription,
            'user'          => new UserResource($this->user),
            'publishUp'     => Carbon::parse($this->publish_up)->toDateTimeString(),
            'publishDown'   => Carbon::parse($this->publish_down)->toDateTimeString(),
            'likesFacebook' => $this->likesfacebook,
            'likes'         => $this->likes,
            'hits'          => $this->hits,
            'state'         => $this->state ? true : false,
            'order'         => $this->order,
            'rules'         => $this->rules,
            'viewName'      => $this->view_name,
            'createdBy'     => $this->user->username ?? null,
            'tags'          => TagResource::collection($this->tags),
            'createdAt'     => Carbon::parse($this->created_at)->toDateTimeString(),
            'updatedAt'     => Carbon::parse($this->updated_at)->toDateTimeString(),
        ];
    }
}
