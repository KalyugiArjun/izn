<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MasterEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $user = User::find(1);
        \App::setLocale($user ? $user->lang : 'english');
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request (traditional email link).
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        try {
            smtpDetail(1);
            $status = Password::sendResetLink(
                $request->only('email')
            );
            return $status == Password::RESET_LINK_SENT
                ? back()->with('status', __($status))
                : back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
        } catch (\Exception $e) {
            return redirect()->route('password.request')->with('error', 'We noticed that the email settings have not been configured for this system. Please check your SMTP configuration.');
        }
    }

    /**
     * Send 6-digit OTP code to the registered email found by mobile number.
     * Captcha is removed as per requirement.
     */
    public function sendOtp(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'phone_number' => 'required|string|min:10|max:15'
        ], [
            'phone_number.required' => __('Please enter your registered mobile number.'),
            'phone_number.min' => __('Please enter a valid 10-digit mobile number.'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        // Clean phone number (keep digits only)
        $rawPhone = trim($request->phone_number);
        $phone = preg_replace('/[^0-9]/', '', $rawPhone);
        if (strlen($phone) > 10 && str_starts_with($phone, '91')) {
            $phone = substr($phone, 2);
        }

        if (strlen($phone) < 10) {
            return response()->json([
                'status' => false,
                'message' => __('Please enter a valid 10-digit mobile number.')
            ]);
        }

        // 1. Find user by phone_number
        $user = User::where('phone_number', $phone)
            ->orWhere('phone_number', '+91' . $phone)
            ->orWhere('phone_number', '91' . $phone)
            ->first();

        // 2. If not found in users table, lookup in master_employees by mobile
        if (!$user) {
            $employee = MasterEmployee::where('mobile', $phone)
                ->orWhere('mobile', '+91' . $phone)
                ->orWhere('mobile', '91' . $phone)
                ->first();

            if ($employee) {
                // Check if user exists with employee's email or mobile
                $user = User::where('phone_number', $employee->mobile)
                    ->orWhere('email', $employee->email)
                    ->first();

                // If user doesn't exist yet but employee is registered with email, create user record
                if (!$user && !empty($employee->email)) {
                    $user = User::create([
                        'name' => $employee->name ?? 'Employee',
                        'email' => $employee->email,
                        'phone_number' => $employee->mobile ?? $phone,
                        'password' => Hash::make(uniqid('saathi_', true)),
                        'type' => 'emp',
                        'department_id' => $employee->department_id,
                        'designation' => $employee->designation_id,
                        'is_active' => 1,
                        'lang' => 'english',
                    ]);
                }
            }
        }

        // If still no user or user has no email address
        if (!$user || empty($user->email)) {
            return response()->json([
                'status' => false,
                'message' => __('No registered user or email address found for mobile number :phone.', ['phone' => $rawPhone])
            ]);
        }

        // Generate 6-digit numeric OTP
        $otp = (string) mt_rand(100000, 999999);

        // Store OTP, user ID, email, and phone in session with 15-minute expiration
        session([
            'password_reset_user_id'    => $user->id,
            'password_reset_email'      => $user->email,
            'password_reset_phone'      => $phone,
            'password_reset_otp'        => $otp,
            'password_reset_expires_at' => now()->addMinutes(15),
            'password_otp_verified'     => false,
        ]);

        // Send OTP email
        try {
            smtpDetail(1);
            $userName = $user->name ?? 'User';
            $userEmail = $user->email;

            Mail::raw(
                "Dear {$userName},\n\nYour SAATHI Password Reset OTP code is: {$otp}\n\nThis OTP is valid for 15 minutes.\n\nIf you did not request a password reset, please ignore this email or contact support immediately.\n\nWarm Regards,\nSAATHI - Izzatnagar Division\nNorth Eastern Railway",
                function ($message) use ($userEmail) {
                    $message->to($userEmail)
                        ->subject('Password Reset OTP - SAATHI');
                }
            );

            return response()->json([
                'status'  => true,
                'email'   => $user->email,
                'message' => __('OTP sent successfully to your registered email address.')
            ]);
        } catch (\Exception $e) {
            Log::error('Password Reset OTP Mail Exception: ' . $e->getMessage());

            // If app is in debug mode (local development without active SMTP connectivity), provide fallback
            if (config('app.debug')) {
                return response()->json([
                    'status'  => true,
                    'email'   => $user->email,
                    'message' => __('OTP sent successfully to your registered email! (Local Test OTP: ') . $otp . ')',
                    'dev_otp' => $otp
                ]);
            }

            return response()->json([
                'status'  => false,
                'message' => __('Failed to send OTP email. Please ensure SMTP configuration is correct.')
            ]);
        }
    }

    /**
     * Verify the 6-digit OTP code (Step 2).
     */
    public function verifyOtp(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'otp' => 'required|numeric|digits:6',
        ], [
            'otp.required' => __('Please enter the complete 6-digit OTP code.'),
            'otp.digits'   => __('OTP code must be exactly 6 digits.'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $savedOtp    = session('password_reset_otp');
        $expiresAt   = session('password_reset_expires_at');

        if (!$savedOtp || !$expiresAt || now()->greaterThan($expiresAt)) {
            return response()->json([
                'status'  => false,
                'message' => __('The OTP code has expired or is invalid. Please request a new code.')
            ]);
        }

        if ((string) $request->otp !== (string) $savedOtp) {
            return response()->json([
                'status'  => false,
                'message' => __('Incorrect OTP code. Please enter the valid 6-digit code received.')
            ]);
        }

        // Mark OTP as verified in session
        session([
            'password_otp_verified' => true,
        ]);

        return response()->json([
            'status'  => true,
            'message' => __('OTP verified successfully! Now please create your new password.')
        ]);
    }

    /**
     * Reset and save new password after OTP verification (Step 3).
     */
    public function resetPassword(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'password'              => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string'
        ], [
            'password.required'  => __('Please enter a new password.'),
            'password.min'       => __('New password must be at least 6 characters long.'),
            'password.confirmed' => __('New password and confirm password do not match.'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $isVerified  = session('password_otp_verified');
        $expiresAt   = session('password_reset_expires_at');
        $savedUserId = session('password_reset_user_id');
        $savedEmail  = session('password_reset_email');

        if (!$isVerified || !$expiresAt || now()->greaterThan($expiresAt)) {
            return response()->json([
                'status'  => false,
                'message' => __('Verification session expired or OTP not verified. Please start again.')
            ]);
        }

        // Locate user to update password
        $user = null;
        if ($savedUserId) {
            $user = User::find($savedUserId);
        }
        if (!$user && $savedEmail) {
            $user = User::where('email', $savedEmail)->first();
        }

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => __('User account not found. Please try again.')
            ]);
        }

        // Hash and save new password
        $user->password = Hash::make($request->password);
        $user->save();

        // Clear session data
        session()->forget([
            'password_reset_user_id',
            'password_reset_email',
            'password_reset_phone',
            'password_reset_otp',
            'password_reset_expires_at',
            'password_otp_verified'
        ]);

        return response()->json([
            'status'  => true,
            'message' => __('Your password has been reset successfully! Redirecting to login...')
        ]);
    }
}
