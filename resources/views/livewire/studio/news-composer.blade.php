<div class="studio-composer studio-composer--news" x-data="{ panel: 'compose' }">
    <header class="studio-composer-head">
        <div><a href="{{ route('studio.news.index') }}" class="studio-back-link" wire:navigate>← News</a><h1>{{ $newsId ? 'Refine news' : 'Write news' }}</h1></div>
        <div class="studio-compose-actions"><span class="studio-save-state" wire:loading>Saving changes…</span><button type="button" class="studio-button studio-button--quiet" wire:click="saveDraft" wire:loading.attr="disabled">Save draft</button><button type="button" class="studio-button studio-button--primary studio-button--blue" wire:click="publish" wire:loading.attr="disabled">Publish <span>↗</span></button></div>
    </header>
    <div class="studio-mobile-switch"><button type="button" :class="panel === 'compose' && 'is-active'" @click="panel = 'compose'">Compose</button><button type="button" :class="panel === 'preview' && 'is-active'" @click="panel = 'preview'">Preview</button></div>

    <div class="studio-compose-grid">
        <section class="studio-compose-form" :class="panel !== 'compose' && 'is-mobile-hidden'">
            <div class="studio-form-section"><div class="studio-form-section__index">01</div><div class="studio-form-section__body">
                <h2>Frame the message</h2><p>The title and address should make the topic immediately clear.</p>
                <label class="studio-field"><span>Headline</span><input type="text" wire:model.live.debounce.250ms="title" maxlength="255" placeholder="A useful, specific headline">@error('title')<small class="studio-field-error">{{ $message }}</small>@enderror</label>
                <label class="studio-field"><span>Page address</span><div class="studio-slug-field"><span>/news/</span><input type="text" wire:model="slug" maxlength="255" placeholder="page-address"></div>@error('slug')<small class="studio-field-error">{{ $message }}</small>@enderror</label>
                <label class="studio-field"><span>Article</span><textarea wire:model.live.debounce.250ms="body" rows="14" maxlength="50000" placeholder="Write the complete news item or guide…"></textarea><small class="studio-field-hint"><span>{{ strlen($body) }}</span> / 50,000</small>@error('body')<small class="studio-field-error">{{ $message }}</small>@enderror</label>
            </div></div>

            <div class="studio-form-section"><div class="studio-form-section__index">02</div><div class="studio-form-section__body">
                <h2>Set the cover</h2><p>The cover is optional. Its accessibility label is created automatically from the headline.</p>
                @if ($newCover)
                    <div class="studio-cover-preview"><img src="{{ $newCover->temporaryUrl() }}" alt=""><button type="button" wire:click="$set('newCover', null)">Remove new cover</button></div>
                @elseif ($existingCoverUrl && ! $removeCover)
                    <div class="studio-cover-preview"><img src="{{ $existingCoverUrl }}" alt=""><button type="button" wire:click="$set('removeCover', true)">Remove cover</button></div>
                @elseif ($existingCoverUrl && $removeCover)
                    <button type="button" class="studio-restore-cover" wire:click="$set('removeCover', false)">Restore current cover</button>
                @endif
                <label class="studio-dropzone"><input type="file" wire:model="newCover" accept="image/jpeg,image/png,image/webp"><span class="studio-dropzone__icon">＋</span><strong>{{ $existingCoverUrl ? 'Replace cover photograph' : 'Add a cover photograph' }}</strong><small>JPEG, PNG or WebP · 5 MB maximum</small></label>
                @error('newCover')<small class="studio-field-error">{{ $message }}</small>@enderror
            </div></div>

            <div class="studio-form-section"><div class="studio-form-section__index">03</div><div class="studio-form-section__body">
                <h2>Choose placement</h2><p>Every published entry gets a detail page. You can also feature it on the homepage.</p>
                <label class="studio-toggle"><input type="checkbox" wire:model.live="showOnHomepage"><span><i></i></span><b>Show this entry on the homepage</b></label>
                @if ($showOnHomepage)<label class="studio-field studio-field--compact"><span>Homepage order</span><input type="number" wire:model="homepageOrder" min="1" max="999" placeholder="1">@error('homepageOrder')<small class="studio-field-error">{{ $message }}</small>@enderror</label>@endif
            </div></div>
        </section>

        <aside class="studio-preview" :class="panel !== 'preview' && 'is-mobile-hidden'">
            <div class="studio-preview__label"><span>LIVE PREVIEW</span><small>News detail page</small></div>
            <article class="news-preview-card">
                <div class="news-preview-card__meta"><span>{{ strtoupper($siteSettings->company_name) }} / NEWS</span><time>PREVIEW</time></div>
                <h2>{{ $title ?: 'Your news headline will appear here' }}</h2>
                <p class="news-preview-card__lead">{{ \Illuminate\Support\Str::limit($body ?: 'Start writing to preview the opening of your article. Every published news entry receives its own shareable page.', 240) }}</p>
                @if ($newCover)<img src="{{ $newCover->temporaryUrl() }}" alt="{{ $title ? $title.' — news cover' : 'News cover' }}">@elseif ($existingCoverUrl && ! $removeCover)<img src="{{ $existingCoverUrl }}" alt="{{ $title ? $title.' — news cover' : 'News cover' }}">@else<div class="news-preview-card__empty">NEWS<br>WITHOUT<br>A COVER</div>@endif
                <footer><span>MYANMAR → JAPAN · SINGAPORE</span><span>Read story ↗</span></footer>
            </article>
            <p class="studio-preview__note"><i></i> The public page adds the site header, footer and final responsive typography.</p>
        </aside>
    </div>
</div>
