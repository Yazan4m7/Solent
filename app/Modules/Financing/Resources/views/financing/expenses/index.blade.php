@extends('financing._layout')

@section('finance-content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h3 class="mb-1">{{ __('financing::financing.expenses') }}</h3>
        <small class="text-muted">{{ __('financing::financing.current_month_total') }}: {{ number_format($monthTotal, 2) }} {{ config('modules.financing.currency', 'JOD') }}</small>
    </div>
    <a href="{{ route('financing.expenses.create') }}" class="btn btn-primary">{{ __('financing::financing.add_expense') }}</a>
</div>

<form method="GET" class="finance-filter mb-3">
    <select name="category_id" class="form-control">
        <option value="">{{ __('financing::financing.all_categories') }}</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
        @endforeach
    </select>
    <select name="account_id" class="form-control">
        <option value="">{{ __('financing::financing.all_accounts') }}</option>
        @foreach($accounts as $account)
            <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
        @endforeach
    </select>
    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
    <button class="btn btn-outline-primary">{{ __('financing::financing.filter') }}</button>
</form>

<div class="finance-table-wrap">
<table class="table table-sm table-hover">
<thead><tr>
    <th>{{ __('financing::financing.date') }}</th>
    <th>{{ __('financing::financing.category') }}</th>
    <th>{{ __('financing::financing.account') }}</th>
    <th>{{ __('financing::financing.description') }}</th>
    <th>{{ __('financing::financing.amount') }}</th>
    <th></th>
</tr></thead>
<tbody>
@forelse($expenses as $expense)
<tr>
    <td>{{ $expense->date->format('d/m/Y') }}</td>
    <td>{{ optional($expense->category)->name }}</td>
    <td>{{ optional($expense->account)->name }}</td>
    <td>
        {{ $expense->description }}
        @if($expense->is_recurring)<span class="badge badge-info">{{ __('financing::financing.recurring') }}</span>@endif
        @if($expense->receipt_path)<a class="ml-1" href="{{ route('financing.expenses.receipt',$expense->id) }}">{{ __('financing::financing.receipt') }}</a>@endif
    </td>
    <td>{{ number_format($expense->amount, 2) }} {{ optional($expense->account)->currency ?: config('modules.financing.currency', 'JOD') }}</td>
    <td><a href="{{ route('financing.expenses.edit', $expense->id) }}">{{ __('financing::financing.edit') }}</a></td>
</tr>
@empty
<tr><td colspan="6" class="text-center text-muted">{{ __('financing::financing.no_records') }}</td></tr>
@endforelse
</tbody>
</table>
</div>
{{ $expenses->links() }}
@endsection
