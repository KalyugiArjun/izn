<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movement;
use App\Models\TypeOfInspection;
use Illuminate\Http\JsonResponse;
use App\Models\Diesellobby;
use App\Models\RunningRoomLocation;
use Illuminate\Http\Request;
use App\Models\InspectionMovement;
use App\Models\RegularInspection;
use App\Models\AmbushInspection;
use App\Models\StaffCounseling;

use App\Models\SafetyDrive;
use App\Models\TypeOfLeave;
use App\Models\AttendBlock;
use App\Models\TypeOfAmbush;

use App\Models\MasterEmployee;
use App\Models\Designation;
use App\Models\Inspection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;







class InspectionController extends Controller
{
    
// ========================== TRAFFIC INSPECTOR HQ ==================================


    private function getAuthEmployeeId()
    {
        // Adjust this line as per your User <-> Employee relationship
        return auth()->user()->employee_id ?? auth()->id();
    }

//------------------- drop menu HQ ----------------------------

    public function movements(): JsonResponse{
        $movements = Movement::where('status', 1)
            ->select('id', 'name')
            // ->orderBy('name')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Movement list fetched successfully.',
            'data' => $movements
        ], 200);
    }

    public function typeofinspections(): JsonResponse{
        $types = TypeOfInspection::select('id', 'name')
            // ->orderBy('name')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Type of inspection list fetched successfully.',
            'data' => $types
        ], 200);
    }

    public function getDieselLobbies()
    {
        $dieselLobbies = Diesellobby::where('status', 1)
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Diesel Lobbies fetched successfully',
            'data' => $dieselLobbies
        ]);
    }

    public function employeesByDieselLobby($id)
    {
            $dieselLobby = Diesellobby::with('employees')->find($id);

            if (!$dieselLobby) {
                return response()->json([
                    'status' => false,
                    'message' => 'Diesel Lobby not found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Employees fetched successfully',
                'data' => [
                    'diesel_lobby' => [
                        'id'   => $dieselLobby->id,
                        'code' => $dieselLobby->code,
                        'name' => $dieselLobby->name,
                    ],
                    'employees' => $dieselLobby->employees,
                    'total_employees' => $dieselLobby->employees->count(),
                ]
            ]);
    }

    public function getRunningRoomLocations()
    {
        $locations = RunningRoomLocation::where('status', 1)
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Running Room Locations fetched successfully',
            'data' => $locations
        ]);
    }

    public function employeesByRunningRoomLocation($id)
    {
            $location = RunningRoomLocation::with('employees')->find($id);

            if (!$location) {
                return response()->json([
                    'status' => false,
                    'message' => 'Running Room Location not found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Employees fetched successfully',
                'data' => [
                    'running_room_location' => [
                        'id'   => $location->id,
                        'code' => $location->code,
                        'name' => $location->name,
                    ],
                    'employees' => $location->employees->map(function ($employee) {
                        return [
                            'id' => $employee->id,
                            'name' => $employee->name,
                            'usmid' => $employee->usmid,
                            'mobile' => $employee->mobile,
                        ];
                    }),
                    'total_employees' => $location->employees->count(),
                ]
            ]);
    }

    public function safetyDrive() : JsonResponse {
        $types = SafetyDrive::select('id','name')->get();

        return response()->json([
            'status'    => true,
            'message'   => 'Safety Drive list fetch Successfully.',
            'data'      => $types
        ], 200);
    }

    public function typeOfLeave(): JsonResponse{
        $types = TypeOfLeave::select('id', 'name')
            // ->orderBy('name')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Type of Leave list fetched successfully.',
            'data' => $types
        ], 200);
    }

    public function attendBlock(): JsonResponse{
        $types = AttendBlock::select('id', 'name')
            // ->orderBy('name')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Attend Block list fetched successfully.',
            'data' => $types
        ], 200);
    }


    public function typeOfAmbushs()
    {
        $data = TypeOfAmbush::where('status', 1)
            // ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'status' => true,
            'message' => 'Type of Ambush list fetched successfully.',
            'data' => $data,
        ]);
    }



