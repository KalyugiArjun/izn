<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\APIComplaint;
use App\Models\Complaint;
use App\Models\MasterEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\RectificationHistory;
use App\Models\Rectify;
use App\Models\TransferredComplaint;
use Illuminate\Support\Facades\DB;
use Auth;


class ComplaintController extends Controller
{

    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'department_id' => 'required',
    //         'station_id' => 'required',
    //         'amenity_category_id' => 'required',
    //         'description' => 'required|string|max:3000',

    //     ], [
    //         'department_id.required' => 'Department is required',
    //         'station_id.required' => 'Station is required',
    //         'amenity_category_id.required' => 'Amenity Category is required',
    //         'description.required' => 'Description is required',
    //         'description.max' => 'Description must not exceed 500 words',

    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Validation error',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     $imagePaths = [];

    //     if ($request->hasFile('images')) {
    //         $files = is_array($request->file('images')) ? $request->file('images') : [$request->file('images')];

    //         foreach ($files as $image) {
    //             $path = $image->store('complaints', 'public');
    //             $imagePaths[] = $path;
    //         }
    //     }

    //     if (count($imagePaths) < 1) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'At least 1 image is required'
    //         ], 422);
    //     }

    //     if (count($imagePaths) > 3) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Maximum 3 images allowed'
    //         ], 422);
    //     }

    //     $wordCount = str_word_count($request->description);

    //     if ($wordCount > 500) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Description must not exceed 500 words'
    //         ], 422);
    //     }

    //     $complaintNumber = 'CMP' . now()->format('YmdHis') . Str::random(6);

    //     // dd($request->file('images'),   $imagePaths);
    //     // dd($request);



    //     $complaint = APIComplaint::create([
    //         'complaint_num' => $complaintNumber,
    //         'department_id' => $request->department_id,
    //         'station_id' => $request->station_id,
    //         'amenity_category_id' => $request->amenity_category_id,
    //         'description' => $request->description,
    //         'is_safety_critical' => $request->is_safety_critical ?? 0,
    //         'timeline' => $request->timeline,
    //         'assigned_to' => $request->assigned_to,
    //         'reported_before' => $request->reported_before ?? 0,
    //         'images' => $imagePaths, // store array, Laravel will cast to JSON
    //         // ── Geo fields ──
    //         'latitude'            => $request->latitude,
    //         'longitude'           => $request->longitude,
    //         'location_address'    => $request->location_address,
    //         'location_accuracy'   => $request->location_accuracy,
    //         'created_by' => auth()->id(),
    //         'updated_by' => auth()->id(),
    //     ]);

