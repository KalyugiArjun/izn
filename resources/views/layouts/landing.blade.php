<!DOCTYPE html>
<html lang="en">

@php
    use ColorThief\ColorThief;

    // =========================================================================
    // Logo se palette extract karein (fallback safe)
    // =========================================================================
    $logoRelativePath = 'upload/logo/landing_logo.png';
    $logoFullPath = public_path(Storage::url($logoRelativePath));

    if (file_exists($logoFullPath)) {
        try {
            $palette = ColorThief::getPalette($logoFullPath, 5);
        } catch (\Exception $e) {
            $palette = [[15, 44, 84], [212, 169, 55], [95, 168, 220], [248, 249, 250], [22, 48, 92]];
        }
    } else {
        $palette = [[15, 44, 84], [212, 169, 55], [95, 168, 220], [248, 249, 250], [22, 48, 92]];
    }

    if (!function_exists('rgbToHex')) {
        function rgbToHex($rgb)
        {
            return sprintf('#%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2]);
        }
    }

    $primaryHex = rgbToHex($palette[0]);
    $secondaryHex = rgbToHex($palette[1]);
    $accentHex = rgbToHex($palette[2]);
    $surfaceHex = rgbToHex($palette[3] ?? [248, 249, 250]);
    $inkHex = rgbToHex($palette[4] ?? [22, 48, 92]);

    $primaryLuminance = (0.299 * $palette[0][0] + 0.587 * $palette[0][1] + 0.114 * $palette[0][2]) / 255;
    $textOnPrimary = $primaryLuminance > 0.5 ? '#111111' : '#ffffff';
    $invertLogo = 0.4 > $primaryLuminance ? 'filter: brightness(0) invert(1);' : '';

    $profile = asset(Storage::url('upload/profile'));
    $settings = settings();
    $copyright = getSettingsValByName('copyright');
    $user = \App\Models\User::find(1);
    \App::setLocale($user ? $user->lang : 'english');
@endphp

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ !empty($settings['app_name']) ? $settings['app_name'] : env('APP_NAME') }} - @yield('page-title')</title>
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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="icon" href="{{ asset(Storage::url('upload/logo/logo.jpeg')) }}" type="image/x-icon" />

    <link href="{{ asset('assets/css/plugins/animate.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/plugins/swiper-bundle.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap"
        id="main-font-link" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" />
    <link rel="stylesheet" href="{{ asset('assets/fonts/phosphor/duotone/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/owl.carousel.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link" />

    @if (!empty($settings['custom_color']) && $settings['color_type'] == 'custom')
        <link rel="stylesheet" id="Pstylesheet" href="{{ asset('assets/css/custom-color.css') }}" />
        <script src="{{ asset('js/theme-pre-color.js') }}"></script>
    @else
        <link rel="stylesheet" id="Pstylesheet" href="{{ asset('assets/css/style-preset.css') }}" />
    @endif

    <link rel="stylesheet" href="{{ asset('assets/css/landing.css') }}" />
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">

    <style>
        :root {
            --color-primary: {{ $primaryHex }};
            --color-secondary: {{ $secondaryHex }};
            --color-accent: {{ $accentHex }};
            --color-surface: #f8f9fa;
            --color-ink: #1a1a2e;
            --color-text-on-primary: {{ $textOnPrimary }};
            --font-display: 'Poppins', 'Segoe UI', sans-serif;
            --font-body: 'Inter', 'Segoe UI', sans-serif;
            --font-mono: 'JetBrains Mono', 'Courier New', monospace;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background-color: #ffffff;
            color: var(--color-ink);
            font-family: var(--font-body);
            line-height: 1.7;
            overflow-x: hidden;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        .h1 {
            font-family: var(--font-display);
            color: var(--color-primary);
            letter-spacing: -0.02em;
            font-weight: 700;
        }

        /* ============ LOADER ============ */
        #loader {
            position: fixed;
            inset: 0;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 99999;
        }

        .loader-box {
            position: relative;
            width: 120px;
            height: 120px;
        }

        .loader-spinner {
            position: absolute;
            inset: 0;
            width: 120px;
            height: 120px;
            border-top: 3px solid #d4a937;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .loader-logo {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60px;
            height: 60px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .loader-logo img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 30px !important;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* ============ NAVBAR ============ */
        .navbar {
            padding: 12px 0;
            transition: all 0.4s ease;
            background: transparent !important;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1030;
        }

        .navbar.default {
            background: rgba({{ $palette[0][0] }}, {{ $palette[0][1] }}, {{ $palette[0][2] }}, 0.95) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.15);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .navbar.top-nav-collapse {
            background: rgba({{ $palette[0][0] }}, {{ $palette[0][1] }}, {{ $palette[0][2] }}, 0.98) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
            padding: 8px 0;
        }

        .navbar-brand.landing-logo {
            display: flex;
            align-items: center;
        }

        .navbar-brand.landing-logo img {
            max-height: 90px;
            width: auto;
            height: auto;
            object-fit: contain;
            border-radius: 15px !important;
        }

        .navbar .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 8px 18px !important;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
            font-size: 0.95rem;
        }

        .navbar .nav-link::after {
            content: '';
            position: absolute;
            bottom: 2px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: var(--color-secondary);
            transition: width 0.3s ease;
            border-radius: 2px;
        }
        .navbar .nav-link:hover::after,
        .navbar .nav-link.active::after {
            width: 60%;
        }
        .navbar .nav-link:hover,
        .navbar .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff !important;
        }
        .navbar .btn-secondary {
            background: var(--color-secondary) !important;
            border: none !important;
            color: #111111 !important;
            font-weight: 700;
            padding: 10px 28px;
            border-radius: 50px;
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.25);
            transition: all 0.4s ease;
            letter-spacing: 0.3px;
            position: relative;
            overflow: hidden;
        }

        .navbar .btn-secondary::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .navbar .btn-secondary:hover::after {
            width: 300px;
            height: 300px;
        }

        .navbar .btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.35);
            background: #ffffff !important;
            color: var(--color-primary) !important;
        }

        /* ============ SLIDER ============ */
        .feature-slider-section {
            padding: 0;
            width: 100%;
            position: relative;
        }

        .slider-wrapper {
            position: relative;
            width: 100%;
            overflow: hidden;
            border-radius: 0;
        }

        .slider-track {
            display: flex;
            transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            will-change: transform;
        }

        .slider-slide {
            min-width: 100%;
            position: relative;
            height: 70vh;
            min-height: 200px !important;
        }

        .slider-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .slider-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            background: linear-gradient(
                90deg,
                rgba(10, 25, 48, 0.55) 0%,
                rgba(10, 25, 48, 0.30) 42%,
                rgba(10, 25, 48, 0.08) 75%,
                rgba(10, 25, 48, 0) 100%
            );
        }

        .slider-content {
            position: relative;
            width: auto;
            max-width: 700px;
            margin-left: 100px;
            padding: 30px 35px;
            background: transparent !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            border: none !important;
            border-left: 4px solid #f2c65d !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            animation: slideInLeft 0.8s ease both;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-60px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .slider-title {
            font-family: var(--font-display);
            font-size: clamp(2.2rem, 5vw, 4rem);
            font-weight: 800;
            color: #ffffff !important;
            line-height: 1.15;
            margin-bottom: 20px;
            letter-spacing: -0.02em;
            text-shadow: 0 3px 16px rgba(0, 0, 0, 0.75);
        }

        .slider-title::after {
            content: '';
            display: block;
            width: 68px;
            height: 4px;
            margin-top: 18px;
            background: #f2c65d;
            border-radius: 4px;
        }

        .slider-title-brand {
            color: #f2c65d !important;
            font-weight: 800;
            display: block;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-shadow: 0 2px 12px rgba(0, 0, 0, 0.4);
        }

        .slider-title-separator {
            display: block;
            width: 68px;
            height: 4px;
            background: #f2c65d;
            margin: 15px 0 20px;
            border-radius: 4px;
        }

        .slider-title-sub {
            font-size: clamp(1.2rem, 3.2vw, 2.2rem);
            font-weight: 600;
            display: block;
            color: #ffffff !important;
            line-height: 1.35;
            text-shadow: 0 3px 14px rgba(0, 0, 0, 0.75);
        }

        .slider-title.has-brand-split::after {
            display: none !important;
        }

        .slider-subtitle {
            font-family: var(--font-body);
            font-size: clamp(1rem, 1.8vw, 1.25rem);
            font-weight: 500;
            color: #ffffff !important;
            margin-bottom: 28px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.85);
            line-height: 1.8;
        }

        .slider-btn-cta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 13px 30px;
            background: #f2c65d !important;
            color: #102642 !important;
            font-size: 1rem;
            font-weight: 700;
            border-radius: 8px;
            text-decoration: none;
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            border: 1px solid #f2c65d;
        }

        .slider-btn-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 9px 25px rgba(0, 0, 0, 0.25);
            background: #ffffff !important;
            border-color: #ffffff;
            color: #102642 !important;
        }

        .slider-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, 0.65);
            background: rgba(16, 38, 66, 0.78);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            color: #ffffff !important;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 24px;
            font-weight: 700;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .slider-btn:hover {
            background: #f2c65d;
            border-color: #f2c65d;
            color: #102642 !important;
            transform: translateY(-50%) scale(1.08);
        }

        .slider-btn-prev { left: 30px; }
        .slider-btn-next { right: 30px; }

        .slider-dots {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 12px;
            z-index: 10;
        }

        .slider-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.8);
            background: transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 0;
        }

        .slider-dot.active {
            background: #f2c65d;
            border-color: #f2c65d;
            transform: scale(1.25);
            box-shadow: 0 0 12px rgba(242, 198, 93, 0.5);
        }

        .slider-counter {
            position: absolute;
            bottom: 75px;
            left: 50%;
            transform: translateX(-50%);
            color: rgba(255, 255, 255, 0.85);
            font-size: 0.9rem;
            z-index: 10;
            font-weight: 500;
            letter-spacing: 2px;
            font-family: var(--font-mono);
        }

        /* ============ SECTIONS ============ */
        section {
            padding: 80px 0;
        }

        section.bg-body {
            background: #f8f9fa;
            padding: 80px 0;
        }

        .feature-illustration {
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.06);
            transition: all 0.5s ease;
            max-width: 80%;
        }

        .feature-illustration:hover {
            transform: scale(1.03) translateY(-8px);
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.1);
        }

        .customize-list {
            padding: 0;
            list-style: none;
        }

        .customize-list li {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 20px;
            margin-bottom: 10px;
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
            transition: all 0.4s ease;
            font-weight: 500;
            color: #2d3436;
            opacity: 0;
            animation: fadeInUp 0.6s ease forwards;
        }

        .customize-list li:nth-child(1) { animation-delay: 0.1s; }
        .customize-list li:nth-child(2) { animation-delay: 0.2s; }
        .customize-list li:nth-child(3) { animation-delay: 0.3s; }
        .customize-list li:nth-child(4) { animation-delay: 0.4s; }
        .customize-list li:nth-child(5) { animation-delay: 0.5s; }

        .customize-list li:hover {
            transform: translateX(10px) scale(1.02);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            background: #ffffff;
        }

        .customize-list li i {
            background: var(--color-secondary);
            color: #111111;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .customize-list li:hover i {
            transform: rotate(15deg) scale(1.1);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .text-lg {
            font-size: 1.1rem;
            color: #636e72;
            line-height: 1.8;
        }

        /* ============ MESSAGES ============ */
        .message-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.4s ease;
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        }

        .message-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
        }

        .message-card .card-body {
            padding: 35px 40px;
        }

        /* ============ FAQ ============ */
        .frameworks-section {
            background: #ffffff;
            padding: 80px 0;
        }

        .accordion-item {
            border: none !important;
            margin-bottom: 14px;
            border-radius: 16px !important;
            overflow: hidden;
            box-shadow: 0 2px 16px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .accordion-item:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        .accordion-button {
            padding: 22px 28px !important;
            font-weight: 600;
            font-size: 1.05rem;
            background: #ffffff !important;
            color: var(--color-primary) !important;
            border: none !important;
            border-left: 4px solid transparent !important;
            transition: all 0.3s ease;
        }

        .accordion-button:not(.collapsed) {
            background: #ffffff !important;
            color: var(--color-primary) !important;
            border-left-color: var(--color-secondary) !important;
            box-shadow: none !important;
        }

        .accordion-button:focus {
            box-shadow: none !important;
            border-color: transparent !important;
        }

        .accordion-button::after {
            color: var(--color-secondary);
            font-weight: 700;
        }

        .accordion-body {
            padding: 0 28px 28px !important;
            color: #636e72 !important;
            line-height: 1.8;
            font-size: 0.98rem;
        }

        /* ============ FOOTER ============ */
        footer.bg-dark {
            background: var(--color-primary) !important;
            padding-top: 60px;
        }

        footer h4 {
            color: #ffffff !important;
            font-weight: 700;
            margin-top: 20px;
        }

        footer h5 {
            color: var(--color-secondary) !important;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 24px !important;
            position: relative;
            padding-bottom: 12px;
            font-size: 1.1rem;
        }

        footer h5::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background: var(--color-secondary);
            border-radius: 2px;
        }

        .footer-link {
            padding: 0;
            list-style: none;
        }

        .footer-link li { margin-bottom: 12px; }

        .footer-link a {
            color: rgba(255, 255, 255, 0.7) !important;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            display: inline-block;
        }

        .footer-link a:hover {
            color: var(--color-secondary) !important;
            padding-left: 8px;
            transform: translateX(4px);
        }

        .sub-footer {
            background: {{ rgbToHex([intval($palette[0][0] * 0.75), intval($palette[0][1] * 0.75), intval($palette[0][2] * 0.75)]) }};
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding: 18px 0;
            margin-top: 40px;
        }

        .sub-footer p {
            color: rgba(255, 255, 255, 0.7) !important;
            font-size: 0.92rem;
        }

        .footer-sos-link {
            display: flex;
            gap: 12px;
            padding: 0;
            list-style: none;
            margin: 0;
            justify-content: center;
        }

        .footer-sos-link li a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            color: var(--color-secondary) !important;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .footer-sos-link li a:hover {
            background: var(--color-secondary);
            color: #111111 !important;
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(212, 169, 55, 0.3);
        }

        /* ============ MARQUEE ============ */
        .marquee-wrapper {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            z-index: 9999;
            background: #1a1a2e;
            border-top: 3px solid var(--color-secondary);
            box-shadow: 0 -4px 30px rgba(0, 0, 0, 0.3);
            padding: 10px 0;
        }

        .marquee-content {
            display: inline-block;
            white-space: nowrap;
            animation: marquee-scroll 30s linear infinite;
            font-size: 15px;
            padding-left: 100%;
            font-weight: 500;
        }

        @keyframes marquee-scroll {
            from { transform: translateX(0%); }
            to { transform: translateX(-100%); }
        }

        .marquee-wrapper:hover .marquee-content {
            animation-play-state: paused;
        }

        .marquee-content .text-danger {
            color: var(--color-secondary) !important;
            font-weight: 700;
            margin-right: 20px;
        }

        .marquee-content .text-dark {
            color: #ffffff !important;
            opacity: 0.9;
        }

        /* ============ SCROLL TO TOP ============ */
        #scrollTopBtn {
            display: none;
            position: fixed;
            bottom: 80px;
            right: 30px;
            z-index: 999;
            background: var(--color-secondary);
            color: #111111;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        #scrollTopBtn:hover {
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
        }

        
