<?php

namespace App\Modules\Stock\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Stock\Models\StockItem;
use App\Modules\Stock\Models\StockLocation;
use App\Modules\Stock\Models\StockMovement;
use App\Modules\Stock\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    public function index(Request $request): View
    {
        $movements = StockMovement::query()
            ->with(['item', 'location'])
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('item_id'), fn ($q) => $q->where('stock_item_id', $request->item_id))
            ->latest('occurred_at')
            ->paginate(40)
            ->withQueryString();

        $items = StockItem::orderBy('name')->get();
        return view('stock.movements.index', compact('movements', 'items'));
    }

    public function createAdjustment(): View
    {
        $items = StockItem::where('is_active', true)->orderBy('name')->get();
        $locations = StockLocation::where('is_active', true)->orderBy('name')->get();
        return view('stock.movements.adjust', compact('items', 'locations'));
    }

    public function storeAdjustment(Request $request, InventoryService $inventory): RedirectResponse
    {
        $data = $request->validate([
            'stock_item_id' => ['required', 'exists:stock_items,id'],
            'location_id' => ['required', 'exists:stock_locations,id'],
            'direction' => ['required', 'in:in,out'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'notes' => ['required', 'string', 'max:1000'],
        ]);

        $item = StockItem::findOrFail($data['stock_item_id']);
        $location = StockLocation::findOrFail($data['location_id']);
        $signed = $data['direction'] === 'in' ? abs((float) $data['quantity']) : -abs((float) $data['quantity']);
        $type = $data['direction'] === 'in' ? 'adjustment_in' : 'adjustment_out';

        $inventory->move($item, $location, $signed, $type, ['notes' => $data['notes']]);

        return redirect()->route('stock.movements.index')->with('success', 'Stock adjustment recorded.');
    }
}
