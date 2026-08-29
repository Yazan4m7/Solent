<?php

namespace App\Modules\Financing\Services;

use App\Modules\Financing\Models\DriverCollection;

class FinancingCollectionService
{
    public function captureFromCompletedJob($job)
    {
        if ((string) setting('module_financing', '0') !== '1') {
            return;
        }

        if ((int) $job->stage !== -1 || empty($job->delivery_accepted) || empty($job->case_id)) {
            return;
        }

        $invoiceModel = config('modules.financing.models.invoice');

        if (! $invoiceModel || ! class_exists($invoiceModel)) {
            return;
        }

        $invoice = $invoiceModel::where('case_id', $job->case_id)
            ->orderByDesc('id')
            ->first();

        if (! $invoice) {
            return;
        }

        DriverCollection::firstOrCreate(
            [
                'user_id' => $job->delivery_accepted,
                'invoice_id' => $invoice->id,
            ],
            [
                'collected_amount' => $invoice->amount,
                'submitted_amount' => 0,
                'submitted_at' => null,
            ]
        );
    }
}
