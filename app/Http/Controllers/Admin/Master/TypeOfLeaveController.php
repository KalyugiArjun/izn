<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\TypeOfLeave;
use Illuminate\Http\Request;

class TypeOfLeaveController extends Controller
{
    public function index()
    {
        $types_of_leaves = TypeOfLeave::latest()->get();
        return view('admin.master.type_of_leave.index', compact('types_of_leaves'));
    }

    public function create()
    {
        return view('admin.master.type_of_leave.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|unique:type_of_leaves,name',
        ]);

        TypeOfLeave::create([
            'name'    => $request->name,
            'status'  => $request->status ?? 1,
            'created_by'        => auth()->id(), // ✅ Added
            'updated_by'        => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Type of Leave created successfully');
    }

    public function edit($id)
    {
        $type_of_leave = TypeOfLeave::findOrFail(decrypt($id));
        return view('admin.master.type_of_leave.edit', compact('type_of_leave'));
    }

    public function update(Request $request, $id)
    {
        $type_of_leave = TypeOfLeave::findOrFail(decrypt($id));

        $request->validate([
            'name'    => 'required|unique:type_of_leaves,name,' . $type_of_leave->id,
        ]);

        $type_of_leave->update([
            'name'    => $request->name,
            'status'  => $request->status ?? 1,
             'updated_by'  => auth()->id(), // ✅ added
        ]);

        return redirect()->back()->with('success', 'Type of Leave updated successfully');
    }

    public function destroy($id)
    {
        $type_of_leave = TypeOfLeave::findOrFail(decrypt($id));
        $type_of_leave->delete();

        return redirect()->back()->with('success', 'Type of Leave deleted successfully');
    }
}
