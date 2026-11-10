<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoadStabling;
use App\Models\MasterEmployee;
use App\Models\LoadStablingSmChecklist;
use App\Models\LoadStablingClearance;
use App\Models\LoadStablingFinalSubmission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * LoadStablingController
 * ─────────────────────────────────────────────────────────────────
 * Handles the full 4-stage Load Stabling flow for Indian Railways.
 *
 * Stage 1  →  store()              Points-man entry
 * Stage 2a →  smChecklist()        Station Master checklist
 * Stage 2b →  forwardClearance()   SM forwards to Points-man
 * Stage 3  →  storeClearance()     Points-man clearance entry
 * Stage 4  →  finalSubmit()        SM confirms dispatch
 * Reads    →  show(), assignedToSm(), assignedClearance()
 * ─────────────────────────────────────────────────────────────────
 */
class LoadStablingController extends Controller
{
    // Designation IDs as per your system
    const DESIGNATION_STATION_MASTER = 2;
    const DESIGNATION_STATION_MANAGER = 4;
    const DESIGNATION_POINTSMAN = 63;

    // ---------------------------------------------------------------
    // HELPERS
    // ---------------------------------------------------------------

    /** Get employee details by user */
    private function getEmployee($user): ?MasterEmployee
    {
        if (!$user) {
            return null;
        }

        // Try multiple ways to find the employee
        $employee = null;

        // Method 1: Check if user has employee_id or id field
        if (isset($user->employee_id)) {
            $employee = MasterEmployee::find($user->employee_id);
        }

        // Method 2: Try by mobile number
        if (!$employee && isset($user->phone_number)) {
            $employee = MasterEmployee::where('mobile', $user->phone_number)->first();
        }

        // Method 3: Try by email
        if (!$employee && isset($user->email)) {
            $employee = MasterEmployee::where('email', $user->email)->first();
        }

        // Method 4: Try by usmid if available
        if (!$employee && isset($user->usmid)) {
            $employee = MasterEmployee::where('usmid', $user->usmid)->first();
        }

        // Method 5: If user object has an id field and no match found, try by user id (assuming it matches employee id)
        if (!$employee && isset($user->id)) {
            $employee = MasterEmployee::find($user->id);
        }

        return $employee;
    }

    /** Get user role based on designation */
    private function getUserRole($user): ?string
    {
        $employee = $this->getEmployee($user);

        if (!$employee) {
            return null;
        }

        // Check designation_id
        if ($employee->designation_id == self::DESIGNATION_STATION_MASTER) {
            return 'STATION_MASTER';
        } elseif ($employee->designation_id == self::DESIGNATION_STATION_MANAGER) {
            return 'STATION_MANAGER';
        } elseif ($employee->designation_id == self::DESIGNATION_POINTSMAN) {
            return 'POINTSMAN';
        }

        return 'OTHER';
    }

    /** Store MULTIPLE photos (multiphoto field) and return array of relative paths */
    private function storeMultiPhotos(Request $request, string $fieldName, string $subfolder = 'photos'): array
    {
        if (!$request->hasFile($fieldName)) {
            return [];
        }
        $files = $request->file($fieldName);
        $files = is_array($files) ? $files : [$files];

        $paths = [];
        foreach ($files as $file) {
            if ($file && $file->isValid()) {
                $paths[] = $file->store("load_stabling/{$subfolder}", 'public');
            }
        }
        return $paths;
    }

    /** New files + existing string paths dono ko merge karke final paths array return karo */
    private function mergeMultiPhotos(Request $request, string $fieldName, string $subfolder = 'photos'): array
    {
        $paths = [];

        // 1. Existing (already-uploaded) paths — string ya JSON-string form mein aa sakta hai
        $existing = $request->input($fieldName, []);
        if (is_string($existing)) {
            $decoded = json_decode($existing, true);
            $existing = is_array($decoded) ? $decoded : ($existing !== '' ? [$existing] : []);
        }
        foreach ((array) $existing as $item) {
            if (is_string($item) && $item !== '') {
                $paths[] = $item;
            }
        }

        // 2. Naye uploaded files
        if ($request->hasFile($fieldName)) {
            $files = $request->file($fieldName);
            $files = is_array($files) ? $files : [$files];
            foreach ($files as $file) {
                if ($file && $file->isValid()) {
                    $paths[] = $file->store("load_stabling/{$subfolder}", 'public');
                }
            }
        }

        return array_values(array_unique($paths));
    }