// ------------------ inspection TI HQ ---------------------------

    
    // search, filter, pagination added list 
    // public function index(Request $request): JsonResponse
    // {
    //     $authEmployeeId = $this->getAuthEmployeeId();
    //     $perPage = min((int) $request->get('per_page', 10), 50);
    //     $search = trim($request->get('search', ''));
    //     $status = $request->get('status', null);

    //     $query = InspectionMovement::query()
    //         ->where('employee_id', $authEmployeeId)
    //         ->when($status === 'ambush', function ($q) {
    //             // ✅ Ambush case: sirf movement_id = 3
    //             $q->where('movement_id', 3)
    //             ->with(['movement', 'ambushInspection', 'ambushInspection.ambushType:id,name',]);
    //         }, function ($q) {
    //             // ✅ Default case: movement_id != 3
    //             $q->where('movement_id', '!=', 3)
    //             ->with(['movement', 'regularInspection']);
    //         })
            
    //        ->when($search !== '', function ($q) use ($search, $status) {
    //         $q->where(function ($inner) use ($search, $status) {

    //             $inner->where('remarks', 'like', "%{$search}%")
    //                 ->orWhere('created_at', 'like', "%{$search}%")
    //                 ->orWhereHas('movement', function ($m) use ($search) {
    //                     $m->where('name', 'like', "%{$search}%");
    //                 });

    //             if ($status === 'ambush') {
    //                 $inner->orWhereHas('ambushInspection', function ($ambush) use ($search) {
    //                     $ambush->whereHas('ambushType', function ($type) use ($search) {
    //                         $type->where('name', 'like', "%{$search}%");
    //                     });
    //                 });
    //             }
    //         });
    //     })




    //         ->orderByDesc('created_at');

    //     $data = $query->paginate($perPage);

    //      return response()->json([
    //         'status' => true,
    //         'message' => $status === 'ambush'
    //             ? 'Ambush Inspection List'
    //             : 'Inspection Movement List',
    //         'data' => $data->items(),
    //         'pagination' => [
    //             'current_page' => $data->currentPage(),
    //             'last_page' => $data->lastPage(),
    //             'per_page' => $data->perPage(),
    //             'total' => $data->total(),
    //             'from' => $data->firstItem(),
    //             'to' => $data->lastItem(),
    //         ],
    //     ]);
    // }

    public function index(Request $request): JsonResponse
    {
        $authEmployeeId = $this->getAuthEmployeeId();
        $perPage = min((int) $request->get('per_page', 10), 50);
        $search = trim($request->get('search', ''));
        $status = $request->get('status', null);

        $query = InspectionMovement::query()
            ->where('employee_id', $authEmployeeId)
            ->when($status === 'ambush', function ($q) {
                $q->where('movement_id', 3)
                ->with(['movement', 'ambushInspection', 'ambushInspection.ambushType:id,name', ]);
            }, function ($q) {
                $q->where('movement_id', '!=', 3)
                ->with(['movement', 'regularInspection', 'regularInspection.type:id,name']);
            });

        if ($search !== '') {
            $query->where(function ($inner) use ($search, $status) {
                // ✅ Only search columns that EXIST in inspection_movements
                $inner->where('officer_name', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%")
                    ->orWhere('train_no', 'like', "%{$search}%")
                    ->orWhere('block_section', 'like', "%{$search}%")
                    ->orWhere('gps_location', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%")
                    ->orWhere('inspection_date', 'like', "%{$search}%")
                    ->orWhere('created_at', 'like', "%{$search}%");

                // Movement name (relation)
                $inner->orWhereHas('movement', function ($m) use ($search) {
                    $m->where('name', 'like', "%{$search}%");
                });

                // Ambush-specific: search in ambushType name
                if ($status === 'ambush') {
                    $inner->orWhereHas('ambushInspection', function ($ambush) use ($search) {
                        $ambush->whereHas('ambushType', function ($type) use ($search) {
                            $type->where('name', 'like', "%{$search}%");
                        });
                    });
                }
            });
        }

        $query->orderByDesc('created_at');

        $data = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => $status === 'ambush'
                ? 'Ambush Inspection List'
                : 'Inspection Movement List',
            'data' => $data->items(),
            'pagination' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem(),
            ],
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'movement_id'        => 'sometimes|nullable|integer',
            'inspection_date'    => 'sometimes|nullable|date',
            'designation'        => 'sometimes|nullable|string|max:255',
            'officer_name'       => 'sometimes|nullable|string|max:255',
            'gps_location'       => 'sometimes|nullable|string',

            'safety_drive_id'    => 'sometimes|nullable|integer',
            'goods_passenger'    => 'sometimes|nullable|string|max:100',
            'train_no'           => 'sometimes|nullable|string|max:100',
            'block_section'      => 'sometimes|nullable|string|max:255',
            'station_id'         => 'sometimes|nullable|integer',
            'reason'             => 'sometimes|nullable|string',
            'type_of_leave_id'   => 'sometimes|nullable|integer',
            'attend_block_id'    => 'sometimes|nullable|integer',

            // ✅ File validation
            'joint_note_pdf'     => 'sometimes|nullable|file|mimes:pdf|max:10240',
            'survey_pdf'         => 'sometimes|nullable|file|mimes:pdf|max:10240',
        ]);

        // ✅ Handle file uploads
        $jointNotePath = null;
        $surveyPath = null;

        if ($request->hasFile('joint_note_pdf')) {
            $jointNotePath = $request->file('joint_note_pdf')->store('joint_notes', 'public');
        }

        if ($request->hasFile('survey_pdf')) {
            $surveyPath = $request->file('survey_pdf')->store('surveys', 'public');
        }

        // ✅ Create InspectionMovement record
        $inspection = InspectionMovement::create([
            'employee_id'        => $this->getAuthEmployeeId(),
            'movement_id'        => $request->movement_id,
            'inspection_date'    => $request->inspection_date,
            'designation'        => $request->designation,
            'officer_name'       => $request->officer_name,
            'gps_location'       => $request->gps_location,

            'safety_drive_id'    => $request->safety_drive_id,
            'goods_passenger'    => $request->goods_passenger,
            'train_no'           => $request->train_no,
            'block_section'      => $request->block_section,
            'station_id'         => $request->station_id,
            'reason'             => $request->reason,
            'type_of_leave_id'   => $request->type_of_leave_id,
            'attend_block_id'    => $request->attend_block_id,

            'joint_note_pdf'     => $jointNotePath,
            'survey_pdf'         => $surveyPath,

            'created_by'         => auth()->id(),
        ]);

        // ✅ Movement-specific insert
        if ($request->movement_id == 2) {
            RegularInspection::create([
                'inspection_id' => $inspection->id,
                // 'station_id'    => $request->station_id,
                // 'train_no'      => $request->train_no,
                // 'from_time'     => $request->from_time ?? null,
                // 'to_time'       => $request->to_time ?? null,
                // 'day_night'     => $request->day_night ?? null,
                'created_by'    => auth()->id(),
            ]);
        }

        if ($request->movement_id == 3) {
            AmbushInspection::create([
                'inspection_id' => $inspection->id,
                'created_by'    => auth()->id(),
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Inspection Movement Created Successfully',
            'data'    => $inspection
        ], 201);
    }



    /**
     * Show
     * Sirf apna record hi dekh payega.
     */
    public function show($id)
    {
        $inspection = InspectionMovement::with([
            // 'employee',
            'movement',
            'regularInspection',
            'ambushInspection',
            'ambushInspection.staffName:id,name',
            'ambushInspection.lobby:id,name',
        ])->find($id);

        if (!$inspection) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ], 404);
        }

        if ($inspection->employee_id != $this->getAuthEmployeeId()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. This record does not belong to you.'
            ], 403);
        }

        if ($inspection->ambushInspection && !empty($inspection->ambushInspection->shunting_staff)) {

            $staffs = collect($inspection->ambushInspection->shunting_staff);

            $employeeIds = $staffs->pluck('employee_id')->toArray();
            $designationIds = $staffs->pluck('designation_id')->toArray();

            $employees = MasterEmployee::whereIn('id', $employeeIds)
                ->get()
                ->keyBy('id');

            $designations = Designation::whereIn('id', $designationIds)
                ->get()
                ->keyBy('id');

            $inspection->ambushInspection->shunting_staff_details =
                $staffs->map(function ($staff) use ($employees, $designations) {

                    return [
                        'employee_id' => $staff['employee_id'],
                        'employee_name' => $employees[$staff['employee_id']]->name ?? null,

                        'designation_id' => $staff['designation_id'],
                        'designation_name' => $designations[$staff['designation_id']]->name ?? null,
                    ];
                });
        }

        return response()->json([
            'status' => true,
            'data' => $inspection
        ]);
    }



    /**
     * Update
     * Sirf apna record hi update kar payega. employee_id request se
     * change nahi hone diya jayega.
     */


    public function update(Request $request, $id)
    {
        $inspection = InspectionMovement::find($id);

        if (!$inspection) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ], 404);
        }

        if ($inspection->employee_id != $this->getAuthEmployeeId()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. This record does not belong to you.'
            ], 403);
        }

        $request->validate([
            'movement_id'      => 'sometimes|nullable|integer',
            'inspection_date'  => 'sometimes|nullable|date',
            'designation'      => 'sometimes|nullable|string|max:255',
            'officer_name'     => 'sometimes|nullable|string|max:255',
            'gps_location'     => 'sometimes|nullable|string',

            'safety_drive_id'  => 'sometimes|nullable|integer',
            'goods_passenger'  => 'sometimes|nullable|string|max:100',
            'train_no'         => 'sometimes|nullable|string|max:100',
            'block_section'    => 'sometimes|nullable|string|max:255',
            'station_id'       => 'sometimes|nullable|integer',
            'reason'           => 'sometimes|nullable|string',
            'type_of_leave_id' => 'sometimes|nullable|integer',
            'attend_block_id'  => 'sometimes|nullable|integer',

            // Files
            'joint_note_pdf'   => 'sometimes|nullable|file|mimes:pdf|max:10240',
            'survey_pdf'       => 'sometimes|nullable|file|mimes:pdf|max:10240',
        ]);

        $data = $request->only([
            'movement_id',
            'inspection_date',
            'designation',
            'officer_name',
            'gps_location',
            'safety_drive_id',
            'goods_passenger',
            'train_no',
            'block_section',
            'station_id',
            'reason',
            'type_of_leave_id',
            'attend_block_id',
        ]);

        // Joint Note PDF Update
        if ($request->hasFile('joint_note_pdf')) {

            if ($inspection->joint_note_pdf &&
                Storage::disk('public')->exists($inspection->joint_note_pdf)) {

                Storage::disk('public')->delete($inspection->joint_note_pdf);
            }

            $data['joint_note_pdf'] = $request->file('joint_note_pdf')
                ->store('joint_notes', 'public');
        }

        // Survey PDF Update
        if ($request->hasFile('survey_pdf')) {

            if ($inspection->survey_pdf &&
                Storage::disk('public')->exists($inspection->survey_pdf)) {

                Storage::disk('public')->delete($inspection->survey_pdf);
            }

            $data['survey_pdf'] = $request->file('survey_pdf')
                ->store('surveys', 'public');
        }

        $data['updated_by'] = auth()->id();

        $inspection->update($data);

        return response()->json([
            'status'  => true,
            'message' => 'Inspection Movement Updated Successfully',
            'data'    => $inspection->fresh()
        ]);
    }

    /**
     * Delete
     */

    // public function destroy($id)
    // {
    //     $inspection = InspectionMovement::find($id);

    //     if (!$inspection) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Record not found'
    //         ], 404);
    //     }

    //     if ($inspection->employee_id != $this->getAuthEmployeeId()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Unauthorized. This record does not belong to you.'
    //         ], 403);
    //     }

    //     DB::beginTransaction();

    //     try {

    //         // Delete Inspection PDFs
    //         if ($inspection->joint_note_pdf) {
    //             Storage::disk('public')->delete($inspection->joint_note_pdf);
    //         }

    //         if ($inspection->survey_pdf) {
    //             Storage::disk('public')->delete($inspection->survey_pdf);
    //         }

    //         // Get Ambush Record
    //         // $ambush = AmbushInspection::where(
    //         //     'inspection_id',
    //         //     $inspection->id
    //         // )->first();

    //         // if ($ambush) {

    //         //     // Delete Audio Files
    //         //     if ($ambush->voice_recording_lc) {
    //         //         Storage::disk('public')->delete(
    //         //             $ambush->voice_recording_lc
    //         //         );
    //         //     }

    //         //     if ($ambush->voice_recording_train) {
    //         //         Storage::disk('public')->delete(
    //         //             $ambush->voice_recording_train
    //         //         );
    //         //     }

    //         //     $ambush->delete();
    //         // }

    //         // Get Ambush Record
    //         $ambush = AmbushInspection::where(
    //             'inspection_id',
    //             $inspection->id
    //         )->first();

    //         if ($ambush) {

    //             // Delete LC Audio
    //             if (!empty($ambush->voice_recording_lc)) {

    //                 $lcPath = $ambush->voice_recording_lc;

    //                 \Log::info('Deleting LC Audio', [
    //                     'db_path' => $lcPath,
    //                     'full_path' => Storage::disk('public')->path($lcPath),
    //                     'exists' => Storage::disk('public')->exists($lcPath),
    //                 ]);

    //                 Storage::disk('public')->delete($lcPath);
    //             }

    //             // Delete Train Audio
    //             if (!empty($ambush->voice_recording_train)) {

    //                 $trainPath = $ambush->voice_recording_train;

    //                 \Log::info('Deleting Train Audio', [
    //                     'db_path' => $trainPath,
    //                     'full_path' => Storage::disk('public')->path($trainPath),
    //                     'exists' => Storage::disk('public')->exists($trainPath),
    //                 ]);

    //                 Storage::disk('public')->delete($trainPath);
    //             }

    //             $ambush->delete();
    //         }


    //         // Delete Regular Inspection
    //         RegularInspection::where(
    //             'inspection_id',
    //             $inspection->id
    //         )->delete();

    //         // Delete Parent Record
    //         $inspection->delete();

    //         DB::commit();

    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Inspection Movement Deleted Successfully'
    //         ]);

    //     } catch (\Exception $e) {

    //         DB::rollBack();

    //         return response()->json([
    //             'status' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function destroy($id)
    {
        $inspection = InspectionMovement::find($id);

        if (!$inspection) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ], 404);
        }

        if ($inspection->employee_id != $this->getAuthEmployeeId()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. This record does not belong to you.'
            ], 403);
        }

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Delete Inspection PDFs
            |--------------------------------------------------------------------------
            */

            if (!empty($inspection->joint_note_pdf)) {
                Storage::disk('public')->delete(
                    $inspection->joint_note_pdf
                );
            }

            if (!empty($inspection->survey_pdf)) {
                Storage::disk('public')->delete(
                    $inspection->survey_pdf
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Get Ambush Record
            |--------------------------------------------------------------------------
            */

            $ambush = AmbushInspection::where(
                'inspection_id',
                $inspection->id
            )->first();

            if ($ambush) {

                /*
                |--------------------------------------------------------------------------
                | Delete LC Audio
                |--------------------------------------------------------------------------
                */

                if (!empty($ambush->voice_recording_lc)) {

                    $lcPath = $ambush->voice_recording_lc;

                    // \Log::info('Deleting LC Audio', [
                    //     'path' => $lcPath,
                    //     'exists' => Storage::disk('public')->exists($lcPath),
                    // ]);

                    Storage::disk('public')->delete($lcPath);
                }


                /*
                |--------------------------------------------------------------------------
                | Delete Train Audio
                |--------------------------------------------------------------------------
                */

                if (!empty($ambush->voice_recording_train)) {

                    $trainPath = $ambush->voice_recording_train;

                    // \Log::info('Deleting Train Audio', [
                    //     'path' => $trainPath,
                    //     'exists' => Storage::disk('public')->exists($trainPath),
                    // ]);

                    Storage::disk('public')->delete($trainPath);
                }


                /*
                |--------------------------------------------------------------------------
                | Delete Ambush DB Record
                |--------------------------------------------------------------------------
                */

                $ambush->delete();
            }


            /*
            |--------------------------------------------------------------------------
            | Delete Regular Inspection
            |--------------------------------------------------------------------------
            */

            RegularInspection::where(
                'inspection_id',
                $inspection->id
            )->delete();


            /*
            |--------------------------------------------------------------------------
            | Delete Parent Inspection
            |--------------------------------------------------------------------------
            */

            $inspection->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Inspection Movement Deleted Successfully'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            // \Log::error('Inspection Movement Delete Error', [
            //     'inspection_id' => $id,
            //     'error' => $e->getMessage(),
            // ]);

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }





//------------------- Regular TI HQ ------------------------------

    /**
     * List
     * Sirf un RegularInspection records ki list jinka parent
     * InspectionMovement logged-in employee ka hai.
     */
    public function regularIndex()
    {
        $authEmployeeId = $this->getAuthEmployeeId();

        $data = RegularInspection::with([
            'inspection',
            'station',
            'type'
        ])
        ->whereHas('inspection', function ($q) use ($authEmployeeId) {
            $q->where('employee_id', $authEmployeeId);
        })
        ->latest()
        ->get();

        return response()->json([
            'status' => true,
            'message' => 'Regular Inspection List',
            'data' => $data
        ]);
    }

    /**
     * Store
     * inspection_id jis InspectionMovement ka diya gaya hai, wo
     * logged-in employee ka hi hona chahiye, tabhi record ban payega.
     */
    public function regularStore(Request $request)
    {
        $request->validate([
            'inspection_id'         => 'required|integer',
            'day_night'             => 'required|in:DAY,NIGHT',
            'type_of_inspection_id' => 'required|integer',
            'station_id'            => 'nullable|integer',
            'station_lc_gate'       => 'nullable|in:Station,LC Gate',
            'gate_no'               => 'nullable|string|max:100',
            'block_section'         => 'nullable|string|max:255',
            'engg_traffic'          => 'nullable|string|max:255',
            'goods_passenger'       => 'nullable|in:Goods,Passenger',
            'train_no'              => 'nullable|string|max:100',
            'location_id'           => 'nullable|integer',
            'major_deficiency'      => 'nullable|string',
            'write_deficiency'      => 'nullable|string',
        ]);

        $parentInspection = InspectionMovement::find($request->inspection_id);

        if (!$parentInspection) {
            return response()->json([
                'status' => false,
                'message' => 'Parent Inspection Movement record not found'
            ], 404);
        }

        if ($parentInspection->employee_id != $this->getAuthEmployeeId()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. This inspection does not belong to you.'
            ], 403);
        }

        $inspection = RegularInspection::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Regular Inspection Created Successfully',
            'data' => $inspection
        ], 201);
    }

    /**
     * Show
     * Sirf apne parent InspectionMovement wala record hi dekh payega.
     */
    public function regularShow($id)
    {
        $inspection = RegularInspection::with([
            'inspection',
            'station',
            'type'
        ])->find($id);

        if (!$inspection) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ], 404);
        }

        if (!$inspection->inspection || $inspection->inspection->employee_id != $this->getAuthEmployeeId()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. This record does not belong to you.'
            ], 403);
        }

        return response()->json([
            'status' => true,
            'data' => $inspection
        ]);
    }

    /**
     * Update
     * Sirf apne parent InspectionMovement wala record hi update kar payega.
     */
    // public function regularUpdate(Request $request, $id)
    // {
    //     $inspection = RegularInspection::with('inspection')->find($id);

    //     if (!$inspection) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Record not found'
    //         ], 404);
    //     }

    //     if (!$inspection->inspection || $inspection->inspection->employee_id != $this->getAuthEmployeeId()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Unauthorized. This record does not belong to you.'
    //         ], 403);
    //     }

    //     $request->validate([
    //         'inspection_id'         => 'required|integer',
    //         'day_night'             => 'required|in:DAY,NIGHT',
    //         'type_of_inspection_id' => 'required|integer',
    //         'station_id'            => 'nullable|integer',
    //         'station_lc_gate'       => 'nullable|in:Station,LC Gate',
    //         'gate_no'               => 'nullable|string|max:100',
    //         'block_section'         => 'nullable|string|max:255',
    //         'engg_traffic'          => 'nullable|string|max:255',
    //         'goods_passenger'       => 'nullable|in:Goods,Passenger',
    //         'train_no'              => 'nullable|string|max:100',
    //         'location_id'           => 'nullable|integer',
    //         'major_deficiency'      => 'nullable|string',
    //         'write_deficiency'      => 'nullable|string',
    //     ]);

    //     // Agar request me inspection_id badla ho, uska bhi ownership check kar lete hain
    //     if ($request->inspection_id != $inspection->inspection_id) {
    //         $newParent = InspectionMovement::find($request->inspection_id);
    //         if (!$newParent || $newParent->employee_id != $this->getAuthEmployeeId()) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Unauthorized. Target inspection does not belong to you.'
    //             ], 403);
    //         }
    //     }

    //     $inspection->update($request->all());

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Regular Inspection Updated Successfully',
    //         'data' => $inspection
    //     ]);
    // }

    public function regularUpdate(Request $request, $id)
    {
        // ✅ Find RegularInspection record by inspection_id instead of id
        $inspection = RegularInspection::with('inspection')
            ->where('inspection_id', $id)
            ->first();

        if (!$inspection) {
            // \Log::info("Record not found for inspection_id: ".$id);
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ], 404);
        }

        if (!$inspection->inspection || $inspection->inspection->employee_id != $this->getAuthEmployeeId()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. This record does not belong to you.'
            ], 403);
        }

        $request->validate([
            'inspection_id'         => 'required|integer',
            'day_night'             => 'required|in:DAY,NIGHT',
            'type_of_inspection_id' => 'required|integer',
            'station_id'            => 'nullable|integer',
            'station_lc_gate'       => 'nullable|in:Station,LC Gate',
            'gate_no'               => 'nullable|string|max:100',
            'block_section'         => 'nullable|string|max:255',
            'engg_traffic'          => 'nullable|string|max:255',
            'goods_passenger'       => 'nullable|in:Goods,Passenger',
            'train_no'              => 'nullable|string|max:100',
            'location_id'           => 'nullable|integer',
            'major_deficiency'      => 'nullable|string',
            'write_deficiency'      => 'nullable|string',
        ]);

        //  Log for local test
        // \Log::info("Updating RegularInspection ID: ".$inspection->id." with inspection_id: ".$inspection->inspection_id);

        $inspection->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Regular Inspection Updated Successfully',
            'data' => $inspection
        ]);
    }


    /**
     * Delete
     * Sirf apne parent InspectionMovement wala record hi delete kar payega.
     */
    public function regularDestroy($id)
    {
        $inspection = RegularInspection::with('inspection')->find($id);

        if (!$inspection) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ], 404);
        }

        if (!$inspection->inspection || $inspection->inspection->employee_id != $this->getAuthEmployeeId()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. This record does not belong to you.'
            ], 403);
        }

        $inspection->delete();

        return response()->json([
            'status' => true,
            'message' => 'Regular Inspection Deleted Successfully'
        ]);
    }




