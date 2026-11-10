<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\SafetyDrive;
use Illuminate\Http\Request;

class SafetyDriveController extends Controller
{
    public function index()
    {
        $safety_drives = SafetyDrive::latest()->get();
        return view('admin.master.safety_drive.index', compact('safety_drives'));
    }

    public function create()
    {
        return view('admin.master.safety_drive.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|unique:safety_drives,name',
        ]);

        SafetyDrive::create([
            'name'    => $request->name,
            'status'  => $request->status ?? 1,
            'created_by'        => auth()->id(), // ✅ Added
            'updated_by'        => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Safety Drive created successfully');
    }

    public function edit($id)
    {
        $safety_drive = SafetyDrive::findOrFail(decrypt($id));
        return view('admin.master.safety_drive.edit', compact('safety_drive'));
    }

    public function update(Request $request, $id)
    {
        $safety_drive = SafetyDrive::findOrFail(decrypt($id));

        $request->validate([
            'name'    => 'required|unique:safety_drives,name,' . $safety_drive->id,
        ]);

        $safety_drive->update([
            'name'    => $request->name,
            'status'  => $request->status ?? 1,
             'updated_by'  => auth()->id(), // ✅ added
        ]);

        return redirect()->back()->with('success', 'Safety Drive updated successfully');
    }

    public function destroy($id)
    {
        $safety_drive = SafetyDrive::findOrFail(decrypt($id));
        $safety_drive->delete();

        return redirect()->back()->with('success', 'Safety Drive deleted successfully');
    }
}
