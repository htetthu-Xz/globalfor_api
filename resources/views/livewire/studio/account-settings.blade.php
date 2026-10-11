<div class="studio-page studio-page--narrow">
    <header class="studio-page-head"><div><p class="studio-kicker"><span></span> YOUR STUDIO IDENTITY</p><h1>My account</h1><p>Keep your contact details and password current.</p></div><a href="{{ route('studio.settings') }}" class="studio-button studio-button--quiet" wire:navigate><x-studio-icon name="settings" /> Site settings</a></header>

    <div class="studio-settings-grid">
        <section class="studio-settings-card">
            <div class="studio-settings-card__head"><span>01</span><div><h2>Profile details</h2><p>Used to identify your content updates inside Studio.</p></div></div>
            @if (session('profile-status'))<div class="studio-alert studio-alert--success">{{ session('profile-status') }}</div>@endif
            <form wire:submit="updateProfile" class="studio-form">
                <label class="studio-field"><span>Name</span><input type="text" wire:model="name" autocomplete="name">@error('name')<small class="studio-field-error">{{ $message }}</small>@enderror</label>
                <label class="studio-field"><span>Email address</span><input type="email" wire:model="email" autocomplete="email">@error('email')<small class="studio-field-error">{{ $message }}</small>@enderror</label>
                <button type="submit" class="studio-button studio-button--primary">Save profile</button>
            </form>
        </section>

        <section class="studio-settings-card">
            <div class="studio-settings-card__head"><span>02</span><div><h2>Change password</h2><p>Use 12 or more characters with mixed case, a number and a symbol.</p></div></div>
            @if (session('password-status'))<div class="studio-alert studio-alert--success">{{ session('password-status') }}</div>@endif
            <form wire:submit="updatePassword" class="studio-form">
                <label class="studio-field"><span>Current password</span><input type="password" wire:model="currentPassword" autocomplete="current-password">@error('current_password', 'updatePassword')<small class="studio-field-error">{{ $message }}</small>@enderror</label>
                <label class="studio-field"><span>New password</span><input type="password" wire:model="password" autocomplete="new-password">@error('password', 'updatePassword')<small class="studio-field-error">{{ $message }}</small>@enderror</label>
                <label class="studio-field"><span>Confirm new password</span><input type="password" wire:model="passwordConfirmation" autocomplete="new-password"></label>
                <button type="submit" class="studio-button studio-button--primary">Update password</button>
            </form>
        </section>

        <section class="studio-settings-card studio-settings-card--session">
            <div class="studio-settings-card__head"><span>03</span><div><h2>Current session</h2><p>Signed in as {{ auth()->user()->email }}.</p></div></div>
            <dl><div><dt>Account status</dt><dd><i></i> Active</dd></div><div><dt>Last sign in</dt><dd>{{ auth()->user()->last_login_at?->format('d M Y, H:i') ?? 'This session' }}</dd></div></dl>
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="studio-button studio-button--quiet">Sign out of Studio</button></form>
        </section>
    </div>
</div>
