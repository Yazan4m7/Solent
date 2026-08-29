<div class="form-row">
    <div class="form-group col-md-4">
        <label>{{ __('financing::financing.category') }}</label>
        <select name="category_id" class="form-control" required>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ old('category_id', $expense->category_id ?? '') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-4">
        <label>{{ __('financing::financing.account') }}</label>
        <select name="account_id" class="form-control" required>
            @foreach($accounts as $account)
                <option value="{{ $account->id }}" {{ old('account_id', $expense->account_id ?? '') == $account->id ? 'selected' : '' }}>{{ $account->name }} — {{ number_format($account->balance, 2) }} {{ $account->currency }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group col-md-4">
        <label>{{ __('financing::financing.amount') }}</label>
        <input name="amount" type="number" step="0.01" min="0.01" class="form-control" required value="{{ old('amount', $expense->amount ?? '') }}">
    </div>
</div>

<div class="form-group">
    <label>{{ __('financing::financing.description') }}</label>
    <textarea dir="auto" name="description" class="form-control" rows="3">{{ old('description', $expense->description ?? '') }}</textarea>
</div>

<div class="form-row">
    <div class="form-group col-md-4">
        <label>{{ __('financing::financing.date') }}</label>
        <input name="date" type="date" class="form-control" required value="{{ old('date', isset($expense) && $expense->date ? $expense->date->format('Y-m-d') : now()->format('Y-m-d')) }}">
    </div>
    <div class="form-group col-md-4">
        <label>{{ __('financing::financing.receipt') }}</label>
        <input name="receipt" type="file" class="form-control-file">
    </div>
    <div class="form-group col-md-4">
        <label>{{ __('financing::financing.recurring_day') }}</label>
        <input name="recurring_day" type="number" min="1" max="31" class="form-control" value="{{ old('recurring_day', $expense->recurring_day ?? '') }}">
    </div>
</div>

<div class="form-check mb-3">
    <input type="hidden" name="is_recurring" value="0">
    <input type="checkbox" class="form-check-input" id="is_recurring" name="is_recurring" value="1"
        {{ old('is_recurring', $expense->is_recurring ?? false) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_recurring">{{ __('financing::financing.recurring_expense') }}</label>
</div>

<button class="btn btn-primary">{{ __('financing::financing.save') }}</button>
