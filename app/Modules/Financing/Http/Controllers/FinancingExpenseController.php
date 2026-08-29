<?php

namespace App\Modules\Financing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Financing\Models\Expense;
use App\Modules\Financing\Models\ExpenseCategory;
use App\Modules\Financing\Models\FinanceAccount;
use App\Modules\Financing\Services\FinanceLedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FinancingExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['category', 'account', 'creator'])->orderByDesc('date')->orderByDesc('id');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $expenses = $query->paginate(40)->appends($request->query());

        $monthTotal = Expense::whereBetween('date', [
                now()->startOfMonth()->toDateString(),
                now()->endOfMonth()->toDateString(),
            ])
            ->sum('amount');

        $categories = ExpenseCategory::orderBy('name')->get();
        $accounts = FinanceAccount::where('is_active', true)->orderBy('name')->get();

        return view('financing.expenses.index', compact(
            'expenses', 'monthTotal', 'categories', 'accounts'
        ));
    }

    public function create()
    {
        $categories = ExpenseCategory::orderBy('name')->get();
        $accounts = FinanceAccount::where('is_active', true)->orderBy('name')->get();

        return view('financing.expenses.create', compact('categories', 'accounts'));
    }

    public function store(Request $request, FinanceLedgerService $ledger)
    {
        $data = $this->validated($request);
        $data['created_by'] = auth()->id();
        $data['is_recurring'] = $request->boolean('is_recurring');
        $data['recurring_day'] = $data['is_recurring'] ? ($data['recurring_day'] ?: date('j', strtotime($data['date']))) : null;

        if ($request->hasFile('receipt')) {
            $data['receipt_path'] = Storage::disk('local')->put('financing/receipts', $request->file('receipt'));
        }

        unset($data['receipt']);

        $expense = DB::transaction(function () use ($data, $ledger) {
            $expense = Expense::create($data);

            $ledger->sync(
                $expense->account_id,
                'outflow',
                $expense->amount,
                $expense->date,
                $expense->description ?: ('Expense #' . $expense->id),
                'expense',
                $expense->id,
                $expense->created_by
            );

            return $expense;
        });

        session()->flash('success', __('financing::financing.saved'));

        return redirect()->route('financing.expenses.index');
    }

    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        $categories = ExpenseCategory::orderBy('name')->get();
        $accounts = FinanceAccount::where('is_active', true)
            ->orWhere('id', $expense->account_id)
            ->orderBy('name')
            ->get();

        return view('financing.expenses.edit', compact('expense', 'categories', 'accounts'));
    }

    public function update(Request $request, $id, FinanceLedgerService $ledger)
    {
        $expense = Expense::findOrFail($id);
        $data = $this->validated($request);
        $data['is_recurring'] = $request->boolean('is_recurring');
        $data['recurring_day'] = $data['is_recurring'] ? ($data['recurring_day'] ?: date('j', strtotime($data['date']))) : null;

        if ($request->hasFile('receipt')) {
            if ($expense->receipt_path) {
                Storage::disk('local')->delete($expense->receipt_path);
            }

            $data['receipt_path'] = Storage::disk('local')->put('financing/receipts', $request->file('receipt'));
        }

        unset($data['receipt']);

        DB::transaction(function () use ($expense, $data, $ledger) {
            $expense->update($data);

            $ledger->sync(
                $expense->account_id,
                'outflow',
                $expense->amount,
                $expense->date,
                $expense->description ?: ('Expense #' . $expense->id),
                'expense',
                $expense->id,
                $expense->created_by
            );
        });

        session()->flash('success', __('financing::financing.saved'));

        return redirect()->route('financing.expenses.index');
    }


    public function receipt($id)
    {
        $expense = Expense::findOrFail($id);

        abort_unless($expense->receipt_path && Storage::disk('local')->exists($expense->receipt_path), 404);

        return Storage::disk('local')->download($expense->receipt_path);
    }

    public function destroy($id, FinanceLedgerService $ledger)
    {
        $expense = Expense::findOrFail($id);

        DB::transaction(function () use ($expense, $ledger) {
            $ledger->removeSource('expense', $expense->id);
            $expense->delete();
        });

        session()->flash('success', __('financing::financing.deleted'));

        return redirect()->route('financing.expenses.index');
    }

    protected function validated(Request $request)
    {
        return $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'account_id' => 'required|exists:finance_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'receipt' => 'nullable|file|max:10240',
            'is_recurring' => 'nullable|boolean',
            'recurring_day' => 'nullable|integer|min:1|max:31',
        ]);
    }
}
