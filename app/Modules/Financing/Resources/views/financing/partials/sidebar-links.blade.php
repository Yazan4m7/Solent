@if((string) setting('module_financing', '0') === '1')
<li class="nav-item">
    <a class="nav-link" href="{{ route('financing.dashboard') }}">
        <span>{{ __('financing::financing.financing') }}</span>
        @php
            $financeCollectionAlert = \App\Models\DriverCollection::where('created_at', '<', now()->subDays(3))
                ->where(function ($q) {
                    $q->whereNull('submitted_at')
                      ->orWhereColumn('submitted_amount', '<', 'collected_amount');
                })->count();
        @endphp
        @if($financeCollectionAlert)
            <span class="badge badge-danger">{{ $financeCollectionAlert }}</span>
        @endif
    </a>
</li>
@endif
