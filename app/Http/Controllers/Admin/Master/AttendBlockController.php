<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\AttendBlock;
use Illuminate\Http\Request;

class AttendBlockController extends Controller
{
    public function index()
    {
        $attend_blocks = AttendBlock::latest()->get();
        return view('admin.master.attend_block.index', compact('attend_blocks'));
    }

    public function create()
    {
        return view('admin.master.attend_block.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|unique:attend_blocks,name',
        ]);

        AttendBlock::create([
            'name'    => $request->name,
            'status'  => $request->status ?? 1,
            'created_by'        => auth()->id(), // ✅ Added
            'updated_by'        => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Attend Block created successfully');
    }

    public function edit($id)
    {
        $attend_block = AttendBlock::findOrFail(decrypt($id));
        return view('admin.master.attend_block.edit', compact('attend_block'));
    }

    public function update(Request $request, $id)
    {
        $attend_block = AttendBlock::findOrFail(decrypt($id));

        $request->validate([
            'name'    => 'required|unique:attend_blocks,name,' . $attend_block->id,
        ]);

        $attend_block->update([
            'name'    => $request->name,
            'status'  => $request->status ?? 1,
             'updated_by'  => auth()->id(), // ✅ added
        ]);

        return redirect()->back()->with('success', 'Attend Block updated successfully');
    }

    public function destroy($id)
    {
        $attend_block = AttendBlock::findOrFail(decrypt($id));
        $attend_block->delete();

        return redirect()->back()->with('success', 'Attend Block deleted successfully');
    }
}