/* ============ ADMIN MANAGED PREMIUM POPUP ============ */
.premium-popup-overlay {
    position: fixed;
    inset: 0;
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: rgba(0, 0, 0, 0.42);
    backdrop-filter: none;
    -webkit-backdrop-filter: none;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    overflow-y: auto;
    transition: opacity 0.3s ease, visibility 0.3s ease;
}

.premium-popup-overlay.active {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}

.premium-popup {
    position: relative;
    width: min(800px, 94vw);
    max-height: 88vh;
    overflow-y: auto;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 12px 45px rgba(0, 0, 0, 0.3);
    transform: scale(0.96) translateY(12px);
    transition: transform 0.3s ease;
}

.premium-popup-overlay.active .premium-popup {
    transform: scale(1) translateY(0);
}

.premium-popup-close {
    position: sticky;
    float: right;
    top: 10px;
    margin: 10px 10px -46px 0;
    width: 36px;
    height: 36px;
    border: 1px solid #d9d9d9;
    border-radius: 50%;
    background: #fff;
    color: #222;
    font-size: 25px;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 20;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
}

.premium-popup-close:hover {
    background: #f1f1f1;
}

.premium-popup-header {
    position: relative;
    padding: 0;
    text-align: center;
    background: #fff;
}

.premium-popup-header::after {
    display: none;
}

