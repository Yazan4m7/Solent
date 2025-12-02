<?php

namespace App\Http\Controllers;
use App\abutmentDeliveryRecord;
use App\abutmentReceiveLogs;
use App\client;
use App\sCase;
use Illuminate\Http\Request;
use App\abutment;
use DB;


class AbutmentsController extends Controller
{
    public function index(){
        $abutments = abutment::all();
        return view('abutments.index',compact("abutments"));
    }

    public function returnCreate()
    {
        return view('abutments.create');
    }
    public function create(Request $request)
    {
        $this->validate($request, [
            'abutment_name'     => 'required|max:50',

        ]);

        $abutment = new abutment();

        try {
            $abutment->name = $request->abutment_name;
            $abutment->save();

            return back()->with('success', 'Abutment has been successfully created');
        } catch (Exception $e) {
            return back()->with('error', $e);
        }
    }
    public function returnUpdate($id)
    {

        $abutment = abutment::findOrFail($id);
        return view('abutments.edit',compact('abutment'));
    }
    public function update(Request $request)
    {
        try {
            $abutment = abutment::where('id', $request->abutment_id)->first();
            if (!$abutment) {
                return back()->with('Implant Not found');
            }
            $abutment->name = $request->abutment_name;

            $abutment->save();


            return back()->with('success', 'Abutment has been successfully updated');
        } catch (Exception $e) {
            return back()->with('error', $e);
        }
    }

        public function abutmentsDeliveryIndex(Request $request){

        // Time Filtration
        if ($request->from && $request->to) {
            $from = $request->from ;
            $to = $request->to ;
        }
        else {
            $from = date('Y-m-d', strtotime('-30 days'));
            $to = now()->toDateString();
        }

        $deliveriesReceived=  abutmentDeliveryRecord::whereBetween('created_at', [ $from. ' 00:00', $to . ' 23:59'])
                ->where('status',3)->get()
                ->sortByDesc(function($delivery, $key) {
                    return $delivery->logs->last()->created_at;
                });
       // $deliveriesReceived = abutmentDeliveryRecord::whereBetween('created_at', [ $from. ' 00:00', $to . ' 23:59'])->where('status',3)->get();
//        $deliveriesPending = abutmentDeliveryRecord::whereBetween('created_at', [ $from. ' 00:00', $to . ' 23:59'])->whereIn('status',[0,1,2])->
//            get()->sortByDesc('case.initial_delivery_date');
        $deliveriesPending = abutmentDeliveryRecord
                ::whereBetween('abutments_delivery.created_at', [ $from. ' 00:00', $to . ' 23:59'])
                ->whereIn('status',[0,1,2])
                ->join('cases', 'cases.id', '=', 'abutments_delivery.case_id')
                ->orderBy('cases.initial_delivery_date','desc')
                ->select('abutments_delivery.*')
                ->get();

        $deliveries = $deliveriesPending->merge($deliveriesReceived);

        //         $selectedClients = $request->doctor;
        //         $clients = client::without(['discounts','cases'])->get();


        return view ('abutments.abutmentsDelivery',compact('deliveries','from','to'));
        }


        public function receiveAbutment(Request $request){
        $deliveryRecord = abutmentDeliveryRecord::where("id",$request->abutment_id)->first();
        if($request->qty > $deliveryRecord->qty)
            return back()->with("error","Quantity Entered too large");

        $qty = $deliveryRecord->remaining_qty - $request->qty;
        $deliveryRecord->remaining_qty = $qty;
        if ($qty == 0)
        $deliveryRecord->status = 3;
        else
            $deliveryRecord->status = 2;
        $deliveryRecord->save();


        $abutReceiveLog = new abutmentReceiveLogs();
        $abutReceiveLog->user_id = Auth()->user()->id;
        $abutReceiveLog->qty = $request->qty;
        $abutReceiveLog->abut_delivery_id = $request->abutment_id;
        $abutReceiveLog->save();
        return back()->with("success","abutments received successfully");
    }
    public function orderAbutment($id){
        $record = abutmentDeliveryRecord::where("id",$id)->first();
        $record->status = 1;
        $record->ordered_by = Auth()->user()->id;
        $record->ordered_on = now();
        $record->save();
        return back()->with("success","abutments marked as ordered successfully");
    }
}