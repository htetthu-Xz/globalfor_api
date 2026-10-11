<?php

namespace App\Livewire\Studio;

use App\Enums\PublicationStatus;
use App\Models\Activity;
use App\Services\MediaAssetService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class ActivityComposer extends Component
{
    use WithFileUploads;

    public ?int $activityId = null;

    public string $title = '';

    public string $body = '';

    /** @var array<int, mixed> */
    public array $newImages = [];

    /** @var array<int, array{id:int,url:string,removed:bool}> */
    public array $existingImages = [];

    public function mount(?Activity $activity = null): void
    {
        if (! $activity?->exists) {
            return;
        }

        $activity->load('images.media');
        $this->activityId = $activity->id;
        $this->title = $activity->title;
        $this->body = $activity->body;
        $this->existingImages = $activity->images->map(fn ($image) => [
            'id' => $image->id,
            'url' => $image->media->publicUrl(),
            'removed' => false,
        ])->values()->all();
    }

    public function removeNewImage(int $index): void
    {
        unset($this->newImages[$index]);
        $this->newImages = array_values($this->newImages);
    }

    public function toggleExistingImage(int $index): void
    {
        if (isset($this->existingImages[$index])) {
            $this->existingImages[$index]['removed'] = ! $this->existingImages[$index]['removed'];
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
        $wasCreating = $this->activityId === null;
        $retainedCount = collect($this->existingImages)->where('removed', false)->count();

        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:10000'],
            'newImages' => ['array', 'max:'.max(0, 10 - $retainedCount)],
            'newImages.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'existingImages.*.removed' => ['boolean'],
        ], [
            'newImages.max' => 'An activity can contain no more than 10 images.',
        ]);

        $activity = DB::transaction(function () use ($status, $media, $validated) {
            $activity = $this->activityId
                ? Activity::query()->with('images.media')->findOrFail($this->activityId)
                : new Activity(['created_by' => auth()->id()]);

            $activity->fill([
                'title' => trim($validated['title']),
                'body' => trim($validated['body']),
                'status' => $status,
                'updated_by' => auth()->id(),
            ])->save();

            foreach ($this->existingImages as $imageState) {
                $image = $activity->images()->with('media')->find($imageState['id']);
                if (! $image) {
                    continue;
                }

                if ($imageState['removed']) {
                    $asset = $image->media;
                    $image->delete();
                    $media->deleteIfUnused($asset);

                    continue;
                }
            }

            $nextPosition = ((int) $activity->images()->max('position')) + 1;
            foreach ($this->newImages as $upload) {
                $position = $nextPosition++;
                $altText = $activity->title.' — activity photo '.$position;
                $asset = $media->store($upload, auth()->user(), 'activities', $altText);
                $activity->images()->create([
                    'media_asset_id' => $asset->id,
                    'position' => $position,
                    'alt_text' => $altText,
                ]);
            }

            return $activity;
        });

        session()->flash('status', $status === PublicationStatus::Published ? 'Activity published.' : 'Draft saved.');

        if ($wasCreating) {
            $this->redirectRoute('studio.activities.index', navigate: true);

            return;
        }

        $this->redirectRoute('studio.activities.edit', ['activity' => $activity], navigate: true);
    }

    public function render(): View
    {
        return view('livewire.studio.activity-composer');
    }
}
