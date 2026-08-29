<?php

namespace App\Modules\Stock\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Stock\Models\StockLocation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockLocationController extends Controller
{
    public function index(): View
    {
        $locations = StockLocation::orderBy('name')->paginate(30);
        return view('stock.locations.index', compact('locations'));
    }

    public function store(Request $request): RedirectResponse
    {
        StockLocation::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', 'unique:stock_locations,code'],
            'notes' => ['nullable', 'string'],
        ]) + ['is_active' => true]);

        return back()->with('success', 'Stock location added.');
    }
}
