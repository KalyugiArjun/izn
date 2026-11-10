<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\InspectionMovement;
use App\Models\Movement;
use App\Models\Station;
use App\Models\Designation;
use App\Models\MasterEmployee;
use App\Models\TypeOfInspection;
use App\Models\TypeOfAmbush;
use App\Models\SafetyDrive;
use App\Models\AttendBlock;
use App\Models\TypeOfLeave;
use App\Models\Diesellobby;
use App\Models\RunningRoomLocation;
use Illuminate\Http\Request;

class InspectionController extends Controller
{
    public function index()
    {
        $inspections = InspectionMovement::latest()->paginate(15);
        return view('admin.master.inspection.index', compact('inspections'));
    }

    public function create()
    {
        $movements = Movement::where('status', 1)->get();
        $stations = Station::where('status', 1)->get();
        $designations = Designation::where('status', 1)->get();
        $employees = MasterEmployee::where('status', 1)->get();
        $type_of_inspections = TypeOfInspection::where('status', 1)->get();
        $type_of_ambushes = TypeOfAmbush::where('status', 1)->get();
        $safety_drives = SafetyDrive::where('status', 1)->get();
        $attend_blocks = AttendBlock::where('status', 1)->get();
        $type_of_leaves = TypeOfLeave::where('status', 1)->get();
        $diesel_lobbies = Diesellobby::where('status', 1)->get();
        $running_room_locations = RunningRoomLocation::where('status', 1)->get();
        $officers = \App\Models\User::where('is_active', 1)->with('designationData')->get();

        return view('admin.master.inspection.create', compact(
            'movements', 'stations', 'designations', 'employees',
            'type_of_inspections', 'type_of_ambushes', 'safety_drives',
            'attend_blocks', 'type_of_leaves', 'diesel_lobbies', 'running_room_locations', 'officers'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_of_inspection' => 'required|date',
            'designation'        => 'required|string|max:255',
            'movement'           => 'required|string',
        ]);

        InspectionMovement::create([
            'date_of_inspection'   => $request->date_of_inspection,
            'designation'          => $request->designation,
            'movement'             => $request->movement,
            'type_of_inspection'   => $request->type_of_inspection,
            'day_night'            => $request->day_night ?? 'DAY',
            'type_of_ambush_check' => $request->type_of_ambush_check,
            'station_yard'         => $request->station_yard,
            'level_crossing_no'    => $request->level_crossing_no,
            'train_number'         => $request->train_number,
            'section'              => $request->section,
            'line_number_load_stable'   => $request->line_number_load_stable,
            'type_of_load_load_stable'  => $request->type_of_load_load_stable,
            't806_number'          => $request->t806_number,
            'shunting_observation_of'   => $request->shunting_observation_of,
            'major_deficiencies'   => $request->major_deficiencies,
            'action_taken'         => $request->action_taken,
            'remark'               => $request->remark,
            'created_by'           => auth()->id(),
        ]);

        return redirect()->route('master.inspection.index')->with('success', 'Inspection created successfully');
    }

    public function show($id)
    {
        $inspection = InspectionMovement::findOrFail(decrypt($id));
        return view('admin.master.inspection.show', compact('inspection'));
    }

    public function edit($id)
    {
        $inspection = InspectionMovement::findOrFail(decrypt($id));

        $movements = Movement::where('status', 1)->get();
        $stations = Station::where('status', 1)->get();
        $designations = Designation::where('status', 1)->get();
        $employees = MasterEmployee::where('status', 1)->get();
        $type_of_inspections = TypeOfInspection::where('status', 1)->get();
        $type_of_ambushes = TypeOfAmbush::where('status', 1)->get();
        $safety_drives = SafetyDrive::where('status', 1)->get();
        $attend_blocks = AttendBlock::where('status', 1)->get();
        $type_of_leaves = TypeOfLeave::where('status', 1)->get();
        $diesel_lobbies = Diesellobby::where('status', 1)->get();
        $running_room_locations = RunningRoomLocation::where('status', 1)->get();
        $officers = \App\Models\User::where('is_active', 1)->with('designationData')->get();

        return view('admin.master.inspection.edit', compact(
            'inspection', 'movements', 'stations', 'designations', 'employees',
            'type_of_inspections', 'type_of_ambushes', 'safety_drives',
            'attend_blocks', 'type_of_leaves', 'diesel_lobbies', 'running_room_locations', 'officers'
        ));
    }

    public function update(Request $request, $id)
    {
        $inspection = InspectionMovement::findOrFail(decrypt($id));

        $request->validate([
            'date_of_inspection' => 'required|date',
            'designation'        => 'required|string|max:255',
            'movement'           => 'required|string',
        ]);

        $inspection->update([
            'date_of_inspection'   => $request->date_of_inspection,
            'designation'          => $request->designation,
            'movement'             => $request->movement,
            'type_of_inspection'   => $request->type_of_inspection,
            'day_night'            => $request->day_night ?? 'DAY',
            'type_of_ambush_check' => $request->type_of_ambush_check,
            'station_yard'         => $request->station_yard,
            'level_crossing_no'    => $request->level_crossing_no,
            'train_number'         => $request->train_number,
            'section'              => $request->section,
            'line_number_load_stable'   => $request->line_number_load_stable,
            'type_of_load_load_stable'  => $request->type_of_load_load_stable,
            't806_number'          => $request->t806_number,
            'shunting_observation_of'   => $request->shunting_observation_of,
            'major_deficiencies'   => $request->major_deficiencies,
            'action_taken'         => $request->action_taken,
            'remark'               => $request->remark,
        ]);

        return redirect()->route('master.inspection.index')->with('success', 'Inspection updated successfully');
    }

    public function destroy($id)
    {
        $inspection = InspectionMovement::findOrFail(decrypt($id));
        $inspection->delete();

        return redirect()->route('master.inspection.index')->with('success', 'Inspection deleted successfully');
    }
}