    /** Raw file objects ({uri,name,type}) ya unke arrays ko safety_check_data se hata do —
    *  yeh photos hamesha dedicated storePhoto()/mergeMultiPhotos() se handle hote hain */
    private function sanitizeSafetyData(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value) && isset($value['uri']) && isset($value['type'])) {
                unset($data[$key]);
                continue;
            }
            if (is_array($value) && isset($value[0]) && is_array($value[0]) && isset($value[0]['uri'])) {
                unset($data[$key]);
            }
        }
        return $data;
    }

    /** Check if user is authorized as Station Master or Station Manager */
    private function isStationAuthority($user): bool
    {
        $role = $this->getUserRole($user);
        return in_array($role, ['STATION_MASTER', 'STATION_MANAGER']);
    }

    /** Store a single uploaded photo to storage/app/public/load_stabling/
     *  and return the relative path string (or null if no file). */
    private function storePhoto(Request $request, string $fieldName, string $subfolder = 'photos'): ?string
    {
        if (!$request->hasFile($fieldName)) {
            return null;
        }
        $file = $request->file($fieldName);
        return $file->store("load_stabling/{$subfolder}", 'public');
    }

    /** Extract all dynamic safety/clearance values from the request. */
    private function extractDynamicData(Request $request, array $photoKeys = []): array
    {
        $allInput = $request->except(array_merge($photoKeys, [
            'pointsman_id', 'station', 'date_time', 'gps_location',
            'train_number', 'arrival_time', 'fouling_mark',
            'load_type', 'load_sub_type', 'stabling_line', 'total_load',
            'all_vehicles_coupled', 'load_completely_stabled',
            'load_stabling_id', 'release_type', 'total_loads_cleared', 'release_time',
        ]));

        return $allInput;
    }

    /** Return a JSON success response */
    private function success(string $message, array $data = [], int $status = 200): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $status);
    }

    /** Return a JSON error response */
    private function error(string $message, array $errors = [], int $status = 422): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message, 'errors' => $errors], $status);
    }



    // private function extractSafetyData(Request $request): array
    // {
    //     $exclude = [
    //         'pointsman_id',
    //         'pointsman_name',
    //         'assign_to_sm',
    //         'station',
    //         'date_time',
    //         'gps_location',
    //         'train_number',
    //         'arrival_time',
    //         'fouling_mark',
    //         'load_type',
    //         'load_sub_type',
    //         'stabling_line',
    //         'total_load',
    //         'all_vehicles_coupled',
    //         'load_completely_stabled',
    //         'photo_wedges',
    //         'photo_chains',
    //         '_method',
    //     ];

    //     return $request->except($exclude);
    // }


    // private function extractSafetyData(Request $request): array
    // {
    //     // Agar frontend safety_check_data object bhej raha hai
    //     if ($request->filled('safety_check_data')) {

    //         $data = $request->input('safety_check_data');

    //         if (is_string($data)) {
    //             $decoded = json_decode($data, true);

    //             if (json_last_error() === JSON_ERROR_NONE) {
    //                 return $decoded;
    //             }
    //         }

    //         if (is_array($data)) {
    //             return $data;
    //         }
    //     }

    //     // Old style fields
    //     $exclude = [
    //         'pointsman_id',
    //         'assign_to_sm',
    //         'station',
    //         'date_time',
    //         'gps_location',
    //         'train_number',
    //         'arrival_time',
    //         'fouling_mark',
    //         'load_type',
    //         'load_sub_type',
    //         'stabling_line',
    //         'total_load',
    //         'all_vehicles_coupled',
    //         'load_completely_stabled',
    //         'photo_wedges',
    //         'photo_chains',
    //         '_method',
    //         'safety_check_data',
    //     ];

    //     return $request->except($exclude);
    // }


    private function extractSafetyData(Request $request): array
{
    if ($request->filled('safety_check_data')) {
        $data = $request->input('safety_check_data');
        if (is_string($data)) {
            $decoded = json_decode($data, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }
        if (is_array($data)) {
            return $data;
        }
    }

    $exclude = [
        'pointsman_id',
        'assign_to_sm',
        'station',
        'date_time',
        'gps_location',
        'train_number',
        'arrival_time',
        'fouling_mark',
        'load_type',
        'load_sub_type',
        'stabling_line',
        'total_load',
        'all_vehicles_coupled',
        'load_completely_stabled',
        'photo_wedges',
        'photo_chains',
        'photo_uncoupled_vehicles', // ✅ NEW — multiphoto, alag se handle hota hai
        'running_line',             // ✅ NEW — dedicated column
        'loco_number',              // ✅ NEW — dedicated column
        'type_of_machine',          // ✅ NEW — dedicated column
        '_method',
        'safety_check_data',
    ];

    return $request->except($exclude);
}

    // ---------------------------------------------------------------
    // STAGE 1 — Points-man Load Stabling Entry
    // POST /api/load-stabling/store
    // ---------------------------------------------------------------
    


    // public function store(Request $request): JsonResponse
    // {
    //     $user = $request->user();
    //     $employee = $this->getEmployee($user);

    //     if (!$employee) {
    //         return $this->error('Employee record not found. Please contact admin.', [], 403);
    //     }

    //     if ($employee->designation_id != self::DESIGNATION_POINTSMAN) {
    //         return $this->error('Only Pointsmen can create load stabling entries.', [], 403);
    //     }

    //     $assignToSmId = $request->input('assign_to_sm');
    //     $stationAuthority = MasterEmployee::find($assignToSmId);

    //     if (!$stationAuthority) {
    //         return $this->error('Station Master/Manager not found. Please select a valid Station Master or Manager.', [], 422);
    //     }

    //     if (!in_array($stationAuthority->designation_id, [self::DESIGNATION_STATION_MASTER, self::DESIGNATION_STATION_MANAGER])) {
    //         return $this->error('The selected employee must be either a Station Master or Station Manager.', [], 422);
    //     }

    //     $validator = Validator::make($request->all(), [
    //         'pointsman_name'    => 'required|string|max:150',
    //         'assign_to_sm'      => 'required|exists:master_employees,id',
    //         'station'           => 'required|string|max:100',
    //         'stable_station'    => 'required|string|max:100',
    //         'train_number'      => 'required|string|max:30',
    //         'load_type'         => 'required|string|max:50',
    //         'stabling_line'     => 'required|string|max:20',
    //         'total_load'        => 'nullable|string|max:100',
    //         'running_line'      => 'nullable|string|max:50',
    //         'loco_number'       => 'nullable|string|max:50',
    //         'type_of_machine'   => 'nullable|string|max:50',
    //         'photo_wedges'      => 'nullable|file|image|max:5120',
    //         'photo_chains'      => 'nullable|file|image|max:5120',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->error('Validation failed', $validator->errors()->toArray());
    //     }


    //     DB::beginTransaction();
    //     try {
    //         $photoWedgesPath = $this->storePhoto($request, 'photo_wedges');
    //         $photoChainsPath = $this->storePhoto($request, 'photo_chains');
    //         $uncoupledPhotos = $this->mergeMultiPhotos($request, 'photo_uncoupled_vehicles');

    //         $safetyData = $this->extractSafetyData($request);
    //         if (!is_array($safetyData)) $safetyData = [];
    //         $safetyData = $this->sanitizeSafetyData($safetyData);          // ✅ junk hata do
    //         $safetyData['photo_uncoupled_vehicles'] = $uncoupledPhotos;    // ✅ real paths daalo

    //         $record = LoadStabling::create([
    //             'pointsman_id'            => $employee->id,
    //             'assign_to_sm'            => $assignToSmId,
    //             'pointsman_name'          => $request->input('pointsman_name'),
    //             'station'                 => $request->input('station'),
    //             'stable_station'          => $request->input('stable_station'),
    //             'date_time'               => $request->input('date_time'),
    //             'gps_location'            => $request->input('gps_location'),
    //             'train_number'            => $request->input('train_number'),
    //             'arrival_time'            => $request->input('arrival_time'),
    //             'fouling_mark'            => $request->input('fouling_mark'),
    //             'load_type'               => $request->input('load_type'),
    //             'load_sub_type'           => $request->input('load_sub_type'),
    //             'stabling_line'           => $request->input('stabling_line'),
    //             'running_line'            => $request->input('running_line'),
    //             'loco_number'             => $request->input('loco_number'),
    //             'type_of_machine'         => $request->input('type_of_machine'),
    //             'total_load'              => $request->input('total_load'),
    //             'all_vehicles_coupled'    => $request->input('all_vehicles_coupled'),
    //             'load_completely_stabled' => $request->input('load_completely_stabled'),
    //             'safety_check_data'       => $safetyData,
    //             'photo_wedges'            => $photoWedgesPath,
    //             'photo_chains'            => $photoChainsPath,
    //             'status'                  => 'PENDING_SM',
    //         ]);

    //         DB::commit();
    //         return $this->success('Load Stabling entry submitted to Station Master/Manager.', [
    //             'id' => $record->id, 'status' => $record->status,
    //         ], 201);

    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         return $this->error('Server error: ' . $e->getMessage(), [], 500);
    //     }
    // }

    // 21-9-2026
    // public function store(Request $request): JsonResponse
    // {
    //     $user = $request->user();
    //     $employee = $this->getEmployee($user);

    //     if (!$employee) {
    //         return $this->error(
    //             'Employee record not found. Please contact admin.',
    //             [],
    //             403
    //         );
    //     }

    //     if ($employee->designation_id != self::DESIGNATION_POINTSMAN) {
    //         return $this->error(
    //             'Only Pointsmen can create load stabling entries.',
    //             [],
    //             403
    //         );
    //     }

    //     $assignToSmId = $request->input('assign_to_sm');

    //     $stationAuthority = MasterEmployee::find($assignToSmId);

    //     if (!$stationAuthority) {
    //         return $this->error(
    //             'Station Master/Manager not found. Please select a valid Station Master or Manager.',
    //             [],
    //             422
    //         );
    //     }

    //     if (!in_array(
    //         $stationAuthority->designation_id,
    //         [
    //             self::DESIGNATION_STATION_MASTER,
    //             self::DESIGNATION_STATION_MANAGER
    //         ]
    //     )) {
    //         return $this->error(
    //             'The selected employee must be either a Station Master or Station Manager.',
    //             [],
    //             422
    //         );
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Validation
    //     |--------------------------------------------------------------------------
    //     */

    //     $validator = Validator::make($request->all(), [
    //         'pointsman_name'    => 'required|string|max:150',
    //         'assign_to_sm'      => 'required|exists:master_employees,id',
    //         'station'           => 'required|string|max:100',
    //         'stable_station'    => 'required|string|max:100',
    //         'train_number'      => 'required|string|max:30',

    //         'load_type'         => 'required|string|max:50',
    //         'load_sub_type'     => 'nullable|string|max:50',

    //         'stabling_line'     => 'required|string|max:20',
    //         'running_line'      => 'nullable|string|max:50',
    //         'loco_number'       => 'nullable|string|max:50',

    //         // IMPORTANT
    //         'type_of_machine'   => 'nullable|array',
    //         'type_of_machine.*' => 'string|max:100',

    //         'total_load'        => 'nullable|string|max:100',

    //         'all_vehicles_coupled'    => 'nullable|in:YES,NO',
    //         'load_completely_stabled' => 'nullable|in:YES,NO',

    //         'photo_wedges'      => 'nullable|file|image|max:5120',
    //         'photo_chains'      => 'nullable|file|image|max:5120',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->error(
    //             'Validation failed',
    //             $validator->errors()->toArray()
    //         );
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Type Of Machine
    //     |--------------------------------------------------------------------------
    //     */

    //     $typeOfMachine = $request->input('type_of_machine');

    //     // Backward compatibility:
    //     // agar string JSON ke form mein aaye
    //     if (is_string($typeOfMachine)) {

    //         $decoded = json_decode($typeOfMachine, true);

    //         if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
    //             $typeOfMachine = $decoded;
    //         } else {
    //             $typeOfMachine = [$typeOfMachine];
    //         }
    //     }

    //     if (!is_array($typeOfMachine)) {
    //         $typeOfMachine = null;
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Transaction
    //     |--------------------------------------------------------------------------
    //     */

    //     DB::beginTransaction();

    //     try {

    //         $photoWedgesPath = $this->storePhoto(
    //             $request,
    //             'photo_wedges'
    //         );

    //         $photoChainsPath = $this->storePhoto(
    //             $request,
    //             'photo_chains'
    //         );

    //         $uncoupledPhotos = $this->mergeMultiPhotos(
    //             $request,
    //             'photo_uncoupled_vehicles'
    //         );

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Safety Data
    //         |--------------------------------------------------------------------------
    //         */

    //         $safetyData = $this->extractSafetyData($request);

    //         if (!is_array($safetyData)) {
    //             $safetyData = [];
    //         }

    //         $safetyData = $this->sanitizeSafetyData($safetyData);

    //         $safetyData['photo_uncoupled_vehicles'] = $uncoupledPhotos;

    //         /*
    //         |--------------------------------------------------------------------------
    //         | Create
    //         |--------------------------------------------------------------------------
    //         */

    //         $record = LoadStabling::create([
    //             'pointsman_id'            => $employee->id,
    //             'assign_to_sm'            => $assignToSmId,

    //             'pointsman_name'          => $request->input('pointsman_name'),
    //             'station'                => $request->input('station'),
    //             'stable_station'         => $request->input('stable_station'),

    //             'date_time'              => $request->input('date_time'),
    //             'gps_location'           => $request->input('gps_location'),

    //             'train_number'           => $request->input('train_number'),
    //             'arrival_time'           => $request->input('arrival_time'),
    //             'fouling_mark'           => $request->input('fouling_mark'),

    //             'load_type'              => $request->input('load_type'),
    //             'load_sub_type'          => $request->input('load_sub_type'),

    //             'stabling_line'          => $request->input('stabling_line'),
    //             'running_line'           => $request->input('running_line'),

    //             'loco_number'            => $request->input('loco_number'),

    //             // IMPORTANT
    //             'type_of_machine'        => $typeOfMachine,

    //             'total_load'             => $request->input('total_load'),

    //             'all_vehicles_coupled'   => $request->input('all_vehicles_coupled'),

    //             'load_completely_stabled'
    //                 => $request->input('load_completely_stabled'),

    //             'safety_check_data'      => $safetyData,

    //             'photo_wedges'           => $photoWedgesPath,
    //             'photo_chains'           => $photoChainsPath,

    //             'status'                 => 'PENDING_SM',
    //         ]);

    //         DB::commit();

    //         return $this->success(
    //             'Load Stabling entry submitted to Station Master/Manager.',
    //             [
    //                 'id'     => $record->id,
    //                 'status' => $record->status,
    //             ],
    //             201
    //         );

    //     } catch (\Throwable $e) {

    //         DB::rollBack();

    //         return $this->error(
    //             'Server error: ' . $e->getMessage(),
    //             [],
    //             500
    //         );
    //     }
    // }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        $employee = $this->getEmployee($user);

        if (!$employee) {
            return $this->error(
                'Employee record not found. Please contact admin.',
                [],
                403
            );
        }

        if ($employee->designation_id != self::DESIGNATION_POINTSMAN) {
            return $this->error(
                'Only Pointsmen can create load stabling entries.',
                [],
                403
            );
        }

        $assignToSmId = $request->input('assign_to_sm');

        $stationAuthority = MasterEmployee::find($assignToSmId);

        if (!$stationAuthority) {
            return $this->error(
                'Station Master/Manager not found. Please select a valid Station Master or Manager.',
                [],
                422
            );
        }

        if (!in_array(
            $stationAuthority->designation_id,
            [
                self::DESIGNATION_STATION_MASTER,
                self::DESIGNATION_STATION_MANAGER
            ]
        )) {
            return $this->error(
                'The selected employee must be either a Station Master or Station Manager.',
                [],
                422
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validator = Validator::make($request->all(), [
            'pointsman_name'    => 'required|string|max:150',
            'assign_to_sm'      => 'required|exists:master_employees,id',
            'station'           => 'required|string|max:100',
            'stable_station'    => 'required|string|max:100',
            'train_number'      => 'required|string|max:30',

            'load_type'         => 'required|string|max:50',
            'load_sub_type'     => 'nullable|string|max:50',

            // ✅ NEW — Rake sub-types (Step 2 dropdowns)
            'coaching_sub_type' => 'nullable|string|max:100',
            'goods_sub_type'    => 'nullable|string|max:100',

            'stabling_line'     => 'required|string|max:20',
            'running_line'      => 'nullable|string|max:50',
            'loco_number'       => 'nullable|string|max:50',

            // IMPORTANT
            'type_of_machine'   => 'nullable|array',
            'type_of_machine.*' => 'string|max:100',

            'total_load'        => 'nullable|string|max:100',

            'all_vehicles_coupled'    => 'nullable|in:YES,NO',
            'load_completely_stabled' => 'nullable|in:YES,NO',

            // ✅ NEW — Train Manager Private Number
            'tm_private_number' => 'nullable|string|max:50',

            // ✅ Photos
            'photo_wedges'         => 'nullable|file|image|max:5120',
            'photo_chains'         => 'nullable|file|image|max:5120',
            'photo_wedges_placed'  => 'nullable|file|image|max:5120', // ✅ NEW
        ]);

        if ($validator->fails()) {
            return $this->error(
                'Validation failed',
                $validator->errors()->toArray()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Type Of Machine
        |--------------------------------------------------------------------------
        */

        $typeOfMachine = $request->input('type_of_machine');

        // Backward compatibility: agar string JSON ke form mein aaye
        if (is_string($typeOfMachine)) {
            $decoded = json_decode($typeOfMachine, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $typeOfMachine = $decoded;
            } else {
                $typeOfMachine = [$typeOfMachine];
            }
        }

        if (!is_array($typeOfMachine)) {
            $typeOfMachine = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Transaction
        |--------------------------------------------------------------------------
        */

        DB::beginTransaction();

        try {

            $photoWedgesPath = $this->storePhoto(
                $request,
                'photo_wedges'
            );

            $photoChainsPath = $this->storePhoto(
                $request,
                'photo_chains'
            );

            // ✅ NEW — Wooden wedges placed photo
            $photoWedgesPlacedPath = $this->storePhoto(
                $request,
                'photo_wedges_placed'
            );

            $uncoupledPhotos = $this->mergeMultiPhotos(
                $request,
                'photo_uncoupled_vehicles'
            );

            /*
            |--------------------------------------------------------------------------
            | Safety Data
            |--------------------------------------------------------------------------
            */

            $safetyData = $this->extractSafetyData($request);

            if (!is_array($safetyData)) {
                $safetyData = [];
            }

            $safetyData = $this->sanitizeSafetyData($safetyData);

            $safetyData['photo_uncoupled_vehicles'] = $uncoupledPhotos;

            // ✅ NEW — photo_wedges_placed bhi safety_check_data me store karo
            if ($photoWedgesPlacedPath) {
                $safetyData['photo_wedges_placed'] = $photoWedgesPlacedPath;
            }

            /*
            |--------------------------------------------------------------------------
            | Create
            |--------------------------------------------------------------------------
            */

            $record = LoadStabling::create([
                'pointsman_id'            => $employee->id,
                'assign_to_sm'            => $assignToSmId,

                'pointsman_name'          => $request->input('pointsman_name'),
                'station'                 => $request->input('station'),
                'stable_station'          => $request->input('stable_station'),

                'date_time'               => $request->input('date_time'),
                'gps_location'            => $request->input('gps_location'),

                'train_number'            => $request->input('train_number'),
                'arrival_time'            => $request->input('arrival_time'),
                'fouling_mark'            => $request->input('fouling_mark'),

                'load_type'               => $request->input('load_type'),
                'load_sub_type'           => $request->input('load_sub_type'),

                // ✅ NEW — Rake sub-types
                'coaching_sub_type'       => $request->input('coaching_sub_type'),
                'goods_sub_type'          => $request->input('goods_sub_type'),

                'stabling_line'           => $request->input('stabling_line'),
                'running_line'            => $request->input('running_line'),

                'loco_number'             => $request->input('loco_number'),

                // IMPORTANT
                'type_of_machine'         => $typeOfMachine,

                'total_load'              => $request->input('total_load'),

                'all_vehicles_coupled'    => $request->input('all_vehicles_coupled'),

                'load_completely_stabled' => $request->input('load_completely_stabled'),

                // ✅ NEW — Train Manager Private Number
                'tm_private_number'       => $request->input('tm_private_number'),

                'safety_check_data'       => $safetyData,

                'photo_wedges'            => $photoWedgesPath,
                'photo_chains'            => $photoChainsPath,

                'status'                  => 'PENDING_SM',
            ]);

            DB::commit();

            return $this->success(
                'Load Stabling entry submitted to Station Master/Manager.',
                [
                    'id'     => $record->id,
                    'status' => $record->status,
                ],
                201
            );

        } catch (\Throwable $e) {

            DB::rollBack();

            return $this->error(
                'Server error: ' . $e->getMessage(),
                [],
                500
            );
        }
    }

    // ---------------------------------------------------------------
    // STAGE 2a — Station Master/Manager Checklist
    // POST /api/load-stabling/{id}/sm-checklist
    // ---------------------------------------------------------------
    public function smChecklist(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $employee = $this->getEmployee($user);

        // Check if user is a Station Master or Station Manager
        if (!$employee || !$this->isStationAuthority($user)) {
            return $this->error('Only Station Masters or Station Managers can submit checklists.', [], 403);
        }

        $record = LoadStabling::find($id);
        if (!$record) {
            return $this->error('Load Stabling record not found.', [], 404);
        }

        // VALIDATION: The logged-in user must be the one assigned to this record
        if ($record->assign_to_sm != $employee->id) {
            return $this->error(
                'You are not authorized to verify this load stabling entry. ' .
                'This entry is assigned to Station Master/Manager ID: ' . $record->assign_to_sm,
                [],
                403
            );
        }

        $validator = Validator::make($request->all(), [
            'interlocking_type'    => 'required|in:RRI,PI,EI',
            'checklist_data'       => 'required|array',
            'sm_private_number'    => 'required|string|max:100',
            'tnl_private_number'   => 'required|string|max:100',
            'load_stable_datetime' => 'nullable|string|max:100',
            'reversal_taken_datetime'=> 'nullable|string|max:100',
            'total_load_cleared'     => 'nullable|string|max:250',
            'loco_reversal_received' => 'nullable|in:YES,NO',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray());
        }

        DB::beginTransaction();
        try {
            LoadStablingSmChecklist::updateOrCreate(
                ['load_stabling_id' => $id],
                [
                    'interlocking_type'      => $request->input('interlocking_type'),
                    'checklist_data'         => $request->input('checklist_data'),
                    'sm_private_number'      => $request->input('sm_private_number'),
                    'tnl_private_number'     => $request->input('tnl_private_number'),
                    'load_stable_datetime'   => $request->input('load_stable_datetime'),
                    'reversal_taken_datetime' => $request->input('reversal_taken_datetime'),
                    'total_load_cleared'     => $request->input('total_load_cleared'),
                    'loco_reversal_received' => $request->input('loco_reversal_received'),
                ]
            );

            $record->update(['status' => 'SM_VERIFIED']);

            DB::commit();
            return $this->success('SM checklist saved.', ['status' => 'SM_VERIFIED']);

        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error('Server error: ' . $e->getMessage(), [], 500);
        }
    }

    // ---------------------------------------------------------------
    // STAGE 2b — Station Master/Manager Forwards to Load Clearance
    // POST /api/load-stabling/{id}/forward-clearance
    // ---------------------------------------------------------------
    public function forwardClearance(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $employee = $this->getEmployee($user);

        // Check if user is a Station Master or Station Manager
        if (!$employee || !$this->isStationAuthority($user)) {
            return $this->error('Only Station Masters or Station Managers can forward for clearance.', [], 403);
        }

        $record = LoadStabling::find($id);
        if (!$record) {
            return $this->error('Record not found.', [], 404);
        }

        // VALIDATION: The logged-in user must be the one assigned to this record
        if ($record->assign_to_sm != $employee->id) {
            return $this->error(
                'You are not authorized to forward this entry. ' .
                'This entry is assigned to Station Master/Manager ID: ' . $record->assign_to_sm,
                [],
                403
            );
        }

        if ($record->status !== 'SM_VERIFIED') {
            return $this->error('SM checklist must be submitted before forwarding.', [], 409);
        }

        // $validator = Validator::make($request->all(), [
        //     'engine_reversal_handed_over' => 'required|in:YES,NO',
        //     'release_type'                => 'required|in:FULL,PARTIAL',
        // ]);


        // new validation to ensure assign_to_pointsman is provided and exists in master_employees table
        $validator = Validator::make($request->all(), [
            'total_load_cleared'          => 'nullable|string|max:250',
            'engine_reversal_handed_over' => 'nullable|in:YES,NO',
            'release_type'                => 'required|in:FULL,PARTIAL',
            'assign_to_pointsman'         => 'required|exists:master_employees,id',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray());
        }

        // Check selected employee is actually a Pointsman
        $pointsman = MasterEmployee::find($request->assign_to_pointsman);

        if (!$pointsman || $pointsman->designation_id != self::DESIGNATION_POINTSMAN) {
            return $this->error(
                'Selected employee must be a Pointsman.',
                [],
                422
            );
        }

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray());
        }

        DB::beginTransaction();
        try {
            LoadStablingSmChecklist::where('load_stabling_id', $id)->update([
                'total_load_cleared'     => $request->input('total_load_cleared'),
                'engine_reversal_handed_over' => $request->input('engine_reversal_handed_over'),
                'release_type'                => $request->input('release_type'),
            ]);

            // $record->update(['status' => 'PENDING_CLEARANCE']);
            $record->update([
                'old_pm_id'   => $record->pointsman_id, 
                'old_sm_id'   => $record->assign_to_sm,
                'status'       => 'PENDING_CLEARANCE',
                'pointsman_id' => $request->assign_to_pointsman,
            ]);

            DB::commit();
            return $this->success('Forwarded to Points-man for Load Clearance.', [
                'status'       => 'PENDING_CLEARANCE',
                'release_type' => $request->input('release_type'),
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error('Server error: ' . $e->getMessage(), [], 500);
        }
    }

    // ---------------------------------------------------------------
    // STAGE 3 — Points-man Load Clearance (brake release)
    // POST /api/load-stabling/clearance/store
    // ---------------------------------------------------------------
    public function storeClearance(Request $request): JsonResponse
    {
        $user = $request->user();
        $employee = $this->getEmployee($user);

        // Check if user is a Pointsman
        if (!$employee || $employee->designation_id != self::DESIGNATION_POINTSMAN) {
            return $this->error('Only Pointsmen can submit clearance.', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'load_stabling_id'   => 'required|integer|exists:load_stablings,id',
            'load_type'          => 'required|string|max:50',
            'release_type'       => 'required|in:FULL,PARTIAL',
            'total_loads_cleared'=> 'nullable|string|max:100',
            

            'assign_to_sm'         => 'required|integer|exists:master_employees,id',
           
            'photo_wedges_removed' => 'nullable|file|image|max:5120',
            'photo_chains_removed' => 'nullable|file|image|max:5120',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray());
        }

        $id = (int) $request->input('load_stabling_id');
        $record = LoadStabling::find($id);

        if (!$record) {
            return $this->error('Load stabling record not found.', [], 404);
        }

        // VALIDATION: The logged-in Pointsman must be the one assigned to this record
        if ($record->pointsman_id != $employee->id) {
            return $this->error(
                'You are not authorized to submit clearance for this entry. ' .
                'This entry is assigned to Pointsman ID: ' . $record->pointsman_id,
                [],
                403
            );
        }

        if ($record->status !== 'PENDING_CLEARANCE') {
            return $this->error('Clearance not yet authorised by SM.', [], 409);
        }

        DB::beginTransaction();
        try {
            $photoWedgesRemovedPath = $this->storePhoto($request, 'photo_wedges_removed', 'clearance');
            $photoChainsRemovedPath = $this->storePhoto($request, 'photo_chains_removed', 'clearance');

            $clearanceData = $this->extractDynamicData($request, [
                'photo_wedges_removed',
                'photo_chains_removed',
            ]);

            LoadStablingClearance::updateOrCreate(
                ['load_stabling_id' => $id],
                [
                    'load_type'           => $request->input('load_type'),
                    'load_sub_type'       => $request->input('load_sub_type'),
                    'release_type'        => $request->input('release_type'),
                    'total_loads_cleared' => $request->input('total_loads_cleared'),
                    'release_time'        => $request->input('release_time'),
                    'clearance_check_data'=> $clearanceData,
                    'photo_wedges_removed'=> $photoWedgesRemovedPath,
                    'photo_chains_removed'=> $photoChainsRemovedPath,
                ]
            );

            // $record->update(['status' => 'CLEARANCE_DONE']);
            $record->update([
                'status'       => 'CLEARANCE_DONE',
                'assign_to_sm' => $request->assign_to_sm,
            ]);


            DB::commit();
            return $this->success('Load Clearance submitted to Station Master/Manager.', [
                'status' => 'CLEARANCE_DONE',
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error('Server error: ' . $e->getMessage(), [], 500);
        }
    }

    // ---------------------------------------------------------------
    // STAGE 4 — Station Master/Manager Final Submission (dispatch confirmation)
    // POST /api/load-stabling/{id}/final-submit
    // ---------------------------------------------------------------
    public function finalSubmit(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $employee = $this->getEmployee($user);

        // Check if user is a Station Master or Station Manager
        if (!$employee || !$this->isStationAuthority($user)) {
            return $this->error('Only Station Masters or Station Managers can final submit.', [], 403);
        }

        $record = LoadStabling::find($id);
        if (!$record) {
            return $this->error('Record not found.', [], 404);
        }

        // VALIDATION: The logged-in user must be the one assigned to this record
        if ($record->assign_to_sm != $employee->id) {
            return $this->error(
                'You are not authorized to final submit this entry. ' .
                'This entry is assigned to Station Master/Manager ID: ' . $record->assign_to_sm,
                [],
                403
            );
        }

        // if ($record->status !== 'CLEARANCE_DONE') {
        //     return $this->error('Clearance not yet done by Points-man.', [], 409);
        // }

        $validator = Validator::make($request->all(), [
            // 'released_completely'      => 'required|in:YES,NO',
            'sm_private_number'        => 'required|string|max:100',
            'tnl_private_number'       => 'required|string|max:100',
            'train_ready_for_dispatch' => 'required|in:YES,NO',
        ]);
        if ($validator->fails()) {
            return $this->error('Validation failed', $validator->errors()->toArray());
        }

        DB::beginTransaction();
        try {
            LoadStablingFinalSubmission::updateOrCreate(
                ['load_stabling_id' => $id],
                [
                    'release_time'             => $request->input('release_time'),
                    // 'released_completely'       => $request->input('released_completely'),
                    'sm_private_number'         => $request->input('sm_private_number'),
                    'tnl_private_number'        => $request->input('tnl_private_number'),
                    'train_ready_for_dispatch'  => $request->input('train_ready_for_dispatch'),
                ]
            );

            $record->update(['status' => 'COMPLETED']);

            DB::commit();
            return $this->success('Load Stabling completed. Train cleared for dispatch.', [
                'status' => 'COMPLETED',
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error('Server error: ' . $e->getMessage(), [], 500);
        }
    }

    // ---------------------------------------------------------------
    // READ — Single record with all related data
    // GET /api/load-stabling/{id}
    // ---------------------------------------------------------------
    public function show($id): JsonResponse
    {
        $user = request()->user();
        $employee = $this->getEmployee($user);

        if (!$employee) {
            return $this->error('Employee record not found.', [], 404);
        }

        $record = LoadStabling::
        with([
        'stablestationData:id,name,code',
        'stationData:id,name,code',
        'assigntosmData:id,name', 
        'pointmanData:id,name',
        'oldsmData:id,name',
        'oldpmData:id,name'   
        ])
        ->with([
            'smChecklist',
            'clearance',
            'finalSubmission',
        ])->find($id);

        if (!$record) {
            return $this->error('Record not found.', [$id], 404);
        }

        // Get user role
        $role = $this->getUserRole($user);

        // AUTHORIZATION: Check if user is authorized to view this record
        $isAuthorized = false;

       if ($role === 'POINTSMAN') {
            // Pointsman can view if either old_pm_id OR pointsman_id matches
            $isAuthorized = (
                $record->old_pm_id == $employee->id ||
                $record->pointsman_id == $employee->id
            );
        } elseif ($this->isStationAuthority($user)) {
            // Station Master/Manager can view if either old_sm_id OR assign_to_sm matches
            $isAuthorized = (
                $record->old_sm_id == $employee->id ||
                $record->assign_to_sm == $employee->id
            );
        } elseif ($role === 'OTHER') {
            $isAuthorized = true; // ya tum restrict karna chaho to false
        }


        if (!$isAuthorized) {
            return $this->error(
                'You are not authorized to view this record.',
                [],
                403
            );
        }

        $data = $record->toArray();
        $data['photo_wedges_url'] = $record->photo_wedges ? Storage::url($record->photo_wedges) : null;
        $data['photo_chains_url'] = $record->photo_chains ? Storage::url($record->photo_chains) : null;

        if ($record->clearance) {
            $data['clearance']['photo_wedges_removed_url'] = $record->clearance->photo_wedges_removed
                ? Storage::url($record->clearance->photo_wedges_removed) : null;
            $data['clearance']['photo_chains_removed_url'] = $record->clearance->photo_chains_removed
                ? Storage::url($record->clearance->photo_chains_removed) : null;
        }

        return $this->success('OK', $data);
    }

    // ---------------------------------------------------------------
    // LIST — Tasks assigned to the logged-in Station Master/Manager
    // GET /api/load-stabling/assigned-to-sm
    // ---------------------------------------------------------------
    // public function assignedToSm(Request $request): JsonResponse
    // {
    //     $user = $request->user();
    //     $employee = $this->getEmployee($user);

    //     if (!$employee || !$this->isStationAuthority($user)) {
    //         return $this->error('Only Station Masters or Station Managers can access this list.', [], 403);
    //     }

    //     $records = LoadStabling::where('assign_to_sm', $employee->id)
    //         ->whereIn('status', ['PENDING_SM', 'SM_VERIFIED', 'PENDING_CLEARANCE'])
    //         ->orderByDesc('created_at')
    //         ->select([
    //             'id', 'pointsman_id', 'assign_to_sm', 'station', 'train_number',
    //             'load_type', 'stabling_line', 'running_line', 'total_load', 'chains_count', 'wedges_count', 'lp_name', 'tm_name', 'pointsman_name', 'status', 'created_at',
    //         ])
    //         ->get();

    //     return $this->success('OK', $records->toArray());
    // }

    public function assignedToSm(Request $request): JsonResponse
    {
        $user = $request->user();
        $employee = $this->getEmployee($user);

        if (!$employee || !$this->isStationAuthority($user)) {
            return $this->error('Only Station Masters or Station Managers can access this list.', [], 403);
        }

    $records = LoadStabling::where('assign_to_sm', $employee->id)
        ->whereIn('status', [
            'PENDING_SM',
            'SM_VERIFIED',
            'PENDING_CLEARANCE'
        ])
        ->with('stationData:id,name,code')
        ->orderByDesc('created_at')
        ->select([
            'id',
            'pointsman_id',
            'assign_to_sm',
            'station',
            'train_number',
            'load_type',
            'stabling_line',
            'running_line',
            'total_load',
            'status',
            'created_at',
            'safety_check_data',
        ])
        ->get()
        ->map(function ($record) {
            return [
                'id' => $record->id,
                'pointsman_id' => $record->pointsman_id,
                'assign_to_sm' => $record->assign_to_sm,

                'station_data' => [
                    'id' => $record->stationData?->id,
                    'name' => $record->stationData?->name,
                    'code' => $record->stationData?->code,
                ],

                'train_number' => $record->train_number,
                'load_type' => $record->load_type,
                'stabling_line' => $record->stabling_line,
                'running_line' => $record->running_line,
                'total_load' => $record->total_load,
                'status' => $record->status,
                'created_at' => $record->created_at,
                'safety_check_data' => $record->safety_check_data,
            ];
        });

    return $this->success('OK', $records->toArray());
    }

// search api
public function listAndSearch(Request $request): JsonResponse
{
    $user = $request->user();
    $employee = $this->getEmployee($user);

    // if (!$employee || !$this->isStationAuthority($user)) {
    //     return $this->error(
    //         'Only Station Masters or Station Managers can access this list.',
    //         [],
    //         403
    //     );
    // }
    if (!$employee ) {
        return $this->error(
            'Employee Not Found.',
            [],
            403
        );
    }

    $query = LoadStabling::query()
    ->whereIn('status', [
        $request->status1,
        $request->status2,
        $request->status3,
    ])
     ->where(function ($q) use ($employee) {
        $q->where('pointsman_id', $employee->id)
          ->orWhere('assign_to_sm', $employee->id);
    })
    ->with([
        'stationData:id,name,code',
        'assigntosmData:id,name', // ✅ Station Master ka naam bhi load hoga
        'oldpmData:id,name',
        'oldsmData:id,name'
    ])
    ->select([
        'id',
        'pointsman_id',
        'assign_to_sm',
        'station',
        'train_number',
        'load_type',
        'stabling_line',
        'running_line',
        'total_load',
        'status',
        'created_at',
        'safety_check_data',
    ])
    ->orderByDesc('created_at');

      // Train number search
    if ($request->filled('train_number')) {
        $query->where(
            'train_number',
            'LIKE',
            '%' . $request->train_number . '%'
        );
    }

    // Station search
    if ($request->filled('station')) {
        $query->where(
            'station',
            'LIKE',
            '%' . $request->station . '%'
        );
    }

    // Status search
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    // date
    if ($request->filled('created_at')) {
    $query->whereDate('created_at', $request->created_at);
    }

    $records = $query->paginate(
        min((int) $request->get('per_page', 20), 50)
    );

    $data = $records->getCollection()->map(function ($record) {
        return [
            'id' => $record->id,
            'pointsman_id' => $record->pointsman_id,
            'assign_to_sm_data' => [
            'id' => $record->assigntosmData?->id,
            'name' => $record->assigntosmData?->name,
            ],

            'station_data' => [
                'id' => $record->stationData?->id,
                'name' => $record->stationData?->name,
                'code' => $record->stationData?->code,
            ],

            'train_number' => $record->train_number,
            'load_type' => $record->load_type,
            'stabling_line' => $record->stabling_line,
            'running_line' => $record->running_line,
            'total_load' => $record->total_load,
            'status' => $record->status,
            'created_at' => $record->created_at,
            'safety_check_data' => $record->safety_check_data,
        ];
    });

    return $this->success('Records fetched successfully.', [
        'records' => $data,
        'pagination' => [
            'current_page' => $records->currentPage(),
            'last_page' => $records->lastPage(),
            'per_page' => $records->perPage(),
            'total' => $records->total(),
        ],
    ]);
}

// old assignments list api with search and filter
public function listOldAssignments(Request $request): JsonResponse
{
    $user = $request->user();
    $employee = $this->getEmployee($user);

    if (!$employee) {
        return $this->error('Employee Not Found.', [], 403);
    }

    $query = LoadStabling::query()
        ->where(function ($q) use ($employee) {
            $q->where('old_pm_id', $employee->id)
              ->orWhere('old_sm_id', $employee->id);
        })
        ->with([
            'stationData:id,name,code',
            'assigntosmData:id,name',
            'oldpmData:id,name',
            'oldsmData:id,name'
        ])
        ->select([
            'id',
            'pointsman_id',
            'assign_to_sm',
            'old_pm_id',
            'old_sm_id',
            'station',
            'train_number',
            'load_type',
            'stabling_line',
            'running_line',
            'total_load',
            'status',
            'created_at',
            'safety_check_data',
        ])
        ->orderByDesc('created_at');

    // Optional filters
    if ($request->filled('train_number')) {
        $query->where('train_number', 'LIKE', '%' . $request->train_number . '%');
    }

    if ($request->filled('station')) {
        $query->where('station', 'LIKE', '%' . $request->station . '%');
    }

    if ($request->filled('created_at')) {
        $query->whereDate('created_at', $request->created_at);
    }

    $records = $query->paginate(
        min((int) $request->get('per_page', 20), 50)
    );

    $data = $records->getCollection()->map(function ($record) {
        return [
            'id' => $record->id,
            'pointsman_id' => $record->pointsman_id,
            'old_pm_data' => [
                'id'   => $record->oldpmData?->id,
                'name' => $record->oldpmData?->name,
            ],
            'assign_to_sm_data' => [
                'id'   => $record->assigntosmData?->id,
                'name' => $record->assigntosmData?->name,
            ],
            'old_sm_data' => [
                'id'   => $record->oldsmData?->id,
                'name' => $record->oldsmData?->name,
            ],
            'station_data' => [
                'id'   => $record->stationData?->id,
                'name' => $record->stationData?->name,
                'code' => $record->stationData?->code,
            ],
            'train_number'      => $record->train_number,
            'load_type'         => $record->load_type,
            'stabling_line'     => $record->stabling_line,
            'running_line'      => $record->running_line,
            'total_load'        => $record->total_load,
            'status'            => $record->status,
            'created_at'        => $record->created_at,
            'safety_check_data' => $record->safety_check_data,
        ];
    });

    return $this->success('Old assignment records fetched successfully.', [
        'records' => $data,
        'pagination' => [
            'current_page' => $records->currentPage(),
            'last_page'    => $records->lastPage(),
            'per_page'     => $records->perPage(),
            'total'        => $records->total(),
        ],
    ]);
}



    // ---------------------------------------------------------------
    // LIST — Load Clearance tasks assigned to Points-man
    // GET /api/load-stabling/assigned-clearance
    // ---------------------------------------------------------------
    public function assignedClearance(Request $request): JsonResponse
    {
        $user = $request->user();
        $employee = $this->getEmployee($user);

        if (!$employee || $employee->designation_id != self::DESIGNATION_POINTSMAN) {
            return $this->error('Only Pointsmen can access this list.', [], 403);
        }

        $records = LoadStabling::where('pointsman_id', $employee->id)
            ->where('status', 'PENDING_CLEARANCE')
            ->with('smChecklist:id,load_stabling_id,release_type,engine_reversal_handed_over')
            ->orderByDesc('created_at')
            ->select([
                'id', 'pointsman_id', 'assign_to_sm', 'station', 'train_number',
                'load_type', 'load_sub_type', 'stabling_line', 'status', 'created_at',
            ])
            ->get();

        return $this->success('OK', $records->toArray());
    }

    // ---------------------------------------------------------------
    // LIST — GET /api/load-stabling/list
    // Filtered by role based on designation
    // ---------------------------------------------------------------
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $employee = $this->getEmployee($user);

        if (!$employee) {
            return $this->error('Employee record not found.', [], 404);
        }

        $role = $this->getUserRole($user);
        $status = $request->query('status');

        $query = LoadStabling::query()
        ->with([
            'stationData:id,name,code',
            'assigntosmData:id,name' // ✅ Station Master ka naam bhi load hoga
        ])
        ->orderByDesc('created_at');

        // If status filter is provided, apply it
        if ($status) {
            $query->where('status', strtoupper($status));
        }

        // Role-based filtering
        if ($role === 'POINTSMAN') {
            // Pointsman sees only their own submissions
            $query->where('pointsman_id', $employee->id);
            if (!$status) {
                $query->whereIn('status', ['PENDING_SM', 'PENDING_CLEARANCE','CLEARANCE_DONE']);
            }
        } elseif ($this->isStationAuthority($user)) {
            // Station Master/Manager sees only entries assigned to them
            $query->where('assign_to_sm', $employee->id);
            
            // If no status filter, show relevant statuses
            if (!$status) {
                $query->whereIn('status', ['PENDING_SM', 'SM_VERIFIED', 'CLEARANCE_DONE', 'COMPLETED']);
            }
        } elseif ($role === 'OTHER') {
            // Admin or other roles - you can decide to show all or restrict
            // For now, show all records (you can change this)
            // $query->whereRaw('1=0'); // Uncomment this to restrict
        }

        $records = $query
            ->with('smChecklist:id,load_stabling_id,release_type')
            ->select([
                'id', 'pointsman_id', 'assign_to_sm', 'station', 'train_number',
                'load_type', 'load_sub_type', 'stabling_line', 'status', 'created_at',
            ])
            ->get();

        return $this->success('OK', $records->toArray());
    }

    // ---------------------------------------------------------------
    // UPDATE — Edit load stabling record
    // PUT/PATCH /api/load-stabling/{id}
    // ---------------------------------------------------------------
// public function update(Request $request, int $id): JsonResponse
// {
//     $user = $request->user();
//     $employee = $this->getEmployee($user);

//     if (!$employee) {
//         return $this->error('Employee record not found.', [], 403);
//     }

//     $record = LoadStabling::find($id);

//     if (!$record) {
//         return $this->error('Record not found', [], 404);
//     }

//     // Check authorization based on role
//     $role = $this->getUserRole($user);


//     if ($this->isStationAuthority($user) && $record->assign_to_sm != $employee->id) {
//         return $this->error('You can only edit records assigned to you.', [], 403);
//     }

//     // SM verified hone ke baad edit block
//     if (!in_array($record->status, ['DRAFT', 'PENDING_SM'])) {
//         return $this->error(
//             'This record can no longer be edited.',
//             [],
//             422
//         );
//     }

    
//     // Build validation rules dynamically - skip file validation if no file is uploaded
//     $validationRules = [
//         'pointsman_name'    => 'sometimes|string|max:150',
//         'assign_to_sm'      => 'sometimes|exists:master_employees,id',
//         'station'           => 'sometimes|string|max:100',
//         'stable_station'    => 'sometimes|string|max:100',
//         'train_number'      => 'sometimes|string|max:30',
//         'arrival_time'      => 'sometimes|string|max:50',
//         'fouling_mark'      => 'sometimes|string|max:50',
//         'load_type'         => 'sometimes|string|max:50',
//         'load_sub_type'     => 'nullable|string|max:50',
//         'stabling_line'     => 'sometimes|string|max:20',
//         'running_line'      => 'nullable|string|max:50',
//         'loco_number'       => 'nullable|string|max:50',
//         'type_of_machine'   => 'nullable|array',
//         'type_of_machine.*' => 'string|max:100',
//         'total_load'        => 'nullable|string|max:100',
//         'all_vehicles_coupled'    => 'nullable|in:YES,NO',
//         'load_completely_stabled' => 'nullable|in:YES,NO',
//         'gps_location'      => 'nullable|string|max:255',
//     ];

//     // Only add file validation rules if the file is actually uploaded
//     if ($request->hasFile('photo_wedges')) {
//         $validationRules['photo_wedges'] = 'file|image|max:5120';
//     }

//     if ($request->hasFile('photo_chains')) {
//         $validationRules['photo_chains'] = 'file|image|max:5120';
//     }

//     // For multi-photos, we need to handle them separately
//     if ($request->hasFile('photo_uncoupled_vehicles')) {
//         $validationRules['photo_uncoupled_vehicles'] = 'array';
//         $validationRules['photo_uncoupled_vehicles.*'] = 'file|image|max:5120';
//     }

//     $validator = Validator::make($request->all(), $validationRules);

//     if ($validator->fails()) {
//         return $this->error(
//             'Validation failed',
//             $validator->errors()->toArray(),
//             422
//         );
//     }

//     DB::beginTransaction();

//     try {
//         $updateData = [];

//         // Handle type_of_machine specially
//         if ($request->has('type_of_machine')) {
//             $typeOfMachine = $request->input('type_of_machine');
            
//             if (is_string($typeOfMachine)) {
//                 $decoded = json_decode($typeOfMachine, true);
//                 if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
//                     $typeOfMachine = $decoded;
//                 } else {
//                     $typeOfMachine = [$typeOfMachine];
//                 }
//             }
            
//             if (!is_array($typeOfMachine)) {
//                 $typeOfMachine = null;
//             }
            
//             $updateData['type_of_machine'] = $typeOfMachine;
//         }

//         // Handle scalar fields
//         $scalarFields = [
//             'station', 'stable_station', 'train_number', 'arrival_time',
//             'fouling_mark', 'load_type', 'load_sub_type', 'stabling_line',
//             'running_line', 'loco_number', 'total_load', 'gps_location',
//             'pointsman_name', 'assign_to_sm',
//             'all_vehicles_coupled', 'load_completely_stabled'
//         ];

//         foreach ($scalarFields as $field) {
//             if ($request->has($field)) {
//                 $updateData[$field] = $request->input($field);
//             }
//         }

//         // Handle photo uploads - only if file is present
//         if ($request->hasFile('photo_wedges')) {
//             $newWedges = $this->storePhoto($request, 'photo_wedges');
//             if ($newWedges) {
//                 if ($record->photo_wedges) {
//                     Storage::disk('public')->delete($record->photo_wedges);
//                 }
//                 $updateData['photo_wedges'] = $newWedges;
//             }
//         }

//         if ($request->hasFile('photo_chains')) {
//             $newChains = $this->storePhoto($request, 'photo_chains');
//             if ($newChains) {
//                 if ($record->photo_chains) {
//                     Storage::disk('public')->delete($record->photo_chains);
//                 }
//                 $updateData['photo_chains'] = $newChains;
//             }
//         }

//         // Handle multi-photos (merge existing + new)
//         $uncoupledPhotos = [];
//         if ($request->hasFile('photo_uncoupled_vehicles')) {
//             $uncoupledPhotos = $this->mergeMultiPhotos($request, 'photo_uncoupled_vehicles');
//         } else {
//             // Keep existing photos if no new ones are uploaded
//             $existingSafetyData = $record->safety_check_data ?? [];
//             $uncoupledPhotos = $existingSafetyData['photo_uncoupled_vehicles'] ?? [];
//         }

//         // Handle safety data
//         $safetyData = $this->extractSafetyData($request);
//         unset($safetyData['safety_check_data']);
//         $safetyData = $this->sanitizeSafetyData($safetyData);
//         $safetyData['photo_uncoupled_vehicles'] = $uncoupledPhotos;

//         // If assign_to_sm is being updated, validate the new Station Master
//         if ($request->has('assign_to_sm')) {
//             $assignToSmId = $request->input('assign_to_sm');
//             $stationAuthority = MasterEmployee::find($assignToSmId);
            
//             if (!$stationAuthority) {
//                 DB::rollBack();
//                 return $this->error(
//                     'Station Master/Manager not found. Please select a valid Station Master or Manager.',
//                     [],
//                     422
//                 );
//             }
            
//             if (!in_array(
//                 $stationAuthority->designation_id,
//                 [
//                     self::DESIGNATION_STATION_MASTER,
//                     self::DESIGNATION_STATION_MANAGER
//                 ]
//             )) {
//                 DB::rollBack();
//                 return $this->error(
//                     'The selected employee must be either a Station Master or Station Manager.',
//                     [],
//                     422
//                 );
//             }
//         }

//         // Update the record
//         if (!empty($updateData)) {
//             $record->update($updateData);
//         }

//         // Update safety_check_data
//         $record->safety_check_data = $safetyData;
//         $record->save();

//         DB::commit();

//         return $this->success(
//             'Record updated successfully',
//             $record->fresh()->toArray()
//         );

//     } catch (\Throwable $e) {
//         DB::rollBack();
        
//         return $this->error(
//             'Server error: ' . $e->getMessage(),
//             [],
//             500
//         );
//     }
// }

public function update(Request $request, int $id): JsonResponse
{
    $user = $request->user();
    $employee = $this->getEmployee($user);

    if (!$employee) {
        return $this->error('Employee record not found.', [], 403);
    }

    $record = LoadStabling::find($id);

    if (!$record) {
        return $this->error('Record not found', [], 404);
    }

    // Check authorization based on role
    $role = $this->getUserRole($user);

    if ($this->isStationAuthority($user) && $record->assign_to_sm != $employee->id) {
        return $this->error('You can only edit records assigned to you.', [], 403);
    }

    // SM verified hone ke baad edit block
    if (!in_array($record->status, ['DRAFT', 'PENDING_SM'])) {
        return $this->error(
            'This record can no longer be edited.',
            [],
            422
        );
    }

    // Build validation rules dynamically - skip file validation if no file is uploaded
    $validationRules = [
        'pointsman_name'    => 'sometimes|string|max:150',
        'assign_to_sm'      => 'sometimes|exists:master_employees,id',
        'station'           => 'sometimes|string|max:100',
        'stable_station'    => 'sometimes|string|max:100',
        'train_number'      => 'sometimes|string|max:30',
        'arrival_time'      => 'sometimes|string|max:50',
        'fouling_mark'      => 'sometimes|string|max:50',
        'load_type'         => 'sometimes|string|max:50',
        'load_sub_type'     => 'nullable|string|max:50',

        // ✅ NEW — Rake sub-types
        'coaching_sub_type' => 'nullable|string|max:100',
        'goods_sub_type'    => 'nullable|string|max:100',

        'stabling_line'     => 'sometimes|string|max:20',
        'running_line'      => 'nullable|string|max:50',
        'loco_number'       => 'nullable|string|max:50',
        'type_of_machine'   => 'nullable|array',
        'type_of_machine.*' => 'string|max:100',
        'total_load'        => 'nullable|string|max:100',
        'all_vehicles_coupled'    => 'nullable|in:YES,NO',
        'load_completely_stabled' => 'nullable|in:YES,NO',
        'gps_location'      => 'nullable|string|max:255',

        // ✅ NEW — Train Manager Private Number
        'tm_private_number' => 'nullable|string|max:50',
    ];

    // Only add file validation rules if the file is actually uploaded
    if ($request->hasFile('photo_wedges')) {
        $validationRules['photo_wedges'] = 'file|image|max:5120';
    }

    if ($request->hasFile('photo_chains')) {
        $validationRules['photo_chains'] = 'file|image|max:5120';
    }

    // ✅ NEW — photo_wedges_placed validation
    if ($request->hasFile('photo_wedges_placed')) {
        $validationRules['photo_wedges_placed'] = 'file|image|max:5120';
    }

    // For multi-photos, we need to handle them separately
    if ($request->hasFile('photo_uncoupled_vehicles')) {
        $validationRules['photo_uncoupled_vehicles'] = 'array';
        $validationRules['photo_uncoupled_vehicles.*'] = 'file|image|max:5120';
    }

    $validator = Validator::make($request->all(), $validationRules);

    if ($validator->fails()) {
        return $this->error(
            'Validation failed',
            $validator->errors()->toArray(),
            422
        );
    }

    DB::beginTransaction();

    try {
        $updateData = [];

        // Handle type_of_machine specially
        if ($request->has('type_of_machine')) {
            $typeOfMachine = $request->input('type_of_machine');

            if (is_string($typeOfMachine)) {
                $decoded = json_decode($typeOfMachine, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $typeOfMachine = $decoded;
                } else {
                    $typeOfMachine = [$typeOfMachine];
                }
            }

            if (!is_array($typeOfMachine)) {
                $typeOfMachine = null;
            }

            $updateData['type_of_machine'] = $typeOfMachine;
        }

        // Handle scalar fields
        $scalarFields = [
            'station', 'stable_station', 'train_number', 'arrival_time',
            'fouling_mark', 'load_type', 'load_sub_type',

            // ✅ NEW — Rake sub-types
            'coaching_sub_type', 'goods_sub_type',

            'stabling_line',
            'running_line', 'loco_number', 'total_load', 'gps_location',
            'pointsman_name', 'assign_to_sm',
            'all_vehicles_coupled', 'load_completely_stabled',

            // ✅ NEW — Train Manager Private Number
            'tm_private_number',
        ];

        foreach ($scalarFields as $field) {
            if ($request->has($field)) {
                $updateData[$field] = $request->input($field);
            }
        }

        // Handle photo uploads - only if file is present
        if ($request->hasFile('photo_wedges')) {
            $newWedges = $this->storePhoto($request, 'photo_wedges');
            if ($newWedges) {
                if ($record->photo_wedges) {
                    Storage::disk('public')->delete($record->photo_wedges);
                }
                $updateData['photo_wedges'] = $newWedges;
            }
        }

        if ($request->hasFile('photo_chains')) {
            $newChains = $this->storePhoto($request, 'photo_chains');
            if ($newChains) {
                if ($record->photo_chains) {
                    Storage::disk('public')->delete($record->photo_chains);
                }
                $updateData['photo_chains'] = $newChains;
            }
        }

        // Handle multi-photos (merge existing + new)
        $uncoupledPhotos = [];
        if ($request->hasFile('photo_uncoupled_vehicles')) {
            $uncoupledPhotos = $this->mergeMultiPhotos($request, 'photo_uncoupled_vehicles');
        } else {
            // Keep existing photos if no new ones are uploaded
            $existingSafetyData = $record->safety_check_data ?? [];
            $uncoupledPhotos = $existingSafetyData['photo_uncoupled_vehicles'] ?? [];
        }

        // Handle safety data
        $safetyData = $this->extractSafetyData($request);
        unset($safetyData['safety_check_data']);
        $safetyData = $this->sanitizeSafetyData($safetyData);
        $safetyData['photo_uncoupled_vehicles'] = $uncoupledPhotos;

        // ✅ NEW — photo_wedges_placed ko safety_check_data me handle karo
        // (purani value preserve karo agar nayi upload nahi hui)
        $existingSafetyData = $record->safety_check_data ?? [];
        $existingWedgesPlaced = $existingSafetyData['photo_wedges_placed'] ?? null;

        if ($request->hasFile('photo_wedges_placed')) {
            $newWedgesPlaced = $this->storePhoto($request, 'photo_wedges_placed');

            if ($newWedgesPlaced) {
                // Purani file delete karo
                if ($existingWedgesPlaced) {
                    Storage::disk('public')->delete($existingWedgesPlaced);
                }
                $safetyData['photo_wedges_placed'] = $newWedgesPlaced;
            }
        } elseif ($existingWedgesPlaced) {
            // Nayi upload nahi hui → purani value preserve karo
            $safetyData['photo_wedges_placed'] = $existingWedgesPlaced;
        }

        // If assign_to_sm is being updated, validate the new Station Master
        if ($request->has('assign_to_sm')) {
            $assignToSmId = $request->input('assign_to_sm');
            $stationAuthority = MasterEmployee::find($assignToSmId);

            if (!$stationAuthority) {
                DB::rollBack();
                return $this->error(
                    'Station Master/Manager not found. Please select a valid Station Master or Manager.',
                    [],
                    422
                );
            }

            if (!in_array(
                $stationAuthority->designation_id,
                [
                    self::DESIGNATION_STATION_MASTER,
                    self::DESIGNATION_STATION_MANAGER
                ]
            )) {
                DB::rollBack();
                return $this->error(
                    'The selected employee must be either a Station Master or Station Manager.',
                    [],
                    422
                );
            }
        }

        // Update the record
        if (!empty($updateData)) {
            $record->update($updateData);
        }

        // Update safety_check_data
        $record->safety_check_data = $safetyData;
        $record->save();

        DB::commit();

        return $this->success(
            'Record updated successfully',
            $record->fresh()->toArray()
        );

    } catch (\Throwable $e) {
        DB::rollBack();

        return $this->error(
            'Server error: ' . $e->getMessage(),
            [],
            500
        );
    }
}


  
    // ---------------------------------------------------------------
    // public function updateClearance(Request $request, int $id): JsonResponse
    // {
    //     $user = $request->user();
    //     $employee = $this->getEmployee($user);

    //     if (!$employee) {
    //         return $this->error('Employee record not found.', [], 403);
    //     }

    //     // if ($employee->designation_id != self::DESIGNATION_POINTSMAN) {
    //     //     return $this->error('Only Pointsmen can edit clearance entries.', [], 403);
    //     // }

    //     $record = LoadStabling::with('clearance')->find($id);

    //     if (!$record) {
    //         return $this->error('Load stabling record not found.', [], 404);
    //     }

    //     // if ($record->pointsman_id != $employee->id) {
    //     //     return $this->error(
    //     //         'You are not authorized to edit clearance for this entry. ' .
    //     //         'This entry is assigned to Pointsman ID: ' . $record->pointsman_id,
    //     //         [],
    //     //         403
    //     //     );
    //     // }

    //     // TODO: confirm — lock edit once SM does final-submit (status COMPLETED)
    //     if ($record->status !== 'CLEARANCE_DONE') {
    //         return $this->error('This clearance can no longer be edited.', [], 422);
    //     }

    //     if (!$record->clearance) {
    //         return $this->error('Clearance record not found.', [], 404);
    //     }

    //     $validator = Validator::make($request->all(), [
    //         'release_type'        => 'sometimes|in:FULL,PARTIAL',
    //         'total_loads_cleared' => 'nullable|string|max:100',
    //         'assign_to_sm'        => 'sometimes|exists:master_employees,id',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->error('Validation failed', $validator->errors()->toArray());
    //     }

    //     DB::beginTransaction();
    //     try {
    //         $clearance = $record->clearance;

    //         // ── Photos: replace only if new file uploaded, else keep existing ──
    //         if ($request->hasFile('photo_wedges_removed')) {
    //             $newWedges = $this->storePhoto($request, 'photo_wedges_removed', 'clearance');
    //             if ($newWedges) {
    //                 if ($clearance->photo_wedges_removed) {
    //                     Storage::disk('public')->delete($clearance->photo_wedges_removed);
    //                 }
    //                 $clearance->photo_wedges_removed = $newWedges;
    //             }
    //         }

    //         if ($request->hasFile('photo_chains_removed')) {
    //             $newChains = $this->storePhoto($request, 'photo_chains_removed', 'clearance');
    //             if ($newChains) {
    //                 if ($clearance->photo_chains_removed) {
    //                     Storage::disk('public')->delete($clearance->photo_chains_removed);
    //                 }
    //                 $clearance->photo_chains_removed = $newChains;
    //             }
    //         }

    //         // ── Dynamic check fields (radio/select/text/tags etc.) ──
    //         // Same flat-field extraction pattern as storeClearance() —
    //         // merge over existing data so untouched fields aren't wiped.
    //         $newDynamicData = $this->extractDynamicData($request, [
    //             'photo_wedges_removed',
    //             'photo_chains_removed',
    //         ]);
    //         $clearance->clearance_check_data = array_merge(
    //             $clearance->clearance_check_data ?? [],
    //             $newDynamicData
    //         );

    //         if ($request->has('release_type')) {
    //             $clearance->release_type = $request->input('release_type');
    //         }
    //         if ($request->has('total_loads_cleared')) {
    //             $clearance->total_loads_cleared = $request->input('total_loads_cleared');
    //         }

    //         $clearance->save();

    //         // ── Re-assign SM/Manager if changed ──
    //         if ($request->has('assign_to_sm')) {
    //             $assignToSmId = $request->input('assign_to_sm');
    //             $stationAuthority = MasterEmployee::find($assignToSmId);

    //             if (!$stationAuthority || !in_array($stationAuthority->designation_id, [
    //                 self::DESIGNATION_STATION_MASTER,
    //                 self::DESIGNATION_STATION_MANAGER,
    //             ])) {
    //                 DB::rollBack();
    //                 return $this->error(
    //                     'The selected employee must be either a Station Master or Station Manager.',
    //                     [],
    //                     422
    //                 );
    //             }

    //             $record->assign_to_sm = $assignToSmId;
    //             $record->save();
    //         }

    //         DB::commit();

    //         return $this->success(
    //             'Load Clearance updated successfully.',
    //             $record->fresh(['clearance'])->toArray()
    //         );

    //     } catch (\Throwable $e) {
    //         DB::rollBack();
    //         return $this->error('Server error: ' . $e->getMessage(), [], 500);
    //     }
    // }

    public function updateClearance(Request $request, int $id): JsonResponse
{
    $user = $request->user();
    $employee = $this->getEmployee($user);

    if (!$employee) {
        return $this->error('Employee record not found.', [], 403);
    }

    $record = LoadStabling::with('clearance')->find($id);

    if (!$record) {
        return $this->error('Load stabling record not found.', [], 404);
    }

    if ($record->status !== 'CLEARANCE_DONE') {
        return $this->error('This clearance can no longer be edited.', [], 422);
    }

    if (!$record->clearance) {
        return $this->error('Clearance record not found.', [], 404);
    }

    $validator = Validator::make($request->all(), [
        'release_type'        => 'sometimes|in:FULL,PARTIAL',
        'total_loads_cleared' => 'nullable|string|max:100',
        'assign_to_sm'        => 'sometimes|exists:master_employees,id',
    ]);

    if ($validator->fails()) {
        return $this->error('Validation failed', $validator->errors()->toArray());
    }

    DB::beginTransaction();
    try {
        $clearance = $record->clearance;

        // ── Photos: replace only if new file uploaded, else keep existing ──
        if ($request->hasFile('photo_wedges_removed')) {
            $newWedges = $this->storePhoto($request, 'photo_wedges_removed', 'clearance');
            if ($newWedges) {
                if ($clearance->photo_wedges_removed) {
                    Storage::disk('public')->delete($clearance->photo_wedges_removed);
                }
                $clearance->photo_wedges_removed = $newWedges;
            }
        }

        if ($request->hasFile('photo_chains_removed')) {
            $newChains = $this->storePhoto($request, 'photo_chains_removed', 'clearance');
            if ($newChains) {
                if ($clearance->photo_chains_removed) {
                    Storage::disk('public')->delete($clearance->photo_chains_removed);
                }
                $clearance->photo_chains_removed = $newChains;
            }
        }

        // ── Dynamic check fields (radio/select/text/tags etc.) ──
        // extractDynamicData top-level request fields padhta hai.
        // Frontend abhi FLAT fields bhej raha hai → ye kaam karega.
        $newDynamicData = $this->extractDynamicData($request, [
            'photo_wedges_removed',
            'photo_chains_removed',
        ]);

        // ── FALLBACK: safety_check_data JSON bhi merge karo ──
        // (frontend JSON bhi bhejta hai; agar koi field flat nahi aaya
        //  to yahan se utha lo, lekin flat fields ko override mat hone do)
        if ($request->filled('safety_check_data')) {
            $json = json_decode($request->input('safety_check_data'), true);
            if (is_array($json)) {
                // JSON pehle, phir flat fields — flat fields win
                $newDynamicData = array_merge($json, $newDynamicData);

                // Photo URL strings ko clear_check_data me mat daalo —
                // wo dedicated columns me hain.
                unset(
                    $newDynamicData['photo_wedges_removed'],
                    $newDynamicData['photo_chains_removed'],
                );
            }
        }

        $clearance->clearance_check_data = array_merge(
            $clearance->clearance_check_data ?? [],
            $newDynamicData
        );

        if ($request->has('release_type')) {
            $clearance->release_type = $request->input('release_type');
        }
        if ($request->has('total_loads_cleared')) {
            $clearance->total_loads_cleared = $request->input('total_loads_cleared');
        }

        $clearance->save();

        // ── Re-assign SM/Manager if changed ──
        if ($request->has('assign_to_sm')) {
            $assignToSmId = $request->input('assign_to_sm');
            $stationAuthority = MasterEmployee::find($assignToSmId);

            if (!$stationAuthority || !in_array($stationAuthority->designation_id, [
                self::DESIGNATION_STATION_MASTER,
                self::DESIGNATION_STATION_MANAGER,
            ])) {
                DB::rollBack();
                return $this->error(
                    'The selected employee must be either a Station Master or Station Manager.',
                    [],
                    422
                );
            }

            $record->assign_to_sm = $assignToSmId;
            $record->save();
        }

        DB::commit();

        return $this->success(
            'Load Clearance updated successfully.',
            $record->fresh(['clearance'])->toArray()
        );

    } catch (\Throwable $e) {
        DB::rollBack();
        return $this->error('Server error: ' . $e->getMessage(), [], 500);
    }
}

    // ---------------------------------------------------------------
    // DELETE — Delete load stabling record
    // DELETE /api/load-stabling/{id}
    // ---------------------------------------------------------------
    public function destroy(int $id): JsonResponse
    {
        $record = LoadStabling::find($id);

        if (!$record) {
            return $this->error('Record not found', [], 404);
        }

        if (!in_array($record->status, ['DRAFT', 'PENDING_SM'])) {
            return $this->error('Cannot delete record after SM verification.', [], 422);
        }

        if ($record->photo_wedges) {
            Storage::disk('public')->delete($record->photo_wedges);
        }

        if ($record->photo_chains) {
            Storage::disk('public')->delete($record->photo_chains);
        }

        LoadStablingSmChecklist::where('load_stabling_id', $id)->delete();
        LoadStablingClearance::where('load_stabling_id', $id)->delete();
        LoadStablingFinalSubmission::where('load_stabling_id', $id)->delete();

        $record->delete();

        return $this->success('Record deleted successfully');
    }
}