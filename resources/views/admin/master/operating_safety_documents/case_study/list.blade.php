@extends('layouts.app')

@section('page-title')
    {{ __('Case Study Details') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('operating-safety-documents.case-study.show') }}">
            {{ __('Case Studies') }}
        </a>
    </li>

    <li class="breadcrumb-item" aria-current="page">
        {{ __('Case Study Details') }}
    </li>
@endsection

@section('content')
    <style>
        .cs-detail-wrapper {
            max-width: 900px;
            margin: 0 auto;
        }

        .cs-detail-header {
            background: rgb(15 44 84 / 98%);
            border-radius: 18px;
            padding: 22px;
            color: #fff;
            box-shadow: 0 8px 25px rgba(15, 44, 84, .18);
            position: relative;
            overflow: hidden;
        }

        .cs-detail-header:after {
            content: '';
            position: absolute;
            width: 210px;
            height: 210px;
            right: -90px;
            top: -110px;
            border: 28px solid rgba(255, 255, 255, .06);
            border-radius: 50%;
        }

        .cs-detail-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }

        .cs-detail-nav {
            width: 40px;
            height: 40px;
            border-radius: 11px;
            background: rgba(255, 255, 255, .12);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: .2s;
        }

        .cs-detail-nav:hover {
            background: rgba(255, 255, 255, .22);
            color: #fff;
        }

        .cs-detail-heading {
            text-align: center;
            max-width: 70%;
        }

        .cs-detail-heading h5 {
            margin: 0;
            font-weight: 800;
            word-break: break-word;
            color: white !important;
        }

        .cs-detail-heading span {
            display: block;
            font-size: 11px;
            opacity: .78;
            margin-top: 3px;
        }

        /* ===== BANNER ===== */
        .cs-detail-banner {
            background: #fff;
            border: 1px solid #e5eaf0;
            border-radius: 16px;
            padding: 16px;
            margin-top: 15px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 4px 18px rgba(15, 44, 84, .05);
        }

        .cs-detail-icon {
            width: 58px;
            height: 58px;
            min-width: 58px;
            border-radius: 13px;
            background: rgba(15, 44, 84, .07);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cs-detail-icon i {
            font-size: 30px;
            color: rgb(15 44 84 / 80%);
        }

        .cs-detail-info {
            flex: 1;
            min-width: 0;
        }

        .cs-detail-name {
            font-weight: 800;
            color: #172033;
            font-size: 15px;
            word-break: break-word;
        }

        .cs-detail-meta {
            color: #8a94a3;
            font-size: 12px;
            margin-top: 4px;
        }

        /* ===== INFO GRID ===== */
        .cs-detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 15px;
        }

        .cs-detail-card {
            background: #fff;
            border: 1px solid #e5eaf0;
            border-radius: 14px;
            padding: 14px 16px;
            box-shadow: 0 2px 10px rgba(15, 44, 84, .04);
            transition: .2s;
        }

        .cs-detail-card:hover {
            border-color: rgba(15, 44, 84, .2);
            box-shadow: 0 4px 18px rgba(15, 44, 84, .06);
        }

        .cs-detail-card .label {
            font-size: 11px;
            font-weight: 600;
            color: #8a94a3;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 4px;
        }

        .cs-detail-card .value {
            font-size: 16px;
            font-weight: 700;
            color: #172033;
        }

        .cs-detail-card .value i {
            font-size: 16px;
            margin-right: 4px;
        }

        .cs-detail-card.full-width {
            grid-column: 1 / -1;
        }

        /* ===== RISK BADGE ===== */
        .cs-risk-badge {
            display: inline-block;
            padding: 5px 16px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 15px;
        }

        .cs-risk-badge.high {
            background: rgba(220, 38, 38, .10);
            color: #dc2626;
        }

        .cs-risk-badge.medium {
            background: rgba(251, 191, 36, .15);
            color: #b45309;
        }

        .cs-risk-badge.low {
            background: rgba(34, 197, 94, .10);
            color: #16a34a;
        }

        .cs-risk-badge.default {
            background: rgba(15, 44, 84, .08);
            color: rgb(15 44 84 / 80%);
        }

        /* ===== PDF SECTION (WITH IMAGE) ===== */
        .cs-pdf-section {
            background: #fff;
            border: 1px solid #e5eaf0;
            border-radius: 14px;
            padding: 14px 16px;
            margin-top: 12px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 2px 10px rgba(15, 44, 84, .04);
            transition: .2s;
        }

        .cs-pdf-section:hover {
            border-color: rgba(15, 44, 84, .2);
        }

        .cs-pdf-img {
            width: 48px;
            height: 48px;
            min-width: 48px;
            border-radius: 11px;
            background: rgba(220, 38, 38, .08);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cs-pdf-img img {
            width: 38px;
            height: 38px;
            object-fit: contain;
        }

        .cs-pdf-info {
            flex: 1;
            min-width: 0;
        }

        .cs-pdf-name {
            font-weight: 600;
            color: #172033;
            font-size: 14px;
            word-break: break-word;
        }

        .cs-pdf-size {
            font-size: 12px;
            color: #8a94a3;
        }

        .cs-pdf-actions {
            display: flex;
            gap: 6px;
        }

        .cs-pdf-btn {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none !important;
            transition: .2s;
            color: #fff !important;
        }

        .cs-pdf-btn.view {
            background: #dc2626;
        }

        .cs-pdf-btn.view:hover {
            background: #b91c1c;
            transform: scale(1.05);
        }

        .cs-pdf-btn.download {
            background: #64748b;
        }

        .cs-pdf-btn.download:hover {
            background: #475569;
            transform: scale(1.05);
        }

        .cs-pdf-btn i {
            font-size: 18px;
        }


        /* ===== RESPONSIVE ===== */
        @media(max-width: 768px) {
            .cs-detail-grid {
                grid-template-columns: 1fr;
            }

            .cs-detail-card.full-width {
                grid-column: 1;
            }

            .cs-detail-header {
                padding: 17px;
                border-radius: 14px;
            }

            .cs-detail-heading {
                max-width: 65%;
            }

            .cs-detail-heading h5 {
                font-size: 15px;
            }

            .cs-detail-banner {
                padding: 13px;
                gap: 10px;
                flex-wrap: wrap;
            }

            .cs-detail-icon {
                width: 48px;
                height: 48px;
                min-width: 48px;
            }

            .cs-detail-icon i {
                font-size: 24px;
            }

            .cs-pdf-section {
                flex-wrap: wrap;
                padding: 12px 14px;
            }

            .cs-pdf-actions {
                margin-left: auto;
            }

            .cs-pdf-btn {
                width: 36px;
                height: 36px;
                min-width: 36px;
            }

            .cs-pdf-btn i {
                font-size: 16px;
            }
        }

        @media(max-width: 576px) {
            .cs-detail-top {
                gap: 8px;
            }

            .cs-detail-nav {
                width: 36px;
                height: 36px;
                min-width: 36px;
                border-radius: 10px;
            }

            .cs-detail-heading {
                max-width: 60%;
            }

            .cs-detail-heading h5 {
                font-size: 14px;
            }

            .cs-detail-card {
                padding: 12px 14px;
            }

            .cs-detail-card .value {
                font-size: 14px;
            }

            .cs-risk-badge {
                font-size: 13px;
                padding: 4px 12px;
            }

        }
    </style>

    <div class="row justify-content-center mt-2">
        <div class="col-12 col-xl-8">

            <div class="cs-detail-wrapper">

                <!-- ===== HEADER ===== -->
                <div class="cs-detail-header">

                    <div class="cs-detail-top">

                        <a href="{{ route('operating-safety-documents.case-study.show') }}" class="cs-detail-nav">
                            <i class="ti ti-arrow-left"></i>
                        </a>

                        <div class="cs-detail-heading">
                            <h5>{{ $caseStudy->case_study_title }}</h5>
                            <span>Case Study Details</span>
                        </div>

                        <a href="{{ route('dashboard') }}" class="cs-detail-nav">
                            <i class="ti ti-home"></i>
                        </a>

                    </div>

                </div>

                <!-- ===== BANNER ===== -->
                <div class="cs-detail-banner">

                    <div class="cs-detail-icon">
                        <i class="ti ti-book"></i>
                    </div>

                    <div class="cs-detail-info">

                        <div class="cs-detail-name">
                            {{ $caseStudy->case_study_title }}
                        </div>

                        <div class="cs-detail-meta">
                            <i class="ti ti-calendar me-1"></i>
                            {{ \Carbon\Carbon::parse($caseStudy->incident_date)->format('d-m-Y') }}
                            &middot;
                            <i class="ti ti-map-pin me-1"></i>
                            {{ $caseStudy->location }}
                        </div>

                    </div>

                    @php
                        $riskClass = 'default';
                        if ($caseStudy->risk_level == 'High') {
                            $riskClass = 'high';
                        } elseif ($caseStudy->risk_level == 'Medium') {
                            $riskClass = 'medium';
                        } elseif ($caseStudy->risk_level == 'Low') {
                            $riskClass = 'low';
                        }
                    @endphp

                    <span class="cs-risk-badge {{ $riskClass }}">
                        <i class="ti ti-alert-circle me-1"></i>
                        {{ $caseStudy->risk_level ?? 'N/A' }}
                    </span>

                </div>

                <!-- ===== INFO GRID ===== -->
                <div class="cs-detail-grid">

                    <div class="cs-detail-card">
                        <div class="label">Date of Incident</div>
                        <div class="value">
                            <i class="ti ti-calendar"></i>
                            {{ \Carbon\Carbon::parse($caseStudy->incident_date)->format('d-m-Y') }}
                        </div>
                    </div>

                    <div class="cs-detail-card">
                        <div class="label">Location</div>
                        <div class="value">
                            <i class="ti ti-map-pin"></i>
                            {{ $caseStudy->location }}
                        </div>
                    </div>

                    <div class="cs-detail-card">
                        <div class="label">Department</div>
                        <div class="value">
                            <i class="ti ti-building"></i>
                            {{ $caseStudy->department->name ?? '-' }}
                        </div>
                    </div>

                    <div class="cs-detail-card">
                        <div class="label">Type of Irregularity</div>
                        <div class="value">
                            <i class="ti ti-file-text"></i>
                            {{ $caseStudy->type_of_irregularity ?? '-' }}
                        </div>
                    </div>

                    <div class="cs-detail-card full-width">
                        <div class="label">Key Learning</div>
                        <div class="value" style="font-weight: 600; font-size: 15px; color: #1e293b;">
                            {{ $caseStudy->key_learning ?? '-' }}
                        </div>
                    </div>

                </div>

                <!-- ===== PDF SECTION WITH IMAGE ===== -->
                @if ($caseStudy->pdf_file)
                    <div class="cs-pdf-section mb-5">

                        <div class="cs-pdf-img">
                            <img src="{{ asset('assets/images/pdf.jpg') }}" alt="PDF">
                        </div>

                        <div class="cs-pdf-info">
                            <div class="cs-pdf-name">{{ $caseStudy->case_study_title }}</div>
                            <div class="cs-pdf-size">Case Study PDF Document</div>
                        </div>

                        <div class="cs-pdf-actions">

                            <a href="{{ asset('public/storage/' . $caseStudy->pdf_file) }}" target="_blank"
                                class="cs-pdf-btn view" title="View PDF">
                                <i class="ti ti-eye"></i>
                            </a>

                            <a href="{{ asset('public/storage/' . $caseStudy->pdf_file) }}" download
                                class="cs-pdf-btn download" title="Download PDF">
                                <i class="ti ti-download"></i>
                            </a>

                        </div>

                    </div>
                @endif

                

            </div>

        </div>
    </div>
@endsection
