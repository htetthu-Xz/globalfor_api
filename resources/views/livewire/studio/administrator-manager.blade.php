<div class="studio-page studio-page--narrow" x-data>
    <header class="studio-page-head"><div><p class="studio-kicker studio-kicker--gold"><span></span> ACCESS</p><h1>Administrators</h1><p>Everyone listed here has the same Studio permissions.</p></div><button type="button" class="studio-button studio-button--primary" wire:click="$set('showCreate', true)"><span>＋</span> Add administrator</button></header>

    @if (session('admin-status'))<div class="studio-alert studio-alert--success">{{ session('admin-status') }}</div>@endif
    @error('administrator')<div class="studio-alert studio-alert--error">{{ $message }}</div>@enderror

    @if ($showCreate)
        <section class="studio-inline-create">
            <div><p class="studio-kicker"><span></span> NEW ACCOUNT</p><h2>Invite by creating credentials</h2><p>Share the temporary password securely and ask the administrator to change it after signing in.</p></div>
            <form wire:submit="createAdministrator" class="studio-form">
                <div class="studio-form-columns"><label class="studio-field"><span>Name</span><input type="text" wire:model="name" autofocus>@error('name')<small class="studio-field-error">{{ $message }}</small>@enderror</label><label class="studio-field"><span>Email</span><input type="email" wire:model="email">@error('email')<small class="studio-field-error">{{ $message }}</small>@enderror</label></div>
                <div class="studio-form-columns"><label class="studio-field"><span>Temporary password</span><input type="password" wire:model="password">@error('password')<small class="studio-field-error">{{ $message }}</small>@enderror</label><label class="studio-field"><span>Confirm password</span><input type="password" wire:model="passwordConfirmation"></label></div>
                <div class="studio-compose-actions"><button type="button" class="studio-button studio-button--quiet" wire:click="$set('showCreate', false)">Cancel</button><button type="submit" class="studio-button studio-button--primary">Create administrator</button></div>
            </form>
        </section>
    @endif

    <section class="studio-admin-list" aria-label="Administrator accounts">
        @foreach ($administrators as $administrator)
            <article wire:key="admin-{{ $administrator->id }}">
                <span class="studio-avatar">{{ strtoupper(substr($administrator->name, 0, 1)) }}</span>
                <div><h2>{{ $administrator->name }} @if ($administrator->is(auth()->user()))<small>You</small>@endif</h2><p>{{ $administrator->email }}</p><small>Last sign in: {{ $administrator->last_login_at?->diffForHumans() ?? 'Never' }}</small></div>
                <span @class(['studio-status', 'studio-status--live' => $administrator->is_active])><i></i>{{ $administrator->is_active ? 'Active' : 'Inactive' }}</span>
                @unless ($administrator->is(auth()->user()))<button type="button" class="studio-button studio-button--quiet" wire:click="toggleActive({{ $administrator->id }})" wire:confirm="{{ $administrator->is_active ? 'Deactivate this administrator?' : 'Reactivate this administrator?' }}">{{ $administrator->is_active ? 'Deactivate' : 'Reactivate' }}</button>@endunless
            </article>
        @endforeach
    </section>
</div>
