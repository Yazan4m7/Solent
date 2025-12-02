@extends('layouts.portal')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    {{ __('Dashboard') }}
                    <form action="{{ route('portal.logout') }}" method="POST" style="display: inline-block; float: right;">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">Logout</button>
                    </form>
                </div>

                <div class="card-body">
                    <h4>Welcome, {{ $client->name }}</h4>

                    <div class="card mt-4">
                        <div class="card-header">Account Statement</div>
                        <div class="card-body">
                            <h5>Current Balance: {{ $balance }}</h5>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">Invoices</div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->id }}</td>
                                        <td>{{ $invoice->amount }}</td>
                                        <td>{{ $invoice->date_applied }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header">Payments</div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Payment #</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                    <tr>
                                        <td>{{ $payment->id }}</td>
                                        <td>{{ $payment->amount }}</td>
                                        <td>{{ $payment->created_at }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
