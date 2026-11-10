<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\LoggedHistory;
use App\Models\User;
use App\Models\MasterEmployee;
use App\Services\CaptchaService;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        if (!file_exists(setup())) {
            header('location:install');
            die;
        }

        $user = \App\Models\User::find(1);
        \App::setLocale($user ? $user->lang : 'english');

        return view('auth.login');
    }

    public function refreshCaptcha()
    {
        $svg = CaptchaService::generate();
        return response()->json([
            'status' => true,
            'captcha_svg' => $svg,
        ]);
    }

    public function store(LoginRequest $request)
    {
        $request->validate([
            'captcha' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!CaptchaService::validate($value)) {
                    $fail(__('Invalid captcha code. Please enter the correct code shown.'));
                }
            }],
        ]);

        $google_recaptcha = getSettingsValByName('google_recaptcha');
        if ($google_recaptcha == 'on') {
            $validation['g-recaptcha-response'] = 'required|captcha';
        } else {
            $validation = [];
        }
        if (!empty($validation)) {
            $this->validate($request, $validation);
        }

        $request->authenticate();
        if ($request->hasSession()) { $request->session()->regenerate(); }
        $loginUser = Auth::user();
        if ($loginUser->is_active == 0) {
            auth()->logout();
            return redirect()->route('login')->with('error', __('Your account is temporarily inactive. Please contact your administrator to reactivate your account.'));
        }
        if ($loginUser->type == 'owner' && empty($loginUser->email_verified_at)) {
            auth()->logout();
            return redirect()->route('login')->with('error', __('Verification required: Please check your email to verify your account before continuing.'));
        }
        if ($loginUser->type == 'owner') {
            if ($loginUser->subscription_expire_date != null && date('Y-m-d') > $loginUser->subscription_expire_date) {
                assignSubscription(1);
                return redirect()->intended(RouteServiceProvider::HOME)->with('error', __('Your subscription has ended, and access to premium features is now restricted. To continue using our services without interruption, please renew your plan or upgrade to a higher-tier package.'));
            }
        }
        userLoggedHistory();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Check phone number in master_employees and users table for dynamic Smart Auth Wizard.
     */
    public function checkPhoneWeb(Request $request)
    {
        try {
            $request->validate([
                'phone_number' => 'required|digits:10',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => __('Invalid phone number. Please enter a valid 10-digit number.'),
            ]);
        }

        // Step 1: Check if user exists and is an admin/owner (not employee)
        $user = User::where('phone_number', $request->phone_number)->first();
        if ($user && !in_array(strtolower($user->type), ['emp', 'employee'])) {
            return response()->json([
                'status' => true,
                'exists' => true,
                'phone_number' => $request->phone_number,
                'employee_name' => $user->name,
                'message' => __('User exists. Please enter password.')
            ]);
        }

        // Step 2: Check in master_employees
        $employee = MasterEmployee::where('mobile', $request->phone_number)->first();

        if (!$employee) {
            return response()->json([
                'status' => false,
                'message' => __('This phone number is not registered by admin.'),
            ]);
        }

        // Check if employee is inactive
        if ($employee->status == 0) {
            return response()->json([
                'status' => false,
                'message' => __('Employee status is inactive. Contact admin.'),
            ]);
        }

        if ($user) {
            return response()->json([
                'status' => true,
                'exists' => true,
                'phone_number' => $request->phone_number,
                'employee_name' => $employee->name,
                'message' => __('User exists. Please enter password.')
            ]);
        } else {
            return response()->json([
                'status' => true,
                'exists' => false,
                'phone_number' => $request->phone_number,
                'employee_name' => $employee->name,
                'message' => __('Create your password.')
            ]);
        }
    }

    /**
     * Handle dynamic login or registration from the smart auth wizard.
     */
    public function loginOrRegisterWeb(Request $request)
    {
        if (!CaptchaService::validate($request->captcha)) {
            return response()->json([
                'status' => false,
                'captcha_invalid' => true,
                'message' => __('Invalid captcha code. Please enter the correct code shown.'),
            ]);
        }

        try {
            $request->validate([
                'phone_number' => 'required|digits:10',
                'action_type' => 'required|in:login,register',
                'password' => 'required|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => __('Validation error. Please verify input fields.'),
            ]);
        }

        $phone_number = $request->phone_number;
        $password = $request->password;
        $action_type = $request->action_type;

        if ($action_type === 'register') {
            try {
                $request->validate([
                    'password' => 'required|confirmed|min:6',
                ]);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'status' => false,
                    'message' => __('Passwords do not match or are too short (minimum 6 characters).'),
                ]);
            }

            // Check if user already exists
            $user = User::where('phone_number', $phone_number)->first();
            if ($user) {
                return response()->json([
                    'status' => false,
                    'need_register' => false,
                    'message' => __('This mobile number is already registered. Please login.')
                ]);
            }

            // Find or create employee in master_employees
            $employee = MasterEmployee::where('mobile', $phone_number)->first();
            if ($employee && $employee->status == 0) {
                return response()->json([
                    'status' => false,
                    'message' => __('Employee is inactive in records. Contact admin.')
                ]);
            }

            if (!$employee) {
                $employee = MasterEmployee::create([
                    'name' => 'Railway Employee (' . substr($phone_number, -4) . ')',
                    'mobile' => $phone_number,
                    'email' => $phone_number . '@izn.com',
                    'status' => 1,
                ]);
            }

            // Create user with employee data
            $user = User::create([
                'name' => $employee->name,
                'email' => $employee->email ?: ($employee->mobile . '@izn.com'),
                'phone_number' => $employee->mobile,
                'password' => Hash::make($password),
                'type' => 'emp',
                'profile' => 'avatar.png',
                'lang' => 'english',
                'subscription' => 1,
                'parent_id' => 1,
                'is_active' => 1,
                'email_verified_at' => now(),
                'department_id' => $employee->department_id,
                'designation' => $employee->designation_id,
            ]);

            // Log the user in
            Auth::login($user, $request->boolean('remember', true));
            if ($request->hasSession()) { $request->session()->regenerate(); }

            // Device details update
            $user->update([
                'ip_address'    => $request->ip(),
                'last_login_at' => now()->setTimezone('Asia/Kolkata'),
            ]);

            userLoggedHistory();

            return response()->json([
                'status' => true,
                'message' => __('Registered and logged in successfully! Opening dashboard...'),
                'redirect' => route('home')
            ]);
        } else {
            // Login flow
            $user = User::where('phone_number', $phone_number)->first();

            // If user is not found in users table, prompt for registration
            if (!$user) {
                $employee = MasterEmployee::where('mobile', $phone_number)->first();
                if ($employee) {
                    return response()->json([
                        'status' => false,
                        'need_register' => true,
                        'exists_in_master' => true,
                        'employee_name' => $employee->name,
                        'message' => __('User not found in database. Welcome :name! Please set & confirm password to register.', ['name' => $employee->name])
                    ]);
                }

                return response()->json([
                    'status' => false,
                    'need_register' => true,
                    'exists_in_master' => false,
                    'message' => __('User not found in database. Please register your account to continue.')
                ]);
            }

            if ($user->is_active == 0) {
                return response()->json([
                    'status' => false,
                    'need_register' => false,
                    'message' => __('Your account is inactive. Please contact administrator.')
                ]);
            }

            // Only verify master_employees status if user is an employee (type == 'emp')
            if ($user->type === 'emp') {
                $employee = MasterEmployee::where('mobile', $phone_number)->first();
                if ($employee && $employee->status == 0) {
                    return response()->json([
                        'status' => false,
                        'need_register' => false,
                        'message' => __('Employee record is marked inactive.')
                    ]);
                }
            }

            if (!Auth::attempt([
                'phone_number' => $phone_number,
                'password' => $password
            ], $request->boolean('remember', true))) {
                return response()->json([
                    'status' => false,
                    'need_register' => false,
                    'message' => __('Invalid credentials. Password does not match.')
                ]);
            }

            $user = Auth::user();
            if ($request->hasSession()) { $request->session()->regenerate(); }

            // Device details update
            $user->update([
                'ip_address'    => $request->ip(),
                'last_login_at' => now()->setTimezone('Asia/Kolkata'),
            ]);

            userLoggedHistory();

            return response()->json([
                'status' => true,
                'message' => __('Login successful! Opening dashboard...'),
                'redirect' => route('home')
            ]);
        }
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/home');
    }
}
