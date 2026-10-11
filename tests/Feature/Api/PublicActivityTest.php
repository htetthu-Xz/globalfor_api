<?php

namespace Tests\Feature\Api;

use App\Enums\PublicationStatus;
use App\Models\Activity;
use App\Models\MediaAsset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_only_the_ten_newest_published_activities(): void
    {
        $newest = null;

        foreach (range(0, 10) as $index) {
            $activity = Activity::query()->create([
                'title' => "Published activity {$index}",
                'body' => 'Verified public activity content.',
                'status' => PublicationStatus::Published,
            ]);

            $newest = $activity;
        }

        Activity::query()->create([
            'title' => 'Draft activity',
            'body' => 'This must remain private.',
            'status' => PublicationStatus::Draft,
        ]);

        $asset = MediaAsset::query()->create([
            'original_filename' => 'activity.jpg',
            'disk' => 'public',
            'path' => 'activities/activity.jpg',
            'mime_type' => 'image/jpeg',
            'width' => 1200,
            'height' => 800,
            'byte_size' => 100000,
            'alt_text' => 'Published activity image',
        ]);

        $newest->images()->create([
            'media_asset_id' => $asset->id,
            'position' => 1,
            'alt_text' => 'Published activity image',
        ]);

        $response = $this
            ->withServerVariables(['HTTP_HOST' => 'localhost:8000'])
            ->getJson('/api/v1/activities');

        $response
            ->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('data.0.id', $newest->id)
            ->assertJsonPath('data.0.publishedAt', $newest->created_at->toIso8601String())
            ->assertJsonMissing(['title' => 'Draft activity'])
            ->assertJsonMissingPath('data.0.category')
            ->assertJsonPath('data.0.images.0.url', 'http://localhost:8000/storage/activities/activity.jpg');

        $this->assertNotNull($response->json('links.next'));
    }
}
