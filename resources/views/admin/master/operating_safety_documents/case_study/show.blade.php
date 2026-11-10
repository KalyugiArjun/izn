@extends('layouts.app')

@section('page-title')
    {{ __('Case Studies') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item" aria-current="page">{{ __('Case Studies') }}</li>
@endsection

@section('content')

    <style>
        .cs-wrapper {
            max-width: 1000px;
            margin: 0 auto;
        }

        /* ===== HEADER ===== */
        .cs-header {
            background: rgb(15 44 84 / 98%);
            border-radius: 18px;
            padding: 22px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(15, 44, 84, .18);
        }

        .cs-header:after {
            content: '';
            position: absolute;
            width: 190px;
            height: 190px;
            right: -75px;
            top: -90px;
            border: 25px solid rgba(255, 255, 255, .06);
            border-radius: 50%;
        }

        .cs-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }

        .cs-back-btn,
        .cs-home-btn {
            width: 40px;
            height: 40px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            background: rgba(255, 255, 255, .12);
            text-decoration: none !important;
            transition: .2s;
            flex-shrink: 0;
        }

        .cs-back-btn:hover,
        .cs-home-btn:hover {
            color: #fff;
            background: rgba(255, 255, 255, .22);
            transform: translateY(-1px);
        }

        .cs-title {
            text-align: center;
        }

        .cs-title h4 {
            margin: 0;
            font-weight: 800;
            letter-spacing: .3px;
            color: white !important;
        }

        .cs-title span {
            font-size: 12px;
            opacity: .78;
        }

        .cs-stats {
            margin-top: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            z-index: 2;
            flex-wrap: wrap;
        }

        .cs-stat {
            background: rgba(255, 255, 255, .10);
            border: 1px solid rgba(255, 255, 255, .10);
            border-radius: 11px;
            padding: 9px 13px;
            font-size: 12px;
        }

        .cs-stat strong {
            font-size: 16px;
            margin-right: 4px;
        }

        /* ===== TOOLBAR ===== */
        .cs-toolbar {
            background: #fff;
            border: 1px solid #e5eaf0;
            border-radius: 15px;
            padding: 13px;
            margin-top: 15px;
            box-shadow: 0 4px 18px rgba(15, 44, 84, .05);
        }

        .cs-search {
            position: relative;
        }

        .cs-search>i {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #7b8796;
            font-size: 17px;
            z-index: 2;
        }

        .cs-search input {
            height: 46px;
            border-radius: 11px;
            padding-left: 43px;
            padding-right: 40px;
            border: 1px solid #dce3eb;
            box-shadow: none;
        }

        .cs-search input:focus {
            border-color: rgb(15 44 84 / 98%);
            box-shadow: 0 0 0 3px rgba(15, 44, 84, .08);
        }

        .cs-clear {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            color: #9ca6b3;
            display: none;
            z-index: 2;
        }

        /* ===== CASE STUDY CARD ===== */
        .cs-list {
            margin-top: 15px;
        }

        .cs-item {
            background: #fff;
            border: 1px solid #e5eaf0;
            border-radius: 15px;
            padding: 15px 16px 15px 15px;
            margin-bottom: 11px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: all .2s ease;
            position: relative;
            overflow: hidden;
            text-decoration: none !important;
            width: 100%;
            cursor: pointer;
        }

        .cs-item:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: rgb(15 44 84 / 98%);
            border-radius: 0 3px 3px 0;
        }

        .cs-item:hover {
            border-color: rgba(15, 44, 84, .28);
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(15, 44, 84, .09);
        }

        .cs-icon {
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

        .cs-icon i {
            font-size: 28px;
            color: rgb(15 44 84 / 80%);
        }

        .cs-info {
            flex: 1;
            min-width: 0;
            padding-right: 5px;
        }

        .cs-title-text {
            font-weight: 700;
            color: #172033;
            font-size: 15px;
            line-height: 1.35;
            margin-bottom: 6px;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .cs-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 7px;
            color: #7b8796;
            font-size: 12px;
        }

        .cs-meta-item {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .cs-meta-item i {
            font-size: 14px;
        }

        .cs-badge {
            border-radius: 20px;
            padding: 4px 9px;
            font-weight: 600;
            white-space: nowrap;
            font-size: 11px;
        }

        .cs-badge-high {
            background: rgba(220, 38, 38, .10);
            color: #dc2626;
        }

        .cs-badge-medium {
            background: rgba(251, 191, 36, .15);
            color: #b45309;
        }

        .cs-badge-low {
            background: rgba(34, 197, 94, .10);
            color: #16a34a;
        }

        .cs-badge-default {
            background: rgba(15, 44, 84, .08);
            color: rgb(15 44 84 / 80%);
        }

        .cs-view {
            width: 40px;
            height: 40px;
            min-width: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgb(15 44 84 / 98%);
            color: #fff !important;
            text-decoration: none !important;
            border: 0;
            transition: all .2s ease;
            flex-shrink: 0;
            margin-left: auto;
        }

        .cs-view i {
            font-size: 18px;
            line-height: 1;
        }

        .cs-view:hover {
            color: #fff !important;
            background: #79e605;
            transform: translateX(2px);
        }

        /* ===== EMPTY STATES ===== */
        .cs-empty {
            background: #fff;
            border: 1px dashed #cfd7e2;
            border-radius: 16px;
            padding: 45px 20px;
            text-align: center;
            color: #8a94a3;
        }

        .cs-empty-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 14px;
            border-radius: 50%;
            background: rgba(15, 44, 84, .07);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cs-empty-icon i {
            font-size: 32px;
            color: rgb(15 44 84 / 80%);
        }

        .cs-no-result {
            display: none;
            background: #fff;
            border: 1px dashed #cfd7e2;
            border-radius: 15px;
            padding: 35px 20px;
            text-align: center;
            color: #8a94a3;
        }

        /* ===== PAGINATION ===== */
        .cs-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            margin-top: 18px;
            flex-wrap: wrap;
        }

        .cs-page {
            min-width: 36px;
            height: 36px;
            padding: 0 10px;
            border-radius: 9px;
            border: 1px solid #dfe5ec;
            background: #fff;
            color: #475569;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 13px;
            transition: .2s;
        }

        .cs-page:hover {
            border-color: rgb(15 44 84 / 98%);
            color: rgb(15 44 84 / 98%);
        }

        .cs-page.active {
            background: rgb(15 44 84 / 98%);
            color: #fff;
            border-color: rgb(15 44 84 / 98%);
        }

        .cs-page.disabled {
            opacity: .45;
            pointer-events: none;
        }

        .cs-result-info {
            text-align: center;
            color: #8a94a3;
            font-size: 12px;
            margin-top: 8px;
        }

        /* ===== RESPONSIVE ===== */
        @media(max-width: 576px) {

            .cs-header {
                border-radius: 14px;
                padding: 17px;
            }

            .cs-title h4 {
                font-size: 17px;
            }

            .cs-title span {
                font-size: 11px;
            }

            .cs-header-top {
                gap: 10px;
            }

            .cs-back-btn,
            .cs-home-btn {
                width: 38px;
                height: 38px;
                min-width: 38px;
            }

            .cs-stats {
                gap: 7px;
                margin-top: 15px;
            }

            .cs-stat {
                padding: 7px 10px;
            }

            .cs-item {
                padding: 12px 12px 12px 13px;
                gap: 10px;
                flex-wrap: wrap;
            }

            .cs-icon {
                width: 44px;
                height: 44px;
                min-width: 44px;
                border-radius: 10px;
            }

            .cs-icon i {
                font-size: 22px;
            }

            .cs-info {
                padding-right: 2px;
                flex: 1 1 100%;
                order: 2;
            }

            .cs-title-text {
                font-size: 14px;
                line-height: 1.35;
            }

            .cs-meta {
                font-size: 11px;
                gap: 5px;
            }

            .cs-badge {
                font-size: 10px;
                padding: 3px 7px;
            }

            .cs-view {
                width: 36px;
                height: 36px;
                min-width: 36px;
                border-radius: 9px;
                order: 3;
                margin-left: auto;
            }

            .cs-view i {
                font-size: 16px;
            }
        }
    </style>

    <div class="row justify-content-center mt-2">
        <div class="col-12 col-xl-9">

            <div class="cs-wrapper">

                <!-- ===== HEADER ===== -->
                <div class="cs-header">

                    <div class="cs-header-top">

                        <a href="{{ route('dashboard') }}" class="cs-back-btn" title="Back">
                            <i class="ti ti-arrow-left"></i>
                        </a>

                        <div class="cs-title">
                            <h4>📚 Case Studies</h4>
                            <span>Operating Safety Case Studies</span>
                        </div>

                        <a href="{{ route('dashboard') }}" class="cs-home-btn" title="Home">
                            <i class="ti ti-home"></i>
                        </a>

                    </div>

                    <div class="cs-stats">

                        <div class="cs-stat">
                            <strong id="totalCases">{{ $caseStudies->count() }}</strong>
                            Total Cases
                        </div>

                        <div class="cs-stat">
                            <i class="ti ti-book me-1"></i>
                            Case Studies
                        </div>

                        @php
                            $highRisk = $caseStudies->where('risk_level', 'High')->count();
                        @endphp
                        @if ($highRisk > 0)
                            <div class="cs-stat"
                                style="background: rgba(220, 38, 38, .15); border-color: rgba(220, 38, 38, .2);">
                                <i class="ti ti-alert-circle me-1"></i>
                                {{ $highRisk }} High Risk
                            </div>
                        @endif

                    </div>

                </div>

                @if ($caseStudies->count() > 0)
                    <div class="cs-toolbar">

                        <div class="cs-search">

                            <i class="ti ti-search"></i>

                            <input type="text" id="csSearch" class="form-control"
                                placeholder="Search by title, location, department..." autocomplete="off">

                            <button type="button" id="csClear" class="cs-clear" title="Clear Search">
                                <i class="ti ti-x"></i>
                            </button>

                        </div>

                    </div>

                    <div class="cs-list" id="csList">

                        @foreach ($caseStudies as $case)
                            <a href="{{ route('operating-safety-documents.case-study.detail', $case->id) }}" class="cs-item"
                                data-search="{{ strtolower(($case->case_study_title ?? '') . ' ' . ($case->location ?? '') . ' ' . ($case->department->name ?? '') . ' ' . ($case->type_of_irregularity ?? '')) }}">

                                <div class="cs-icon">
                                    <i class="ti ti-book"></i>
                                </div>

                                <div class="cs-info">

                                    <div class="cs-title-text">
                                        {{ $case->case_study_title }}
                                    </div>

                                    <div class="cs-meta">

                                        <span class="cs-meta-item">
                                            <i class="ti ti-calendar"></i>
                                            {{ \Carbon\Carbon::parse($case->incident_date)->format('d-m-Y') }}
                                        </span>

                                        <span>•</span>

                                        <span class="cs-meta-item">
                                            <i class="ti ti-map-pin"></i>
                                            {{ $case->location }}
                                        </span>

                                        <span>•</span>

                                        <span class="cs-meta-item">
                                            <i class="ti ti-building"></i>
                                            {{ $case->department->name ?? '-' }}
                                        </span>

                                        <span>•</span>

                                        @php
                                            $riskClass = 'cs-badge-default';
                                            if ($case->risk_level == 'High') {
                                                $riskClass = 'cs-badge-high';
                                            } elseif ($case->risk_level == 'Medium') {
                                                $riskClass = 'cs-badge-medium';
                                            } elseif ($case->risk_level == 'Low') {
                                                $riskClass = 'cs-badge-low';
                                            }
                                        @endphp

                                        <span class="cs-badge {{ $riskClass }}">
                                            <i class="ti ti-alert-circle me-1"></i>
                                            {{ $case->risk_level ?? 'N/A' }}
                                        </span>

                                    </div>

                                </div>

                                <span class="cs-view" title="View Details">
                                    <i class="ti ti-arrow-right"></i>
                                </span>

                            </a>
                        @endforeach

                    </div>

                    <div id="csNoResult" class="cs-no-result">

                        <div class="cs-empty-icon">
                            <i class="ti ti-search-off"></i>
                        </div>

                        <div class="fw-semibold text-dark mb-1">
                            No case study found
                        </div>

                        <div>
                            Try another search term.
                        </div>

                    </div>

                    <div class="cs-result-info" id="csResultInfo">
                    </div>

                    <div class="cs-pagination" id="csPagination">
                    </div>
                @else
                    <div class="cs-empty mt-3">

                        <div class="cs-empty-icon">
                            <i class="ti ti-book-off"></i>
                        </div>

                        <div class="fw-bold text-dark mb-1">
                            No Case Studies Available
                        </div>

                        <div class="small">
                            No case studies have been uploaded yet.
                        </div>

                    </div>
                @endif

            </div>

        </div>
    </div>

    @if ($caseStudies->count() > 0)
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const searchInput = document.getElementById('csSearch');
                const clearButton = document.getElementById('csClear');
                const items = Array.from(document.querySelectorAll('.cs-item'));
                const pagination = document.getElementById('csPagination');
                const noResult = document.getElementById('csNoResult');
                const resultInfo = document.getElementById('csResultInfo');
                const totalCases = document.getElementById('totalCases');

                const perPage = 8;

                let currentPage = 1;
                let filteredItems = [...items];

                function render() {

                    const totalPages = Math.ceil(filteredItems.length / perPage);

                    if (currentPage > totalPages && totalPages > 0) {
                        currentPage = totalPages;
                    }

                    items.forEach(function(item) {
                        item.style.display = 'none';
                    });

                    if (filteredItems.length === 0) {

                        noResult.style.display = 'block';
                        pagination.innerHTML = '';
                        resultInfo.textContent = '';
                        totalCases.textContent = '0';

                        return;
                    }

                    noResult.style.display = 'none';

                    const start = (currentPage - 1) * perPage;
                    const end = Math.min(start + perPage, filteredItems.length);

                    filteredItems.slice(start, end).forEach(function(item) {
                        item.style.display = 'flex';
                    });

                    const from = start + 1;

                    resultInfo.textContent =
                        'Showing ' + from + '–' + end + ' of ' + filteredItems.length + ' case studies';

                    totalCases.textContent = filteredItems.length;

                    renderPagination(totalPages);
                }

                function renderPagination(totalPages) {

                    pagination.innerHTML = '';

                    if (totalPages <= 1) {
                        return;
                    }

                    const previous = document.createElement('button');
                    previous.type = 'button';
                    previous.className = 'cs-page' + (currentPage === 1 ? ' disabled' : '');
                    previous.innerHTML = '<i class="ti ti-chevron-left"></i>';
                    previous.addEventListener('click', function() {
                        if (currentPage > 1) {
                            currentPage--;
                            render();
                        }
                    });
                    pagination.appendChild(previous);

                    let startPage = Math.max(1, currentPage - 2);
                    let endPage = Math.min(totalPages, currentPage + 2);

                    if (currentPage <= 2) {
                        endPage = Math.min(totalPages, 5);
                    }

                    if (currentPage >= totalPages - 1) {
                        startPage = Math.max(1, totalPages - 4);
                    }

                    for (let i = startPage; i <= endPage; i++) {

                        const button = document.createElement('button');
                        button.type = 'button';
                        button.className = 'cs-page' + (i === currentPage ? ' active' : '');
                        button.textContent = i;
                        button.addEventListener('click', function() {
                            currentPage = i;
                            render();
                        });
                        pagination.appendChild(button);
                    }

                    const next = document.createElement('button');
                    next.type = 'button';
                    next.className = 'cs-page' + (currentPage === totalPages ? ' disabled' : '');
                    next.innerHTML = '<i class="ti ti-chevron-right"></i>';
                    next.addEventListener('click', function() {
                        if (currentPage < totalPages) {
                            currentPage++;
                            render();
                        }
                    });
                    pagination.appendChild(next);
                }

                searchInput.addEventListener('input', function() {

                    const value = this.value.trim().toLowerCase();

                    clearButton.style.display = value ? 'block' : 'none';

                    filteredItems = items.filter(function(item) {
                        return item.dataset.search.includes(value);
                    });

                    currentPage = 1;
                    render();
                });

                clearButton.addEventListener('click', function() {

                    searchInput.value = '';
                    clearButton.style.display = 'none';
                    filteredItems = [...items];
                    currentPage = 1;
                    render();
                    searchInput.focus();
                });

                render();

            });
        </script>
    @endif

@endsection
