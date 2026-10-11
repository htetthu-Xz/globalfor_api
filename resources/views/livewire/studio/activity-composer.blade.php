<div class="studio-composer" x-data="{ panel: 'compose' }">
    <header class="studio-composer-head">
        <div>
            <a href="{{ route('studio.activities.index') }}" class="studio-back-link" wire:navigate>← Activities</a>
            <h1>{{ $activityId ? 'Refine activity' : 'Compose activity' }}</h1>
        </div>
        <div class="studio-compose-actions">
            <span class="studio-save-state" wire:loading>Saving changes…</span>
            <button type="button" class="studio-button studio-button--quiet" wire:click="saveDraft" wire:loading.attr="disabled">Save draft</button>
            <button type="button" class="studio-button studio-button--primary" wire:click="publish" wire:loading.attr="disabled">Publish <span aria-hidden="true">↗</span></button>
        </div>
    </header>

    <div class="studio-mobile-switch" aria-label="Composer view">
        <button type="button" :class="panel === 'compose' && 'is-active'" @click="panel = 'compose'">Compose</button>
        <button type="button" :class="panel === 'preview' && 'is-active'" @click="panel = 'preview'">Preview</button>
    </div>

    <div class="studio-compose-grid">
        <section class="studio-compose-form" :class="panel !== 'compose' && 'is-mobile-hidden'">
            <div class="studio-form-section">
                <div class="studio-form-section__index">01</div>
                <div class="studio-form-section__body">
                    <h2>Tell the story</h2>
                    <p>Keep it natural and useful. The full text appears directly in the public feed.</p>
                    <label class="studio-field">
                        <span>Post title</span>
                        <input type="text" wire:model.live.debounce.250ms="title" maxlength="255" placeholder="A clear, short headline">
                        @error('title')<small class="studio-field-error">{{ $message }}</small>@enderror
                    </label>
                    <label class="studio-field">
                        <span>Post text</span>
                        <textarea wire:model.live.debounce.250ms="body" rows="9" maxlength="10000" placeholder="What happened, who was involved, and why does it matter?"></textarea>
                        <small class="studio-field-hint"><span>{{ strlen($body) }}</span> / 10,000</small>
                        @error('body')<small class="studio-field-error">{{ $message }}</small>@enderror
                    </label>
                </div>
            </div>

            <div class="studio-form-section">
                <div class="studio-form-section__index">02</div>
                <div class="studio-form-section__body">
                    <h2>Build the gallery</h2>
                    <p>Add up to ten photographs. Accessibility labels are created automatically from the post title.</p>

                    @if ($existingImages)
                        <div class="studio-upload-list">
                            @foreach ($existingImages as $index => $image)
                                <div @class(['studio-upload-item', 'is-removed' => $image['removed']]) wire:key="existing-{{ $image['id'] }}">
                                    <img src="{{ $image['url'] }}" alt="">
                                    <button type="button" wire:click="toggleExistingImage({{ $index }})">{{ $image['removed'] ? 'Restore' : 'Remove' }}</button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if ($newImages)
                        <div class="studio-upload-list">
                            @foreach ($newImages as $index => $image)
                                <div class="studio-upload-item" wire:key="new-{{ $index }}-{{ $image->getFilename() }}">
                                    <img src="{{ $image->temporaryUrl() }}" alt="">
                                    <button type="button" wire:click="removeNewImage({{ $index }})">Remove</button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <label class="studio-dropzone">
                        <input type="file" wire:model="newImages" multiple accept="image/jpeg,image/png,image/webp">
                        <span class="studio-dropzone__icon">＋</span>
                        <strong>Add photographs</strong>
                        <small>JPEG, PNG or WebP · 5 MB each · 10 images maximum</small>
                    </label>
                    <p class="studio-uploading" wire:loading wire:target="newImages">Preparing image previews…</p>
                    @error('newImages')<small class="studio-field-error">{{ $message }}</small>@enderror
                    @error('newImages.*')<small class="studio-field-error">{{ $message }}</small>@enderror
                </div>
            </div>
        </section>

        <aside class="studio-preview" :class="panel !== 'preview' && 'is-mobile-hidden'">
            <div class="studio-preview__label"><span>LIVE PREVIEW</span><small>Public activity feed</small></div>
            <article class="activity-preview-card">
                <header>
                    <span class="activity-preview-avatar">
                        @if ($siteSettings->logoMedia)<img src="{{ $siteSettings->logoMedia->publicUrl() }}" alt="">@else<i></i><i></i><i></i>@endif
                    </span>
                    <span><strong>{{ $siteSettings->company_name }}</strong><small>Official update · Just now</small></span>
                    <b>•••</b>
                </header>
                <div class="activity-preview-copy">
                    <h2>{{ $title ?: 'Your activity title will appear here' }}</h2>
                    <p>{{ $body ?: 'Start writing to see the public post take shape in real time. The preview keeps your text and gallery together.' }}</p>
                </div>
                @php
                    $visibleExisting = collect($existingImages)->where('removed', false);
                    $previewCount = $visibleExisting->count() + count($newImages);
                @endphp
                @if ($previewCount)
                    <div @class(['activity-preview-gallery', 'has-many' => $previewCount > 1])>
                        @foreach ($visibleExisting->take(4) as $image)
                            <img src="{{ $image['url'] }}" alt="{{ $title ? $title.' — activity photo' : 'Activity photo' }}">
                        @endforeach
                        @foreach (collect($newImages)->take(max(0, 4 - $visibleExisting->count())) as $index => $image)
                            <img src="{{ $image->temporaryUrl() }}" alt="{{ $title ? $title.' — activity photo '.($index + 1) : 'Activity photo' }}">
                        @endforeach
                    </div>
                @else
                    <div class="activity-preview-empty"><span>＋</span><p>Your photo gallery will appear here.</p></div>
                @endif
                <footer><span>{{ strtoupper($siteSettings->company_name) }} · MYANMAR</span><span>Share ↗</span></footer>
            </article>
            <p class="studio-preview__note"><i></i> Preview approximates the public feed. Final line breaks adapt to each screen.</p>
        </aside>
    </div>
</div>
