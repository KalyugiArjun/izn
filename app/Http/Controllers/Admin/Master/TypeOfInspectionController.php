<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\TypeOfInspection;
use Illuminate\Http\Request;

class TypeOfInspectionController extends Controller
{
    public function index()
    {
        $types_of_inspections = TypeOfInspection::latest()->get();
        return view('admin.master.type_of_inspection.index', compact('types_of_inspections'));
    }

    public function create()
    {
        return view('admin.master.type_of_inspection.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|unique:type_of_inspections,name',
        ]);

        TypeOfInspection::create([
            'name'    => $request->name,
            'status'  => $request->status ?? 1,
            'created_by'        => auth()->id(), // ✅ Added
            'updated_by'        => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Type of Inspection created successfully');
    }

    public function edit($id)
    {
        $type_of_inspection = TypeOfInspection::findOrFail(decrypt($id));
        return view('admin.master.type_of_inspection.edit', compact('type_of_inspection'));
    }

    public function update(Request $request, $id)
    {
        $type_of_inspection = TypeOfInspection::findOrFail(decrypt($id));

        $request->validate([
            'name'    => 'required|unique:type_of_inspections,name,' . $type_of_inspection->id,
        ]);

        $type_of_inspection->update([
            'name'    => $request->name,
            'status'  => $request->status ?? 1,
             'updated_by'  => auth()->id(), // ✅ added
        ]);

        return redirect()->back()->with('success', 'Type of Inspection updated successfully');
    }

    public function destroy($id)
    {
        $type_of_inspection = TypeOfInspection::findOrFail(decrypt($id));
        $type_of_inspection->delete();

        return redirect()->back()->with('success', 'Type of Inspection deleted successfully');
    }
}
