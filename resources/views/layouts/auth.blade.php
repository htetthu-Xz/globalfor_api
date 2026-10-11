<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <meta name="theme-color" content="#082c37">
    <title>{{ $title ?? 'Sign in' }} · Global F.O.R Studio</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="studio-auth-body">
    <main class="studio-auth-shell">
        <section class="studio-auth-story" aria-label="Global F.O.R Studio introduction">
            <div class="studio-auth-orbit" aria-hidden="true"></div>
            <a href="{{ config('app.url') }}" class="studio-brand studio-brand--light" aria-label="Global F.O.R Studio">
                <span class="studio-brand__mark" aria-hidden="true"><i></i><i></i><i></i></span>
                <span><strong>GLOBAL.F.O.R</strong><small>CONTENT STUDIO</small></span>
            </a>
            <div class="studio-auth-story__copy">
                <p class="studio-kicker studio-kicker--light"><span></span> PUBLISH WITH PURPOSE</p>
                <h1>One calm place for every company update.</h1>
                <p>Create stories, prepare galleries and keep public information current—from phone or desktop.</p>
            </div>
            <div class="studio-auth-story__steps" aria-label="Studio workflow">
                <span>Compose</span><b>→</b><span>Preview</span><b>→</b><span>Publish</span>
            </div>
        </section>

        <section class="studio-auth-panel">
            <div class="studio-auth-panel__inner">
                <a href="{{ config('app.url') }}" class="studio-brand studio-brand--mobile" aria-label="Global F.O.R Studio">
                    <span class="studio-brand__mark" aria-hidden="true"><i></i><i></i><i></i></span>
                    <span><strong>GLOBAL.F.O.R</strong><small>CONTENT STUDIO</small></span>
                </a>
                @yield('content')
            </div>
            <p class="studio-auth-panel__foot">Private administration · Global F.O.R Co., Ltd.</p>
        </section>
    </main>
</body>
</html>
