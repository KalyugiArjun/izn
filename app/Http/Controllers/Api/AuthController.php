<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\APIComplaint;
use App\Models\Complaint;
use App\Models\MasterEmployee;
use App\Models\EmployeeStation;
use App\Models\Rectify;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LoadStabling;
use App\Models\LoadStablingClearance;
use App\Models\AmbushInspection;
use App\Models\StaffCounseling;
use App\Models\EmployeePME;
use App\Models\EmployeeRefresherCourse;
use App\Models\EmployeeGradation;
use App\Models\EmergencyContact;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
    use Illuminate\Http\JsonResponse;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class AuthController extends Controller
{
    public function checkPhone(Request $request)
    {
        try {
            $request->validate([
                'phone_number' => 'required|digits:10',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid phone number. Please enter a valid 10-digit number.',
                'errors' => $e->errors()
            ], 422);
        }

        // ✅ Step 1: Check in master_employees
        $employee = MasterEmployee::where('mobile', $request->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'This phone number is not registered by admin.',
            ], 404);
        }

        // ✅ Step 2: Check in users table
        $user = User::where('phone_number', $request->phone_number)->first();

        if ($user) {
            return response()->json([
                'status' => true,
                'exists' => true,
                'phone_number' => $request->phone_number,
                'employee_name' => $employee->name, // optional
                'message' => 'User exists. Please enter password.'
            ]);
        } else {
            return response()->json([
                'status' => true,
                'exists' => false,
                'phone_number' => $request->phone_number,
                'employee_name' => $employee->name, // optional
                'message' => 'Create your password.'
            ]);
        }
    }


    // ✅ Register with Phone Number
    public function register(Request $request)
    {
        try {
            $request->validate([
                'phone_number' => 'required|string|max:15|unique:users,phone_number',
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        }

        // ✅ Find employee
        $employee = MasterEmployee::where('mobile', $request->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found. Contact admin.'
            ], 404);
        }

        // ✅ Create user with employee data
        $user = User::create([
            'name' => $employee->name,
            'email' => $employee->email,
            'phone_number' => $employee->mobile,
            'password' => Hash::make($request->password),

            // 🔥 Your requirements
            'type' => 'emp', // ✅ set employee type
            'profile' => 'avatar.png', // ✅ default image

            'lang' => 'english',
            'subscription' => 1,
            'parent_id' => 1,
            'is_active' => 1,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Employee registered successfully',
            'data' => $user
        ], 201);
    }


    public function login(Request $request)
    {
        $request->validate([
            'phone_number' => 'required',
            'password' => 'required',
        ]);

        // Step 1: User check
        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Step 2: User active check
        if ($user->is_active == 0) {
            return response()->json([
                'status' => false,
                'message' => 'Your account is inactive'
            ], 403);
        }

        // Step 3: Employee active check
        $employee = MasterEmployee::where('mobile', $request->phone_number)->first();

        if (!$employee || $employee->status == 0) {
            return response()->json([
                'status' => false,
                'message' => 'Employee is inactive.'
            ], 403);
        }

        // Step 4: Password check
        if (!Auth::attempt([
            'phone_number' => $request->phone_number,
            'password' => $request->password
        ])) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Step 5: Authenticated user
        $user = Auth::user();

        // Employee relation load
        $user->load('masterEmployee');

        // Department Admin name override
        if (
            strtolower($user->type) === 'department admin' &&
            $user->masterEmployee
        ) {
            $user->name = $user->masterEmployee->name;
        }

        // Device details update
        $user->update([
            'device_id'     => $request->device_id,
            'device_name'   => $request->device_name,
            'device_type'   => $request->device_type,
            'app_version'   => $request->app_version,
            'ip_address'    => $request->ip(),
            'last_login_at' => now()->setTimezone('Asia/Kolkata'),
        ]);

       
            // Old tokens delete
            $user->tokens()->delete();

            // New token create
            $token = $user->createToken('auth_token')->plainTextToken;

            // Initial expiry = 2 hours
            $user->tokens()->latest()->first()->update([
                'expires_at' => now()->addHours(2)
            ]);

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'data' => $user
        ]);
    }

    public function getEmailByPhone($phone)
    {
        try {

            $user = User::where(
                'phone_number',
                $phone
            )->first();

            if (!$user) {

                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ], 404);
            }

            return response()->json([
                'status' => true,
                'email' => $user->email
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function sendForgotOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check user
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        try {

            // Delete old otp
            DB::table('password_resets')
                ->where('email', $request->email)
                ->delete();

            // Generate 6 digit OTP
            $otp = rand(100000, 999999);

            // Save OTP
            DB::table('password_resets')->insert([
                'email' => $request->email,
                // 'token' => $otp,
                'token' => Hash::make($otp),
                'created_at' => now()
            ]);

            // Send Email
            Mail::raw(
                "Your password reset OTP is: $otp",
                function ($message) use ($request) {

                    $message->to($request->email)
                        ->subject('Password Reset OTP');
                }
            );

            return response()->json([
                'status' => true,
                'message' => 'OTP sent to email'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyForgotOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check OTP
        // $reset = DB::table('password_resets')
        //     ->where('email', $request->email)
        //     ->where('token', $request->otp)
        //     ->first();
        $reset = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

            // OTP Expiry Check (10 Minutes)

        if (
            Carbon::parse($reset->created_at)
                ->addMinutes(10)
                ->isPast()
        ) {

            // Delete expired OTP
            DB::table('password_resets')
                ->where('email', $request->email)
                ->delete();

            return response()->json([
                'status' => false,
                'message' => 'OTP expired. Please request new OTP.'
            ], 400);
        }

        // if (!$reset) {

        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Invalid OTP'
        //     ], 400);
        // }

        if (!$reset ||!Hash::check($request->otp, $reset->token))
        {
            return response()->json([
                'status' => false,
                'message' => 'Invalid OTP'
            ], 400);
        }
        
        // Update password
        User::where('email', $request->email)
            ->update([
                'password' => Hash::make($request->password)
            ]);

        // Delete OTP
        DB::table('password_resets')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            'status' => true,
            'message' => 'Password changed successfully'
        ]);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        // Check old password
        if (
            !Hash::check(
                $request->old_password,
                $user->password
            )
        ) {

            return response()->json([
                'status' => false,
                'message' => 'Old password is incorrect'
            ], 400);
        }

        // Update password
        $user->update([
            'password' => Hash::make(
                $request->password
            )
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Password changed successfully'
        ]);
    }

    // ✅ Get Profile
    public function profile()
    {
        return response()->json([
            'status' => true,
            'data' => Auth::user()
        ]);
    }

    public function profileUpdate(Request $request)
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

        // Update allowed fields
        $employee->update($request->only([
            'name',
            'usmid',
            'department_id',
            'designation_id',
            'date_of_birth',
            'date_of_appointment',
            'date_of_retirement',
            'date_of_posting_present_station',
            'mobile',
            'email',
            'posting_present_station',
            'emergency_contact_number',
            'relation',
            'status',
            'updated_by'
        ]));

        // Reload with relations
        $employee->load([
            'department:id,name',
            'designation:id,name',
            'stations:id,code,name'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Employee updated successfully',
            'data' => $employee
        ]);
    }

    // public function getEmployeeStation()
    // {
    //     $user = Auth::user();

    //     // Find employee by mobile number
    //     $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

    //     if (!$employee) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Employee not found'
    //         ], 404);
    //     }

    //     $data = EmployeeStation::where('master_employee_id', $employee->id)
    //         ->orderBy('id', 'asc')
    //         ->get();

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Employee station list',
    //         'data' => $data
    //     ]);
    // }

    public function updateEmployeeStation(Request $request)
    {
        $request->validate([
            'station_id' => 'required|array',
            'station_id.*' => 'integer'
        ]);


        $user = Auth::user();

        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        // Purane station delete karo
        EmployeeStation::where('master_employee_id', $employee->id)->delete();

        // Naye station insert karo
        $insertData = [];

        foreach ($request->station_id as $stationId) {
            $insertData[] = [
                'master_employee_id' => $employee->id,
                'station_id' => $stationId,
                'created_at' => now(),
            ];
        }



        EmployeeStation::insert($insertData);

        return response()->json([
            'status' => true,
            'message' => 'Stations updated successfully'
        ]);
    }

 
    public function storeOrUpdateEmergencyContact(Request $request): JsonResponse
    {
        // Validate incoming request
        $request->validate([
            'name'          => 'required|string|max:255',
            'mobile_number' => 'required|string|max:15',
            'relation'      => 'required|string|max:50',
            'email'         => 'nullable|email',
            'address'       => 'nullable|string|max:255',
            'other_relation'=> 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        // Find employee by mobile number
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        // Create or update emergency contact
        $contact = EmergencyContact::updateOrCreate(
            ['employee_id' => $employee->id],
            [
                'name'           => $request->name,
                'mobile_number'  => $request->mobile_number,
                'email'          => $request->email,
                'address'        => $request->address,
                'relation'       => $request->relation,
                'other_relation' => $request->other_relation,
                'created_by'     => $employee->id,
                'updated_by'     => $employee->id,
            ]
        );

        // Eager load employee relation
        $contact->load('employee');

        return response()->json([
            'status'  => true,
            'message' => 'Emergency contact saved successfully',
            'data'    => $contact
        ]);
    }

    public function emergencyContactIndex(Request $request): JsonResponse
    {
        $user = Auth::user();

        // Authenticated employee fetch karo
        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found',
                'completion' => 0,
                'data' => []
            ], 404);
        }

        // Relations ke saath employee reload karo
        $employee = MasterEmployee::with([
            'department:id,name',
            'designation:id,name',
            'stations:id,code,name'
        ])->find($employee->id);

        // Emergency contact fetch karo
        $contact = EmergencyContact::where('employee_id', $employee->id)->first();

        // Required fields for profile completion
        $employeeFields = [
            'name', 'usmid', 'department_id', 'designation_id',
            'date_of_birth', 'date_of_appointment', 'date_of_retirement',
            'date_of_posting_present_station', 'mobile', 'email', 'posting_present_station'
        ];

        $contactFields = ['name', 'mobile_number', 'relation'];

        $totalFields = count($employeeFields) + count($contactFields);
        $filled = 0;

        foreach ($employeeFields as $field) {
            if (!empty($employee->$field)) {
                $filled++;
            }
        }

        if ($contact) {
            foreach ($contactFields as $field) {
                if (!empty($contact->$field)) {
                    $filled++;
                }
            }
        }

        $percentage = round(($filled / $totalFields) * 100);

        return response()->json([
            'status' => true,
            'message' => 'Profile data with completion percentage',
            'completion' => $percentage,
            'employee' => $employee,
            'emergency_contact' => $contact
        ]);
    }


    // public function checkUserRoles()
    // {
    //     $user = Auth::user();

    //     $employee = MasterEmployee::with('designation')
    //         ->where('mobile', $user->phone_number)
    //         ->first();

    //     if (!$employee) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Employee not found'
    //         ]);
    //     }

    //     // ✅ employee_station table se station_id nikalo
    //     $employeeStation = EmployeeStation::where(
    //         'master_employee_id',
    //         $employee->id
    //     )->first();

    //     $roleType = strtolower(
    //         $employee->designation->role_type ?? ''
    //     );

    //     return response()->json([

    //         'status' => true,

    //         'is_controller_user' => $roleType === 'controller',

    //         'is_complaint_user' => $roleType === 'complaint',

    //         'is_rectify_user' => $roleType === 'rectify',

    //         'role_type' => $roleType,

    //         'designation' => $employee->designation->name ?? null,

    //         // ✅ station_id add
    //         'station_id' => $employeeStation->station_id ?? null,

    //         'employee' => $employee
    //     ]);
    // }

    public function checkUserRoles()
    {
        $user = Auth::user();

        // Get Employee
        $employee = MasterEmployee::with('designation')
            ->where('mobile', $user->phone_number)
            ->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ]);
        }

        // Employee Station
        $employeeStation = EmployeeStation::where(
            'master_employee_id',
            $employee->id
        )->first();

        // Complaint User
        $isComplaintUser = Complaint::where(
                'department_id',
                $employee->department_id
            )
            ->where(
                'designation_id',
                $employee->designation_id
            )
            ->exists();

        // Rectify User
        $isRectifyUser = Rectify::where(
                'designation_id',
                $employee->designation_id
            )
            ->exists();

        // Controller User
        // $isControllerUser = strtolower(trim($user->type ?? '')) === 'department admin';

        $roleType = null;

        if ($isRectifyUser) {
            $roleType = 'controller';
        } elseif ($isComplaintUser) {
            $roleType = 'complaint';
        }
         elseif ($employee) {
            $roleType = 'rectify';
        }


        return response()->json([
            'status' => true,

            // 'is_controller_user' => $isControllerUser,
            'is_controller_user' => $isRectifyUser,
            'is_complaint_user'  => $isComplaintUser,
            'is_rectify_user'    => $isRectifyUser? false : true,

            'role_type' => $roleType,

            'user_type'   => $user->type,
            'designation' => $employee->designation->name ?? null,
            'station_id'  => $employeeStation->station_id ?? null,

            'employee' => $employee
        ]);
    }
    


    // ✅ Logout
    public function logout(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        // Get current token
        $token = $request->user()->currentAccessToken();

        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => 'No active token found'
            ]);
        }

        // Store token info before delete
        $tokenId = $token->id;
        $tokenName = $token->name;

        // Delete current token
        $token->delete();

        return response()->json([
            'status' => true,
            'message' => 'Token deleted successfully',
            'deleted_token' => [
                'id' => $tokenId,
                'name' => $tokenName
            ]
        ]);
    }

    public function logoutAll(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        // Count tokens before deleting (optional)
        $tokenCount = $user->tokens()->count();

        // Delete all tokens
        $user->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out from all devices successfully',
            'deleted_tokens_count' => $tokenCount
        ]);
    }



    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $isControllerUser = strtolower($user->type) === 'department admin';

        $userId = $user->id;

        $employee = MasterEmployee::where('mobile', $user->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found'
            ]);
        }

        // 🔍 Role Check
        $isComplaintUser = Complaint::where('department_id', $employee->department_id)
            ->where('designation_id', $employee->designation_id)
            ->exists();

        // $isRectifyUser = Rectify::where('department_id', $employee->department_id)
        //     ->where('designation_id', $employee->designation_id)
        //     ->exists();

        $isRectifyUser = APIComplaint::where('assigned_employee_id', $employee->id)
            ->exists();



        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $lastMonth = Carbon::now()->subMonth();

        $result = [
            'role' => [
                'is_complaint_user' => $isComplaintUser,
                'is_rectify_user' => $isRectifyUser,
                'is_controller_user' => $isControllerUser
            ]
        ];

        // =====================================================
        // ✅ COMPLAINT DASHBOARD
        // =====================================================
        if ($isComplaintUser) {

            $result['complaint_dashboard'] = [
                'totalComplaints' => APIComplaint::where('created_by', $userId)->count(),

                'pendingComplaints' => APIComplaint::where('created_by', $userId)
                    ->where('status', 0)->count(),

                'rectifiedComplaints' => APIComplaint::where('created_by', $userId)
                    ->where('status', 1)->count(),

                'closedComplaints' => APIComplaint::where('created_by', $userId)
                    ->where('status', 3)->count(),

                'todayComplaints' => APIComplaint::where('created_by', $userId)
                    ->whereDate('created_at', $today)->count(),

                'tomorrowComplaints' => APIComplaint::where('created_by', $userId)
                    ->whereDate('timeline', $tomorrow)->count(),

                'lastMonthComplaints' => APIComplaint::where('created_by', $userId)
                    ->whereMonth('created_at', $lastMonth->month)
                    ->whereYear('created_at', $lastMonth->year)->count(),

                'delayedComplaints' => APIComplaint::where('created_by', $userId)
                    ->whereDate('timeline', '<', $today)
                    ->whereNotIn('status', [3])->count(),
            ];

            $result['reopen_dashboard'] = [

                'reopenedComplaints' => APIComplaint::where('reopened_by', $userId)->where('status', 2)->count(),

                'reopenedComplaints_and_closed' => APIComplaint::where('reopened_by', $userId)->where('status', 3)->count()

            ];
        }

        // =====================================================
        // ✅ RECTIFIER DASHBOARD
        // =====================================================
        if ($isRectifyUser) {

            $result['rectifier_dashboard'] = [

                'rectified_by' => APIComplaint::where('rectified_by', $userId)->where('status', 1)->count(),

                'rectified_and_closed' => APIComplaint::where('rectified_by', $userId)->where('status', 3)->count(),

                'rectified_and_reopen' =>  APIComplaint::where('rectified_by', $userId)->where('status', 2)->count()

            ];

            $result['reopen_dashboard'] = [

                'reopenedComplaints' => APIComplaint::where('reopened_by', $userId)->where('status', 2)->count(),

                'reopenedComplaints_and_closed' => APIComplaint::where('reopened_by', $userId)->where('status', 3)->count()

            ];
        }

        if ($isControllerUser) {

            $baseQuery = APIComplaint::where('department_id', $user->department_id);

            $result['controller_dashboard'] = [
                'totalComplaints'    => (clone $baseQuery)->count(),
                'pendingComplaints'  => (clone $baseQuery)->where('status', 0)->count(),
                'resolvedComplaints' => (clone $baseQuery)->where('status', 1)->count(),
                'reopenedComplaints' => (clone $baseQuery)->where('status', 2)->count(),
                'closedComplaints'   => (clone $baseQuery)->where('status', 3)->count(),
                'safetyComplaints'   => (clone $baseQuery)->where('is_safety_critical', 1)->count(),

                'todayComplaints' => (clone $baseQuery)
                    ->whereDate('created_at', $today)->count(),

                'yesterdayComplaints' => (clone $baseQuery)
                    ->whereDate('created_at', now()->subDay())->count(),

                'lastMonthComplaints' => (clone $baseQuery)
                    ->whereMonth('created_at', $lastMonth->month)
                    ->whereYear('created_at', $lastMonth->year)->count(),

                'delayComplaints' => (clone $baseQuery)
                    ->whereDate('timeline', '<', now())
                    ->whereNotIn('status', [1, 3])->count(),

                'recentComplaints' => (clone $baseQuery)
                    ->with(['department', 'station', 'amenityCategory'])
                    ->latest()
                    ->take(10)
                    ->get(),
            ];

            // // ✅ By Station
            // $result['controller_dashboard']['byStation'] = (clone $baseQuery)
            //     ->join('stations', 'api_complaints.station_id', '=', 'stations.id')
            //     ->selectRaw("
            //     stations.id,
            //     stations.name AS station_name,
            //     stations.code AS station_code,
            //     COUNT(*) AS total,
            //     SUM(api_complaints.status = 0) AS pending,
            //     SUM(api_complaints.status = 1) AS rectified,
            //     SUM(api_complaints.status = 2) AS reopened,
            //     SUM(api_complaints.status = 3) AS closed
            // ")
            //     ->groupBy('stations.id', 'stations.name', 'stations.code')
            //     ->orderByDesc('total')
            //     ->take(6)
            //     ->get();

            // // ✅ Top Reporters
            // $result['controller_dashboard']['topReportersByStation'] = (clone $baseQuery)
            //     ->selectRaw("
            //     station_id,
            //     created_by,
            //     COUNT(*) AS report_count
            // ")
            //     ->whereIn('station_id', $result['controller_dashboard']['byStation']->pluck('id'))
            //     ->groupBy('station_id', 'created_by')
            //     ->orderByDesc('report_count')
            //     ->get()
            //     ->groupBy('station_id')
            //     ->map(fn($rows) => $rows->take(3));
        }






        return response()->json([
            'status' => true,
            'data' => $result
        ]);
    }



    public function newDashboard(Request $request)
    {
        $userData = Auth::user();

        // Authenticated employee fetch karo
        $employeeData = MasterEmployee::where('mobile', $userData->phone_number)->first();

        if (!$employeeData) {
            return response()->json([
                'status' => false,
                'message' => 'Employee not found',
                'completion' => 0,
                'data' => []
            ], 404);
        }
        // Agar request me employee id aa raha hai to use karo
        $id = $employeeData->id;

        

        $today = Carbon::today();

        // Complaints, rectified, load entries, etc. ka count
        $complaint       = APIComplaint::whereDate('created_at', $today)->count();
        $rectified       = APIComplaint::whereDate('rectified_at', $today)->count();
        $loadEntry       = LoadStabling::whereDate('created_at', $today)->count();
        $clearanceEntry  = LoadStablingClearance::whereDate('created_at', $today)->count();
        $ambush          = AmbushInspection::whereDate('created_at', $today)->count();
        $staffCounseling = StaffCounseling::whereDate('created_at', $today)->count();

        // PME Due In calculation

            $pme_due = EmployeePme::where('master_employee_id', $id)
                ->select([
                    'id',
                    'master_employee_id',
                    'last_pme_date',
                    'certificate_no',
                    'next_due_date',
                    'status',
                ])
                ->latest('created_at')   // ✅ last entry ke liye
                ->first();               // ✅ sirf ek record

            if ($pme_due) {
                $pme_due = [
                    'pme_due_in_days'   => $pme_due->next_due_date 
                        ? Carbon::parse($pme_due->next_due_date)->diffInDays(Carbon::today(), false)
                        : null,
                ];
            }

            $refresher_due = EmployeeRefresherCourse::where('master_employee_id', $id)
                ->select([
                    'id',
                    'master_employee_id',
                    'last_refresher_date',
                    'certificate_no',
                    'institution_name',
                    'next_due_date',
                    'status',
                ])
                ->latest('created_at')   // ✅ last entry only
                ->first();

            if ($refresher_due) {
                $refresher_due = [
                'refresher_due_in_days' => $refresher_due->next_due_date
                        ? Carbon::parse($refresher_due->next_due_date)->diffInDays($today, false)
                        : null,
                ];
            }

            $grade = EmployeeGradation::where('master_employee_id', $id)
                ->select(['grade'])
                ->latest('created_at')   // last entry only
                ->first();

        return response()->json([
            'status'          => true,
            'data' => [
            'complaint'       => $complaint,
            'rectified'       => $rectified,
            'Load_stabling'   => $loadEntry,
            'Load_clearance'  => $clearanceEntry,
            'ambush'          => $ambush,
            'staff_counseling'=> $staffCounseling,
            'pme_due_in'      => $pme_due,
            'refresher_due'   => $refresher_due,
            'your_grade'      => $grade,
            ],
        ]);
    }


}
