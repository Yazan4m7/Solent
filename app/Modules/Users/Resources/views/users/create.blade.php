@extends('layouts.app' ,[ 'pageSlug' =>'New User'])

@push('css')
    @include('users._form-styles')
@endpush

@php
    $selectedPermissionIds = array_map('strval', (array) old('permission', []));
    $isAdminChecked = old('form_submitted') ? old('is_admin') !== null : false;
    $statusChecked = old('form_submitted') ? old('status') !== null : true;
@endphp

@section('content')
    <div class="card user-management-card">
        <header class="user-management-card__header">
            <div>
                <h1 class="user-management-card__title">Create User</h1>
                <p class="user-management-card__subtitle">Employees and their details.</p>
            </div>
        </header>

        <form class="kt-form user-management-form" method="POST" action="{{ route('new-user') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="form_submitted" value="1">
            <input type="hidden" name="status_control_present" value="1">

                <div class="kt-portlet__body">
                    @include('alerts.errors')

                    <div class="user-form-grid">
                        <div class="form-group">
                            <label for="first_name">User first name</label>
                            <input type="text" class="form-control user-text-control" id="first_name" name="first_name" placeholder="Enter the first name" value="{{ old('first_name') }}">
                            @if ($errors->has('first_name'))
                                <span class="help-block" style="color: red">{{ $errors->first('first_name') }}</span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="last_name">User last name</label>
                            <input type="text" class="form-control user-text-control" id="last_name" name="last_name" placeholder="Enter the last name" value="{{ old('last_name') }}">
                            @if ($errors->has('last_name'))
                                <span class="help-block" style="color: red">{{ $errors->first('last_name') }}</span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="name_initials">Name initials</label>
                            <input type="text" class="form-control user-text-control" id="name_initials" name="name_initials" placeholder="E.g. : Y. Moh." value="{{ old('name_initials') }}">
                        </div>
                    </div>

                    <div class="user-form-grid mt-3">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control user-text-control" id="username" name="username" placeholder="Enter the username" value="{{ old('username') }}" autocomplete="username">
                            @if ($errors->has('username'))
                                <span class="help-block" style="color: red">{{ $errors->first('username') }}</span>
                            @endif
                        </div>

                        <div class="form-group">
                            <span class="user-field-label d-block">Account Status</span>
                            <label class="user-toggle-panel" for="status">
                                <span class="user-switch">
                                    <input type="checkbox" class="user-switch__input" id="status" name="status" {{ $statusChecked ? 'checked' : '' }}>
                                    <span class="user-switch__track" aria-hidden="true"></span>
                                </span>
                                <span class="user-toggle-panel__text">Active account</span>
                            </label>
                            @if ($errors->has('status'))
                                <span class="help-block" style="color: red">{{ $errors->first('status') }}</span>
                            @endif
                        </div>

                        <div class="form-group">
                            <span class="user-field-label d-block">Admin Privileges</span>
                            <label class="user-toggle-panel" for="is_admin">
                                <span class="user-switch">
                                    <input type="checkbox" class="user-switch__input" id="is_admin" name="is_admin" {{ $isAdminChecked ? 'checked' : '' }}>
                                    <span class="user-switch__track" aria-hidden="true"></span>
                                </span>
                                <span class="user-toggle-panel__text">Grant administrator access</span>
                            </label>
                            @if ($errors->has('is_admin'))
                                <span class="help-block" style="color: red">{{ $errors->first('is_admin') }}</span>
                            @endif
                        </div>
                    </div>

                    <section class="user-form-section" aria-labelledby="create-user-security-title">
                        <h4 class="user-form-section__title" id="create-user-security-title">Security</h4>
                        <div class="user-form-grid user-form-grid--security">
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control user-text-control" id="password" name="password" placeholder="Enter password" autocomplete="new-password">
                                @if ($errors->has('password'))
                                    <span class="help-block" style="color: red">{{ $errors->first('password') }}</span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation">Confirm Password</label>
                                <input type="password" class="form-control user-text-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm password" autocomplete="new-password">
                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block" style="color: red">{{ $errors->first('password_confirmation') }}</span>
                                @endif
                            </div>
                        </div>
                    </section>

                    <section class="user-form-section" id="disable" aria-labelledby="create-user-permissions-title">
                        <h4 class="user-form-section__title" id="create-user-permissions-title">Permissions</h4>
                        @include('users._permissions-box', ['permissionInputPrefix' => 'create-user'])
                    </section>

                    <section class="user-form-section delivery-driver-section" style="display: none;" aria-labelledby="create-user-driver-title">
                        <h4 class="user-form-section__title" id="create-user-driver-title">Delivery Driver</h4>
                        <div class="form-group">
                            <label for="driver-image">Driver Image</label>
                            <div class="alert alert-info">
                                <strong>Note:</strong> This image will be shown in the delivery dialog for assigning cases to drivers.
                                <br>Only users with permission ID 131 will appear as delivery drivers.
                            </div>
                            <div class="input-group mb-3">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="driver-image" name="driver_image" accept=".png,.jpg,.jpeg">
                                    <label class="custom-file-label" for="driver-image">Choose file (PNG, JPG, JPEG)</label>
                                </div>
                            </div>
                            <div class="preview-container mt-3" style="display: none;">
                                <label>Preview:</label>
                                <div class="driver-image-preview" style="max-width: 150px; max-height: 150px; overflow: hidden; border-radius: 50%;">
                                    <img id="driver-image-preview" style="width: 100%; height: auto;" src="" alt="Driver image preview">
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="user-form-section" aria-labelledby="create-user-profile-title">
                        <h4 class="user-form-section__title" id="create-user-profile-title">Profile Image</h4>
                        <x-user-image-picker></x-user-image-picker>
                    </section>
                </div>

            <div class="kt-portlet__foot">
                <div class="kt-form__actions">
                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('js')
    @include('users._access-script')
    <script>
        (function ($) {
            'use strict';

            $(function () {
                const $form = $('.user-management-form');
                const $driverImage = $form.find('#driver-image');
                const $driverPreview = $form.find('#driver-image-preview');
                const $previewContainer = $form.find('.preview-container');
                const defaultFileLabel = $driverImage.next('.custom-file-label').text();

                $driverImage.on('change', function (event) {
                    const file = event.target.files[0];

                    if (!file) {
                        $previewContainer.hide();
                        $(this).next('.custom-file-label').text(defaultFileLabel);
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function (readerEvent) {
                        $driverPreview.attr('src', readerEvent.target.result);
                        $previewContainer.show();
                    };
                    reader.readAsDataURL(file);
                    $(this).next('.custom-file-label').text(file.name);
                });

                $form.on('reset.driverImage', function () {
                    window.setTimeout(function () {
                        $driverImage.next('.custom-file-label').text(defaultFileLabel);
                        $driverPreview.attr('src', '');
                        $previewContainer.hide();
                    }, 0);
                });
            });
        })(jQuery);
    </script>
@endpush
