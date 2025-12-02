@extends('layouts.app' ,[ 'pageSlug' => 'System Configuration' ])

@section('content')
<style>


    body {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        width: 100%;
        height: 100vh;
        background-color: #d1dad3;

        font-size: 12px;
    }

    .container {
        max-width: 1000px;
        width: 100%;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
    }

    .switch-holder {
        display: flex;

        border-radius: 10px;
        margin-bottom: 20px;

        justify-content: space-between;
        align-items: center;
    }

    .switch-label {
        width: 100px;
        margin-right: 20px;
    }

    .switch-label i {
        margin-right: 5px;
    }

    .switch-toggle {
        height: 40px;
    }

    .switch-toggle input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        z-index: -2;
    }

    .switch-toggle input[type="checkbox"] + label {
        position: relative;
        display: inline-block;
        width: 100px;
        height: 40px;
        border-radius: 20px;
        margin: 0;
        cursor: pointer;
        box-shadow: inset -8px -8px 15px rgba(255,255,255,.6),
        inset 10px 10px 10px rgba(0,0,0, .25);

    }

    .switch-toggle input[type="checkbox"] + label::before {
        position: absolute;
        content: 'OFF';
        font-size: 12px;
        text-align: center;
        line-height: 25px;
        top: 8px;
        left: 8px;
        width: 45px;
        height: 25px;
        border-radius: 20px;
        background-color: #d1dad3;
        box-shadow: -3px -3px 5px rgba(255,255,255,.5),
        3px 3px 5px rgba(0,0,0, .25);
        transition: .3s ease-in-out;
    }

    .switch-toggle input[type="checkbox"]:checked + label::before {
        left: 50%;
        content: 'ON';
        color: #fff;
        background-color: #00b33c;
        box-shadow: -3px -3px 5px rgba(255,255,255,.5),
        3px 3px 5px #00b33c;
    }
    table,td{
        border: 0px !important;
    }
    td{
        padding-bottom: 10px;
    }




</style>
    @php
        $permissions = Cache::get('user'.Auth()->user()->id);
    @endphp
    <div class="row" style="padding:20px;">
        <form class="kt-form" method="POST" action="{{route('update-sys-config')}}">
            @csrf

            <table >

                <tbody>
                <tr>
                    <td width="10%">
                    <div class="switch-label">
                        <span>Testing Environment</span>
                    </div>
                    </td>
                    <td>
                    <div class="switch-toggle">
                        <input type="checkbox" id="bluetooth" {{config('site_vars.environment') == 'testing' ? 'checked' : ''}}>
                        <label for="bluetooth"></label>
                    </div>
                    </td>
                </tr>
                <tr>
                    <td width="10%">
                        <div class="switch-label">
                            <span>Model Restriction</span>
                        </div>
                    </td>
                    <td>
                        <div class="switch-toggle">
                            <input type="checkbox" id="modelRestrict" {{config('site_vars.abutmentRestriction') == '1' ? 'checked' : ''}}>
                            <label for="modelRestrict"></label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td width="10%">
                        <div class="switch-label">
                            <span>Abutment Restriction</span>
                        </div>
                    </td>
                    <td>
                        <div class="switch-toggle">
                            <input type="checkbox" id="abutmentRestrict" {{config('site_vars.ModelRestriction') == '1' ? 'checked' : ''}}>
                            <label for="abutmentRestrict"></label>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
                <div class="kt-portlet__foot">
                    <div class="kt-form__actions">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="reset" class="btn btn-danger">Reset</button>
                    </div>
                </div>




        </form>
    </div>
@endsection
@push('js')
    <script>

    </script>
@endpush
