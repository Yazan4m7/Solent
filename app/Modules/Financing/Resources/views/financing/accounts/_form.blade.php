<div class="form-group">
    <label>{{ __('financing::financing.name') }}</label>
    <input dir="auto" name="name" class="form-control" required value="{{ old('name', $account->name ?? '') }}">
</div>

<div class="form-row">
    <div class="form-group col-md-4">
        <label>{{ __('financing::financing.type') }}</label>
        <select name="type" class="form-control" required>
            <option value="cash" {{ old('type', $account->type ?? '') === 'cash' ? 'selected' : '' }}>{{ __('financing::financing.cash') }}</option>
            <option value="bank" {{ old('type', $account->type ?? '') === 'bank' ? 'selected' : '' }}>{{ __('financing::financing.bank') }}</option>
        </select>
    </div>
    <div class="form-group col-md-4">
        <label>{{ __('financing::financing.currency') }}</label>
        <input name="currency" class="form-control" maxlength="10" required value="{{ old('currency', $account->currency ?? config('modules.financing.currency', 'JOD')) }}">
    </div>
    @if(!isset($account))
    <div class="form-group col-md-4">
        <label>{{ __('financing::financing.opening_balance') }}</label>
        <input name="opening_balance" type="number" step="0.01" min="0" class="form-control" value="{{ old('opening_balance', 0) }}">
    </div>
    @endif
</div>

<div class="form-check mb-3">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" class="form-check-input" name="is_active" value="1" id="is_active"
        {{ old('is_active', isset($account) ? $account->is_active : true) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active">{{ __('financing::financing.active') }}</label>
</div>

<button class="btn btn-primary">{{ __('financing::financing.save') }}</button>
