<?php

namespace App\Http\Controllers;
use App\abutmentDeliveryRecord;
use App\abutmentReceiveLogs;
use App\client;
use App\payment;
use App\sCase;
use Illuminate\Http\Request;
use App\abutment;
use DB;
use Illuminate\Support\Facades\Auth;


class DeliveryController extends Controller
{

    public function myCollections(){

        $payments = payment::where('collector',Auth()->user()->id)->whereNull('received_by')->get();
        return view ('delivery.my-collections',compact('payments'));
        }

}