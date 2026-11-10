<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Station;
use App\Models\User;
use App\Models\MasterEmployee;
use App\Models\AmenityCategory;
use App\Models\Designation;
use App\Models\Rectify;
use App\Models\Diesellobby;
use App\Models\EOEmployee;
use App\Models\CoachingRakeType;
use App\Models\GoodsRakeType;
use App\Models\AreaOfProficiency;
use Illuminate\Support\Facades\Validator;

class DropdownController extends Controller
{
    public function departments()
    {
        return response()->json(
            Department::where('status', 1)->get()
        );
    }

    public function departmentById($id)
    {
        $department = Department::where('id', $id)
            ->where('status', 1)
            ->first();

        if (!$department) {
            return response()->json([
                'status' => false,
                'message' => 'Department not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $department
        ]);
    }

    public function designations()
    {
        return response()->json(
            Designation::where('status', 1)->get()
        );
    }
    public function designationsById($id)
    {
        $designation = Designation::where('id', $id)
            ->where('status', 1)
            ->first();

        if (!$designation) {
            return response()->json([
                'status' => false,
                'message' => 'Designation not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $designation
        ]);
    }

    public function stationsById($id)
    {
        // Station ko id se fetch karo
        $station = Station::find($id);

        if (!$station) {
            return response()->json([
                'status' => false,
                'message' => 'Station not found',
                'data' => []
            ], 404);
        }

        // Response return karo
        return response()->json([
            'status' => true,
            'message' => 'Station fetch successful',
            'data' => $station
        ]);
    }

    public function stations()
    {
        // Step 1: Get logged-in user
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        // Step 2: Get phone number
        $phone = $user->phone_number;

        // Step 3: Find employee by phone
        $employee = MasterEmployee::where('mobile', $phone)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        // Step 4: Get stations (only active if needed)
        $stations = $employee->stations()->where('status', 1)->get();

        // Step 5: Return response
        return response()->json([
            'status' => true,
            'data' => $stations
        ]);
    }
    public function allStations()
    {


        // Step 4: Get stations (only active if needed)
        $stations = Station::get();

        // dd($stations);

        // Step 5: Return response
        return response()->json([
            'status' => true,
            'data' => $stations
        ]);
    }

    public function coachingRake()
    {
        // Step 1: Get logged-in user
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $rake = CoachingRakeType::get();


        // Step 5: Return response
        return response()->json([
            'status' => true,
            'data' => $rake
        ]);
    }
    public function goodsRake()
    {
        // Step 1: Get logged-in user
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $rake = GoodsRakeType::get();

        // Step 5: Return response
        return response()->json([
            'status' => true,
            'data' => $rake
        ]);
    }

    // public function employeesByStation($stationId)
    // {
    //     $station = Station::find($stationId);

    //     if (!$station) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Station not found'
    //         ], 404);
    //     }

    //     $employees = $station->employees()
    //         ->where('master_employees.status', 1)
    //         ->select(
    //             'master_employees.id',
    //             'master_employees.name',
    //             'master_employees.usmid',
    //             'master_employees.mobile',
    //             'master_employees.department_id',
    //             'master_employees.designation_id'
    //         )
    //         ->get();

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Employees fetched successfully',
    //         'data' => $employees
    //     ]);
    // }

    public function employeesByStation($stationId)
    {
        $station = Station::find($stationId);

        if (!$station) {
            return response()->json([
                'status' => false,
                'message' => 'Station not found'
            ], 404);
        }

        $employees = $station->employees()
            ->where('master_employees.status', 1)
            ->join('designations', 'master_employees.designation_id', '=', 'designations.id')
            ->select(
                'master_employees.id',
                'master_employees.name',
                'master_employees.usmid',
                'master_employees.mobile',
                'master_employees.department_id',
                'master_employees.designation_id',
                'designations.name as designation_name'
            )
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Employees fetched successfully',
            'data' => $employees
        ]);
    }


    public function stationsByEmployee($employeeId)
    {
        $employee = MasterEmployee::with('stations')->find($employeeId);

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Stations fetched successfully',
            'data' => [
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'usmid' => $employee->usmid,
                    'mobile' => $employee->mobile,
                ],
                'stations' => $employee->stations,
                'total_stations' => $employee->stations->count(),
            ]
        ]);
    }


    // public function employeesById($id)
    // {
    //     $employee = MasterEmployee::find($id);

    //     if (!$employee) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Employee not found'
    //         ], 404);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Employee fetched successfully',
    //         'data' => $employee
    //     ]);
    // }

    public function employeesById($id)
    {
        $employee = MasterEmployee::with([
            'department:id,name',
            'designation:id,name',
            'stations:id,code,name'
        ])->find($id);

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Employee fetched successfully',
            'data' => $employee
        ]);
    }
    


    public function amenityCategories()
    {
        return response()->json(
            AmenityCategory::where('status', 1)->get()
        );
    }
    public function timinline()
    {
        $data = [];

        for ($i = 1; $i <= 10; $i++) {
            $data[] = [
                'id' => $i,
                'value' => $i
            ];
        }

        return response()->json($data);
    }

    public function assignTo(Request $request)
    {
        $department_id = $request->department_id;

        if (!$department_id) {
            return response()->json([
                'status' => false,
                'message' => 'Department ID is required'
            ], 400);
        }

        // ✅ Check department exists
        $department = Department::find($department_id);

        // dd($department);

        if (!$department) {
            return response()->json([
                'status' => false,
                'message' => 'Department not found'
            ], 404);
        }

        // ✅ Get designation IDs from Rectify table
        $designationIds = Rectify::where('department_id', $department_id)
            ->pluck('designation_id');

        // dd($designationIds);

        // ✅ Fetch only those designations
        $designations = Designation::whereIn('id', $designationIds)
            ->where('status', 1)
            ->select('id', 'name')
            ->get();

        // dd($designations);

        if ($designations->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No designations found in rectify',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Filtered designation list fetched successfully',
            'data' => $designations
        ]);
    }

    public function employeesByDepartment($id)
    {
        $employees = MasterEmployee::where('department_id', $id)
            ->where('status', 1)
            ->select('id', 'name', 'mobile')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $employees
        ]);
    }

    public function loadStablingEmployee()
    {
        $employees = EOEmployee::where('status', 1)
            ->with(['diesellobby' => function($query) {
                $query->select('id', 'name', 'code');
            }])
            ->get();

        $data = $employees->map(function ($emp) {
            return [
                'id' => $emp->id,
                'diesel_lobby_id' => $emp->diesel_lobby_id,
                'hrms_id' => $emp->hrms_id,
                'name' => $emp->name,
                'designation_id' => $emp->designation_id,
                'lobby_name' => $emp->diesellobby->name ?? null,
                'lobby_code' => $emp->diesellobby->code ?? null,
            ];
        });

        return response()->json($data);
    }


    public function getEmployeeByDieselLobbyId($diesel_lobby_id)
    {
        $employees = EoEmployee::with(['designation:id,name'])
            ->where('diesel_lobby_id', $diesel_lobby_id)
            ->get();

        if ($employees->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Employees not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'count' => $employees->count(),
            'data' => $employees->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'diesel_lobby_id' => $employee->diesel_lobby_id,
                    'designation_id' => $employee->designation_id,
                    'designation_name' => $employee->designation?->name,
                ];
            })
        ]);
    }

    // area of proficiency 
    public function getProficiency()
    {
        return response()->json(
            AreaOfProficiency::where('status', 1)->get()
        );
    }




}
