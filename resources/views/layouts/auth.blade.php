@php
    use App\Models\BackgroundLogin;
    use Illuminate\Support\Facades\Storage;

    $settings = settings();

    $backgroundLogin = BackgroundLogin::where('status', 1)->first()
        ?? BackgroundLogin::first();
@endphp

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ env('APP_NAME') }} - @yield('tab-title')</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <meta name="author" content="{{ !empty($settings['app_name']) ? $settings['app_name'] : env('APP_NAME') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="title" content="{{ $settings['meta_seo_title'] }}">
    <meta name="keywords" content="{{ $settings['meta_seo_keyword'] }}">
    <meta name="description" content="{{ $settings['meta_seo_description'] }}">

    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ env('APP_URL') }}">
    <meta property="og:title" content="{{ $settings['meta_seo_title'] }}">
    <meta property="og:description" content="{{ $settings['meta_seo_description'] }}">
    <meta property="og:image" content="{{ asset(Storage::url('upload/seo')) . '/' . $settings['meta_seo_image'] }}">

    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ env('APP_URL') }}">
    <meta property="twitter:title" content="{{ $settings['meta_seo_title'] }}">
    <meta property="twitter:description" content="{{ $settings['meta_seo_description'] }}">
    <meta property="twitter:image"
        content="{{ asset(Storage::url('upload/seo')) . '/' . $settings['meta_seo_image'] }}">

    <link rel="icon" href="{{ asset(Storage::url('upload/logo.jpeg')) . '/logo.jpeg' }}" type="image/x-icon" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" />
    <link rel="stylesheet" href="{{ asset('assets/fonts/phosphor/duotone/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link" />
    @if (!empty($settings['custom_color']) && $settings['color_type'] == 'custom')
        <link rel="stylesheet" id="Pstylesheet" href="{{ asset('assets/css/custom-color.css') }}" />
        <script src="{{ asset('js/theme-pre-color.js') }}"></script>
    @else
        <link rel="stylesheet" id="Pstylesheet" href="{{ asset('assets/css/style-preset.css') }}" />
    @endif
    <link href="{{ asset('css/custom.css') }} " rel="stylesheet">

 @php
    $bgImageUrl = !empty($backgroundLogin?->background_image)
        ? asset('storage/' . $backgroundLogin->background_image)
        : asset('upload/logo/auth-bg.png');

    $sideImageUrl = !empty($backgroundLogin?->login_image)
        ? asset('storage/' . $backgroundLogin->login_image)
        : asset('upload/logo/auth-side.png');
