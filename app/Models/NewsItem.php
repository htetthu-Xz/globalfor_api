<?php

namespace App\Models;

use App\Enums\PublicationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'body',
        'cover_media_id',
        'cover_alt_text',
        'status',
        'show_on_homepage',
        'homepage_order',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => PublicationStatus::class,
            'show_on_homepage' => 'boolean',
            'homepage_order' => 'integer',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PublicationStatus::Published->value);
    }

    public function scopeVisibleOnHomepage(Builder $query): Builder
    {
        return $query
            ->published()
            ->where('show_on_homepage', true)
            ->orderBy('homepage_order')
            ->orderByDesc('created_at');
    }

    public function coverMedia(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'cover_media_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