.premium-popup-logo {
    display: block;
    width: 100%;
    height: auto;
    max-height: 72vh;
    object-fit: contain;
    margin: 0 auto;
    padding: 0;
    border-radius: 0;
    background: #fff;
    box-shadow: none;
}

.premium-popup-title {
    padding: 18px 24px 0;
    margin: 0;
    color: var(--color-primary);
    font-family: var(--font-display);
    font-size: 1.5rem;
    font-weight: 700;
}

.premium-popup-subtitle {
    padding: 5px 24px 15px;
    margin: 0;
    color: #555;
    font-size: 1rem;
}

.premium-popup-body {
    padding: 20px 28px;
    text-align: center;
}

.premium-popup-message {
    margin: 0 0 10px;
    color: #2d3436;
    font-size: 1rem;
    line-height: 1.7;
}

.premium-popup-note {
    margin-top: 12px;
    color: #636e72;
    font-size: 0.9rem;
}

.premium-popup-footer {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 12px;
    padding: 0 24px 24px;
}

.premium-popup-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 11px 25px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 0.95rem;
    text-decoration: none;
    cursor: pointer;
    transition: background 0.2s ease;
}

.premium-popup-btn-primary {
    border: 1px solid var(--color-secondary);
    background: var(--color-secondary);
    color: #111;
}

.premium-popup-btn-primary:hover {
    background: #f2c65d;
    color: #111;
}

.premium-popup-btn-secondary {
    border: 1px solid #ddd;
    background: #fff;
    color: var(--color-primary);
}

.premium-popup-btn-secondary:hover {
    background: #f5f5f5;
}

