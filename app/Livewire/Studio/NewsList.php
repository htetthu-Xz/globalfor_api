<?php

namespace App\Livewire\Studio;

use App\Enums\PublicationStatus;
use App\Models\NewsItem;
use App\Services\MediaAssetService;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class NewsList extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = 'all';

    public ?int $pendingDeleteId = null;

    public string $pendingDeleteTitle = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function confirmDelete(int $newsId): void
    {
        $news = NewsItem::query()->findOrFail($newsId);

        $this->pendingDeleteId = $news->id;
        $this->pendingDeleteTitle = $news->title;
    }

    public function cancelDelete(): void
    {
        $this->reset('pendingDeleteId', 'pendingDeleteTitle');
    }

    public function deleteConfirmed(MediaAssetService $media): void
    {
        if (! $this->pendingDeleteId) {
            return;
        }

        $news = NewsItem::query()->with('coverMedia')->findOrFail($this->pendingDeleteId);
        $cover = $news->coverMedia;
        $news->delete();

        if ($cover) {
            $media->deleteIfUnused($cover);
        }

        $this->cancelDelete();
        session()->flash('status', 'News entry deleted.');
        $this->resetPage();
    }

    public function render(): View
    {
        $items = NewsItem::query()
            ->with(['coverMedia', 'updater'])
            ->when($this->search !== '', fn ($query) => $query->where(function ($nested) {
                $nested->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('body', 'like', '%'.$this->search.'%');
            }))
            ->when($this->status !== 'all', fn ($query) => $query->where('status', $this->status))
            ->latest('updated_at')
            ->paginate(10);

        return view('livewire.studio.news-list', [
            'items' => $items,
            'publishedStatus' => PublicationStatus::Published,
        ]);
    }
}
