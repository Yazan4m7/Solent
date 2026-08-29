<?php

namespace App\Modules\Stock\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Stock\Models\StockItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockItemController extends Controller
{
    public function index(Request $request): View
    {
        $items = StockItem::query()
            ->withSum('balances as quantity', 'quantity')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->string('q')->toString();
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('sku', 'like', "%{$q}%")
                        ->orWhere('category', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->paginate(30)
            ->withQueryString();

        return view('stock.items.index', compact('items'));
    }

    public function create(): View
    {
        return view('stock.items.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $item = StockItem::create($this->validated($request));
        return redirect()->route('stock.items.show', $item)->with('success', 'Stock item created.');
    }

    public function show(StockItem $item): View
    {
        $item->load(['balances.location']);
        $movements = $item->movements()->with('location')->latest('occurred_at')->paginate(25);
        return view('stock.items.show', compact('item', 'movements'));
    }

    public function edit(StockItem $item): View
    {
        return view('stock.items.edit', compact('item'));
    }

    public function update(Request $request, StockItem $item): RedirectResponse
    {
        $item->update($this->validated($request, $item->id));
        return redirect()->route('stock.items.show', $item)->with('success', 'Stock item updated.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'sku' => ['nullable', 'string', 'max:100', 'unique:stock_items,sku,' . ($ignoreId ?? 'NULL')],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:120'],
            'unit' => ['required', 'string', 'max:30'],
            'minimum_stock' => ['required', 'numeric', 'min:0'],
            'target_stock' => ['nullable', 'numeric', 'min:0'],
            'default_unit_cost' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}
