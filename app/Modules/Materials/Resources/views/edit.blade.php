@extends('layouts.app' ,[ 'pageSlug' => 'Edit Material' ])

@push('css')
    @include('materials::_form-styles')
@endpush

@php
    $currencyLabel = (string) ($currencyContext['display'] ?? $currencyContext['code'] ?? 'JOD');
    $formWasSubmitted = (bool) old('material_form_submitted', false);
    $selectedJobTypeIds = array_map('strval', (array) old('jobTypes', $matJobTypes));
    $countAsUnitChecked = $formWasSubmitted ? old('count_as_unit') !== null : (bool) $material->count_as_unit;
    $designChecked = $formWasSubmitted ? old('design') !== null : (bool) $material->design;
    $finishingChecked = $formWasSubmitted ? old('finishing') !== null : (bool) $material->finish;
    $qualityControlChecked = $formWasSubmitted ? old('qc') !== null : (bool) $material->qc;
    $deliveryChecked = $formWasSubmitted ? old('delivery') !== null : (bool) $material->delivery;
    $defaultManufacturingValue = $material->mill ? '2' : ($material->print_3d ? '3' : '0');
    $manufacturingValue = (string) old('manufacturing', $defaultManufacturingValue);
    $defaultFurnaceValue = $material->sinter_furnace ? '4' : ($material->press_furnace ? '5' : ($material->metal_work ? '9' : '0'));
    $furnaceValue = (string) old('furnace', $defaultFurnaceValue);
@endphp

