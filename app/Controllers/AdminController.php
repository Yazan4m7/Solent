<?php

namespace App\Http\Controllers;


use App\abutment;
use App\discount;
use App\implant;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Cache;
use App\client;
use App\material;
use App\JobType;
use App\sCase;
use App\caseTag;
use App\file;
use App\note;
use App\job;
use App\invoice;
use App\impressionType;
use App\materialJobtype;
use App\tag;
use App\caseLog;
use App\User;
use App\lab;
use App\editLog;
use App\Http\Traits\helperTrait;
class AdminController extends Controller
{

    public function intelDashboard(){
        return view('admin.intel_dashboard');
    }
    public function mobileAccessStats(Request $request){

        if ($request->doctor &&  !in_array( "all",$request->doctor) ) {
            $clients = client::whereIn('id', $request->doctor)->get();
            $selectedClients =  $request->doctor ;
        } else {
            $clients = client::all();
            $selectedClients = null;
        }
        $allClients = client::all();
        return view('admin.mobile-access-stats',compact('clients','allClients','selectedClients'));
    }


}