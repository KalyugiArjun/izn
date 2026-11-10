<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CaptchaService;
use App\Models\MasterEmployee;
use App\Models\Page;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function create()
    {
        $user = User::find(1);
        \App::setLocale($user ? $user->lang : 'english');
        $menu = Page::where('slug', 'terms_conditions')->first();
        return view('auth.register', compact('menu'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {

        // dd('rr');

        $google_recaptcha = getSettingsValByName('google_recaptcha');
        if ($google_recaptcha == 'on') {
            $validation['g-recaptcha-response'] = 'required|captcha';
        } else {
            $validation = [];
        }
        if (!empty($validation)) {
            $this->validate($request, $validation);
        }

        $request->validate([
            'captcha' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!CaptchaService::validate($value)) {
                    $fail(__('Invalid captcha code. Please enter the correct code shown.'));
                }
            }],
            'phone_number' => ['required', 'string', 'max:15'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Find employee in master_employees
        $employee = MasterEmployee::where('mobile', $request->phone_number)->first();

        if (!$employee) {
            return redirect()->back()->withInput()->with('error', __('Employee not found. Contact admin.'));
        }

        // Check if user already exists
        $existingUser = User::where('phone_number', $request->phone_number)->first();
        if ($existingUser) {
            return redirect()->route('login')->with('error', __('This mobile number is already registered. Please login.'));
        }

        // Create user with employee data
        $user = User::create([
            'name' => $employee->name,
            'email' => $employee->email ?: ($employee->mobile . '@izn.com'),
            'phone_number' => $employee->mobile,
            'password' => Hash::make($request->password),
            'type' => 'emp', // Set employee type
            'profile' => 'avatar.png', // Default image
            'lang' => 'english',
            'subscription' => 1,
            'parent_id' => 1,
            'is_active' => 1,
            'email_verified_at' => now(),
            'department_id' => $employee->department_id,
            'designation' => $employee->designation_id,
        ]);

        return redirect()->route('login')->with('success', __('Registered successfully! Please login using your mobile number and password.'));
    }
}
