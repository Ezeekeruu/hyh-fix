<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('supplier_name')->get();

        return view('suppliers.suppliers-index', [
            'suppliers' => $suppliers,
        ]);
    }

    public function create()
    {
        return view('suppliers.add_supplier');
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:100|unique:suppliers,supplier_name',
            'contact_info' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
        ]);

        $supplier = new Supplier;
        $supplier->supplier_name = $request->input('supplier_name');
        // Columns are NOT NULL in the schema, so store '' instead of null.
        $supplier->contact_info = $request->input('contact_info') ?? '';
        $supplier->location = $request->input('location') ?? '';
        $supplier->save();

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier added successfully.');
    }

    public function show($id)
    {
        return redirect()->route('suppliers.index');
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);

        return view('suppliers.edit_supplier', [
            'supplier' => $supplier,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'supplier_name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('suppliers', 'supplier_name')->ignore($id),
            ],
            'contact_info' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:255',
        ]);

        $supplier = Supplier::findOrFail($id);
        $supplier->supplier_name = $request->input('supplier_name');
        $supplier->contact_info = $request->input('contact_info') ?? '';
        $supplier->location = $request->input('location') ?? '';
        $supplier->save();

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy($id)
    {
        Supplier::findOrFail($id)->delete();

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}
