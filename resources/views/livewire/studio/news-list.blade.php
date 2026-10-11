<div class="studio-page">
    <header class="studio-page-head">
        <div>
            <p class="studio-kicker studio-kicker--blue"><span></span> NEWSROOM</p>
            <h1>News & guides</h1>
            <p>Prepare announcements and useful information with dedicated public pages.</p>
        </div>
        <a href="{{ route('studio.news.create') }}" class="studio-button studio-button--primary studio-button--blue" wire:navigate><span>＋</span> Write news</a>
    </header>

    <section class="studio-context-strip studio-context-strip--blue" aria-label="News publishing summary">
        <div><span>All entries</span><strong>{{ \App\Models\NewsItem::count() }}</strong></div>
        <div><span>Published</span><strong>{{ \App\Models\NewsItem::published()->count() }}</strong></div>
        <div><span>On homepage</span><strong>{{ \App\Models\NewsItem::visibleOnHomepage()->count() }}</strong></div>
        <p><i></i> Every published item has a detail page</p>
    </section>

    <div class="studio-toolbar">
        <label class="studio-search"><span>⌕</span><input type="search" wire:model.live.debounce.300ms="search" placeholder="Search news" aria-label="Search news"></label>
        <div class="studio-segmented">
            <button type="button" wire:click="$set('status', 'all')" @class(['is-active' => $status === 'all'])>All</button>
            <button type="button" wire:click="$set('status', 'published')" @class(['is-active' => $status === 'published'])>Published</button>
            <button type="button" wire:click="$set('status', 'draft')" @class(['is-active' => $status === 'draft'])>Drafts</button>
        </div>
    </div>

    <div class="studio-content-list" wire:loading.class="is-loading">
        @forelse ($items as $item)
            <article class="studio-content-row" wire:key="news-{{ $item->id }}">
                <a href="{{ route('studio.news.edit', $item) }}" class="studio-content-row__media" wire:navigate tabindex="-1">
                    @if ($item->coverMedia)<img src="{{ $item->coverMedia->publicUrl() }}" alt="">@else<div class="studio-media-placeholder studio-media-placeholder--blue"><span>◇</span></div>@endif
                </a>
                <div class="studio-content-row__body">
                    <div class="studio-content-row__meta">
                        <span @class(['studio-status', 'studio-status--live' => $item->status === $publishedStatus])><i></i>{{ $item->status === $publishedStatus ? 'Published' : 'Draft' }}</span>
                        @if ($item->show_on_homepage)<span class="studio-home-badge">Homepage</span>@endif
                        <time>Updated {{ $item->updated_at->diffForHumans() }}</time>
                    </div>
                    <a href="{{ route('studio.news.edit', $item) }}" wire:navigate><h2>{{ $item->title }}</h2></a>
                    <p>{{ \Illuminate\Support\Str::limit($item->body, 150) }}</p>
                    <small>/news/{{ $item->slug }}</small>
                </div>
                <div class="studio-content-row__actions">
                    <a href="{{ route('studio.news.edit', $item) }}" class="studio-icon-button" aria-label="Edit {{ $item->title }}" wire:navigate>↗</a>
                    <button type="button" class="studio-icon-button studio-icon-button--danger" wire:click="confirmDelete({{ $item->id }})" aria-label="Delete {{ $item->title }}">×</button>
                </div>
            </article>
        @empty
            <div class="studio-empty"><span class="studio-empty__orbit studio-empty__orbit--blue">◇</span><h2>{{ $search ? 'No matching news' : 'Your newsroom is ready' }}</h2><p>{{ $search ? 'Try a different search phrase or status.' : 'Create an announcement, guide or important company update.' }}</p>@unless($search)<a href="{{ route('studio.news.create') }}" class="studio-button studio-button--primary studio-button--blue" wire:navigate>Write the first entry</a>@endunless</div>
        @endforelse
    </div>
    <div class="studio-pagination">{{ $items->links() }}</div>

    @if ($pendingDeleteId)
        <x-studio-delete-dialog
            dialog-id="delete-news"
            title="Delete this news entry?"
            message="The news page and its cover photograph will be removed from Studio and the public website. This cannot be undone."
            :target-title="$pendingDeleteTitle"
        />
    @endif
</div>