@media (max-width: 600px) {
    .premium-popup-overlay {
        padding: 12px;
    }

    .premium-popup {
        width: 100%;
        max-height: 90vh;
        border-radius: 7px;
    }

    .premium-popup-logo {
        max-height: 72vh;
    }

    .premium-popup-body {
        padding: 16px;
    }

    .premium-popup-footer {
        padding: 0 16px 18px;
        flex-direction: column;
    }

    .premium-popup-btn {
        width: 100%;
    }
}

        /* ============ RESPONSIVE ============ */
        @media (max-width: 992px) {
            .slider-content {
                margin-left: 65px;
                padding: 25px 28px;
                max-width: 78%;
            }

            .slider-slide {
                height: 70vh;
                min-height: 400px;
            }

            .slider-title {
                font-size: clamp(2rem, 4.5vw, 3.2rem);
            }

            section,
            section.bg-body {
                padding: 50px 0;
            }
        }

        @media (max-width: 768px) {
            .navbar { padding: 8px 0 !important; }

            .navbar-brand.landing-logo img { max-height: 40px; }

            .slider-overlay {
                background: linear-gradient(
                    90deg,
                    rgba(10, 25, 48, 0.55) 0%,
                    rgba(10, 25, 48, 0.28) 60%,
                    rgba(10, 25, 48, 0.08) 100%
                );
            }

            .slider-content {
                width: auto;
                margin: 0 auto;
                padding: 20px 18px;
                max-width: calc(100% - 65px);
                background: transparent !important;
                backdrop-filter: none !important;
                -webkit-backdrop-filter: none !important;
                border: none !important;
                border-left: 3px solid #f2c65d !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }

            .slider-slide {
                height: 60vh;
                min-height: 350px;
            }

            .slider-title {
                font-size: 1.8rem !important;
                margin-bottom: 15px;
            }

            .slider-title::after {
                width: 50px;
                height: 3px;
            }

            .slider-title-separator {
                width: 50px;
                height: 3px;
                margin: 12px 0 15px;
            }

            .slider-title-sub {
                font-size: 1.2rem !important;
                font-weight: 600;
                margin-top: 6px;
            }

            .slider-subtitle {
                font-size: 0.95rem !important;
                margin-bottom: 18px;
                line-height: 1.65;
            }

            .slider-btn-cta {
                padding: 10px 20px;
                font-size: 0.9rem;
            }

            .slider-btn { display: none !important; }

            .slider-dots {
                bottom: 15px;
                gap: 8px;
            }

            .slider-dot {
                width: 10px;
                height: 10px;
            }

            .slider-counter {
                bottom: 40px;
                font-size: 0.75rem;
            }

            section,
            section.bg-body {
                padding: 40px 0;
            }

            .feature-illustration {
                max-width: 100%;
                margin: 20px 0;
            }

            .customize-list li {
                padding: 12px 16px;
                font-size: 0.92rem;
            }

            .message-card .card-body { padding: 25px 20px; }

            .accordion-button {
                padding: 16px 20px !important;
                font-size: 0.95rem;
            }

            .accordion-body {
                padding: 0 20px 20px !important;
                font-size: 0.9rem;
            }

            footer { padding-top: 40px !important; }

            .sub-footer {
                padding: 14px 0;
                text-align: center;
            }

            .sub-footer .row {
                flex-direction: column;
                gap: 10px;
            }

            .marquee-content {
                font-size: 12px;
                animation-duration: 20s;
            }

            #scrollTopBtn {
                width: 40px;
                height: 40px;
                font-size: 18px;
                bottom: 70px;
                right: 15px;
            }
        }

        @media (max-width: 576px) {
            .premium-popup {
                max-width: 94%;
                border-radius: 16px;
            }
            .premium-popup-header {
                padding: 24px 20px 20px;
            }
            .premium-popup-title {
                font-size: 1.3rem;
            }
            .premium-popup-body {
                padding: 22px 20px 16px;
            }
            .premium-popup-footer {
                padding: 0 20px 22px;
                flex-direction: column;
            }
            .premium-popup-btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .slider-content {
                padding: 16px 14px;
                margin: 0 15px;
                max-width: calc(100% - 40px);
                background: transparent !important;
                backdrop-filter: none !important;
                -webkit-backdrop-filter: none !important;
                border-left: 3px solid #f2c65d !important;
            }

            .slider-title {
                font-size: 1.45rem !important;
                margin-bottom: 12px;
            }

            .slider-title-brand { letter-spacing: 0.5px; }

            .slider-title-separator {
                width: 42px;
                height: 3px;
                margin: 10px 0 12px;
            }

            .slider-title-sub {
                font-size: 1rem !important;
                font-weight: 600;
                margin-top: 4px;
                line-height: 1.35;
            }

            .slider-subtitle {
                font-size: 0.85rem !important;
                margin-bottom: 15px;
                line-height: 1.55;
            }

            .slider-btn-cta {
                padding: 9px 16px;
                font-size: 0.85rem;
                gap: 6px;
                border-radius: 6px;
            }

            .slider-btn-cta svg {
                width: 15px;
                height: 15px;
            }

            .slider-slide {
                height: 50vh;
                min-height: 280px;
            }

            .slider-btn {
                width: 32px;
                height: 32px;
                font-size: 14px;
            }

            .customize-list li {
                padding: 10px 14px;
                font-size: 0.85rem;
            }

            .customize-list li i {
                width: 22px;
                height: 22px;
                font-size: 12px;
            }
        }
    </style>
</head>

<body class="landing-page"
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

    <!-- ========== PREMIUM POPUP ========== -->
    
@if (isset($popup) && $popup)
    <div class="premium-popup-overlay" id="premiumPopup"
         aria-hidden="true">
        <div class="premium-popup" role="dialog"
             aria-modal="true"
             aria-label="{{ $popup->title }}">

            <button type="button"
                    class="premium-popup-close"
                    onclick="closePremiumPopup()"
                    aria-label="Close popup">
                &times;
            </button>

            @if ($popup->image)
                <div class="premium-popup-header">
                    <img src="{{ $popup->image_url }}"
                         alt="{{ $popup->title }}"
                         class="premium-popup-logo">
                </div>
            @else
                <div class="premium-popup-header">
                    @if ($popup->title)
                        <h3 class="premium-popup-title">
                            {{ $popup->title }}
                        </h3>
                    @endif

                    @if ($popup->subtitle)
                        <p class="premium-popup-subtitle">
                            {{ $popup->subtitle }}
                        </p>
                    @endif
                </div>
            @endif

            @if ($popup->image && ($popup->title || $popup->subtitle))
                <div class="text-center px-3 pt-3">
                    @if ($popup->title)
                        <h3 class="premium-popup-title">
                            {{ $popup->title }}
                        </h3>
                    @endif

                    @if ($popup->subtitle)
                        <p class="premium-popup-subtitle">
                            {{ $popup->subtitle }}
                        </p>
                    @endif
                </div>
            @endif

            @if ($popup->message || $popup->note)
                <div class="premium-popup-body">
                    @if ($popup->message)
                        <div class="premium-popup-message">
                            {!! nl2br(e($popup->message)) !!}
                        </div>
                    @endif

                    @if ($popup->note)
                        <p class="premium-popup-note">
                            {{ $popup->note }}
                        </p>
                    @endif
                </div>
            @endif

            @if ($popup->button_text)
                <div class="premium-popup-footer">
                    <a href="{{ $popup->button_url ?: '#' }}"
                       class="premium-popup-btn premium-popup-btn-primary">
                        {{ $popup->button_text }}
                    </a>
                </div>
            @endif
        </div>
    </div>
