@extends('layouts.app' ,[ 'pageSlug' =>'New User'])
@push('css')
    <style>
        .permissions-box {
            background: #ffffff;
            border: 1px solid #dfe3e8;
            border-radius: 6px;
            padding: 12px;
            width: 100%;
            max-height: 320px;
            overflow-y: auto;
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 6px 12px;
        }

        .permissions-box.is-disabled {
            opacity: 0.62;
        }

        .user-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px 16px;
        }

        .user-form-grid .form-group {
            margin-bottom: 0;
        }

        .user-form-span {
            grid-column: 1 / -1;
        }

        .user-admin-toggle {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 4px 0 0;
            cursor: pointer;
        }

        .user-admin-toggle input {
            width: 18px;
            height: 18px;
        }

        .permission-item {
            display: flex;
            align-items: center;
            padding: 6px 8px;
            border-radius: 4px;
            cursor: pointer;
            user-select: none;
        }

        .permission-item:hover {
            background: #f6f8fb;
        }

        .permission-item.is-disabled {
            cursor: not-allowed;
            background: #f8f9fb;
        }

        .permissions-box.is-disabled .permission-item:hover {
            background: #f8f9fb;
        }

        .permission-item:last-child {
            margin-bottom: 0;
        }

        .permission-checkbox {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .permission-icon {
            width: 18px;
            text-align: center;
            margin-right: 8px;
        }

        .permission-icon-off {
            color: #dc3545;
            display: inline-flex;
        }

        .permission-icon-on {
            color: #28a745;
            display: none;
        }

        .permission-checkbox:checked + .permission-icon-off {
            display: none;
        }

        .permission-checkbox:checked + .permission-icon-off + .permission-icon-on {
            display: inline-flex;
        }

        .permission-checkbox:disabled + .permission-icon-off,
        .permission-checkbox:disabled + .permission-icon-off + .permission-icon-on {
            opacity: 0.5;
        }

        .permission-checkbox:disabled ~ .permission-name {
            color: #9aa5b1;
        }

        .permission-item.is-disabled .permission-icon,
        .permission-item.is-disabled .permission-name {
            opacity: 0.6;
        }

        @media (max-width: 1200px) {
            .permissions-box {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 992px) {
            .permissions-box {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 576px) {
            .user-form-grid,
            .permissions-box {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush
@section('content')

    <div class="row card">
        <div class="col-lg-12 col-sm-12">
            <!--begin::Portlet-->
            <div class="kt-portlet">
                <div class="kt-portlet__head">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            Create User
                        </h3>
                    </div>
                </div>

                <!--begin::Form-->
                <form class="kt-form" method="POST" action="{{route('new-user')}}" enctype="multipart/form-data">
                    @csrf
                    <div class="kt-portlet__body">
                        @include('alerts.errors')
                        <div class="user-form-grid">
                        <div class="form-group">
                            <label>User first name</label>
                            <input type="text" class="form-control" name="first_name" placeholder="Enter the first name" value="{{old('first_name')}}">
                            @if ($errors->has('first_name'))
                                <span class="help-block" style="color: red">{{ $errors->first('first_name') }}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label>User last name</label>
                            <input type="text" class="form-control" name="last_name" placeholder="Enter the first name" value="{{old('last_name')}}">
                            @if ($errors->has('last_name'))
                                <span class="help-block" style="color: red">{{ $errors->first('last_name') }}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label>Name initials</label>
                            <input type="text" class="form-control" name="name_initials" placeholder="E.g. : Y. Moh." value="{{old('name_initials')}}">

                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" class="form-control" name="username" placeholder="Enter the username" value="{{old('username')}}">
                            @if ($errors->has('username'))
                                <span class="help-block" style="color: red">{{ $errors->first('username') }}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Password">
                            @if ($errors->has('password'))
                                <span class="help-block" style="color: red">{{ $errors->first('password') }}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Confirm Password</label>
                            <input type="password" class="form-control" name="password_confirmation" placeholder="Password">
                            @if ($errors->has('password_confirmation'))
                                <span class="help-block" style="color: red">{{ $errors->first('password_confirmation') }}</span>
                            @endif
                        </div>
                        <div class="form-group user-form-span">
                            <label class="user-admin-toggle" for="is_admin">
                                <input type="checkbox" id="is_admin" name="is_admin">
                                <span>Administrator access</span>
                            </label>
                        </div>
                        </div>
                        <div class="form-group mt-3" id="disable">
                            <label for="Permission">Permission</label>
                            <div class="permissions-box" id="Permission">
                                @foreach($permissions as $perm)
                                    <label class="permission-item">
                                        <input type="checkbox" class="permission-checkbox" name="permission[]" value="{{$perm->id}}">
                                        <span class="permission-icon permission-icon-off"><i class="fa fa-times"></i></span>
                                        <span class="permission-icon permission-icon-on"><i class="fa fa-check"></i></span>
                                        <span class="permission-name">{{$perm->name}}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group delivery-driver-section" style="display: none;">
                            <label>Driver Image</label>
                            <div class="alert alert-info">
                                <strong>Note:</strong> This image will be shown in the delivery dialog for assigning cases to drivers.
                                <br>Only users with permission ID 131 will appear as delivery drivers.
                            </div>
                            <div class="input-group mb-3">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="driver-image" name="driver_image">
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
                    </div>
                    <div class="kt-portlet__foot">
                        <div class="kt-form__actions">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <button type="reset" class="btn btn-danger">Reset</button>
                        </div>
                    </div>
                </form>

                <!--end::Form-->
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        // Check if permission 131 (delivery driver) is selected
        function checkDeliveryDriverPermission() {
            if ($('#is_admin').is(':checked')) {
                $('.delivery-driver-section').hide();
                return;
            }

            // Check if permission ID 131 is selected
            const hasDeliveryPermission = $('.permission-checkbox[value="131"]').is(':checked');

            if (hasDeliveryPermission) {
                $('.delivery-driver-section').show();
            } else {
                $('.delivery-driver-section').hide();
            }
        }

        function syncPermissionState() {
            const isAdmin = $('#is_admin').is(':checked');
            $('.permission-checkbox').prop('disabled', isAdmin);
            $('#Permission').toggleClass('is-disabled', isAdmin);
            $('#Permission .permission-item').toggleClass('is-disabled', isAdmin);
        }

        // Initialize on page load
        $(document).ready(function() {
            // Set up driver image preview
            $('#driver-image').on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        $('#driver-image-preview').attr('src', event.target.result);
                        $('.preview-container').show();
                    };
                    reader.readAsDataURL(file);

                    // Update file input label with selected filename
                    $(this).next('.custom-file-label').html(file.name);
                }
            });

            // Check delivery driver permission on page load
            syncPermissionState();
            checkDeliveryDriverPermission();

            // Check delivery driver permission when permissions change
            $('.permission-checkbox').on('change', function() {
                checkDeliveryDriverPermission();
            });
        });

        $('#is_admin').on('change', function() {
            syncPermissionState();
            checkDeliveryDriverPermission();
        });

        $('select[name="position"]').on('change', function() {
            var selected = $(this).find('option:selected');
            var extra = selected.data('content');
            if (extra == 'B') {
                $('#TypeB').removeAttr('hidden')
            } else {
                $('#TypeB').prop('hidden', true)
            }
            console.log(extra)
        })
    </script>
@endpush
