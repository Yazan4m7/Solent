<?php

namespace App\Modules\Financing\Services;

use App\Modules\Financing\Models\FinanceAccount;
use App\Modules\Financing\Models\FinanceAccountTransaction;
use Illuminate\Support\Facades\DB;

class FinanceLedgerService
{
    public function sync($accountId, $direction, $amount, $date, $description, $sourceType, $sourceId, $createdBy = null)
    {
        return DB::transaction(function () use ($accountId, $direction, $amount, $date, $description, $sourceType, $sourceId, $createdBy) {
            // A source must exist only once in the ledger. Remove any old account mapping first.
            $old = FinanceAccountTransaction::withTrashed()
                ->where('source_type', $sourceType)
                ->where('source_id', $sourceId)
                ->first();

            $oldAccountId = $old ? $old->account_id : null;

            if ($old && $old->account_id != $accountId) {
                $old->forceDelete();
                $old = null;
            }

            if (! $old) {
                $old = new FinanceAccountTransaction();
            } elseif ($old->trashed()) {
                $old->restore();
            }

            $old->account_id = $accountId;
            $old->direction = $direction;
            $old->amount = $amount;
            $old->date = $date;
            $old->description = $description;
            $old->source_type = $sourceType;
            $old->source_id = $sourceId;
            $old->created_by = $createdBy;
            $old->save();

            if ($oldAccountId && $oldAccountId != $accountId) {
                $this->recalculateAccount($oldAccountId);
            }

            $this->recalculateAccount($accountId);

            return $old;
        });
    }

    public function removeSource($sourceType, $sourceId)
    {
        return DB::transaction(function () use ($sourceType, $sourceId) {
            $transactions = FinanceAccountTransaction::where('source_type', $sourceType)
                ->where('source_id', $sourceId)
                ->get();

            $accountIds = $transactions->pluck('account_id')->unique()->values();

            foreach ($transactions as $transaction) {
                $transaction->delete();
            }

            foreach ($accountIds as $accountId) {
                $this->recalculateAccount($accountId);
            }
        });
    }

    public function recalculateAccount($accountId)
    {
        $account = FinanceAccount::withTrashed()->find($accountId);

        if (! $account) {
            return;
        }

        $inflows = FinanceAccountTransaction::where('account_id', $accountId)
            ->where('direction', 'inflow')
            ->sum('amount');

        $outflows = FinanceAccountTransaction::where('account_id', $accountId)
            ->where('direction', 'outflow')
            ->sum('amount');

        $account->balance = round((float) $inflows - (float) $outflows, 2);
        $account->save();
    }
}