@endphp

    <style>
        :root {
            --color-primary: #1b56d6;
            --color-primary-dark: #143f9e;
            --color-primary-light: #e8effc;
            --color-text-dark: #14224a;
            --color-text-muted: #8896a8;
        }

        /* ========================================
               GLOBAL RESET - NO SCROLL
            ======================================== */
        html,
        body {
            height: 100% !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            overflow: hidden !important;
            box-sizing: border-box !important;
        }

        /* ========================================
               LOADER
            ======================================== */
        #loader {
            position: fixed;
            inset: 0;
            background: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 99999;
            transition: opacity 0.5s ease;
        }

        #loader.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .loader-box {
            position: relative;
            width: 100px;
            height: 100px;
        }

        .loader-spinner {
            position: absolute;
            inset: 0;
            width: 100px;
            height: 100px;
            border-top: 3px solid var(--color-primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .loader-logo {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50px;
            height: 50px;
            background: #ffffff;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .loader-logo img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 30px !important;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* ========================================
               AUTH MAIN - DYNAMIC BACKGROUND
            ======================================== */
       .auth-main {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    width: 100% !important;
    height: 100vh !important;
    margin: 0 !important;
    padding: 0 16px 70px 16px !important;

    display: flex !important;
    align-items: center !important;
    justify-content: center !important;

    background-image:
        linear-gradient(
            135deg,
            rgba(10, 26, 74, 0.25) 0%,
            rgba(13, 42, 107, 0.25) 50%,
            rgba(10, 26, 74, 0.25) 100%
        ),
        url('{{ $bgImageUrl }}') !important;

    background-size: cover !important;
    background-position: center center !important;
    background-repeat: no-repeat !important;
    background-color: #0a1a4a !important;
    background-attachment: fixed !important;

    overflow: hidden !important;
    box-sizing: border-box !important;
}

.auth-main::before {
    display: none !important;
}

        /* ========================================
               AUTH CARD
            ======================================== */
        .auth-wrapper.v2 {
            display: flex !important;
            flex-direction: row !important;
            align-items: stretch !important;
            flex-wrap: nowrap !important;

            width: 100% !important;
            max-width: 1060px !important;
            min-height: 570px !important;
            max-height: 640px !important;

            margin: auto !important;
            position: relative !important;
            transform: none !important;

            border-radius: 14px !important;
            overflow: hidden !important;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.55) !important;
            background: #ffffff !important;
            z-index: 2 !important;
            box-sizing: border-box !important;
        }

        /* ========================================
               FORM - 40%
            ======================================== */
        .auth-wrapper.v2 .auth-form {
            flex: 1 1 42% !important;
            width: 42% !important;
            max-width: 42% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
            height: auto !important;
            max-height: 100% !important;

            background: #ffffff !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 16px 26px !important;

            overflow-y: auto !important;
            overflow-x: hidden !important;
        }

        .auth-wrapper.v2 .auth-form::-webkit-scrollbar {
            width: 4px;
        }

        .auth-wrapper.v2 .auth-form::-webkit-scrollbar-thumb {
            background: #c9d4e8;
            border-radius: 4px;
        }

        .auth-wrapper.v2 .auth-form::-webkit-scrollbar-track {
            background: transparent;
        }

        .auth-wrapper.v2 .auth-form>.card {
            width: 100%;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            margin-bottom: 0 !important;
            background: transparent !important;
        }

        .auth-wrapper.v2 .auth-form>.card>.card-body {
            padding: 0 !important;
        }

        /* ========================================
               IMAGE SIDE - 60% WITH BLUE WAVE + LOCATION
            ======================================== */
        .auth-wrapper.v2 .auth-sidecontent {
            flex: 1 1 58% !important;
            width: 58% !important;
            max-width: 58% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
            height: auto !important;
            max-height: 100% !important;

            padding: 0 !important;
            position: relative !important;
            background: linear-gradient(135deg, #0a1a4a 0%, #0d2a6b 100%) !important;
            overflow: hidden !important;

            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        /* Image (top 78% of container height) */
        .auth-wrapper.v2 .auth-sidecontent img {
            width: 100% !important;
            height: 100% !important;
            min-height: 100% !important;
            object-fit: cover !important;
            object-position: center 25% !important;
            display: block !important;

            /* 🔥 Image ko upar shift karo taaki neeche wave ke liye jagah bache */
            padding-bottom: 0 !important;
            margin-bottom: 0 !important;
        }

        /* ========================================
               BLUE WAVE + LOCATION BAR
               🔥 Bottom pe fixed overlay, image ke upar
            ======================================== */
        .auth-wrapper.v2 .auth-sidecontent .location-bar {
            position: absolute !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            height: 110px !important;
            z-index: 5 !important;

            display: flex !important;
            align-items: flex-end !important;
            justify-content: flex-end !important;
            padding: 0 26px 8px 0 !important;
            gap: 8px !important;

            color: #ffffff !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            letter-spacing: 0.3px !important;
            pointer-events: none !important;
            box-sizing: border-box !important;
        }

        /* 🔥 Blue wave SVG background */
        .auth-wrapper.v2 .auth-sidecontent .location-bar::before {
            content: '' !important;
            position: absolute !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100% !important;
            height: 100% !important;
            z-index: -1 !important;

            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 120' preserveAspectRatio='none'%3E%3Cpath d='M0,40 C150,0 350,0 500,35 C650,70 850,90 1050,55 C1100,45 1150,40 1200,35 L1200,120 L0,120 Z' fill='%231b56d6'/%3E%3Cpath d='M0,55 C150,20 350,20 500,50 C650,80 850,100 1050,70 C1100,60 1150,55 1200,50 L1200,120 L0,120 Z' fill='%230d2a6b'/%3E%3C/svg%3E") !important;
            background-size: 100% 100% !important;
            background-position: center bottom !important;
            background-repeat: no-repeat !important;
        }

        .auth-wrapper.v2 .auth-sidecontent .location-bar i {
            font-size: 18px !important;
            color: #ffffff !important;
            flex-shrink: 0 !important;
            position: relative !important;
            z-index: 2 !important;
            line-height: 1 !important;
        }

        .auth-wrapper.v2 .auth-sidecontent .location-bar .loc-text {
            position: relative !important;
            z-index: 2 !important;
            line-height: 1.2 !important;
        }

        .auth-wrapper.v2 .auth-sidecontent .location-bar .divider {
            opacity: 0.55 !important;
            margin: 0 4px !important;
            font-weight: 300 !important;
            position: relative !important;
            z-index: 2 !important;
        }

        /* ========================================
               MARQUEE
            ======================================== */
        .marquee-wrapper {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            z-index: 9999;
            background: #1a1a2e;
            border-top: 3px solid var(--color-primary);
            box-shadow: 0 -4px 30px rgba(0, 0, 0, 0.3);
            padding: 8px 0;
        }

        .marquee-content {
            display: inline-block;
            white-space: nowrap;
            animation: marquee-scroll 30s linear infinite;
            font-size: 14px;
            padding-left: 100%;
            font-weight: 500;
        }

        @keyframes marquee-scroll {
            from {
                transform: translateX(0%);
            }

            to {
                transform: translateX(-100%);
            }
        }

        .marquee-wrapper:hover .marquee-content {
            animation-play-state: paused;
        }

        .marquee-content .text-danger {
            color: red !important;
            font-weight: 700;
            margin-right: 20px;
        }

        .marquee-content .text-dark {
            color: #ffffff !important;
            opacity: 0.9;
        }

        /* ========================================
               NUCLEAR CENTER FIX
            ======================================== */
        body>.auth-main,
        body .auth-main {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        body>.auth-main>.auth-wrapper.v2,
        body .auth-main .auth-wrapper.v2 {
            margin: auto !important;
            align-self: center !important;
        }

        /* ========================================
               RESPONSIVE
            ======================================== */
        @media (max-width: 991px) {
            .auth-wrapper.v2 {
                max-width: 800px;
            }
        }

        @media (max-width: 767px) {
            .auth-wrapper.v2 .auth-sidecontent {
                display: none !important;
            }

            .auth-wrapper.v2 .auth-form {
                width: 100% !important;
                max-width: 100% !important;
                flex: 1 1 100% !important;
                padding: 30px 24px !important;
            }

            .auth-wrapper.v2 {
                max-width: 420px !important;
                max-height: none !important;
            }
        }

        @media (max-height: 640px) {

            html,
            body {
                overflow: auto !important;
            }

            .auth-main {
                position: relative !important;
                height: auto !important;
                min-height: 100vh !important;
                padding: 20px 16px 70px !important;
                align-items: flex-start !important;
            }

            .auth-wrapper.v2 {
                max-height: none !important;
            }
        }
    </style>

    @stack('style-page')
</head>

<body
    data-pc-preset="{{ !empty($settings['color_type']) && $settings['color_type'] == 'custom' ? 'custom' : $settings['accent_color'] }}"
    data-pc-sidebar-theme="light" data-pc-sidebar-caption="{{ $settings['sidebar_caption'] }}"
    data-pc-direction="{{ $settings['theme_layout'] }}" data-pc-theme="{{ $settings['theme_mode'] }}">

    <!-- ========== LOADER ========== -->
    <div id="loader">
        <div class="loader-box">
            <div class="loader-spinner"></div>
            <div class="loader-logo">
                <img src="{{ asset(Storage::url('upload/logo/logo.jpeg')) }}" alt="Loading">
            </div>
        </div>
    </div>
    <script>
        setTimeout(function() {
            var loader = document.getElementById('loader');
            if (loader && !loader.classList.contains('hidden')) {
                loader.classList.add('hidden');
                setTimeout(function() {
                    loader.style.display = 'none';
                }, 200);
            }
        }, 500);
    </script>

    <div class="auth-main">
        <div class="auth-wrapper v2">
            <div class="auth-form">
                @yield('content')
            </div>
            <div class="auth-sidecontent">
                <img src="{{ $sideImageUrl }}" alt="{{ env('APP_NAME') }}" />

                <!-- ===== BLUE WAVE + LOCATION OVERLAY ===== -->
                <div class="location-bar">
                    <i class="ti ti-map-pin"></i>
                    <span class="loc-text">Izzatnagar Mandal</span>
                    <span class="divider">|</span>
                    <span class="loc-text">North Eastern Railway</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Marquee Notice -->
    <div class="marquee-wrapper">
        <div class="marquee-content">
            <span class="text-danger fw-semibold">🔔 Notice:</span>
            <span class="text-dark">कृपया ध्यान दें कि SAATHI Station Asset Attention, Tracking, Help & Improvement टीम
                द्वारा पासवर्ड बदलने हेतु किसी भी
                प्रकार का OTP, लिंक या संदेश नहीं भेजा जाता है। इसलिए यदि आपको ऐसा कोई संदेश प्राप्त होता है तो उस पर
                बिल्कुल भी प्रतिक्रिया न दें।</span>
        </div>
    </div>

    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/fonts/custom-font.js') }}"></script>
    <script src="{{ asset('assets/js/pcoded.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>

    @stack('script-page')

    <script>
        font_change('Roboto');

        document.addEventListener('DOMContentLoaded', function() {
            var loader = document.getElementById('loader');
            if (loader) {
                loader.classList.add('hidden');
                setTimeout(function() {
                    loader.style.display = 'none';
                }, 200);
            }
        });
    </script>
</body>

</html>
