@extends('layouts.auth')

@section('tab-title')
    {{ __('Reset Password') }}
@endsection

@php
    $settings = settings();
@endphp

@push('style-page')
    <style>
        /* ========================================
                       GOOGLE FONTS
                    ======================================== */
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        /* ========================================
                       LOGIN CONTAINER
                    ======================================== */
        .login-container {
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
        .login-logo {
            text-align: center;
            margin-bottom: 28px;
            position: relative;
        }

        .login-logo .logo-wrapper {
            display: inline-block;
            padding: 16px 28px;
            background: transparent;
            border-radius: 20px;
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        .login-logo .logo-wrapper img {
            max-height: 64px;
            transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .auth-main .auth-wrapper .auth-form img {
            padding-right: 0px !important;
        }

        .login-logo .logo-wrapper:hover img {
            transform: scale(1.03);
        }

        .login-logo .brand-name {
            display: block;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 15px;
            font-weight: 700;
            color: #3a2a10;
            margin-top: 10px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .login-logo .brand-sub {
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
            padding: 0 44px 0 48px;
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
                       BUTTON
                    ======================================== */
        .reset-btn {
            width: 100%;
            height: 54px;
            background: linear-gradient(135deg, #f4a261, #e07c3e);
            border: none;
            border-radius: 12px;
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
            letter-spacing: 1px;
            text-transform: uppercase;
            box-shadow: 0 8px 24px rgba(224, 124, 62, 0.35);
        }

        .reset-btn .ripple {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            transform: translate(-50%, -50%);
            transition: width 0.8s cubic-bezier(0.16, 1, 0.3, 1),
                height 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 1;
        }

        .reset-btn:hover .ripple {
            width: 600px;
            height: 600px;
        }

        .reset-btn .btn-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            z-index: 2;
        }

        .reset-btn .btn-content .arrow-icon {
            display: inline-flex;
            transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .reset-btn:hover .btn-content .arrow-icon {
            transform: translateX(4px);
        }

        .reset-btn:hover {
            background: linear-gradient(135deg, #f5b07a, #e8884a);
            transform: translateY(-2px);
            box-shadow: 0 14px 34px rgba(224, 124, 62, 0.4);
        }

        .reset-btn:active {
            transform: scale(0.98);
        }

        /* ========================================
                       BACK LINK
                    ======================================== */
        .back-link {
            text-align: center;
            margin-top: 22px;
            padding-top: 18px;
            border-top: 1px solid rgba(0, 0, 0, 0.04);
            position: relative;
        }

        .back-link p {
            color: #8896a8;
            font-size: 14px;
            margin: 0;
            font-weight: 400;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .back-link a {
            color: #e0ab3e;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.4s ease;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .back-link a:hover {
            color: #3a2a10;
        }

        /* ========================================
                       RESPONSIVE
                    ======================================== */
        @media (max-width: 576px) {
            .login-container {
                padding: 0;
            }

            .login-logo .logo-wrapper img {
                max-height: 46px;
            }

            .welcome-section h2 {
                font-size: 22px;
            }

            .form-group .input-wrapper .form-control {
                height: 48px;
                font-size: 14px;
                padding: 0 40px 0 42px;
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

            .reset-btn {
                height: 48px;
                font-size: 14px;
                border-radius: 12px;
            }
        }

        @media (max-width: 380px) {
            .login-logo .logo-wrapper img {
                max-height: 40px;
            }

            .welcome-section h2 {
                font-size: 18px;
            }

            .welcome-section p {
                font-size: 13px;
            }

            .form-group .input-wrapper .form-control {
                height: 44px;
                font-size: 13px;
                padding: 0 36px 0 38px;
                border-radius: 10px;
            }

            .form-group .input-wrapper .input-icon {
                font-size: 14px;
                left: 12px;
            }

            .password-toggle {
                font-size: 14px;
                right: 8px;
                width: 24px;
                height: 24px;
            }

            .reset-btn {
                height: 44px;
                font-size: 13px;
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
                    <div class="logo-wrapper">
                        <img src="{{ asset(Storage::url('upload/logo/logo.png')) }}" alt="Logo" />
                        <span class="brand-name">{{ env('APP_NAME') }}</span>
                        <span class="brand-sub">{{ __('SAATHI Station Asset Attention, Tracking, Help & Improvement') }}</span>
                    </div>
                </div>

                <!-- ===== WELCOME ===== -->
                <div class="welcome-section">
                    <h2>{{ __('Reset your password') }}</h2>
                    <p>{{ __('You have successfully verified your account.') }}<br>{{ __('Enter new password below.') }}</p>
                </div>

                <!-- ===== FORM ===== -->
                {{ Form::open(['route' => 'password.update', 'method' => 'post', 'id' => 'resetPasswordForm']) }}
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        <span class="alert-icon">✅</span>
                        {{ session('status') }}
                    </div>
                @endif

                <!-- ===== Email ===== -->
                <div class="form-group">
                    <label for="email">{{ __('Email address') }}</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="ti ti-mail"></i></span>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" placeholder="{{ __('Enter your email address') }}"
                            value="{{ $request->get('email') }}" required readonly />
                    </div>
                    @error('email')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <!-- ===== New Password ===== -->
                <div class="form-group">
                    <label for="password">{{ __('New Password') }}</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="ti ti-lock"></i></span>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                            name="password" placeholder="{{ __('Enter new password') }}" required />
                        <button type="button" class="password-toggle" id="togglePassword" aria-label="Toggle password">
                            <i class="ti ti-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <!-- ===== Confirm Password ===== -->
                <div class="form-group">
                    <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="ti ti-lock-check"></i></span>
                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                            id="password_confirmation" name="password_confirmation"
                            placeholder="{{ __('Confirm new password') }}" required />
                        <button type="button" class="password-toggle" id="toggleConfirmPassword"
                            aria-label="Toggle confirm password">
                            <i class="ti ti-eye" id="toggleConfirmIcon"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                    @enderror
                </div>

                <!-- ===== Update Password Button ===== -->
                <button type="submit" class="reset-btn">
                    <span class="ripple"></span>
                    <span class="btn-content">
                        {{ __('Update Password') }}
                        <span class="arrow-icon">
                            <i class="ti ti-arrow-right"></i>
                        </span>
                    </span>
                </button>

                <!-- ===== Back to Login ===== -->
                <div class="back-link">
                    <p>{{ __('Back to') }} <a href="{{ route('login') }}">{{ __('Log In') }}</a></p>
                </div>

                {{ Form::close() }}
            </div>
        </div>
    </div>

    <script>
        // ===== Password Toggle for New Password =====
        document.getElementById('togglePassword')?.addEventListener('click', function() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'ti ti-eye-off';
            } else {
                input.type = 'password';
                icon.className = 'ti ti-eye';
            }
        });

        // ===== Password Toggle for Confirm Password =====
        document.getElementById('toggleConfirmPassword')?.addEventListener('click', function() {
            const input = document.getElementById('password_confirmation');
            const icon = document.getElementById('toggleConfirmIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'ti ti-eye-off';
            } else {
                input.type = 'password';
                icon.className = 'ti ti-eye';
            }
        });

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
        document.querySelector('.reset-btn')?.addEventListener('click', function(e) {
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

    <script>
        // ===== HIDE LOADER - same logic as landing page =====
        window.addEventListener('load', function() {
            var loader = document.getElementById('loader');
            if (loader) {
                setTimeout(function() {
                    loader.classList.add('hidden');
                    setTimeout(function() {
                        loader.style.display = 'none';
                    }, 500);
                }, 500);
            }
        });
    </script>
@endsection

