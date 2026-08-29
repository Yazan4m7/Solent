<?php

namespace Tests\Feature\Modules;

use App\Modules\Financing\Http\Middleware\FinancingAccess;
use App\Modules\Financing\Http\Middleware\ModuleEnabled;
use App\Modules\Financing\Models\FinanceAccount;
use App\Modules\Financing\Models\FinanceAccountTransaction;
use App\Modules\Financing\Services\FinanceLedgerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FinancingModuleContractTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        DB::purge('sqlite');

        Schema::create('users', function ($table): void {
            $table->bigIncrements('id');
        });

        require_once base_path('app/Modules/Financing/Database/migrations/2026_08_28_100010_create_finance_accounts_tables.php');
        (new \CreateFinanceAccountsTables())->up();
    }

    public function test_module_routes_are_registered_with_expected_security_layers(): void
    {
        $routes = app('router')->getRoutes();

        foreach (['financing.dashboard', 'financing.accounts.index', 'financing.expenses.index', 'financing.payroll.index'] as $name) {
            $route = $routes->getByName($name);
            $this->assertNotNull($route, "Missing financing route [{$name}].");
            $middleware = $route->gatherMiddleware();
            $this->assertContains('auth', $middleware);
            $this->assertContains('module:financing', $middleware);
            $this->assertContains('financing.access', $middleware);
        }

        $postPayroll = $routes->getByName('financing.payroll.post');
        $this->assertNotNull($postPayroll);
        $this->assertContains('admin', $postPayroll->gatherMiddleware());

        $toggle = $routes->getByName('admin.settings.module.update');
        $this->assertContains('admin', $toggle->gatherMiddleware());
    }


    public function test_financing_middleware_aliases_and_recurring_command_are_registered(): void
    {
        $middleware = app('router')->getMiddleware();

        $this->assertSame(ModuleEnabled::class, $middleware['module'] ?? null);
        $this->assertSame(FinancingAccess::class, $middleware['financing.access'] ?? null);
        $this->assertArrayHasKey('financing:generate-recurring', \Illuminate\Support\Facades\Artisan::all());
    }

    public function test_setting_helper_is_safe_before_settings_migration_runs(): void
    {
        $this->assertFalse(Schema::hasTable('settings'));
        $this->assertSame('fallback', setting('module_financing', 'fallback'));
    }

    public function test_financing_uses_actual_solent_legacy_models(): void
    {
        $this->assertSame(\App\User::class, config('modules.financing.models.user'));
        $this->assertSame(\App\client::class, config('modules.financing.models.client'));
        $this->assertSame(\App\invoice::class, config('modules.financing.models.invoice'));
        $this->assertSame(\App\payment::class, config('modules.financing.models.payment'));
        $this->assertSame(\App\job::class, config('modules.financing.models.job'));
    }

    public function test_ledger_sync_is_idempotent_and_recalculates_balance(): void
    {
        $account = FinanceAccount::create([
            'name' => 'Cash',
            'type' => 'cash',
            'currency' => 'JOD',
            'balance' => 0,
            'is_active' => 1,
        ]);

        $ledger = app(FinanceLedgerService::class);
        $ledger->sync($account->id, 'inflow', 100, '2026-08-29', 'Payment', 'payment', 1);
        $ledger->sync($account->id, 'inflow', 75, '2026-08-29', 'Corrected payment', 'payment', 1);

        $this->assertSame(1, FinanceAccountTransaction::count(), 'A source must create only one ledger row.');
        $this->assertSame(75.0, (float) $account->fresh()->balance);
    }

    public function test_moving_a_source_between_accounts_recalculates_both_accounts(): void
    {
        $cash = FinanceAccount::create(['name' => 'Cash', 'type' => 'cash', 'currency' => 'JOD', 'balance' => 0, 'is_active' => 1]);
        $bank = FinanceAccount::create(['name' => 'Bank', 'type' => 'bank', 'currency' => 'JOD', 'balance' => 0, 'is_active' => 1]);

        $ledger = app(FinanceLedgerService::class);
        $ledger->sync($cash->id, 'outflow', 20, '2026-08-29', 'Expense', 'expense', 7);
        $ledger->sync($bank->id, 'outflow', 20, '2026-08-29', 'Expense', 'expense', 7);

        $this->assertSame(0.0, (float) $cash->fresh()->balance);
        $this->assertSame(-20.0, (float) $bank->fresh()->balance);
        $this->assertSame(1, FinanceAccountTransaction::count());
    }

    public function test_module_source_does_not_modify_existing_business_tables(): void
    {
        $forbidden = ['invoices', 'payments', 'clients', 'users', 'cases', 'jobs'];
        $violations = [];

        foreach (glob(base_path('app/Modules/Financing/Database/migrations/*.php')) as $migration) {
            $source = file_get_contents($migration);
            foreach ($forbidden as $table) {
                if (preg_match("/Schema::table\\(['\\\"]" . preg_quote($table, '/') . "['\\\"]/", $source)) {
                    $violations[] = basename($migration) . ':' . $table;
                }
            }
        }

        $this->assertSame([], $violations, 'Financing migration modifies an existing Solent table.');
    }

    public function test_financing_models_other_than_shared_setting_use_soft_deletes(): void
    {
        $modelDir = base_path('app/Modules/Financing/Models');
        $exceptions = ['Setting.php'];
        $missing = [];

        foreach (glob($modelDir . '/*.php') as $file) {
            if (in_array(basename($file), $exceptions, true)) {
                continue;
            }
            $source = file_get_contents($file);
            if (strpos($source, 'SoftDeletes') === false) {
                $missing[] = basename($file);
            }
        }

        $this->assertSame([], $missing, 'Financing model missing SoftDeletes.');
    }
}
