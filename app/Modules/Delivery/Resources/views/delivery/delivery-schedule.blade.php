@extends('layouts.app' ,[ 'pageSlug' => 'Delivery Schedule' ])

@section('content')
    @php
        $permissions = Cache::get('user' . Auth()->user()->id);
        $now = \Carbon\Carbon::now();
        $rangeFrom = $data['from'] ?? null;
        $rangeTo = $data['to'] ?? null;
        $totalCases = count($cases);
        $overdue = 0;
        $dueToday = 0;
        $dueTomorrow = 0;
        $dueThisWeek = 0;
        $numOfUnits = 0;
        $deliveryUi = trans('ui.dom');
        $isArabic = app()->isLocale('ar');

        foreach ($cases as $case) {
            $numOfUnits += $case->unitsAmount();

            try {
                $deliveryAt = $case->initial_delivery_date
                    ? \Carbon\Carbon::parse(str_replace('T', ' ', $case->initial_delivery_date))
                    : null;
            } catch (\Exception $exception) {
                $deliveryAt = null;
            }

            if (!$deliveryAt) {
                continue;
            }

            if ($deliveryAt->lt($now)) {
                $overdue++;
            }

            if ($deliveryAt->isSameDay($now)) {
                $dueToday++;
            }

            if ($deliveryAt->isSameDay($now->copy()->addDay())) {
                $dueTomorrow++;
            }

            if ($deliveryAt->betweenIncluded($now->copy()->startOfDay(), $now->copy()->addDays(7)->endOfDay())) {
                $dueThisWeek++;
            }
        }
    @endphp

    <div class="delivery-page">
        <section class="delivery-hero">
            <div class="delivery-hero__copy">

                <h1>{{ $deliveryUi['Delivery Schedule'] ?? 'Delivery Schedule' }}</h1>
            </div>

            <form class="kt-form delivery-filter-card" method="GET" action="{{ route('delivery-schedule') }}">
                @csrf
                <div class="delivery-filter-card__grid">
                    <div class="delivery-filter-card__field">
                        <label class="solent-filter-label" for="deliveryFrom"><i class="fa-regular fa-calendar" aria-hidden="true"></i><span>{{ $deliveryUi['From date'] ?? 'From date' }}</span></label>
                        <input id="deliveryFrom" class="form-control SDTP" name="from" type="text"
                            value="{{ $rangeFrom ?? '' }}" required readonly>

                        @if ($errors->has('from'))
                            <span class="help-block">{{ $errors->first('from') }}</span>
                        @endif
                    </div>

                    <div class="delivery-filter-card__field">
                        <label class="solent-filter-label" for="deliveryTo"><i class="fa-regular fa-calendar" aria-hidden="true"></i><span>{{ $deliveryUi['To date'] ?? 'To date' }}</span></label>
                        <input id="deliveryTo" class="form-control SDTP" name="to" type="text"
                            value="{{ $rangeTo ?? '' }}" required readonly>

                        @if ($errors->has('to'))
                            <span class="help-block">{{ $errors->first('to') }}</span>
                        @endif
                    </div>
                </div>

                <div class="delivery-filter-card__actions">
                    <button type="submit" class="btn btn-primary">{{ $deliveryUi['Apply filter'] ?? 'Apply filter' }}</button>
                    <button type="button" onclick="printResult()" class="btn btn-secondary">{{ $deliveryUi['Print schedule'] ?? 'Print schedule' }}</button>
                </div>
            </form>
        </section>

        <section class="delivery-summary-grid delivery-summary-grid--standalone"
            aria-label="{{ $deliveryUi['Delivery summary'] ?? 'Delivery summary' }}">
            <article class="delivery-summary-card delivery-summary-card--primary">
                <span class="delivery-summary-card__label">{{ $deliveryUi['Total deliveries'] ?? 'Total deliveries' }}</span>
                <strong class="delivery-summary-card__value">{{ $totalCases }}</strong>
            </article>
            <article class="delivery-summary-card delivery-summary-card--danger">
                <span class="delivery-summary-card__label">{{ $deliveryUi['Overdue'] ?? 'Overdue' }}</span>
                <strong class="delivery-summary-card__value">{{ $overdue }}</strong>
            </article>
            <article class="delivery-summary-card">
                <span class="delivery-summary-card__label">{{ $deliveryUi['Due today'] ?? 'Due today' }}</span>
                <strong class="delivery-summary-card__value">{{ $dueToday }}</strong>
            </article>
            <article class="delivery-summary-card">
                <span class="delivery-summary-card__label">{{ $deliveryUi['Due tomorrow'] ?? 'Due tomorrow' }}</span>
                <strong class="delivery-summary-card__value">{{ $dueTomorrow }}</strong>
            </article>
            <article class="delivery-summary-card">
                <span class="delivery-summary-card__label">{{ $deliveryUi['Next 7 days'] ?? 'Next 7 days' }}</span>
                <strong class="delivery-summary-card__value">{{ $dueThisWeek }}</strong>
            </article>
            <article class="delivery-summary-card">
                <span class="delivery-summary-card__label">{{ $deliveryUi['Units'] ?? 'Units' }}</span>
                <strong class="delivery-summary-card__value">{{ $numOfUnits }}</strong>
            </article>
        </section>

        <section class="list-results-shell delivery-results-shell">
            <div class="delivery-results-shell__header">
                <span class="delivery-results-shell__range">
                    <bdi dir="ltr">{{ $rangeFrom ? str_replace('T', ' ', $rangeFrom) : ($deliveryUi['Open range'] ?? 'Open range') }}</bdi>
                    <span>→</span>
                    <bdi dir="ltr">{{ $rangeTo ? str_replace('T', ' ', $rangeTo) : ($deliveryUi['Now'] ?? 'Now') }}</bdi>
                </span>
            </div>

            <div class="delivery-table-wrap">
                <table id="datatable" class="table dataTable no-footer sunriseTable delivery-table" role="grid"
                    aria-describedby="datatable_info">
                    <thead>
                        <tr>
                            <th>{{ $deliveryUi['Doctor'] ?? 'Doctor' }}</th>
                            <th>{{ $deliveryUi['Patient'] ?? 'Patient' }}</th>
                            <th>{{ $deliveryUi['Delivery date'] ?? 'Delivery date' }}</th>
                            <th>{{ $deliveryUi['Time'] ?? 'Time' }}</th>
                            <th>{{ $deliveryUi['Units'] ?? 'Units' }}</th>
                            <th>{{ $deliveryUi['Status'] ?? 'Status' }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($cases as $case)
                            @php
                                $status = $case->status();

                                try {
                                    $deliveryAt = $case->initial_delivery_date
                                        ? \Carbon\Carbon::parse(str_replace('T', ' ', $case->initial_delivery_date))
                                        : null;
                                } catch (\Exception $exception) {
                                    $deliveryAt = null;
                                }

                                $isOverdue = $deliveryAt ? $deliveryAt->lt($now) : false;
                                $isToday = $deliveryAt ? $deliveryAt->isSameDay($now) : false;
                                $deliveryDate = $deliveryAt ? $deliveryAt->format('Y-m-d') : '-';
                                $deliveryTime = $deliveryAt ? $deliveryAt->format('g:i') : '-';
                                $deliveryPeriod = $deliveryAt
                                    ? ($isArabic
                                        ? ($deliveryAt->format('a') === 'am' ? 'ص' : 'م')
                                        : $deliveryAt->format('a'))
                                    : null;
                                $rowState = $isOverdue ? 'is-overdue' : ($isToday ? 'is-today' : '');
                                $statusClass = 'delivery-status--warning';

                                if (str_contains($status, 'Completed')) {
                                    $statusClass = 'delivery-status--success';
                                } elseif (str_contains($status, 'Active') || str_contains($status, 'In-Progress')) {
                                    $statusClass = 'delivery-status--active';
                                } elseif (str_contains($status, 'Waiting')) {
                                    $statusClass = 'delivery-status--danger';
                                }

                                $displayStatus = str_contains($status, 'In-Progress') ? 'Active' : $status;
                            @endphp

                            <tr data-row="{{ $case->id }}" class="delivery-row clickable {{ $rowState }}"
                                data-toggle="modal" data-target="#caseActionsModal{{ $case->id }}">
                                <td>
                                    <span class="delivery-table__primary">{{ $case->client->name ?? ($deliveryUi['Unknown doctor'] ?? 'Unknown doctor') }}</span>
                                    <span class="delivery-table__meta">{{ $deliveryUi['Doctor'] ?? 'Doctor' }}</span>
                                </td>
                                <td>
                                    <span class="delivery-table__primary">{{ $case->patient_name }}</span>
                                    <span class="delivery-table__meta">{{ $deliveryUi['Patient'] ?? 'Patient' }}</span>
                                </td>
                                <td data-order="{{ $deliveryAt ? $deliveryAt->timestamp : 0 }}">
                                    <span class="delivery-date-pill {{ $isOverdue ? 'delivery-date-pill--late' : '' }}">
                                        {{ $deliveryDate }}
                                    </span>
                                </td>
                                <td data-order="{{ $deliveryAt ? $deliveryAt->format('H:i:s') : '' }}">
                                    <span class="delivery-time" dir="ltr">
                                        <span>{{ $deliveryTime }}</span>
                                        @if ($deliveryPeriod)
                                            <span class="delivery-time__period">{{ $deliveryPeriod }}</span>
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <span class="delivery-units-pill">{{ $case->unitsAmount(-2) }}</span>
                                </td>
                                <td>
                                    <span class="delivery-status solent-case-status-badge {{ $statusClass }}">
                                        {{ $deliveryUi[$displayStatus] ?? $displayStatus }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    @if (($permissions && $permissions->contains('permission_id', 110)) || Auth()->user()->is_admin)
        @foreach ($cases as $case)
            <div class="modal delivery-date-modal" tabindex="-1" role="dialog" id="myModal{{ $case->id }}">
                <form action="{{ route('edit-delivery-date') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $case->id }}">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <div>
                                    <span class="delivery-modal-eyebrow">{{ $deliveryUi['Delivery date'] ?? 'Delivery date' }}</span>
                                    <h5 class="modal-title">{{ $case->client->name ?? ($deliveryUi['Unknown doctor'] ?? 'Unknown doctor') }} · {{ $case->patient_name }}</h5>
                                </div>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ $deliveryUi['Close'] ?? 'Close' }}</button>
                            </div>
                            <div class="modal-body">
                                @php($time = str_replace(' ', 'T', $case->initial_delivery_date))
                                <label for="deliveryDate{{ $case->id }}">{{ $deliveryUi['Change delivery date'] ?? 'Change delivery date' }}</label>
                                <input id="deliveryDate{{ $case->id }}" class="form-control SDTP" name="delivery_date"
                                    type="text" value="{{ $time }}" required readonly>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">{{ $deliveryUi['Save changes'] ?? 'Save changes' }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        @endforeach
    @endif

    @foreach ($cases as $case)
        <x-partiels.caseActionsModal
            :case="$case"
            :modalId="'caseActionsModal' . $case->id"
            :allowDeliveryDateChange="true"
            :deliveryDateModalId="'myModal' . $case->id" />
    @endforeach
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#datatable')) {
                return;
            }

            const deliveryTableLanguage = @json(trans('ui.datatables'));
            deliveryTableLanguage.search = '';
            deliveryTableLanguage.searchPlaceholder = @json($deliveryUi['Search deliveries...'] ?? 'Search deliveries...');

            $('#datatable').DataTable({
                "order": [[2, "asc"], [3, "asc"]],
                "buttons": window.solentDataTableButtons ? window.solentDataTableButtons(true) : [],
                "paging": true,
                "pageLength": 25,
                "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
                "searching": true,
                "lengthChange": true,
                "responsive": true,
                "autoWidth": false,
                "dom": "<'solent-datatable-toolbar'Bfl>rt<'solent-datatable-foot'ip>",
                "columnDefs": [
                    { "width": "18%", "targets": 5 }
                ],
                "language": deliveryTableLanguage
            });
        });

        function printResult() {
            if ($.fn.DataTable.isDataTable('#datatable')) {
                const deliveryTable = $('#datatable').DataTable();
                const printButton = deliveryTable.button('.buttons-print');

                if (printButton.any()) {
                    printButton.trigger();
                    return true;
                }
            }

            window.print();
            return false;
        }
    </script>
@endpush
