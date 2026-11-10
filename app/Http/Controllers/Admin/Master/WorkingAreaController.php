<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Division;
use App\Models\Station;
use App\Models\WorkingArea;
use Illuminate\Http\Request;

class WorkingAreaController extends Controller
{
    public function index()
    {
        $workingAreas = WorkingArea::with(['division', 'department', 'designation', 'stations'])
            ->latest()
            ->get();

        return view('admin.master.working-area.index', compact('workingAreas'));
    }

    public function create()
    {
        $divisions = Division::where('status', 1)->get();
        $stations  = Station::where('status', 1)->get();

        return view('admin.master.working-area.create', compact('divisions', 'stations'));
    }

    public function store(Request $request)
    {
        // dd(auth()->id());
        $request->validate([
            'division_id'       => 'required|exists:divisions,id',
            'department_id'     => 'required|exists:departments,id',
            'designation_id'    => 'required|exists:designations,id',
            'station_ids'       => 'required|array|min:1',
            'station_ids.*'     => 'exists:stations,id',
            'head_station_name' => 'nullable|string|max:255',
        ]);

        $workingArea = WorkingArea::create([
            'division_id'       => $request->division_id,
            'department_id'     => $request->department_id,
            'designation_id'    => $request->designation_id,
            'mobile'            => $request->mobile,
            'email'             => $request->email,
            'head_station_name' => $request->head_station_name,
            'created_by'        => auth()->id(), // ✅ Added
            'updated_by'        => auth()->id(),
        ]);

        $workingArea->stations()->sync($request->station_ids);

        return redirect()->route('master.working-area')
            ->with('success', 'Working Area created successfully');
    }

    public function edit($id)
    {
        $workingArea = WorkingArea::with('stations')->findOrFail(decrypt($id));
        $divisions   = Division::where('status', 1)->get();
        $departments = Department::where('division_id', $workingArea->division_id)
            ->where('status', 1)->get();
        $designations = Designation::where('department_id', $workingArea->department_id)
            ->where('status', 1)->get();
        $stations        = Station::where('status', 1)->get();
        $selectedStations = $workingArea->stations->pluck('id')->toArray();

        return view('admin.master.working-area.edit', compact(
            'workingArea', 'divisions', 'departments',
            'designations', 'stations', 'selectedStations'
        ));
    }

    public function update(Request $request, $id)
    {
        $workingArea = WorkingArea::findOrFail(decrypt($id));

        $request->validate([
            'division_id'       => 'required|exists:divisions,id',
            'department_id'     => 'required|exists:departments,id',
            'designation_id'    => 'required|exists:designations,id',
            'station_ids'       => 'required|array|min:1',
            'station_ids.*'     => 'exists:stations,id',
            'head_station_name' => 'nullable|string|max:255',
        ]);

        $workingArea->update([
            'division_id'       => $request->division_id,
            'department_id'     => $request->department_id,
            'designation_id'    => $request->designation_id,
            'mobile'            => $request->mobile,
            'email'             => $request->email,
            'head_station_name' => $request->head_station_name,
            'updated_by'        => auth()->id(),
        ]);

        $workingArea->stations()->sync($request->station_ids);

        return redirect()->route('master.working-area')
            ->with('success', 'Working Area updated successfully');
    }

    public function destroy($id)
    {
        $workingArea = WorkingArea::findOrFail(decrypt($id));
        $workingArea->stations()->detach();
        $workingArea->delete();

        return redirect()->back()->with('success', 'Working Area deleted successfully');
    }

    // ─── AJAX Methods ────────────────────────────────────────────────

    public function getDepartments($division_id)
    {
        // dd($division_id);
        $departments = Department::where('division_id', $division_id)
            ->where('status', 1)
            ->get(['id', 'name']);

        return response()->json($departments);
    }

    public function getDesignations($department_id)
    {
        $designations = Designation::where('department_id', $department_id)
            ->where('status', 1)
            ->get(['id', 'name']);

        return response()->json($designations);
    }

    public function getDesignationDetail($id)
    {
        $designation = Designation::findOrFail($id);

        return response()->json([
            'mobile' => $designation->mobile,
            'email'  => $designation->email,
        ]);
    }
}
