@extends('layouts.app' ,[ 'pageSlug' =>'Edit User'])

@push('css')
    @include('users._form-styles')
@endpush

@php
    $formWasSubmitted = (bool) old('form_submitted', false);
    $selectedPermissionIds = $formWasSubmitted
        ? array_map('strval', (array) old('permission', []))
        : $user->permissions->pluck('permission_id')->map(function ($permissionId) {
            return (string) $permissionId;
        })->all();
    $isAdminChecked = $formWasSubmitted ? old('is_admin') !== null : (bool) $user->is_admin;
    $statusChecked = $formWasSubmitted ? old('status') !== null : (bool) $user->status;

    $profileImagePath = null;
    if ($user->has_photo) {
        $tenantProfilePath = app(\App\Support\Tenancy\TenantStorage::class)->path('users/' . $user->id . '/profile_picture.png');
        $legacyProfilePath = 'users/' . $user->id . '/profile_picture.png';
        $profilePath = file_exists(public_path($tenantProfilePath)) ? $tenantProfilePath : $legacyProfilePath;
        $profileImagePath = '/' . $profilePath . '?v=' . time();
    }
@endphp

@section('content')
    <div class="card user-management-card">
        <div class="kt-portlet">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">Edit User</h3>
                </div>
            </div>

            <form class="kt-form user-management-form" method="POST" action="{{ route('edit-user') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $user->id }}">
                <input type="hidden" name="form_submitted" value="1">

                <div class="kt-portlet__body">
                    @include('alerts.errors')

                    <div class="user-form-grid">
                        <div class="form-group">
                            <label for="first_name">User first name</label>
                            <input type="text" class="form-control user-text-control" id="first_name" name="first_name" placeholder="Enter the first name" value="{{ old('first_name', $user->first_name) }}">
                            @if ($errors->has('first_name'))
                                <span class="help-block" style="color: red">{{ $errors->first('first_name') }}</span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="last_name">User last name</label>
                            <input type="text" class="form-control user-text-control" id="last_name" name="last_name" placeholder="Enter the last name" value="{{ old('last_name', $user->last_name) }}">
                            @if ($errors->has('last_name'))
                                <span class="help-block" style="color: red">{{ $errors->first('last_name') }}</span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="name_initials">Name initials</label>
                            <input type="text" class="form-control user-text-control" id="name_initials" name="name_initials" placeholder="E.g. : Y. Moh." value="{{ old('name_initials', $user->name_initials) }}">
                        </div>
                    </div>

                    <div class="user-form-grid mt-3">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control user-text-control" id="username" name="username" placeholder="Enter the username" value="{{ $user->username }}" disabled>
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

                    <section class="user-form-section" aria-labelledby="edit-user-security-title">
                        <h4 class="user-form-section__title" id="edit-user-security-title">Security</h4>
                        <div class="user-form-grid user-form-grid--security">
                            <div class="form-group">
                                <label for="password">New Password</label>
                                <input type="password" class="form-control user-text-control" id="password" name="password" placeholder="Leave blank to keep current" autocomplete="new-password">
                                @if ($errors->has('password'))
                                    <span class="help-block" style="color: red">{{ $errors->first('password') }}</span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation">Confirm Password</label>
                                <input type="password" class="form-control user-text-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm new password" autocomplete="new-password">
                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block" style="color: red">{{ $errors->first('password_confirmation') }}</span>
                                @endif
                            </div>
                        </div>
                    </section>

                    <section class="user-form-section" id="disable" aria-labelledby="edit-user-permissions-title">
                        <h4 class="user-form-section__title" id="edit-user-permissions-title">Permissions</h4>
                        @include('users._permissions-box', ['permissionInputPrefix' => 'edit-user'])
                    </section>

                    <section class="user-form-section" aria-labelledby="edit-user-profile-title">
                        <h4 class="user-form-section__title" id="edit-user-profile-title">Profile Image</h4>
                        <x-user-image-picker current_image="{{ $profileImagePath }}"></x-user-image-picker>
                    </section>
                </div>

                <div class="kt-portlet__foot">
                    <div class="kt-form__actions">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="reset" class="btn btn-danger">Reset</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('js')
    @include('users._access-script')
@endpush
