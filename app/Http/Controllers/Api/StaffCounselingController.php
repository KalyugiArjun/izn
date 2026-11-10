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
use App\Models\Designation;
use App\Models\Station;

use App\Models\SafetyDrive;
use App\Models\TypeOfLeave;
use App\Models\AttendBlock;
use App\Models\TypeOfAmbush;

use App\Models\MasterEmployee;
use App\Models\Inspection;
use Illuminate\Support\Facades\Auth;






class StaffCounselingController extends Controller
{

    private function getAuthEmployeeId()
    {
        $user = Auth::user();

        // Find employee by mobile number
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        return $employee?->id;
    }

    // searchable list 
    // public function staffCounselingIndex(Request $request): JsonResponse
    // {
    //     $authEmployeeId = $this->getAuthEmployeeId();

    //     if (!$authEmployeeId) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Employee not found for authenticated user.',
    //             'data' => [],
    //         ], 403);
    //     }

    //     $search = trim($request->get('search', ''));

    //     $query = StaffCounseling::query()
    //         ->where('employee_id', $authEmployeeId);

    //     if (!empty($search)) {
    //         $query->where(function ($q) use ($search) {
    //             $q->where('employee_id', 'like', "%{$search}%")
    //                 ->orWhere('counselor_name', 'like', "%{$search}%")
    //                 ->orWhere('counselor_designation', 'like', "%{$search}%")
    //                 ->orWhere('counseling_mode', 'like', "%{$search}%")
    //                 ->orWhere('counseling_type', 'like', "%{$search}%")
    //                 ->orWhere('subject_of_counseling', 'like', "%{$search}%")
    //                 ->orWhere('train_no', 'like', "%{$search}%")
    //                 ->orWhere('lc_gate', 'like', "%{$search}%")
    //                 ->orWhere('counseling_location', 'like', "%{$search}%")
    //                 ->orWhere('gps_location', 'like', "%{$search}%");
    //         });
    //     }

