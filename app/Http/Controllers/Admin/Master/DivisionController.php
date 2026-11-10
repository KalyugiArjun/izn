<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index()
    {
        $divisions = Division::latest()->get();

        // dd($divisions);
        return view('admin.master.division.index', compact('divisions'));
    }

    public function create()
    {
        return view('admin.master.division.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        Division::create([
            'name' => $request->name,
            'status' => $request->status ?? 1,
            'created_by'        => auth()->id(), // ✅ Added
            'updated_by'        => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Division created successfully');
    }

    public function edit($id)
    {
        $division = Division::findOrFail(decrypt($id));
        return view('admin.master.division.edit', compact('division'));
    }

    public function update(Request $request, $id)
    {
        $division = Division::findOrFail(decrypt($id));

        $request->validate([
            'name' => 'required'
        ]);

        $division->update([
            'name' => $request->name,
            'status' => $request->status ?? 1,
             'updated_by'  => auth()->id(), // ✅ added
        ]);

        return redirect()->back()->with('success', 'Updated successfully');
    }

    public function destroy($id)
    {
        $division = Division::findOrFail(decrypt($id));
        $division->delete();

        return redirect()->back()->with('success', 'Deleted successfully');
    }
}
