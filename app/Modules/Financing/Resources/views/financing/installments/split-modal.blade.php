{{-- Include this partial from the existing invoice detail view and pass $invoice. --}}
<div class="modal fade" id="financeSplitInvoiceModal" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
<form method="POST" action="{{ route('financing.installments.create', $invoice->id) }}">
    @csrf
    <div class="modal-header">
        <h5 class="modal-title">{{ __('financing::financing.split_into_installments') }} — #{{ $invoice->id }}</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    <div class="modal-body">
        <p>{{ __('financing::financing.invoice_total') }}: <strong>{{ number_format($invoice->amount,2) }} {{ config('modules.financing.currency','JOD') }}</strong></p>
        <div id="finance-installment-rows">
            @for($i=0;$i<2;$i++)
            <div class="form-row finance-installment-row mb-2">
                <div class="col"><input type="number" step="0.01" min="0.01" name="amounts[]" class="form-control" placeholder="{{ __('financing::financing.amount') }}" required></div>
                <div class="col"><input type="date" name="due_dates[]" class="form-control" required></div>
            </div>
            @endfor
        </div>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="financeAddInstallmentRow()">{{ __('financing::financing.add_row') }}</button>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary">{{ __('financing::financing.create_installments') }}</button>
    </div>
</form>
</div>
</div>
</div>
<script>
function financeAddInstallmentRow() {
    var box = document.getElementById('finance-installment-rows');
    var row = document.createElement('div');
    row.className = 'form-row finance-installment-row mb-2';
    row.innerHTML = '<div class="col"><input type="number" step="0.01" min="0.01" name="amounts[]" class="form-control" placeholder="{{ __("financing::financing.amount") }}" required></div><div class="col"><input type="date" name="due_dates[]" class="form-control" required></div>';
    box.appendChild(row);
}
</script>
