<?php

namespace App\Modules\Financing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Financing\Models\FinanceAccount;
use App\Modules\Financing\Models\InvoiceInstallment;
use App\Modules\Financing\Services\FinanceLedgerService;
use App\Modules\Financing\Services\FinancePaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancingInstallmentController extends Controller
{
    public function index(Request $request)
    {
        $query = InvoiceInstallment::with(['client', 'invoice'])->orderBy('due_date');

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->boolean('overdue')) {
            $query->whereNull('paid_at')->whereDate('due_date', '<', today());
        }

        $installments = $query->paginate(50)->appends($request->query());

        $clientModel = config('modules.financing.models.client');
        $clients = $clientModel::orderBy('name')->get();
        $accounts = FinanceAccount::where('is_active', true)->orderBy('name')->get();

        return view('financing.installments.index', compact('installments', 'clients', 'accounts'));
    }

    public function create(Request $request, $invoiceId)
    {
        $invoiceModel = config('modules.financing.models.invoice');
        $invoice = $invoiceModel::findOrFail($invoiceId);

        if (InvoiceInstallment::where('invoice_id', $invoice->id)->exists()) {
            return back()->withErrors([
                'installments' => __('financing::financing.invoice_already_split'),
            ]);
        }

        $data = $request->validate([
            'amounts' => 'required|array|min:2',
            'amounts.*' => 'required|numeric|min:0.01',
            'due_dates' => 'required|array',
            'due_dates.*' => 'required|date',
        ]);

        if (count($data['amounts']) !== count($data['due_dates'])) {
            return back()->withErrors([
                'installments' => __('financing::financing.installment_rows_invalid'),
            ]);
        }

        $sum = array_sum(array_map('floatval', $data['amounts']));

        if (abs($sum - (float) $invoice->amount) > 0.01) {
            return back()->withErrors([
                'installments' => __('financing::financing.installments_must_match_invoice'),
            ])->withInput();
        }

        DB::transaction(function () use ($invoice, $data) {
            foreach ($data['amounts'] as $index => $amount) {
                InvoiceInstallment::create([
                    'invoice_id' => $invoice->id,
                    'client_id' => $invoice->doctor_id,
                    'amount' => $amount,
                    'due_date' => $data['due_dates'][$index],
                ]);
            }
        });

        session()->flash('success', __('financing::financing.installments_created'));

        return back();
    }

    public function pay(Request $request, $id, FinanceLedgerService $ledger, FinancePaymentService $paymentService)
    {
        $installment = InvoiceInstallment::findOrFail($id);

        if ($installment->paid_at) {
            return back()->withErrors([
                'installment' => __('financing::financing.installment_already_paid'),
            ]);
        }

        $data = $request->validate([
            'account_id' => 'required|exists:finance_accounts,id',
        ]);

        DB::transaction(function () use ($installment, $data, $ledger, $paymentService) {
            $paymentModel = config('modules.financing.models.payment');
            $payment = new $paymentModel();
            $payment->doctor_id = $installment->client_id;
            $payment->amount = $installment->amount;
            $payment->save();

            $installment->payment_id = $payment->id;
            $installment->paid_at = now();
            $installment->save();

            $paymentService->linkPaymentToAccount($payment, $data['account_id'], $ledger);
        });

        session()->flash('success', __('financing::financing.payment_recorded'));

        return back();
    }
}
