<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Division;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with('division')->latest()->get();
        return view('admin.master.department.index', compact('departments'));
    }

    public function create()
    {
        $divisions = Division::where('status', 1)->get();
        return view('admin.master.department.create', compact('divisions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'name'        => 'required'
        ]);

        Department::create([
            'division_id' => $request->division_id,
            'name'        => $request->name,
            'status'      => $request->status ?? 1,
            'created_by'        => auth()->id(), // ✅ Added
            'updated_by'        => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Department created successfully');
    }

    public function edit($id)
    {
        $department = Department::findOrFail(decrypt($id));
        $divisions  = Division::where('status', 1)->get();
        return view('admin.master.department.edit', compact('department', 'divisions'));
    }

    public function update(Request $request, $id)
    {
        $department = Department::findOrFail(decrypt($id));

        $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'name'        => 'required'
        ]);

        $department->update([
            'division_id' => $request->division_id,
            'name'        => $request->name,
            'status'      => $request->status ?? 1,
            'updated_by'  => auth()->id(), // ✅ added
        ]);

        return redirect()->back()->with('success', 'Department updated successfully');
    }

    public function destroy($id)
    {
        $department = Department::findOrFail(decrypt($id));
        $department->delete();

        return redirect()->back()->with('success', 'Department deleted successfully');
    }
}
