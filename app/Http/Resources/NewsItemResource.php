<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'body' => $this->body,
            'publishedAt' => $this->created_at?->toIso8601String(),
            'updatedAt' => $this->updated_at?->toIso8601String(),
            'showOnHomepage' => $this->show_on_homepage,
            'homepageOrder' => $this->homepage_order,
            'cover' => $this->when(
                $this->relationLoaded('coverMedia') && $this->coverMedia,
                fn (): array => [
                    'url' => $this->coverMedia->publicUrl(),
                    'altText' => $this->cover_alt_text,
                    'width' => $this->coverMedia->width,
                    'height' => $this->coverMedia->height,
                ],
            ),
        ];
    }
}
