<?php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceTypeController extends Controller
{
    public function index()
    {
        $serviceTypes = ServiceType::orderBy('name')->paginate(10);

        return view('service-types.service-types-index', [
            'serviceTypes' => $serviceTypes,
        ]);
    }

    public function create()
    {
        return view('service-types.add_service_type');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:service_types,name',
        ]);

        ServiceType::create([
            'name' => $request->input('name'),
        ]);

        return redirect()
            ->route('service-types.index')
            ->with('success', 'Service type added successfully.');
    }

    public function edit($id)
    {
        $serviceType = ServiceType::findOrFail($id);

        return view('service-types.edit_service_type', [
            'serviceType' => $serviceType,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('service_types', 'name')->ignore($id),
            ],
        ]);

        $serviceType = ServiceType::findOrFail($id);
        $serviceType->name = $request->input('name');
        $serviceType->save();

        return redirect()
            ->route('service-types.index')
            ->with('success', 'Service type updated successfully.');
    }

    public function destroy($id)
    {
        ServiceType::findOrFail($id)->delete();

        return redirect()
            ->route('service-types.index')
            ->with('success', 'Service type deleted successfully.');
    }
}
