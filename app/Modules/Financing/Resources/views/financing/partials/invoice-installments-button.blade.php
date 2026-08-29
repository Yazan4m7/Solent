@if((string) setting('module_financing','0') === '1')
    <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#financeSplitInvoiceModal">
        {{ __('financing::financing.split_into_installments') }}
    </button>

    @include('financing.installments.split-modal', ['invoice' => $invoice])
@endif
