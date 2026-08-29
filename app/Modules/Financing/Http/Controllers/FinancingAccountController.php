<?php

namespace App\Modules\Financing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Financing\Models\FinanceAccount;
use App\Modules\Financing\Services\FinanceLedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancingAccountController extends Controller
{
    public function index()
    {
        $accounts = FinanceAccount::orderBy('is_active', 'desc')
            ->orderBy('name')
            ->get();

        return view('financing.accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('financing.accounts.create');
    }

    public function store(Request $request, FinanceLedgerService $ledger)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:cash,bank',
            'currency' => 'required|string|max:10',
            'opening_balance' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $account = DB::transaction(function () use ($data, $ledger) {
            $account = FinanceAccount::create([
                'name' => $data['name'],
                'type' => $data['type'],
                'currency' => $data['currency'],
                'balance' => 0,
                'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : true,
            ]);

            $opening = (float) ($data['opening_balance'] ?? 0);

            if ($opening > 0) {
                $ledger->sync(
                    $account->id,
                    'inflow',
                    $opening,
                    today(),
                    'Opening balance',
                    'opening_balance',
                    $account->id,
                    auth()->id()
                );
            }

            return $account;
        });

        session()->flash('success', __('financing::financing.saved'));

        return redirect()->route('financing.accounts.show', $account->id);
    }

    public function show($id)
    {
        $account = FinanceAccount::findOrFail($id);
        $transactions = $account->transactions()
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(50);

        return view('financing.accounts.show', compact('account', 'transactions'));
    }

    public function edit($id)
    {
        $account = FinanceAccount::findOrFail($id);

        return view('financing.accounts.edit', compact('account'));
    }

    public function update(Request $request, $id)
    {
        $account = FinanceAccount::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:cash,bank',
            'currency' => 'required|string|max:10',
            'is_active' => 'nullable|boolean',
        ]);

        $account->update([
            'name' => $data['name'],
            'type' => $data['type'],
            'currency' => $data['currency'],
            'is_active' => $request->boolean('is_active'),
        ]);

        session()->flash('success', __('financing::financing.saved'));

        return redirect()->route('financing.accounts.show', $account->id);
    }

    public function destroy($id)
    {
        $account = FinanceAccount::findOrFail($id);

        if ($account->transactions()->exists()) {
            return back()->withErrors([
                'account' => __('financing::financing.account_with_history_cannot_delete'),
            ]);
        }

        $account->delete();

        session()->flash('success', __('financing::financing.deleted'));

        return redirect()->route('financing.accounts.index');
    }
}
