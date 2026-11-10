<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\LoadType;
use Illuminate\Http\Request;

class LoadTypeController extends Controller
{
    public function index()
    {
        $load_types = LoadType::latest()->get();
        return view('admin.master.load_type.index', compact('load_types'));
    }

    public function create()
    {
        return view('admin.master.load_type.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|unique:load_types,name',
        ]);

        LoadType::create([
            'name'    => $request->name,
            'status'  => $request->status ?? 1,
            'created_by'        => auth()->id(), // ✅ Added
            'updated_by'        => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Load Type created successfully');
    }

    public function edit($id)
    {
        $load_type = LoadType::findOrFail(decrypt($id));
        return view('admin.master.load_type.edit', compact('load_type'));
    }

    public function update(Request $request, $id)
    {
        $load_type = LoadType::findOrFail(decrypt($id));

        $request->validate([
            'name'    => 'required|unique:load_types,name,' . $load_type->id,
        ]);

        $load_type->update([
            'name'    => $request->name,
            'status'  => $request->status ?? 1,
             'updated_by'  => auth()->id(), // ✅ added
        ]);

        return redirect()->back()->with('success', 'Load Type updated successfully');
    }

    public function destroy($id)
    {
        $load_type = LoadType::findOrFail(decrypt($id));
        $load_type->delete();

        return redirect()->back()->with('success', 'Load Type deleted successfully');
    }
}
