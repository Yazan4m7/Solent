@extends('layouts.app' ,[ 'pageSlug' =>'Edit User'])
@section('content')
	<!--begin::Portlet-->
    <div class="card" style="padding:15px">
            <div class="kt-portlet__head">
                <div class="kt-portlet__head-label">
                    <h3 class="kt-portlet__head-title">
                        Edit User
                    </h3>
                </div>
            </div>

            <!--begin::Form-->
            <form class="kt-form" method="POST" action="{{route('edit-user')}}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{$user->id}}">
                <div class="kt-portlet__body">
                    <div class="form-group">
                        <label>User first name</label>
                        <input type="text" class="form-control" name="first_name" placeholder="Enter the first name" value="{{$user->first_name}}">
                        @if ($errors->has('first_name'))
                        <span class="help-block" style="color: red">{{ $errors->first('first_name') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                            <label>User last name</label>
                            <input type="text" class="form-control" name="last_name" placeholder="Enter the first name" value="{{$user->last_name}}">
                            @if ($errors->has('last_name'))
                            <span class="help-block" style="color: red">{{ $errors->first('last_name') }}</span>
                            @endif
                        </div>

                    <div class="form-group">
                        <label>Name initials</label>
                        <input type="text" class="form-control" name="name_initials" placeholder="E.g. : Y. Moh."  value="{{$user->name_initials}}">

                    </div>
                        <div class="form-group">
                                <label>Username</label>
                                <input type="text" class="form-control" name="username" placeholder="Enter the username" value="{{$user->username}}" disabled>
                                @if ($errors->has('username'))
                                <span class="help-block" style="color: red">{{ $errors->first('username') }}</span>
                                @endif
                        </div>
                        <div class="form-group row">
                                <label for="example-tel-input" class="col-2 col-form-label">Phone</label>
                                <div class="col-10">
                                    <input class="form-control" type="tel" name="phone" id="example-tel-input" value="{{$user->phone}}">
                                </div>
                            </div>
                    <div class="form-group">
                        <label>Email address</label>
                        <input type="email" class="form-control" name="email" aria-describedby="emailHelp" placeholder="Enter email" value="{{$user->email}}">
                        @if ($errors->has('email'))
                        <span class="help-block" style="color: red">{{ $errors->first('email') }}</span>
                        @endif
                    </div>
                    <div class="form-group">
                            <label for="is_admin" style="color:red">Admin</label>
                            <input type="checkbox" class="form-control" id="is_admin" name="is_admin"  style="width:20px" {{$user->is_admin ? 'checked' : ''}}>
                            @if ($errors->has('is_admin'))
                            <span class="help-block" style="color: red">{{ $errors->first('is_admin') }}</span>
                            @endif
                        </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">New Password</label>
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
                    <div class="form-group">
                            <label for="status">Status</label>
                            <input type="checkbox" class="form-control" id="status" name="status"  style="width:20px" {{$user->status ? 'checked' : ''}}>
                            @if ($errors->has('status'))
                            <span class="help-block" style="color: red">{{ $errors->first('status') }}</span>
                            @endif
                    </div>
                    <div class="form-group" id="disable">
                            <label for="Permission">Permission</label>
                            <select class="form-control" id="Permission" multiple name="permission[]" style="height: 250px;">
                                    @foreach($permissions as $perm)
                                    <option value="{{$perm->id}}" {{$user->permissions->contains('permission_id', $perm->id) ? 'selected' : ''}}>{{$perm->name}}</option>
                                    @endforeach
                                </select>
                        </div>

                        <div class="form-group">
                            <label>Profile Image</label>
                            @php
                                $profileImagePath = null;
                                if ($user->has_photo) {
                                    $tenantProfilePath = app(\App\Support\Tenancy\TenantStorage::class)->path('users/' . $user->id . '/profile_picture.png');
                                    $legacyProfilePath = 'users/' . $user->id . '/profile_picture.png';
                                    $profilePath = file_exists(public_path($tenantProfilePath)) ? $tenantProfilePath : $legacyProfilePath;
                                    $profileImagePath = '/' . $profilePath . '?v=' . time();
                                }
                            @endphp
                            <x-user-image-picker current_image="{{ $profileImagePath }}"></x-user-image-picker>
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

@stop
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
                    $('#new-preview').show();
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

    $("select").mousedown(function(e){
        e.preventDefault();

        var select = this;
        var scroll = select.scrollTop;

        e.target.selected = !e.target.selected;

        setTimeout(function(){select.scrollTop = scroll;}, 0);

        $(select).focus();
    }).mousemove(function(e){e.preventDefault()});

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
            $('#TypeC').prop('hidden', true);
            $('#TypeB').removeAttr('hidden')
        } else {
            $('#TypeB').prop('hidden', true);
            $('#TypeC').removeAttr('hidden')
        }
        console.log(extra)
    })
</script>
@endpush
