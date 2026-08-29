<?php

namespace App\Modules\Stock\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Stock\Models\StockSupplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockSupplierController extends Controller
{
    public function index(): View
    {
        $suppliers = StockSupplier::orderBy('name')->paginate(30);
        return view('stock.suppliers.index', compact('suppliers'));
    }

    public function store(Request $request): RedirectResponse
    {
        StockSupplier::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:80'],
            'email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]) + ['is_active' => true]);

        return back()->with('success', 'Supplier added.');
    }
}
