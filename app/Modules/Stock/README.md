# Solent Stock Module

Generic Laravel inventory module designed to be copied into Solent. It intentionally keeps its views and CSS separate from the existing Solent CSS.

## What is included now

- Item master: SKU, category, unit, minimum stock, target stock, cost, specification.
- Multiple stock locations.
- Suppliers.
- Purchase receipts: enter purchased items and add them to stock.
- Stock balances by item/location.
- Append-only stock movement ledger (audit trail).
- Manual adjustments with required reason.
- Low-stock / out-of-stock dashboard.
- "Need to buy" list with suggested quantity = target stock - current stock.
- Inventory value estimate.
- Lot number and expiry fields on purchase/movement records, ready for materials that need them.
- `InventoryService::consume()` ready for later integration with job creation.

## ERP design rule used

Never directly edit a stock quantity. Every increase/decrease is a **movement** and the current balance is updated from that movement. This gives Solent an audit trail and makes job consumption, purchase receipts, corrections and returns use the same stock engine.

## Copy locations

Copy the folders into the matching Laravel paths:

- `resources/views/stock` -> `resources/views/stock`
- `public/css/stock.css` -> `public/css/stock.css`
- `public/js/stock.js` -> `public/js/stock.js`
- `app/Models/Stock` -> `app/Models/Stock`
- `app/Services/Stock` -> `app/Services/Stock`
- `app/Http/Controllers/Stock` -> `app/Http/Controllers/Stock`
- migration file -> `database/migrations`
- `routes/stock.php` -> `routes/stock.php`

Then add this one line to `routes/web.php`:

```php
require base_path('routes/stock.php');
```

Run the migration through the same database/tenant migration process Solent already uses.

```bash
php artisan migrate
```

Then open `/stock`, add at least one stock location, add items, and use **Receive purchase** to create opening/current stock. For an existing physical quantity, use **Stock adjustment -> Add stock** with reason `Opening balance`.

## Layout assumption

The Blade files use:

```blade
@extends('layouts.app')
@section('content')
```

If Solent's main layout has another name/section, change only those lines. The stock UI itself is isolated under `resources/views/stock` and all CSS selectors are prefixed `stock-`.

## Later: job / zircon block integration

Do **not** subtract stock directly inside the job controller. Call the stock service after the job is successfully created.

Example for the future "New zircon block" checkbox:

```php
use App\Services\Stock\InventoryService;

if ($request->boolean('new_zircon_block')) {
    app(InventoryService::class)->consume(
        stockItemId: (int) $request->stock_item_id,
        quantity: 1,
        locationId: (int) $request->stock_location_id,
        referenceId: $job->id,
        referenceType: 'job',
        notes: 'New zircon block opened for job #'.$job->id,
    );
}
```

The better final Solent integration is to link each relevant Material/variant to a `stock_item_id`, so the user does not need to choose the inventory item twice. The checkbox then only confirms that one full block/package was consumed.

## Recommended phase 2

After this base module works in real labs:

1. Link Solent materials/job types to inventory items.
2. Automatic job consumption (block, disc, resin bottle, etc.).
3. Returns / undo movement when a job is cancelled incorrectly.
4. Purchase orders: Draft -> Ordered -> Partially received -> Received.
5. Supplier price history and preferred supplier.
6. Lot/expiry tracking UI and expiry alerts.
7. Physical stock-count sessions.
8. Optional barcode/QR scanning.
9. Permissions: view stock / receive / adjust / manage items.
10. Reports: consumption by material, technician/job, supplier spend, stock valuation.

## Important Solent-specific assumption

This module uses the application's current/default database connection. If each Solent lab has its own tenant database, run these tables in the tenant schema/database, not the central landlord database.
