@extends('layouts.app' ,[ 'pageSlug' => 'Materials Report' ])


@section('content')
    @php($currencyLabel = (string) ($currencyContext['display'] ?? $currencyContext['code'] ?? 'JOD'))

    @include('reports.partials.report-ui')

    <div class="solent-report-page">
    @include('reports.partials.report-header', [
        'title' => 'Materials Usage',
        'description' => 'Review delivered cases, materials used, and invoiced value for the selected period.',
        'icon' => 'fas fa-cubes',
    ])

    <form class="kt-form KorvexPanel report-filters" method="GET" action="{{route('materials-report')}}">
        @include('reports.partials.report-section-heading', [
            'title' => 'Filters',
            'description' => 'Choose the delivery dates and doctors to include.',
            'icon' => 'fas fa-sliders-h',
        ])
        <div class="report-filter-grid">
            <div class="report-filter">
                <div class="kt-subheader__search">
                    <label class="report-filter-label"><i class="far fa-calendar" aria-hidden="true"></i><span>From:</span></label>
                    <input type="date" class="form-control" name="from" value="{{$from}}">
                </div>
            </div>
            <div class="report-filter">
                <div class="kt-subheader__search">
                    <label class="report-filter-label"><i class="far fa-calendar" aria-hidden="true"></i><span>To:</span></label>
                    <input type="date" class="form-control" name="to" value="{{$to}}">
                </div>
            </div>
            <div class="report-filter">
                @if(isset($clients))
                    <div class="dropdown">
                        <label class="report-filter-label"><i class="fas fa-user-md" aria-hidden="true"></i><span>Doctor:</span></label>
                        <select class="selectpicker clearOnAll" multiple name="doctor[]" id="doctor" data-live-search="true" title="All" data-hide-disabled="true">
                            <option value="all" {{(isset($selectedClients) && $selectedClients== 'all') ? 'selected' : ''}}>All</option>
                            @foreach($clients as $d)
                                <option value="{{$d->id}}" {{(isset($selectedClients) && in_array($d->id ,$selectedClients)) ? 'selected' : ''}}>{{$d->name}}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
        </div>
        <div class="report-filter-actions">
            <button type="submit" class="btn btn-primary"><i class="fas fa-filter" aria-hidden="true"></i><span>Apply Filters</span></button>
            <button type="button" class="btn btn-secondary materialsPrintBtn"><i class="fas fa-print" aria-hidden="true"></i><span>Print</span></button>
        </div>
    </form>

    <div class="KorvexPanel report-results" data-report-consolidated="true">
        @include('reports.partials.report-section-heading', [
            'title' => 'Delivered case details',
            'description' => 'Material use and invoice value for every matching delivered case.',
            'icon' => 'fas fa-table',
            'count' => $cases->count(),
            'countLabel' => 'cases',
        ])
        @include('reports.partials.report-range', [
            'reportRangeStart' => $from,
            'reportRangeEnd' => $to,
        ])
        <div class="report-summary-grid">
            <article class="report-summary-card">
                <span class="report-summary-icon" aria-hidden="true"><i class="fas fa-box-open"></i></span>
                <div><span class="report-summary-label">Delivered cases</span><strong class="report-summary-value">{{ number_format($cases->count()) }}</strong></div>
            </article>
            <article class="report-summary-card">
                <span class="report-summary-icon" aria-hidden="true"><i class="fas fa-coins"></i></span>
                <div><span class="report-summary-label">Total amount</span><strong class="report-summary-value">{{ number_format($totalAmount) }} {{ $currencyLabel }}</strong></div>
            </article>
        </div>
        <div class="table-odd report-table-scroll">

                        <table id="datatable" class="dataTable no-footer order-column display nowrap compact cell-border sunriseTable printable report-table materials-usage-table" role="grid" aria-describedby="datatable_info">
                            <thead>
                            <tr role="row">
                                <th class="sorting_asc" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Doctor: activate to sort column descending">Doctor</th>
                                <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Patient: activate to sort column ascending">Patient</th>
                                <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Zircon: activate to sort column ascending">Zircon</th>
                                <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Emax: activate to sort column ascending">Emax</th>
                                <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Acrylic: activate to sort column ascending">Acrylic</th>
                                <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Model: activate to sort column ascending">Model</th>
                                <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Amount: activate to sort column ascending">Amount</th>
                                <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Delivered on: activate to sort column ascending">Delivered On</th>
                            </tr>
                            </thead>


                            <tbody>
                            @foreach($cases as $case)
                                    <tr role="row" class="odd" onclick="window.location='{{route('view-invoice', $case->id)}}';">

                                        <td class="sorting_1">{{$case->client->name}}</td> <!--bold -->
                                        <td>{{$case->patient_name}}</td>
                                        <td>{{$case->materialUsed([1,20])}}</td>
                                        <td>{{$case->materialUsed([2])}}</td>
                                        <td>{{$case->materialUsed([3,4,6,7])}}</td>
                                        <td>{{$case->materialUsed([9,10])}}</td>
                                        <td>{{isset($case->invoice) ? $case->invoice->amount : 0}} </td> <!--JOD -->
                                        <td>{{substr($case->actual_delivery_date,0,10)}} </td>
                                    </tr>
                                    @endforeach
                            </tbody>
                        </table>
        </div>
    </div>
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
        var materialsReportTable = $.fn.DataTable.isDataTable('#datatable')
            ? $('#datatable').DataTable()
            : $('#datatable').DataTable({
            "fixedHeader": true,
            "colReorder": true,
            "responsive": false,
            "sPaginationType": "full_numbers",
            "pagingType": "full_numbers",
            "bLengthChange": false,
            "aLengthMenu": [[5, 10, 15, 20, -1], [5, 10, 15, 20, "All"]],
            "iDisplayLength": 20,
            "order": [[ 7, "desc" ]],
            "dom": "<'report-datatable-toolbar'Bf>rt<'report-datatable-foot'ip>",
            "bProcessing": true,
            buttons: window.solentDataTableButtons ? window.solentDataTableButtons(true) : [],
            language: Object.assign({}, window.SolentI18n?.dataTables || {}, {
                search: '',
                searchPlaceholder: @json(trans('ui.dom')['Search report...'] ?? 'Search report...')
            })
            //{ dom: 'Bfrtip', buttons: ['colvis', 'excel', 'print'] }
            //  "bJQueryUI": true
            // "sDom": 'l<"H"Rf>t<"F"ip>'
        });

        $('.materialsPrintBtn').off('click.solentReports').on('click.solentReports', function () {
            if (materialsReportTable.button('.buttons-print').count()) {
                materialsReportTable.button('.buttons-print').trigger();
                return;
            }

            window.print();
        });
    });
</script>
@endpush
