@extends('layouts.auth')

@section('tab-title')
    {{ __('Register') }}
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
        /* ========================================
                   GOOGLE FONTS
                ======================================== */
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        /* ========================================
                   REGISTER CONTAINER
                ======================================== */
        .register-container {
            margin: 0 auto;
            padding: 10px 0;
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
                   LOGO SECTION - GLASS EFFECT
                ======================================== */
        .register-logo {
            text-align: center;
            margin-bottom: 28px;
            position: relative;
        }

        .register-logo .logo-wrapper {
            display: inline-block;
            padding: 16px 28px;
            background: transparent;
            border-radius: 20px;
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        .register-logo .logo-wrapper img {
            max-height: 64px;
            transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .auth-main .auth-wrapper .auth-form img {
            padding-right: 0px !important;
        }

        .register-logo .logo-wrapper:hover img {
            transform: scale(1.03);
        }

        .register-logo .brand-name {
            display: block;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 15px;
            font-weight: 700;
            color: #3a2a10;
            margin-top: 10px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .register-logo .brand-sub {
            display: block;
            font-size: 11px;
            color: #8896a8;
            font-weight: 500;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* ========================================
                   WELCOME SECTION
                ======================================== */
        .welcome-section {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }

        .welcome-section h2 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 26px;
            font-weight: 800;
            color: #3a2a10;
            margin: 0 0 6px;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .welcome-section p {
            color: #8896a8;
            font-size: 14px;
            margin: 0;
            font-weight: 400;
        }

        /* ========================================
                   FORM FIELDS - MODERN
                ======================================== */
        .form-group {
            margin-bottom: 18px;
            position: relative;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 700;
            color: #3a2a10;
            margin-bottom: 8px;
            letter-spacing: 0.2px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: color 0.3s ease;
        }

        .form-group .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            border-radius: 12px;
            background: #ffffff;
            border: 2px solid #f0dfb8;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .form-group .input-wrapper:focus-within {
            border-color: #e0ab3e;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(224, 171, 62, 0.08);
        }

        .form-group .input-wrapper .input-icon {
            position: absolute;
            left: 16px;
            color: #e0ab3e;
            font-size: 19px;
            z-index: 2;
            pointer-events: none;
            transition: all 0.4s ease;
        }

        .form-group .input-wrapper .form-control {
            width: 100%;
            height: 54px;
            padding: 0 44px 0 48px !important;
            border: none;
            border-radius: 12px;
            background: transparent;
            color: #3a2a10;
            font-size: 15px;
            font-weight: 500;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: all 0.3s ease;
            box-shadow: none;
            outline: none;
        }

        .form-group .input-wrapper .form-control::placeholder {
            color: #a7b3c8;
            font-weight: 400;
        }

        /* ========================================
                   PASSWORD TOGGLE
                ======================================== */
        .password-toggle {
            position: absolute;
            right: 14px;
            background: none;
            border: none;
            color: #a7b3c8;
            cursor: pointer;
            padding: 4px;
            font-size: 18px;
            transition: all 0.4s ease;
            z-index: 2;
            line-height: 1;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle:hover {
            color: #e0ab3e;
            background: rgba(224, 171, 62, 0.06);
        }

        .password-toggle:focus {
            outline: none;
        }

        /* ========================================
                   REGISTER BUTTON - PREMIUM GLOW
                ======================================== */
        .register-btn {
            width: 100%;
            height: 54px;
            background: linear-gradient(135deg, #e0ab3e 0%, #b88628 100%);
            border: none;
            border-radius: 14px;
            color: #ffffff;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 4px 15px rgba(224, 171, 62, 0.25);
            margin-top: 10px;
        }

        .register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(224, 171, 62, 0.4);
            background: linear-gradient(135deg, #e7b64f 0%, #c4912e 100%);
        }

        .register-btn:active {
            transform: translateY(1px);
            box-shadow: 0 2px 10px rgba(224, 171, 62, 0.2);
        }

        .register-btn .btn-content {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            letter-spacing: 0.5px;
        }

        .register-btn .arrow-icon {
            font-size: 18px;
            transition: transform 0.3s ease;
            display: inline-flex;
            align-items: center;
        }

        .register-btn:hover .arrow-icon {
            transform: translateX(4px);
        }

        .register-btn .ripple {
            position: absolute;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: scale(0);
            animation: rippleEffect 0.8s ease-out;
            pointer-events: none;
            z-index: 1;
            left: 50%;
            top: 50%;
            margin-left: -300px;
            margin-top: -300px;
            width: 0;
            height: 0;
            transition: width 0.5s, height 0.5s;
        }

        /* ========================================
                   ALERTS
                ======================================== */
        .alert {
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 14px;
            margin-bottom: 18px;
            border: none;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideAlert 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 500;
        }

        @keyframes slideAlert {
            from {
                opacity: 0;
                transform: translateY(-10px) scale(0.96);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
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

        .alert .alert-icon {
            font-size: 18px;
            flex-shrink: 0;
        }

        /* ========================================
                   ERROR TEXT
                ======================================== */
        .invalid-feedback {
            display: block;
            font-size: 12px;
            margin-top: 4px;
            color: #dc2626;
            font-weight: 500;
            font-family: 'Plus Jakarta Sans', sans-serif;
            animation: slideAlert 0.3s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        /* ========================================
                   RESPONSIVE
                ======================================== */
                /* ========================================
                   CAPTCHA ROW
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

        .captcha-input-wrapper .form-control {
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

        @media (max-width: 576px) {
            .form-group .input-wrapper .form-control {
                height: 48px;
                font-size: 14px;
                padding: 0 40px 0 42px !important;
            }
            .form-group .input-wrapper .input-icon {
                font-size: 16px;
                left: 14px;
            }
            .password-toggle {
                font-size: 16px;
                right: 10px;
                width: 28px;
                height: 28px;
            }
            .register-btn {
                height: 48px;
                font-size: 14px;
                border-radius: 12px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="register-container">
                <!-- ===== LOGO ===== -->
                <div class="register-logo">
                    <div class="logo-wrapper">
                        <img src="{{ asset(Storage::url('upload/logo/logo.png')) }}" alt="Logo" />
                        <span class="brand-name">{{ env('APP_NAME') }}</span>
                        <span class="brand-sub">{{ __('SAATHI Station Asset Attention, Tracking, Help & Improvement') }}</span>
                    </div>
                </div>

                <!-- ===== WELCOME ===== -->
                <div class="welcome-section">
                    <h2>{{ __('Create Account') }}</h2>
                    <p>{{ __('Enter your registered mobile number and password') }}</p>
                </div>

                <!-- ===== FORM ===== -->
                {{ Form::open(['route' => 'register', 'method' => 'post', 'id' => 'registerForm']) }}

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

                <!-- ===== Mobile Number ===== -->
                <div class="form-group">
                    <label for="phone_number">{{ __('Mobile Number') }}</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="ti ti-user"></i></span>
                        <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number"
                            name="phone_number" placeholder="{{ __('Enter your mobile number') }}"
                            value="{{ old('phone_number') }}" required autofocus autocomplete="off" inputmode="numeric" pattern="[0-9]*" />
                    </div>
                    @error('phone_number')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <!-- ===== Password ===== -->
                <div class="form-group">
                    <label for="password">{{ __('Password') }}</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="ti ti-lock"></i></span>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                            name="password" placeholder="{{ __('Create your password') }}" required />
                        <button type="button" class="password-toggle" id="togglePassword" aria-label="Toggle password">
                            <i class="ti ti-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <!-- ===== Password Confirmation ===== -->
                <div class="form-group">
                    <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="ti ti-lock"></i></span>
                        <input type="password" class="form-control" id="password_confirmation"
                            name="password_confirmation" placeholder="{{ __('Confirm your password') }}" required />
                        <button type="button" class="password-toggle" id="togglePasswordConf" aria-label="Toggle password">
                            <i class="ti ti-eye" id="toggleIconConf"></i>
                        </button>
                    </div>
                </div>

                                <!-- ===== Captcha Field ===== -->
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

                <!-- ===== Recaptcha ===== -->
                @if ($settings['google_recaptcha'] == 'on')
                    <div class="recaptcha-wrapper" style="transform: scale(0.92); transform-origin: center; margin: 12px 0 6px; display: flex; justify-content: center;">
                        {!! NoCaptcha::display() !!}
                    </div>
                    @error('g-recaptcha-response')
                        <span class="invalid-feedback text-center">{{ $message }}</span>
                    @enderror
                @endif

                <!-- ===== Register Button ===== -->
                <button type="submit" class="register-btn">
                    <span class="ripple"></span>
                    <span class="btn-content">
                        {{ __('SUBMIT FOR REGISTRATION') }}
                        <span class="arrow-icon">
                            <i class="ti ti-arrow-right"></i>
                        </span>
                    </span>
                </button>

                {{ Form::close() }}

                <!-- ===== Back to Login ===== -->
                <div class="text-center mt-3" style="margin-top: 20px;">
                    <span class="text-muted" style="color: #a7b3c8 !important; font-family: 'Plus Jakarta Sans', sans-serif;">{{ __('Already have an account?') }}</span>
                    <a href="{{ route('login') }}" class="ms-1" style="color: #e0ab3e; font-weight: 700; text-decoration: none; font-family: 'Plus Jakarta Sans', sans-serif;">{{ __('Login here') }}</a>
                </div>
            </div>
        </div>
    </div>

    <script>
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
                .catch(err => console.error(err))
                .finally(() => {
                    if (icon) icon.classList.remove('ti-spin');
                });
        }

        document.getElementById('refresh-captcha-btn')?.addEventListener('click', refreshCaptcha);
        document.getElementById('captcha-svg-container')?.addEventListener('click', refreshCaptcha);

        // ===== Password Toggle =====
        const togglePassword = document.getElementById('togglePassword');
        if (togglePassword) {
            togglePassword.addEventListener('click', function() {
                const input = document.getElementById('password');
                const icon = document.getElementById('toggleIcon');
                if (input && icon) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.className = 'ti ti-eye-off';
                    } else {
                        input.type = 'password';
                        icon.className = 'ti ti-eye';
                    }
                }
            });
        }

        // ===== Password Confirmation Toggle =====
        const togglePasswordConf = document.getElementById('togglePasswordConf');
        if (togglePasswordConf) {
            togglePasswordConf.addEventListener('click', function() {
                const input = document.getElementById('password_confirmation');
                const icon = document.getElementById('toggleIconConf');
                if (input && icon) {
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.className = 'ti ti-eye-off';
                    } else {
                        input.type = 'password';
                        icon.className = 'ti ti-eye';
                    }
                }
            });
        }

        // ===== Input Focus Effects =====
        document.querySelectorAll('.input-wrapper .form-control').forEach(input => {
            input.addEventListener('focus', function() {
                const wrapper = this.closest('.input-wrapper');
                const icon = wrapper.querySelector('.input-icon');
                if (icon) icon.style.color = '#e0ab3e';
            });
            input.addEventListener('blur', function() {
                const wrapper = this.closest('.input-wrapper');
                const icon = wrapper.querySelector('.input-icon');
                if (icon && !this.value) icon.style.color = '#e0ab3e';
            });
        });

        // ===== Ripple Effect on Button =====
        document.querySelector('.register-btn')?.addEventListener('click', function(e) {
            const ripple = this.querySelector('.ripple');
            if (ripple) {
                ripple.style.width = '600px';
                ripple.style.height = '600px';
                setTimeout(() => {
                    ripple.style.width = '0';
                    ripple.style.height = '0';
                }, 800);
            }
        });
    </script>
@endsection
