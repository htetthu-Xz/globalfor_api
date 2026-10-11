<?php

namespace Tests\Feature\Api;

use App\Enums\PublicationStatus;
use App\Models\NewsItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicNewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_published_news_item_has_a_public_detail_response(): void
    {
        $news = NewsItem::query()->create([
            'slug' => 'published-guide',
            'title' => 'Published guide',
            'body' => 'Verified public news content.',
            'status' => PublicationStatus::Published,
            'show_on_homepage' => true,
            'homepage_order' => 1,
        ]);

        $this->getJson('/api/v1/news/published-guide')
            ->assertOk()
            ->assertJsonPath('data.slug', 'published-guide')
            ->assertJsonPath('data.publishedAt', $news->created_at->toIso8601String())
            ->assertJsonPath('data.updatedAt', $news->updated_at->toIso8601String())
            ->assertJsonPath('data.showOnHomepage', true);
    }

    public function test_draft_news_items_are_not_publicly_accessible(): void
    {
        NewsItem::query()->create([
            'slug' => 'draft-guide',
            'title' => 'Draft guide',
            'body' => 'Private draft content.',
            'status' => PublicationStatus::Draft,
            'show_on_homepage' => true,
        ]);

        $this->getJson('/api/v1/news/draft-guide')->assertNotFound();
        $this->getJson('/api/v1/news?homepage=1')->assertJsonCount(0, 'data');
    }
}
