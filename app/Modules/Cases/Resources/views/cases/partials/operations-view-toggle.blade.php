<nav class="operations-view-toggle" aria-label="Operations dashboard view">
    <a href="{{ route('admin-dashboard-v2', ['view' => 'new']) }}"
       @if($operationsView === 'new') aria-current="page" @endif>New view</a>
    <a href="{{ route('admin-dashboard-v2', ['view' => 'classic']) }}"
       @if($operationsView === 'classic') aria-current="page" @endif>Classic view</a>
</nav>

@once
    <style>
        .operations-view-toggle {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            max-width: 100%;
            padding: 4px;
            margin-block-end: 12px;
            border: 1px solid #d8dce8;
            border-radius: 12px;
            background: #f0f1f7;
        }

        .operations-view-toggle a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 8px 14px;
            border-radius: 8px;
            color: #475569 !important;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.4;
            text-decoration: none;
        }

        .operations-view-toggle a:hover {
            background: #e2e4f1;
        }

        .operations-view-toggle a[aria-current="page"] {
            background: var(--primary, #6366f1);
            color: #ffffff !important;
        }

        .operations-view-toggle a:focus-visible {
            outline: 2px solid #4338ca;
            outline-offset: 2px;
        }
    </style>
@endonce
