@extends('layouts.auth')

@section('tab-title')
    {{ __('Forgot / Reset Password') }}
@endsection

@php
    $settings = settings();
@endphp

@push('style-page')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        /* ========================================
           LOGIN / RESET CONTAINER
        ======================================== */
        .login-container {
            width: 100%;
            margin: 0 auto;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(12px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* ========================================
           LOGO SECTION
        ======================================== */
        .login-logo {
            text-align: center;
            margin-bottom: 8px;
        }

        .login-logo .logo-circle {
            width: 48px;
            height: 48px;
            margin: 0 auto 6px;
            border-radius: 50%;
            border: 1.5px solid #d8e2f0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            color: #1b56d6;
            font-size: 22px;
            box-shadow: 0 4px 12px rgba(27, 86, 214, 0.08);
        }

        .login-logo .brand-name {
            display: block;
            font-size: 17px;
            font-weight: 800;
            color: #14224a;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }

        .login-logo .brand-sub {
            display: block;
            font-size: 8.5px;
            color: #1b56d6;
            font-weight: 700;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            margin-top: 3px;
            line-height: 1.4;
            max-width: 270px;
            margin-left: auto;
            margin-right: auto;
        }

        .login-logo hr {
            width: 100%;
            max-width: 260px;
            border: none;
            border-top: 1.5px solid #e3e9f5;
            margin: 10px auto 0;
        }

        /* ========================================
           STEP INDICATOR (3 STEPS)
        ======================================== */
        .step-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 12px 0 6px;
        }

        .step-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #e2e8f0;
            transition: all 0.3s ease;
        }

        .step-dot.active {
            background: #1b56d6;
            width: 24px;
            border-radius: 4px;
        }

        .step-dot.done {
            background: #16a34a;
        }

        /* ========================================
           WELCOME SECTION
        ======================================== */
        .welcome-section {
            text-align: center;
            margin: 8px 0 14px;
        }

        .welcome-section h2 {
            font-size: 19px;
            font-weight: 800;
            color: #14224a;
            margin: 0 0 4px;
            letter-spacing: -0.3px;
        }

        .welcome-section p {
            color: #64748b;
            font-size: 12px;
            margin: 0;
            font-weight: 500;
            line-height: 1.4;
        }

        /* ========================================
           CLEAN INPUT DESIGN
        ======================================== */
        .form-group {
            margin-bottom: 14px;
            position: relative;
        }

        .form-group label {
            display: block;
            font-size: 10.5px;
            font-weight: 700;
            color: #1b56d6;
            margin-bottom: 6px;
            letter-spacing: 0.7px;
            text-transform: uppercase;
        }

        .login-container .form-group .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            border-radius: 8px !important;
            background: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            box-shadow: none !important;
            outline: none !important;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .login-container .form-group .input-wrapper:focus-within {
            border-color: #1b56d6 !important;
            box-shadow: 0 0 0 3px rgba(27, 86, 214, 0.12) !important;
        }

        .login-container .form-group .input-wrapper .input-icon {
            position: absolute;
            left: 14px;
            color: #1b56d6;
            font-size: 16px;
            z-index: 2;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container .form-group .input-wrapper .form-control {
            width: 100% !important;
            height: 42px !important;
            padding: 0 40px 0 42px !important;
            border: 0 !important;
            border-style: none !important;
            border-width: 0 !important;
            border-radius: 0 !important;
            outline: none !important;
            box-shadow: none !important;
            background: transparent !important;
            color: #0f172a !important;
            font-size: 14px !important;
            font-weight: 600 !important;
        }

        .login-container .form-group .input-wrapper .form-control:focus,
        .login-container .form-group .input-wrapper .form-control:active {
            border: 0 !important;
            border-style: none !important;
            border-width: 0 !important;
            outline: none !important;
            box-shadow: none !important;
            background: transparent !important;
        }

        .form-group .input-wrapper .form-control::placeholder {
            color: #94a3b8 !important;
            font-weight: 400 !important;
        }

        .password-toggle-btn {
            position: absolute;
            right: 12px;
            background: transparent;
            border: none;
            color: #94a3b8;
            font-size: 16px;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 3;
            transition: color 0.2s;
        }

        .password-toggle-btn:hover {
            color: #1b56d6;
        }

        /* ========================================
           OTP DIGIT BOXES
        ======================================== */
        .otp-boxes {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-top: 6px;
            margin-bottom: 6px;
        }

        .otp-box {
            width: 42px;
            height: 46px;
            border: 1.5px solid #cbd5e1 !important;
            border-radius: 8px !important;
            text-align: center;
            font-size: 19px;
            font-weight: 700;
            color: #0f172a;
            background: #ffffff !important;
            outline: none !important;
            box-shadow: none !important;
            transition: all 0.2s ease;
            caret-color: #1b56d6;
        }

        .otp-box:focus {
            border-color: #1b56d6 !important;
            box-shadow: 0 0 0 3px rgba(27, 86, 214, 0.12) !important;
            background: #f8fafc !important;
        }

        .otp-box.filled {
            background: #eff6ff !important;
            border-color: #1b56d6 !important;
            color: #1b56d6;
        }

        /* ========================================
           ALERTS
        ======================================== */
        .alert {
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 12.5px;
            margin-bottom: 14px;
            border: none;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            animation: slideAlert 0.3s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes slideAlert {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-danger {
            background: #fef2f2;
            color: #b91c1c;
            border-left: 4px solid #dc2626;
        }

        .alert-success {
            background: #f0fdf4;
            color: #15803d;
            border-left: 4px solid #16a34a;
        }

        /* ========================================
           ACTION BUTTONS
        ======================================== */
        .reset-btn {
            width: 100%;
            height: 44px;
            background: #1b56d6;
            border: none;
            border-radius: 8px;
            color: #ffffff;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.25s ease;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            box-shadow: 0 6px 18px rgba(27, 86, 214, 0.25);
            margin-top: 10px;
        }

        .reset-btn:hover {
            background: #143f9e;
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(27, 86, 214, 0.32);
        }

        .reset-btn:active {
            transform: scale(0.98);
        }

        .reset-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .step-panel {
            transition: opacity 0.25s ease, transform 0.25s ease;
        }

        /* ========================================
           BACK LINK
        ======================================== */
        .back-link {
            text-align: center;
            margin-top: 14px;
            padding-bottom: 2px;
        }

        .back-link p {
            color: #64748b;
            font-size: 12px;
            margin: 0;
            font-weight: 500;
        }

        .back-link a {
            color: #1b56d6;
            font-weight: 700;
            text-decoration: none;
            transition: color 0.2s;
        }

        .back-link a:hover {
            color: #14224a;
            text-decoration: underline;
        }
    </style>
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="login-container">

                <!-- ===== LOGO SECTION ===== -->
                <div class="login-logo">
                    <div class="logo-circle" title="{{ __('SAATHI') }}">
                        <i class="ti ti-shield-lock" style="font-size: 22px; color: #1b56d6;"></i>
                    </div>
                    <span class="brand-name">{{ env('APP_NAME') }}</span>
                    <span class="brand-sub">{{ __('SAATHI Station Asset Attention, Tracking, Help & Improvement') }}</span>
                    <hr>
                </div>

                <!-- ===== STEP INDICATOR (3 CLEAN STEPS) ===== -->
                <div class="step-indicator" id="stepIndicator">
                    <div class="step-dot active" id="dot1" title="{{ __('Step 1: Mobile Number') }}"></div>
                    <div class="step-dot" id="dot2" title="{{ __('Step 2: Verify OTP') }}"></div>
                    <div class="step-dot" id="dot3" title="{{ __('Step 3: New Password') }}"></div>
                </div>

                <!-- ========================================================
                     SCREEN 1 (STEP 1): ENTER MOBILE NUMBER
                     ======================================================== -->
                <div id="step-1-panel" class="step-panel">
                    <div class="welcome-section">
                        <h2>{{ __('Forgot Password?') }}</h2>
                        <p>{{ __('Enter your registered mobile number to receive a 6-digit OTP code.') }}</p>
                    </div>

                    <div id="alert-wrapper-step1"></div>

                    <form id="step1Form" autocomplete="off">
                        @csrf
                        <div class="form-group">
                            <label for="phone_number">{{ __('Registered Mobile Number') }}</label>
                            <div class="input-wrapper">
                                <span class="input-icon"><i class="ti ti-phone"></i></span>
                                <input type="tel" class="form-control" id="phone_number" name="phone_number"
                                    placeholder="{{ __('Enter 10-digit mobile number') }}" 
                                    maxlength="10" inputmode="numeric" pattern="[0-9]{10}" required autofocus />
                            </div>
                        </div>

                        <button type="submit" class="reset-btn" id="step1Btn">
                            <span id="step1BtnText">{{ __('Send OTP Code') }}</span>
                            <i class="ti ti-arrow-right" id="step1BtnIcon"></i>
                        </button>
                    </form>

                    <div class="back-link">
                        <p>{{ __('Remember your password?') }} <a href="{{ route('login') }}">{{ __('Log In') }}</a></p>
                    </div>
                </div>


                <!-- ========================================================
                     SCREEN 2 (STEP 2): ENTER & VERIFY OTP ONLY
                     ======================================================== -->
                <div id="step-2-panel" class="step-panel" style="display: none;">
                    <div class="welcome-section">
                        <h2>{{ __('Verify OTP Code') }}</h2>
                        <p>{{ __('Enter the 6-digit verification code sent to your email.') }}</p>
                    </div>

                    <div id="alert-wrapper-step2"></div>

                    <!-- Verified Account Info Banner with "Change" option -->
                    <div class="d-flex align-items-center justify-content-between p-2 px-3 mb-3" style="background: #f1f5f9; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <div class="small text-truncate" style="color: #334155;">
                            <i class="ti ti-mail text-primary me-1"></i>
                            <strong>{{ __('OTP Sent:') }}</strong> <span id="targetEmailText" class="fw-semibold text-dark">-</span>
                        </div>
                        <a href="javascript:void(0);" id="changeNumberBtn" class="small text-decoration-none fw-bold" style="color: #1b56d6; font-size: 11px;">
                            {{ __('Change') }}
                        </a>
                    </div>

                    <form id="step2Form" autocomplete="off">
                        @csrf

                        <!-- 6-Digit OTP Boxes -->
                        <div class="form-group mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <label class="mb-0">{{ __('Enter 6-Digit OTP Code') }}</label>
                                <a href="javascript:void(0);" id="resendOtpBtn" class="small text-decoration-none fw-semibold" style="color: #1b56d6; font-size: 11px;">
                                    <i class="ti ti-refresh me-1"></i>{{ __('Resend OTP') }}
                                </a>
                            </div>
                            <div class="otp-boxes">
                                <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="otp_1" autocomplete="one-time-code">
                                <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="otp_2">
                                <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="otp_3">
                                <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="otp_4">
                                <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="otp_5">
                                <input class="otp-box" type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" id="otp_6">
                            </div>
                            <input type="hidden" id="otp" name="otp">
                        </div>

                        <!-- Verify OTP Button -->
                        <button type="submit" class="reset-btn" id="step2Btn">
                            <span id="step2BtnText">{{ __('Verify OTP Code') }}</span>
                            <i class="ti ti-check" id="step2BtnIcon"></i>
                        </button>
                    </form>

                    <div class="back-link">
                        <p><a href="javascript:void(0);" id="backToStep1Link"><i class="ti ti-arrow-left me-1"></i>{{ __('Change Mobile Number') }}</a></p>
                    </div>
                </div>


                <!-- ========================================================
                     SCREEN 3 (STEP 3): SET NEW PASSWORD & CONFIRM PASSWORD
                     ======================================================== -->
                <div id="step-3-panel" class="step-panel" style="display: none;">
                    <div class="welcome-section">
                        <h2>{{ __('Set New Password') }}</h2>
                        <p>{{ __('Create a strong new password for your account.') }}</p>
                    </div>

                    <div id="alert-wrapper-step3"></div>

                    <form id="step3Form" autocomplete="off">
                        @csrf

                        <!-- New Password -->
                        <div class="form-group">
                            <label for="password">{{ __('New Password') }}</label>
                            <div class="input-wrapper">
                                <span class="input-icon"><i class="ti ti-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="{{ __('Enter new password (min 6 characters)') }}" autocomplete="new-password" required />
                                <button type="button" class="password-toggle-btn" data-target="password" tabindex="-1">
                                    <i class="ti ti-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Confirm Password (with lock icon) -->
                        <div class="form-group">
                            <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                            <div class="input-wrapper">
                                <span class="input-icon"><i class="ti ti-lock"></i></span>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" placeholder="{{ __('Confirm your new password') }}" autocomplete="new-password" required />
                                <button type="button" class="password-toggle-btn" data-target="password_confirmation" tabindex="-1">
                                    <i class="ti ti-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Reset Password Button -->
                        <button type="submit" class="reset-btn" id="step3Btn">
                            <span id="step3BtnText">{{ __('Reset Password') }}</span>
                            <i class="ti ti-shield-check" id="step3BtnIcon"></i>
                        </button>
                    </form>

                    <div class="back-link">
                        <p>{{ __('Back to') }} <a href="{{ route('login') }}">{{ __('Log In') }}</a></p>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('script-page')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const step1Panel        = document.getElementById('step-1-panel');
    const step2Panel        = document.getElementById('step-2-panel');
    const step3Panel        = document.getElementById('step-3-panel');
    const step1Form         = document.getElementById('step1Form');
    const step2Form         = document.getElementById('step2Form');
    const step3Form         = document.getElementById('step3Form');
    const phoneInput        = document.getElementById('phone_number');
    const targetEmailText   = document.getElementById('targetEmailText');
    const step1Btn          = document.getElementById('step1Btn');
    const step1BtnText      = document.getElementById('step1BtnText');
    const step1BtnIcon      = document.getElementById('step1BtnIcon');
    const step2Btn          = document.getElementById('step2Btn');
    const step2BtnText      = document.getElementById('step2BtnText');
    const step2BtnIcon      = document.getElementById('step2BtnIcon');
    const step3Btn          = document.getElementById('step3Btn');
    const step3BtnText      = document.getElementById('step3BtnText');
    const step3BtnIcon      = document.getElementById('step3BtnIcon');
    const alertWrapper1     = document.getElementById('alert-wrapper-step1');
    const alertWrapper2     = document.getElementById('alert-wrapper-step2');
    const alertWrapper3     = document.getElementById('alert-wrapper-step3');
    const dot1              = document.getElementById('dot1');
    const dot2              = document.getElementById('dot2');
    const dot3              = document.getElementById('dot3');
    const resendOtpBtn      = document.getElementById('resendOtpBtn');
    const changeNumberBtn   = document.getElementById('changeNumberBtn');
    const backToStep1Link   = document.getElementById('backToStep1Link');
    const otpHiddenInput    = document.getElementById('otp');
    const otpBoxes          = document.querySelectorAll('.otp-box');
    const passwordInput     = document.getElementById('password');
    const confirmInput      = document.getElementById('password_confirmation');

    let currentEmail = '';

    // Phone input: only digits up to 10
    phoneInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
    });

    // Password visibility toggle buttons
    document.querySelectorAll('.password-toggle-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'ti ti-eye-off';
            } else {
                input.type = 'password';
                icon.className = 'ti ti-eye';
            }
        });
    });

    // OTP Boxes keyboard and paste handling
    otpBoxes.forEach((box, idx) => {
        box.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(-1);
            if (this.value) {
                this.classList.add('filled');
                if (idx < otpBoxes.length - 1) otpBoxes[idx + 1].focus();
            } else {
                this.classList.remove('filled');
            }
            otpHiddenInput.value = Array.from(otpBoxes).map(b => b.value).join('');
        });

        box.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value && idx > 0) {
                otpBoxes[idx - 1].focus();
                otpBoxes[idx - 1].value = '';
                otpBoxes[idx - 1].classList.remove('filled');
            }
        });

        box.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
            paste.split('').forEach((char, i) => {
                if (otpBoxes[i]) {
                    otpBoxes[i].value = char;
                    otpBoxes[i].classList.add('filled');
                }
            });
            otpHiddenInput.value = Array.from(otpBoxes).map(b => b.value).join('');
            if (otpBoxes[paste.length - 1]) otpBoxes[paste.length - 1].focus();
        });
    });

    function setStepIndicator(step) {
        [dot1, dot2, dot3].forEach(d => d.classList.remove('active', 'done'));
        if (step === 1) {
            dot1.classList.add('active');
        } else if (step === 2) {
            dot1.classList.add('done');
            dot2.classList.add('active');
        } else if (step === 3) {
            dot1.classList.add('done');
            dot2.classList.add('done');
            dot3.classList.add('active');
        }
    }

    function showAlert(wrapper, message, type) {
        wrapper.innerHTML = `
            <div class="alert alert-${type}" role="alert">
                <span class="fs-5">${type === 'success' ? '✅' : '⚠️'}</span>
                <div>${message}</div>
            </div>
        `;
    }

    // Switch to Screen 2 (OTP Screen)
    function goToStep2(email) {
        currentEmail = email;
        targetEmailText.textContent = email;

        step1Panel.style.opacity = '0';
        setTimeout(() => {
            step1Panel.style.display = 'none';
            step3Panel.style.display = 'none';
            step2Panel.style.display = 'block';
            step2Panel.style.opacity = '1';
            setStepIndicator(2);
            alertWrapper2.innerHTML = '';
            showAlert(alertWrapper2, "{{ __('OTP code sent to ') }}" + email, 'success');
            setTimeout(() => {
                if (otpBoxes[0]) otpBoxes[0].focus();
            }, 100);
        }, 200);
    }

    // Switch to Screen 3 (Password Screen)
    function goToStep3() {
        step2Panel.style.opacity = '0';
        setTimeout(() => {
            step2Panel.style.display = 'none';
            step1Panel.style.display = 'none';
            step3Panel.style.display = 'block';
            step3Panel.style.opacity = '1';
            setStepIndicator(3);
            alertWrapper3.innerHTML = '';
            showAlert(alertWrapper3, "{{ __('OTP verified! Please set your new password.') }}", 'success');
            setTimeout(() => {
                if (passwordInput) passwordInput.focus();
            }, 100);
        }, 200);
    }

    // Back to Screen 1
    function goToStep1() {
        step2Panel.style.opacity = '0';
        step3Panel.style.opacity = '0';
        setTimeout(() => {
            step2Panel.style.display = 'none';
            step3Panel.style.display = 'none';
            step1Panel.style.display = 'block';
            step1Panel.style.opacity = '1';
            setStepIndicator(1);
            alertWrapper1.innerHTML = '';
            phoneInput.focus();
        }, 200);
    }

    changeNumberBtn.addEventListener('click', goToStep1);
    backToStep1Link.addEventListener('click', goToStep1);

    // ==========================================
    // STEP 1 SUBMIT: Send OTP to Mobile Number
    // ==========================================
    step1Form.addEventListener('submit', function(e) {
        e.preventDefault();
        alertWrapper1.innerHTML = '';

        const phone = phoneInput.value.trim();
        if (!phone || phone.length < 10) {
            showAlert(alertWrapper1, "{{ __('Please enter a valid 10-digit registered mobile number.') }}", 'danger');
            phoneInput.focus();
            return;
        }

        step1Btn.disabled = true;
        step1BtnText.textContent = "{{ __('Sending OTP...') }}";
        step1BtnIcon.className = "ti ti-loader-2 ti-spin";

        fetch('{{ route('password.send-otp') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ phone_number: phone })
        })
        .then(r => r.json())
        .then(data => {
            step1Btn.disabled = false;
            step1BtnText.textContent = "{{ __('Send OTP Code') }}";
            step1BtnIcon.className = "ti ti-arrow-right";

            if (data.status) {
                goToStep2(data.email || phone);
            } else {
                showAlert(alertWrapper1, data.message || "{{ __('User not found. Please verify your mobile number.') }}", 'danger');
            }
        })
        .catch(err => {
            step1Btn.disabled = false;
            step1BtnText.textContent = "{{ __('Send OTP Code') }}";
            step1BtnIcon.className = "ti ti-arrow-right";
            showAlert(alertWrapper1, "{{ __('Unable to connect to server. Please try again.') }}", 'danger');
        });
    });

    // Resend OTP in Step 2
    resendOtpBtn.addEventListener('click', function(e) {
        e.preventDefault();
        const phone = phoneInput.value.trim();
        if (!phone) {
            goToStep1();
            return;
        }

        resendOtpBtn.style.pointerEvents = 'none';
        resendOtpBtn.innerHTML = '<i class="ti ti-loader-2 ti-spin me-1"></i>{{ __("Sending...") }}';

        fetch('{{ route('password.send-otp') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ phone_number: phone })
        })
        .then(r => r.json())
        .then(data => {
            resendOtpBtn.style.pointerEvents = 'auto';
            resendOtpBtn.innerHTML = '<i class="ti ti-refresh me-1"></i>{{ __("Resend OTP") }}';
            if (data.status) {
                showAlert(alertWrapper2, data.message || "{{ __('New OTP code sent successfully!') }}", 'success');
            } else {
                showAlert(alertWrapper2, data.message || "{{ __('Failed to resend OTP.') }}", 'danger');
            }
        })
        .catch(() => {
            resendOtpBtn.style.pointerEvents = 'auto';
            resendOtpBtn.innerHTML = '<i class="ti ti-refresh me-1"></i>{{ __("Resend OTP") }}';
            showAlert(alertWrapper2, "{{ __('Connection error. Please try again.') }}", 'danger');
        });
    });

    // ==========================================
    // STEP 2 SUBMIT: Verify OTP Only
    // ==========================================
    step2Form.addEventListener('submit', function(e) {
        e.preventDefault();
        alertWrapper2.innerHTML = '';

        const otp = otpHiddenInput.value.trim();
        if (!otp || otp.length !== 6) {
            showAlert(alertWrapper2, "{{ __('Please enter the complete 6-digit OTP code.') }}", 'danger');
            otpBoxes[0].focus();
            return;
        }

        step2Btn.disabled = true;
        step2BtnText.textContent = "{{ __('Verifying OTP...') }}";
        step2BtnIcon.className = "ti ti-loader-2 ti-spin";

        fetch('{{ route('password.verify-otp') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ otp: otp })
        })
        .then(r => r.json())
        .then(data => {
            step2Btn.disabled = false;
            step2BtnText.textContent = "{{ __('Verify OTP Code') }}";
            step2BtnIcon.className = "ti ti-check";

            if (data.status) {
                goToStep3();
            } else {
                showAlert(alertWrapper2, data.message || "{{ __('Incorrect OTP code. Please try again.') }}", 'danger');
            }
        })
        .catch(() => {
            step2Btn.disabled = false;
            step2BtnText.textContent = "{{ __('Verify OTP Code') }}";
            step2BtnIcon.className = "ti ti-check";
            showAlert(alertWrapper2, "{{ __('Unable to connect to server. Please try again.') }}", 'danger');
        });
    });

    // ==========================================
    // STEP 3 SUBMIT: Set New Password & Confirm Password
    // ==========================================
    step3Form.addEventListener('submit', function(e) {
        e.preventDefault();
        alertWrapper3.innerHTML = '';

        const password     = passwordInput.value;
        const passwordConf = confirmInput.value;

        if (!password || password.length < 6) {
            showAlert(alertWrapper3, "{{ __('New password must be at least 6 characters long.') }}", 'danger');
            passwordInput.focus();
            return;
        }
        if (password !== passwordConf) {
            showAlert(alertWrapper3, "{{ __('New password and confirm password do not match.') }}", 'danger');
            confirmInput.focus();
            return;
        }

        step3Btn.disabled = true;
        step3BtnText.textContent = "{{ __('Updating Password...') }}";
        step3BtnIcon.className = "ti ti-loader-2 ti-spin";

        fetch('{{ route('password.reset-otp') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                password: password,
                password_confirmation: passwordConf
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.status) {
                showAlert(alertWrapper3, data.message || "{{ __('Password reset successfully!') }}", 'success');
                step3Btn.disabled = true;
                step3BtnText.textContent = "{{ __('Password Updated!') }}";
                step3BtnIcon.className = "ti ti-check";

                setTimeout(() => {
                    window.location.href = '{{ route('login') }}';
                }, 1600);
            } else {
                step3Btn.disabled = false;
                step3BtnText.textContent = "{{ __('Reset Password') }}";
                step3BtnIcon.className = "ti ti-shield-check";
                showAlert(alertWrapper3, data.message || "{{ __('Failed to reset password. Please try again.') }}", 'danger');
            }
        })
        .catch(() => {
            step3Btn.disabled = false;
            step3BtnText.textContent = "{{ __('Reset Password') }}";
            step3BtnIcon.className = "ti ti-shield-check";
            showAlert(alertWrapper3, "{{ __('Unable to connect to server. Please try again.') }}", 'danger');
        });
    });
});
</script>
@endpush
