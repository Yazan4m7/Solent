@extends('layouts.app' ,[ 'pageSlug' =>'New User'])
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
                        <div class="form-group row">
                            <label for="example-tel-input" class="col-2 col-form-label">User Phone Number</label>
                            <div class="col-10">
                                <input class="form-control" type="tel" name="phone" id="example-tel-input" value="{{old('phone')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>User Email address</label>
                            <input type="email" class="form-control" name="email" aria-describedby="emailHelp" placeholder="Enter email" value="{{old('email')}}">
                            @if ($errors->has('email'))
                                <span class="help-block" style="color: red">{{ $errors->first('email') }}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label for="is_admin">Admin</label>
                            <input type="checkbox" class="form-control" id="is_admin" name="is_admin">
                            @if ($errors->has('is_admin'))
                                <span class="help-block" style="color: red">{{ $errors->first('is_admin') }}</span>
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
                        <div class="form-group" id="disable">
                            <label for="Permission">Permission</label>
                            <select class="form-control selectpicker" id="Permission" multiple name="permission[]">
                                @foreach($permissions as $perm)
                                    <option value="{{$perm->id}}">{{$perm->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Profile Image</label>
                            <x-partiels.user-image-picker></x-partiels.user-image-picker>
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
            const hasDeliveryPermission = $('#Permission option[value="131"]:selected').length > 0;

            if (hasDeliveryPermission) {
                $('.delivery-driver-section').show();
            } else {
                $('.delivery-driver-section').hide();
            }
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
            checkDeliveryDriverPermission();

            // Check delivery driver permission when permissions change
            $('#Permission').on('change', function() {
                checkDeliveryDriverPermission();
            });
        });

        $('#is_admin').on('change', function() {
            if(this.checked){
                $('#Permission').attr('disabled', true);
                $('#disable').css('visibility', 'hidden');
                // Hide delivery driver section if admin
                $('.delivery-driver-section').hide();
            } else {
                $('#Permission').attr('disabled', false);
                $('#disable').css('visibility', 'visible');
                // Recheck permissions
                checkDeliveryDriverPermission();
            }
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
