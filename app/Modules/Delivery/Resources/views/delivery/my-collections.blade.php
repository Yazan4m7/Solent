@extends('layouts.app' ,[ 'pageSlug' =>'My Collections'])

@section('content')
                     <div class="row" style="padding-left: 10px;padding-top: 10px">
                        <div class="col-lg-12 col-sm-12">
                            <div class="m-b-30">
                                <div class="table-responsive">
                                    <h5 class="header-title">Payments Total</h5>
                                    <h2 style=""><span style="font-weight: bold;color:#a13030">{{number_format($payments->sum('amount'))}}</span> <span style="font-weight: bold;font-size:18px;">JOD</span></h2>
                                    <p class="text-muted"></p>
                                    <div class="table-odd">
                                        <div id="datatable_wrapper" class=""><div class="row"><div class="col-sm-12">
                                                    <table id="datatable" class="table sunriseTable order-column  display nowrap compact cell-border dataTable no-footer" role="grid" aria-describedby="datatable_info">
                                                        <thead>
                                                        <tr role="row"><th class="sorting_asc" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Name: activate to sort column descending" style="width: 50.93px;">ID</th>
                                                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Position: activate to sort column ascending" style="width: 240px;">Doctor</th>
                                                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Office: activate to sort column ascending" style="width: 148.32px;">Amount</th>
                                                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Start date: activate to sort column ascending" style="width: 160.664px;">Paid on</th>
                                                            <th class="sorting" tabindex="0" aria-controls="datatable" rowspan="1" colspan="1" aria-label="Salary: activate to sort column ascending" style="width: 126.035px;">Type</th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>

                                                        @foreach($payments as $payment)
                                                            <tr role="row">
                                                                <td class="sorting_1">{{$payment->id}}</td>
                                                                <td>{{$payment->client->name}}</td>
                                                                <td>{{$payment->amount}} JOD</td>
                                                                <td>{{substr($payment->created_at,0,16) }}</td>
                                                                <td>{{$payment->from_bank ? $payment->notes : "Cash"}}</td>

                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @endsection

@push('js')

                        <!-- Responsive and datatable js -->
                            <script src="//cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $('#datatable').DataTable(
                                        {
                                            "pageLength": 25,
                                            "searching": false,
                                            "lengthChange": false,
                                            "order": [[ 4, "desc" ]],
                                        }
                                    );
                                } );
                            </script>
@endpush
