<?php

namespace App\Services;

use App\Models\MediaAsset;
use App\Models\NewsItem;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class MediaAssetService
{
    public function store(UploadedFile $file, User $user, string $collection, ?string $altText = null): MediaAsset
    {
        $dimensions = @getimagesize($file->getRealPath());

        if ($dimensions === false) {
            throw new RuntimeException('The uploaded file is not a readable image.');
        }

        $extension = strtolower($file->guessExtension() ?: $file->getClientOriginalExtension());
        $filename = Str::uuid().'.'.$extension;
        $directory = trim($collection, '/').'/'.now()->format('Y/m');
        $path = $file->storeAs($directory, $filename, 'public');

        if (! $path) {
            throw new RuntimeException('The image could not be stored.');
        }

        return MediaAsset::query()->create([
            'original_filename' => $file->getClientOriginalName(),
            'disk' => 'public',
            'path' => $path,
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
            'width' => $dimensions[0],
            'height' => $dimensions[1],
            'byte_size' => $file->getSize(),
            'alt_text' => trim($altText ?: 'Uploaded image'),
            'uploaded_by' => $user->id,
        ]);
    }

    public function deleteIfUnused(MediaAsset $asset): void
    {
        $isUsed = $asset->activityImages()->exists()
            || NewsItem::query()->where('cover_media_id', $asset->id)->exists()
            || SiteSetting::query()->where('logo_media_id', $asset->id)->exists();

        if ($isUsed) {
            return;
        }

        Storage::disk($asset->disk)->delete($asset->path);
        $asset->delete();
    }
}
