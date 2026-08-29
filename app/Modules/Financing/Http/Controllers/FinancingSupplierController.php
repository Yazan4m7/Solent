<?php

namespace App\Modules\Financing\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Financing\Models\Supplier;
use Illuminate\Http\Request;

class FinancingSupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::withCount('bills')->orderBy('name')->paginate(40);

        return view('financing.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('financing.suppliers.create');
    }

    public function store(Request $request)
    {
        $supplier = Supplier::create($this->validated($request));

        session()->flash('success', __('financing::financing.saved'));

        return redirect()->route('financing.suppliers.show', $supplier->id);
    }

    public function show($id)
    {
        $supplier = Supplier::findOrFail($id);
        $bills = $supplier->bills()
            ->with('payments.account')
            ->orderByDesc('due_date')
            ->get();

        return view('financing.suppliers.show', compact('supplier', 'bills'));
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);

        return view('financing.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->update($this->validated($request));

        session()->flash('success', __('financing::financing.saved'));

        return redirect()->route('financing.suppliers.show', $supplier->id);
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);

        if ($supplier->bills()->exists()) {
            return back()->withErrors([
                'supplier' => __('financing::financing.supplier_with_bills_cannot_delete'),
            ]);
        }

        $supplier->delete();

        session()->flash('success', __('financing::financing.deleted'));

        return redirect()->route('financing.suppliers.index');
    }

    protected function validated(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|max:150',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'payment_terms_days' => 'required|integer|min:0|max:3650',
            'notes' => 'nullable|string',
        ]);
    }
}
