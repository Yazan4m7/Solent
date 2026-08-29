<?php

namespace App\Modules\Stock\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Stock\Models\StockItem;
use App\Modules\Stock\Models\StockMovement;
use Illuminate\View\View;

class StockDashboardController extends Controller
{
    public function index(): View
    {
        $items = StockItem::query()
            ->where('is_active', true)
            ->withSum('balances as quantity', 'quantity')
            ->orderBy('name')
            ->get();

        $lowStock = $items->filter(fn ($item) => (float) ($item->quantity ?? 0) <= (float) $item->minimum_stock);
        $outOfStock = $items->filter(fn ($item) => (float) ($item->quantity ?? 0) <= 0);
        $inventoryValue = $items->sum(fn ($item) => (float) ($item->quantity ?? 0) * (float) ($item->default_unit_cost ?? 0));

        $recentMovements = StockMovement::query()
            ->with(['item', 'location'])
            ->latest('occurred_at')
            ->limit(12)
            ->get();

        return view('stock.index', compact('items', 'lowStock', 'outOfStock', 'inventoryValue', 'recentMovements'));
    }

    public function needs(): View
    {
        $items = StockItem::query()
            ->where('is_active', true)
            ->withSum('balances as quantity', 'quantity')
            ->orderBy('name')
            ->get()
            ->filter(fn ($item) => (float) ($item->quantity ?? 0) <= (float) $item->minimum_stock)
            ->map(function ($item) {
                $current = (float) ($item->quantity ?? 0);
                $target = $item->target_stock !== null ? (float) $item->target_stock : (float) $item->minimum_stock;
                $item->suggested_order = max(0, $target - $current);
                return $item;
            });

        return view('stock.needs', compact('items'));
    }
}
