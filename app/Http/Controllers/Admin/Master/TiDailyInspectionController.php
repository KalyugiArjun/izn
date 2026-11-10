<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Inspection;
use App\Models\MasterEmployee;
use App\Models\Designation;
use App\Models\Movement;
use App\Models\TypeOfInspection;
use App\Models\TypeOfAmbush;
use App\Models\Station;
use Illuminate\Http\Request;

class TiDailyInspectionController extends Controller
{
    private function getTIData()
    {
        $user = auth()->user();
        $isTI = false;
        $tiStationIds = [];
        $tiStationNames = [];
        if ($user && (strtolower($user->type) === 'emp' || strtolower($user->type) === 'employee')) {
            $designationName = $user->designationData ? strtoupper($user->designationData->name) : '';
            if (str_contains($designationName, 'TRAFFIC INSPECTOR') || str_contains($designationName, 'TI')) {
                $isTI = true;
                $employee = MasterEmployee::where('mobile', $user->phone_number)->first();
                if ($employee) {
                    $tiStationIds = $employee->stations->pluck('id')->toArray();
                    $tiStationNames = $employee->stations->pluck('name')->map(function($val) {
                        return strtoupper(trim($val));
                    })->toArray();
                }
            }
        }
        return [$isTI, $tiStationIds, $tiStationNames];
    }

    public function index()
    {
        list($isTI, $tiStationIds, $tiStationNames) = $this->getTIData();
        $query = Inspection::query();
        if ($isTI) {
            $query->whereIn(\DB::raw('UPPER(TRIM(station_yard))'), $tiStationNames);
        }
        
        $inspections = $query->with('employee')->latest()->paginate(15);
        return view('admin.master.ti_daily_inspection.index', compact('inspections'));
    }

    public function show($id)
    {
        $inspection = Inspection::with('employee')->findOrFail(decrypt($id));
        return view('admin.master.ti_daily_inspection.show', compact('inspection'));
    }

    public function create()
    {
        list($isTI, $tiStationIds, $tiStationNames) = $this->getTIData();
        
        $stationsQuery = Station::where('status', 1);
        $employeesQuery = MasterEmployee::where('status', 1);
        
        if ($isTI) {
            $stationsQuery->whereIn('id', $tiStationIds);
            $employeesQuery->whereHas('stations', function ($q) use ($tiStationIds) {
                $q->whereIn('stations.id', $tiStationIds);
            });
        }
        
        $stations = $stationsQuery->get();
        $employees = $employeesQuery->get();

        $designations = Designation::where('status', 1)->get();
        $movements = Movement::where('status', 1)->get();
        $type_of_inspections = TypeOfInspection::where('status', 1)->get();
        $type_of_ambushes = TypeOfAmbush::where('status', 1)->get();

        return view('admin.master.ti_daily_inspection.create', compact(
            'employees', 'designations', 'movements', 'type_of_inspections', 'type_of_ambushes', 'stations'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_of_inspection' => 'required|date',
            'created_by'         => 'required|exists:master_employees,id',
            'designation'        => 'required|string|max:255',
            'movement'           => 'required|string|max:255',
        ]);

        // Process shunting staff array into JSON string
        $staffData = [];
        if ($request->has('shunting_staff')) {
            foreach ($request->shunting_staff as $staff) {
                $name = trim($staff['name'] ?? '');
                $desg = trim($staff['designation'] ?? '');
                if ($name || $desg) {
                    $staffData[] = ['name' => $name, 'designation' => $desg];
                }
            }
        }

        $remarkText = $request->remark ?? '';
        if (!empty($staffData)) {
            $remarkText .= "\n[SHUNTING_STAFF:" . json_encode($staffData, JSON_UNESCAPED_UNICODE) . "]";
        }

        Inspection::create([
            'date_of_inspection'       => $request->date_of_inspection,
            'designation'              => $request->designation,
            'movement'                 => $request->movement,
            'type_of_inspection'       => $request->type_of_inspection,
            'day_night'                => $request->day_night ?? 'DAY',
            'type_of_ambush_check'     => $request->type_of_ambush_check,
            'station_yard'             => $request->station_yard,
            'level_crossing_no'        => $request->level_crossing_no,
            'train_number'             => $request->train_number,
            'section'                  => $request->section,
            'line_number_load_stable'  => $request->line_number_load_stable,
            'type_of_load_load_stable' => $request->type_of_load_load_stable,
            't806_number'              => $request->t806_number,
            'shunting_observation_of'  => $request->shunting_observation_of,
            'major_deficiencies'       => $request->major_deficiencies,
            'action_taken'             => $request->action_taken,
            'remark'                   => $remarkText,
            'created_by'               => $request->created_by,
        ]);

        return redirect()->route('master.ti_daily_inspection.index')->with('success', 'TI Daily Inspection Record created successfully');
    }

