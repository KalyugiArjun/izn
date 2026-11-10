<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Movement;
use Illuminate\Http\Request;

class MovementController extends Controller
{
    public function index()
    {
        $movements = Movement::latest()->get();
        return view('admin.master.movement.index', compact('movements'));
    }

    public function create()
    {
        return view('admin.master.movement.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|unique:movements,name',
        ]);

        Movement::create([
            'name'    => $request->name,
            'status'  => $request->status ?? 1,
            'created_by'        => auth()->id(), // ✅ Added
            'updated_by'        => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Movement created successfully');
    }

    public function edit($id)
    {
        // dd('diesellobby edit');
        $movement = Movement::findOrFail(decrypt($id));
        return view('admin.master.movement.edit', compact('movement'));
    }

    public function update(Request $request, $id)
    {
        $movement = Movement::findOrFail(decrypt($id));

        $request->validate([
            'name'    => 'required|unique:movements,name,' . $movement->id,
        ]);

        $movement->update([
            'name'    => $request->name,
            'status'  => $request->status ?? 1,
             'updated_by'  => auth()->id(), // ✅ added
        ]);

        return redirect()->back()->with('success', 'Movement updated successfully');
    }

    public function destroy($id)
    {
        $movement = Movement::findOrFail(decrypt($id));
        $movement->delete();

        return redirect()->back()->with('success', 'Movement deleted successfully');
    }
}
