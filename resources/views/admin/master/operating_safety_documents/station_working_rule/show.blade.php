@extends('layouts.app')

@section('page-title')
    {{ __('Station Working Rule') }} - {{ $station->name }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('operating-safety-documents.station-working-rule.stations') }}">
            {{ __('Station Working Rule') }}
        </a>
    </li>

    <li class="breadcrumb-item" aria-current="page">
        {{ $station->name }}
    </li>
@endsection

@section('content')
    <style>
        .swr-show-wrapper {
            max-width: 900px;
            margin: 0 auto;
        }

        .swr-show-header {
            background: rgb(15 44 84 / 98%);
            border-radius: 18px;
            padding: 22px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(15, 44, 84, .18);
        }

        .swr-show-header:after {
            content: '';
            position: absolute;
            width: 190px;
            height: 190px;
            right: -75px;
            top: -90px;
            border: 25px solid rgba(255, 255, 255, .06);
            border-radius: 50%;
        }

        .swr-show-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 2;
            gap: 15px;
        }

        .swr-show-back,
        .swr-show-home {
            width: 40px;
            height: 40px;
            min-width: 40px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            background: rgba(255, 255, 255, .12);
            text-decoration: none !important;
            transition: .2s;
        }

        .swr-show-back:hover,
        .swr-show-home:hover {
            color: #fff;
            background: rgba(255, 255, 255, .22);
            transform: translateY(-1px);
        }

        .swr-show-title {
            text-align: center;
            flex: 1;
            min-width: 0;
        }

        .swr-show-title h4 {
            margin: 0;
            font-weight: 800;
            color: #fff !important;
            font-size: 20px;
            word-break: break-word;
        }

        .swr-show-title span {
            font-size: 12px;
            opacity: .78;
        }

        .swr-show-stats {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 18px;
            position: relative;
            z-index: 2;
            flex-wrap: wrap;
        }

        .swr-show-stat {
            background: rgba(255, 255, 255, .10);
            border: 1px solid rgba(255, 255, 255, .10);
            border-radius: 11px;
            padding: 9px 13px;
            font-size: 12px;
        }

        .swr-show-stat strong {
            font-size: 16px;
            margin-right: 4px;
        }

        .swr-tabs-wrapper {
            background: #fff;
            border: 1px solid #e5eaf0;
            border-radius: 15px;
            padding: 8px;
            margin-top: 15px;
            box-shadow: 0 4px 18px rgba(15, 44, 84, .05);
        }

        .swr-tabs {
            display: flex;
            gap: 7px;
        }

        .swr-tabs .nav-item {
            flex: 1;
        }

        .swr-tabs .nav-link {
            width: 100%;
            border-radius: 10px;
            color: #687586;
            font-weight: 700;
            font-size: 12px;
            padding: 11px 10px;
            border: 1px solid transparent;
            background: transparent;
            transition: .2s;
        }

        .swr-tabs .nav-link:hover {
            color: rgb(15 44 84 / 98%);
            background: rgba(15, 44, 84, .05);
        }

        .swr-tabs .nav-link.active {
            background: rgb(15 44 84 / 98%);
            color: #fff;
            border-color: rgb(15 44 84 / 98%);
            box-shadow: 0 4px 12px rgba(15, 44, 84, .16);
        }

        .swr-tab-content {
            margin-top: 15px;
        }

        .swr-section-label {
            color: #6f7b8b;
            font-size: 12px;
            font-weight: 700;
            margin: 0 0 9px 3px;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        .swr-pdf-card {
            background: #fff;
            border: 1px solid #e5eaf0;
            border-radius: 15px;
            padding: 14px 15px;
            margin-bottom: 11px;
            display: flex;
            align-items: center;
            gap: 13px;
            position: relative;
            overflow: hidden;
            transition: all .2s ease;
        }

        .swr-pdf-card:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: rgb(15 44 84 / 98%);
        }

        .swr-pdf-card:hover {
            border-color: rgba(15, 44, 84, .28);
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(15, 44, 84, .09);
        }

        .swr-pdf-icon {
            width: 52px;
            height: 52px;
            min-width: 52px;
            border-radius: 12px;
            background: rgba(15, 44, 84, .07);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .swr-pdf-icon img {
            width: 38px;
            height: 38px;
            object-fit: contain;
        }

        .swr-pdf-info {
            flex: 1;
            min-width: 0;
            padding-right: 5px;
        }

        .swr-pdf-title {
            font-weight: 700;
            color: #172033;
            font-size: 14px;
            line-height: 1.35;
            margin-bottom: 6px;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .swr-pdf-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 7px;
            color: #7b8796;
            font-size: 11px;
        }

        .swr-pdf-badge {
            background: rgba(15, 44, 84, .08);
            color: rgb(15 44 84 / 98%);
            border-radius: 20px;
            padding: 4px 9px;
            font-weight: 600;
            white-space: nowrap;
        }

        .swr-pdf-actions {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .swr-pdf-action {
            width: 38px;
            height: 38px;
            min-width: 38px;
            border-radius: 9px;
            border: 1px solid #e0e6ed;
            background: rgb(15 44 84 / 98%);
            color: #fff !important;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none !important;
            transition: .2s;
        }

        .swr-pdf-action:hover {
            background: #79e605;
            border-color: #79e605;
            color: #fff;
            transform: translateY(-1px);
        }

        .swr-show-empty {
            background: #fff;
            border: 1px dashed #cfd7e2;
            border-radius: 15px;
            padding: 40px 20px;
            text-align: center;
            color: #8a94a3;
        }

        .swr-show-empty-icon {
            width: 65px;
            height: 65px;
            margin: 0 auto 13px;
            border-radius: 50%;
            background: rgba(15, 44, 84, .07);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .swr-show-empty-icon i {
            font-size: 30px;
            color: rgb(15 44 84 / 98%);
        }

        @media(max-width: 576px) {

            .swr-show-header {
                border-radius: 14px;
                padding: 17px;
            }

            .swr-show-title h4 {
                font-size: 17px;
            }

            .swr-show-title span {
                font-size: 11px;
            }

            .swr-show-back,
            .swr-show-home {
                width: 38px;
                height: 38px;
                min-width: 38px;
            }

            .swr-show-stats {
                gap: 7px;
                margin-top: 15px;
            }

            .swr-show-stat {
                padding: 7px 10px;
            }

            .swr-tabs .nav-link {
                font-size: 10px;
                padding: 10px 5px;
            }

            .swr-pdf-card {
                padding: 12px;
                gap: 10px;
            }

            .swr-pdf-icon {
                width: 44px;
                height: 44px;
                min-width: 44px;
                border-radius: 10px;
            }

            .swr-pdf-icon img {
                width: 32px;
                height: 32px;
            }

            .swr-pdf-title {
                font-size: 13px;
            }

            .swr-pdf-meta {
                font-size: 10px;
                gap: 5px;
            }

            .swr-pdf-actions {
                gap: 4px;
            }

            .swr-pdf-action {
                width: 34px;
                height: 34px;
                min-width: 34px;
            }
        }
    </style>

    <div class="row justify-content-center mt-2">
        <div class="col-12 col-xl-8">

            <div class="swr-show-wrapper">

                <div class="swr-show-header">

                    <div class="swr-show-header-top">

                        <a href="{{ route('operating-safety-documents.station-working-rule.stations') }}"
                            class="swr-show-back" title="Back">
                            <i class="ti ti-arrow-left"></i>
                        </a>

                        <div class="swr-show-title">
                            <h4>{{ $station->name }}</h4>
                            <span>Station Working Rule</span>
                        </div>

                        <a href="{{ route('dashboard') }}" class="swr-show-home" title="Home">
                            <i class="ti ti-home"></i>
                        </a>

                    </div>

                    <div class="swr-show-stats">

                        <div class="swr-show-stat">
                            <strong>{{ $originals->count() }}</strong>
                            Original PDFs
                        </div>

                        <div class="swr-show-stat">
                            <strong>{{ $allCorrections->count() }}</strong>
                            Correction Slips
                        </div>

                    </div>

                </div>

                <div class="swr-tabs-wrapper">

                    <ul class="nav nav-pills swr-tabs" id="swrTabs" role="tablist">

                        <li class="nav-item" role="presentation">

                            <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#original" type="button">

                                <i class="ti ti-file-text me-1"></i>
                                {{ __('ORIGINAL') }}

                            </button>

                        </li>

                        <li class="nav-item" role="presentation">

                            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#slips" type="button">

                                <i class="ti ti-file-symlink me-1"></i>
                                {{ __('CORRECTION SLIPS') }}

                            </button>

                        </li>

                    </ul>

                </div>

                <div class="tab-content swr-tab-content">

                    <div class="tab-pane fade show active" id="original">

                        <div class="swr-section-label">
                            {{ __('Original PDFs') }}
                        </div>

                        @forelse($originals as $original)
                            @php
                                $originalPdf = asset('public/storage/' . $original->original_pdf);
                                $correctionCount = $original->corrections->count();
                            @endphp

                            <div class="swr-pdf-card">

                                <div class="swr-pdf-icon">
                                    <img src="{{ asset('assets/images/pdf.jpg') }}" alt="PDF">
                                </div>

                                <div class="swr-pdf-info">

                                    <div class="swr-pdf-title">
                                        {{ $original->original_name }}
                                    </div>

                                    <div class="swr-pdf-meta">

                                        <span>
                                            <i class="ti ti-file-text me-1"></i>
                                            {{ __('Original PDF') }}
                                        </span>

                                        <span>•</span>

                                        <span class="swr-pdf-badge">
                                            <i class="ti ti-file-symlink me-1"></i>
                                            {{ $correctionCount }}
                                            {{ Str::plural('Correction Slip', $correctionCount) }}
                                        </span>

                                    </div>

                                </div>

                                <div class="swr-pdf-actions">

                                    <a href="{{ $originalPdf }}" target="_blank" class="swr-pdf-action" title="View PDF">

                                        <i class="ti ti-eye"></i>

                                    </a>

                                    <a href="{{ $originalPdf }}" download class="swr-pdf-action" title="Download PDF">

                                        <i class="ti ti-download"></i>

                                    </a>

                                </div>

                            </div>

                        @empty

                            <div class="swr-show-empty">

                                <div class="swr-show-empty-icon">
                                    <i class="ti ti-file-off"></i>
                                </div>

                                <div class="fw-bold text-dark mb-1">
                                    {{ __('No Original PDF Available') }}
                                </div>

                                <div class="small">
                                    {{ __('No original PDF has been uploaded yet.') }}
                                </div>

                            </div>
                        @endforelse

                    </div>

                    <div class="tab-pane fade" id="slips">

                        <div class="swr-section-label">
                            {{ __('Correction Slips') }}
                        </div>

                        @forelse($allCorrections as $slip)
                            @php
                                $slipPdf = asset('public/storage/' . $slip->correction_slip_pdf);
                            @endphp

                            <div class="swr-pdf-card">

                                <div class="swr-pdf-icon">
                                    <img src="{{ asset('assets/images/pdf.jpg') }}" alt="PDF">
                                </div>

                                <div class="swr-pdf-info">

                                    <div class="swr-pdf-title">
                                        {{ __('Correction Slip No.') }}
                                        {{ $slip->correction_slip_no }}
                                    </div>

                                    <div class="swr-pdf-meta">

                                        <span>
                                            <i class="ti ti-calendar me-1"></i>
                                            {{ __('Date') }}:
                                            {{ $slip->issue_date ?? '-' }}
                                        </span>

                                        <span>•</span>

                                        <span class="swr-pdf-badge">
                                            {{ $slip->original->original_name ?? '-' }}
                                        </span>

                                    </div>

                                </div>

                                <div class="swr-pdf-actions">

                                    <a href="{{ $slipPdf }}" target="_blank" class="swr-pdf-action" title="View PDF">

                                        <i class="ti ti-eye"></i>

                                    </a>

                                    <a href="{{ $slipPdf }}" download class="swr-pdf-action" title="Download PDF">

                                        <i class="ti ti-download"></i>

                                    </a>

                                </div>

                            </div>

                        @empty

                            <div class="swr-show-empty">

                                <div class="swr-show-empty-icon">
                                    <i class="ti ti-file-off"></i>
                                </div>

                                <div class="fw-bold text-dark mb-1">
                                    {{ __('No Correction Slips Available') }}
                                </div>

                                <div class="small">
                                    {{ __('No correction slips have been added yet.') }}
                                </div>

                            </div>
                        @endforelse

                    </div>

                </div>

            </div>

        </div>
    </div>
@endsection
