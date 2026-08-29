<?php

namespace App\Modules\Financing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Financing\Models\EmployeeSalary;
use App\Modules\Financing\Models\Expense;
use App\Modules\Financing\Models\ExpenseCategory;
use App\Modules\Financing\Models\FinanceAccount;
use App\Modules\Financing\Models\PayrollLine;
use App\Modules\Financing\Models\PayrollRun;
use App\Modules\Financing\Services\FinanceLedgerService;
use App\Modules\Financing\Services\FinancingPdfService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancingPayrollController extends Controller
{
    public function index()
    {
        $runs = PayrollRun::withCount('lines')
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->paginate(30);

        return view('financing.payroll.index', compact('runs'));
    }

    public function create()
    {
        return view('financing.payroll.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'period_month' => 'required|integer|min:1|max:12',
            'period_year' => 'required|integer|min:2000|max:2100',
            'notes' => 'nullable|string',
        ]);

        if (PayrollRun::where('period_month', $data['period_month'])
            ->where('period_year', $data['period_year'])
            ->exists()) {
            return back()->withErrors([
                'period_month' => __('financing::financing.payroll_period_exists'),
            ])->withInput();
        }

        $salaryRows = EmployeeSalary::with('user')->get();

        if ($salaryRows->isEmpty()) {
            return back()->withErrors([
                'period_month' => __('financing::financing.no_salary_config'),
            ]);
        }

        $run = DB::transaction(function () use ($data, $salaryRows) {
            $run = PayrollRun::create([
                'period_month' => $data['period_month'],
                'period_year' => $data['period_year'],
                'notes' => $data['notes'] ?? null,
                'total' => 0,
                'created_by' => auth()->id(),
            ]);

            foreach ($salaryRows as $salary) {
                PayrollLine::create([
                    'run_id' => $run->id,
                    'user_id' => $salary->user_id,
                    'base_salary' => $salary->base_salary,
                    'bonus' => 0,
                    'deductions' => 0,
                    'net' => $salary->base_salary,
                ]);
            }

            $run->total = $run->lines()->sum('net');
            $run->save();

            return $run;
        });

        session()->flash('success', __('financing::financing.payroll_created'));

        return redirect()->route('financing.payroll.show', $run->id);
    }

    public function show($id)
    {
        $run = PayrollRun::with('lines.user')->findOrFail($id);
        $accounts = FinanceAccount::where('is_active', true)->orderBy('name')->get();

        return view('financing.payroll.show', compact('run', 'accounts'));
    }

    public function updateLines(Request $request, $id)
    {
        $run = PayrollRun::with('lines')->findOrFail($id);

        if ($run->posted_at) {
            abort(409, 'Posted payroll runs are locked.');
        }

        $data = $request->validate([
            'lines' => 'required|array',
            'lines.*.bonus' => 'nullable|numeric|min:0',
            'lines.*.deductions' => 'nullable|numeric|min:0',
            'lines.*.notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($run, $data) {
            foreach ($run->lines as $line) {
                if (! isset($data['lines'][$line->id])) {
                    continue;
                }

                $row = $data['lines'][$line->id];
                $line->bonus = (float) ($row['bonus'] ?? 0);
                $line->deductions = (float) ($row['deductions'] ?? 0);
                $line->notes = $row['notes'] ?? null;
                $line->net = $line->calculateNet();
                $line->save();
            }

            $run->total = $run->lines()->sum('net');
            $run->save();
        });

        session()->flash('success', __('financing::financing.saved'));

        return redirect()->route('financing.payroll.show', $run->id);
    }

    public function post(Request $request, $id, FinanceLedgerService $ledger)
    {
        abort_unless((bool) auth()->user()->is_admin, 403);

        $run = PayrollRun::with('lines')->findOrFail($id);

        if ($run->posted_at) {
            return back()->withErrors(['payroll' => __('financing::financing.payroll_already_posted')]);
        }

        $data = $request->validate([
            'account_id' => 'required|exists:finance_accounts,id',
        ]);

        DB::transaction(function () use ($run, $data, $ledger) {
            foreach ($run->lines as $line) {
                $line->net = $line->calculateNet();
                $line->save();
            }

            $run->total = $run->lines()->sum('net');
            $run->posted_at = now();
            $run->save();

            $category = ExpenseCategory::firstOrCreate(['name' => 'Payroll']);
            $periodDate = Carbon::create($run->period_year, $run->period_month, 1)->endOfMonth();

            $expense = Expense::create([
                'category_id' => $category->id,
                'account_id' => $data['account_id'],
                'amount' => $run->total,
                'description' => 'Payroll ' . str_pad($run->period_month, 2, '0', STR_PAD_LEFT) . '/' . $run->period_year . ' - run #' . $run->id,
                'date' => $periodDate->toDateString(),
                'is_recurring' => false,
                'created_by' => auth()->id(),
            ]);

            $ledger->sync(
                $expense->account_id,
                'outflow',
                $expense->amount,
                $expense->date,
                $expense->description,
                'expense',
                $expense->id,
                auth()->id()
            );
        });

        session()->flash('success', __('financing::financing.payroll_posted'));

        return redirect()->route('financing.payroll.show', $run->id);
    }

    public function salaries()
    {
        $userModel = config('modules.financing.models.user');
        $users = $userModel::orderBy('name')->get();
        $salaryMap = EmployeeSalary::all()->keyBy('user_id');

        return view('financing.payroll.salaries', compact('users', 'salaryMap'));
    }

    public function saveSalaries(Request $request)
    {
        $data = $request->validate([
            'salaries' => 'required|array',
            'salaries.*' => 'nullable|numeric|min:0',
        ]);

        foreach ($data['salaries'] as $userId => $amount) {
            if ($amount === null || $amount === '') {
                EmployeeSalary::where('user_id', $userId)->delete();
                continue;
            }

            $salary = EmployeeSalary::withTrashed()->where('user_id', $userId)->first();

            if (! $salary) {
                $salary = new EmployeeSalary();
                $salary->user_id = $userId;
            } elseif ($salary->trashed()) {
                $salary->restore();
            }

            $salary->base_salary = $amount;
            $salary->save();
        }

        session()->flash('success', __('financing::financing.saved'));

        return redirect()->route('financing.payroll.salaries');
    }

    public function payslip($runId, $lineId, FinancingPdfService $pdf)
    {
        $run = PayrollRun::findOrFail($runId);
        $line = PayrollLine::with('user')
            ->where('run_id', $run->id)
            ->findOrFail($lineId);

        return $pdf->download(
            'financing.payroll.payslip',
            compact('run', 'line'),
            'payslip-' . $run->period_year . '-' . $run->period_month . '-' . $line->user_id . '.pdf'
        );
    }
}
