@props([
    'dialogId',
    'title',
    'message',
    'targetTitle',
    'confirmLabel' => 'Delete permanently',
])

<div
    class="studio-dialog-backdrop"
    wire:keydown.escape.window="cancelDelete"
>
    <section
        class="studio-dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="{{ $dialogId }}-title"
        aria-describedby="{{ $dialogId }}-description"
        @click.stop
    >
        <span class="studio-dialog__symbol" aria-hidden="true">×</span>
        <p class="studio-kicker"><span></span> PERMANENT ACTION</p>
        <h2 id="{{ $dialogId }}-title">{{ $title }}</h2>
        <p id="{{ $dialogId }}-description">{{ $message }}</p>
        <div class="studio-dialog__target"><small>Selected item</small><strong>{{ $targetTitle }}</strong></div>
        <div class="studio-dialog__actions">
            <button type="button" class="studio-button studio-button--quiet" wire:click="cancelDelete" wire:loading.attr="disabled" wire:target="deleteConfirmed">Keep it</button>
            <button
                type="button"
                class="studio-button studio-button--danger-solid"
                wire:click="deleteConfirmed"
                wire:loading.attr="disabled"
                wire:target="deleteConfirmed"
            >
                <span wire:loading.remove wire:target="deleteConfirmed">{{ $confirmLabel }}</span>
                <span wire:loading wire:target="deleteConfirmed">Deleting…</span>
            </button>
        </div>
    </section>
</div>
