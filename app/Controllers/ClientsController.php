<?php
/**
 * User: Yazan
 * Date: 10/4/2021
 * Time: 8:36 PM
 */
namespace App\Http\Controllers;
use App\Http\Traits\helperTrait;
use App\material;
use App\JobType;
use App\client;
use App\invoice;
use App\payment;
use App\bank;
use App\clientDiscount;
use App\sCase;
use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class ClientsController extends Controller
{
    use helperTrait;
    public function index(Request $request)
    {
        $status = $request->get('status', 'active'); // Default to active

        if ($request->doctor &&  !in_array( "all",$request->doctor) ) {
            $clients = client::whereIn('id', $request->doctor)->get();
            $selectedClients =  $request->doctor ;

        } else {
            if ($status === 'disabled') {
                if (Schema::hasColumn('clients', 'is_active')) {
                    $clients = client::where('is_active', 0)->get();
                } elseif (Schema::hasColumn('clients', 'active')) {
                    $clients = client::where('active', 0)->get();
                } else {
                    $clients = collect(); // Empty collection
                }
            } else {
                // Show active clients
                $clients = client::active()->get();
            }
            $selectedClients = null;
        }
        if ($request->from)
            $from = $request->from;
         else
            $from = now()->toDateString() . ' 23:59';

        $totalBalance=0;
            foreach($clients as $client)
            $totalBalance = $totalBalance + $client->balanceAt($from);

         //dd($from);
        $allClients = client::all();
        $banks = bank::all();
        return view('clients.index',compact('allClients',"clients",'banks','selectedClients','from','totalBalance','status'));
    }

    public function returnCreate()
    {
        $jobTypes =  JobType::all();
        $materials =  material::active()->get();
        return view('clients.create',compact('jobTypes','materials'));
    }
    public function create(Request $request)
    {
    $this->validate($request, [
            'dentist_name'     => 'required|max:30',
            'phone_number'    => 'required',
            'address'  => 'required',
            //'discount' => 'required|numeric|min:0',
            //'type'     => 'required',
    ]);

        $dentist = new client();
        $dentist->name = $request->dentist_name;
        $dentist->phone = $request->phone_number;
        $dentist->address = $request->address;
        $dentist->is_active = 1;
        $dentist->save();
        foreach ($request->repeat as $rep) {
            if(isset($rep['discount'])){
                $discount = new clientDiscount();
                $discount->type = $rep['type'];
                $discount->discount = $rep['discount'];
                $discount->material_id = $rep['material'];
                $discount->client_id = $dentist->id;
                $discount->save();
            }
        }
        return back()->with('success', 'Doctor has been successfully created');
    }
    public function update(Request $request)
    {
        $this->validate($request, [
            'name'     => 'required|max:30',
            'phone'    => 'required',
            'address'  => 'required',
        ]);

        $doctor = client::where('id', $request->id)->first();
        if (!$doctor) {
            abort(404);
        }

        //update.
        $doctor->name = $request->name;
        $doctor->phone = $request->phone;
        $doctor->clinic_phone = $request->clinic_phone;
        $doctor->address = $request->address;
         $doctor->doc_password = Hash::make($request->doc_password);
         $doctor->clinic_password = Hash::make($request->clinic_password);
        $doctor->save();
        clientDiscount::where('client_id', $request->id)->delete();
        if (is_array($request->ids)) {
            foreach ($request->ids as $mat) {
                $discount = new clientDiscount();
                $o_type = "old_type_".$mat;
                $o_discount = "old_discount_".$mat;
                $o_material = "old_material_".$mat;
                $discount->type = $request->$o_type[0];
                $discount->discount = $request->$o_discount[0];
                $discount->material_id = $request->$o_material[0];
                $discount->client_id = $request->id;
                $discount->save();
            }
        }
        if(is_array($request->repeat)){
            foreach ($request->repeat as $rep) {
                if(!isset($rep["type"]) || !isset($rep['discount']) || !isset($rep['material'])){
                    continue;
                }
                $discount = new clientDiscount();
                $discount->type = $rep['type'];
                $discount->discount = $rep['discount'];
                $discount->material_id = $rep['material'];
                $discount->client_id = $doctor->id;
                $discount->save();
            }
        }
        return back()->with('success', 'Doctor has been successfully updated');
    }
    public function view($id)
    {
        $user = client::with('discounts')->where('id', $id)->first();
        if (!$user) {
            abort(404);
        }
        $materials = material::all();
        return view('clients.view-edit')->with('user', $user)->with('materials', $materials);
    }
    public function statementOfAccount($id =-1, Request $request)
        {
            if($request->allTime == 1){
                $from = date('Y-m-d', strtotime('01-01-2021'));
                $to = now()->toDateString();

            }
            else if ($request->from && $request->to) {
                $from = $request->from ;
                $to = $request->to ;
            }
            else {
                $from = date('Y-m-d', strtotime('first day of this month'));
                $to = now()->toDateString();
            }
        $client = client::findOrFail($id);
        $invoices = invoice::where("doctor_id", $id)->where('status',1)->whereBetween('date_applied', [$from . ' 00:00', $to . ' 23:59'])->get();
        $payments = payment::where("doctor_id", $id)->whereBetween('created_at', [$from . ' 00:00', $to . ' 23:59'])->get();
        // toBase() to prevent id overwriting.
        $transactions =  $invoices->toBase()->merge($payments)
             ->transform( function ($item) {
                 if(!empty($item->date_applied)) {
                     $item->created_at = $item->date_applied;
                 }
                 else if(!empty($item->case->actual_delivery_date))
                 {
                     $item->created_at = $item->case->actual_delivery_date;
                 }
//                 if(!empty($item->case->actual_delivery_date)) {
//                     $item->created_at = $item->case->actual_delivery_date;
//                 }
                 return $item;
             })->sortBy('created_at');

        $amountDuePreDate = invoice::where("doctor_id", $id)->where('date_applied','<',$from . ' 00:00')->where('status',1)->sum('amount');
        $amountPaidPreDate =  payment::where("doctor_id", $id)->where('created_at','<',$from . ' 00:00')->sum('amount');

        $openingBalance  =$amountDuePreDate - $amountPaidPreDate;

        return view("clients.statement",compact('amountPaidPreDate','amountDuePreDate','invoices','client','payments','transactions','to','from','openingBalance'));
        }

    public function quickAccessDS(Request $request){
        $doctorQuery = client::query();
        $searchValues = preg_split('/\s+/', $request->docNameSearchText, -1, PREG_SPLIT_NO_EMPTY);
        $doctor = $doctorQuery->where(function ($q) use ($searchValues) {
            foreach ($searchValues as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->first();

        if(!$doctor) return back()->with("error","no matching doctor found.");
        else
            return $this->statementOfAccount($doctor->id,$request);
    }
    public function statementOfAccountWithFilters(Request $request)
    {
        /*
        if ($request->from && $request->to)
        {}
        else {}
        $client = client::findOrFail($id);
        $invoices = invoice::where("doctor_id", $id)->get();
        $payments = payment::where("doctor_id", $id)->get();
        $transactions =  $invoices->merge($payments)->sortBy('created_at');
        return view("clients.statement",compact('invoices','client','payments','transactions'));
        */
    }
    public function newPayment(Request $request){
        $this->validate($request, [
            'id'     => 'required',
            'amount' => 'required|numeric',
        ]);
        $doctor = client::where('id', $request->id)->first();

        if(!$doctor){
            return back()->with('error', "Doctor not found");
        }

        $doctor->balance = $doctor->balance - $request->amount;
        $doctor->save();

        $payment = new payment();
        $payment->amount = $request->amount;
        $payment->collector = Auth()->user()->id;
        if($request->payment_type == 'cash'){
            $payment->notes = "دفعة نقدية";
        }
        else if($request->payment_type == 'transfer'){
            $payment->notes = "حوالة بنكية/ كليك";
        }
        else
        {
            $bank = bank::where('id' , $request->bank_id)->first();
            $payment->from_bank = $bank->id;
            $payment->notes =  $request->chequeNumber .' '. $bank->bank_abbrev  . ' شيك ';
        }
        $payment->doctor_id = $doctor->id;
        $payment->additional_notes = $request->note;
        $payment->save();
        if(isset($doctor->doc_notification_token))
            $this->sendPaymentNotification($doctor->doc_notification_token,
                "Payment Received",
                $payment->amount . " " . $this->currentCurrencyCode() . " has been received"
                );

        return back()->with('success', "Payment received successfully!");
    }
    public function paymentsIndex(Request $request){
        if ($request->from && $request->to) {
            $from = $request->from ;
            $to = $request->to ;
        }
        else {
            $from = date('Y-m-d', strtotime('first day of this month')) . ' 00:00';
            $to = now()->toDateString(). ' 23:59';
        }
        if ($request->doctor && !in_array( "all",$request->doctor))
            $payments = payment::whereBetween('created_at', [$from, $to ])->whereIn('doctor_id',$request->doctor)->get();
        else
            $payments = payment::whereBetween('created_at', [$from, $to ])->get();
        $selectedClients = $request->doctor;
        $clients = client::all();

        return view('generic.payments-list',compact('payments','to','from','clients','selectedClients'));
    }

    public function accountDiscount(Request $request){
        $doctor = client::where('id', $request->id)->first();

        if(!$doctor){
            return back()->with('error', "Doctor not found");
        }

            $invoice = new invoice();
            $invoice->status =1;
            $invoice->date_applied = $request->discount_date;;
            $invoice->created_at = $request->discount_date;
             $invoice->updated_at = $request->discount_date;
            $invoice->amount =$request->discountAmount*-1;
            $invoice->amount_before_discount =$request->discountAmount*-1;
            $invoice->case_id =-1;
            $invoice->doctor_id =$doctor->id;
            $invoice->discount_title =$request->discount_title;
            $invoice->save();
            $doctor->balance =  $doctor->balance + $invoice->amount;
            $doctor->save();
        return back()->with('success', "Discount applied successfully");

    }

    public function deletePayment($id){

        $payment = payment::where('id',$id)->first();
        if(!$payment)return back()->with('error', 'Payment not found.');

        $doc =client::where('id', $payment->doctor_id)->first();
        $doc->balance= $doc->balance + $payment->amount;
        $doc->save();


        $payment->delete();

        return back()->with('success', 'Payment removed.');
    }

    public function doctorInvoices(Request $request)
    {
        if ($request->from && $request->to) {
            $from = $request->from ;
            $to = $request->to ;
        }
        else {
            $from = date('Y-m-d', strtotime('first day of this month'));
            $to = now()->toDateString();
        }

        $invoices = Invoice::where('doctor_id', $request->id)->whereBetween('created_at', [$from, $to ])->get();

        return view('generic.invoices-list',compact('invoices','to','from'))->with('id',$request->id);
    }

    public function doctorCases(Request $request)
    {

        if ($request->from && $request->to) {
            $from = $request->from ;
            $to = $request->to ;
        }
        else {
            $from = date('Y-m-d', strtotime('first day of this month'));
            $to = now()->toDateString();
        }

        $cases = sCase::where('doctor_id', $request->id)->whereBetween('actual_delivery_date', [ $from. ' 00:00', $to . ' 23:59'])
            ->orWhereNull('actual_delivery_date');
        $cases = $cases->where('doctor_id', $request->id);
        $cases = $cases->orderByRaw('-`actual_delivery_date` ASC')->orderBy("initial_delivery_date",'asc')->paginate(20)->withQueryString();

        return view ('cases.index',compact('cases','from','to'))->with('id',$request->id);

    }

    public function doctorPayments(Request $request)
    {
        if ($request->from && $request->to) {
            $from = $request->from ;
            $to = $request->to ;
        }
        else {
            $from = date('Y-m-d', strtotime('first day of this month')) . ' 00:00';
            $to = now()->toDateString(). ' 23:59';
        }

        $payments = payment::where('doctor_id', $request->id)->whereBetween('created_at', [$from, $to ])->get();



        return view('generic.payments-list',compact('payments','to','from'))->with('id',$request->id);

    }

    public function delete(Request $request)
    {
        try {
            $client = client::findOrFail($request->client_id);

            // Check if doctor is used in any cases
            $usedInCases = sCase::where('doctor_id', $client->id)->exists();

            if ($usedInCases) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This doctor cannot be deleted because they have been used in one or more cases. Would you like to disable them instead?',
                    'canDisable' => true
                ]);
            }

            $client->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Doctor has been successfully deleted'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting doctor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function disable(Request $request)
    {
        try {
            $client = client::findOrFail($request->client_id);

            // Check if is_active column exists (new implementation)
            if (Schema::hasColumn('clients', 'is_active')) {
                $client->is_active = 0;
                $client->save();
                $message = 'Doctor has been successfully disabled.';
            }
            // Fallback to existing active column if it exists
            elseif (Schema::hasColumn('clients', 'active')) {
                $client->active = 0;
                $client->save();
                $message = 'Doctor has been successfully disabled. ';
            }
            else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Database schema not supported. Neither is_active nor active column found.'
                ], 500);
            }

            return response()->json([
                'status' => 'success',
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error disabling doctor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function enable(Request $request)
    {
        try {
            $client = client::findOrFail($request->client_id);

            // Check if is_active column exists (new implementation)
            if (Schema::hasColumn('clients', 'is_active')) {
                $client->is_active = 1;
                $client->save();
                $message = 'Doctor has been successfully enabled.';
            }
            // Fallback to existing active column if it exists
            elseif (Schema::hasColumn('clients', 'active')) {
                $client->active = 1;
                $client->save();
                $message = 'Doctor has been successfully enabled.';
            }
            else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Database schema not supported. Neither is_active nor active column found.'
                ], 500);
            }

            return response()->json([
                'status' => 'success',
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error enabling doctor: ' . $e->getMessage()
            ], 500);
        }
    }

}
