<?php

namespace App\Modules\Financing\Services;

use App\Modules\Financing\Models\FinancePaymentAccount;
use Illuminate\Support\Facades\DB;

class FinancePaymentService
{
    public function linkPaymentToAccount($payment, $accountId, FinanceLedgerService $ledger)
    {
        return DB::transaction(function () use ($payment, $accountId, $ledger) {
            $link = FinancePaymentAccount::withTrashed()
                ->where('payment_id', $payment->id)
                ->first();

            if (! $link) {
                $link = new FinancePaymentAccount();
                $link->payment_id = $payment->id;
            } elseif ($link->trashed()) {
                $link->restore();
            }

            $link->account_id = $accountId;
            $link->save();

            $date = isset($payment->created_at) && $payment->created_at
                ? $payment->created_at->toDateString()
                : today()->toDateString();

            $ledger->sync(
                $accountId,
                'inflow',
                $payment->amount,
                $date,
                'Client payment #' . $payment->id,
                'payment',
                $payment->id,
                auth()->id()
            );

            return $link;
        });
    }
}
