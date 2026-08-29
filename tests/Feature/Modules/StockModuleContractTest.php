<?php

namespace Tests\Feature\Modules;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use App\Modules\Stock\Models\StockItem;
use App\Modules\Stock\Models\StockLocation;
use App\Modules\Stock\Models\StockMovement;
use App\Modules\Stock\Services\InventoryService;
use Tests\TestCase;

class StockModuleContractTest extends TestCase
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

        $migration = require base_path('app/Modules/Stock/Database/migrations/2026_08_28_000000_create_stock_module_tables.php');
        $migration->up();
    }

    public function test_module_routes_and_assets_are_registered(): void
    {
        foreach (['stock.index', 'stock.items.index', 'stock.purchases.store', 'stock.adjustments.store'] as $name) {
            $route = app('router')->getRoutes()->getByName($name);
            $this->assertNotNull($route, "Missing stock route [{$name}].");
            $this->assertContains('auth', $route->gatherMiddleware());
        }

        $this->assertFileExists(public_path('css/stock.css'));
        $this->assertFileExists(public_path('js/stock.js'));
    }

    public function test_inventory_service_keeps_balance_and_append_only_movement_ledger_in_sync(): void
    {
        $item = StockItem::create(['name' => 'Zircon block', 'unit' => 'piece', 'minimum_stock' => 1]);
        $location = StockLocation::create(['name' => 'Main Stock']);

        $service = app(InventoryService::class);
        $service->move($item, $location, 10, 'purchase', ['notes' => 'opening']);
        $service->consume($item->id, 3, $location->id, 99, 'job', 'job consumption');

        $this->assertSame('7.000', (string) $item->balances()->first()->quantity);
        $this->assertSame(2, StockMovement::count());
        $this->assertSame(10.0, (float) StockMovement::oldest('id')->first()->quantity);
        $this->assertSame(-3.0, (float) StockMovement::latest('id')->first()->quantity);
    }

    public function test_inventory_service_rejects_negative_stock_without_partial_writes(): void
    {
        $item = StockItem::create(['name' => 'Resin bottle', 'unit' => 'bottle']);
        $location = StockLocation::create(['name' => 'Main Stock']);
        $service = app(InventoryService::class);

        $service->move($item, $location, 1, 'purchase');

        try {
            $service->consume($item->id, 2, $location->id);
            $this->fail('Expected negative-stock validation failure.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('quantity', $e->errors());
        }

        $this->assertSame(1.0, (float) $item->balances()->first()->quantity);
        $this->assertSame(1, StockMovement::count(), 'Failed consumption must not leave a movement behind.');
    }

    public function test_inventory_service_rejects_zero_quantity(): void
    {
        $item = StockItem::create(['name' => 'Disc', 'unit' => 'piece']);
        $location = StockLocation::create(['name' => 'Main Stock']);

        $this->expectException(ValidationException::class);
        app(InventoryService::class)->move($item, $location, 0, 'adjustment_in');
    }
}
