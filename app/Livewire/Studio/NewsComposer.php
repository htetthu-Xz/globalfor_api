<?php

namespace App\Livewire\Studio;

use App\Enums\PublicationStatus;
use App\Models\NewsItem;
use App\Services\MediaAssetService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class NewsComposer extends Component
{
    use WithFileUploads;

    public ?int $newsId = null;

    public string $title = '';

    public string $slug = '';

    public string $body = '';

    public bool $showOnHomepage = false;

    public ?int $homepageOrder = null;

    public mixed $newCover = null;

    public ?string $existingCoverUrl = null;

    public bool $removeCover = false;

    public function mount(?NewsItem $newsItem = null): void
    {
        if (! $newsItem?->exists) {
            return;
        }

        $newsItem->load('coverMedia');
        $this->newsId = $newsItem->id;
        $this->title = $newsItem->title;
        $this->slug = $newsItem->slug;
        $this->body = $newsItem->body;
        $this->showOnHomepage = $newsItem->show_on_homepage;
        $this->homepageOrder = $newsItem->homepage_order;
        $this->existingCoverUrl = $newsItem->coverMedia?->publicUrl();
    }

    public function updatedTitle(string $value): void
    {
        if ($this->newsId === null || $this->slug === '') {
            $this->slug = Str::slug($value);
        }
    }

    public function saveDraft(MediaAssetService $media): void
    {
        $this->persist(PublicationStatus::Draft, $media);
    }

    public function publish(MediaAssetService $media): void
    {
        $this->persist(PublicationStatus::Published, $media);
    }

    private function persist(PublicationStatus $status, MediaAssetService $media): void
    {
        $wasCreating = $this->newsId === null;
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('news_items', 'slug')->ignore($this->newsId)],
            'body' => ['required', 'string', 'max:50000'],
            'showOnHomepage' => ['boolean'],
            'homepageOrder' => ['nullable', 'integer', 'min:1', 'max:999'],
            'newCover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $news = DB::transaction(function () use ($status, $media, $validated) {
            $news = $this->newsId
                ? NewsItem::query()->with('coverMedia')->findOrFail($this->newsId)
                : new NewsItem(['created_by' => auth()->id()]);
            $previousCover = $news->coverMedia;
            $coverAltText = trim($validated['title']).' — news cover';

            if ($this->newCover) {
                $asset = $media->store($this->newCover, auth()->user(), 'news', $coverAltText);
                $news->cover_media_id = $asset->id;
            } elseif ($this->removeCover) {
                $news->cover_media_id = null;
            }

            $news->fill([
                'title' => trim($validated['title']),
                'slug' => $validated['slug'],
                'body' => trim($validated['body']),
                'cover_alt_text' => $news->cover_media_id ? $coverAltText : null,
                'status' => $status,
                'show_on_homepage' => $validated['showOnHomepage'],
                'homepage_order' => $validated['showOnHomepage'] ? ($validated['homepageOrder'] ?: 1) : null,
                'updated_by' => auth()->id(),
            ])->save();

            if ($news->cover_media_id) {
                $news->coverMedia()->update(['alt_text' => $coverAltText]);
            }

            if ($previousCover && $previousCover->id !== $news->cover_media_id) {
                $media->deleteIfUnused($previousCover);
            }

            return $news;
        });

        session()->flash('status', $status === PublicationStatus::Published ? 'News entry published.' : 'Draft saved.');

        if ($wasCreating) {
            // Use a full redirect after creation so the new composer cannot be
            // preserved by Livewire's client-side navigation cache.
            $this->redirectRoute('studio.news.index');

            return;
        }

        $this->redirectRoute('studio.news.edit', ['newsItem' => $news], navigate: true);
    }

    public function render(): View
    {
        return view('livewire.studio.news-composer');
    }
}
