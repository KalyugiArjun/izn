<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\TypeOfAmbush;
use Illuminate\Http\Request;

class TypeOfAmbushController extends Controller
{
    public function index()
    {
        $type_of_ambushs = TypeOfAmbush::latest()->get();
        return view('admin.master.type_of_ambush.index', compact('type_of_ambushs'));
    }

    public function create()
    {
        return view('admin.master.type_of_ambush.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|unique:type_of_ambushs,name',
        ]);

        TypeOfAmbush::create([
            'name'    => $request->name,
            'status'  => $request->status ?? 1,
            'created_by'        => auth()->id(), // ✅ Added
            'updated_by'        => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Type of Ambush created successfully');
    }

    public function edit($id)
    {
        $type_of_ambush = TypeOfAmbush::findOrFail(decrypt($id));
        return view('admin.master.type_of_ambush.edit', compact('type_of_ambush'));
    }

    public function update(Request $request, $id)
    {
        $type_of_ambush = TypeOfAmbush::findOrFail(decrypt($id));

        $request->validate([
            'name'    => 'required|unique:type_of_ambushs,name,' . $type_of_ambush->id,
        ]);

        $type_of_ambush->update([
            'name'    => $request->name,
            'status'  => $request->status ?? 1,
            'updated_by'  => auth()->id(), // ✅ added
        ]);

        return redirect()->back()->with('success', 'Type of Ambush updated successfully');
    }

    public function destroy($id)
    {
        $type_of_ambush = TypeOfAmbush::findOrFail(decrypt($id));
        $type_of_ambush->delete();

        return redirect()->back()->with('success', 'Type of Ambush deleted successfully');
    }
}
