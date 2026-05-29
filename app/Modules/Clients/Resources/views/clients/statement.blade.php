@extends('layouts.app' ,[ 'pageSlug' => 'Statement Of Account' ])


@section('content')
    @php($currencyLabel = (string) ($currencyContext['display'] ?? $currencyContext['code'] ?? 'JOD'))
    <style>
        :root {
            --brand-primary: {{ config('branding.defaults.primary_color') }};
            --brand-secondary: {{ config('branding.defaults.secondary_color') }};
            --brand-accent: {{ config('branding.defaults.accent_color') }};
        }
        body {
            -webkit-print-color-adjust: exact !important;
        }
        hr { display: block; height: 1px;
            background-color:black;
            margin:0; padding: 0; border-color:black;}
        th {  padding: 0;  color:white !important;


        }
         th:nth-child(1){

            /* Safari 3-4, iOS 1-3.2, Android 1.6- */
            -webkit-border-radius: 3px 0px 0px 3px;

            /* Firefox 1-3.6 */
            -moz-border-radius: 3px 0px 0px 3px;

            /* Opera 10.5, IE 9, Safari 5, Chrome, Firefox 4, iOS 4, Android 2.1+ */
            border-radius: 3px 0px 0px 3px;
        }

       th:nth-last-child(1){
            /* Safari 3-4, iOS 1-3.2, Android 1.6- */
            -webkit-border-radius: 0px 3px 3px 0px;

            /* Firefox 1-3.6 */
            -moz-border-radius: 0px 3px 3px 0px;

            /* Opera 10.5, IE 9, Safari 5, Chrome, Firefox 4, iOS 4, Android 2.1+ */
            border-radius: 0px 3px 3px 0px;
        }
        .row:not(.headerRow) {
            width: auto;
            padding: 0;
        }
        .col-md-6 , .col-md-4{
            padding:0px;
        }
        thead{
            background-color: var(--brand-primary);
            border-radius: 20px;
        }

        .brand-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 12px;
        }

        .brand-header img {
            height: 64px;
            width: auto;
        }

        .brand-header .brand-name {
            margin: 0;
            font-weight: 800;
            color: var(--brand-accent, #1b1b1b);
        }

        .brand-header .brand-tagline {
            margin: 0;
            color: #4b5563;
            font-size: 0.95rem;
        }

        .summary-title {
            background-color: var(--brand-primary);
            color: #fff;
            font-weight: 600;
            padding: 6px 8px;
            border-radius: 4px;
        }

        @media print{
            hr { display: block; height: 1px;
                background-color:black;
                margin:0; padding: 0; border-color:black;}
            th {  padding: 0; color:white !important;}
            thead{
                background-color: var(--brand-primary);
            }
            body {
                -webkit-print-color-adjust: exact !important;
            }

            }
</style>
    <div class="brand-header">
        <img src="{{ asset('images/v_logo_eng.png') }}" alt="{{ $brandingName ?? 'Brand' }} logo">
        <div>
            <h4 class="brand-name mb-0">{{ $brandingName ?? 'Al Basma Digital Dental Lab' }}</h4>
            <p class="brand-tagline mb-0">Precision dental restorations</p>
        </div>
    </div>
    <form  class="kt-form" method="GET" action="{{route('client-statement-admin',$client->id)}}">
        <div class="col-lg-12 col-sm-12 card">
            <input type="hidden" name="id" value="{{$client->id}}" >
            <div class="row" style="padding-left: 10px;padding-top: 10px">

                <div class="col-lg-4 col-md-3 col-3 mb-3">
                    <div class="kt-subheader__search" style="">
                        <label>From:</label>
                        <input type="date" class="form-control" name="from" value="{{$from}}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-3 col-3 mb-3">
                    <div class="kt-subheader__search" style="">
                        <label>To:</label>
                        <input type="date" class="form-control" name="to" value="{{$to}}">
                    </div>
                </div>
                <div class="col-lg-4 col-md-3 col-3 mb-3" style="padding-right:0">

                    <div class="kt-subheader__search" style="width:100%">
                        <label>&nbsp; &nbsp; </label>

                        <div class="kt-form__actions">
                            <button type="submit" class="btn btn-primary" style="margin:0;" >Filter</button>
                            <button type="button" class="btn btn-secondary" style="margin:0 !important;" onclick="window.location='{{ route('client-statement-admin',['id' => $client->id, 'allTime' =>1]) }}'">All-time</button>
                            <button type="button" class="btn btn-success unstyled" style="margin:0 !important;" onclick="PrintStatement()"><i class="fa-solid fa-print" style="color:#202020;padding-right: 5px;padding-left: 5px;"></i></button>

                            {{--<a href="{{route('client-statement-admin',['id' => $client->id, 'allTime' =>1])}}" >--}}
                                {{--<button class="btn btn-secondary " style="padding:auto !important;margin:0;" >All time</button>--}}
                            {{--</a>--}}

                            {{--<a onclick="PrintStatement()"  >--}}
                                {{--<button class="btn btn-success unstyled" style="padding:auto;margin:0" >--}}
                                    {{--<i class="fa-solid fa-print" style="color:#202020;padding-right: 5px;padding-left: 5px;"></i></button>--}}
                           {{--</a>--}}
                        </div>
                    </div>

                </div>



            </div>
        </div>

    </form>
    <br>
    <div class="row">
        <div class="col-lg-12 col-sm-12">
            <div class="card m-b-30">

                <div class="card-body">

                    <div class="row">
                        <div class="col-md-8" style="position: relative;">
                            <div style="">  <p>To</p>
                                <h4>Dr. :<b> {{$client->name}}</b></h4></div>

                        </div>
                        @php
                        if ($transactions != null)
                        $invoicesAmount = $transactions->whereNotNull("case_id")->whereNull("discount_title")->sum('amount');
                        $discounts = $transactions->whereNotNull("case_id")->whereNotNull("discount_title")->sum('amount');
                        $amountPaid = $transactions->whereNull("case_id")->sum('amount');
                        $balanceDue = $invoicesAmount - $amountPaid + $openingBalance ?? 0;
                        @endphp
                        <div class="col-md-4">
                            <h4 style="text-align: center;font-weight: bold">Statement of Account</h4>
                            <hr style="margin:0">
                            <div class="row">
                            <div class="col-md-4">{{substr($from,0,10) }}</div>
                                <div class="col-md-4" style="text-align: center">To</div>
                                <div class="col-md-4">{{substr($to,0,10) }}</div>
                            </div>
                            <hr style="margin:0">
                            <h6 class="summary-title">Account Summery </h6>
                            <div class="row"><div class="col-md-6">Opening Balance :


                                {{--{{ $amountDuePreDate  . " - " . $amountPaidPreDate}}--}}



                                </div> <div class="col-md-6"><h5 style="text-align: right" ><b> {{$openingBalance ?? '0'}}  {{ $currencyLabel }}</b></h5></div></div>
                            <div class="row"><div class="col-md-6">Invoices Amount :</div> <div class="col-md-6"><h5 style="text-align: right" ><b> {{$invoicesAmount ?? '0'}} {{ $currencyLabel }}</b></h5></div></div>
                            <div class="row"><div class="col-md-6">Amount Paid :</div> <div class="col-md-6"><h5 style="text-align: right" ><b> {{$amountPaid ?? '0'}} {{ $currencyLabel }}</b></h5></div></div>
                            @if($discounts)
                                <div class="row"><div class="col-md-6">Discounts :</div> <div class="col-md-6"><h5 style="text-align: right" ><b> {{$discounts ?? '0'}} {{ $currencyLabel }}</b></h5></div></div>
                            @endif
                            <hr style="margin:0">
                            <div class="row"><div class="col-md-6">Balance Due :</div> <div class="col-md-6"><h5 style="text-align: right" ><b> {{$balanceDue + $discounts ?? '0'}} {{ $currencyLabel }}</b></h5></div></div>
                        </div>
                    </div>

<br>
                    <div class="">
                        <table class="table table-hover">
                            <thead>
                            <tr style="background-color: var(--brand-primary);">
                                <th scope="col" style="padding:5px;">Date</th>
                                <th scope="col" style="padding:5px;">Transaction</th>
                                <th scope="col" style="padding:5px;">Description</th>
                                <th scope="col" style="padding:5px;">Payment</th>
                                <th scope="col" style="padding:5px;">Amount</th>
                                <th scope="col" style="padding:5px;">Balance</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php

                            $discountExist = false;
                            $balance =$openingBalance ?? 0;
                            @endphp
                            @foreach($transactions as $trans)
                                @php
                                    if(isset($trans->case_id))
                                    $balance +=$trans->amount;
                                    else
                                    $balance -=$trans->amount;
                                @endphp
                            <tr>
                                <td scope="row">{{ substr($trans->created_at,0,10) }}</td>
                                <td>{{isset($trans->case_id) ? (isset($trans->case) ? "Invoice" : "Discount") : "Payment"  }}</td>
                                <td>{{isset($trans->case_id) ? (isset($trans->case) ? ( $trans->rejection_invoice == 1  ? $trans->case->patient_name . " / مرتجع": str_replace('/ تعديل', '', $trans->case->patient_name)) :$trans->discount_title) :$trans->notes }}</td>
                                <td>{{isset($trans->case_id) ? '-' : $trans->amount}}</td>
                                <td>{{isset($trans->case_id) ? $trans->amount : '-' }}
                                @php
                                    if(isset($trans->discount ))
                                     {echo '*';
                                     $discountExist = true;}
                                @endphp

                                </td>
                                <td><b>{{$balance }}</b></td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <hr>
                        <div>
                            <div class="row" style="padding-right: 40px;padding-top: 10px">
                            <div class="col-md-8">
                                @if ($discountExist)
                                * Discount applied
                                @endif
                            </div>
                                <div class="col-md-2"><h5>Balance Due :</h5></div>
                            <div class="col-md-2"><h5 style="text-align: right" ><b> {{$balance}} {{ $currencyLabel }}</b></h5></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
    function PrintStatement() {
    var mywindow = window.open('', 'PRINT', 'height=400,width=600');

    //mywindow.document.write('<html><head><title>' + document.title + '</title>');
        //noinspection JSAnnotator
        mywindow.document.write( `
            <html>
            <head>
            <link href="//cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
<link href="//cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css" rel="stylesheet" type="text/css" />


            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
<title>{{ $brandingName ?? "Al Basma Digital Dental Lab" }}</title>



            <link href="{{asset('assets/css/menu.css')}}" rel="stylesheet" type="text/css">
            <link href="{{asset('assets/css/style.css')}}" rel="stylesheet">
            <style>
            body {
            -webkit-print-color-adjust: exact !important;
            }
            hr { display: block; height: 1px;
            background-color:black;
            margin:0; padding: 0; border-color:black;}
            th {  padding: 0; }
            @media print{
            hr { display: block; height: 1px;
            background-color:black;
            margin:0; padding: 0; border-color:black;}
            th {  padding: 0; }
            thead{
            background-color: {{ config('branding.defaults.primary_color') }};
            }
            body {
            -webkit-print-color-adjust: exact !important;
            }
            }
            </style>
            </head>
            <body>
          <div class="row" >
        <div class="col-lg-12 col-sm-12">
            <div class="card m-b-30">

                <div class="card-body">

                    <div class="row" style="float:left;width:48%; align-items:center;">
                        <img src="{{ asset('images/v_logo_eng.png') }}" alt="{{ $brandingName ?? 'Brand' }} logo" style="height:60px; width:auto; margin-bottom:10px;">
                        <div class="col-md-8" >
            <h1 style="font-weight:bolder;font-size:24px;">{{ $brandingName ?? "Al Basma Digital Dental Lab" }}</h1><p style="margin:0;font-weight:500;color:#4b5563;">Precision dental restorations</p>
            <br><br><br><br><br>
                                <h4>Dr. : {{$client->name}}</h4></div>

                        </div>
            @php
                if ($transactions != null)
                           $invoicesAmount = $transactions->whereNotNull("case_id")->whereNull("discount_title")->sum('amount');
                           $discounts = $transactions->whereNotNull("case_id")->whereNotNull("discount_title")->sum('amount');
                           $amountPaid = $transactions->whereNull("case_id")->sum('amount');
                           $balanceDue = $invoicesAmount - $amountPaid + $openingBalance ?? 0;
            @endphp
            <div class="col-md-4" style="float:right;width:48%;">
                <h5 style="font-weight: bold;">Statement of Account</h5>
                <hr style="margin:0">
                <div class="row">
                <div  style="float:left;width: 33%;">{{substr($from,0,10) }}</div>
                                <div  style="text-align: center;width: 33%;">To</div>
                                <div  style="float:right;width: 33%;">{{substr($to,0,10) }}</div>

            </div>
                            <hr style="margin:0">
                            <h6 style="background-color: {{ config('branding.defaults.primary_color') }};color:white;font-weight: 600;text-align:center !important;">Account Summery </h6>
                            <div class="row">
            <div style="float:left;width: 50%;">Opening Balance :</div>
            <div style="float:right;width: 50%;"><h6 style="text-align: right" ><b> {{$openingBalance ?? '0'}}  {{ $currencyLabel }}</b></h6></div>
            </div>
                            <div class="row"><div style="float:left;width: 50%;">Invoices Amount :</div> <div style="float:right;width:50%;"><h6 style="text-align: right" ><b> {{$invoicesAmount ?? '0'}} {{ $currencyLabel }}</b></h6></div></div>
                            <div class="row"><div style="float:left;width: 50%;">Amount Paid :</div> <div style="float:right;width: 50%;"><h6 style="text-align: right" ><b> {{$amountPaid ?? '0'}} {{ $currencyLabel }}</b></h6></div></div>
                              @if($discounts)
                            <div class="row"><div style="float:left;width: 50%;">Discounts :</div> <div style="float:right;width: 50%;"><h6 style="text-align: right" ><b>  {{$discounts ?? '0'}} {{ $currencyLabel }}</b></h6></div></div>
                            @endif
                            <hr style="margin:0">
                            <div class="row"><div style="float:left;width: 50%;">Balance Due :</div> <div style="float:right;width: 50%;"><h6 style="text-align: right" ><b>{{$balanceDue + $discounts ?? '0'}} {{ $currencyLabel }}</b></h6></div></div>
                        </div>
                    </div>

<br>
            `);
        mywindow.document.write( `
                    <div class="">
                        <table class="table table-hover" style="width:100%">
                            <thead style="background-color: {{ config('branding.defaults.primary_color') }} !important;color:white">
                            <tr style="background-color: {{ config('branding.defaults.primary_color') }} !important;color:white">
                                <th scope="col" style="padding:5px;">Date</th>
                                <th scope="col" style="padding:5px;">Transaction</th>
                                <th scope="col" style="padding:5px;">Description</th>
                                <th scope="col" style="padding:5px;">Payment</th>
                                <th scope="col" style="padding:5px;">Amount</th>
                                <th scope="col" style="padding:5px;">Balance</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $balance =$openingBalance ?? 0;
        @endphp
                @foreach($transactions as $trans)
                @php
                    if(isset($trans->case_id))
                    $balance +=$trans->amount;
                    else
                    $balance -=$trans->amount;
                @endphp
            <tr>
            <td scope="row">{{isset($trans->case_id) ? (isset($trans->case->actual_delivery_date) ? substr($trans->case->actual_delivery_date,0,10) : substr($trans->date_applied,0,10)) :  substr($trans->created_at,0,10) }}</td>
            <td>{{isset($trans->case_id) ? (isset($trans->case) ? "Invoice" : "Discount") : "Payment"  }}</td>
            <td>{{isset($trans->case_id) ? (isset($trans->case) ? $trans->case->patient_name :$trans->discount_title) :$trans->notes }}</td>
            <td>{{isset($trans->case_id) ? '-' : $trans->amount}}{{isset($trans->discount )? '*' : ''}}</td>
            <td>{{isset($trans->case_id) ? $trans->amount : '-' }}</td>
            <th>{{$balance }}</th>
                            </tr>
                            @endforeach
            </tbody>
        </table>
        <hr>
        <div>
            <div class="row" style="padding-right: 40px;margin-top:10px">
            <div class="col-md-8"></div>

            <div style="float:left;width: 50%;">Balance Due :</div>
            <div style="float:right;width: 50%;"><h6 style="text-align: right" ><b> {{$balance}} {{ $currencyLabel }}</b></h6></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous" />
            </body>

            </html>
        `);
            //mywindow.document.close(); // necessary for IE >= 10
           // mywindow.focus(); // necessary for IE >= 10*/
            setTimeout(function(){ mywindow.print(); mywindow.close(); },2000);



    }

    </script>
@endpush
