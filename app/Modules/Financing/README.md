# SIGMA Financing Module

Standalone Laravel/Blade financing module for SIGMA. It does not alter the structure of any existing SIGMA table.

## Included

- Module setting + toggle
- `module:financing` middleware
- Finance accounts + transaction ledger
- Expense tracking + private receipt downloads
- Recurring expenses command
- Payroll + salary configuration + posted/locked runs + payslip PDF
- Invoice installments + payment creation
- Suppliers + AP bills + partial bill payments
- Driver collection reconciliation
- P&L report + PDF + Excel-compatible export
- Arabic + English language files
- Separate `public/css/financing.css`
- Sidebar and invoice-detail partials

## Two additional new tables

The original prompt says existing tables cannot be modified while account balances must track inflows/outflows.

For that reason the module adds:

- `finance_account_transactions`: immutable-style account ledger used to calculate account balances.
- `finance_payment_accounts`: maps an existing `payments.id` to a finance account without adding `account_id` to the existing `payments` table.

These are new tables only; existing tables remain unchanged.

## Install

Copy the module tree into the matching SIGMA directories.

### 1. Register provider

Add to `config/app.php` providers:

```php
App\Providers\FinancingServiceProvider::class,
```

The provider loads `app/Helpers/Setting.php`, registers the recurring command, and listens for completed delivery jobs without editing the existing Job model/controller.

### 2. Register middleware alias

In `app/Http/Kernel.php`:

```php
'module' => \App\Http\Middleware\ModuleEnabled::class,
```

The routes intentionally use the requested existing middleware:

```php
financing.access
```

If SIGMA's role middleware alias/name differs, change only that middleware string in `routes/financing.php`.

### 3. Load routes

At the bottom of the existing `routes/web.php`:

```php
require base_path('routes/financing.php');
```

### 4. Existing model namespaces

The uploaded archives available while this module was generated contain public CSS/assets, not SIGMA's Laravel `app/` source. Therefore the existing model namespaces could not be verified.

Defaults are in `config/financing.php`:

```php
'models' => [
    'user' => 'App\\User',
    'client' => 'App\\Client',
    'invoice' => 'App\\Invoice',
    'payment' => 'App\\Payment',
    'job' => 'App\\Job',
],
```

If SIGMA uses `App\Models\User`, etc., change those five values only.

### 5. Seed + migrate

```bash
php artisan migrate
php artisan db:seed --class="Database\\Seeders\\FinancingSeeder"
```

The seed includes:

- Materials
- Utilities
- Rent
- Maintenance
- External Lab
- Other
- Payroll

`Payroll` is added because posting a payroll run must create a Payroll expense.

### 6. Schedule recurring expenses

In `app/Console/Kernel.php`:

```php
protected function schedule(\Illuminate\Console\Scheduling\Schedule $schedule)
{
    // existing schedules...

    $schedule->command('financing:generate-recurring')
        ->monthlyOn(1, '00:10');
}
```

The command is idempotent per recurring template/month.

### 7. Sidebar

Inside the existing sidebar:

```blade
@include('financing.partials.sidebar-links')
```

It appears only when `module_financing = 1` and shows a badge for driver collections older than 3 days that are still not fully submitted.

### 8. Admin settings page

Inside the admin settings view:

```blade
@include('admin.settings.partials.financing-toggle')
```

The POST endpoint is exactly:

```text
/admin/settings/module
```

### 9. Invoice detail page

Where `$invoice` is available:

```blade
@include('financing.partials.invoice-installments-button', ['invoice' => $invoice])
```

This adds the Split into Installments button + modal only when the module is enabled.

### 10. Existing normal client payments

Installment payments are automatically mapped into the finance ledger.

If SIGMA has another existing controller that records ordinary client payments and you want that payment to increase a bank/cash account, after the existing `$payment->save()` call use:

```php
$ledger = app(\App\Services\Financing\FinanceLedgerService::class);

app(\App\Services\Financing\FinancePaymentService::class)
    ->linkPaymentToAccount($payment, $request->account_id, $ledger);
```

That is how ordinary existing `payments` can be linked to an account without changing the `payments` table.

## Important accounting decisions

### Account balances

`finance_accounts.balance` is not directly edited after creation. It is recalculated from `finance_account_transactions`, preventing balance drift.

### Payroll and P&L

Posting payroll:

1. locks the payroll run,
2. stores `payroll_runs.total`,
3. creates a `Payroll` expense,
4. records the account outflow.

The P&L excludes the `Payroll` expense category from normal expenses and adds posted payroll separately. This prevents payroll from being counted twice.

### Driver collections

A completed job automatically creates a collection when:

```php
$job->stage == -1 && $job->delivery_accepted
```

The provider resolves the latest invoice by `case_id` and creates one collection per driver/invoice.

Finance users get the grouped reconciliation screen. Drivers also have:

```text
/financing/my-collections
```

and can only submit their own records.

### Soft deletes

Every new model uses `SoftDeletes`. `deleted_at` was therefore added to every new module table, including tables where the draft SQL omitted it.

## PDF / Excel

`FinancingPdfService` automatically supports the common `barryvdh/laravel-dompdf` facade or the `dompdf.wrapper` binding.

`FinancingExcelService` has no dependency: it produces an Excel-compatible `.xls` response. If SIGMA already uses Maatwebsite Excel, this one service can be replaced with the existing export pattern without changing report controllers/views.

## Migration timestamps

The migrations are dated `2026_08_28` and ordered from `100000` onward. If SIGMA already has a migration with a later timestamp on the same date, rename only the timestamp prefixes so these run after it.
