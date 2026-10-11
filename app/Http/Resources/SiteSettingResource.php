<?php

namespace App\Http\Resources;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteSettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'companyName' => $this->company_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'facebookUrl' => $this->facebook_url,
            'logoUrl' => $this->when(
                $this->relationLoaded('logoMedia') && $this->logoMedia,
                fn (): string => $this->logoMedia->publicUrl(),
            ),
        ];
    }

    public static function defaults(): self
    {
        return self::make(new SiteSetting(SiteSetting::DEFAULTS));
    }
}