//------------------- ambush TI HQ -------------------------------

    /**
     * List
     * Sirf un AmbushInspection records ki list jinka parent
     * InspectionMovement logged-in employee ka hai.
     */
    public function ambushIndex()
    {
        $authEmployeeId = $this->getAuthEmployeeId();

        $data = AmbushInspection::with([
            'inspection',
            'station'
        ])
        ->whereHas('inspection', function ($q) use ($authEmployeeId) {
            $q->where('employee_id', $authEmployeeId);
        })
        ->latest()
        ->get();

        return response()->json([
            'status' => true,
            'message' => 'Ambush Inspection List',
            'data' => $data
        ]);
    }

    /**
     * Store
     * inspection_id jis InspectionMovement ka diya gaya hai, wo
     * logged-in employee ka hi hona chahiye, tabhi record ban payega.
     */

    public function ambushStore(Request $request)
    {
        $request->validate([
            'inspection_id'                  => 'required|integer',
            'station_id'                     => 'nullable|integer',
            'from_time'                      => 'nullable|string|max:100',
            'to_time'                        => 'nullable|string|max:100',
            'day_night'                      => 'nullable|in:DAY,NIGHT',
            'type_of_ambush_check'           => 'nullable|string|max:255',
            'lobby_id'                       => 'nullable|integer',
            'shunting_observation'            => 'nullable|string',
            't_806_no'                        => 'nullable|string|max:100',
            'train_no'                        => 'nullable|string|max:100',

            'shunting_staff_name'             => 'nullable|integer',
            'shunting_staff'                  => 'nullable|array',
            'shunting_staff.*.employee_id'    => 'required_with:shunting_staff|integer|exists:master_employees,id',
            'shunting_staff.*.designation_id' => 'required_with:shunting_staff|integer|exists:master_employees,id',

            'load_type'                       => 'nullable|string|max:255',
            'loco_self_proppeled'             => 'nullable|string|max:255',
            'track_machine'                   => 'nullable|string|max:255',
            'total_number_of_load'            => 'nullable|string|max:100',
            'running_line'                    => 'nullable|string|max:255',
            'stabling_line_no'                => 'nullable|string|max:100',
            'block_section'                   => 'nullable|string|max:255',
            'block_taken_by'                  => 'nullable|string|max:255',

            'date_of_voice_recording'         => 'nullable|date',

            // Existing time columns
            'lc_gate_no'                      => 'nullable|string|max:100',
            'voice_recording_time_lc'         => 'nullable|string|max:100',
            'voice_recording_time_train'      => 'nullable|string|max:100',

            'actual_departure_time'           => 'nullable|string|max:100',
            'actual_arrival_time'             => 'nullable|string|max:100',

            'staff_name'                      => 'nullable|integer',
            'designation'                     => 'nullable|string|max:255',
            'alcohol_test'                    => 'nullable|string|max:255',
            'alcohol_percentage'              => 'nullable|numeric|min:0|max:100',
            'major_deficiency'                => 'nullable|string',
            'write_deficiency'                => 'nullable|string',

            // NEW AUDIO FILES
            'voice_recording_lc'               => 'nullable|file|mimes:mp3,wav,m4a,aac,ogg|max:2048',
            'voice_recording_train'            => 'nullable|file|mimes:mp3,wav,m4a,aac,ogg|max:2048',
        ]);

        // Parent inspection check
        $parentInspection = InspectionMovement::find($request->inspection_id);

        if (!$parentInspection) {
            return response()->json([
                'status' => false,
                'message' => 'Parent Inspection Movement record not found'
            ], 404);
        }

        // Authorization check
        if ($parentInspection->employee_id != $this->getAuthEmployeeId()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. This inspection does not belong to you.'
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Prepare data
        |--------------------------------------------------------------------------
        */

        $data = $request->except([
            'voice_recording_lc',
            'voice_recording_train',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Upload LC Audio
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('voice_recording_lc')) {

            $file = $request->file('voice_recording_lc');

            $fileName = 'lc_' . time() . '_' . uniqid() . '.' .
                $file->getClientOriginalExtension();

            $file->storeAs(
                'ambush/audio',
                $fileName,
                'public'
            );

            $data['voice_recording_lc'] = 'ambush/audio/' . $fileName;
        }

        if ($request->hasFile('voice_recording_train')) {

            $file = $request->file('voice_recording_train');

            $fileName = 'train_' . time() . '_' . uniqid() . '.' .
                $file->getClientOriginalExtension();

            $file->storeAs(
                'ambush/audio',
                $fileName,
                'public'
            );

            $data['voice_recording_train'] = 'ambush/audio/' . $fileName;
        }

        /*
        |--------------------------------------------------------------------------
        | Upload Train Audio
        |--------------------------------------------------------------------------
        */

        // if ($request->hasFile('voice_recording_train')) {
        //     $file = $request->file('voice_recording_train');
        //     $fileName = 'train_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        //     $file->move(
        //         storage_path('app/public/ambush/audio'),
        //         $fileName
        //     );
        //     $data['voice_recording_train'] =
        //         'app/public/ambush/audio/' . $fileName;
        // }

        /*
        |--------------------------------------------------------------------------
        | Create Ambush Inspection
        |--------------------------------------------------------------------------
        */

        $inspection = AmbushInspection::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Ambush Inspection Created Successfully',
            'data' => $inspection
        ], 201);
    }

    /**
     * Show
     * Sirf apne parent InspectionMovement wala record hi dekh payega.
     */
    public function ambushShow($id)
    {
        $inspection = AmbushInspection::with([
            'inspection',
            'station',
            'lobby'
        ])->find($id);

        if (!$inspection) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ], 404);
        }

        if (!$inspection->inspection || $inspection->inspection->employee_id != $this->getAuthEmployeeId()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. This record does not belong to you.'
            ], 403);
        }

        return response()->json([
            'status' => true,
            'data' => $inspection
        ]);
    }

    /**
     * Update
     * Sirf apne parent InspectionMovement wala record hi update kar payega.
     */
    public function ambushUpdate(Request $request, $id)
    {
        // Find AmbushInspection record by inspection_id instead of id
        $inspection = AmbushInspection::with('inspection')
            ->where('inspection_id', $id)
            ->first();

        if (!$inspection) {
            // \Log::info("Record not found for inspection_id: ".$id);
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ], 404);
        }

        // Authorization check
        if (
            !$inspection->inspection ||
            $inspection->inspection->employee_id != $this->getAuthEmployeeId()
        ) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. This record does not belong to you.'
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $request->validate([
            'inspection_id'                    => 'required|integer',
            'station_id'                       => 'nullable|integer',
            'from_time'                        => 'nullable|string|max:100',
            'to_time'                          => 'nullable|string|max:100',
            'day_night'                        => 'nullable|in:DAY,NIGHT',
            'type_of_ambush_id'                => 'nullable|integer',
            'lobby_id'                         => 'nullable|integer',
            'shunting_observation'             => 'nullable|string',
            't_806_no'                         => 'nullable|string|max:100',
            'train_no'                         => 'nullable|string|max:100',

            'shunting_staff_name'              => 'nullable|integer',
            'shunting_staff'                   => 'nullable|array',
            'shunting_staff.*.employee_id'     => 'required_with:shunting_staff|integer|exists:master_employees,id',
            'shunting_staff.*.designation_id'  => 'required_with:shunting_staff|integer|exists:master_employees,id',

            'load_type'                        => 'nullable|string|max:255',
            'loco_self_proppeled'              => 'nullable|string|max:255',
            'track_machine'                    => 'nullable|string|max:255',
            'total_number_of_load'             => 'nullable|string|max:100',
            'running_line'                     => 'nullable|string|max:255',
            'stabling_line_no'                 => 'nullable|string|max:100',
            'block_section'                    => 'nullable|string|max:255',
            'block_taken_by'                   => 'nullable|string|max:255',

            'date_of_voice_recording'          => 'nullable|date',

            'lc_gate_no'                       => 'nullable|string|max:100',
            'voice_recording_time_train'       => 'nullable|string|max:100',
            'voice_recording_time_lc'          => 'nullable|string|max:100',

            'actual_departure_time'            => 'nullable|string|max:100',
            'actual_arrival_time'              => 'nullable|string|max:100',

            'staff_name'                       => 'nullable|integer',
            'designation'                      => 'nullable|string|max:255',
            'alcohol_test'                     => 'nullable|string|max:255',
            'alcohol_percentage'               => 'nullable|numeric|min:0|max:100',
            'major_deficiency'                 => 'nullable|string',
            'write_deficiency'                 => 'nullable|string',

            // AUDIO
            'voice_recording_lc'               => 'nullable|file|mimes:mp3,wav,m4a,aac,ogg|max:2048',
            'voice_recording_train'            => 'nullable|file|mimes:mp3,wav,m4a,aac,ogg|max:2048',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Parent inspection ownership
        |--------------------------------------------------------------------------
        */

        $parentInspection = InspectionMovement::find($request->inspection_id);

        if (!$parentInspection) {
            return response()->json([
                'status' => false,
                'message' => 'Parent Inspection Movement record not found'
            ], 404);
        }

        if ($parentInspection->employee_id != $this->getAuthEmployeeId()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. This inspection does not belong to you.'
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Prepare update data
        |--------------------------------------------------------------------------
        */

        $data = $request->except([
            'voice_recording_lc',
            'voice_recording_train',
        ]);

        /*
        |--------------------------------------------------------------------------
        | LC Audio Upload
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('voice_recording_lc')) {

            // Delete old LC audio
            if (
                $inspection->voice_recording_lc &&
                \Storage::disk('public')->exists($inspection->voice_recording_lc)
            ) {
                \Storage::disk('public')->delete(
                    $inspection->voice_recording_lc
                );
            }

            $file = $request->file('voice_recording_lc');

            $fileName = 'lc_' . time() . '_' . uniqid() . '.' .
                $file->getClientOriginalExtension();

            $file->storeAs(
                'ambush/audio',
                $fileName,
                'public'
            );

            $data['voice_recording_lc'] =
                'ambush/audio/' . $fileName;
        }

        /*
        |--------------------------------------------------------------------------
        | Train Audio Upload
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('voice_recording_train')) {

            // Delete old Train audio
            if (
                $inspection->voice_recording_train &&
                \Storage::disk('public')->exists($inspection->voice_recording_train)
            ) {
                \Storage::disk('public')->delete(
                    $inspection->voice_recording_train
                );
            }

            $file = $request->file('voice_recording_train');

            $fileName = 'train_' . time() . '_' . uniqid() . '.' .
                $file->getClientOriginalExtension();

            $file->storeAs(
                'ambush/audio',
                $fileName,
                'public'
            );

            $data['voice_recording_train'] =
                'ambush/audio/' . $fileName;
        }

        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        //  Log for local test
        // \Log::info("Updating AmbushInspection ID: ".$inspection->id." with inspection_id: ".$inspection->inspection_id);

        // $inspection->update($request->all());
        $inspection->update($data);
        $inspection->refresh();

        return response()->json([
            'status' => true,
            'message' => 'Ambush Inspection Updated Successfully',
            'data' => $inspection
        ]);
    }

    /**
     * Delete
     * Sirf apne parent InspectionMovement wala record hi delete kar payega.
     */
    public function ambushDestroy($id)
    {
        $inspection = AmbushInspection::with('inspection')->find($id);

        if (!$inspection) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ], 404);
        }

        if (!$inspection->inspection || $inspection->inspection->employee_id != $this->getAuthEmployeeId()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. This record does not belong to you.'
            ], 403);
        }

        $inspection->delete();

        return response()->json([
            'status' => true,
            'message' => 'Ambush Inspection Deleted Successfully'
        ]);
    }




