@extends('layouts.auth')

@section('tab-title')
    {{ __('Login') }}
@endsection

@php
    $settings = settings();
@endphp

@push('script-page')
    @if ($settings['google_recaptcha'] == 'on')
        {!! NoCaptcha::renderJs() !!}
    @endif
@endpush

@push('style-page')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        /* ========================================
                   LOGIN CONTAINER
                ======================================== */
        .login-container {
            width: 100%;
            margin: 0 auto;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.96);
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
            margin-bottom: 6px;
        }

        .login-logo .logo-circle {
            width: 50px;
            height: 50px;
            margin: 0 auto 6px;
            border-radius: 50%;
            border: 1.5px solid #d8e2f0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .login-logo .logo-circle i {
            font-size: 20px;
            color: #94a3b8;
        }

        .auth-main .auth-wrapper .auth-form img {
            padding-right: 0px !important;
        }

        .login-logo .brand-name {
            display: block;
            font-size: 18px;
            font-weight: 800;
            color: #14224a;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .login-logo .brand-sub {
            display: block;
            font-size: 8px;
            color: #1b56d6;
            font-weight: 700;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            margin-top: 3px;
            line-height: 1.3;
            max-width: 270px;
            margin-left: auto;
            margin-right: auto;
        }

        .login-logo hr {
            width: 100%;
            max-width: 260px;
            border: none;
            border-top: 1px solid #e3e9f5;
            margin: 7px auto 0;
        }

        /* ========================================
                   WELCOME SECTION
                ======================================== */
        .welcome-section {
            text-align: center;
            margin: 7px 0 9px;
        }

        .welcome-section h2 {
            font-size: 19px;
            font-weight: 800;
            color: #14224a;
            margin: 0 0 2px;
            letter-spacing: -0.3px;
            line-height: 1.2;
        }

        .welcome-section p {
            color: #8896a8;
            font-size: 11.5px;
            margin: 0;
            font-weight: 400;
        }

        /* ========================================
                   FORM FIELDS
                ======================================== */
        .form-group {
            margin-bottom: 9px;
            position: relative;
        }

        .form-group label {
            display: block;
            font-size: 9.5px;
            font-weight: 700;
            color: #1b56d6;
            margin-bottom: 3px;
            letter-spacing: 0.7px;
            text-transform: uppercase;
        }

        .form-group .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            border-radius: 8px !important;
            background: #ffffff !important;
            border: 1.5px solid #e3e9f5 !important;
            transition: all 0.25s ease;
            box-shadow: none !important;
        }

        .form-group .input-wrapper:focus-within {
            border-color: #1b56d6 !important;
            box-shadow: 0 0 0 3px rgba(27, 86, 214, 0.1) !important;
        }

        .form-group .input-wrapper .input-icon {
            position: absolute;
            left: 12px;
            color: #1b56d6;
            font-size: 15px;
            z-index: 2;
            pointer-events: none;
        }

        /* Completely remove inner form-control borders and shadows from custom.css */
        .form-group .input-wrapper .form-control,
        .form-group .input-wrapper input.form-control {
            width: 100% !important;
            height: 38px !important;
            padding: 0 36px 0 36px !important;
            border: none !important;
            border-width: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            background-color: transparent !important;
            color: #14224a !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            outline: none !important;
            box-shadow: none !important;
        }

        .form-group .input-wrapper .form-control:focus,
        .form-group .input-wrapper input.form-control:focus {
            border: none !important;
            border-width: 0 !important;
            outline: none !important;
            box-shadow: none !important;
            background: transparent !important;
            background-color: transparent !important;
        }

        .form-group .input-wrapper .form-control::placeholder,
        .form-group .input-wrapper input.form-control::placeholder {
            color: #a7b3c8 !important;
            font-weight: 400 !important;
            font-size: 12.5px !important;
        }

        /* ========================================
                   PASSWORD TOGGLE
                ======================================== */
        .password-toggle {
            position: absolute;
            right: 8px;
            background: none;
            border: none;
            color: #a7b3c8;
            cursor: pointer;
            padding: 2px;
            font-size: 15px;
            z-index: 2;
            line-height: 1;
            border-radius: 50%;
            width: 26px;
            height: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle:hover {
            color: #1b56d6;
            background: rgba(27, 86, 214, 0.06);
        }

        .password-toggle:focus {
            outline: none;
        }

        /* ========================================
                   CAPTCHA ROW (SINGLE COMPACT LINE)
                ======================================== */
        .captcha-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .captcha-svg-container {
            flex-shrink: 0;
            border-radius: 8px;
            cursor: pointer;
            user-select: none;
            overflow: hidden;
            transition: all 0.2s ease;
        }

        .captcha-svg-container:hover {
            filter: brightness(0.96);
        }

        .captcha-svg-container svg {
            display: block;
        }

        .captcha-refresh-btn {
            flex-shrink: 0;
            background: #f8fafc;
            border: 1.5px solid #e3e9f5;
            border-radius: 8px;
            height: 38px;
            width: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1b56d6;
            font-size: 17px;
            cursor: pointer;
            transition: all 0.25s ease;
            padding: 0;
        }

        .captcha-refresh-btn:hover {
            background: #1b56d6;
            color: #ffffff;
            border-color: #1b56d6;
            transform: rotate(90deg);
        }

        .captcha-input-wrapper {
            flex: 1 1 auto;
            min-width: 0;
        }

        .captcha-input-wrapper .form-control,
        .captcha-input-wrapper input.form-control {
            height: 38px !important;
            padding: 0 10px 0 32px !important;
            font-size: 13px !important;
            letter-spacing: 1px !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            border: none !important;
            border-width: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            box-shadow: none !important;
        }

        .captcha-input-wrapper .input-icon {
            left: 10px !important;
            font-size: 14px !important;
        }

        /* ========================================
                   OPTIONS ROW
                ======================================== */
        .options-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 2px 0 8px;
            flex-wrap: wrap;
            gap: 6px;
        }

        .options-row .custom-check {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            margin: 0;
        }

        .options-row .custom-check input[type="checkbox"] {
            width: 15px;
            height: 15px;
            border-radius: 4px;
            border: 1.5px solid #c9d4e8;
            background: #ffffff;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            position: relative;
        }

        .options-row .custom-check input[type="checkbox"]:checked {
            background: #1b56d6;
            border-color: #1b56d6;
        }

        .options-row .custom-check input[type="checkbox"]:checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
        }

        .options-row .custom-check .check-label {
            color: #4b5876;
            font-size: 11.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .options-row .forgot-link {
            color: #1b56d6;
            text-decoration: none;
            font-size: 11.5px;
            font-weight: 700;
        }

        .options-row .forgot-link:hover {
            color: #14224a;
        }

        /* ========================================
                   LOGIN BUTTON
                ======================================== */
        .login-btn {
            width: 100%;
            height: 40px;
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
            box-shadow: 0 6px 16px rgba(27, 86, 214, 0.25);
        }

        .login-btn:hover {
            background: #143f9e;
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(27, 86, 214, 0.32);
        }

        .login-btn:active {
            transform: scale(0.98);
        }

        .login-btn:disabled {
            opacity: 0.75;
            cursor: not-allowed;
        }

        /* ========================================
                   REGISTER LINK
                ======================================== */
        .register-link {
            text-align: center;
            margin-top: 8px;
            padding-bottom: 2px;
            display: block;
        }

        .register-link,
        #toggle-text {
            color: #8896a8;
            font-size: 12px;
            font-weight: 500;
        }

        #toggle-auth-mode {
            color: #1b56d6 !important;
            font-weight: 700;
            text-decoration: none;
        }

        #toggle-auth-mode:hover {
            color: #14224a !important;
            text-decoration: underline;
        }

        /* ========================================
                   ALERTS
                ======================================== */
        .alert {
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 12px;
            margin-bottom: 9px;
            border: none;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
        }

        .alert-danger {
            background: #fef2f2;
            color: #dc2626;
            border-left: 4px solid #dc2626;
        }

        .alert-success {
            background: #f0fdf4;
            color: #16a34a;
            border-left: 4px solid #16a34a;
        }

        .invalid-feedback {
            display: block;
            font-size: 11px;
            margin-top: 3px;
            color: #dc2626;
            font-weight: 500;
        }

        .recaptcha-wrapper {
            margin: 6px 0 4px;
            display: flex;
            justify-content: center;
            transform: scale(0.82);
            transform-origin: center;
        }

        /* ========================================
                   RESPONSIVE
                ======================================== */
        @media (max-width: 576px) {
            .welcome-section h2 {
                font-size: 19px;
            }

            .form-group .input-wrapper .form-control {
                height: 42px;
                font-size: 13px;
            }

            .login-btn {
                height: 42px;
                font-size: 13px;
            }

            .options-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="login-container">
                <!-- ===== LOGO ===== -->
                <div class="login-logo">
                    <div class="logo-circle" title="{{ __('Logo space') }}">
                        <i class="ti ti-photo"></i>
                    </div>
                    <span class="brand-name">{{ env('APP_NAME') }}</span>
                    <span class="brand-sub">{{ __('SAATHI Station Asset Attention, Tracking, Help & Improvement') }}</span>
                    <hr>
                </div>

                <!-- ===== WELCOME ===== -->
                <div class="welcome-section">
                    <h2 id="welcome-heading">{{ __('Welcome back') }}</h2>
                    <p id="welcome-sub">{{ __('Sign in or create your account to continue') }}</p>
                </div>

                <!-- ===== AJAX ALERTS WRAPPER ===== -->
                <div id="alert-wrapper">
                    @if (session('info'))
                        <div class="alert alert-info" role="alert" style="background: #e0f2fe; color: #0369a1; border-left: 4px solid #0284c7;">
                            <span class="alert-icon">ℹ️</span>
                            {{ session('info') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            <span class="alert-icon">🔒</span>
                            {{ session('error') }}
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            <span class="alert-icon">✅</span>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <span class="alert-icon">⚠️</span>
                            {{ $errors->first() }}
                        </div>
                    @endif
                </div>

                <!-- ===== FORM ===== -->
                {{ Form::open(['route' => 'login', 'method' => 'post', 'id' => 'loginForm']) }}

                <!-- ===== Mobile Number / Email ===== -->
                <div class="form-group" id="phone-group">
                    <label for="phone">{{ __('MOBILE NUMBER') }}</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="ti ti-user"></i></span>
                        <input type="text" class="form-control" id="phone" name="phone"
                            placeholder="{{ __('Enter your mobile number') }}" value="{{ old('email') ?? old('phone') }}"
                            required autofocus autocomplete="off"   />
                    </div>
                </div>

                <!-- ===== Password ===== -->
                <div class="form-group" id="password-group">
                    <label for="password">{{ __('Password') }}</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="ti ti-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="{{ __('Enter your password') }}" required />
                        <button type="button" class="password-toggle" id="togglePassword" aria-label="Toggle password">
                            <i class="ti ti-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- ===== Confirm Password (Register Only) ===== -->
                <div class="form-group" id="confirm-password-group" style="display: none;">
                    <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="ti ti-lock"></i></span>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                            placeholder="{{ __('Confirm your password') }}" />
                        <button type="button" class="password-toggle" id="togglePasswordConf" aria-label="Toggle password">
                            <i class="ti ti-eye" id="toggleIconConf"></i>
                        </button>
                    </div>
                </div>

                <!-- ===== Captcha Field (Single Unified Row) ===== -->
                @php
                    $initialCaptchaSvg = \App\Services\CaptchaService::generate();
                @endphp
                <div class="form-group" id="captcha-group">
                    <label for="captcha">{{ __('Security Captcha') }} <span class="text-danger">*</span></label>
                    <div class="captcha-row">
                        <div class="captcha-svg-container" id="captcha-svg-container" title="{{ __('Click to refresh captcha') }}">
                            {!! $initialCaptchaSvg !!}
                        </div>
                        <button type="button" class="captcha-refresh-btn" id="refresh-captcha-btn" title="{{ __('Refresh Captcha') }}">
                            <i class="ti ti-refresh"></i>
                        </button>
                        <div class="input-wrapper captcha-input-wrapper">
                            <span class="input-icon"><i class="ti ti-shield-check"></i></span>
                            <input type="text" class="form-control" id="captcha" name="captcha"
                                placeholder="{{ __('Enter code') }}" required autocomplete="off" maxlength="8" />
                        </div>
                    </div>
                </div>

                <!-- ===== Options Row (Login Only) ===== -->
                <div class="options-row">
                    <label class="custom-check">
                        <input type="checkbox" name="remember" checked />
                        <span class="check-label">{{ __('Remember me') }}</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">{{ __('Forgot Password?') }}</a>
                    @endif
                </div>

                <!-- ===== Recaptcha ===== -->
                @if ($settings['google_recaptcha'] == 'on')
                    <div class="recaptcha-wrapper" id="recaptcha-area" style="display: none;">
                        {!! NoCaptcha::display() !!}
                    </div>
                @endif

                <!-- ===== Submit Button ===== -->
                <button type="submit" class="login-btn">
                    <span class="btn-content-text">{{ __('Login') }}</span>
                    <i class="ti ti-arrow-right"></i>
                </button>

                {{ Form::close() }}

                <!-- ===== Toggle Mode Link ===== -->
                <div class="register-link">
                    <span id="toggle-text">{!! __("Don't have an account?") !!}</span>
                    <a href="#" id="toggle-auth-mode" class="ms-1">{{ __('Sign Up') }}</a>
                </div>
            </div>
        </div>
    </div>

        <script>
        let actionMode = 'login'; // 'login' or 'register'

        const phoneInput = document.getElementById('phone');
        const passwordGroup = document.getElementById('password-group');
        const passwordInput = document.getElementById('password');
        const confirmPasswordGroup = document.getElementById('confirm-password-group');
        const confirmPasswordInput = document.getElementById('password_confirmation');
        const optionsRow = document.querySelector('.options-row');
        const submitBtn = document.querySelector('.login-btn');
        const btnContentText = submitBtn.querySelector('.btn-content-text');
        const alertWrapper = document.getElementById('alert-wrapper');
        const welcomeHeading = document.getElementById('welcome-heading');
        const welcomeSub = document.getElementById('welcome-sub');
        const toggleText = document.getElementById('toggle-text');
        const toggleAuthMode = document.getElementById('toggle-auth-mode');
        const recaptchaArea = document.getElementById('recaptcha-area');

        function switchToRegisterMode(employeeName) {
            actionMode = 'register';
            welcomeHeading.textContent = employeeName ? ("Welcome, " + employeeName) : "{{ __('Create Account') }}";
            welcomeSub.textContent = "{{ __('Please confirm your password to complete registration') }}";
            confirmPasswordGroup.style.display = 'block';
            confirmPasswordInput.setAttribute('required', 'true');
            if (optionsRow) optionsRow.style.display = 'none';
            btnContentText.textContent = "{{ __('SUBMIT FOR REGISTRATION') }}";
            toggleText.textContent = "{{ __('Already have an account?') }}";
            toggleAuthMode.textContent = "{{ __('Sign In') }}";

            if (passwordInput.value) {
                confirmPasswordInput.focus();
            } else {
                passwordInput.focus();
            }
        }

        function switchToLoginMode() {
            actionMode = 'login';
            welcomeHeading.textContent = "{{ __('Welcome back') }}";
            welcomeSub.textContent = "{{ __('Sign in or create your account to continue') }}";
            confirmPasswordGroup.style.display = 'none';
            confirmPasswordInput.removeAttribute('required');
            if (optionsRow) optionsRow.style.display = 'flex';
            btnContentText.textContent = "{{ __('Login') }}";
            toggleText.textContent = "{{ __('Don\'t have an account?') }}";
            toggleAuthMode.textContent = "{{ __('Sign Up') }}";
        }

        // Toggle Mode Action Handler
        toggleAuthMode.addEventListener('click', function(e) {
            e.preventDefault();
            alertWrapper.innerHTML = '';
            if (actionMode === 'login') {
                switchToRegisterMode();
            } else {
                switchToLoginMode();
            }
        });

        function refreshCaptcha() {
            const container = document.getElementById('captcha-svg-container');
            const refreshBtn = document.getElementById('refresh-captcha-btn');
            const icon = refreshBtn ? refreshBtn.querySelector('i') : null;
            if (icon) icon.classList.add('ti-spin');

            fetch('{{ route('captcha.refresh') }}')
                .then(res => res.json())
                .then(data => {
                    if (data.status && data.captcha_svg) {
                        container.innerHTML = data.captcha_svg;
                        const captchaInput = document.getElementById('captcha');
                        if (captchaInput) captchaInput.value = '';
                    }
                })
                .catch(err => {
                    console.error('Error refreshing captcha', err);
                })
                .finally(() => {
                    if (icon) icon.classList.remove('ti-spin');
                });
        }

        document.getElementById('refresh-captcha-btn')?.addEventListener('click', refreshCaptcha);
        document.getElementById('captcha-svg-container')?.addEventListener('click', refreshCaptcha);

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const loginInput = phoneInput.value.trim();
            const isEmployeePhone = /^\d{10}$/.test(loginInput);

            if (!isEmployeePhone) {
                if (actionMode === 'register') {
                    e.preventDefault();
                    alertWrapper.innerHTML = '';
                    showAlert("{{ __('Please enter a valid 10-digit mobile number to register as an employee.') }}", 'danger');
                    return;
                }
                phoneInput.setAttribute('name', 'email');
                return;
            }

            e.preventDefault();
            alertWrapper.innerHTML = '';

            const phoneNumber = loginInput;
            const password = passwordInput.value;
            const passwordConf = confirmPasswordInput ? confirmPasswordInput.value : '';
            const remember = document.querySelector('input[name="remember"]')?.checked;
            const captchaInput = document.getElementById('captcha');
            const captchaVal = captchaInput ? captchaInput.value.trim() : '';

            if (!password) {
                showAlert("{{ __('Password is required.') }}", 'danger');
                return;
            }

            if (actionMode === 'register' && password !== passwordConf) {
                showAlert("{{ __('Passwords do not match.') }}", 'danger');
                return;
            }

            if (!captchaVal) {
                showAlert("{{ __('Please enter the security captcha code.') }}", 'danger');
                if (captchaInput) captchaInput.focus();
                return;
            }

            showLoading(true);

            fetch('{{ route('login-or-register') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    phone_number: phoneNumber,
                    password: password,
                    password_confirmation: passwordConf,
                    action_type: actionMode,
                    remember: remember,
                    captcha: captchaVal
                })
            })
            .then(response => response.json())
            .then(res => {
                if (!res.status) {
                    showLoading(false);
                    showAlert(res.message, 'danger');
                    if (res.captcha_invalid) {
                        refreshCaptcha();
                        if (captchaInput) {
                            captchaInput.value = '';
                            captchaInput.focus();
                        }
                    }
                    if (res.need_register) {
                        switchToRegisterMode(res.employee_name);
                    }
                } else {
                    showAlert(res.message, 'success');
                    setTimeout(() => {
                        window.location.href = res.redirect;
                    }, 800);
                }
            })
            .catch(err => {
                showLoading(false);
                showAlert("{{ __('Authentication failed. Please verify inputs.') }}", 'danger');
            });
        });

        function showLoading(isLoading) {
            if (isLoading) {
                submitBtn.setAttribute('disabled', 'true');
                btnContentText.textContent = "{{ __('Processing...') }}";
            } else {
                submitBtn.removeAttribute('disabled');
                btnContentText.textContent = actionMode === 'login' ? "{{ __('Login') }}" : "{{ __('SUBMIT FOR REGISTRATION') }}";
            }
        }

        function showAlert(message, type) {
            const icon = type === 'success' ? '✅' : '🔒';
            alertWrapper.innerHTML = `
                <div class="alert alert-${type}" role="alert">
                    <span class="alert-icon">${icon}</span>
                    ${message}
                </div>
            `;
        }

        // Toggle Password Visibility
        const togglePassword = document.getElementById('togglePassword');
        const toggleIcon = document.getElementById('toggleIcon');
        if (togglePassword && passwordInput && toggleIcon) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                toggleIcon.className = type === 'password' ? 'ti ti-eye' : 'ti ti-eye-off';
            });
        }

        // Toggle Confirm Password Visibility
        const togglePasswordConf = document.getElementById('togglePasswordConf');
        const toggleIconConf = document.getElementById('toggleIconConf');
        if (togglePasswordConf && confirmPasswordInput && toggleIconConf) {
            togglePasswordConf.addEventListener('click', function() {
                const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmPasswordInput.setAttribute('type', type);
                toggleIconConf.className = type === 'password' ? 'ti ti-eye' : 'ti ti-eye-off';
            });
        }
    </script>
@endsection
