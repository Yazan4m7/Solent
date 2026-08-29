<?php

namespace App\Modules\Financing\Console\Commands;

use App\Modules\Financing\Models\Expense;
use App\Modules\Financing\Services\FinanceLedgerService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateRecurringExpenses extends Command
{
    protected $signature = 'financing:generate-recurring {--month=} {--year=}';
    protected $description = 'Generate recurring financing expenses for a month';

    public function handle(FinanceLedgerService $ledger)
    {
        if ((string) setting('module_financing', '0') !== '1') {
            $this->info('Financing module disabled.');
            return 0;
        }

        $month = (int) ($this->option('month') ?: now()->month);
        $year = (int) ($this->option('year') ?: now()->year);

        $templates = Expense::where('is_recurring', true)
            ->whereNull('recurring_parent_id')
            ->get();

        $created = 0;

        foreach ($templates as $template) {
            $day = min((int) ($template->recurring_day ?: $template->date->day), Carbon::create($year, $month, 1)->daysInMonth);
            $date = Carbon::create($year, $month, $day)->toDateString();

            if ($template->date->format('Y-m') >= Carbon::create($year, $month, 1)->format('Y-m')) {
                continue;
            }

            DB::transaction(function () use ($template, $date, $ledger, &$created) {
                $expense = Expense::firstOrCreate(
                    [
                        'recurring_parent_id' => $template->id,
                        'date' => $date,
                    ],
                    [
                        'category_id' => $template->category_id,
                        'account_id' => $template->account_id,
                        'amount' => $template->amount,
                        'description' => $template->description,
                        'receipt_path' => null,
                        'is_recurring' => false,
                        'recurring_day' => null,
                        'created_by' => $template->created_by,
                    ]
                );

                if ($expense->wasRecentlyCreated) {
                    $ledger->sync(
                        $expense->account_id,
                        'outflow',
                        $expense->amount,
                        $expense->date,
                        'Recurring expense #' . $expense->id,
                        'expense',
                        $expense->id,
                        $expense->created_by
                    );
                    $created++;
                }
            });
        }

        $this->info($created . ' recurring expense(s) generated.');
        return 0;
    }
}