//------------------- Staff Counseling TI HQ--------------

    public function staffCounselingIndex()
    {
        $authEmployeeId = $this->getAuthEmployeeId();

        $data = StaffCounseling::with([
            'inspection',
            'station',
        ])
        ->whereHas('inspection', function ($q) use ($authEmployeeId) {
            $q->where('employee_id', $authEmployeeId);
        })
        ->latest()
        ->get();

        return response()->json([
            'status' => true,
            'message' => 'Staff Counseling List',
            'data' => $data
        ]);
    }

    public function staffCounselingStore(Request $request)
    {
        $request->validate([
            'inspection_id' => 'required|integer',
            'station_id' => 'nullable|integer',
            'lc_gate' => 'nullable|string|max:100',
            'running_room_id' => 'nullable|integer',
            'diesel_lobby_id' => 'nullable|integer',
            'train_no' => 'nullable|string|max:100',

            'staff' => 'required|array|min:1',
            'staff.*.staff_id' => 'required|integer',
            'staff.*.designation_id' => 'required|integer',

            'counseling_type' => 'required|in:Individual,Group',
            'subject_of_counseling' => 'required|string',
        ]);

        $parentInspection = InspectionMovement::find($request->inspection_id);

        if (!$parentInspection) {
            return response()->json([
                'status' => false,
                'message' => 'Parent Inspection Movement record not found'
            ], 404);
        }

        if ($parentInspection->employee_id != $this->getAuthEmployeeId()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. This inspection does not belong to you.'
            ], 403);
        }

        $data = StaffCounseling::create([
            'inspection_id' => $request->inspection_id,
            'station_id' => $request->station_id,
            'lc_gate' => $request->lc_gate,
            'running_room_id' => $request->running_room_id,
            'diesel_lobby_id' => $request->diesel_lobby_id,
            'train_no' => $request->train_no,
            'staff' => $request->staff,
            'counseling_type' => $request->counseling_type,
            'subject_of_counseling' => $request->subject_of_counseling,
        ]);

        // $data = StaffCounseling::create($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Staff Counseling Created Successfully',
            'data' => $data
        ], 201);
    }

    public function staffCounselingShow($id)
    {
        $data = StaffCounseling::with([
            'inspection',
            'station',
        ])->find($id);

        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ], 404);
        }

        if (!$data->inspection || $data->inspection->employee_id != $this->getAuthEmployeeId()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. This record does not belong to you.'
            ], 403);
        }

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    public function staffCounselingUpdate(Request $request, $id)
    {
        $data = StaffCounseling::with('inspection')->find($id);

        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ], 404);
        }

        if (!$data->inspection || $data->inspection->employee_id != $this->getAuthEmployeeId()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. This record does not belong to you.'
            ], 403);
        }

        $request->validate([
            'inspection_id'           => 'required|integer',
            'station_id'              => 'nullable|integer',
            'lc_gate'                 => 'nullable|string|max:100',
            'running_room_id'         => 'nullable|integer',
            'diesel_lobby_id'         => 'nullable|integer',
            'train_no'                => 'nullable|string|max:100',
            'staff'                   => 'required|array|min:1',
            'staff.*.staff_id'        => 'required|integer',
            'staff.*.designation_id'  => 'required|integer',
            'counseling_type'         => 'required|in:Individual,Group',
            'subject_of_counseling'   => 'required|string',
        ]);

        if ($request->inspection_id != $data->inspection_id) {

            $newParent = InspectionMovement::find($request->inspection_id);

            if (!$newParent || $newParent->employee_id != $this->getAuthEmployeeId()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized. Target inspection does not belong to you.'
                ], 403);
            }
        }

        $data->update([
            'inspection_id' => $request->inspection_id,
            'station_id' => $request->station_id,
            'lc_gate' => $request->lc_gate,
            'running_room_id' => $request->running_room_id,
            'diesel_lobby_id' => $request->diesel_lobby_id,
            'train_no' => $request->train_no,
            'staff' => $request->staff,
            'counseling_type' => $request->counseling_type,
            'subject_of_counseling' => $request->subject_of_counseling,
        ]);
        // $data->update($request->all());

        return response()->json([
            'status' => true,
            'message' => 'Staff Counseling Updated Successfully',
            'data' => $data
        ]);
    }

    public function staffCounselingDestroy($id)
    {
        $data = StaffCounseling::with('inspection')->find($id);

        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found'
            ], 404);
        }

        if (!$data->inspection || $data->inspection->employee_id != $this->getAuthEmployeeId()) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized. This record does not belong to you.'
            ], 403);
        }

        $data->delete();

        return response()->json([
            'status' => true,
            'message' => 'Staff Counseling Deleted Successfully'
        ]);
    }



