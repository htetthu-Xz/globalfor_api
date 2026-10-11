<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteSetting extends Model
{
    /** @var array<string, string> */
    public const DEFAULTS = [
        'company_name' => 'Global F.O.R Co., Ltd.',
        'phone' => '+95 9 261 880 062',
        'email' => 'admin@globalformm.com',
        'address' => 'Room No(6-D)/6th Floor/Pannita Mini Condo(Shwe Myanmar Mya Condo)/Pannita Street/ No.(7)Block/ Hlaing Township, Yangon, Myanmar, 11051',
        'facebook_url' => 'https://www.facebook.com/share/1GAKsJK5A4/',
    ];

    protected $fillable = [
        'company_name',
        'phone',
        'email',
        'address',
        'facebook_url',
        'logo_media_id',
        'updated_by',
    ];

    public function logoMedia(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'logo_media_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
