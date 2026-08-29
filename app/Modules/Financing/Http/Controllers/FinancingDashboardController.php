<?php

namespace App\Modules\Financing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Financing\Models\DriverCollection;
use App\Modules\Financing\Models\Expense;
use App\Modules\Financing\Models\ExpenseCategory;
use App\Modules\Financing\Models\InvoiceInstallment;
use App\Modules\Financing\Models\PayrollRun;
use App\Modules\Financing\Models\SupplierBill;
use Carbon\Carbon;

class FinancingDashboardController extends Controller
{
    public function index()
    {
        $start = now()->startOfMonth()->toDateString();
        $end = now()->endOfMonth()->toDateString();
        $invoiceModel = config('modules.financing.models.invoice');

        $revenue = $invoiceModel::whereBetween('date_applied', [$start, $end])->sum('amount');

        $payrollCategoryId = ExpenseCategory::where('name', 'Payroll')->value('id');

        $expenses = Expense::whereBetween('date', [$start, $end])
            ->when($payrollCategoryId, function ($query) use ($payrollCategoryId) {
                $query->where('category_id', '!=', $payrollCategoryId);
            })
            ->sum('amount');

        $payroll = PayrollRun::whereNotNull('posted_at')
            ->where('period_month', now()->month)
            ->where('period_year', now()->year)
            ->sum('total');

        $overdueInstallments = InvoiceInstallment::whereNull('paid_at')
            ->whereDate('due_date', '<', today())
            ->count();

        $overdueSupplierBills = SupplierBill::where('status', '!=', 'paid')
            ->whereDate('due_date', '<', today())
            ->count();

        $unsubmittedCollections = DriverCollection::where(function ($query) {
                $query->whereNull('submitted_at')
                    ->orWhereColumn('submitted_amount', '<', 'collected_amount');
            })
            ->count();

        $collectionAlertCount = DriverCollection::where('created_at', '<', now()->subDays(3))
            ->where(function ($query) {
                $query->whereNull('submitted_at')
                    ->orWhereColumn('submitted_amount', '<', 'collected_amount');
            })
            ->count();

        return view('financing.dashboard', compact(
            'revenue',
            'expenses',
            'payroll',
            'overdueInstallments',
            'overdueSupplierBills',
            'unsubmittedCollections',
            'collectionAlertCount'
        ));
    }
}
