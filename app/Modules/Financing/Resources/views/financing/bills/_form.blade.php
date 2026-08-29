<div class="form-group"><label>{{ __('financing::financing.supplier') }}</label>
<select name="supplier_id" class="form-control" required>
@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}" {{ old('supplier_id',$bill->supplier_id ?? $selectedSupplierId ?? '')==$supplier->id?'selected':'' }}>{{ $supplier->name }}</option>@endforeach
</select></div>
<div class="form-row">
<div class="form-group col-md-6"><label>{{ __('financing::financing.amount') }}</label><input type="number" step="0.01" min="0.01" name="amount" class="form-control" required value="{{ old('amount',$bill->amount ?? '') }}"></div>
<div class="form-group col-md-6"><label>{{ __('financing::financing.due_date') }}</label><input type="date" name="due_date" class="form-control" required value="{{ old('due_date',isset($bill)&&$bill->due_date?$bill->due_date->format('Y-m-d'):now()->addDays(30)->format('Y-m-d')) }}"></div>
</div>
<div class="form-group"><label>{{ __('financing::financing.notes') }}</label><textarea dir="auto" name="notes" class="form-control">{{ old('notes',$bill->notes ?? '') }}</textarea></div>
<button class="btn btn-primary">{{ __('financing::financing.save') }}</button>
