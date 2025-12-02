@extends('layouts.app')

@section('content')
    <style>
        #case-section.disabled {
            opacity: 0.4;
            pointer-events: none;
        }
    </style>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Create Invoice</div>

                    <div class="card-body">
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="paymentOnlyToggle"> Payment Only (No Case)
                            </label>
                        </div>

                        <form method="GET" action="" id="createInvoiceForm">
                            <div class="form-group">
                                <label for="doctor">Select Doctor</label>
                                <select name="doctor" id="doctor" class="form-control">
                                    <option value="">-- Select Doctor --</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group" id="case-section">
                                <label for="case">Select Case</label>
                                <select name="case" id="case" class="form-control" disabled>
                                    <option value="">-- Select Case --</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary" disabled>Generate Invoice</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script>
        $(document).ready(function () {
            var isPaymentOnly = false;

            // Toggle handler
            $('#paymentOnlyToggle').change(function () {
                isPaymentOnly = $(this).is(':checked');
                var caseSection = $('#case-section');
                var caseSelect = $('#case');
                var submitButton = $('#createInvoiceForm button[type="submit"]');
                var doctorSelect = $('#doctor');

                if (isPaymentOnly) {
                    caseSection.addClass('disabled');
                    caseSelect.html('<option value="">-- Select Case --</option>');

                    // If doctor selected, enable submit for payment invoice
                    if (doctorSelect.val()) {
                        submitButton.prop('disabled', false);
                    }
                } else {
                    caseSection.removeClass('disabled');
                    submitButton.prop('disabled', true);

                    // If doctor selected, reload cases
                    if (doctorSelect.val()) {
                        loadDoctorCases(doctorSelect.val());
                    }
                }
            });

            // Doctor change handler
            $('#doctor').change(function () {
                var doctorId = $(this).val();
                var caseSelect = $('#case');
                var submitButton = $('#createInvoiceForm button[type="submit"]');

                if (isPaymentOnly) {
                    // Payment Only Mode - just enable submit
                    submitButton.prop('disabled', !doctorId);
                } else {
                    // Case Invoice Mode - load cases
                    caseSelect.prop('disabled', true);
                    caseSelect.html('<option value="">-- Select Case --</option>');
                    submitButton.prop('disabled', true);

                    if (doctorId) {
                        loadDoctorCases(doctorId);
                    }
                }
            });

            // Case change handler
            $('#case').change(function () {
                var caseId = $(this).val();
                var submitButton = $('#createInvoiceForm button[type="submit"]');

                if (caseId) {
                    submitButton.prop('disabled', false);
                } else {
                    submitButton.prop('disabled', true);
                }
            });

            // Form submit handler
            $('#createInvoiceForm').submit(function(e) {
                e.preventDefault();

                if (isPaymentOnly) {
                    var doctorId = $('#doctor').val();
                    if (doctorId) {
                        window.location.href = '/clients/payment-invoice/' + doctorId;
                    }
                } else {
                    var caseId = $('#case').val();
                    if (caseId) {
                        window.location.href = '/invoice/' + caseId;
                    }
                }
            });

            // Function to load doctor cases
            function loadDoctorCases(doctorId) {
                var caseSelect = $('#case');
                $.ajax({
                    url: '/api/doctor-cases/' + doctorId,
                    type: 'GET',
                    data: {doctor_id: doctorId},
                    success: function (data) {
                        caseSelect.prop('disabled', false);
                        $.each(data, function (key, value) {
                            caseSelect.append('<option value="' + value.id + '">' + value.patient_name + '</option>');
                        });
                    }
                });
            }
        });
    </script>
@endpush
