<?php

namespace App\Http\Controllers;

use App\client;
use DB;

use App\payment;
use App\sCase;

use App\job;
use App\User;
use Illuminate\Http\Request;

class AccountantController extends Controller
{

    public function deliveryCases4Accountant(){

        $drivers = User::whereHas('permissions', function ($q): void {
            $q->whereIn('permission_id',array(8));
        })->orWhere("is_admin",1)->get();
        $from = date('Y-m-d', strtotime('first day of last month')) . ' 00:00';
        $to = now()->toDateString(). ' 23:59';
        $jobs =job::select('case_id','assignee','delivery_accepted','has_been_rejected','is_rejection' )->whereBetween('created_at', [$from, $to ])->whereIn("stage",[8,-1])->where('is_rejection',0)->where('has_been_rejected',0)->distinct('case_id')->get();

        $activeCases = sCase::whereIn('id',$jobs->whereNotNull("delivery_accepted")->pluck("case_id")->toArray())->whereNull('voucher_recieved_by')->orderBy("actual_delivery_date","DESC")->get();
        $waitingCases = sCase::whereIn('id',$jobs->whereNull("delivery_accepted")->pluck("case_id")->toArray())->get();

        return view ('accountant.deliveryCases-accountant',compact('activeCases','waitingCases','jobs','drivers'));
    }


    public function receiveVoucher($caseId){
        $case=  sCase::findOrFail($caseId);
        if(isset($case->actual_delivery_date) || $case->delivered_to_client > 0 ) {
            $case->voucher_recieved_by = Auth()->user()->id;
            $case->save();
            return back()->with('success', 'Voucher received successfully ');
        }
        else
        return back()->with('error', 'Case has not been delivered yet.');
    }


    public function receiveVoucherWithoutRedirecting($caseId){
        $case=  sCase::findOrFail($caseId);
        if(isset($case->actual_delivery_date) || $case->delivered_to_client > 0 ) {
            $case->voucher_recieved_by = Auth()->user()->id;
            $case->save();
        }
    }
    public function receiveMultipleVoucher(Request $request){
        if(!$request->casesCheckBoxes)
            return back()->with('error', 'Select cases first.');

        foreach($request->casesCheckBoxes as $caeId){
            $this->receiveVoucherWithoutRedirecting($caeId);
        }
        return back()->with('success', 'Vouchers were received successfully ');

    }

    public function receivablePayments(Request $request){
        if ($request->from && $request->to) {
            $from = $request->from ;
            $to = $request->to ;
        }
        else {
            $from = date('Y-m-d', strtotime('first day of this month')) . ' 00:00';
            $to = now()->toDateString(). ' 23:59';
        }
        if ($request->doctor && !in_array( "all",$request->doctor))
            $payments = payment::whereBetween('created_at', [$from, $to . ' 23:59'])->whereIn('doctor_id',$request->doctor)->get();
        else
            $payments = payment::whereBetween('created_at', [$from, $to ])->get();
        $selectedClients = $request->doctor;
        $clients = client::all();
        $payments = $payments->whereNull('received_by');

        $tag ="acc";
        return view('generic.payments-list',compact('payments','to','from','clients','selectedClients','tag'));
        ;
    }

    public function receivePayment($id){
        $payment = payment::where('id',$id)->first();
        if(!$payment) return back()->with('error', 'Payment not found.');
        if($payment->received_by != null )     return back()->with('error', 'Payment Already received');
        $payment->received_by = Auth()->user()->id;
        $payment->recieved_on = now();
        $payment->save();
        return back()->with('success', 'Payment received successfully ');
    }

    public function paymentsWithCollectors(Request $request){
        if ($request->from && $request->to) {
            $from = $request->from ;
            $to = $request->to ;
        }
        else {
            $from = date('Y-m-d', strtotime('first day of this month')) . ' 00:00';
            $to = now()->toDateString(). ' 23:59';
        }
        $payments = payment::query();

        if ($request->doctor && !in_array( "all",$request->doctor))
            $payments = $payments->whereIn('doctor_id',$request->doctor);

        if ($request->collectors && !in_array( "all",$request->collectors))
            $payments = $payments->whereIn('collector',$request->collectors);
            $payments = $payments->whereNull('received_by');
            $payments = $payments->whereBetween('created_at', [$from, $to ])->get();

        $selectedClients = $request->doctor;
        $selectedCollectors = $request->collectors;
        $clients = client::all();


        $collectors = User::whereHas('permissions', function ($q): void {
            $q->whereIn('permission_id',array(111));
        })->orWhere("is_admin",1)->get();

        $tag ="acc";
        return view('delivery.receive-payment',compact('payments','to','from','clients','selectedClients','tag','collectors','selectedCollectors'));

    }
}