    //     $data = $query
    //         ->orderBy('id', 'desc')
    //         ->get();

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Staff Counseling List',
    //         'total' => $data->count(),
    //         'data' => $data,
    //     ], 200);
    // }

public function staffCounselingIndex(Request $request): JsonResponse
{
    $authEmployeeId = $this->getAuthEmployeeId();

    if (!$authEmployeeId) {
        return response()->json([
            'status' => false,
            'message' => 'Employee not found for authenticated user.',
            'data' => [],
        ], 403);
    }

    $search = trim($request->get('search', ''));

    $query = StaffCounseling::query()
        ->where('employee_id', $authEmployeeId);

    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->where('employee_id', 'like', "%{$search}%")
                ->orWhere('counselor_name', 'like', "%{$search}%")
                ->orWhere('counselor_designation', 'like', "%{$search}%")
                ->orWhere('counseling_mode', 'like', "%{$search}%")
                ->orWhere('counseling_type', 'like', "%{$search}%")
                ->orWhere('subject_of_counseling', 'like', "%{$search}%")
                ->orWhere('train_no', 'like', "%{$search}%")
                ->orWhere('lc_gate', 'like', "%{$search}%")
                ->orWhere('counseling_location', 'like', "%{$search}%")
                ->orWhere('gps_location', 'like', "%{$search}%");
        });
    }

   

    $data = $query
        ->with(['dieselLobby', 'counselorDesignation', 'station'])
        ->orderBy('id', 'desc')
        ->get();

    // ✅ Enrich staff array with names
    $data->transform(function ($item) {
    // Diesel Lobby name
    $item->diesel_lobby_name = optional($item->dieselLobby)->name;

    $item->counselor_designation_name = optional($item->counselorDesignation)->name;

    // Optional — other relations
    // $item->running_room_name = optional($item->runningRoom)->name;
    $item->station_name      = optional($item->station)->name;
    // $item->inspection_name   = optional($item->inspection)->name;

    // Staff enrichment (previous logic)
    if (!empty($item->staff) && is_array($item->staff)) {
        $item->staff = array_map(function ($s) {
            return [
                'staff_id'         => $s['staff_id'] ?? null,
                'staff_name'       => MasterEmployee::find($s['staff_id'] ?? null)?->name,
                'designation_id'   => $s['designation_id'] ?? null,
                'designation_name' => Designation::find($s['designation_id'] ?? null)?->name,
                'station_id'       => $s['station_id'] ?? null,
                'station_name'     => Station::find($s['station_id'] ?? null)?->name,
            ];
        }, $item->staff);
    }

    // Hide the raw relation objects if you don't want them in JSON
    $item->makeHidden(['dieselLobby', 'counselorDesignation', 'station']);

    return $item;
});

    return response()->json([
        'status'  => true,
        'message' => 'Staff Counseling List',
        'total'   => $data->count(),
        'data'    => $data,
    ], 200);
}





    // public function staffCounselingStore(Request $request)
    // {
    //     $authEmployeeId = $this->getAuthEmployeeId();

    //     if (!$authEmployeeId) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Employee not found for authenticated user.',
    //         ], 403);
    //     }

    //     $request->validate([
    //         'counseling_date'         => 'required|string',
    //         'counseling_time'         => 'required|string',
    //         'gps_location'            => 'required|string',
    //         'counselor_name'          => 'required|string',
    //         'counselor_designation'   => 'required|string',
    //         'counseling_mode'         => 'required|string',
    //         'counseling_location'     => 'nullable|string',

    //         'inspection_id'           => 'nullable|integer',
    //         'station_id'              => 'nullable|integer',
    //         'lc_gate'                 => 'nullable|string|max:100',
    //         'running_room_id'         => 'nullable|integer',
    //         'diesel_lobby_id'         => 'nullable|integer',
    //         'train_no'                => 'nullable|string|max:100',

    //         'staff'                   => 'required|array|min:1',
    //         'staff.*.staff_id'        => 'required|integer',
    //         'staff.*.designation_id'  => 'required|integer',

    //         'counseling_type'         => 'required|in:Individual,Group',
    //         'subject_of_counseling'   => 'required|string',
    //     ]);

    //     $data = StaffCounseling::create([
    //         // Authenticated user's employee ID
    //         'employee_id' => $authEmployeeId,

    //         'counseling_date' => $request->counseling_date,
    //         'counseling_time' => $request->counseling_time,
    //         'gps_location' => $request->gps_location,
    //         'counselor_name' => $request->counselor_name,
    //         'counselor_designation' => $request->counselor_designation,
    //         'counseling_mode' => $request->counseling_mode,
    //         'counseling_location' => $request->counseling_location,

    //         'inspection_id' => $request->inspection_id,
    //         'station_id' => $request->station_id,
    //         'lc_gate' => $request->lc_gate,
    //         'running_room_id' => $request->running_room_id,
    //         'diesel_lobby_id' => $request->diesel_lobby_id,
    //         'train_no' => $request->train_no,

    //         'staff' => $request->staff,

    //         'counseling_type' => $request->counseling_type,
    //         'subject_of_counseling' => $request->subject_of_counseling,
    //     ]);

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Staff Counseling Created Successfully',
    //         'data' => $data,
    //     ], 201);
    // }

    public function staffCounselingStore(Request $request)
{
    $authEmployeeId = $this->getAuthEmployeeId();

    if (!$authEmployeeId) {
        return response()->json([
            'status' => false,
            'message' => 'Employee not found for authenticated user.',
        ], 403);
    }

    $request->validate([
        'counseling_date'         => 'required|string',
        'counseling_time'         => 'required|string',
        'gps_location'            => 'required|string',
        'latitude'                => 'nullable|numeric',
        'longitude'               => 'nullable|numeric',
        'counselor_name'          => 'required|string',
        'counselor_designation'   => 'required|string',
        'counseling_mode'         => 'required|in:online,offline',

        // ── NEW: Calling mode (only for online) ──
        'calling_mode'            => 'nullable|string|max:255',

        'counseling_location'     => 'nullable|string',

        'inspection_id'           => 'nullable|integer',
        'station_id'              => 'nullable|integer',
        'lc_gate'                 => 'nullable|string|max:100',
        'running_room_id'         => 'nullable|integer',
        'diesel_lobby_id'         => 'nullable|integer',
        'train_no'                => 'nullable|string|max:100',

        // ── NEW: Photo (single image) ──
        'image'                   => 'nullable|image|mimes:jpeg,jpg,png|max:5120',

        'staff'                   => 'required|array|min:1',
        'staff.*.staff_id'        => 'required|integer',
        'staff.*.designation_id'  => 'required|integer',
        'staff.*.station_id'      => 'nullable|integer',

        'counseling_type'         => 'required|in:Individual,Group',
        'subject_of_counseling'   => 'required|string',
    ]);

    // ── Handle Photo Upload ──
    $imagePath = null;
    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $filename = 'staff_counseling_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $imagePath = $file->storeAs('staff_counseling', $filename, 'public');
    }

    $data = StaffCounseling::create([
        'employee_id' => $authEmployeeId,

        'counseling_date' => $request->counseling_date,
        'counseling_time' => $request->counseling_time,
        'gps_location' => $request->gps_location,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        'counselor_name' => $request->counselor_name,
        'counselor_designation' => $request->counselor_designation,
        'counseling_mode' => $request->counseling_mode,

        // ── NEW ──
        'calling_mode' => $request->calling_mode,
        'image' => $imagePath,

        'counseling_location' => $request->counseling_location,
        'inspection_id' => $request->inspection_id,
        'station_id' => $request->station_id,
        'lc_gate' => $request->lc_gate,
        'running_room_id' => $request->running_room_id,
        'diesel_lobby_id' => $request->diesel_lobby_id,
        'train_no' => $request->train_no,

        'staff' => $request->staff, // JSON array

        'counseling_type' => $request->counseling_type,
        'subject_of_counseling' => $request->subject_of_counseling,
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Staff Counseling Created Successfully',
        'data' => $data,
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
        $authEmployeeId = $this->getAuthEmployeeId();

        if (!$authEmployeeId) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found for authenticated user.',
            ], 403);
        }

        $data = StaffCounseling::where('id', $id)
            ->where('employee_id', $authEmployeeId)
            ->first();

        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized or record not found.',
            ], 403);
        }

        $request->validate([
            'counseling_date'         => 'required|string',
            'counseling_time'         => 'required|string',
            'gps_location'            => 'required|string',
            'counselor_name'          => 'required|string',
            'counselor_designation'   => 'required|string',
            'counseling_mode'         => 'required|string',
            'counseling_location'     => 'nullable|string',

            'inspection_id'           => 'nullable|integer',
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

        $data->update([
            'counseling_date' => $request->counseling_date,
            'counseling_time' => $request->counseling_time,
            'gps_location' => $request->gps_location,
            'counselor_name' => $request->counselor_name,
            'counselor_designation' => $request->counselor_designation,
            'counseling_mode' => $request->counseling_mode,
            'counseling_location' => $request->counseling_location,

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

        return response()->json([
            'status' => true,
            'message' => 'Staff Counseling Updated Successfully',
            'data' => $data,
        ]);
    }

    public function staffCounselingDestroy($id)
    {
        $authEmployeeId = $this->getAuthEmployeeId();

        if (!$authEmployeeId) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found for authenticated user.',
            ], 403);
        }

        $data = StaffCounseling::where('id', $id)
            ->where('employee_id', $authEmployeeId)
            ->first();

        if (!$data) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized or record not found.',
            ], 403);
        }

        $data->delete();

        return response()->json([
            'status' => true,
            'message' => 'Staff Counseling Deleted Successfully',
        ]);
    }

}