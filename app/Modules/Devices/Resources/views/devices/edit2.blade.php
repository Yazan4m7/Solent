@extends('layouts.app', ['pageSlug' => 'Edit ' . $device->name]) <!-- Assuming $device is passed to the view -->
@section('content')
    <style>
        .image-picker {
            max-width: 250%;
            max-height: 250%;
            box-shadow: 0 0 10px 0;
            position: absolute;
        }
    </style>
    <form method="POST" action="{{ route('edit-device', $device->id) }}" class="card" enctype="multipart/form-data" id="device-form">
        @csrf
{{--        @method('PUT') <!-- Assuming  -->--}}
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h6 class="kt-portlet__head-title">
                    <i class="fa fa-suitcase" style="width:3%"></i> Device Info:
                </h6>
            </div>
        </div>
        <input value="{{$device->id}}" type="hidden" name="device_id"/>
        <hr style="margin-top: 0;">
        <div class="row">
            <div class="col-md-3 col-xs-6 col-l-3 col-xl-3">
                <div class="col-md-12 col-xs-12"><label>Device name:</label></div>
                <div class="col-md-12 col-xs-12">
                    <input class="form-control" type="text" name="device_name" required placeholder="Device name" value="{{ $device->name }}" />
                    <span class="help-block text-muted"><small></small></span>
                </div>
            </div>
            <div class="col-md-3 col-xs-6 col-l-3 col-xl-3">
                <div class="col-md-12 col-xs-12"><label>Device Type:</label></div>
                <div class="col-md-12 col-xs-12">
                    <select class="form-control selectpicker" id="dev" name="device_type">
                        <option value="3" {{ $device->type == 3 ? 'selected' : '' }}>3D Printer</option>
                        <option value="2" {{ $device->type == 2 ? 'selected' : '' }}>Milling Machine</option>
                        <option value="4" {{ $device->type == 4 ? 'selected' : '' }}>Sintering Furnace</option>
                        <option value="5" {{ $device->type == 5 ? 'selected' : '' }}>Pressing Furnace</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2 col-l-2 col-xl-2">
                <div class="col-md-12 col-xs-12"><label>Device Order:</label></div>
                {{-- <small>Order out of devices of same type</small> --}}
                <div class="col-md-12 col-xs-12">
                    <input class="form-control btn dropdown-toggle btn-light" type="number" name="device_order" value="{{ $device->sorting_order }}" required />
                </div>
            </div>
            <div class="col-md-3 col-xs-6 col-l-3 col-xl-3">
                <div class="col-md-12 col-xs-12"><label>Device Image:</label></div>
                <label for="image-picker">
                    <img id="image-preview" class="image-picker" src="{{ asset($device->img) }}" alt="Machine/Device" />
                </label>
                <input id="image-picker" value="" type="file" accept="image/png" name="device_image" style="display:contents"/>
            </div>

        </div>

        <br/>
        <div class="form-group mb-0">
            <div>

                <button type="submit" class="btn btn-info waves-effect waves-light">
                    Submit
                </button>
                <button type="reset" class="btn btn-secondary waves-effect m-l-5">
                    Cancel
                </button>
            </div>
        </div>
    </form>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function () {
            $('form').parsley();
        });

        $("#image-picker").change(function (event) {
            const file = this.files[0];
            const fileType = file.type;
            const fileSize = file.size;

            // Validate file type and size
            if (fileType !== 'image/png') {
                alert('Only PNG images are allowed.');
                this.value = ''; // Clear the input
                return;
            }
            if (fileSize > 4 * 1024 * 1024) { // 4MB in bytes
                alert('Image size must not exceed 4MB.');
                this.value = ''; // Clear the input
                return;
            }

            readURL(this);
        });

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#image-preview').attr('src', e.target.result);
                }
                $('#image-picker').css("display", "none");
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    <script type="text/javascript" src="{{ asset('assets/plugins/parsleyjs/dist/parsley.min.js') }}"></script>
@endpush
