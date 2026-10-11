<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'position' => $this->position,
            'altText' => $this->alt_text,
            'url' => $this->media->publicUrl(),
            'width' => $this->media->width,
            'height' => $this->media->height,
        ];
    }
}
