<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'publishedAt' => $this->created_at?->toIso8601String(),
            'images' => ActivityImageResource::collection($this->whenLoaded('images')),
        ];
    }
}
