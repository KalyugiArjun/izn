@extends('layouts.app')

@section('page-title')
    {{ __('Employee Profile') }}
@endsection

@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}"><i class="ti ti-home me-1"></i>{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item active">
            {{ __('Employee Profile') }}
        </li>
    </ul>
@endsection

@section('content')
@php
    $authUser = Auth::user();
    $profilePath = 'app/public/upload/profile/' . ($authUser->profile ?? '');
    $publicPath  = 'storage/upload/profile/' . ($authUser->profile ?? '');
    $profileExists = !empty($authUser->profile) && (file_exists(storage_path($profilePath)) || file_exists(public_path($publicPath)));
    $profileUrl = asset(Storage::url('upload/profile')) . '/' . ($authUser->profile ?? '');
    $initials   = $authUser ? $authUser->getInitials() : 'EP';

    $assignedStations = $employee->stations ?? collect([]);
    $stationCount = $assignedStations->count();

    // Smart present working station fallback
    $presentStation = $employee->posting_present_station;
    if (empty($presentStation)) {
        if ($stationCount === 1) {
            $firstSt = $assignedStations->first();
            $presentStation = $firstSt->name . (!empty($firstSt->code) ? ' (' . $firstSt->code . ')' : '');
        } elseif ($stationCount > 1) {
            $presentStation = 'Divisional Railway Manager Office (IZN)';
        }
    }

    $contactsCount = $employee->emergencyContacts ? $employee->emergencyContacts->count() : 0;
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    .profile-page-wrapper {
        font-family: 'Plus Jakarta Sans', sans-serif;
        color: #1e293b;
    }

    /* ============================================================
       1. HERO HEADER BANNER
    ============================================================ */
    .profile-hero-card {
        background: linear-gradient(135deg, #091a33 0%, #0d284f 45%, #163e72 100%);
        border-radius: 22px;
        padding: 34px 38px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 16px 36px rgba(10, 25, 47, 0.22);
        border: 1px solid rgba(255, 255, 255, 0.14);
        margin-bottom: 24px;
    }

    .profile-hero-card::before {
        content: '';
        position: absolute;
        width: 440px;
        height: 440px;
        right: -80px;
        top: -140px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(56, 189, 248, 0.22) 0%, rgba(56, 189, 248, 0) 70%);
        pointer-events: none;
    }

    .profile-hero-card::after {
        content: '';
        position: absolute;
        width: 300px;
        height: 300px;
        left: -60px;
        bottom: -120px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(224, 171, 62, 0.14) 0%, rgba(224, 171, 62, 0) 70%);
        pointer-events: none;
    }

    .profile-avatar-box {
        width: 96px;
        height: 96px;
        min-width: 96px;
        border-radius: 24px;
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 34px;
        font-weight: 800;
        border: 3.5px solid rgba(255, 255, 255, 0.85);
        box-shadow: 0 10px 28px rgba(0, 0, 0, 0.35);
        position: relative;
        flex-shrink: 0;
        overflow: hidden;
    }

    .profile-avatar-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-avatar-badge {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #10b981;
        border: 3.5px solid #091a33;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.5);
    }

    .profile-hero-name {
        color: #ffffff;
        font-size: 28px;
        font-weight: 800;
        margin-bottom: 8px;
        letter-spacing: -0.5px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .profile-hero-desig {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .desig-pill-gold {
        background: rgba(224, 171, 62, 0.2);
        color: #fef3c7;
        border: 1px solid rgba(224, 171, 62, 0.45);
        font-size: 13.5px;
        padding: 6px 14px;
        border-radius: 10px;
        font-weight: 700;
        letter-spacing: 0.3px;
        display: inline-flex;
        align-items: center;
    }

    .dept-pill-cyan {
        background: rgba(56, 189, 248, 0.16);
        color: #e0f2fe;
        border: 1px solid rgba(56, 189, 248, 0.4);
        font-size: 13px;
        padding: 6px 13px;
        border-radius: 10px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
    }

    .status-pill-green {
        background: rgba(16, 185, 129, 0.22);
        color: #a7f3d0;
        border: 1px solid rgba(16, 185, 129, 0.45);
        font-size: 13px;
        padding: 6px 13px;
        border-radius: 20px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .status-pill-red {
        background: rgba(239, 68, 68, 0.2);
        color: #fecaca;
        border: 1px solid rgba(239, 68, 68, 0.45);
        font-size: 13px;
        padding: 6px 13px;
        border-radius: 20px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #10b981;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulseGreen 1.8s infinite;
    }

    @keyframes pulseGreen {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    .profile-hero-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .hero-chip {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 6px 14px;
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: 10px;
        color: #f1f5f9;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .hero-chip:hover {
        background: rgba(255, 255, 255, 0.14);
        transform: translateY(-1px);
    }

    .hero-chip i {
        color: #38bdf8;
        font-size: 16px;
    }

    .hero-hrms-display {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.22);
        border-radius: 16px;
        padding: 14px 22px;
        text-align: center;
        backdrop-filter: blur(12px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.18);
    }

    .hero-hrms-label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: #94a3b8;
        font-weight: 700;
        display: block;
        margin-bottom: 4px;
    }

    .hero-hrms-value {
        font-size: 20px;
        font-weight: 800;
        color: #fce3a1;
        letter-spacing: 2px;
        font-family: 'Courier New', Courier, monospace;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .copy-hrms-btn {
        background: none;
        border: none;
        color: #94a3b8;
        font-size: 15px;
        cursor: pointer;
        padding: 0;
        transition: color 0.2s;
    }

    .copy-hrms-btn:hover {
        color: #ffffff;
    }

    /* ============================================================
       2. KPI MINI STATS ROW
    ============================================================ */
    .kpi-stat-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 18px 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.03);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.2s ease;
        height: 100%;
    }

    .kpi-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.07);
        border-color: #cbd5e1;
    }

    .kpi-icon-wrapper {
        width: 48px;
        height: 48px;
        min-width: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .kpi-blue { background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe; }
    .kpi-emerald { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
    .kpi-amber { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
    .kpi-purple { background: #faf5ff; color: #6d28d9; border: 1px solid #ede9fe; }

    .kpi-title {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #64748b;
        font-weight: 700;
        margin: 0 0 2px 0;
    }

    .kpi-value {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        line-height: 1.25;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 170px;
    }

    .kpi-subtitle {
        font-size: 11.5px;
        color: #94a3b8;
        margin: 2px 0 0 0;
        font-weight: 600;
    }

    /* ============================================================
       3. PROFILE DETAILS CARDS
    ============================================================ */
    .profile-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);
        margin-bottom: 24px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
    }

    .profile-card:hover {
        box-shadow: 0 8px 26px rgba(15, 23, 42, 0.07);
    }

    .profile-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #ffffff;
    }

    .profile-card-title {
        display: flex;
        align-items: center;
        gap: 14px;
        margin: 0;
    }

    .profile-icon-pill {
        width: 44px;
        height: 44px;
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .icon-pill-blue { background: #eff6ff; color: #1d4ed8; }
    .icon-pill-emerald { background: #ecfdf5; color: #047857; }
    .icon-pill-purple { background: #faf5ff; color: #6d28d9; }

    .profile-card-title h5 {
        margin: 0;
        font-size: 17px;
        font-weight: 700;
        color: #0f172a;
    }

    .profile-card-title p {
        margin: 2px 0 0 0;
        font-size: 12.5px;
        color: #64748b;
        font-weight: 500;
    }

    .profile-edit-btn-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 15px;
        font-size: 12.5px;
        font-weight: 600;
        color: #0f172a;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .profile-edit-btn-pill:hover {
        background: #0f172a;
        color: #ffffff;
        border-color: #0f172a;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
    }

    .profile-card-body {
        padding: 8px 24px 20px;
    }

    /* ============================================================
       4. DATA ITEM ROWS
    ============================================================ */
    .profile-data-row {
        padding: 13px 0;
        border-bottom: 1px solid #f8fafc;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .profile-data-row:last-child {
        border-bottom: none;
    }

    .profile-label-group {
        display: flex;
        align-items: center;
        gap: 11px;
        color: #475569;
        font-size: 13.5px;
        font-weight: 600;
        min-width: 190px;
    }

    .profile-label-group i {
        font-size: 18px;
        color: #94a3b8;
    }

    .profile-value {
        font-size: 14px;
        font-weight: 600;
        color: #0f172a;
        text-align: right;
        word-break: break-word;
    }

    .profile-value a {
        color: #1d4ed8;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.2s;
    }

    .profile-value a:hover {
        color: #0284c7;
        text-decoration: underline;
    }

    .placeholder-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 8px;
        background: #f1f5f9;
        color: #64748b;
        font-size: 12px;
        font-weight: 500;
        font-style: italic;
    }

    .station-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 8px;
        background: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
        font-size: 12px;
        font-weight: 600;
        margin: 2px 3px;
    }

    .view-all-stations-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 8px;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .view-all-stations-btn:hover {
        background: #1d4ed8;
        color: #ffffff;
        border-color: #1d4ed8;
    }

    /* Modal station card */
    .modal-station-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.15s ease;
    }

    .modal-station-item:hover {
        background: #eff6ff;
        border-color: #93c5fd;
        transform: translateY(-1px);
    }

    @media (max-width: 768px) {
        .profile-hero-card {
            padding: 24px 20px;
            text-align: center;
        }

        .profile-avatar-box {
            margin: 0 auto;
        }

        .profile-hero-name {
            justify-content: center;
        }

        .profile-hero-desig {
            justify-content: center;
        }

        .profile-hero-chips {
            justify-content: center;
        }

        .hero-hrms-display {
            margin-top: 18px;
        }

        .profile-data-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
        }

        .profile-value {
            text-align: left;
            padding-left: 29px;
        }
    }
