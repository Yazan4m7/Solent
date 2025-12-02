@extends('layouts.app' ,[ 'pageSlug' => 'Oops' ])


@section('content')
    <head>

    </head>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
    }

    .container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
        background-color: #f4f4f4;
    }

    .header {
        text-align: center;
        background-color: #006a4d; /* Dark green */
        color: white;
        padding: 20px;
        border-radius: 10px;
        width: 80%;
        max-width: 500px;
    }

    h1 {
        font-size: 2.5em;
        margin-bottom: 10px;
    }

    p {
        font-size: 1.2em;
    }

    .error-content {
        margin-top: 30px;
        justify-self: anchor-center;
    }

    button {
        padding: 10px 20px;
        background-color: #00b5ad; /* Light green */
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 1.2em;
        cursor: pointer;
    }

    button:hover {
        background-color: #007d71; /* Darker green */
    }

    @media screen and (max-width: 991px){
        #datatable_wrapper {
            overflow: auto;
        }
    }
    .card-body{
        padding: 0;
    }
    .row, .container-fluid{
        padding-left:0px;
        padding-right:0px;
    }
    /*.col-sm-12 {*/
        /*padding-right:0px;*/
        /*padding-left:0px;*/
    /*}*/
    tr { cursor: pointer; }
    td {border : 0 !important;}

    .oops p {
        font-size: 1em;
        height: 12em;
        overflow-y: scroll;
        overflow-x: hidden;
    }
    .oops.one {
        height: 12em;
        overflow: hidden;
        width: 100%;
         max-width: 100%;
        text-align: left;
        background-color: #38fff5;
    }
    .oops.two {
        width: 100%;
        max-width: 100%;
        text-align: left;
        background-color: #38fff5;
    }
</style>
<div class=" oops bg-white">
    <div class=" oops ">
        <div class=" oops header one">
            <h1>Oops! Something Went Wrong :(</h1>
            <p>{{ $errorMessage  ?? "No Error Message provided"}}</p>
        </div>
        <div class=" oops header two">
            <p style="overflow: scroll">{{ $exception ?? "No Exception provided" }}</p>
        </div>
        <div class=" oops error-content">

            <button onclick="window.location.href='/home'">Back to Dashboard</button>
        </div>
    </div>

    </div>





    @endsection




