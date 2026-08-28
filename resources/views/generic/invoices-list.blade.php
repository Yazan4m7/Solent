@extends('layouts.app' ,[ 'pageSlug' => 'Invoices List' ])


@section('content')
    @php($currencyLabel = (string) ($currencyContext['display'] ?? $currencyContext['code'] ?? 'JOD'))
    <head>

    </head>
<style>
    @media screen and (max-width: 991px){
        #datatable_wrapper {
            overflow: auto;
        }
    }
    .invoice-page .card-body{
        padding: 0;
    }
    .invoice-page .row, .invoice-page .container-fluid{
        padding-left:0px;
        padding-right:0px;
    }
    /*.col-sm-12 {*/
        /*padding-right:0px;*/
        /*padding-left:0px;*/
    /*}*/
    .invoice-page tr { cursor: pointer; }
    .invoice-page td {border : 0 !important;}

    .invoice-page {
        --invoice-accent: #6366f1;
        --invoice-border: #dfe4ec;
        --invoice-muted: #64748b;
        --invoice-text: #0f172a;
        display: grid;
        width: calc(100% - 24px);
        max-width: 1500px;
        gap: 18px;
        margin: 20px auto 40px;
    }

    .invoice-panel {
        min-width: 0;
        padding: 20px;
        background: var(--color-card, #ffffff) !important;
        border: 1px solid var(--invoice-border);
        border-inline-start: 4px solid var(--invoice-accent);
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    }

    .invoice-filter-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        align-items: end;
        gap: 14px;
    }

    .invoice-filter-field {
        min-width: 0;
    }

    .invoice-filter-field .form-control,
    .invoice-filter-field .bootstrap-select,
    .invoice-filter-field .bootstrap-select > .dropdown-toggle {
        width: 100% !important;
        min-height: 44px;
        margin: 0;
        border-radius: 10px;
    }

    .invoice-filter-actions .btn {
        width: 100%;
        min-height: 44px;
        margin: 0;
        border-radius: 10px;
        font-weight: 800;
    }

    .invoice-results-summary {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 12px;
        margin-block-end: 16px;
        padding-block-end: 14px;
        border-block-end: 1px solid var(--invoice-border);
    }

    .invoice-results-summary .header-title {
        margin: 0;
        color: var(--invoice-muted);
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .invoice-results-summary h2 {
        margin: 0;
        color: var(--invoice-text);
        font-size: 28px;
        font-weight: 800;
    }

    .invoice-results-summary h2 span:first-child {
        color: var(--invoice-text) !important;
    }

    @media (max-width: 991.98px) {
        .invoice-filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 575.98px) {
        .invoice-page {
            width: calc(100% - 16px);
            gap: 12px;
            margin-block: 12px 28px;
        }

        .invoice-panel {
            padding: 14px;
            border-radius: 14px;
        }

        .invoice-filter-grid {
            grid-template-columns: 1fr;
        }

        .invoice-results-summary {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>
<div class="invoice-page">
    <section class="invoice-panel invoice-panel--filters" aria-label="Invoice filters">
    @if(isset($clients))
        <form class="kt-form invoice-filter-form" method="GET" action="{{route('invoices-index')}}">
    @else
        <form class="kt-form invoice-filter-form" method="GET" action="{{route('dentist-invoices',['id' =>$id])}}">
            <input type="hidden" class="form-control" name="id" value="{{$id}}">
    @endif
            <div class="invoice-filter-grid">
                <div class="invoice-filter-field">
                    <div class="kt-subheader__search">
                    <label class="solent-filter-label"><i class="fa-regular fa-calendar" aria-hidden="true"></i><span>From:</span></label>
                    <input type="date" class="form-control" name="from" value="{{$from}}">
                    </div>
                </div>
                <div class="invoice-filter-field">
                    <div class="kt-subheader__search">
                    <label class="solent-filter-label"><i class="fa-regular fa-calendar" aria-hidden="true"></i><span>To:</span></label>
                    <input type="date" class="form-control" name="to" value="{{$to}}">
                    </div>
                </div>
                @if(isset($clients))
                <div class="invoice-filter-field">
                    <div class="dropdown">
                        <label class="solent-filter-label"><i class="fa-solid fa-user-doctor" aria-hidden="true"></i><span>Doctor:</span></label>
                        <select style="width:100%"  class="selectpicker clearOnAll" multiple name="doctor[]" id="doctor" data-live-search="true" title="All" data-hide-disabled="true">

                            <option value="all" {{(isset($selectedClients) && $selectedClients== 'all') ? 'selected' : ''}}>All</option>
                            @foreach($clients as $d)
                                <option value="{{$d->id}}" {{(isset($selectedClients) && in_array($d->id ,$selectedClients)) ? 'selected' : ''}}>{{$d->name}}</option>
                            @endforeach

                        </select>
                    </div>
                </div>
                @endif
                <div class="invoice-filter-field invoice-filter-actions">
                    <div class="kt-form__actions">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </section>

    <section class="invoice-panel invoice-panel--results" aria-label="Invoice results">
            <div class="invoice-results-summary">
                <h5 class="header-title">Total Amount:</h5>
                <h2 style=""><span style="font-weight: bold;color:#a13030">{{number_format($invoices->sum('amount'))}}</span> <span style="font-weight: bold;font-size:18px;">{{ $currencyLabel }}</span></h2>
            </div>
            <div class="card-body table-responsive">
                <div class="table-odd">
                    <div id="datatable_wrapper" class="dataTables_wrapper container-fluid dt-bootstrap4 no-footer"><div class="row"><div class="col-sm-12" style="padding:5px">

                                <table id="datatable" class="dataTable no-footer  order-column  display nowrap compact cell-border sunriseTable" role="grid" aria-describedby="datatable_info">
                                    <thead>
                                    <tr role="row">
                                        <th class="sorting_asc" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Name: activate to sort column descending" style="width: 50.93px;">ID</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 240px;">Doctor</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending" style="width: 148.32px;">Patient name</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Age: activate to sort column ascending" style="width: 83.1445px;">Amount</th>
                                        <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Start date: activate to sort column ascending" style="width: 160.664px;">Delivered on</th>

                      </tr>
                                    </thead>


                                    <tbody>
                                    @foreach($invoices as $invoice)
                                        @if(isset($invoice->case))
                                        <tr role="row" class="odd" onclick="window.location='{{route('view-invoice', $invoice->case->id)}}';">
                                            @else
                                            <tr role="row" class="odd" onclick="alert('This case has no invoice.');">
                                            @endif
                                            <td class="sorting_1">{{$invoice->id}}</td>
                                            <td>{{$invoice->client->name}}</td>
                                            <td>{{isset($invoice->case) ? $invoice->case->patient_name :$invoice->discount_title }}</td>
                                            <td>{{$invoice->amount}} {{ $currencyLabel }}</td>
                                            @if (isset($invoice->case) && isset($invoice->case->actual_delivery_date) )
                                            <td> {{$invoice->case->actualDeliveryDate()}}&nbsp;&nbsp;&nbsp;&nbsp;{{$invoice->case->actualDeliveryTime()}}</td>
                                            @else
                                            <td>-</td>
                                            @endif

                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table></div></div></div>
                </div>
            </div>
    </section>
</div>





    @endsection




@push('js')

    <script type="text/javascript">
//        $(document).ready(function() {
//            $('#datatable').DataTable({
//                dom: 'Bfrtip',
//                buttons: [ 'csv', 'excel', 'pdf', 'print' ],
//                "pageLength": 25,
//                "searching": false,
//                "lengthChange": false,
//                "order": [[ 4, "desc" ]]
//            });
//        });
$(document).ready(function() {

        $('#datatable').dataTable({
            "fixedHeader": true,
            "colReorder": true,
            "responsive": true,
            "sPaginationType": "full_numbers",
            "bLengthChange": true,
            "aLengthMenu": [[5, 10, 15, 20, -1], [5, 10, 15, 20, "All"]],
            "iDisplayLength": 20,
            "order": [[ 4, "desc" ]],
            "dom": 'Bfrtip',
            "bProcessing": true,
            buttons: [
                {extend: 'excel',text: 'Export Excel'}

            ]
            //{ dom: 'Bfrtip', buttons: ['colvis', 'excel', 'print'] }
            //  "bJQueryUI": true
            // "sDom": 'l<"H"Rf>t<"F"ip>'
        });
});
    </script>
    @endpush