    public function edit($id)
    {
        $inspection = Inspection::findOrFail(decrypt($id));
        list($isTI, $tiStationIds, $tiStationNames) = $this->getTIData();
        
        $stationsQuery = Station::where('status', 1);
        $employeesQuery = MasterEmployee::where('status', 1);
        
        if ($isTI) {
            $stationsQuery->whereIn('id', $tiStationIds);
            $employeesQuery->whereHas('stations', function ($q) use ($tiStationIds) {
                $q->whereIn('stations.id', $tiStationIds);
            });
        }
        
        $stations = $stationsQuery->get();
        $employees = $employeesQuery->get();

        $designations = Designation::where('status', 1)->get();
        $movements = Movement::where('status', 1)->get();
        $type_of_inspections = TypeOfInspection::where('status', 1)->get();
        $type_of_ambushes = TypeOfAmbush::where('status', 1)->get();

        return view('admin.master.ti_daily_inspection.edit', compact(
            'inspection', 'employees', 'designations', 'movements', 'type_of_inspections', 'type_of_ambushes', 'stations'
        ));
    }

    public function update(Request $request, $id)
    {
        $inspection = Inspection::findOrFail(decrypt($id));

        $request->validate([
            'date_of_inspection' => 'required|date',
            'created_by'         => 'required|exists:master_employees,id',
            'designation'        => 'required|string|max:255',
            'movement'           => 'required|string|max:255',
        ]);

        // Process shunting staff array into JSON string
        $staffData = [];
        if ($request->has('shunting_staff')) {
            foreach ($request->shunting_staff as $staff) {
                $name = trim($staff['name'] ?? '');
                $desg = trim($staff['designation'] ?? '');
                if ($name || $desg) {
                    $staffData[] = ['name' => $name, 'designation' => $desg];
                }
            }
        }

        // Strip old shunting staff tag from remark before saving
        $baseRemark = preg_replace('/\\n?\[SHUNTING_STAFF:.*?\]/s', '', $inspection->remark ?? '');
        $remarkText = trim($request->remark ?? $baseRemark);
        if (!empty($staffData)) {
            $remarkText .= "\n[SHUNTING_STAFF:" . json_encode($staffData, JSON_UNESCAPED_UNICODE) . "]";
        }

        $inspection->update([
            'date_of_inspection'       => $request->date_of_inspection,
            'designation'              => $request->designation,
            'movement'                 => $request->movement,
            'type_of_inspection'       => $request->type_of_inspection,
            'day_night'                => $request->day_night ?? 'DAY',
            'type_of_ambush_check'     => $request->type_of_ambush_check,
            'station_yard'             => $request->station_yard,
            'level_crossing_no'        => $request->level_crossing_no,
            'train_number'             => $request->train_number,
            'section'                  => $request->section,
            'line_number_load_stable'  => $request->line_number_load_stable,
            'type_of_load_load_stable' => $request->type_of_load_load_stable,
            't806_number'              => $request->t806_number,
            'shunting_observation_of'  => $request->shunting_observation_of,
            'major_deficiencies'       => $request->major_deficiencies,
            'action_taken'             => $request->action_taken,
            'remark'                   => $remarkText,
            'created_by'               => $request->created_by,
        ]);

        return redirect()->route('master.ti_daily_inspection.index')->with('success', 'TI Daily Inspection Record updated successfully');
    }

    public function destroy($id)
    {
        $inspection = Inspection::findOrFail(decrypt($id));
        $inspection->delete();

        return redirect()->route('master.ti_daily_inspection.index')->with('success', 'TI Daily Inspection Record deleted successfully');
    }
}
