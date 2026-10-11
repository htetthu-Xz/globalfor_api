@props(['name'])

<svg {{ $attributes->merge(['class' => 'studio-menu-icon']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('activities')
            <circle cx="12" cy="12" r="3" />
            <path d="M16.25 7.75a6 6 0 0 1 0 8.5M7.75 16.25a6 6 0 0 1 0-8.5M19.1 4.9a10 10 0 0 1 0 14.2M4.9 19.1a10 10 0 0 1 0-14.2" />
            @break
        @case('news')
            <path d="M6 3.75h9l3 3v13.5H6z" />
            <path d="M15 3.75v3h3M9 11h6M9 14.5h6M9 18h4" />
            @break
        @case('administrators')
            <circle cx="9" cy="9" r="3" />
            <circle cx="17" cy="10" r="2.25" />
            <path d="M3.75 19.5c.45-3.35 2.2-5 5.25-5s4.8 1.65 5.25 5M14.25 15.25c2.9-.55 5.15.75 6 3.75" />
            @break
        @case('account')
            <circle cx="12" cy="8.5" r="3.25" />
            <path d="M5.25 20c.45-4.15 2.7-6.25 6.75-6.25s6.3 2.1 6.75 6.25" />
            @break
        @case('settings')
            <circle cx="12" cy="12" r="3" />
            <path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-2.86 2.86-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6 1.7 1.7 0 0 0-.4 1V21H9.55v-.08a1.7 1.7 0 0 0-1.1-1.58 1.7 1.7 0 0 0-1.88.34l-.06.06-2.86-2.86.06-.06A1.7 1.7 0 0 0 4.05 15a1.7 1.7 0 0 0-.6-1 1.7 1.7 0 0 0-1-.4h-.08V9.55h.08a1.7 1.7 0 0 0 1.58-1.1 1.7 1.7 0 0 0-.34-1.88l-.06-.06 2.86-2.86.06.06a1.7 1.7 0 0 0 1.88.34 1.7 1.7 0 0 0 1-.6 1.7 1.7 0 0 0 .4-1v-.08h4.05v.08a1.7 1.7 0 0 0 1.1 1.58 1.7 1.7 0 0 0 1.88-.34l.06-.06 2.86 2.86-.06.06a1.7 1.7 0 0 0-.34 1.88 1.7 1.7 0 0 0 .6 1 1.7 1.7 0 0 0 1 .4h.08v4.05h-.08A1.7 1.7 0 0 0 19.4 15Z" />
            @break
        @case('logout')
            <path d="M14.5 5H6.75A1.75 1.75 0 0 0 5 6.75v10.5A1.75 1.75 0 0 0 6.75 19h7.75M13 8l4 4-4 4M17 12H9" />
            @break
    @endswitch
</svg>
