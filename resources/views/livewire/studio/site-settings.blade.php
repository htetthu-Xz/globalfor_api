<div class="studio-page studio-page--narrow">
    <header class="studio-page-head">
        <div>
            <p class="studio-kicker studio-kicker--gold"><span></span> PUBLIC WEBSITE</p>
            <h1>Site settings</h1>
            <p>Update the company details that visitors use most often.</p>
        </div>
    </header>

    <form wire:submit="save" class="studio-settings-grid">
        <section class="studio-settings-card">
            <div class="studio-settings-card__head"><span>01</span><div><h2>Company identity</h2><p>The logo and official display name used across the public website.</p></div></div>

            <div class="studio-logo-setting">
                <div class="studio-logo-setting__preview">
                    @if ($newLogo)
                        <img src="{{ $newLogo->temporaryUrl() }}" alt="">
                    @elseif ($currentLogoUrl)
                        <img src="{{ $currentLogoUrl }}" alt="">
                    @else
                        <span class="studio-brand__mark" aria-hidden="true"><i></i><i></i><i></i></span>
                    @endif
                </div>
                <div class="studio-logo-setting__controls">
                    <label class="studio-button studio-button--quiet">
                        <input type="file" wire:model="newLogo" accept="image/jpeg,image/png,image/webp">
                        {{ $currentLogoUrl ? 'Replace logo' : 'Upload logo' }}
                    </label>
                    <small>JPEG, PNG or WebP · 3 MB maximum</small>
                    @error('newLogo')<small class="studio-field-error">{{ $message }}</small>@enderror
                </div>
            </div>

            <label class="studio-field">
                <span>Company display name</span>
                <input type="text" wire:model="companyName" maxlength="255">
                @error('companyName')<small class="studio-field-error">{{ $message }}</small>@enderror
            </label>
        </section>

        <section class="studio-settings-card">
            <div class="studio-settings-card__head"><span>02</span><div><h2>Contact essentials</h2><p>Keep the public contact page, footer and contact actions accurate.</p></div></div>
            <div class="studio-form">
                <div class="studio-form-columns">
                    <label class="studio-field"><span>Telephone</span><input type="text" wire:model="phone" maxlength="50">@error('phone')<small class="studio-field-error">{{ $message }}</small>@enderror</label>
                    <label class="studio-field"><span>Business email</span><input type="email" wire:model="email" maxlength="255">@error('email')<small class="studio-field-error">{{ $message }}</small>@enderror</label>
                </div>
                <label class="studio-field"><span>Office address</span><textarea wire:model="address" rows="4" maxlength="2000"></textarea>@error('address')<small class="studio-field-error">{{ $message }}</small>@enderror</label>
                <label class="studio-field"><span>Facebook page</span><input type="url" wire:model="facebookUrl" maxlength="2048" placeholder="https://www.facebook.com/…">@error('facebookUrl')<small class="studio-field-error">{{ $message }}</small>@enderror</label>
            </div>
        </section>

        <div class="studio-settings-save">
            <span wire:loading wire:target="save">Saving website details…</span>
            <button type="submit" class="studio-button studio-button--primary" wire:loading.attr="disabled" wire:target="save">Save site settings</button>
        </div>
    </form>
</div>
