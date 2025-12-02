<?php
/**
 * User: Yazan
 * Date: 10/4/2021
 * Time: 8:36 PM
 */
namespace App\Modules\Clients\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Traits\helperTrait;
use App\material;
use App\JobType;
use App\client;
use App\invoice;
use App\MobileNotificationToken;
use App\payment;
use App\bank;
use App\clientDiscount;
use App\sCase;
use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Hash;

class ClientsController extends Controller
{
    use helperTrait;
//    public function index(Request $request)
//    {
//
//
//        if ($request->doctor &&  !in_array( "all",$request->doctor) ) {
//            $clients = client::whereIn('id', $request->doctor)->get();
//            $selectedClients =  $request->doctor ;
//
//        } else {
//
//        $clients = client::all();
//        $selectedClients = null;
//        }
//        if ($request->from)
//            $from = $request->from;
//         else
//            $from = now()->toDateString() . ' 23:59';
//        $status = $request->active  == 1 ? 1: 0 ;
//       $clients =   $clients->where('active',$status) ;
//        $totalBalance=0;
//            foreach($clients as $client)
//            $totalBalance = $totalBalance + $client->balanceAt($from);
//
//         //dd($from);
//        $allClients = client::all();
//        $banks = bank::all();
//        return view('clients.index',compact('allClients',"clients",'banks','selectedClients','from','totalBalance'))->with('status', $status);
//    }
    public function index(Request $request)
    {
        $from = $request->from ?? now()->toDateString() . ' 23:59';
        $status = $request->has('active') ? (int) $request->active : 1;

// Start query
        $query = Client::query();

// Filter by active status only (no soft deletes)
        $query->where('active', $status);

// Filter by doctors
        if ($request->doctor && !in_array('all', $request->doctor)) {
            $query->whereIn('id', $request->doctor);
            $selectedClients = $request->doctor;
        } else {
            $selectedClients = null;
        }

        $clients = $query->get();

// Balance logic
        $from = $request->from ?? now()->toDateString() . ' 23:59';
        $totalBalance = $clients->sum(fn($client) => $client->balanceAt($from));

// Supporting data
        $allClients = Client::all();
        $banks = Bank::all();

        return view('clients.index', compact(
            'allClients',
            'clients',
            'banks',
            'selectedClients',
            'from',
            'totalBalance'
        ))->with('status', $status);

    }

    public function returnCreate()
    {
        $jobTypes =  JobType::all();
        $materials =  material::all();
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
        $openingBalanceInvoice = invoice::where('doctor_id', $id)->where('discount_title', 'Opening Balance')->first();
        return view('clients.view-edit')->with('user', $user)->with('materials', $materials)->with('openingBalanceInvoice', $openingBalanceInvoice);
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
        $doctor = $doctorQuery->where(function ($q) use ($searchValues): void {
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
        $clientTokens = MobileNotificationToken::where("client_id",$doctor->id)->where("is_clinic",0)->get();
        foreach ($clientTokens as $token) {
            if (isset($token->token))
                $this->sendPaymentNotification($token->token,
                    "Payment Received",
                    $payment->amount . " JOD has been received"
                );
        }
        return back()->with('success', "Payment received successfully!");
    }
    public function paymentsIndex(Request $request){
        if ($request->from && $request->to) {
            $from = $request->from ;
            $to = $request->to;
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

    public function toggleActive($id)
    {
        try {
            $client = client::where('id', $id)->first();
            if (!$client) {
                return back()->with('error', 'Doctor not found');
            }
            
            // Toggle the active status
            $client->active = $client->active ? 0 : 1;
            $client->save();
            
            $status = $client->active ? 'enabled' : 'disabled';
            return back()->with('success', "Doctor has been {$status} successfully");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    public function setOpeningBalance(Request $request)
    {
        $this->validate($request, [
            'client_id' => 'required|exists:clients,id',
            'opening_balance' => 'required|numeric',
            'opening_balance_date' => 'required|date',
        ]);

        $client = client::findOrFail($request->client_id);
        $openingBalanceDate = $request->opening_balance_date;

        // Check for existing records before the opening balance date
        $existingInvoices = invoice::where('doctor_id', $client->id)
            ->where('discount_title', '!=', 'Opening Balance')
            ->where('date_applied', '<', $openingBalanceDate)
            ->exists();

        $existingPayments = payment::where('doctor_id', $client->id)
            ->where('created_at', '<', $openingBalanceDate)
            ->exists();

        if ($existingInvoices || $existingPayments) {
            return back()->with('error', 'There are existing invoices or payments before the opening balance date. Please choose a later date.');
        }

        $openingBalanceInvoice = invoice::where('doctor_id', $client->id)
            ->where('discount_title', 'Opening Balance')
            ->first();

        if ($openingBalanceInvoice) {
            // Update existing opening balance invoice
            $oldAmount = $openingBalanceInvoice->amount;
            $openingBalanceInvoice->amount = $request->opening_balance;
            $openingBalanceInvoice->amount_before_discount = $request->opening_balance;
            $openingBalanceInvoice->date_applied = $openingBalanceDate;
            $openingBalanceInvoice->created_at = $openingBalanceDate;
            $openingBalanceInvoice->updated_at = $openingBalanceDate;
            $openingBalanceInvoice->save();

            // Adjust client balance
            $client->balance = $client->balance - $oldAmount + $openingBalanceInvoice->amount;
            $client->save();
        } else {
            // Create a new opening balance invoice
            $invoice = new invoice();
            $invoice->status = 1;
            $invoice->date_applied = $openingBalanceDate;
            $invoice->created_at = $openingBalanceDate;
            $invoice->updated_at = $openingBalanceDate;
            $invoice->amount = $request->opening_balance;
            $invoice->amount_before_discount = $request->opening_balance;
            $invoice->case_id = -2; // Special case ID for opening balance
            $invoice->doctor_id = $client->id;
            $invoice->discount_title = 'Opening Balance';
            $invoice->save();

            // Update client balance
            $client->balance = $client->balance + $invoice->amount;
            $client->save();
        }

        return back()->with('success', 'Opening balance set successfully.');
    }

    public function paymentInvoice($id)
    {
        $client = client::findOrFail($id);

        return view('generic.payment-invoice', compact('client'));
    }
}
