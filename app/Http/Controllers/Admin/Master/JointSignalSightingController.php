<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\JointSignalSighting;
use Illuminate\Http\Request;

class JointSignalSightingController extends Controller
{
    public function index()
    {
        $joint_signal_sightings = JointSignalSighting::latest()->get();
        return view('admin.master.joint_signal_sighting.index', compact('joint_signal_sightings'));
    }

    public function create()
    {
        return view('admin.master.joint_signal_sighting.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|unique:joint_signal_sightings,name',
        ]);

        JointSignalSighting::create([
            'name'    => $request->name,
            'status'  => $request->status ?? 1,
            'created_by'        => auth()->id(), // ✅ Added
            'updated_by'        => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Joint Signal Sighting created successfully');
    }

    public function edit($id)
    {
        $joint_signal_sighting = JointSignalSighting::findOrFail(decrypt($id));
        return view('admin.master.joint_signal_sighting.edit', compact('joint_signal_sighting'));
    }

    public function update(Request $request, $id)
    {
        $joint_signal_sighting = JointSignalSighting::findOrFail(decrypt($id));

        $request->validate([
            'name'    => 'required|unique:joint_signal_sightings,name,' . $joint_signal_sighting->id,
        ]);

        $joint_signal_sighting->update([
            'name'    => $request->name,
            'status'  => $request->status ?? 1,
             'updated_by'  => auth()->id(), // ✅ added
        ]);

        return redirect()->back()->with('success', 'Joint Signal Sighting updated successfully');
    }

    public function destroy($id)
    {
        $joint_signal_sighting = JointSignalSighting::findOrFail(decrypt($id));
        $joint_signal_sighting->delete();

        return redirect()->back()->with('success', 'Joint Signal Sighting deleted successfully');
    }
}