</style>

<div class="profile-page-wrapper mt-3">

    {{-- ============================================================
         1. HERO HEADER
    ============================================================ --}}
    <div class="profile-hero-card">
        <div class="row align-items-center g-4">

            {{-- Avatar Column --}}
            <div class="col-12 col-md-auto text-center text-md-start">
                <div class="profile-avatar-box">
                    @if ($profileExists)
                        <img src="{{ $profileUrl }}" alt="{{ $employee->name }}" />
                    @else
                        <span>{{ $initials }}</span>
                    @endif

                    @if ($employee->status == 1)
                        <span class="profile-avatar-badge" title="{{ __('Active in Service') }}"></span>
                    @endif
                </div>
            </div>

            {{-- Main Details Column --}}
            <div class="col-12 col-md">
                <h1 class="profile-hero-name">
                    {{ $employee->name ?? '-' }}
                </h1>

                <div class="profile-hero-desig">
                    <span class="desig-pill-gold">
                        <i class="ti ti-briefcase me-1 text-warning"></i> {{ $employee->designation->name ?? '-' }}
                    </span>

                    <span class="dept-pill-cyan">
                        <i class="ti ti-building me-1 text-info"></i> {{ $employee->department->name ?? '-' }}
                    </span>

                    @if ($employee->status == 1)
                        <span class="status-pill-green">
                            <span class="pulse-dot"></span>
                            {{ __('Active in Service') }}
                        </span>
                    @else
                        <span class="status-pill-red">
                            <span class="dot-indicator bg-danger"></span>
                            {{ __('Inactive') }}
                        </span>
                    @endif
                </div>

                {{-- Quick Meta Chips --}}
                <div class="profile-hero-chips">
                    <span class="hero-chip">
                        <i class="ti ti-id"></i>
                        <span>HRMS: <strong class="text-white">{{ $employee->usmid ?? 'N/A' }}</strong></span>
                    </span>

                    <span class="hero-chip">
                        <i class="ti ti-map-pin"></i>
                        <span>Jurisdiction: <strong class="text-white">{{ $stationCount > 0 ? ($stationCount . ' Stations') : ($presentStation ?: 'Assigned') }}</strong></span>
                    </span>

                    <span class="hero-chip">
                        <i class="ti ti-award"></i>
                        <span>Pay Scale: <strong class="text-white">{{ $employee->level ? ('Level ' . str_ireplace('level', '', $employee->level)) : '7th CPC' }}</strong></span>
                    </span>

                    <span class="hero-chip">
                        <i class="ti ti-calendar"></i>
                        <span>Joined: <strong class="text-white">{{ !empty($employee->date_of_appointment) ? dateFormat($employee->date_of_appointment) : '-' }}</strong></span>
                    </span>

                    @if (Auth::user()->isPh16Authorized())
                        <span class="hero-chip" style="background: rgba(16, 185, 129, 0.22); border-color: rgba(16, 185, 129, 0.5);">
                            <i class="ti ti-shield-check text-success"></i>
                            <span>PH 16 Authority: <strong class="text-success">ACTIVE</strong></span>
                        </span>
                    @elseif (Auth::user()->isPlanningWork())
                        <span class="hero-chip" style="background: rgba(14, 165, 233, 0.22); border-color: rgba(14, 165, 233, 0.5);">
                            <i class="ti ti-clipboard-list text-info"></i>
                            <span>PH 16 Planning: <strong class="text-info">ACTIVE</strong></span>
                        </span>
                    @endif
                </div>
            </div>

            {{-- Right ID Widget & Actions --}}
            <div class="col-12 col-md-auto text-center text-md-end">
                <div class="hero-hrms-display">
                    <span class="hero-hrms-label">
                        <i class="ti ti-train me-1"></i>{{ __('Railway HRMS ID') }}
                    </span>
                    <div class="hero-hrms-value">
                        <span id="hrmsDisplayCode">{{ $employee->usmid ?? 'FIULPC' }}</span>
                        <button type="button" class="copy-hrms-btn" onclick="copyHrmsId()" title="{{ __('Copy HRMS ID') }}">
                            <i class="ti ti-copy" id="copyIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-center justify-content-md-end gap-2 mt-3">
                    <a href="{{ route('dashboard') }}" class="btn btn-sm" style="background: rgba(255, 255, 255, 0.12); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.25); border-radius: 10px; font-weight: 600;">
                        <i class="ti ti-arrow-left me-1"></i> {{ __('Dashboard') }}
                    </a>

                    <a href="#" class="btn btn-sm customModal" data-size="lg"
                        style="background: #e0ab3e; color: #0b1f3a; border: 1px solid #fce3a1; border-radius: 10px; font-weight: 700;"
                        data-bs-toggle="tooltip" title="{{ __('Edit Personal Details') }}"
                        data-url="{{ route('employee-profile.edit', ['id' => encrypt($employee->id), 'section' => 'personal']) }}"
                        data-title="{{ __('Edit Personal Details') }}">
                        <i class="ti ti-pencil me-1"></i> {{ __('Edit Profile') }}
                    </a>
                </div>
            </div>

        </div>
    </div>


    {{-- ============================================================
         2. KPI MINI STATS ROW
    ============================================================ --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Department --}}
        <div class="col-6 col-lg-3">
            <div class="kpi-stat-card">
                <div class="kpi-icon-wrapper kpi-blue">
                    <i class="ti ti-building"></i>
                </div>
                <div>
                    <p class="kpi-title">{{ __('Department') }}</p>
                    <h4 class="kpi-value" title="{{ $employee->department->name ?? '-' }}">{{ $employee->department->name ?? '-' }}</h4>
                    <p class="kpi-subtitle">{{ __('Official Cadre') }}</p>
                </div>
            </div>
        </div>

        {{-- Card 2: Jurisdiction Scope --}}
        <div class="col-6 col-lg-3">
            <div class="kpi-stat-card">
                <div class="kpi-icon-wrapper kpi-emerald">
                    <i class="ti ti-map-pin"></i>
                </div>
                <div>
                    <p class="kpi-title">{{ __('Territory Scope') }}</p>
                    <h4 class="kpi-value">{{ $stationCount > 0 ? ($stationCount . ' Stations') : ($presentStation ?: 'Assigned') }}</h4>
                    <p class="kpi-subtitle">{{ $stationCount > 1 ? __('Division Jurisdiction') : __('Assigned Station') }}</p>
                </div>
            </div>
        </div>

        {{-- Card 3: Pay Scale --}}
        <div class="col-6 col-lg-3">
            <div class="kpi-stat-card">
                <div class="kpi-icon-wrapper kpi-amber">
                    <i class="ti ti-award"></i>
                </div>
                <div>
                    <p class="kpi-title">{{ __('Pay Scale') }}</p>
                    <h4 class="kpi-value">{{ $employee->level ? ('Level ' . str_ireplace('level', '', $employee->level)) : '7th CPC' }}</h4>
                    <p class="kpi-subtitle">{{ __('Pay Matrix Level') }}</p>
                </div>
            </div>
        </div>

        {{-- Card 4: SOS Network --}}
        <div class="col-6 col-lg-3">
            <div class="kpi-stat-card">
                <div class="kpi-icon-wrapper kpi-purple">
                    <i class="ti ti-shield-check"></i>
                </div>
                <div>
                    <p class="kpi-title">{{ __('Emergency SOS') }}</p>
                    <h4 class="kpi-value">{{ $contactsCount }} {{ $contactsCount === 1 ? __('Contact') : __('Contacts') }}</h4>
                    <p class="kpi-subtitle">{{ $contactsCount > 0 ? __('Verified SOS Network') : __('None registered') }}</p>
                </div>
            </div>
        </div>
    </div>


    {{-- ============================================================
         3. MAIN INFORMATION CARDS
    ============================================================ --}}
    <div class="row g-4">

        {{-- ================= LEFT COLUMN: PERSONAL DETAILS ================= --}}
        <div class="col-lg-6">
            <div class="profile-card h-100">
                <div class="profile-card-header">
                    <div class="profile-card-title">
                        <div class="profile-icon-pill icon-pill-blue">
                            <i class="ti ti-user-check"></i>
                        </div>
                        <div>
                            <h5>{{ __('Personal & Service Details') }}</h5>
                            <p>{{ __('Employee identification & official service records') }}</p>
                        </div>
                    </div>

                    <a href="#" class="profile-edit-btn-pill customModal" data-size="lg"
                        data-bs-toggle="tooltip" title="{{ __('Edit Personal Details') }}"
                        data-url="{{ route('employee-profile.edit', ['id' => encrypt($employee->id), 'section' => 'personal']) }}"
                        data-title="{{ __('Edit Personal Details') }}">
                        <i class="ti ti-pencil"></i>
                        <span>{{ __('Edit') }}</span>
                    </a>
                </div>

                <div class="profile-card-body">
                    {{-- 1. Full Name --}}
                    <div class="profile-data-row">
                        <div class="profile-label-group">
                            <i class="ti ti-user"></i>
                            <span>{{ __('Full Name') }}</span>
                        </div>
                        <div class="profile-value fw-bold text-dark">
                            {{ $employee->name ?? '-' }}
                        </div>
                    </div>

                    {{-- 2. HRMS ID --}}
                    <div class="profile-data-row">
                        <div class="profile-label-group">
                            <i class="ti ti-id"></i>
                            <span>{{ __('Railway HRMS ID') }}</span>
                        </div>
                        <div class="profile-value">
                            <span class="badge bg-light text-dark border px-3 py-1 font-monospace" style="font-size: 13.5px; letter-spacing: 1px;">
                                {{ $employee->usmid ?? '-' }}
                            </span>
                        </div>
                    </div>

                    {{-- 3. Department --}}
                    <div class="profile-data-row">
                        <div class="profile-label-group">
                            <i class="ti ti-building"></i>
                            <span>{{ __('Department') }}</span>
                        </div>
                        <div class="profile-value">
                            <span class="badge bg-light text-primary border px-3 py-1 fw-bold">
                                {{ $employee->department->name ?? '-' }}
                            </span>
                        </div>
                    </div>

                    {{-- 4. Designation --}}
                    <div class="profile-data-row">
                        <div class="profile-label-group">
                            <i class="ti ti-briefcase"></i>
                            <span>{{ __('Designation') }}</span>
                        </div>
                        <div class="profile-value">
                            <span class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning px-3 py-1 fw-bold">
                                {{ $employee->designation->name ?? '-' }}
                            </span>
                        </div>
                    </div>

                    {{-- 5. Pay Level / Scale --}}
                    <div class="profile-data-row">
                        <div class="profile-label-group">
                            <i class="ti ti-award"></i>
                            <span>{{ __('Pay Level / Scale') }}</span>
                        </div>
                        <div class="profile-value">
                            @if (!empty($employee->level))
                                <span class="badge bg-amber-subtle text-dark border px-3 py-1 fw-bold">
                                    <i class="ti ti-award me-1 text-warning"></i> Level {{ str_ireplace('level', '', $employee->level) }} (7th CPC)
                                </span>
                            @else
                                <span class="placeholder-badge"><i class="ti ti-pencil me-1"></i> {{ __('Not specified') }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- 6. Date of Birth --}}
                    <div class="profile-data-row">
                        <div class="profile-label-group">
                            <i class="ti ti-calendar"></i>
                            <span>{{ __('Date of Birth') }}</span>
                        </div>
                        <div class="profile-value">
                            @if (!empty($employee->date_of_birth))
                                <span>{{ dateFormat($employee->date_of_birth) }}</span>
                                @php
                                    try {
                                        $age = \Carbon\Carbon::parse($employee->date_of_birth)->age;
                                        echo '<span class="text-muted ms-1 fs-7">(' . $age . ' yrs)</span>';
                                    } catch (\Exception $e) {}
                                @endphp
                            @else
                                <span class="placeholder-badge"><i class="ti ti-pencil me-1"></i> {{ __('Not specified') }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- 7. Date of Appointment --}}
                    <div class="profile-data-row">
                        <div class="profile-label-group">
                            <i class="ti ti-calendar-event"></i>
                            <span>{{ __('Date of Appointment') }}</span>
                        </div>
                        <div class="profile-value">
                            @if (!empty($employee->date_of_appointment))
                                <span>{{ dateFormat($employee->date_of_appointment) }}</span>
                                @php
                                    try {
                                        $tenure = \Carbon\Carbon::parse($employee->date_of_appointment)->diff(\Carbon\Carbon::now());
                                        if ($tenure->y > 0) {
                                            echo '<span class="badge bg-light-primary text-primary ms-1">' . $tenure->y . ' yrs service</span>';
                                        }
                                    } catch (\Exception $e) {}
                                @endphp
                            @else
                                <span class="placeholder-badge"><i class="ti ti-pencil me-1"></i> {{ __('Not specified') }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- 8. Date of Retirement --}}
                    <div class="profile-data-row">
                        <div class="profile-label-group">
                            <i class="ti ti-calendar-time"></i>
                            <span>{{ __('Date of Retirement') }}</span>
                        </div>
                        <div class="profile-value">
                            @if (!empty($employee->date_of_retirement))
                                <span>{{ dateFormat($employee->date_of_retirement) }}</span>
                            @else
                                <span class="placeholder-badge"><i class="ti ti-pencil me-1"></i> {{ __('Not specified') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- ================= RIGHT COLUMN: POSTING & CONTACT DETAILS ================= --}}
        <div class="col-lg-6">

            {{-- 1. POSTING & JURISDICTION CARD --}}
            <div class="profile-card mb-4">
                <div class="profile-card-header">
                    <div class="profile-card-title">
                        <div class="profile-icon-pill icon-pill-emerald">
                            <i class="ti ti-map-pin"></i>
                        </div>
                        <div>
                            <h5>{{ __('Posting & Jurisdiction') }}</h5>
                            <p>{{ __('Current station posting & jurisdictional territory') }}</p>
                        </div>
                    </div>

                    <a href="#" class="profile-edit-btn-pill customModal" data-size="lg"
                        data-bs-toggle="tooltip" title="{{ __('Edit Posting Details') }}"
                        data-url="{{ route('employee-profile.edit', ['id' => encrypt($employee->id), 'section' => 'posting']) }}"
                        data-title="{{ __('Edit Posting Details') }}">
                        <i class="ti ti-pencil"></i>
                        <span>{{ __('Edit') }}</span>
                    </a>
                </div>

                <div class="profile-card-body">
                    {{-- Present Working Station --}}
                    <div class="profile-data-row">
                        <div class="profile-label-group">
                            <i class="ti ti-building-community"></i>
                            <span>{{ __('Present Working Station / Office') }}</span>
                        </div>
                        <div class="profile-value fw-bold text-dark">
                            @if (!empty($presentStation))
                                <span class="badge bg-light text-dark border px-3 py-1">
                                    <i class="ti ti-map-pin me-1 text-primary"></i> {{ $presentStation }}
                                </span>
                            @else
                                <span class="placeholder-badge"><i class="ti ti-pencil me-1"></i> {{ __('Not assigned') }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Jurisdiction Stations --}}
                    <div class="profile-data-row">
                        <div class="profile-label-group">
                            <i class="ti ti-map-pins"></i>
                            <span>{{ __('Jurisdiction Station(s)') }}</span>
                        </div>
                        <div class="profile-value">
                            @if ($stationCount > 5)
                                <div class="d-flex flex-wrap align-items-center justify-content-end gap-1">
                                    <span class="badge bg-primary text-white px-2 py-1">
                                        <i class="ti ti-building-warehouse me-1"></i> {{ $stationCount }} {{ __('Stations Assigned') }}
                                    </span>
                                    <button type="button" class="view-all-stations-btn" data-bs-toggle="modal" data-bs-target="#stationsJurisdictionModal">
                                        <i class="ti ti-list-search me-1"></i> {{ __('View All (:count)', ['count' => $stationCount]) }}
                                    </button>
                                </div>
                                <div class="mt-2 text-end">
                                    @foreach($assignedStations->take(3) as $st)
                                        <span class="station-badge-pill">{{ $st->name }}</span>
                                    @endforeach
                                    <span class="text-muted fs-7">...</span>
                                </div>
                            @elseif ($stationCount >= 1)
                                <div class="d-flex flex-wrap justify-content-end gap-1">
                                    @foreach($assignedStations as $st)
                                        <span class="station-badge-pill">
                                            <i class="ti ti-map-pin me-1"></i>{{ $st->name }}{{ !empty($st->code) ? ' (' . $st->code . ')' : '' }}
                                        </span>
                                    @endforeach
                                </div>
                            @elseif (!empty($employee->jurisdiction_stations))
                                <span class="badge bg-light text-dark border px-2 py-1">{{ $employee->jurisdiction_stations }}</span>
                            @else
                                <span class="placeholder-badge"><i class="ti ti-pencil me-1"></i> {{ __('Not assigned') }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Posting Joining Date --}}
                    <div class="profile-data-row">
                        <div class="profile-label-group">
                            <i class="ti ti-calendar"></i>
                            <span>{{ __('Posting Joining Date') }}</span>
                        </div>
                        <div class="profile-value">
                            @if (!empty($employee->date_of_posting_present_station))
                                <span>{{ dateFormat($employee->date_of_posting_present_station) }}</span>
                            @else
                                <span class="placeholder-badge"><i class="ti ti-pencil me-1"></i> {{ __('Not specified') }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Posting Category / Role --}}
                    <div class="profile-data-row">
                        <div class="profile-label-group">
                            <i class="ti ti-certificate"></i>
                            <span>{{ __('Posting Category / Role') }}</span>
                        </div>
                        <div class="profile-value">
                            <span class="badge bg-light-primary text-primary px-3 py-1 fw-bold">
                                <i class="ti ti-shield-check me-1"></i> {{ $employee->posting ?? 'Regular (Division Cadre)' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. CONTACT & EMERGENCY DETAILS CARD --}}
            <div class="profile-card">
                <div class="profile-card-header">
                    <div class="profile-card-title">
                        <div class="profile-icon-pill icon-pill-purple">
                            <i class="ti ti-phone-call"></i>
                        </div>
                        <div>
                            <h5>{{ __('Contact & Emergency Details') }}</h5>
                            <p>{{ __('Registered phone, email & emergency SOS network') }}</p>
                        </div>
                    </div>

                    <a href="#" class="profile-edit-btn-pill customModal" data-size="lg"
                        data-bs-toggle="tooltip" title="{{ __('Edit Contact Details') }}"
                        data-url="{{ route('employee-profile.edit', ['id' => encrypt($employee->id), 'section' => 'contact']) }}"
                        data-title="{{ __('Edit Contact Details') }}">
                        <i class="ti ti-pencil"></i>
                        <span>{{ __('Edit') }}</span>
                    </a>
                </div>

                <div class="profile-card-body">
                    {{-- Mobile Number --}}
                    <div class="profile-data-row">
                        <div class="profile-label-group">
                            <i class="ti ti-phone"></i>
                            <span>{{ __('Mobile Number') }}</span>
                        </div>
                        <div class="profile-value">
                            @if (!empty($employee->mobile))
                                <a href="tel:{{ $employee->mobile }}" class="fw-bold text-dark text-decoration-none">
                                    <i class="ti ti-phone-call text-success me-1"></i>{{ $employee->mobile }}
                                </a>
                                <span class="badge bg-success-subtle text-success ms-2 border border-success-subtle">
                                    <i class="ti ti-check me-1"></i>{{ __('Verified') }}
                                </span>
                            @else
                                <span class="placeholder-badge">{{ __('Not provided') }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Email Address --}}
                    <div class="profile-data-row">
                        <div class="profile-label-group">
                            <i class="ti ti-mail"></i>
                            <span>{{ __('Email Address') }}</span>
                        </div>
                        <div class="profile-value">
                            @if (!empty($employee->email))
                                <a href="mailto:{{ $employee->email }}" class="fw-semibold text-primary text-decoration-none">
                                    <i class="ti ti-mail me-1"></i>{{ $employee->email }}
                                </a>
                            @else
                                <span class="placeholder-badge">{{ __('Not provided') }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Emergency Contact --}}
                    <div class="profile-data-row">
                        <div class="profile-label-group">
                            <i class="ti ti-alert-triangle"></i>
                            <span>{{ __('Emergency Contact') }}</span>
                        </div>
                        <div class="profile-value">
                            @if ($contactsCount === 0)
                                <div class="d-inline-flex align-items-center gap-2">
                                    <span class="placeholder-badge text-warning"><i class="ti ti-alert-circle me-1"></i>{{ __('None added') }}</span>
                                    <a href="#" class="btn btn-sm btn-outline-primary py-1 px-2 customModal" data-size="lg"
                                        data-url="{{ route('employee-profile.emergency-contact.create', ['redirect_to' => 'profile']) }}"
                                        data-title="{{ __('Add Emergency Contact') }}">
                                        <i class="ti ti-plus me-1"></i>{{ __('Add') }}
                                    </a>
                                </div>
                            @elseif ($contactsCount === 1)
                                @php $c = $employee->emergencyContacts->first(); @endphp
                                <div class="d-inline-flex align-items-center gap-2 flex-wrap justify-content-end">
                                    <a href="tel:{{ $c->mobile_number }}" class="fw-bold text-dark">
                                        <i class="ti ti-phone-call text-danger me-1"></i>{{ $c->mobile_number }}
                                    </a>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                        {{ $c->relation === 'Other' ? ($c->other_relation ?: 'Other') : $c->relation }}
                                    </span>
                                    <a href="{{ route('employee-profile.emergency-contacts') }}" class="btn btn-xs btn-link p-0 text-primary">
                                        {{ __('Manage') }}
                                    </a>
                                </div>
                            @else
                                <div class="d-inline-flex align-items-center gap-2">
                                    <a href="{{ route('employee-profile.emergency-contacts') }}" class="badge bg-danger text-white px-3 py-1 text-decoration-none">
                                        <i class="ti ti-shield-plus me-1"></i>{{ $contactsCount }} {{ __('Contacts Registered') }}
                                    </a>
                                    <a href="#" class="btn btn-xs btn-outline-primary py-1 px-2 customModal" data-size="lg"
                                        data-url="{{ route('employee-profile.emergency-contact.create', ['redirect_to' => 'profile']) }}"
                                        data-title="{{ __('Add Emergency Contact') }}">
                                        <i class="ti ti-plus"></i>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Service Status --}}
                    <div class="profile-data-row">
                        <div class="profile-label-group">
                            <i class="ti ti-activity"></i>
                            <span>{{ __('Service Status') }}</span>
                        </div>
                        <div class="profile-value">
                            @if ($employee->status == 1)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 fw-bold">
                                    <span class="pulse-dot me-1"></span> {{ __('Active in Service') }}
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 fw-bold">
                                    <span class="dot-indicator bg-danger me-1"></span> {{ __('Inactive') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

{{-- ============================================================
     STATIONS JURISDICTION MODAL (FOR OFFICERS WITH MULTIPLE STATIONS)
============================================================ --}}
@if ($stationCount > 0)
<div class="modal fade" id="stationsJurisdictionModal" tabindex="-1" aria-labelledby="stationsModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius: 18px; border: 1px solid #e2e8f0;">
            <div class="modal-header" style="background: linear-gradient(135deg, #091a33 0%, #163e72 100%); color: #ffffff; border-radius: 17px 17px 0 0; padding: 20px 24px;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: rgba(56, 189, 248, 0.2); display: flex; align-items: center; justify-content: center; font-size: 20px; color: #38bdf8;">
                        <i class="ti ti-map-pins"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white mb-0" id="stationsModalTitle">
                            {{ __('Assigned Station Jurisdiction') }}
                        </h5>
                        <small style="color: #94a3b8;">{{ $employee->name }} ({{ $employee->designation->name ?? '-' }}) — {{ $stationCount }} {{ __('Total Stations') }}</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                {{-- Search Filter Input --}}
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0" style="border-radius: 10px 0 0 10px;">
                            <i class="ti ti-search text-muted"></i>
                        </span>
                        <input type="text" id="stationModalFilter" class="form-control border-start-0" placeholder="{{ __('Type station name or code to filter...') }}" style="border-radius: 0 10px 10px 0;" onkeyup="filterStationPills()">
                    </div>
                </div>

                {{-- Stations Responsive Grid --}}
                <div class="row g-2" id="stationPillsContainer" style="max-height: 420px; overflow-y: auto;">
                    @foreach($assignedStations->sortBy('name') as $st)
                        <div class="col-12 col-sm-6 col-md-4 station-filter-col" data-search="{{ strtolower($st->name . ' ' . ($st->code ?? '')) }}">
                            <div class="modal-station-item">
                                <span class="d-flex align-items-center gap-2 text-dark fw-bold fs-7">
                                    <i class="ti ti-map-pin text-primary"></i>
                                    <span>{{ $st->name }}</span>
                                </span>
                                @if(!empty($st->code))
                                    <span class="badge bg-light text-primary border font-monospace">{{ $st->code }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div id="noStationMatchMsg" class="text-center py-4 text-muted" style="display: none;">
                    <i class="ti ti-map-pin-off fs-1 d-block mb-2 text-muted"></i>
                    {{ __('No matching stations found for your search query.') }}
                </div>
            </div>

            <div class="modal-footer" style="background: #f8fafc; border-radius: 0 0 17px 17px;">
                <span class="text-muted fs-7 me-auto">
                    <i class="ti ti-info-circle me-1"></i> {{ __('Stations linked through division employee station master') }}
                </span>
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>
@endif

<script>
    function copyHrmsId() {
        var hrmsText = document.getElementById('hrmsDisplayCode').innerText.trim();
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(hrmsText).then(function() {
                var icon = document.getElementById('copyIcon');
                icon.className = 'ti ti-check text-success';
                setTimeout(function() {
                    icon.className = 'ti ti-copy';
                }, 2000);
            });
        }
    }

    function filterStationPills() {
        var query = document.getElementById('stationModalFilter').value.toLowerCase().trim();
        var cols = document.querySelectorAll('.station-filter-col');
        var visibleCount = 0;

        cols.forEach(function(col) {
            var searchData = col.getAttribute('data-search') || '';
            if (query === '' || searchData.indexOf(query) !== -1) {
                col.style.display = '';
                visibleCount++;
            } else {
                col.style.display = 'none';
            }
        });

        var noMatch = document.getElementById('noStationMatchMsg');
        if (noMatch) {
            noMatch.style.display = (visibleCount === 0) ? 'block' : 'none';
        }
    }
</script>
@endsection
