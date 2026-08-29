<?php

use App\Modules\Financing\Http\Controllers\AdminModuleSettingController;
use App\Modules\Financing\Http\Controllers\FinancingAccountController;
use App\Modules\Financing\Http\Controllers\FinancingBillController;
use App\Modules\Financing\Http\Controllers\FinancingCollectionController;
use App\Modules\Financing\Http\Controllers\FinancingDashboardController;
use App\Modules\Financing\Http\Controllers\FinancingExpenseController;
use App\Modules\Financing\Http\Controllers\FinancingInstallmentController;
use App\Modules\Financing\Http\Controllers\FinancingPayrollController;
use App\Modules\Financing\Http\Controllers\FinancingReportController;
use App\Modules\Financing\Http\Controllers\FinancingSupplierController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin module toggle
|--------------------------------------------------------------------------
| Must stay outside module:financing so an admin can re-enable the module.
*/
Route::post('/admin/settings/module', 'AdminModuleSettingController@update')
    ->middleware(['auth', 'admin'])
    ->name('admin.settings.module.update');

/*
|--------------------------------------------------------------------------
| Driver self-service collection reconciliation
|--------------------------------------------------------------------------
| A driver can access only their own collection records. Finance staff use
| the full collection screen below.
*/
Route::middleware(['auth', 'module:financing'])
    ->prefix('financing')
    ->name('financing.')
    ->group(function () {
        Route::get('/my-collections', 'FinancingCollectionController@myCollections')
            ->name('collections.mine');
        Route::post('/my-collections/{id}/submit', 'FinancingCollectionController@submitMine')
            ->name('collections.mine.submit');
    });

/*
|--------------------------------------------------------------------------
| Finance staff
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'module:financing', 'financing.access'])
    ->prefix('financing')
    ->name('financing.')
    ->group(function () {
        Route::get('/', 'FinancingDashboardController@index')->name('dashboard');

        // Accounts
        Route::get('/accounts', 'FinancingAccountController@index')->name('accounts.index');
        Route::get('/accounts/create', 'FinancingAccountController@create')->name('accounts.create');
        Route::post('/accounts', 'FinancingAccountController@store')->name('accounts.store');
        Route::get('/accounts/{id}', 'FinancingAccountController@show')->name('accounts.show');
        Route::get('/accounts/{id}/edit', 'FinancingAccountController@edit')->name('accounts.edit');
        Route::post('/accounts/{id}', 'FinancingAccountController@update')->name('accounts.update');
        Route::delete('/accounts/{id}', 'FinancingAccountController@destroy')->name('accounts.destroy');

        // Expenses
        Route::get('/expenses', 'FinancingExpenseController@index')->name('expenses.index');
        Route::get('/expenses/create', 'FinancingExpenseController@create')->name('expenses.create');
        Route::post('/expenses', 'FinancingExpenseController@store')->name('expenses.store');
        Route::get('/expenses/{id}/receipt', 'FinancingExpenseController@receipt')->name('expenses.receipt');
        Route::get('/expenses/{id}/edit', 'FinancingExpenseController@edit')->name('expenses.edit');
        Route::post('/expenses/{id}', 'FinancingExpenseController@update')->name('expenses.update');
        Route::delete('/expenses/{id}', 'FinancingExpenseController@destroy')->name('expenses.destroy');

        // Payroll
        Route::get('/payroll', 'FinancingPayrollController@index')->name('payroll.index');
        Route::get('/payroll/create', 'FinancingPayrollController@create')->name('payroll.create');
        Route::post('/payroll', 'FinancingPayrollController@store')->name('payroll.store');
        Route::get('/payroll/salaries', 'FinancingPayrollController@salaries')->name('payroll.salaries');
        Route::post('/payroll/salaries', 'FinancingPayrollController@saveSalaries')->name('payroll.salaries.save');
        Route::get('/payroll/{id}', 'FinancingPayrollController@show')->name('payroll.show');
        Route::post('/payroll/{id}/lines', 'FinancingPayrollController@updateLines')->name('payroll.lines.update');
        Route::post('/payroll/{id}/post', 'FinancingPayrollController@post')->middleware('admin')->name('payroll.post');
        Route::get('/payroll/{runId}/payslip/{lineId}', 'FinancingPayrollController@payslip')->name('payroll.payslip');

        // Installments
        Route::get('/installments', 'FinancingInstallmentController@index')->name('installments.index');
        Route::post('/installments/{invoiceId}/create', 'FinancingInstallmentController@create')->name('installments.create');
        Route::post('/installments/{id}/pay', 'FinancingInstallmentController@pay')->name('installments.pay');

        // Suppliers
        Route::get('/suppliers', 'FinancingSupplierController@index')->name('suppliers.index');
        Route::get('/suppliers/create', 'FinancingSupplierController@create')->name('suppliers.create');
        Route::post('/suppliers', 'FinancingSupplierController@store')->name('suppliers.store');
        Route::get('/suppliers/{id}', 'FinancingSupplierController@show')->name('suppliers.show');
        Route::get('/suppliers/{id}/edit', 'FinancingSupplierController@edit')->name('suppliers.edit');
        Route::post('/suppliers/{id}', 'FinancingSupplierController@update')->name('suppliers.update');
        Route::delete('/suppliers/{id}', 'FinancingSupplierController@destroy')->name('suppliers.destroy');

        // Supplier bills
        Route::get('/bills', 'FinancingBillController@index')->name('bills.index');
        Route::get('/bills/create', 'FinancingBillController@create')->name('bills.create');
        Route::post('/bills', 'FinancingBillController@store')->name('bills.store');
        Route::get('/bills/{id}', 'FinancingBillController@show')->name('bills.show');
        Route::get('/bills/{id}/edit', 'FinancingBillController@edit')->name('bills.edit');
        Route::post('/bills/{id}', 'FinancingBillController@update')->name('bills.update');
        Route::post('/bills/{id}/pay', 'FinancingBillController@pay')->name('bills.pay');
        Route::delete('/bills/{id}', 'FinancingBillController@destroy')->name('bills.destroy');

        // Driver collections
        Route::get('/collections', 'FinancingCollectionController@index')->name('collections.index');
        Route::post('/collections/{id}/submit', 'FinancingCollectionController@submit')->name('collections.submit');

        // P&L
        Route::get('/reports/pl', 'FinancingReportController@pl')->name('reports.pl');
        Route::get('/reports/pl/pdf', 'FinancingReportController@plPdf')->name('reports.pl.pdf');
        Route::get('/reports/pl/excel', 'FinancingReportController@plExcel')->name('reports.pl.excel');
    });
