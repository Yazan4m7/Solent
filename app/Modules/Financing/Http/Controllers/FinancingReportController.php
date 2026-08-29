<?php

namespace App\Modules\Financing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Financing\Models\Expense;
use App\Modules\Financing\Models\ExpenseCategory;
use App\Modules\Financing\Models\PayrollRun;
use App\Modules\Financing\Services\FinancingExcelService;
use App\Modules\Financing\Services\FinancingPdfService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinancingReportController extends Controller
{
    public function pl(Request $request)
    {
        $report = $this->buildReport($request);

        return view('financing.reports.pl', $report);
    }

    public function plPdf(Request $request, FinancingPdfService $pdf)
    {
        $report = $this->buildReport($request);

        return $pdf->download(
            'financing.reports.pl-pdf',
            $report,
            'profit-loss-' . $report['from']->format('Y-m-d') . '-' . $report['to']->format('Y-m-d') . '.pdf',
            'a4',
            'portrait'
        );
    }

    public function plExcel(Request $request, FinancingExcelService $excel)
    {
        $report = $this->buildReport($request);

        $rows = [
            ['Revenue', number_format($report['revenue'], 2)],
            ['Operating Expenses', number_format($report['expenses'], 2)],
            ['Payroll', number_format($report['payroll'], 2)],
            ['Net', number_format($report['net'], 2)],
            ['', ''],
            ['Expense Category', 'Amount'],
        ];

        foreach ($report['breakdown'] as $name => $amount) {
            $rows[] = [$name, number_format($amount, 2)];
        }

        return $excel->download(
            'profit-loss-' . $report['from']->format('Y-m-d') . '-' . $report['to']->format('Y-m-d') . '.xls',
            ['Metric', 'Amount (' . config('modules.financing.currency', 'JOD') . ')'],
            $rows
        );
    }

    protected function buildReport(Request $request)
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : now()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : now()->endOfMonth();

        if ($from->gt($to)) {
            abort(422, 'Invalid date range.');
        }

        $invoiceModel = config('modules.financing.models.invoice');
        $revenue = (float) $invoiceModel::whereBetween('date_applied', [
            $from->toDateString(),
            $to->toDateString(),
        ])->sum('amount');

        // Payroll creates an expense for cash-ledger purposes. Exclude that category here,
        // then add posted payroll separately so payroll is not double-counted.
        $payrollCategoryId = ExpenseCategory::where('name', 'Payroll')->value('id');

        $expenseRows = Expense::with('category')
            ->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->when($payrollCategoryId, function ($query) use ($payrollCategoryId) {
                $query->where('category_id', '!=', $payrollCategoryId);
            })
            ->get();

        $expenses = (float) $expenseRows->sum('amount');

        $payroll = (float) PayrollRun::whereNotNull('posted_at')
            ->get()
            ->filter(function ($run) use ($from, $to) {
                $date = Carbon::create($run->period_year, $run->period_month, 1);
                return $date->between($from->copy()->startOfMonth(), $to->copy()->endOfMonth());
            })
            ->sum('total');

        $breakdown = $expenseRows
            ->groupBy(function ($expense) {
                return optional($expense->category)->name ?: 'Uncategorized';
            })
            ->map(function ($group) {
                return (float) $group->sum('amount');
            })
            ->sortDesc();

        $net = $revenue - $expenses - $payroll;

        return compact('from', 'to', 'revenue', 'expenses', 'payroll', 'net', 'breakdown');
    }
}