    //     RectificationHistory::create([
    //         'complaint_id' => $complaint->id,
    //         'action' => 'created',
    //         'new_status' => 0,
    //         'action_by' => auth()->id(),
    //     ]);

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Complaint created successfully',
    //         'data' => [
    //             'id' => $complaint->id,
    //             'complaint_num' => $complaint->complaint_num,
    //             'images' => array_map(fn($p) => asset('storage/app/public' . $p), $complaint->images),
    //             'description' => $complaint->description,
    //             'created_at' => $complaint->created_at,
    //             'latitude'         => $complaint->latitude,
    //             'longitude'        => $complaint->longitude,
    //             'location_address' => $complaint->location_address,
    //         ]
    //     ]);
    // }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'department_id' => 'required',
            'station_id' => 'required',
            'amenity_category_id' => 'required',
            'description' => 'required|string|max:3000',
            'images' => 'required|array|min:1|max:3',  // ✅ Changed to array validation
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048', // ✅ Validate each image 
             
        ], [
            'department_id.required' => 'Department is required',
            'station_id.required' => 'Station is required',
            'amenity_category_id.required' => 'Amenity Category is required',
            'description.required' => 'Description is required',
            'description.max' => 'Description must not exceed 500 words',
            'images.required' => 'At least 1 image is required',
            'images.array' => 'Images must be an array',
            'images.min' => 'At least 1 image is required',
            'images.max' => 'Maximum 3 images allowed',
            'images.*.image' => 'Each file must be an image',
            'images.*.mimes' => 'Images must be jpg, jpeg, or png format',
            'images.*.max' => 'Each image must not exceed 2MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $imagePaths = [];

        // ✅ Handle images properly - they will be an array
        if ($request->hasFile('images')) {
            // Since we're using 'images[]', it will automatically be an array
            $files = $request->file('images');
            
            // Ensure it's an array
            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $image) {
                $path = $image->store('complaints', 'public');
                $imagePaths[] = $path;
            }
        }

        // ✅ Validation already ensures at least 1 and max 3 images
        if (count($imagePaths) < 1) {
            return response()->json([
                'status' => false,
                'message' => 'At least 1 image is required'
            ], 422);
        }

        if (count($imagePaths) > 3) {
            return response()->json([
                'status' => false,
                'message' => 'Maximum 3 images allowed'
            ], 422);
        }

        $wordCount = str_word_count($request->description);

        if ($wordCount > 500) {
            return response()->json([
                'status' => false,
                'message' => 'Description must not exceed 500 words'
            ], 422);
        }

        $complaintNumber = 'CMP' . now()->format('YmdHis') . Str::random(6);

        $complaint = APIComplaint::create([
            'complaint_num' => $complaintNumber,
            'department_id' => $request->department_id,
            'station_id' => $request->station_id,
            'amenity_category_id' => $request->amenity_category_id,
            'description' => $request->description,
            'is_safety_critical' => $request->is_safety_critical ?? 0,
            'timeline' => $request->timeline,
            'assigned_to' => $request->assigned_to,
            'reported_before' => $request->reported_before ?? 0,
            'images' => $imagePaths,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'location_address' => $request->location_address,
            'location_accuracy' => $request->location_accuracy,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        RectificationHistory::create([
            'complaint_id' => $complaint->id,
            'action' => 'created',
            'new_status' => 0,
            'action_by' => auth()->id(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Complaint created successfully',
            'data' => [
                'id' => $complaint->id,
                'complaint_num' => $complaint->complaint_num,
                'images' => array_map(fn($p) => asset('storage/app/public' . $p), $complaint->images),
                'description' => $complaint->description,
                'created_at' => $complaint->created_at,
                'latitude' => $complaint->latitude,
                'longitude' => $complaint->longitude,
                'location_address' => $complaint->location_address,
            ]
        ]);
    }

    // ✅ Read All
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;

        // ✅ Get employee
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ]);
        }

        // ✅ Role checks
        $isComplaintUser = Complaint::where('department_id', $employee->department_id)
            ->where('designation_id', $employee->designation_id)
            ->exists();

        // $isRectifyUser = APIComplaint::where('assigned_employee_id', $employee->id)
        //     ->exists();

        $isRectifyUser = Rectify::where('designation_id', $employee->designation_id)->exists();

        // 🔥 NEW: Controller Role
        $isControllerUser = strtolower($user->type) === 'department admin';

        // // ✅ MAIN QUERY
        // $query = APIComplaint::query();

        // if ($isControllerUser) {
        //     // ✅ Controller sees ALL department complaints
        //     $query->where('department_id', $employee->department_id);
        // } else {
        //     // ✅ Normal users → created OR assigned
        //     $query->where(function ($q) use ($userId, $employee) {
        //         $q->where('created_by', $userId)
        //             ->orWhere('assigned_employee_id', $employee->id);
        //     });
        // }
    

        // ✅ MAIN QUERY
        $query = APIComplaint::query();

        // if ($isControllerUser) {
        //     // ✅ Controller sees ALL department complaints
        //     $query->where('department_id', $employee->department_id);
        // } elseif ($isRectifyUser) {

        //     // ✅ Rectify User → designation based complaints
        //     $query->where('assigned_to', $employee->designation_id);
        // } else {

        //     // ✅ Complaint User
        //     $query->where('created_by', $userId);
        // }


        if ($isControllerUser) {

            // ✅ Controller sees all department complaints
            $query->where('department_id', $employee->department_id);

        } else {

            // ✅ Employee sees assigned complaints
            $query->where(function ($q) use ($employee, $userId) {

                $q->where('assigned_employee_id', $employee->id)

                // fallback old designation complaints
                ->orWhere(function ($sub) use ($employee) {

                    $sub->whereNull('assigned_employee_id')
                        ->where('assigned_to', $employee->designation_id);

                })

                // creator complaints
                ->orWhere('created_by', $userId);

            });
        }

           
        $complaints = $query->latest()->get();

        if ($complaints->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No complaints found',
                'data' => []
            ], 200);
        }

        return response()->json([
            'status' => true,
            'message' => 'Complaint list fetched successfully',

            'role' => [
                'is_complaint_user'  => $isComplaintUser,
                'is_rectify_user'   => $isRectifyUser,
                'is_controller_user' => $isControllerUser // ✅ added
            ],

            'data' => $complaints
        ], 200);
    }


    // searchable list with pagination
    // public function searchPaginationList(Request $request)
    // {
    //     $user = Auth::user();
    //     $userId = $user->id;

    //     $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

    //     if (!$employee) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Employee not found'
    //         ]);
    //     }

    //     $isComplaintUser = Complaint::where('department_id', $employee->department_id)
    //         ->where('designation_id', $employee->designation_id)
    //         ->exists();

    //     // $isRectifyUser = Rectify::where('designation_id', $employee->designation_id)->exists();
    //     $isRectifyUser = APIComplaint::where('assigned_employee_id', $employee->id)->exists();
    //     $isControllerUser = APIComplaint::where('controller_id', $employee->id)->exists();

    //     // $isControllerUser = strtolower($user->type) === 'department admin';

        
    //     // Status map
    //     $statusMap = [
    //         0 => 'Pending',
    //         1 => 'Solved',
    //         2 => 'Reopened',
    //         3 => 'Closed',
    //         4 => 'Assigned',
    //         5 => 'Reassigned',
    //         6 => 'Transferred',
    //     ];

    //     $query = APIComplaint::query();

    //     // Role based filtering
    //     if ($isRectifyUser) {

    //         $query->where('department_id', $employee->department_id)
    //             ->whereIn('status', [1, 2, 4, 5]);
         

    //     } 
    //     elseif ($isControllerUser) {
    //         $query->where('department_id', $employee->department_id)
    //             ->whereIn('status', [0, 4]);
    //     } 
    //     else {
    //         $query->where(function ($q) use ($employee, $userId, $isControllerUser) {

    //             $q->where('assigned_employee_id', $employee->id)
    //             ->orWhere(function ($sub) use ($employee) {
    //                 $sub->whereNull('assigned_employee_id')
    //                     ->where('assigned_to', $employee->designation_id);
    //             })
    //             ->orWhere('created_by', $userId);
    //         });

    //     }



    //     // Status filter
    //     if ($request->filled('status')) {
    //         $status = (int) $request->input('status');
    //         if (array_key_exists($status, $statusMap)) {
    //             $query->where('status', $status);
    //         }
    //     }

    //     // // Status map (numeric id => DB status value)
    //     // $statusMap = [
    //     //     0 => 'Pending',
    //     //     1 => 'Rectified',
    //     //     2 => 'Reopened',
    //     //     3 => 'Closed',
    //     //     4 => 'Assigned',
    //     //     5 => 'Reassigned',
    //     //     6 => 'Transferred',
    //     // ];

    //     // $query = APIComplaint::query();

    //     // // Step 1: Role based filtering
    //     // if ($isControllerUser) {
    //     //     // Controller: Pending (0), Assigned (4)
    //     //     $query->where('department_id', $employee->department_id)
    //     //         ->whereIn('status', [0, 4]);

    //     // } elseif ($isRectifyUser) {
    //     //     $query->where('department_id', $employee->department_id)
    //     //         ->whereIn('status', [1, 2, 4, 5]);

    //     // } else {
    //     //     // Normal complaint user
    //     //     $query->where(function ($q) use ($employee, $userId) {
    //     //         $q->where('assigned_employee_id', $employee->id)
    //     //         ->orWhere(function ($sub) use ($employee) {
    //     //             $sub->whereNull('assigned_employee_id')
    //     //                 ->where('assigned_to', $employee->designation_id);
    //     //         })
    //     //         ->orWhere('created_by', $userId);
    //     //     });
    //     // }

        



    //     // // Step 2: Status filter (pehle status apply hoga)
    //     // if ($request->filled('status') && array_key_exists($request->status, $statusMap)) {
    //     //     $query->where('status', (int) $request->status);
    //     // }

    //     // Step 3: Search filter (status ke baad, multi-word support, case-insensitive)
    //     if ($request->filled('search')) {
    //         $search = trim($request->input('search'));

    //         if ($search !== '') {
    //             $keywords = preg_split('/\s+/', $search);

    //             $query->where(function ($q) use ($keywords) {
    //                 foreach ($keywords as $word) {
    //                     $q->where(function ($sub) use ($word) {
    //                         $sub->where('complaint_num', 'LIKE', "%{$word}%")
    //                             ->orWhere('description', 'LIKE', "%{$word}%")
    //                             ->orWhere('created_at', 'LIKE', "%{$word}%")
    //                             ->orWhere('location_address', 'LIKE', "%{$word}%");
    //                     });
    //                 }
    //             });
    //         }
    //     }

    //     //     \Log::info('FINAL COMPLAINT QUERY', [
    //     // 'employee_id' => $employee->id,
    //     // 'department_id' => $employee->department_id,
    //     // 'status' => $request->status,
    //     // 'sql' => $query->toSql(),
    //     // 'bindings' => $query->getBindings(),
    //     // 'count' => $query->count(),
    //     // ]);

    //     // Step 4: Relations + Pagination
    //     $perPage = (int) $request->input('per_page', 10);
    //     if ($perPage <= 0) {
    //         $perPage = 10;
    //     }

    //     $complaints = $query->with([
    //         'department:id,name',
    //         'station:id,name,code',
    //         'amenityCategory:id,name',
    //         'assignedEmployee:id,name,designation_id'
    //     ])->latest()->paginate($perPage);

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Complaint list fetched successfully',
    //         'role' => [
    //             'is_complaint_user'  => $isComplaintUser,
    //             'is_rectify_user'    => $isRectifyUser,
    //             'is_controller_user' => $isControllerUser
    //         ],
    //         'data' => $complaints
    //     ], 200);
    // }

    public function searchPaginationList(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ]);
        }

        $isComplaintUser = Complaint::where('department_id', $employee->department_id)
            ->where('designation_id', $employee->designation_id)
            ->exists();

        $isRectifyUser = APIComplaint::where('assigned_employee_id', $employee->id)->exists();
        $isControllerUser = APIComplaint::where('controller_id', $employee->id)->exists();

        // Status map
        $statusMap = [
            0 => 'Pending',
            1 => 'Solved',
            2 => 'Reopened',
            3 => 'Closed',
            4 => 'Assigned',
            5 => 'Reassigned',
            6 => 'Transferred',
        ];

        $query = APIComplaint::query();

        // Role based filtering
        if ($isRectifyUser) {
            $query->where('department_id', $employee->department_id)
                ->whereIn('status', [1, 2, 3, 4, 5]);
        } elseif ($isControllerUser) {
            $query->where('department_id', $employee->department_id)
                ->whereIn('status', $request->status === '4' ? [4] : [0]);
        } else {
            $query->where(function ($q) use ($employee, $userId) {
                $q->where('assigned_employee_id', $employee->id)
                ->orWhere(function ($sub) use ($employee) {
                    $sub->whereNull('assigned_employee_id')
                        ->where('assigned_to', $employee->designation_id);
                })
                ->orWhere('created_by', $userId);
            });
        }

        // ✅ Step 2: Status filter (multiple allowed)
        if ($request->filled('status')) {
            $statuses = explode(',', $request->input('status')); // comma separated
            $statuses = array_map('intval', $statuses);
            $validStatuses = array_intersect($statuses, array_keys($statusMap));

            if (!empty($validStatuses)) {
                $query->whereIn('status', $validStatuses);
            }
        }

        // ✅ Step 3: Search filter (multi-word + relations)
        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            if ($search !== '') {
                $keywords = preg_split('/\s+/', $search);

                $query->where(function ($q) use ($keywords) {
                    foreach ($keywords as $word) {
                        $q->orWhere('complaint_num', 'LIKE', "%{$word}%")
                        ->orWhere('description', 'LIKE', "%{$word}%")
                        ->orWhere('created_at', 'LIKE', "%{$word}%")
                        ->orWhere('location_address', 'LIKE', "%{$word}%")
                        // ✅ Search by relations
                        ->orWhereHas('station', function ($sub) use ($word) {
                            $sub->where('name', 'LIKE', "%{$word}%")
                                ->orWhere('code', 'LIKE', "%{$word}%");
                        })
                        ->orWhereHas('department', function ($sub) use ($word) {
                            $sub->where('name', 'LIKE', "%{$word}%");
                        })
                        ->orWhereHas('amenityCategory', function ($sub) use ($word) {
                            $sub->where('name', 'LIKE', "%{$word}%");
                        });
                    }
                });
            }
        }

        // Step 4: Relations + Pagination
        $perPage = (int) $request->input('per_page', 10);
        if ($perPage <= 0) {
            $perPage = 10;
        }

        $complaints = $query->with([
            'department:id,name',
            'station:id,name,code',
            'amenityCategory:id,name',
            'assignedEmployee:id,name,designation_id'
        ])->latest()->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Complaint list fetched successfully',
            'role' => [
                'is_complaint_user'  => $isComplaintUser,
                'is_rectify_user'    => $isRectifyUser,
                'is_controller_user' => $isControllerUser
            ],
            'data' => $complaints
        ], 200);
    }


    // status count data
    public function statusCount(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ]);
        }

        // Status map
        $statusMap = [
            0 => 'pending',
            1 => 'rectified',
            2 => 'reopened',
            3 => 'closed',
            4 => 'assigned',
            5 => 'reassigned',
            6 => 'transferred',
        ];

        $query = APIComplaint::query();

        // Role based filtering (same logic as searchPaginationList)
        $isComplaintUser = Complaint::where('department_id', $employee->department_id)
            ->where('designation_id', $employee->designation_id)
            ->exists();

        $isRectifyUser = APIComplaint::where('assigned_employee_id', $employee->id)->exists();
        $isControllerUser = APIComplaint::where('controller_id', $employee->id)->exists();

        if ($isRectifyUser) {
            $query->where('department_id', $employee->department_id)
                ->whereIn('status', [1, 2, 4, 5]);
        } elseif ($isControllerUser) {
            $query->where('department_id', $employee->department_id)
                ->whereIn('status', [0, 4]);
        } else {
            $query->where(function ($q) use ($employee, $userId) {
                $q->where('assigned_employee_id', $employee->id)
                ->orWhere(function ($sub) use ($employee) {
                    $sub->whereNull('assigned_employee_id')
                        ->where('assigned_to', $employee->designation_id);
                })
                ->orWhere('created_by', $userId);
            });
        }

        // Group by status and count
        $counts = $query->select('status', DB::raw('COUNT(*) as total'))
                        ->groupBy('status')
                        ->pluck('total', 'status')
                        ->toArray();

        // Map numeric status to names
        $result = [];
        foreach ($statusMap as $code => $name) {
            $result[$name] = $counts[$code] ?? 0;
        }

        return response()->json([
            'status' => true,
            'message' => 'Status count fetched successfully',
            'data' => $result
        ], 200);
    }


    // ✅ Read One
    public function show($id)
    {
        return APIComplaint::findOrFail($id);
    }


    public function update(Request $request, $id)
    {
        $complaint = APIComplaint::findOrFail($id);

        // ✅ Validation (same as store but optional fields allowed)
        $validator = Validator::make($request->all(), [
            'department_id' => 'sometimes|required',
            'station_id' => 'sometimes|required',
            'amenity_category_id' => 'sometimes|required',
            'description' => 'sometimes|required|string|max:3000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // ✅ Word count check
        if ($request->has('description')) {
            $wordCount = str_word_count($request->description);

            if ($wordCount > 500) {
                return response()->json([
                    'status' => false,
                    'message' => 'Description must not exceed 500 words'
                ], 422);
            }
        }

        // ✅ Existing images
        $imagePaths = $complaint->images ?? [];

        // ✅ Handle new images
        if ($request->hasFile('images')) {

            $files = is_array($request->file('images'))
                ? $request->file('images')
                : [$request->file('images')];

            if (count($files) > 3) {
                return response()->json([
                    'status' => false,
                    'message' => 'Maximum 3 images allowed'
                ], 422);
            }

            // 🔥 Delete old images (optional)
            foreach ($imagePaths as $oldImage) {
                Storage::disk('public')->delete($oldImage);
            }

            $imagePaths = [];

            foreach ($files as $image) {
                $path = $image->store('complaints', 'public');
                $imagePaths[] = $path;
            }
        }

        // ✅ Update data safely
        $complaint->update([
            'department_id'        => $request->department_id ?? $complaint->department_id,
            'station_id'           => $request->station_id ?? $complaint->station_id,
            'amenity_category_id'  => $request->amenity_category_id ?? $complaint->amenity_category_id,
            'description'          => $request->description ?? $complaint->description,
            'is_safety_critical'   => $request->is_safety_critical ?? $complaint->is_safety_critical,
            'timeline'             => $request->timeline ?? $complaint->timeline,
            'assigned_to'          => $request->assigned_to ?? $complaint->assigned_to,
            'reported_before'      => $request->reported_before ?? $complaint->reported_before,
            'images'               => $imagePaths,

            // ✅ Geo fields
            'latitude'             => $request->latitude ?? $complaint->latitude,
            'longitude'            => $request->longitude ?? $complaint->longitude,
            'location_address'     => $request->location_address ?? $complaint->location_address,
            'location_accuracy'    => $request->location_accuracy ?? $complaint->location_accuracy,

            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Complaint updated successfully',
            'data' => [
                'id' => $complaint->id,
                'complaint_num' => $complaint->complaint_num,
                'images' => array_map(fn($p) => asset('storage/' . $p), $complaint->images),
                'description' => $complaint->description,
                'updated_at' => $complaint->updated_at,
            ]
        ]);
    }

    public function updateRectify(Request $request, $id)
    {

        // dd($request->all(), $request->file('images'));

        $complaint = APIComplaint::findOrFail($id);

        // ✅ Validation (Rectification Required)
        $validator = Validator::make($request->all(), [
            'rectifier_name' => 'required|string',
            'rectifier_designation' => 'required|string',
            'rectification_date' => 'required',
            'rectification_remark' => 'required|string',

            'images' => 'required',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // ✅ Word limit check (optional)
        $wordCount = str_word_count($request->rectification_remark);
        if ($wordCount > 500) {
            return response()->json([
                'status' => false,
                'message' => 'Remark must not exceed 500 words'
            ], 422);
        }

        // ✅ Handle Rectification Images
        $rectifyImages = [];

        if ($request->hasFile('images')) {

            $files = is_array($request->file('images'))
                ? $request->file('images')
                : [$request->file('images')];

            if (count($files) > 3) {
                return response()->json([
                    'status' => false,
                    'message' => 'Maximum 3 images allowed'
                ], 422);
            }

            foreach ($files as $image) {
                $path = $image->store('rectifications', 'public');
                $rectifyImages[] = $path;
            }
        }

        RectificationHistory::create([
            'complaint_id' => $complaint->id,
            'action' => 'rectified',

            'old_status' => $complaint->status,
            'new_status' => 1, // completed

            'rectifier_name' => $request->rectifier_name,
            'rectifier_designation' => $request->rectifier_designation,
            'rectification_date' => $request->rectification_date,
            'rectification_remark' => $request->rectification_remark,
            'rectification_images' => $rectifyImages,

            'action_by' => auth()->id(),
        ]);

        // ✅ Update ONLY rectification fields
        $complaint->update([
            'rectifier_name' => $request->rectifier_name,
            'rectifier_designation' => $request->rectifier_designation,
            'rectification_date' => $request->rectification_date,
            'rectification_remark' => $request->rectification_remark,
            'rectification_images' => $rectifyImages,

            'rectified_by' => auth()->id(),
            'rectified_at' => now(),

            'status' => 1, // ✅ completed
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Rectification submitted successfully',
            'data' => [
                'id' => $complaint->id,
                'complaint_num' => $complaint->complaint_num,
                'rectifier_name' => $complaint->rectifier_name,
                'rectification_date' => $complaint->rectification_date,
                'images' => array_map(fn($p) => asset('storage/' . $p), $rectifyImages),
                'status' => $complaint->status,
            ]
        ]);
    }   

    public function submitFeedback(Request $request, $id)
    {
        $complaint = APIComplaint::find($id);
        if (!$complaint) {
            return response()->json([
                'status' => false,
                'message' => 'Complaint not found'
            ], 404);
        }

        // Validate request
        $validator = Validator::make($request->all(), [
            'feedback_description' => 'required|string',
            'feedback_rating'      => 'nullable|integer|min:1|max:5',
            'feedback_images.*'    => 'nullable|image|mimes:jpg,jpeg,png',
            'feedback_given_by'    => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation Error',
                'errors'  => $validator->errors()
            ], 422);
        }

        $imagePaths = [];
        if ($request->hasFile('feedback_images')) {
            foreach ($request->file('feedback_images') as $image) {
                $path = $image->store('feedback_images', 'public');
                $imagePaths[] = $path;
            }
        }

        $oldStatus = $complaint->status; // 👈 Capture before update

        // Update complaint with feedback and close it
        $complaint->update([
            'feedback_description' => $request->feedback_description,
            'feedback_rating'      => $request->feedback_rating,
            'feedback_images'      => $imagePaths,
            'feedback_given_by'    => $request->feedback_given_by,
            'feedback_given_at'    => now(),
            'status'               => 3, // 👈 Close the complaint
        ]);

        // Log closure in rectification history
        RectificationHistory::create([
            'complaint_id' => $complaint->id,
            'action'       => 'closed',
            'old_status'   => $oldStatus,
            'new_status'   => 3,
            'action_by'    => $request->feedback_given_by, // or auth()->id()
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Feedback submitted successfully and complaint closed',
            'data'    => $complaint
        ]);
    }

    public function submitClose(Request $request, $id)
    {
        $complaint = APIComplaint::find($id);

        if (!$complaint) {
            return response()->json([
                'status' => false,
                'message' => 'Complaint not found'
            ], 404);
        }

        // Feedback optional
        $validator = Validator::make($request->all(), [
            'feedback_description' => 'nullable|string',
            'feedback_rating'      => 'nullable|integer|min:1|max:5',
            'feedback_images.*'    => 'nullable|image|mimes:jpg,jpeg,png',
            'feedback_given_by'    => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation Error',
                'errors'  => $validator->errors()
            ], 422);
        }

        $imagePaths = [];

        if ($request->hasFile('feedback_images')) {
            foreach ($request->file('feedback_images') as $image) {
                $path = $image->store('feedback_images', 'public');
                $imagePaths[] = $path;
            }
        }

        $oldStatus = $complaint->status;

        // Complaint close with or without feedback
        $complaint->update([
            'feedback_description' => $request->feedback_description,
            'feedback_rating'      => $request->feedback_rating,
            'feedback_images'      => !empty($imagePaths) ? $imagePaths : $complaint->feedback_images,
            'feedback_given_by'    => $request->feedback_given_by,
            'feedback_given_at'    => now(),
            'status'               => 3,
        ]);

        RectificationHistory::create([
            'complaint_id' => $complaint->id,
            'action'       => 'closed',
            'old_status'   => $oldStatus,
            'new_status'   => 3,
            'action_by'    => $request->feedback_given_by ?? auth()->id(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Complaint closed successfully',
            'data'    => $complaint->fresh()
        ]);
    }

    public function reopenComplaint(Request $request, $id)
    {
        $complaint = APIComplaint::find($id);

        if (!$complaint) {
            return response()->json([
                'status' => false,
                'message' => 'Complaint not found'
            ], 404);
        }

        // ❌ Only allow reopen if completed
        if ($complaint->status != 1) {
            return response()->json([
                'status' => false,
                'message' => 'Only completed complaints can be reopened'
            ], 400);
        }

        // ✅ Validate reason
        $validator = Validator::make($request->all(), [
            'reopen_reason' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        RectificationHistory::create([
            'complaint_id' => $complaint->id,
            'action' => 'reopened',

            'old_status' => $complaint->status,
            'new_status' => 2, // reopened

            'reopen_reason' => $request->reopen_reason,

            'action_by' => auth()->id(),
        ]);

        // ✅ Update complaint
        $complaint->update([
            'status' => 2, // Reopened
            'reopen_reason' => $request->reopen_reason,
            'reopened_by' => auth()->id(),
            'reopened_at' => now(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Complaint reopened successfully',
            'data' => [
                'id' => $complaint->id,
                'complaint_num' => $complaint->complaint_num,
                'status' => $complaint->status,
                'reopen_reason' => $complaint->reopen_reason,
            ]
        ]);
    }

    public function getAssignComplaintData(Request $request, $id)
    {
        // dd($id);
        $user = Auth::user();

        // debug only
        // dd($user->type);

        // 🔥 Controller Role
        // $isControllerUser = strtolower($user->type) === 'department admin';

        $isControllerUser = in_array(strtolower(trim($user->type)), [
            'department admin',
            'controller',
            'admin',
            'emp'
        ]);

        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ]);
        }

        // ✅ Base query
        $query = APIComplaint::where('id', $id)
            ->where('department_id', $employee->department_id);

        $employeesQuery = MasterEmployee::where('department_id', $employee->department_id)
            ->where('status', 1)
            ->with('designation');

        // 🔍 Filter by designation
        if ($request->designation_id) {
            $employeesQuery->where('designation_id', $request->designation_id);
        }

        // 🔍 Search by name
        if ($request->search) {
            $employeesQuery->where('name', 'like', '%' . $request->search . '%');
        }

        // 🔍 Filter by station (if relation exists)
        if ($request->station_id) {
            $employeesQuery->whereHas('stations', function ($q) use ($request) {
                $q->where('station_id', $request->station_id);
            });
        }

        // ❗ Restrict only non-controller users
        if (!$isControllerUser) {
            return response()->json([
                'status' => false,
                'message' => 'You are not controller'
            ]);
        }

        $complaint = $query->with([
            'department',
            'station',
            'amenityCategory',
            'assignedEmployee'
        ])->first();

        if (!$complaint) {
            return response()->json([
                'status' => false,
                'message' => 'Complaint not found'
            ]);
        }

        // ✅ Employees of same department
        $employees = MasterEmployee::where('department_id', $user->department_id)
            ->where('status', 1)
            ->with('designation')
            ->orderBy('name')
            ->get();

        $employees = $employeesQuery->orderBy('name')->get();

        return response()->json([
            'status' => true,

            'complaint' => [
                'id' => $complaint->id,
                'complaint_no' => $complaint->complaint_num,
                'station' => $complaint->station->name ?? '—',
                'category' => $complaint->amenityCategory->name ?? '—',
                'date' => $complaint->created_at->format('d M Y'),
                'status' =>  $complaint->status,
                'is_safety' => $complaint->is_safety_critical,
                'assigned_employee_id' => $complaint->assigned_employee_id
            ],

            'employees' => $employees->map(function ($emp) {
                return [
                    'id' => $emp->id,
                    'name' => $emp->name,
                    'designation' => $emp->designation->name ?? '—',
                    'mobile' => $emp->mobile,
                    'email' => $emp->email
                ];
            })
        ]);
    }

    // public function assignComplaintStore(Request $request, $id)
    // {
    //     $request->validate([
    //         'employee_id' => 'required|exists:master_employees,id'
    //     ]);

    //     $user = Auth::user();

    //     $isControllerUser = strtolower($user->type) === 'department admin';

    //     // ✅ Find complaint
    //     $query = APIComplaint::where('id', $id)
    //         ->where('department_id', $user->department_id);

    //     if (!$isControllerUser) {
    //         $query->where('assigned_to', $user->designation);
    //     }

    //     $complaint = $query->first();

    //     if (!$complaint) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Unauthorized or complaint not found'
    //         ]);
    //     }

    //     // ✅ Assign
    //     $complaint->assigned_employee_id = $request->employee_id;
    //     $complaint->assigned_employee_at = now();
    //     $complaint->status = 4; // Assigned
    //     $complaint->save();

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Complaint assigned successfully'
    //     ]);
    // }

    public function assignComplaintStore(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|exists:master_employees,id'
        ]);

        $user = Auth::user();

        // ✅ Login employee
        $loginEmployee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$loginEmployee) {
            return response()->json([
                'status' => false,
                'message' => 'Login employee not found'
            ]);
        }

        // ✅ Controller check
        $isControllerUser = in_array(strtolower(trim($user->type)), [
            'department admin',
            'controller',
            'admin',
            'emp'
        ]);

        if (!$isControllerUser) {
            return response()->json([
                'status' => false,
                'message' => 'Only controller can assign complaint'
            ]);
        }

        // ✅ Find complaint
        $complaint = APIComplaint::where('id', $id)
            ->where('department_id', $loginEmployee->department_id)
            ->first();

        if (!$complaint) {
            return response()->json([
                'status' => false,
                'message' => 'Complaint not found'
            ]);
        }

        // ✅ Selected employee
        $employee = MasterEmployee::where('id', $request->employee_id)
            ->where('status', 1)
            ->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ]);
        }

        // ✅ SAVE ASSIGNMENT
        $complaint->assigned_employee_id = $employee->id;
        $complaint->assigned_employee_at = now();

        // optional
        $complaint->assigned_to = $employee->designation_id;
        $complaint->controller_id = $loginEmployee->id;

        $complaint->status = 4;

        $complaint->save();

        // ✅ History
        RectificationHistory::create([
            'complaint_id' => $complaint->id,
            'action' => 'assigned',
            'old_status' => 0,
            'new_status' => 4,
            'rectifier_name' => $employee->name,
            'rectifier_designation' => optional($employee->designation)->name,
            'rectification_date' => now(),
            'action_by' => $user->id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Complaint assigned successfully',
            'data' => [
                'complaint_id' => $complaint->id,
                'assigned_employee_id' => $employee->id,
                'employee_name' => $employee->name,
            ]
        ]);
    }

    public function history($id)
    {
        $complaint = APIComplaint::find($id);

        if (!$complaint) {
            return response()->json([
                'status'  => false,
                'message' => 'Complaint not found',
            ], 404);
        }

        $histories = RectificationHistory::where('complaint_id', $id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($h) {
                return [
                    'id'                     => $h->id,
                    'action'                 => $h->action,          // created | rectified | reopened
                    'old_status'             => $h->old_status,
                    'new_status'             => $h->new_status,

                    // rectification fields
                    'rectifier_name'         => $h->rectifier_name,
                    'rectifier_designation'  => $h->rectifier_designation,
                    'rectification_date'     => $h->rectification_date,
                    'rectification_remark'   => $h->rectification_remark,
                    'rectification_images'   => collect($h->rectification_images ?? [])
                        ->map(fn($p) => asset('storage/' . $p))
                        ->values(),

                    // reopen fields
                    'reopen_reason'          => $h->reopen_reason,

                    'action_by'              => $h->action_by,
                    'created_at'             => $h->created_at,
                ];
            });

        return response()->json([
            'status'  => true,
            'message' => 'History fetched successfully',
            'data'    => [
                'complaint' => [
                    'id'            => $complaint->id,
                    'complaint_num' => $complaint->complaint_num,
                    'description'   => $complaint->description,
                    'status'        => $complaint->status,
                    'station'       => ['name'=>$complaint->station->name,
                                        'code'=>$complaint->station->code,],
                    'department'    => ['name'=>$complaint->department->name,],
                    
                    'created_at'    => $complaint->created_at,
                ],
                'history' => $histories,
            ],
        ]);
    }

    public function assign(Request $request, $id)
    {
        $user = Auth::user();

        $request->validate([
            'employee_id' => 'required|exists:master_employees,id',
        ]);

        // Get employee (same department only)
        $employee = MasterEmployee::where('id', $request->employee_id)
            ->where('status', 1)
            ->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid employee'
            ], 400);
        }

        

        // Get complaint
        $complaint = APIComplaint::where('id', $id)
            ->where('department_id', $employee->department_id)
            ->first();

        if (!$complaint) {
            return response()->json([
                'status' => false,
                'message' => 'Complaint not found or Department not match.'
            ], 404);
        }

        $oldStatus = $complaint->status;

        if (!in_array($oldStatus, [0, 4])) { // Pending or Assigned
            return response()->json([
                'status' => false,
                'message' => 'Only pending or assigned complaints can be reassigned'
            ], 400);
        }

        // 🔥 CHECK: First assign OR reassign
        if (is_null($complaint->assigned_employee_id)) {
            // ✅ First assignment
            $newStatus = 4;
            $action = 'assigned';
        } else {
            // ✅ Reassignment
            $newStatus = 5;
            $action = 'reassigned';
        }

        // Prevent assigning to same employee again (optional but smart)
        if ($complaint->assigned_employee_id == $employee->id) {
            return response()->json([
                'status' => false,
                'message' => 'This complaint is already assigned to the selected employee'
            ], 400);
        }
        
        // Save assignment
        $complaint->assigned_employee_id = $employee->id;
        $complaint->assigned_employee_at = now();
        $complaint->status = $newStatus;
        $complaint->save();
        

        // Save history
        RectificationHistory::create([
            'complaint_id' => $complaint->id,
            'action' => $action,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'rectifier_name' => $employee->name,
            'rectifier_designation' => optional($employee->designation)->name,
            'rectification_date' => now(),
            'action_by' => $user->id,
        ]);

        return response()->json([
            'status' => true,
            'message' => ucfirst($action) . ' successfully',
            'data' => $complaint
        ]);
    }



    public function transfer(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'department_id' => 'required',
            'assigned_to'   => 'required',
        ], [
            'department_id.required' => 'Department is required',
            'assigned_to.required'   => 'Assigned To is required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // ✅ Find complaint
        $complaint = APIComplaint::find($id);

        if (!$complaint) {
            return response()->json([
                'status' => false,
                'message' => 'Complaint not found'
            ], 404);
        }

        // ✅ Validate designation belongs to department
        $isValid = Rectify::where('department_id', $request->department_id)
            ->where('designation_id', $request->assigned_to)
            ->exists();

        if (!$isValid) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid designation for selected department'
            ], 422);
        }

        // ✅ Store old values
        $oldDepartment = $complaint->department_id;
        $oldAssignedTo = $complaint->assigned_to;
        $oldStatus     = $complaint->status;

        // ✅ New status = TRANSFER (6)
        $newStatus = 6;

        // ✅ Update complaint
        $complaint->update([
            'department_id' => $request->department_id,
            'assigned_to'   => $request->assigned_to,
            'status'        => $newStatus,
            'updated_by'    => auth()->id(),
        ]);

        // ✅ Save history
        RectificationHistory::create([
            'complaint_id' => $complaint->id,
            'action' => 'transferred',

            // 🔥 Status tracking
            'old_status' => $oldStatus,
            'new_status' => $newStatus,

            // 🔥 Transfer tracking
            'old_department_id' => $oldDepartment,
            'new_department_id' => $request->department_id,
            'old_assigned_to'   => $oldAssignedTo,
            'new_assigned_to'   => $request->assigned_to,

            'action_by' => auth()->id(),
        ]);

        // ✅ Save transfer data
        TransferredComplaint::create([

            'complaint_id'       => $complaint->id,

            'from_department_id' => $oldDepartment,
            'to_department_id'   => $request->department_id,

            'from_assigned_to'   => $oldAssignedTo,
            'to_assigned_to'     => $request->assigned_to,

            'transferred_by'     => auth()->id(),

            'transfer_date'      => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Complaint transferred successfully',
            'data' => [
                'complaint_id' => $complaint->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'old_department' => $oldDepartment,
                'new_department' => $request->department_id,
                'old_assigned_to' => $oldAssignedTo,
                'new_assigned_to' => $request->assigned_to,
            ]
        ]);
    }

    // ✅ Delete
    public function destroy($id)
    {
        APIComplaint::findOrFail($id)->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
