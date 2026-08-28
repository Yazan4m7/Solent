@extends('layouts.app', ['pageSlug' => $clientTitle . "'s List"])

@section('content')
    <style>
        .dropdown-toggle::after {
            display: inline-block !important;
        }

        .dropdown-menu {
            color: inherit;
        }

        .modal-footer {
            padding: 0 !important;
        }

        @media screen and (max-width: 768px) {
            #my-table {
                table-layout: fixed;
                width: 100% !important;
            }

            #my-table .doctor-table__mobile-hidden {
                display: none;
            }
        }
    </style>

    @php
        $permissions = Cache::get('user' . Auth()->user()->id);
    @endphp
    @php($currencyLabel = (string) ($currencyContext['display'] ?? $currencyContext['code'] ?? 'JOD'))
    @php($clientsUi = trans('ui.dom'))

    <div class="row">
        <div class="col-lg-12 col-sm-12">
            <div class="m-b-30">
                <div class="">
                    <form class="kt-form list-filter-shell" method="GET" action="{{ route('clients-index') }}">
                        <div class="row">
                            <div class="col-12 col-sm-6 col-md-3 my-1">
                                @if (($permissions && $permissions->contains('permission_id', 107)) || Auth()->user()->is_admin)
                                    <label class="solent-filter-label" for="from"><i class="fa-regular fa-calendar" aria-hidden="true"></i><span>To:</span></label>
                                    <input class="form-control SDTP" id="from" name="from" type="text"
                                        value="{{ old('from', $from ?? '') }}" required readonly />
                                @endif
                            </div>

                            <div class="col-12 col-sm-6 col-md-3 my-1">
                                <label class="solent-filter-label" for="active"><i class="fa-solid fa-toggle-on" aria-hidden="true"></i><span>Status:</span></label>
                                <select name="active" id="active" class="form-control" onchange="this.form.submit()">
                                    <option value="1" {{ old('active', $status) == 1 ? 'selected' : '' }}>Enabled</option>
                                    <option value="0" {{ old('active', $status) == 0 ? 'selected' : '' }}>Disabled</option>
                                </select>
                            </div>

                            <div class="col-12 col-sm-6 col-md-3 my-1">
                                <label class="solent-filter-label" for="doctor-filter"><i class="fa-solid fa-user-doctor" aria-hidden="true"></i><span>Doctor:</span></label>
                                <select style="width:100%" class="selectpicker form-control clearOnAll" multiple name="doctor[]"
                                    id="doctor-filter" data-live-search="true" title="All" data-hide-disabled="true">
                                    <option value="all"
                                        {{ isset($selectedClients) && in_array('all', $selectedClients) ? 'selected' : '' }}>
                                        All
                                    </option>
                                    @foreach ($allClients as $d)
                                        <option value="{{ $d->id }}"
                                            {{ isset($selectedClients) && in_array($d->id, $selectedClients) ? 'selected' : '' }}>
                                            {{ $d->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-sm-6 col-md-3 my-1" style="text-align: right">
                                @if (($permissions && $permissions->contains('permission_id', 107)) || Auth()->user()->is_admin)
                                    <a href="{{ route('new-dentist-view') }}">
                                        <button type="button" class="btn btn-secondary">
                                            <i class="fa fa-plus-circle"></i> Add Doctor
                                        </button>
                                    </a>
                                @endif
                            </div>

                            <div class="col-12 col-sm-6 col-md-3 my-1">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>

                    @if (($permissions && $permissions->contains('permission_id', 107)) || Auth()->user()->is_admin)
                        <div class="clients-index-summary-row">
                            <div class="clients-index__total-card">
                                <span class="clients-index__total-card-label">Total balance</span>
                                <strong class="clients-index__total-card-value">{{ number_format($totalBalance) }}</strong>
                                <span class="clients-index__total-card-meta">{{ $currencyLabel }}</span>
                            </div>
                        </div>
                    @endif

                    <p class="text-muted"></p>
                    <div class="list-results-shell">
                        <table class="table-striped table-bordered compact sunriseTable" id="my-table" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="doctor-table__mobile-hidden" style="font-weight: bold">ID</th>
                                    <th style="font-weight: bold">Name</th>
                                    <th style="font-weight: bold">Personal Phone</th>
                                    <th class="doctor-table__mobile-hidden" style="font-weight: bold">Clinic Phone</th>
                                    @if (($permissions && $permissions->contains('permission_id', 107)) || Auth()->user()->is_admin)
                                        <th>Balance</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clients as $client)
                                    <tr id="{{ $client->id }}"
                                        class="odd clickable {{ $client->active ? '' : 'table-secondary' }}"
                                        data-toggle="modal" data-target="#actionsDialog{{ $client->id }}"
                                        style="{{ $client->active ? '' : 'opacity: 0.6;' }}">
                                        <td class="doctor-table__mobile-hidden">
                                            <span class="tabledit-span tabledit-identifier">{{ $client->id }}</span>
                                        </td>
                                        <td class="tabledit-view-mode doctor-table__mobile-hidden">
                                            <span class="tabledit-span">
                                                {{ $client->name }}
                                                @if (!$client->active)
                                                    <span class="badge badge-secondary ml-1">Disabled</span>
                                                @endif
                                            </span>
                                            <input class="tabledit-input form-control input-sm" type="text" name="col1"
                                                value="John" style="display: none;" disabled="">
                                        </td>
                                        <td class="tabledit-view-mode">
                                            <span class="tabledit-span">{{ $client->phone }}</span>
                                            <input class="tabledit-input form-control input-sm" type="text" name="col1"
                                                value="John" style="display: none;" disabled="">
                                        </td>
                                        <td class="tabledit-view-mode">
                                            <span class="tabledit-span">{{ $client->clinic_phone }}</span>
                                            <input class="tabledit-input form-control input-sm" type="text" name="col1"
                                                value="John" style="display: none;" disabled="">
                                        </td>
                                        @if (($permissions && $permissions->contains('permission_id', 107)) || Auth()->user()->is_admin)
                                            <td class="tabledit-view-mode">
                                                <span class="tabledit-span">{{ isset($from) ? $client->balanceAt($from) : $client->balance }}</span>
                                                <input class="tabledit-input form-control input-sm" type="text"
                                                    name="col1" value="Doe" style="display: none;" disabled="">
                                            </td>
                                        @endif
                                    </tr>

                                    @if (($permissions && $permissions->contains('permission_id', 111)) || Auth()->user()->is_admin)
                                        <div class="modal" tabindex="-1" role="dialog" id="myModal{{ $client->id }}">
                                            <form action="{{ route('new-payment') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $client->id }}">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">New Payment balance</h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <h4 style="color:#ff0000"><b>{{ $client->name }}</b></h4>
                                                            <label>Payment amount</label>
                                                            <input type="number" class="form-control" name="amount" required>
                                                            <br />
                                                            <label>Payment type:</label> <br />

                                                            <input type="radio" id="cash{{ $client->id }}"
                                                                onclick="paymentTypeChange({{ $client->id }});"
                                                                name="payment_type" value="cash">
                                                            <label for="cash{{ $client->id }}">دفعة نقدية</label><br>
                                                            <input type="radio" id="cheque{{ $client->id }}"
                                                                onclick="paymentTypeChange({{ $client->id }});"
                                                                name="payment_type" value="cheque">
                                                            <label for="cheque{{ $client->id }}">شيك بنكي</label><br>
                                                            <input type="radio" id="transfer{{ $client->id }}"
                                                                onclick="paymentTypeChange({{ $client->id }});"
                                                                name="payment_type" value="transfer">
                                                            <label for="transfer{{ $client->id }}">حوالة بنكية/ كليك</label><br>
                                                            <br />
                                                            <div id="chequeDetailsInputs{{ $client->id }}"
                                                                style="display:none">
                                                                <label>Bank:</label>

                                                                <div class="kt-form__control">
                                                                    <select class="form-control" id="bank"
                                                                        name="bank_id">
                                                                        @foreach ($banks as $bank)
                                                                            <option value="{{ $bank->id }}">
                                                                                {{ $bank->bank_name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <br />
                                                                <label>Cheque number:</label>
                                                                <input type="text" class="form-control"
                                                                    name="chequeNumber">
                                                                <br />
                                                            </div>
                                                            <label>Extra details (Optional):</label>
                                                            <textarea name="note" class="form-control"></textarea>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary">Save
                                                                changes</button>
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Close
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    @endif

                                    @if (Auth()->user()->is_admin)
                                        <div class="modal" tabindex="-1" role="dialog"
                                            id="accountDiscount{{ $client->id }}">
                                            <form action="{{ route('account-discount') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="id" value="{{ $client->id }}">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Doctor balance</h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <label>Discount amount</label>
                                                            <input type="number" class="form-control"
                                                                name="discountAmount" required>
                                                            <br />
                                                            <label>Date of discount: :</label>
                                                            <input type="datetime-local" name="discount_date"
                                                                class="form-control"></input>
                                                            <br />

                                                            <label>Details ( How it appears on account statement)
                                                                :</label>
                                                            <input type="text" name="discount_title"
                                                                class="form-control"></input>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary">Save
                                                                changes</button>
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">Close
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    @endif

                                    <div class="modal fade solent-record-actions-modal" tabindex="-1" role="dialog"
                                        aria-labelledby="actionsDialogTitle{{ $client->id }}"
                                        id="actionsDialog{{ $client->id }}">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <div>
                                                        <span class="solent-record-actions-modal__eyebrow">{{ $clientsUi['Doctor account'] ?? 'Doctor account' }}</span>
                                                        <h5 class="modal-title" id="actionsDialogTitle{{ $client->id }}">
                                                            {{ $client->name }}
                                                        </h5>
                                                    </div>
                                                    <button type="button" class="close solent-record-actions-modal__close"
                                                        data-dismiss="modal"
                                                        aria-label="{{ $clientsUi['Close'] ?? 'Close' }}">
                                                        {{ $clientsUi['Close'] ?? 'Close' }}
                                                    </button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="solent-record-actions-modal__summary">
                                                        <div class="solent-record-actions-modal__avatar" aria-hidden="true">
                                                            {{ mb_strtoupper(mb_substr(trim($client->name), 0, 1)) }}
                                                        </div>
                                                        <div class="solent-record-actions-modal__identity">
                                                            <span>{{ $clientsUi['Doctor ID'] ?? 'Doctor ID' }} #{{ $client->id }}</span>
                                                            <strong>{{ $client->name }}</strong>
                                                        </div>
                                                        <div class="solent-record-actions-modal__balance">
                                                            <span>{{ $clientsUi['Current balance'] ?? 'Current balance' }}</span>
                                                            <strong>{{ number_format(isset($from) ? $client->balanceAt($from) : $client->balance, 2) }}</strong>
                                                            <small>{{ $currencyLabel }}</small>
                                                        </div>
                                                    </div>

                                                    <div class="solent-record-actions-modal__section">
                                                        <h6>{{ $clientsUi['Account actions'] ?? 'Account actions' }}</h6>
                                                        <div class="solent-record-actions-modal__actions">
                                                            @if (($permissions && $permissions->contains('permission_id', 107)) || Auth()->user()->is_admin)
                                                                <a class="solent-record-action solent-record-action--primary"
                                                                    href="{{ route('client-statement-admin', $client->id) }}">
                                                                    <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                                                    <span>
                                                                        <strong>{{ $clientsUi['Account statement'] ?? 'Account statement' }}</strong>
                                                                        <small>{{ $clientsUi['View balances and transactions'] ?? 'View balances and transactions' }}</small>
                                                                    </span>
                                                                    <i class="fa fa-chevron-right" aria-hidden="true"></i>
                                                                </a>
                                                                <a class="solent-record-action"
                                                                    href="{{ route('client-view-edit', ['id' => $client->id]) }}">
                                                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                                                    <span>
                                                                        <strong>{{ $clientsUi['Edit doctor'] ?? 'Edit doctor' }}</strong>
                                                                        <small>{{ $clientsUi['Update contact and account details'] ?? 'Update contact and account details' }}</small>
                                                                    </span>
                                                                    <i class="fa fa-chevron-right" aria-hidden="true"></i>
                                                                </a>
                                                            @endif

                                                            @if (($permissions && $permissions->contains('permission_id', 111)) || Auth()->user()->is_admin)
                                                                <button type="button"
                                                                    class="solent-record-action solent-record-action--payment"
                                                                    data-dismiss="modal" data-toggle="modal"
                                                                    data-target="#myModal{{ $client->id }}">
                                                                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                                                    <span>
                                                                        <strong>{{ $clientsUi['Add payment'] ?? 'Add payment' }}</strong>
                                                                        <small>{{ $clientsUi['Record a new account payment'] ?? 'Record a new account payment' }}</small>
                                                                    </span>
                                                                    <i class="fa fa-chevron-right" aria-hidden="true"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    @if (Auth()->user()->is_admin)
                                                        <div class="solent-record-actions-modal__section">
                                                            <h6>{{ $clientsUi['Administration'] ?? 'Administration' }}</h6>
                                                            <div class="solent-record-actions-modal__actions">
                                                                <a class="solent-record-action"
                                                                    href="{{ route('dentist-cases', ['id' => $client->id]) }}">
                                                                    <i class="fa fa-briefcase" aria-hidden="true"></i>
                                                                    <span><strong>{{ $clientsUi['Cases'] ?? 'Cases' }}</strong><small>{{ $clientsUi["View the doctor's cases"] ?? "View the doctor's cases" }}</small></span>
                                                                    <i class="fa fa-chevron-right" aria-hidden="true"></i>
                                                                </a>
                                                                <a class="solent-record-action"
                                                                    href="{{ route('dentist-invoices', ['id' => $client->id]) }}">
                                                                    <i class="fa fa-file-text" aria-hidden="true"></i>
                                                                    <span><strong>{{ $clientsUi['Invoices'] ?? 'Invoices' }}</strong><small>{{ $clientsUi['Review issued invoices'] ?? 'Review issued invoices' }}</small></span>
                                                                    <i class="fa fa-chevron-right" aria-hidden="true"></i>
                                                                </a>
                                                                <a class="solent-record-action"
                                                                    href="{{ route('dentist-payments', ['id' => $client->id]) }}">
                                                                    <i class="fa fa-credit-card" aria-hidden="true"></i>
                                                                    <span><strong>{{ $clientsUi['Payments'] ?? 'Payments' }}</strong><small>{{ $clientsUi['Review payment history'] ?? 'Review payment history' }}</small></span>
                                                                    <i class="fa fa-chevron-right" aria-hidden="true"></i>
                                                                </a>
                                                                <button type="button" class="solent-record-action"
                                                                    data-dismiss="modal" data-toggle="modal"
                                                                    data-target="#accountDiscount{{ $client->id }}">
                                                                    <i class="fa fa-tag" aria-hidden="true"></i>
                                                                    <span><strong>{{ $clientsUi['Create discount'] ?? 'Create discount' }}</strong><small>{{ $clientsUi['Apply an account adjustment'] ?? 'Apply an account adjustment' }}</small></span>
                                                                    <i class="fa fa-chevron-right" aria-hidden="true"></i>
                                                                </button>
                                                                <a class="solent-record-action {{ $client->active ? 'solent-record-action--warning' : 'solent-record-action--success' }}"
                                                                    href="{{ route('toggle-client-active', $client->id) }}"
                                                                    onclick="return confirm('{{ $client->active ? ($clientsUi['Are you sure you want to disable this doctor?'] ?? 'Are you sure you want to disable this doctor?') : ($clientsUi['Are you sure you want to enable this doctor?'] ?? 'Are you sure you want to enable this doctor?') }}');">
                                                                    <i class="fa {{ $client->active ? 'fa-ban' : 'fa-check-circle' }}"
                                                                        aria-hidden="true"></i>
                                                                    <span>
                                                                        <strong>{{ $client->active ? ($clientsUi['Disable doctor'] ?? 'Disable doctor') : ($clientsUi['Enable doctor'] ?? 'Enable doctor') }}</strong>
                                                                        <small>{{ $client->active ? ($clientsUi['Prevent new activity'] ?? 'Prevent new activity') : ($clientsUi['Restore account access'] ?? 'Restore account access') }}</small>
                                                                    </span>
                                                                    <i class="fa fa-chevron-right" aria-hidden="true"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                        {{ $clientsUi['Close'] ?? 'Close' }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $('.selectpicker').selectpicker();
            $('.selectpicker').selectpicker('refresh');
        });
    </script>
    <script>
        function paymentTypeChange(id) {
            if (document.getElementById('cheque'.concat(id)).checked) {
                document.getElementById('chequeDetailsInputs'.concat(id)).style.display = 'block';
            } else {
                document.getElementById('chequeDetailsInputs'.concat(id)).style.display = 'none';
            }
        }
    </script>
@endpush
