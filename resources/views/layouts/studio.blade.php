<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <meta name="theme-color" content="#f5f5ef">
    <title>@yield('title', 'Studio') · {{ $siteSettings->company_name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="studio-body">
    <div class="studio-app">
        <aside class="studio-rail">
            <a href="{{ route('studio.activities.index') }}" class="studio-brand" wire:navigate>
                @if ($siteSettings->logoMedia)
                    <img class="studio-brand__image" src="{{ $siteSettings->logoMedia->publicUrl() }}" alt="">
                @else
                    <span class="studio-brand__mark" aria-hidden="true"><i></i><i></i><i></i></span>
                @endif
                <span><strong>{{ strtoupper($siteSettings->company_name) }}</strong><small>CONTENT STUDIO</small></span>
            </a>

            <nav class="studio-nav" aria-label="Studio navigation">
                <p>Publish</p>
                <a href="{{ route('studio.activities.index') }}" @class(['is-active' => request()->routeIs('studio.activities.*')]) wire:navigate>
                    <span class="studio-nav__icon"><x-studio-icon name="activities" /></span><span>Activities</span>
                </a>
                <a href="{{ route('studio.news.index') }}" @class(['is-active' => request()->routeIs('studio.news.*')]) wire:navigate>
                    <span class="studio-nav__icon"><x-studio-icon name="news" /></span><span>News</span>
                </a>
                <p>Manage</p>
                <a href="{{ route('studio.administrators.index') }}" @class(['is-active' => request()->routeIs('studio.administrators.*')]) wire:navigate>
                    <span class="studio-nav__icon"><x-studio-icon name="administrators" /></span><span>Administrators</span>
                </a>
                <a href="{{ route('studio.settings') }}" @class(['is-active' => request()->routeIs('studio.settings')]) wire:navigate>
                    <span class="studio-nav__icon"><x-studio-icon name="settings" /></span><span>Site settings</span>
                </a>
                <a href="{{ route('studio.account') }}" @class(['is-active' => request()->routeIs('studio.account')]) wire:navigate>
                    <span class="studio-nav__icon"><x-studio-icon name="account" /></span><span>My account</span>
                </a>
            </nav>

            <div class="studio-rail__account">
                <span class="studio-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                <span><strong>{{ auth()->user()->name }}</strong><small>Administrator</small></span>
                <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" title="Sign out" aria-label="Sign out"><x-studio-icon name="logout" /></button></form>
            </div>
        </aside>

        <header class="studio-mobile-head">
            <a href="{{ route('studio.activities.index') }}" class="studio-brand" wire:navigate>
                @if ($siteSettings->logoMedia)
                    <img class="studio-brand__image" src="{{ $siteSettings->logoMedia->publicUrl() }}" alt="">
                @else
                    <span class="studio-brand__mark" aria-hidden="true"><i></i><i></i><i></i></span>
                @endif
                <span><strong>{{ strtoupper($siteSettings->company_name) }}</strong><small>STUDIO</small></span>
            </a>
            <a href="{{ route('studio.account') }}" class="studio-avatar" aria-label="Open account" wire:navigate>{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</a>
        </header>

        <main class="studio-main">
            @if (session('status'))
                <div
                    class="studio-toast"
                    role="status"
                    x-data="{ visible: true }"
                    x-init="setTimeout(() => visible = false, 4000)"
                    x-show="visible"
                    x-transition.opacity.duration.200ms
                >
                    <span>{{ session('status') }}</span>
                    <button type="button" aria-label="Dismiss notification" @click="visible = false">×</button>
                </div>
            @endif
            @yield('content')
        </main>

        <nav class="studio-bottom-nav" aria-label="Mobile Studio navigation">
            <a href="{{ route('studio.activities.index') }}" @class(['is-active' => request()->routeIs('studio.activities.*')]) wire:navigate><span class="studio-bottom-nav__icon"><x-studio-icon name="activities" /></span><span class="studio-bottom-nav__label">Activities</span></a>
            <a href="{{ route('studio.news.index') }}" @class(['is-active' => request()->routeIs('studio.news.*')]) wire:navigate><span class="studio-bottom-nav__icon"><x-studio-icon name="news" /></span><span class="studio-bottom-nav__label">News</span></a>
            <a href="{{ route('studio.administrators.index') }}" @class(['is-active' => request()->routeIs('studio.administrators.*')]) wire:navigate><span class="studio-bottom-nav__icon"><x-studio-icon name="administrators" /></span><span class="studio-bottom-nav__label">Admins</span></a>
            <a href="{{ route('studio.account') }}" @class(['is-active' => request()->routeIs('studio.account', 'studio.settings')]) wire:navigate><span class="studio-bottom-nav__icon"><x-studio-icon name="account" /></span><span class="studio-bottom-nav__label">Account</span></a>
        </nav>
    </div>
    @livewireScripts
</body>
</html>
