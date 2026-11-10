<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\RunningRoomLocation;
use Illuminate\Http\Request;

class RunningRoomLocationController extends Controller
{
    public function index()
    {
        $running_room_locations = RunningRoomLocation::latest()->get();
        return view('admin.master.running_room_location.index', compact('running_room_locations'));
    }

    public function create()
    {
        return view('admin.master.running_room_location.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required',
            'code'    => 'nullable|unique:running_room_locations,code',
            'pincode' => 'nullable|digits:6'
        ]);

        RunningRoomLocation::create([
            'code'    => $request->code,
            'name'    => $request->name,
            'created_by'        => auth()->id(), // ✅ Added
            'updated_by'        => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Running Room Location created successfully');
    }

    public function edit($id)
    {
        // dd('diesellobby edit');
        $running_room_location = RunningRoomLocation::findOrFail(decrypt($id));
        return view('admin.master.running_room_location.edit', compact('running_room_location'));
    }

    public function update(Request $request, $id)
    {
        $running_room_location = RunningRoomLocation::findOrFail(decrypt($id));

        $request->validate([
            'name'    => 'required',
            'code'    => 'nullable|unique:running_room_locations,code,' . $running_room_location->id,
        ]);

        $running_room_location->update([
            'code'    => $request->code,
            'name'    => $request->name,
            'status'  => $request->status ?? 1,
             'updated_by'  => auth()->id(), // ✅ added
        ]);

        return redirect()->back()->with('success', 'Running Room Location updated successfully');
    }

    public function destroy($id)
    {
        $running_room_location = RunningRoomLocation::findOrFail(decrypt($id));
        $running_room_location->delete();

        return redirect()->back()->with('success', 'Running Room Location deleted successfully');
    }
}
