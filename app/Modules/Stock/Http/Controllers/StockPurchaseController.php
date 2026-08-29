<?php

namespace App\Modules\Stock\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Stock\Models\StockItem;
use App\Modules\Stock\Models\StockLocation;
use App\Modules\Stock\Models\StockPurchase;
use App\Modules\Stock\Models\StockSupplier;
use App\Modules\Stock\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockPurchaseController extends Controller
{
    public function index(): View
    {
        $purchases = StockPurchase::query()
            ->with(['supplier', 'lines'])
            ->latest('purchased_at')
            ->paginate(30);

        return view('stock.purchases.index', compact('purchases'));
    }

    public function create(): View
    {
        $items = StockItem::where('is_active', true)->orderBy('name')->get();
        $locations = StockLocation::where('is_active', true)->orderBy('name')->get();
        $suppliers = StockSupplier::where('is_active', true)->orderBy('name')->get();

        return view('stock.purchases.create', compact('items', 'locations', 'suppliers'));
    }

    public function store(Request $request, InventoryService $inventory): RedirectResponse
    {
        $data = $request->validate([
            'supplier_id' => ['nullable', 'exists:stock_suppliers,id'],
            'reference_no' => ['nullable', 'string', 'max:120'],
            'purchased_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'lines' => ['required', 'array', 'min:1'],
            'lines.*.stock_item_id' => ['required', 'exists:stock_items,id'],
            'lines.*.location_id' => ['required', 'exists:stock_locations,id'],
            'lines.*.quantity' => ['required', 'numeric', 'gt:0'],
            'lines.*.unit_cost' => ['nullable', 'numeric', 'min:0'],
            'lines.*.lot_number' => ['nullable', 'string', 'max:120'],
            'lines.*.expires_at' => ['nullable', 'date'],
        ]);

        $purchase = DB::transaction(function () use ($data, $inventory) {
            $purchase = StockPurchase::create([
                'supplier_id' => $data['supplier_id'] ?? null,
                'reference_no' => $data['reference_no'] ?? null,
                'purchased_at' => $data['purchased_at'],
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($data['lines'] as $lineData) {
                $line = $purchase->lines()->create($lineData);
                $inventory->move($line->item()->firstOrFail(), $line->location()->firstOrFail(), (float) $line->quantity, 'purchase', [
                    'unit_cost' => $line->unit_cost,
                    'lot_number' => $line->lot_number,
                    'expires_at' => $line->expires_at,
                    'reference_type' => 'stock_purchase',
                    'reference_id' => $purchase->id,
                    'notes' => $purchase->reference_no ? 'Purchase ' . $purchase->reference_no : 'Purchase receipt',
                ]);

                if ($line->unit_cost !== null) {
                    $line->item()->update(['default_unit_cost' => $line->unit_cost]);
                }
            }

            return $purchase;
        });

        return redirect()->route('stock.purchases.index')->with('success', "Purchase #{$purchase->id} received into stock.");
    }
}
