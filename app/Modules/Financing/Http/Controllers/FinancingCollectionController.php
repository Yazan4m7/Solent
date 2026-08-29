<?php

namespace App\Modules\Financing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Financing\Models\DriverCollection;
use App\Modules\Financing\Models\FinanceAccount;
use App\Modules\Financing\Services\FinanceLedgerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancingCollectionController extends Controller
{
    public function index()
    {
        $collections = DriverCollection::with(['user', 'invoice', 'account'])
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('user_id');

        $accounts = FinanceAccount::where('is_active', true)->orderBy('name')->get();

        return view('financing.collections.index', compact('collections', 'accounts'));
    }

    public function myCollections()
    {
        $rows = DriverCollection::with(['invoice', 'account'])
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();

        $accounts = FinanceAccount::where('is_active', true)->orderBy('name')->get();

        return view('financing.collections.mine', compact('rows', 'accounts'));
    }

    public function submit(Request $request, $id, FinanceLedgerService $ledger)
    {
        $collection = DriverCollection::findOrFail($id);

        return $this->reconcile($request, $collection, $ledger);
    }

    public function submitMine(Request $request, $id, FinanceLedgerService $ledger)
    {
        $collection = DriverCollection::where('user_id', auth()->id())->findOrFail($id);

        return $this->reconcile($request, $collection, $ledger);
    }

    protected function reconcile(Request $request, DriverCollection $collection, FinanceLedgerService $ledger)
    {
        $data = $request->validate([
            'submitted_amount' => 'required|numeric|min:0.01',
            'account_id' => 'required|exists:finance_accounts,id',
            'notes' => 'nullable|string',
        ]);

        if ((float) $data['submitted_amount'] - (float) $collection->collected_amount > 0.0001) {
            return back()->withErrors([
                'submitted_amount' => __('financing::financing.submission_exceeds_collection'),
            ]);
        }

        DB::transaction(function () use ($collection, $data, $ledger) {
            $collection->submitted_amount = $data['submitted_amount'];
            $collection->account_id = $data['account_id'];
            $collection->notes = $data['notes'] ?? $collection->notes;
            $collection->submitted_at = ((float) $data['submitted_amount'] + 0.0001 >= (float) $collection->collected_amount)
                ? now()
                : null;
            $collection->save();

            $ledger->sync(
                $collection->account_id,
                'inflow',
                $collection->submitted_amount,
                today(),
                'Driver collection #' . $collection->id,
                'driver_collection',
                $collection->id,
                auth()->id()
            );
        });

        session()->flash('success', __('financing::financing.collection_reconciled'));

        return back();
    }
}
