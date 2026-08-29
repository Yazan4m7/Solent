@extends('financing._layout')

@section('finance-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>{{ __('financing::financing.accounts') }}</h3>
    <a href="{{ route('financing.accounts.create') }}" class="btn btn-primary">{{ __('financing::financing.add_account') }}</a>
</div>

<div class="row">
@foreach($accounts as $account)
    <div class="col-lg-4 col-md-6 mb-3">
        <a class="finance-card finance-card-link" href="{{ route('financing.accounts.show', $account->id) }}">
            <div class="d-flex justify-content-between">
                <strong>{{ $account->name }}</strong>
                <span class="badge badge-{{ $account->is_active ? 'success' : 'secondary' }}">{{ strtoupper($account->type) }}</span>
            </div>
            <div class="finance-card-value mt-3">{{ number_format($account->balance, 2) }} {{ $account->currency }}</div>
        </a>
    </div>
@endforeach
</div>
@endsection
