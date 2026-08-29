<div class="form-row">
<div class="form-group col-md-6"><label>{{ __('financing::financing.name') }}</label><input dir="auto" name="name" class="form-control" required value="{{ old('name',$supplier->name ?? '') }}"></div>
<div class="form-group col-md-3"><label>{{ __('financing::financing.phone') }}</label><input dir="auto" name="phone" class="form-control" value="{{ old('phone',$supplier->phone ?? '') }}"></div>
<div class="form-group col-md-3"><label>{{ __('financing::financing.email') }}</label><input dir="auto" type="email" name="email" class="form-control" value="{{ old('email',$supplier->email ?? '') }}"></div>
</div>
<div class="form-group"><label>{{ __('financing::financing.address') }}</label><textarea dir="auto" name="address" class="form-control">{{ old('address',$supplier->address ?? '') }}</textarea></div>
<div class="form-group"><label>{{ __('financing::financing.payment_terms_days') }}</label><input type="number" min="0" max="3650" name="payment_terms_days" class="form-control" value="{{ old('payment_terms_days',$supplier->payment_terms_days ?? 30) }}"></div>
<div class="form-group"><label>{{ __('financing::financing.notes') }}</label><textarea dir="auto" name="notes" class="form-control">{{ old('notes',$supplier->notes ?? '') }}</textarea></div>
<button class="btn btn-primary">{{ __('financing::financing.save') }}</button>
