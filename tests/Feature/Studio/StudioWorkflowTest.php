<?php

namespace Tests\Feature\Studio;

use App\Enums\PublicationStatus;
use App\Livewire\Studio\ActivityComposer;
use App\Livewire\Studio\ActivityList;
use App\Livewire\Studio\NewsComposer;
use App\Livewire\Studio\NewsList;
use App\Livewire\Studio\SiteSettings;
use App\Models\Activity;
use App\Models\MediaAsset;
use App\Models\NewsItem;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class StudioWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_administrator_can_open_every_studio_section(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user);

        foreach (['activities', 'news', 'settings', 'account', 'administrators'] as $section) {
            $this->get('/studio/'.$section)->assertOk();
        }

        $this->get('/studio/media')->assertNotFound();
    }

    public function test_image_description_fields_are_not_shown_in_composers(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)
            ->get('/studio/activities/create')
            ->assertOk()
            ->assertDontSee('wire:date-picker', false)
            ->assertDontSee('Publishing schedule')
            ->assertDontSee('type="datetime-local"', false)
            ->assertDontSee('Image description')
            ->assertDontSee('Describe every new image');

        $this->get('/studio/news/create')
            ->assertOk()
            ->assertDontSee('wire:date-picker', false)
            ->assertDontSee('Publishing schedule')
            ->assertDontSee('type="datetime-local"', false)
            ->assertDontSee('Cover description')
            ->assertDontSee('Describe the photograph');
    }

    public function test_administrator_can_create_activity_draft_and_publish_it(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        Livewire::actingAs($user)
            ->test(ActivityComposer::class)
            ->set('title', 'Preparation workshop')
            ->set('body', 'Candidates completed a practical preparation workshop.')
            ->call('saveDraft')
            ->assertHasNoErrors()
            ->assertRedirect(route('studio.activities.index'));

        $this->assertDatabaseHas('activities', [
            'title' => 'Preparation workshop',
            'status' => PublicationStatus::Draft->value,
            'created_by' => $user->id,
        ]);

        $activity = Activity::firstOrFail();

        Livewire::actingAs($user)
            ->test(ActivityComposer::class, ['activity' => $activity])
            ->call('publish')
            ->assertHasNoErrors()
            ->assertRedirect(route('studio.activities.edit', $activity));

        $this->assertDatabaseHas('activities', [
            'id' => $activity->id,
            'status' => PublicationStatus::Published->value,
        ]);
        $this->assertNotNull($activity->fresh()->created_at);
        $this->assertNull($activity->fresh()->published_at);
    }

    public function test_administrator_can_publish_news_without_a_language_field(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        Livewire::actingAs($user)
            ->test(NewsComposer::class)
            ->set('title', 'Office information guide')
            ->set('slug', 'office-information-guide')
            ->set('body', 'Useful office information for candidates and business partners.')
            ->set('showOnHomepage', true)
            ->set('homepageOrder', 1)
            ->call('publish')
            ->assertHasNoErrors()
            ->assertRedirect(route('studio.news.index'));

        $this->assertDatabaseHas('news_items', [
            'slug' => 'office-information-guide',
            'status' => PublicationStatus::Published->value,
            'show_on_homepage' => true,
        ]);
    }

    public function test_administrator_can_publish_an_activity_with_an_image_without_a_description_field(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['is_active' => true]);
        $image = UploadedFile::fake()->image('classroom.jpg', 1280, 960)->size(500);

        Livewire::actingAs($user)
            ->test(ActivityComposer::class)
            ->set('title', 'Classroom preparation')
            ->set('body', 'Students completed their classroom preparation session.')
            ->set('newImages', [$image])
            ->call('publish')
            ->assertHasNoErrors()
            ->assertRedirect(route('studio.activities.index'));

        $activity = Activity::query()->with('images.media')->firstOrFail();

        $this->assertCount(1, $activity->images);
        $this->assertSame('Classroom preparation — activity photo 1', $activity->images->first()->alt_text);
        Storage::disk('public')->assertExists($activity->images->first()->media->path);
    }

    public function test_administrator_can_publish_news_with_a_cover_without_a_description_field(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['is_active' => true]);
        $image = UploadedFile::fake()->image('office.jpg', 1280, 960)->size(500);

        Livewire::actingAs($user)
            ->test(NewsComposer::class)
            ->set('title', 'Office information guide')
            ->set('slug', 'office-information-guide')
            ->set('body', 'Useful office information for candidates and business partners.')
            ->set('newCover', $image)
            ->call('publish')
            ->assertHasNoErrors()
            ->assertRedirect(route('studio.news.index'));

        $news = NewsItem::query()->with('coverMedia')->firstOrFail();

        $this->assertSame('Office information guide — news cover', $news->cover_alt_text);
        $this->assertSame('Office information guide — news cover', $news->coverMedia->alt_text);
        Storage::disk('public')->assertExists($news->coverMedia->path);
    }

    public function test_success_toast_can_close_and_auto_dismisses(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)
            ->withSession(['status' => 'Activity published.'])
            ->get('/studio/activities')
            ->assertOk()
            ->assertSee('Activity published.')
            ->assertSee('setTimeout(() => visible = false, 4000)', false)
            ->assertSee('Dismiss notification');
    }

    public function test_activity_delete_uses_custom_dialog_and_removes_attached_files(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['is_active' => true]);

        Livewire::actingAs($user)
            ->test(ActivityComposer::class)
            ->set('title', 'Departure preparation')
            ->set('body', 'Candidates prepared for departure.')
            ->set('newImages', [UploadedFile::fake()->image('departure.jpg', 1200, 900)])
            ->call('publish');

        $activity = Activity::query()->with('images.media')->firstOrFail();
        $asset = $activity->images->first()->media;
        Storage::disk('public')->assertExists($asset->path);

        Livewire::actingAs($user)
            ->test(ActivityList::class)
            ->call('confirmDelete', $activity->id)
            ->assertSet('pendingDeleteId', $activity->id)
            ->assertSee('Delete this activity?')
            ->assertSee('Departure preparation')
            ->assertDontSee('wire:confirm', false)
            ->call('deleteConfirmed')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('activities', ['id' => $activity->id]);
        $this->assertDatabaseMissing('media_assets', ['id' => $asset->id]);
        Storage::disk('public')->assertMissing($asset->path);
    }

    public function test_removing_an_activity_photo_deletes_its_file(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['is_active' => true]);

        Livewire::actingAs($user)
            ->test(ActivityComposer::class)
            ->set('title', 'Classroom update')
            ->set('body', 'A classroom update with one photograph.')
            ->set('newImages', [UploadedFile::fake()->image('classroom.jpg', 1200, 900)])
            ->call('publish');

        $activity = Activity::query()->with('images.media')->firstOrFail();
        $asset = $activity->images->first()->media;

        Livewire::actingAs($user)
            ->test(ActivityComposer::class, ['activity' => $activity])
            ->call('toggleExistingImage', 0)
            ->call('publish')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('media_assets', ['id' => $asset->id]);
        Storage::disk('public')->assertMissing($asset->path);
    }

    public function test_news_delete_removes_its_cover_file(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['is_active' => true]);

        Livewire::actingAs($user)
            ->test(NewsComposer::class)
            ->set('title', 'Company update')
            ->set('slug', 'company-update')
            ->set('body', 'A useful company update.')
            ->set('newCover', UploadedFile::fake()->image('cover.jpg', 1200, 900))
            ->call('publish');

        $news = NewsItem::query()->with('coverMedia')->firstOrFail();
        $asset = $news->coverMedia;
        Storage::disk('public')->assertExists($asset->path);

        Livewire::actingAs($user)
            ->test(NewsList::class)
            ->call('confirmDelete', $news->id)
            ->assertSet('pendingDeleteId', $news->id)
            ->assertSee('Delete this news entry?')
            ->assertSee('Company update')
            ->assertDontSee('wire:confirm', false)
            ->call('deleteConfirmed')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('news_items', ['id' => $news->id]);
        $this->assertDatabaseMissing('media_assets', ['id' => $asset->id]);
        Storage::disk('public')->assertMissing($asset->path);
    }

    public function test_removing_a_news_cover_deletes_its_file(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['is_active' => true]);

        Livewire::actingAs($user)
            ->test(NewsComposer::class)
            ->set('title', 'Office guide')
            ->set('slug', 'office-guide')
            ->set('body', 'Office information for visitors.')
            ->set('newCover', UploadedFile::fake()->image('office.jpg', 1200, 900))
            ->call('publish');

        $news = NewsItem::query()->with('coverMedia')->firstOrFail();
        $asset = $news->coverMedia;

        Livewire::actingAs($user)
            ->test(NewsComposer::class, ['newsItem' => $news])
            ->set('removeCover', true)
            ->call('publish')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('media_assets', ['id' => $asset->id]);
        Storage::disk('public')->assertMissing($asset->path);
    }

    public function test_administrator_can_update_essential_site_settings_and_logo(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['is_active' => true]);

        Livewire::actingAs($user)
            ->test(SiteSettings::class)
            ->set('companyName', 'Global F.O.R International Co., Ltd.')
            ->set('phone', '+95 9 123 456 789')
            ->set('email', 'hello@globalformm.com')
            ->set('address', 'Hlaing Township, Yangon, Myanmar')
            ->set('facebookUrl', 'https://www.facebook.com/globalfor')
            ->set('newLogo', UploadedFile::fake()->image('logo.png', 600, 600))
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('studio.settings'));

        $settings = SiteSetting::query()->with('logoMedia')->firstOrFail();

        $this->assertSame('Global F.O.R International Co., Ltd.', $settings->company_name);
        $this->assertSame('hello@globalformm.com', $settings->email);
        $this->assertNotNull($settings->logoMedia);
        Storage::disk('public')->assertExists($settings->logoMedia->path);

        $this->getJson('/api/v1/site-settings')
            ->assertOk()
            ->assertJsonPath('data.companyName', 'Global F.O.R International Co., Ltd.')
            ->assertJsonPath('data.phone', '+95 9 123 456 789')
            ->assertJsonPath('data.logoUrl', $settings->logoMedia->publicUrl());

        $this->actingAs($user)
            ->get('/studio/settings')
            ->assertOk()
            ->assertSee('GLOBAL F.O.R INTERNATIONAL CO., LTD.')
            ->assertSee($settings->logoMedia->publicUrl(), false);
    }

    public function test_replacing_site_logo_deletes_the_previous_file(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['is_active' => true]);

        Livewire::actingAs($user)
            ->test(SiteSettings::class)
            ->set('newLogo', UploadedFile::fake()->image('first.png', 600, 600))
            ->call('save');

        $oldAsset = MediaAsset::query()->firstOrFail();
        Storage::disk('public')->assertExists($oldAsset->path);

        Livewire::actingAs($user)
            ->test(SiteSettings::class)
            ->set('newLogo', UploadedFile::fake()->image('second.png', 600, 600))
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('media_assets', ['id' => $oldAsset->id]);
        Storage::disk('public')->assertMissing($oldAsset->path);
    }
}
