<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class MediaAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_filename',
        'disk',
        'path',
        'mime_type',
        'width',
        'height',
        'byte_size',
        'alt_text',
        'attribution',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'width' => 'integer',
            'height' => 'integer',
            'byte_size' => 'integer',
        ];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function activityImages(): HasMany
    {
        return $this->hasMany(ActivityImage::class);
    }

    public function publicUrl(): string
    {
        if ($this->disk === 'public') {
            $path = '/storage/'.ltrim($this->path, '/');

            return app()->runningInConsole()
                ? rtrim((string) config('app.url'), '/').$path
                : request()->getSchemeAndHttpHost().$path;
        }

        return Storage::disk($this->disk)->url($this->path);
    }
}
