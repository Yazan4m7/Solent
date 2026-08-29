<?php

namespace App\Modules\Financing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Financing\Models\FinanceAccount;
use App\Modules\Financing\Models\Supplier;
use App\Modules\Financing\Models\SupplierBill;
use App\Modules\Financing\Models\SupplierBillPayment;
use App\Modules\Financing\Services\FinanceLedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancingBillController extends Controller
{
    public function index(Request $request)
    {
        $query = SupplierBill::with('supplier')->orderBy('status')->orderBy('due_date');

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->boolean('overdue')) {
            $query->where('status', '!=', 'paid')->whereDate('due_date', '<', today());
        }

        $bills = $query->paginate(40)->appends($request->query());
        $suppliers = Supplier::orderBy('name')->get();

        return view('financing.bills.index', compact('bills', 'suppliers'));
    }

    public function create(Request $request)
    {
        $suppliers = Supplier::orderBy('name')->get();
        $selectedSupplierId = $request->supplier_id;

        return view('financing.bills.create', compact('suppliers', 'selectedSupplierId'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['created_by'] = auth()->id();
        $data['paid_amount'] = 0;
        $data['status'] = 'unpaid';

        $bill = SupplierBill::create($data);

        session()->flash('success', __('financing::financing.saved'));

        return redirect()->route('financing.bills.show', $bill->id);
    }

    public function show($id)
    {
        $bill = SupplierBill::with(['supplier', 'payments.account'])->findOrFail($id);
        $accounts = FinanceAccount::where('is_active', true)->orderBy('name')->get();

        return view('financing.bills.show', compact('bill', 'accounts'));
    }

    public function edit($id)
    {
        $bill = SupplierBill::findOrFail($id);
        $suppliers = Supplier::orderBy('name')->get();

        return view('financing.bills.edit', compact('bill', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $bill = SupplierBill::findOrFail($id);
        $data = $this->validated($request);

        if ((float) $data['amount'] + 0.0001 < (float) $bill->paid_amount) {
            return back()->withErrors([
                'amount' => __('financing::financing.bill_amount_below_paid'),
            ])->withInput();
        }

        $bill->update($data);
        $bill->recalculateStatus();

        session()->flash('success', __('financing::financing.saved'));

        return redirect()->route('financing.bills.show', $bill->id);
    }

    public function pay(Request $request, $id, FinanceLedgerService $ledger)
    {
        $bill = SupplierBill::findOrFail($id);

        $data = $request->validate([
            'account_id' => 'required|exists:finance_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        if ((float) $data['amount'] - $bill->remaining_amount > 0.0001) {
            return back()->withErrors([
                'amount' => __('financing::financing.payment_exceeds_balance'),
            ]);
        }

        DB::transaction(function () use ($bill, $data, $ledger) {
            $payment = SupplierBillPayment::create([
                'bill_id' => $bill->id,
                'account_id' => $data['account_id'],
                'amount' => $data['amount'],
                'date' => $data['date'],
                'notes' => $data['notes'] ?? null,
            ]);

            $ledger->sync(
                $payment->account_id,
                'outflow',
                $payment->amount,
                $payment->date,
                'Supplier bill #' . $bill->id . ' payment',
                'supplier_bill_payment',
                $payment->id,
                auth()->id()
            );

            $bill->recalculateStatus();
        });

        session()->flash('success', __('financing::financing.payment_recorded'));

        return redirect()->route('financing.bills.show', $bill->id);
    }

    public function destroy($id)
    {
        $bill = SupplierBill::findOrFail($id);

        if ($bill->payments()->exists()) {
            return back()->withErrors([
                'bill' => __('financing::financing.bill_with_payments_cannot_delete'),
            ]);
        }

        $bill->delete();

        session()->flash('success', __('financing::financing.deleted'));

        return redirect()->route('financing.bills.index');
    }

    protected function validated(Request $request)
    {
        return $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'amount' => 'required|numeric|min:0.01',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);
    }
}
