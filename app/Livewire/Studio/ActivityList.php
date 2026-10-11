<?php

namespace App\Livewire\Studio;

use App\Enums\PublicationStatus;
use App\Models\Activity;
use App\Services\MediaAssetService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityList extends Component
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

    public function confirmDelete(int $activityId): void
    {
        $activity = Activity::query()->findOrFail($activityId);

        $this->pendingDeleteId = $activity->id;
        $this->pendingDeleteTitle = $activity->title;
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

        $activity = Activity::query()->with('images.media')->findOrFail($this->pendingDeleteId);
        $assets = $activity->images->pluck('media')->filter();

        DB::transaction(fn () => $activity->delete());
        $assets->each(fn ($asset) => $media->deleteIfUnused($asset));

        $this->cancelDelete();
        session()->flash('status', 'Activity deleted.');
        $this->resetPage();
    }

    public function render(): View
    {
        $activities = Activity::query()
            ->with(['images.media', 'updater'])
            ->when($this->search !== '', fn ($query) => $query->where(function ($nested) {
                $nested->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('body', 'like', '%'.$this->search.'%');
            }))
            ->when($this->status !== 'all', fn ($query) => $query->where('status', $this->status))
            ->latest('updated_at')
            ->paginate(10);

        return view('livewire.studio.activity-list', [
            'activities' => $activities,
            'publishedStatus' => PublicationStatus::Published,
        ]);
    }
}
