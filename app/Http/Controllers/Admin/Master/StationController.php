<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Station;
use Illuminate\Http\Request;

class StationController extends Controller
{
    public function index()
    {
        $stations = Station::latest()->get();
        return view('admin.master.station.index', compact('stations'));
    }

    public function create()
    {
        return view('admin.master.station.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required',
            'code'    => 'nullable|unique:stations,code',
            'pincode' => 'nullable|digits:6'
        ]);

        Station::create([
            'code'    => $request->code,
            'name'    => $request->name,
            // 'address' => $request->address,
            // 'city'    => $request->city,
            // 'state'   => $request->state,
            // 'pincode' => $request->pincode,
            // 'status'  => $request->status ?? 1,
            'created_by'        => auth()->id(), // ✅ Added
            'updated_by'        => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Station created successfully');
    }

    public function edit($id)
    {
        $station = Station::findOrFail(decrypt($id));
        return view('admin.master.station.edit', compact('station'));
    }

    public function update(Request $request, $id)
    {
        $station = Station::findOrFail(decrypt($id));

        $request->validate([
            'name'    => 'required',
            'code'    => 'nullable|unique:stations,code,' . $station->id,
            'pincode' => 'nullable|digits:6'
        ]);

        $station->update([
            'code'    => $request->code,
            'name'    => $request->name,
            // 'address' => $request->address,
            // 'city'    => $request->city,
            // 'state'   => $request->state,
            // 'pincode' => $request->pincode,
            'status'  => $request->status ?? 1,
             'updated_by'  => auth()->id(), // ✅ added
        ]);

        return redirect()->back()->with('success', 'Station updated successfully');
    }

    public function destroy($id)
    {
        $station = Station::findOrFail(decrypt($id));
        $station->delete();

        return redirect()->back()->with('success', 'Station deleted successfully');
    }
}