@endif

    <!-- ========== NAVBAR ========== -->
    <nav class="navbar navbar-expand-md navbar-light default">
        <div class="container">
            <a class="navbar-brand landing-logo" href="{{ route('home') }}">
                <img src="{{ asset(Storage::url('upload/logo/logo.jpeg')) }}" alt="NER Logo" class="img-fluid" />
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center gap-1">
                    @php
                        $HomePage = App\Models\HomePage::where('section', 'Section 0')->first();
                    @endphp

                    @if (!empty($HomePage->content_value))
                        @php
                            $HomePage = json_decode($HomePage->content_value, true);
                            $active_menus = !empty($HomePage['menu_pages']) ? $HomePage['menu_pages'] : [];
                        @endphp

                        @foreach ($menus as $menu)
                            @if (in_array($menu->id, $active_menus))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('page', $menu->slug) }}">{{ $menu->title }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif

                    <li class="nav-item">
                        <a class="btn btn-secondary" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ========== HERO SLIDER ========== -->
    @php
        $sliders = App\Models\Slider::where('status', 1)->orderBy('sort_order')->get();
    @endphp

    @if ($sliders->isNotEmpty())
        <section class="feature-slider-section" id="featureSliderSection">
            <div class="slider-wrapper">
                <div class="slider-track" id="featureSliderTrack">
                    @foreach ($sliders as $slider)
                        <div class="slider-slide">
                            <img loading="lazy" src="{{ $slider->image_url }}"
                                alt="{{ $slider->title ?? 'Slide' }}" class="slider-image"
                                onerror="this.onerror=null; this.src='{{ asset('storage/app/public/' . $slider->image) }}';" />

                            @if ($slider->title || $slider->subtitle || $slider->button_text)
                                <div class="slider-overlay">
                                    <div class="slider-content">
                                        @if ($slider->title)
                                            @php
                                                $title = $slider->title;
                                                $hasBrand = (strpos(strtoupper($title), 'SAATHI') === 0);

                                                if ($hasBrand) {
                                                    $saathi = substr($title, 0, 6);
                                                    $rest = substr($title, 6);
                                                    $formattedTitle = '<span class="slider-title-brand">' . e($saathi) . '</span><span class="slider-title-separator"></span><span class="slider-title-sub">' . e(trim($rest)) . '</span>';
                                                } else {
                                                    $formattedTitle = e($title);
                                                }
                                            @endphp

                                            <h2 class="slider-title {{ $hasBrand ? 'has-brand-split' : '' }}">{!! $formattedTitle !!}</h2>
                                        @endif

                                        @if ($slider->subtitle)
                                            <p class="slider-subtitle">{{ $slider->subtitle }}</p>
                                        @endif

                                        @if ($slider->button_text)
                                            <a href="{{ $slider->button_url ?? '#' }}" class="slider-btn-cta"
                                                {{ $slider->button_url ? 'target=_blank' : '' }}>
                                                {{ $slider->button_text }}
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                                    <polyline points="12 5 19 12 12 19"></polyline>
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <button class="slider-btn slider-btn-prev" id="sliderPrev" aria-label="Previous">‹</button>
                <button class="slider-btn slider-btn-next" id="sliderNext" aria-label="Next">›</button>

                <div class="slider-dots">
                    @foreach ($sliders as $i => $slider)
                        <button class="slider-dot {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}"
                            aria-label="Slide {{ $i + 1 }}"></button>
                    @endforeach
                </div>
            </div>
        </section>

        <script>
            function adjustSliderTop() {
                const navbar = document.querySelector('nav.navbar');
                const section = document.getElementById('featureSliderSection');

                if (navbar && section) {
                    section.style.marginTop = navbar.offsetHeight + 'px';
                }
            }

            document.addEventListener('DOMContentLoaded', adjustSliderTop);
            window.addEventListener('resize', adjustSliderTop);

            (function() {
                var track = document.getElementById('featureSliderTrack');
                var dots = document.querySelectorAll('.slider-dot');
                var prevBtn = document.getElementById('sliderPrev');
                var nextBtn = document.getElementById('sliderNext');
                var currentSlideSpan = document.getElementById('currentSlide');
                var total = {{ $sliders->count() }};
                var current = 0,
                    autoTimer;

                function goTo(i) {
                    current = (i + total) % total;
                    track.style.transform = 'translateX(-' + (current * 100) + '%)';

                    dots.forEach(function(d, j) {
                        if (j === current) {
                            d.classList.add('active');
                        } else {
                            d.classList.remove('active');
                        }
                    });

                    if (currentSlideSpan) {
                        var num = String(current + 1);
                        if (num.length < 2) num = '0' + num;
                        currentSlideSpan.textContent = num;
                    }
                }

                function next() {
                    goTo(current + 1);
                }

                function prev() {
                    goTo(current - 1);
                }

                function startAuto() {
                    autoTimer = setInterval(next, 4000);
                }

                function resetAuto() {
                    clearInterval(autoTimer);
                    startAuto();
                }

                nextBtn.addEventListener('click', function() {
                    next();
                    resetAuto();
                });

                prevBtn.addEventListener('click', function() {
                    prev();
                    resetAuto();
                });

                dots.forEach(function(dot) {
                    dot.addEventListener('click', function() {
                        goTo(parseInt(this.dataset.index));
                        resetAuto();
                    });
                });

                if (total <= 1) {
                    prevBtn.style.display = 'none';
                    nextBtn.style.display = 'none';

                    if (dots[0] && dots[0].parentElement) {
                        dots[0].parentElement.style.display = 'none';
                    }

                    if (currentSlideSpan) {
                        currentSlideSpan.parentElement.style.display = 'none';
                    }
                } else {
                    startAuto();
                }
            })();
        </script>
    @else
        <script>
            function adjustBodyTop() {
                const navbar = document.querySelector('nav.navbar');

                if (navbar) {
                    document.body.style.paddingTop = navbar.offsetHeight + 'px';
                }
            }

            document.addEventListener('DOMContentLoaded', adjustBodyTop);
            window.addEventListener('resize', adjustBodyTop);
        </script>
    @endif

    <!-- ========== MESSAGES SECTION ========== -->
    @php
        $messages = App\Models\Message::where('status', 1)->orderBy('sort_order')->get();
    @endphp

    @if ($messages->isNotEmpty())
        <section style="background: #f8f9fa; padding: 80px 0;">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 style="font-size: 2.5rem; font-weight: 800; color: var(--color-primary);">Messages</h2>
                    <p style="color: #636e72; font-size: 1.1rem;">Important updates and announcements</p>
                </div>

                <div class="d-flex flex-column gap-4">
                    @foreach ($messages as $msg)
                        <div class="message-card">
                            <div class="row g-0 align-items-center">
                                @if ($msg->image_url)
                                    <div class="col-md-3 col-lg-2">
                                        <img loading="lazy" src="{{ $msg->image_url }}" alt="{{ $msg->title }}"
                                            class="img-fluid"
                                            style="object-fit: cover; width: 100%; height: 100%; min-height: 250px; max-height: 320px;">
                                    </div>
                                @endif

                                <div class="col-md-{{ $msg->image_url ? '9' : '12' }} col-lg-{{ $msg->image_url ? '10' : '12' }}">
                                    <div class="card-body">
                                        <h4 style="color: #2d3436; font-weight: 700; margin-bottom: 4px;">
                                            {{ $msg->title }}
                                        </h4>

                                        @if ($msg->designation)
                                            <p style="color: var(--color-secondary); font-weight: 600; margin-bottom: 12px;">
                                                {{ $msg->designation }}
                                            </p>
                                        @endif

                                        <p style="color: #636e72; line-height: 1.9; margin: 0;">{!! $msg->message !!}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- ========== FEATURES SECTION ========== -->
    @php
        $Section_3 = App\Models\HomePage::where('section', 'Section 3')->first();
        $Section_3_content_value = !empty($Section_3->content_value)
            ? json_decode($Section_3->content_value, true)
            : [];
    @endphp

    @if (empty($Section_3_content_value['section_enabled']) || $Section_3_content_value['section_enabled'] == 'active')
        <section style="background: #ffffff; padding: 80px 0;">
            <div class="container">
                @for ($is3 = 1; $is3 <= 2; $is3++)
                    <div class="row align-items-center g-5" style="margin-bottom: {{ $is3 == 1 ? '60px' : '0' }};">
                        @if ($is3 % 2 != 0)
                            <div class="col-md-6 text-center">
                                @if (!empty($Section_3_content_value['Box' . $is3 . '_image_path']))
                                    <img loading="lazy"
                                        src="{{ asset(Storage::url($Section_3_content_value['Box' . $is3 . '_image_path'])) }}"
                                        alt="Feature" class="feature-illustration" />
                                @else
                                    <img loading="lazy" src="{{ asset('storage/homepage/img-customize-1.svg') }}"
                                        alt="Feature" class="feature-illustration">
                                @endif
                            </div>
                        @endif

                        <div class="col-md-6">
                            <h2 style="font-size: clamp(2rem, 3.5vw, 2.8rem); font-weight: 800; color: var(--color-primary);">
                                {{ !empty($Section_3_content_value['Box' . $is3 . '_title']) ? $Section_3_content_value['Box' . $is3 . '_title'] : 'Empower Your Business to Thrive with Us' }}
                            </h2>

                            <p class="text-lg" style="margin: 20px 0 30px;">
                                {{ !empty($Section_3_content_value['Box' . $is3 . '_info']) ? $Section_3_content_value['Box' . $is3 . '_info'] : 'Unlock growth, streamline operations, and achieve success with our innovative solutions.' }}
                            </p>

                            <ul class="customize-list">
                                @if (!empty($Section_3_content_value['Box' . $is3 . '_list']))
                                    @foreach ($Section_3_content_value['Box' . $is3 . '_list'] as $box_item)
                                        <li><i class="ti ti-circle-check"></i> {{ $box_item }}</li>
                                    @endforeach
                                @else
                                    <li><i class="ti ti-circle-check"></i> Simplify and automate your work processes</li>
                                    <li><i class="ti ti-circle-check"></i> Receive tailored strategies for your needs</li>
                                    <li><i class="ti ti-circle-check"></i> Grow confidently with flexible solutions</li>
                                    <li><i class="ti ti-circle-check"></i> Make smarter decisions with real-time analytics</li>
                                    <li><i class="ti ti-circle-check"></i> 24/7 expert assistance for smooth operations</li>
                                @endif
                            </ul>
                        </div>

                        @if ($is3 % 2 == 0)
                            <div class="col-md-6 text-center">
                                @if (!empty($Section_3_content_value['Box' . $is3 . '_image_path']))
                                    <img loading="lazy"
                                        src="{{ asset(Storage::url($Section_3_content_value['Box' . $is3 . '_image_path'])) }}"
                                        alt="Feature" class="feature-illustration" />
                                @else
                                    <img loading="lazy" src="{{ asset('storage/homepage/img-customize-2.svg') }}"
                                        alt="Feature" class="feature-illustration">
                                @endif
                            </div>
                        @endif
                    </div>
                @endfor
            </div>
        </section>

        <!-- ========== SAFETY MARQUEE ========== -->
        <div class="marquee-wrapper">
            <div class="marquee-content">
                <span class="text-danger">🔔 NOTICE:</span>
                <span class="text-dark">कृपया ध्यान दें... पासवर्ड बदलने हेतु OTP, लिंक या संदेश नहीं भेजा जाता।
                    सुरक्षा के लिए किसी भी अनधिकृत संदेश पर ध्यान न दें।</span>
                <span style="color: rgba(255,255,255,0.3); margin: 0 20px;">|</span>
                <span class="text-danger">🔔 NOTICE:</span>
                <span class="text-dark">Password reset OTP is never shared via email or SMS. Stay safe!</span>
            </div>
        </div>
    @endif

    <!-- ========== FAQ SECTION ========== -->
    @php
        $Section_9 = App\Models\HomePage::where('section', 'Section 9')->first();
        $Section_9_content_value = !empty($Section_9->content_value)
            ? json_decode($Section_9->content_value, true)
            : [];
    @endphp

    @if (empty($Section_9_content_value['section_enabled']) || $Section_9_content_value['section_enabled'] == 'active')
        <section class="frameworks-section" id="faqs">
            <div class="container">
                <div class="row justify-content-center" style="margin-bottom: 50px;">
                    <div class="col-md-9 col-lg-6 text-center">
                        <h2 style="font-size: 2.5rem; font-weight: 800; color: var(--color-primary);">
                            {{ !empty($Section_9_content_value['Sec9_title']) ? $Section_9_content_value['Sec9_title'] : 'Frequently Asked Questions' }}
                        </h2>
                        <p style="color: #636e72; font-size: 1.1rem; margin-top: 12px;">
                            {{ !empty($Section_9_content_value['Sec9_info']) ? $Section_9_content_value['Sec9_info'] : 'Find quick answers to common questions about our platform.' }}
                        </p>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="accordion accordion-flush" id="accordionFlushExample">
                            @if (!empty($FAQs->toArray()))
                                @foreach ($FAQs as $FAQ_key => $FAQ)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-{{ $FAQ->id }}">
                                            <button class="accordion-button {{ $FAQ_key == 0 ? '' : 'collapsed' }}"
                                                type="button" data-bs-toggle="collapse"
                                                data-bs-target="#flush-collapse-{{ $FAQ->id }}"
                                                aria-expanded="{{ $FAQ_key == 0 ? 'true' : 'false' }}">
                                                <b>{{ $FAQ->question }}</b>
                                            </button>
                                        </h2>

                                        <div id="flush-collapse-{{ $FAQ->id }}"
                                            class="accordion-collapse collapse {{ $FAQ_key == 0 ? 'show' : '' }}"
                                            aria-labelledby="flush-{{ $FAQ->id }}"
                                            data-bs-parent="#accordionFlushExample">
                                            <div class="accordion-body">{!! $FAQ->description !!}</div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#default-faq">
                                            <b>What features does your software offer?</b>
                                        </button>
                                    </h2>
                                    <div id="default-faq" class="accordion-collapse collapse show">
                                        <div class="accordion-body">Our software provides a range of features including
                                            automation tools, real-time analytics, cloud-based access, secure data
                                            storage, seamless integrations, and customizable solutions tailored to your
                                            business needs.</div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#faq2">
                                            <b>Is your software easy to use?</b>
                                        </button>
                                    </h2>
                                    <div id="faq2" class="accordion-collapse collapse">
                                        <div class="accordion-body">Yes, our software is designed to be easy to use. It
                                            comes with intuitive interfaces and a user-friendly design.</div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#faq3">
                                            <b>Can I integrate your software with my existing systems?</b>
                                        </button>
                                    </h2>
                                    <div id="faq3" class="accordion-collapse collapse">
                                        <div class="accordion-body">Yes, our software is designed to integrate
                                            seamlessly with your existing systems.</div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#faq4">
                                            <b>Is my data secure with your software?</b>
                                        </button>
                                    </h2>
                                    <div id="faq4" class="accordion-collapse collapse">
                                        <div class="accordion-body">Yes, your data is secure with our software. We use
                                            industry-leading encryption and security measures to protect your data.</div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#faq5">
                                            <b>Can I customize the software to fit my business needs?</b>
                                        </button>
                                    </h2>
                                    <div id="faq5" class="accordion-collapse collapse">
                                        <div class="accordion-body">Yes, we offer customization options to help you
                                            tailor the software to fit your specific business needs.</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- ========== FOOTER ========== -->
    <footer class="bg-dark footer">
        @php
            $Section_10 = App\Models\HomePage::where('section', 'Section 10')->first();
            $Section_10_content_value = !empty($Section_10->content_value)
                ? json_decode($Section_10->content_value, true)
                : [];
        @endphp

        @if (empty($Section_10_content_value['section_enabled']) || $Section_10_content_value['section_enabled'] == 'active')
            <div class="container">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="landing-logo">
                            <img loading="lazy" src="{{ asset(Storage::url('upload/logo/logo.jpeg')) }}"
                                alt="Logo" style="max-height: 55px; border-radius: 15px;" />
                        </div>

                        <h4 style="margin: 20px 0 16px; color: #ffffff !important; font-weight: 700;">
                            {{ !empty($Section_10_content_value['Sec10_title']) ? $Section_10_content_value['Sec10_title'] : 'About Smart Tenant' }}
                        </h4>

                        <p style="color: rgba(255,255,255,0.7); line-height: 1.8;">
                            {!! !empty($Section_10_content_value['Sec10_info'])
                                ? $Section_10_content_value['Sec10_info']
                                : 'Property management, streamline operations, and achieve success with our innovative solutions tailored to your business needs.' !!}
                        </p>
                    </div>

                    <div class="col-md-8">
                        <div class="row g-4">
                            @php
                                $footer_col = 0;

                                if ($settings['footer_column_1_enabled'] == 'active') {
                                    $footer_col = 12;
                                }

                                if ($settings['footer_column_2_enabled'] == 'active') {
                                    $footer_col = 6;
                                }

                                if ($settings['footer_column_3_enabled'] == 'active') {
                                    $footer_col = 4;
                                }

                                if ($settings['footer_column_4_enabled'] == 'active') {
                                    $footer_col = 3;
                                }
                            @endphp

                            @if ($footer_col > 0)
                                @if ($settings['footer_column_1_enabled'] == 'active')
                                    <div class="col-6 col-md-{{ $footer_col }}">
                                        <h5>{{ $settings['footer_column_1'] }}</h5>
                                        <ul class="footer-link">
                                            @php
                                                $active_footer_menu1 = !empty($settings['footer_column_1_pages'])
                                                    ? json_decode($settings['footer_column_1_pages'], true)
                                                    : [];
                                            @endphp

                                            @foreach ($menus as $menu)
                                                @if (in_array($menu->id, $active_footer_menu1))
                                                    <li>
                                                        <a target="_blank" href="{{ route('page', $menu->slug) }}">
                                                            {{ $menu->title }}
                                                        </a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if ($settings['footer_column_2_enabled'] == 'active')
                                    <div class="col-6 col-md-{{ $footer_col }}">
                                        <h5>{{ $settings['footer_column_2'] }}</h5>
                                        <ul class="footer-link">
                                            @php
                                                $active_footer_menu2 = !empty($settings['footer_column_2_pages'])
                                                    ? json_decode($settings['footer_column_2_pages'], true)
                                                    : [];
                                            @endphp

                                            @foreach ($menus as $menu)
                                                @if (in_array($menu->id, $active_footer_menu2))
                                                    <li>
                                                        <a target="_blank" href="{{ route('page', $menu->slug) }}">
                                                            {{ $menu->title }}
                                                        </a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if ($settings['footer_column_3_enabled'] == 'active')
                                    <div class="col-6 col-md-{{ $footer_col }}">
                                        <h5>{{ $settings['footer_column_3'] }}</h5>
                                        <ul class="footer-link">
                                            @php
                                                $active_footer_menu3 = !empty($settings['footer_column_3_pages'])
                                                    ? json_decode($settings['footer_column_3_pages'], true)
                                                    : [];
                                            @endphp

                                            @foreach ($menus as $menu)
                                                @if (in_array($menu->id, $active_footer_menu3))
                                                    <li>
                                                        <a target="_blank" href="{{ route('page', $menu->slug) }}">
                                                            {{ $menu->title }}
                                                        </a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if ($settings['footer_column_4_enabled'] == 'active')
                                    <div class="col-6 col-md-{{ $footer_col }}">
                                        <h5>{{ $settings['footer_column_4'] }}</h5>
                                        <ul class="footer-link">
                                            @php
                                                $active_footer_menu4 = !empty($settings['footer_column_4_pages'])
                                                    ? json_decode($settings['footer_column_4_pages'], true)
                                                    : [];
                                            @endphp

                                            @foreach ($menus as $menu)
                                                @if (in_array($menu->id, $active_footer_menu4))
                                                    <li>
                                                        <a target="_blank" href="{{ route('page', $menu->slug) }}">
                                                            {{ $menu->title }}
                                                        </a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="sub-footer mb-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 my-1 text-center text-md-start">
                        <p style="margin: 0; color: rgba(255,255,255,0.7); font-size: 0.95rem;">
                            @if (!empty($settings['copyright']))
                                {{ $settings['copyright'] }}
                            @else
                                © {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.
                            @endif
                        </p>
                    </div>

                    <div class="col-md-6 my-1 text-center text-md-end">
                        <ul class="footer-sos-link" style="display: inline-flex;">
                            <li><a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a></li>
                            <li><a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a></li>
                            <li><a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a></li>
                            <li><a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- ========== SCROLL TO TOP BUTTON ========== -->
    <button id="scrollTopBtn" aria-label="Scroll to top">↑</button>

    <!-- ========== SCRIPTS ========== -->
    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/fonts/custom-font.js') }}"></script>
    <script src="{{ asset('assets/js/pcoded.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>

    <script>
        font_change('Roboto');
    </script>

    <script src="{{ asset('assets/js/plugins/wow.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/swiper-bundle.js') }}"></script>

    <script>
        // ===== Hide Loader =====
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

            // ===== Show Premium Popup after loader =====
            setTimeout(function() {
                showPremiumPopup();
            }, 900);
        });

        // ===== Premium Popup Functions =====
        function showPremiumPopup() {
            const popup = document.getElementById('premiumPopup');
            if (popup) {
                popup.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closePremiumPopup() {
            const popup = document.getElementById('premiumPopup');
            if (popup) {
                popup.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        // Close on overlay click
        document.getElementById('premiumPopup')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closePremiumPopup();
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePremiumPopup();
            }
        });

        // ===== Navbar Scroll Effect =====
        var ost = 0;

        document.addEventListener('scroll', function() {
            var cOst = document.documentElement.scrollTop;
            var navbar = document.querySelector('.navbar');

            if (!navbar) return;

            if (cOst === 0) {
                navbar.classList.add('default');
                navbar.classList.remove('top-nav-collapse');
            } else if (cOst > ost) {
                navbar.classList.add('top-nav-collapse');
                navbar.classList.remove('default');
            } else {
                navbar.classList.add('default');
                navbar.classList.remove('top-nav-collapse');
            }

            ost = cOst;
        });

        // ===== Active Menu Link =====
        document.querySelectorAll('.nav-link').forEach(function(link) {
            if (link.href === window.location.href) {
                link.classList.add('active');
            }
        });

        // ===== Scroll to Top =====
        window.onscroll = function() {
            var btn = document.getElementById('scrollTopBtn');

            if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
                btn.style.display = 'block';
            } else {
                btn.style.display = 'none';
            }
        };

        document.getElementById('scrollTopBtn').addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // ===== WOW Animations =====
        var wow = new WOW({
            animateClass: 'animated',
            offset: 50
        });

        wow.init();

        // ===== Check if Swiper exists before initializing =====
        if (typeof Swiper !== 'undefined') {
            // App Slider
            var appSliderElement = document.querySelector('.app-slider');

            if (appSliderElement) {
                var app_Swiper = new Swiper('.app-slider', {
                    loop: true,
                    slidesPerView: '1.2',
                    centeredSlides: true,
                    spaceBetween: 20,
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev'
                    }
                });
            }

            // Choose Slider
            var chooseSliderElement = document.querySelector('.choose-slider');

            if (chooseSliderElement) {
                var choose_Swiper = new Swiper('.choose-slider', {
                    direction: 'vertical',
                    loop: true,
                    centeredSlides: true,
                    slidesPerView: '4',
                    autoplay: {
                        delay: 2500,
                        disableOnInteraction: false
                    }
                });
            }

            // Frameworks Slider
            var frameworksSliderElement = document.querySelector('.frameworks-slider');

            if (frameworksSliderElement) {
                var frameworks_Swiper = new Swiper('.frameworks-slider', {
                    loop: true,
                    centeredSlides: true,
                    spaceBetween: 24,
                    slidesPerView: 2,
                    pagination: {
                        el: '.swiper-pagination',
                        dynamicBullets: true,
                        clickable: true
                    },
                    breakpoints: {
                        640: {
                            slidesPerView: 2
                        },
                        768: {
                            slidesPerView: 4
                        },
                        1024: {
                            slidesPerView: 5
                        }
                    }
                });
            }
        }
    </script>
</body>
</html>