// ------------------- TI Movements Function -----------------------------


// ========================== TRAFFIC INSPECTOR ==================================

    /**
     * Store a new inspection
     */
    public function storeTi(Request $request)
    {
        $user = Auth::user();

        // ✅ Login check
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        // ✅ Master Employee check
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Only Master Employee can submit Inspection Form'
            ], 403);
        }

        // ✅ Validate request data
        $validated = $request->validate([
            'date_of_inspection' => 'required|date',
            'designation' => 'nullable|string',
            'movement' => 'nullable|string',
            'type_of_inspection' => 'nullable|string',
            'day_night' => 'nullable|string',
            'type_of_ambush_check' => 'nullable|string',
            'station_yard' => 'nullable|string',
            'level_crossing_no' => 'nullable|string',
            'train_number' => 'nullable|string',
            'section' => 'nullable|string',
            'line_number_load_stable' => 'nullable|string',
            'type_of_load_load_stable' => 'nullable|string',
            't806_number' => 'nullable|string',
            'shunting_observation_of' => 'nullable|string',
            'major_deficiencies' => 'nullable|string',
            'action_taken' => 'nullable|string',
            'remark' => 'nullable|string',
        ]);

        // ✅ Save Data with employee_id
        $inspection = Inspection::create(array_merge($validated, [
            'created_by' => $employee->id,
            'employee_id' => $employee->id, // Store employee_id
        ]));

        return response()->json([
            'status' => true,
            'message' => 'Inspection saved successfully',
            'data' => $inspection
        ]);
    }

    /**
     * Get all inspections (with filtering)
     */
    public function indexTi(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Only Master Employee can view inspections'
            ], 403);
        }

        // Only show inspections created by this employee
        $inspections = Inspection::where('employee_id', $employee->id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Inspection list fetched successfully',
            'data' => $inspections
        ]);
    }

    /**
     * Get single inspection
     */
    public function showTi($id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Only Master Employee can view inspection'
            ], 403);
        }

        $inspection = Inspection::where('employee_id', $employee->id)
            ->find($id);

        if (!$inspection) {
            return response()->json([
                'status' => false,
                'message' => 'Inspection not found or you don\'t have permission to view it'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $inspection
        ]);
    }

    /**
     * Update inspection
     */
    public function updateTi(Request $request, $id)
    {
        $user = Auth::user();

        // ✅ Login check
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        // ✅ Master Employee check
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Only Master Employee can update Inspection Form'
            ], 403);
        }

        // ✅ Find Inspection that belongs to this employee
        $inspection = Inspection::where('employee_id', $employee->id)
            ->find($id);

        if (!$inspection) {
            return response()->json([
                'status' => false,
                'message' => 'Inspection not found or you don\'t have permission to update it'
            ], 404);
        }

        // ✅ Validate request data
        $validated = $request->validate([
            'date_of_inspection' => 'sometimes|date',
            'designation' => 'nullable|string',
            'movement' => 'nullable|string',
            'type_of_inspection' => 'nullable|string',
            'day_night' => 'nullable|string',
            'type_of_ambush_check' => 'nullable|string',
            'station_yard' => 'nullable|string',
            'level_crossing_no' => 'nullable|string',
            'train_number' => 'nullable|string',
            'section' => 'nullable|string',
            'line_number_load_stable' => 'nullable|string',
            'type_of_load_load_stable' => 'nullable|string',
            't806_number' => 'nullable|string',
            'shunting_observation_of' => 'nullable|string',
            'major_deficiencies' => 'nullable|string',
            'action_taken' => 'nullable|string',
            'remark' => 'nullable|string',
        ]);

        // ✅ Update Data
        $inspection->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Inspection updated successfully',
            'data' => $inspection
        ]);
    }

    /**
     * Delete inspection
     */
    public function destroyTi($id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Only Master Employee can delete inspection'
            ], 403);
        }

        // Find inspection that belongs to this employee
        $inspection = Inspection::where('employee_id', $employee->id)
            ->find($id);

        if (!$inspection) {
            return response()->json([
                'status' => false,
                'message' => 'Inspection not found or you don\'t have permission to delete it'
            ], 404);
        }

        $inspection->delete();

        return response()->json([
            'status' => true,
            'message' => 'Inspection deleted successfully'
        ]);
    }



}