@section('content')
    <div class="solent-material-shell">
        <form method="POST" class="solent-material-form" action="{{ route('edit-material') }}">
            @csrf
            <input type="hidden" name="mat_id" value="{{ $material->id }}">
            <input type="hidden" name="material_form_submitted" value="1">

            @include('alerts.errors')

            <section class="material-form-section" aria-labelledby="material-information-title">
                <h2 class="material-section-header" id="material-information-title">
                    <i class="fa fa-cube" aria-hidden="true"></i>
                    <span>Material Information</span>
                </h2>

                <div class="material-info-grid">
                    <div class="material-field">
                        <label class="material-field-label" for="mat_name">Material Name</label>
                        <input
                            class="material-input"
                            id="mat_name"
                            type="text"
                            name="mat_name"
                            value="{{ old('mat_name', $material->name) }}"
                            required
                            maxlength="30"
                            placeholder="Enter material name"
                            aria-describedby="material-name-help"
                        >
                        <div class="material-help-text" id="material-name-help">E.g. Zircon, E.max, etc.</div>
                        @error('mat_name')
                            <span class="material-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="material-field">
                        <label class="material-field-label" for="price">Price ({{ $currencyLabel }})</label>
                        <input class="material-input" id="price" type="number" name="price" value="{{ old('price', $material->price) }}" required min="0" step="0.01" placeholder="{{ trans('ui.dom')['Price'] ?? 'Price' }} ({{ $currencyLabel }})" aria-describedby="material-price-help">
                        <div class="material-help-text" id="material-price-help">Price per unit in {{ $currencyLabel }}</div>
                        @error('price')
                            <span class="material-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="material-field">
                        <label class="material-field-label" for="jobTypes">Job Types</label>
                        <select
                            class="select selectpicker"
                            id="jobTypes"
                            name="jobTypes[]"
                            multiple
                            required
                            title="Select Job Types"
                            data-selected-text-format="count > 1"
                            data-count-selected-text="{0} job types selected"
                        >
                            @foreach($jobTypes as $type)
                                <option value="{{ $type->id }}" {{ in_array((string) $type->id, $selectedJobTypeIds, true) ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                        <div class="material-help-text">Select compatible job types</div>
                        @error('jobTypes')
                            <span class="material-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="material-field">
                        <span class="material-field-label">Count as Unit</span>
                        <label class="material-toggle" for="count_as_unit">
                            <input type="checkbox" id="count_as_unit" name="count_as_unit" {{ $countAsUnitChecked ? 'checked' : '' }}>
                            <span class="material-toggle-track" aria-hidden="true"></span>
                        </label>
                        <div class="material-help-text">Enable unit-based counting</div>
                    </div>
                </div>
            </section>

            <section class="material-form-section" aria-labelledby="workflow-configuration-title">
                <h2 class="material-section-header" id="workflow-configuration-title">
                    <i class="fa fa-code-fork" aria-hidden="true"></i>
                    <span>Workflow Configuration</span>
                </h2>

                <div class="material-workflow-stack">
                    <div class="material-field">
                        <span class="material-field-label">Production Stages</span>
                        <div class="material-choice-group">
                            <label class="material-choice" for="design">
                                <span class="material-choice-control material-choice-control--checkbox">
                                    <input type="checkbox" id="design" name="design" value="1" {{ $designChecked ? 'checked' : '' }}>
                                    <span class="material-choice-indicator" aria-hidden="true"></span>
                                </span>
                                <span>Design</span>
                            </label>

                            <label class="material-choice" for="finishing">
                                <span class="material-choice-control material-choice-control--checkbox">
                                    <input type="checkbox" id="finishing" name="finishing" value="6" {{ $finishingChecked ? 'checked' : '' }}>
                                    <span class="material-choice-indicator" aria-hidden="true"></span>
                                </span>
                                <span>Finishing</span>
                            </label>

                            <label class="material-choice" for="qc">
                                <span class="material-choice-control material-choice-control--checkbox">
                                    <input type="checkbox" id="qc" name="qc" value="7" {{ $qualityControlChecked ? 'checked' : '' }}>
                                    <span class="material-choice-indicator" aria-hidden="true"></span>
                                </span>
                                <span>Quality Control</span>
                            </label>

                            <label class="material-choice" for="delivery">
                                <span class="material-choice-control material-choice-control--checkbox">
                                    <input type="checkbox" id="delivery" name="delivery" value="8" {{ $deliveryChecked ? 'checked' : '' }}>
                                    <span class="material-choice-indicator" aria-hidden="true"></span>
                                </span>
                                <span>Delivery</span>
                            </label>
                        </div>
                    </div>

                    <div class="material-method-grid">
                        <div class="material-field">
                            <span class="material-field-label">Manufacturing Method</span>
                            <div class="material-choice-group">
                                @foreach ([0 => 'None', 2 => 'Milling', 3 => '3D Printing'] as $value => $label)
                                    <label class="material-choice" for="manufacturing-{{ $value }}">
                                        <span class="material-choice-control material-choice-control--radio">
                                            <input type="radio" id="manufacturing-{{ $value }}" name="manufacturing" value="{{ $value }}" {{ $manufacturingValue === (string) $value ? 'checked' : '' }} {{ $value === 0 ? 'required' : '' }}>
                                            <span class="material-choice-indicator" aria-hidden="true"></span>
                                        </span>
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="material-field">
                            <span class="material-field-label">Furnace Type</span>
                            <div class="material-choice-group">
                                @foreach ([0 => 'None', 4 => 'Sintering Furnace', 5 => 'Press Furnace', 9 => 'Metal Work'] as $value => $label)
                                    <label class="material-choice" for="furnace-{{ $value }}">
                                        <span class="material-choice-control material-choice-control--radio">
                                            <input type="radio" id="furnace-{{ $value }}" name="furnace" value="{{ $value }}" {{ $furnaceValue === (string) $value ? 'checked' : '' }} {{ $value === 0 ? 'required' : '' }}>
                                            <span class="material-choice-indicator" aria-hidden="true"></span>
                                        </span>
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="material-button-group">
                <button type="submit" class="btn material-action material-action--primary">
                    <i class="fa fa-save" aria-hidden="true"></i>
                    <span>Update Material</span>
                </button>
                <button type="reset" class="btn material-action material-action--secondary">
                    <i class="fa fa-times" aria-hidden="true"></i>
                    <span>Cancel</span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('js')
    @include('materials::_form-script')
@endpush
