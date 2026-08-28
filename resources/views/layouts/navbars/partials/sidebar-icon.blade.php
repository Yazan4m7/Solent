<svg class="solent-sidebar-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
    @switch($name)
        @case('home')
            <path d="M3 11.5 12 4l9 7.5"/>
            <path d="M5.5 10v9h13v-9M9.5 19v-5h5v5"/>
            @break
        @case('dashboard')
            <rect x="4" y="4" width="6" height="6" rx="1"/>
            <rect x="14" y="4" width="6" height="6" rx="1"/>
            <rect x="4" y="14" width="6" height="6" rx="1"/>
            <rect x="14" y="14" width="6" height="6" rx="1"/>
            @break
        @case('plus-square')
            <rect x="4" y="4" width="16" height="16" rx="3"/>
            <path d="M12 8v8M8 12h8"/>
            @break
        @case('case')
            <rect x="4" y="6" width="16" height="13" rx="2"/>
            <path d="M9 6V4h6v2M4 11h16M10 11v2h4v-2"/>
            @break
        @case('monitor')
            <rect x="3" y="4" width="18" height="13" rx="2"/>
            <path d="M8 21h8M12 17v4M7 9h3v4H7zM14 7h3v6h-3z"/>
            @break
        @case('clock')
            <circle cx="12" cy="12" r="8"/>
            <path d="M12 8v4l3 2"/>
            @break
        @case('users')
            <circle cx="9" cy="9" r="3"/>
            <circle cx="17" cy="10" r="2"/>
            <path d="M4 19c.5-3 2.2-4.5 5-4.5s4.5 1.5 5 4.5M15 15c2.8 0 4.3 1.3 4.8 3.5"/>
            @break
        @case('database')
            <ellipse cx="12" cy="5" rx="7.5" ry="3"/>
            <path d="M4.5 5v7c0 1.7 3.4 3 7.5 3s7.5-1.3 7.5-3V5M4.5 12v7c0 1.7 3.4 3 7.5 3s7.5-1.3 7.5-3v-7"/>
            @break
        @case('chart')
            <path d="M5 19V9M12 19V5M19 19v-7M3 19h18"/>
            @break
        @case('layers')
            <path d="m12 4 8 4-8 4-8-4 8-4Z"/>
            <path d="m4 12 8 4 8-4M4 16l8 4 8-4"/>
            @break
        @case('flow')
            <circle cx="6" cy="6" r="2"/>
            <circle cx="18" cy="6" r="2"/>
            <circle cx="12" cy="18" r="2"/>
            <path d="M8 6h8M7 8l4 8M17 8l-4 8"/>
            @break
        @case('check')
            <path d="M9 4h6M9 6h6"/>
            <rect x="5" y="5" width="14" height="15" rx="2"/>
            <path d="m8.5 13 2.2 2.2 4.8-5"/>
            @break
        @case('refresh')
            <path d="M19 8a7 7 0 0 0-12-2L5 8M5 4v4h4M5 16a7 7 0 0 0 12 2l2-2M19 20v-4h-4"/>
            @break
        @case('flask')
            <path d="M9 4h6M10 4v5l-5 8.5A1.7 1.7 0 0 0 6.5 20h11a1.7 1.7 0 0 0 1.5-2.5L14 9V4M8 15h8"/>
            @break
        @case('billing')
            <rect x="4" y="5" width="16" height="14" rx="2"/>
            <path d="M4 10h16M8 15h3"/>
            @break
        @case('invoice')
            <path d="M6 3h9l3 3v15H6zM15 3v4h4M9 11h6M9 15h6"/>
            @break
        @case('settings')
            <circle cx="12" cy="12" r="3"/>
            <path d="M19 13.5v-3l-2-.6a6 6 0 0 0-.7-1.6l1-1.8-2-2-1.8 1a6 6 0 0 0-1.6-.7L10.5 3h-3l-.6 2a6 6 0 0 0-1.6.7l-1.8-1-2 2 1 1.8A6 6 0 0 0 1.8 10l-2 .6v3l2 .6a6 6 0 0 0 .7 1.6l-1 1.8 2 2 1.8-1a6 6 0 0 0 1.6.7l.6 2h3l.6-2a6 6 0 0 0 1.6-.7l1.8 1 2-2-1-1.8a6 6 0 0 0 .7-1.6Z" transform="translate(2.25 -.75) scale(.8)"/>
            @break
        @case('boxes')
            <path d="m8 4 4 2-4 2-4-2 4-2ZM16 4l4 2-4 2-4-2 4-2ZM12 12l4 2-4 2-4-2 4-2Z"/>
            <path d="M4 6v4l4 2 4-2V6M12 6v4l4 2 4-2V6M8 14v4l4 2 4-2v-4"/>
            @break
        @case('tooth')
            <path d="M8 4c1.2 0 2.4.8 4 .8S14.8 4 16 4c2.4 0 4 1.8 4 4.5 0 2.4-1.1 4.2-2 6.3-.9 2.2-1.4 5.2-3.2 5.2-1.4 0-1.3-4.8-2.8-4.8S10.6 20 9.2 20C7.4 20 7 17 6 14.8c-.9-2.1-2-3.9-2-6.3C4 5.8 5.6 4 8 4Z"/>
            @break
        @case('tag')
            <path d="M4 5v6l8 8 7-7-8-8H5a1 1 0 0 0-1 1Z"/>
            <circle cx="8" cy="8" r="1"/>
            @break
        @default
            <circle cx="12" cy="12" r="8"/>
            <path d="M12 8v4M12 16h.01"/>
    @endswitch
</svg>
