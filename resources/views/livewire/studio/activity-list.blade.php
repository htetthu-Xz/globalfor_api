<div class="studio-page">
    <header class="studio-page-head">
        <div>
            <p class="studio-kicker"><span></span> SOCIAL STREAM</p>
            <h1>Activities</h1>
            <p>Shape the moments people see in the public activity feed.</p>
        </div>
        <a href="{{ route('studio.activities.create') }}" class="studio-button studio-button--primary" wire:navigate>
            <span aria-hidden="true">＋</span> Compose activity
        </a>
    </header>

    <section class="studio-context-strip" aria-label="Activity publishing summary">
        <div><span>All posts</span><strong>{{ \App\Models\Activity::count() }}</strong></div>
        <div><span>Published</span><strong>{{ \App\Models\Activity::published()->count() }}</strong></div>
        <div><span>Drafts</span><strong>{{ \App\Models\Activity::where('status', 'draft')->count() }}</strong></div>
        <p><i></i> Public feed is newest first</p>
    </section>

    <div class="studio-toolbar">
        <label class="studio-search">
            <span aria-hidden="true">⌕</span>
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search activities" aria-label="Search activities">
        </label>
        <div class="studio-segmented" aria-label="Filter by status">
            <button type="button" wire:click="$set('status', 'all')" @class(['is-active' => $status === 'all'])>All</button>
            <button type="button" wire:click="$set('status', 'published')" @class(['is-active' => $status === 'published'])>Published</button>
            <button type="button" wire:click="$set('status', 'draft')" @class(['is-active' => $status === 'draft'])>Drafts</button>
        </div>
    </div>

    <div class="studio-content-list" wire:loading.class="is-loading">
        @forelse ($activities as $activity)
            <article class="studio-content-row" wire:key="activity-{{ $activity->id }}">
                <a href="{{ route('studio.activities.edit', $activity) }}" class="studio-content-row__media" wire:navigate tabindex="-1" aria-hidden="true">
                    @if ($activity->images->first()?->media)
                        <img src="{{ $activity->images->first()->media->publicUrl() }}" alt="">
                        @if ($activity->images->count() > 1)<span>+{{ $activity->images->count() - 1 }}</span>@endif
                    @else
                        <div class="studio-media-placeholder"><span>◎</span></div>
                    @endif
                </a>
                <div class="studio-content-row__body">
                    <div class="studio-content-row__meta">
                        <span @class(['studio-status', 'studio-status--live' => $activity->status === $publishedStatus])>
                            <i></i>{{ $activity->status === $publishedStatus ? 'Published' : 'Draft' }}
                        </span>
                        <time datetime="{{ $activity->updated_at->toAtomString() }}">Updated {{ $activity->updated_at->diffForHumans() }}</time>
                    </div>
                    <a href="{{ route('studio.activities.edit', $activity) }}" wire:navigate><h2>{{ $activity->title }}</h2></a>
                    <p>{{ \Illuminate\Support\Str::limit($activity->body, 150) }}</p>
                    <small>{{ $activity->images->count() }} {{ \Illuminate\Support\Str::plural('image', $activity->images->count()) }} · {{ $activity->updater?->name ?? 'Administrator' }}</small>
                </div>
                <div class="studio-content-row__actions">
                    <a href="{{ route('studio.activities.edit', $activity) }}" class="studio-icon-button" aria-label="Edit {{ $activity->title }}" wire:navigate>↗</a>
                    <button type="button" class="studio-icon-button studio-icon-button--danger" wire:click="confirmDelete({{ $activity->id }})" aria-label="Delete {{ $activity->title }}">×</button>
                </div>
            </article>
        @empty
            <div class="studio-empty">
                <span class="studio-empty__orbit">◎</span>
                <h2>{{ $search ? 'No matching activities' : 'Your activity stream starts here' }}</h2>
                <p>{{ $search ? 'Try a different search phrase or status.' : 'Share a company moment, classroom update or departure story.' }}</p>
                @unless ($search)<a href="{{ route('studio.activities.create') }}" class="studio-button studio-button--primary" wire:navigate>Compose the first activity</a>@endunless
            </div>
        @endforelse
    </div>

    <div class="studio-pagination">{{ $activities->links() }}</div>

    @if ($pendingDeleteId)
        <x-studio-delete-dialog
            dialog-id="delete-activity"
            title="Delete this activity?"
            message="The post and every photograph attached to it will be removed from Studio and the public activity feed. This cannot be undone."
            :target-title="$pendingDeleteTitle"
        />
    @endif
</div>
