@extends('layouts.app')

@section('page-title')
    {{ $title }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('operating-safety-documents.alert.show') }}">
            {{ __('Operating Safety Alert') }}
        </a>
    </li>

    <li class="breadcrumb-item" aria-current="page">
        {{ $title }}
    </li>
@endsection

@section('content')

    <style>
        
        .osa-list-wrapper {
            max-width: 900px;
            margin: 0 auto;
        }

        .osa-list-header {
            background: rgb(15 44 84 / 98%);
            border-radius: 18px;
            padding: 22px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(15, 44, 84, .18);
        }

        .osa-list-header:after {
            content: '';
            position: absolute;
            width: 190px;
            height: 190px;
            right: -75px;
            top: -90px;
            border: 25px solid rgba(255, 255, 255, .06);
            border-radius: 50%;
        }

        .osa-list-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }

        .osa-list-back-btn,
        .osa-list-home-btn {
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

        .osa-list-back-btn:hover,
        .osa-list-home-btn:hover {
            color: #fff;
            background: rgba(255, 255, 255, .22);
            transform: translateY(-1px);
        }

        .osa-list-title {
            text-align: center;
        }

        .osa-list-title h4 {
            margin: 0;
            font-weight: 800;
            letter-spacing: .3px;
            color: white !important;
        }

        .osa-list-title span {
            font-size: 12px;
            opacity: .78;
        }

        .osa-list-stats {
            margin-top: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            z-index: 2;
            flex-wrap: wrap;
        }

        .osa-list-stat {
            background: rgba(255, 255, 255, .10);
            border: 1px solid rgba(255, 255, 255, .10);
            border-radius: 11px;
            padding: 9px 13px;
            font-size: 12px;
        }

        .osa-list-stat strong {
            font-size: 16px;
            margin-right: 4px;
        }

        .osa-list-stat.highlight {
            background: rgba(121, 230, 5, .12);
            border-color: rgba(121, 230, 5, .2);
        }

        .osa-list-toolbar {
            background: #fff;
            border: 1px solid #e5eaf0;
            border-radius: 15px;
            padding: 13px !important;
            margin-top: 15px;
            box-shadow: 0 4px 18px rgba(15, 44, 84, .05);
        }

        .osa-list-search {
            position: relative;
        }

        .osa-list-search>i {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #7b8796;
            font-size: 17px;
            z-index: 2;
        }

        .osa-list-search input {
            height: 46px !important;
            border-radius: 11px !important;
            padding-left: 43px !important;
            padding-right: 40px !important;
            border: 1px solid #dce3eb !important;
            box-shadow: none !important;
        }

        .osa-list-search input:focus {
            border-color: rgb(15 44 84 / 98%);
            box-shadow: 0 0 0 3px rgba(15, 44, 84, .08);
        }

        .osa-list-clear {
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

        .osa-list-container {
            margin-top: 15px;
        }

        .osa-list-alert {
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
            width: 100%;
        }

        .osa-list-alert:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: rgb(15 44 84 / 98%);
            border-radius: 0 3px 3px 0;
        }

        .osa-list-alert:hover {
            border-color: rgb(15 44 84 / 98%);
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(220, 38, 38, .09);
        }

        .osa-list-pdf {
            width: 52px;
            height: 52px;
            min-width: 52px;
            border-radius: 12px;
            /* background: rgba(220, 38, 38, .07); */
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .osa-list-pdf img {
            width: 38px;
            height: 38px;
            object-fit: contain;
        }

        .osa-list-alert-info {
            flex: 1;
            min-width: 0;
            padding-right: 5px;
        }

        .osa-list-alert-title {
            font-weight: 700;
            color: #172033;
            font-size: 15px;
            line-height: 1.35;
            margin-bottom: 6px;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .osa-list-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 7px;
            color: #7b8796;
            font-size: 12px;
        }

        .osa-list-badge {
            background: rgb(15 44 84 / 98%);
            color: #fff;
            border-radius: 20px;
            padding: 4px 9px;
            font-weight: 600;
            white-space: nowrap;
        }

        .osa-list-actions {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-shrink: 0;
        }

        .osa-list-view {
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
        }

        .osa-list-view i {
            font-size: 18px;
            line-height: 1;
        }

        .osa-list-view:hover {
            color: #fff !important;
            background: #79e605;
            transform: translateX(2px);
        }

        .osa-list-view-download {
            width: 40px;
            height: 40px;
            min-width: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #64748b;
            color: #fff !important;
            text-decoration: none !important;
            border: 0;
            transition: all .2s ease;
            flex-shrink: 0;
        }

        .osa-list-view-download i {
            font-size: 18px;
            line-height: 1;
        }

        .osa-list-view-download:hover {
            color: #fff !important;
            background: #475569;
            transform: translateX(2px);
        }

        .osa-list-empty {
            background: #fff;
            border: 1px dashed #cfd7e2;
            border-radius: 16px;
            padding: 45px 20px;
            text-align: center;
            color: #8a94a3;
        }

        .osa-list-empty-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 14px;
            border-radius: 50%;
            background: rgba(220, 38, 38, .07);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .osa-list-empty-icon i {
            font-size: 32px;
            color: #dc2626;
        }

        .osa-list-no-result {
            display: none;
            background: #fff;
            border: 1px dashed #cfd7e2;
            border-radius: 15px;
            padding: 35px 20px;
            text-align: center;
            color: #8a94a3;
        }

        .osa-list-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            margin-top: 18px;
            flex-wrap: wrap;
        }

        .osa-list-page {
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

        .osa-list-page:hover {
            border-color: #dc2626;
            color: #dc2626;
        }

        .osa-list-page.active {
            background: #dc2626;
            color: #fff;
            border-color: #dc2626;
        }

        .osa-list-page.disabled {
            opacity: .45;
            pointer-events: none;
        }

        .osa-list-result-info {
            text-align: center;
            color: #8a94a3;
            font-size: 12px;
            margin-top: 8px;
        }

        @media(max-width: 576px) {

            .osa-list-header {
                border-radius: 14px;
                padding: 17px;
            }

            .osa-list-title h4 {
                font-size: 17px;
            }

            .osa-list-title span {
                font-size: 11px;
            }

            .osa-list-header-top {
                gap: 10px;
            }

            .osa-list-back-btn,
            .osa-list-home-btn {
                width: 38px;
                height: 38px;
                min-width: 38px;
            }

            .osa-list-stats {
                gap: 7px;
                margin-top: 15px;
            }

            .osa-list-stat {
                padding: 7px 10px;
            }

            .osa-list-alert {
                padding: 12px 12px 12px 13px;
                gap: 10px;
                flex-wrap: wrap;
            }

            .osa-list-pdf {
                width: 44px;
                height: 44px;
                min-width: 44px;
                border-radius: 10px;
            }

            .osa-list-pdf img {
                width: 32px;
                height: 32px;
            }

            .osa-list-alert-info {
                padding-right: 2px;
                flex: 1 1 100%;
                order: 2;
            }

            .osa-list-alert-title {
                font-size: 14px;
                line-height: 1.35;
            }

            .osa-list-meta {
                font-size: 11px;
                gap: 5px;
            }

            .osa-list-badge {
                padding: 4px 7px;
            }

            .osa-list-view,
            .osa-list-view-download {
                width: 36px;
                height: 36px;
                min-width: 36px;
                border-radius: 9px;
            }

            .osa-list-view i,
            .osa-list-view-download i {
                font-size: 16px;
            }

            .osa-list-actions {
                order: 3;
                margin-left: auto;
            }
        }
    </style>

    <div class="row justify-content-center mt-2">
        <div class="col-12 col-xl-8">

            <div class="osa-list-wrapper">

                <div class="osa-list-header">

                    <div class="osa-list-header-top">

                        <a href="{{ route('operating-safety-documents.alert.show') }}" class="osa-list-back-btn"
                            title="Back">
                            <i class="ti ti-arrow-left"></i>
                        </a>

                        <div class="osa-list-title">
                            <h4>{{ $title }}</h4>
                            <span>Filtered Safety Alerts</span>
                        </div>

                        <a href="{{ route('dashboard') }}" class="osa-list-home-btn" title="Home">
                            <i class="ti ti-home"></i>
                        </a>

                    </div>

                    <div class="osa-list-stats">

                        <div class="osa-list-stat highlight">
                            <strong id="osaListTotal">{{ $records->count() }}</strong>
                            Total Alerts
                        </div>

                        <div class="osa-list-stat">
                            <i class="ti ti-file-text me-1"></i>
                            PDF Files
                        </div>

                        <div class="osa-list-stat"
                            style="background: rgba(220, 38, 38, .15); border-color: rgba(220, 38, 38, .2);">
                            <i class="ti ti-alert-circle me-1"></i>
                            {{ $filter === 'all' ? 'All' : ($filter === 'year' ? 'Year' : 'Month') }}
                        </div>

                    </div>

                </div>

                @if ($records->count() > 0)
                    <div class="osa-list-toolbar">

                        <div class="osa-list-search">

                            <i class="ti ti-search"></i>

                            <input type="text" id="osaListSearch" class="form-control"
                                placeholder="Search by Alert No. or Subject..." autocomplete="off">

                            <button type="button" id="osaListClear" class="osa-list-clear" title="Clear Search">
                                <i class="ti ti-x"></i>
                            </button>

                        </div>

                    </div>

                    <div class="osa-list-container" id="osaListContainer">

                        @foreach ($records as $record)
                            <div class="osa-list-alert"
                                data-search="{{ strtolower(($record->alert_no ?? '') . ' ' . ($record->subject ?? '')) }}">

                                <div class="osa-list-pdf">
                                    <img src="{{ asset('assets/images/pdf.jpg') }}" alt="PDF">
                                </div>

                                <div class="osa-list-alert-info">

                                    <div class="osa-list-alert-title">
                                        Alert No. {{ $record->alert_no }}
                                    </div>

                                    <div class="osa-list-meta">

                                        <span>
                                            <i class="ti ti-calendar me-1"></i>
                                            {{ $record->date?->format('d-m-Y') ?? '-' }}
                                        </span>

                                        <span>•</span>

                                        <span>
                                            <i class="ti ti-file-text me-1"></i>
                                            {{ $record->subject }}
                                        </span>

                                        <span>•</span>

                                        <span class="osa-list-badge">
                                            <i class="ti ti-alert-circle me-1"></i>
                                            Safety Alert
                                        </span>

                                    </div>

                                </div>

                                <div class="osa-list-actions">

                                    <a href="{{ asset('public/storage/' . $record->pdf_file) }}" target="_blank"
                                        class="osa-list-view" title="View PDF">
                                        <i class="ti ti-eye"></i>
                                    </a>

                                    <a href="{{ asset('public/storage/' . $record->pdf_file) }}" download
                                        class="osa-list-view" title="Download PDF">
                                        <i class="ti ti-download"></i>
                                    </a>

                                </div>

                            </div>
                        @endforeach

                    </div>

                    <div id="osaListNoResult" class="osa-list-no-result">

                        <div class="osa-list-empty-icon">
                            <i class="ti ti-search-off"></i>
                        </div>

                        <div class="fw-semibold text-dark mb-1">
                            No alert found
                        </div>

                        <div>
                            Try another alert number or subject.
                        </div>

                    </div>

                    <div class="osa-list-result-info" id="osaListResultInfo">
                    </div>

                    <div class="osa-list-pagination" id="osaListPagination">
                    </div>
                @else
                    <div class="osa-list-empty mt-3">

                        <div class="osa-list-empty-icon">
                            <i class="ti ti-file-off"></i>
                        </div>

                        <div class="fw-bold text-dark mb-1">
                            No Alerts Found
                        </div>

                        <div class="small">
                            No alerts match the selected filter.
                        </div>

                        <a href="{{ route('operating-safety-documents.alert.show') }}"
                            class="btn btn-outline-primary mt-3 rounded-pill px-4">
                            <i class="ti ti-arrow-left me-1"></i>
                            Back to All Alerts
                        </a>

                    </div>
                @endif

            </div>

        </div>
    </div>

    @if ($records->count() > 0)
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const searchInput = document.getElementById('osaListSearch');
                const clearButton = document.getElementById('osaListClear');
                const alerts = Array.from(document.querySelectorAll('.osa-list-alert'));
                const pagination = document.getElementById('osaListPagination');
                const noResult = document.getElementById('osaListNoResult');
                const resultInfo = document.getElementById('osaListResultInfo');
                const totalAlerts = document.getElementById('osaListTotal');

                const perPage = 8;

                let currentPage = 1;
                let filteredAlerts = [...alerts];

                function render() {

                    const totalPages = Math.ceil(filteredAlerts.length / perPage);

                    if (currentPage > totalPages && totalPages > 0) {
                        currentPage = totalPages;
                    }

                    alerts.forEach(function(alert) {
                        alert.style.display = 'none';
                    });

                    if (filteredAlerts.length === 0) {

                        noResult.style.display = 'block';
                        pagination.innerHTML = '';
                        resultInfo.textContent = '';
                        totalAlerts.textContent = '0';

                        return;
                    }

                    noResult.style.display = 'none';

                    const start = (currentPage - 1) * perPage;
                    const end = Math.min(start + perPage, filteredAlerts.length);

                    filteredAlerts.slice(start, end).forEach(function(alert) {
                        alert.style.display = 'flex';
                    });

                    const from = start + 1;

                    resultInfo.textContent =
                        'Showing ' + from + '–' + end + ' of ' + filteredAlerts.length + ' alerts';

                    totalAlerts.textContent = filteredAlerts.length;

                    renderPagination(totalPages);
                }

                function renderPagination(totalPages) {

                    pagination.innerHTML = '';

                    if (totalPages <= 1) {
                        return;
                    }

                    const previous = document.createElement('button');
                    previous.type = 'button';
                    previous.className = 'osa-list-page' + (currentPage === 1 ? ' disabled' : '');
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
                        button.className = 'osa-list-page' + (i === currentPage ? ' active' : '');
                        button.textContent = i;
                        button.addEventListener('click', function() {
                            currentPage = i;
                            render();
                        });
                        pagination.appendChild(button);
                    }

                    const next = document.createElement('button');
                    next.type = 'button';
                    next.className = 'osa-list-page' + (currentPage === totalPages ? ' disabled' : '');
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

                    filteredAlerts = alerts.filter(function(alert) {
                        return alert.dataset.search.includes(value);
                    });

                    currentPage = 1;
                    render();
                });

                clearButton.addEventListener('click', function() {

                    searchInput.value = '';
                    clearButton.style.display = 'none';
                    filteredAlerts = [...alerts];
                    currentPage = 1;
                    render();
                    searchInput.focus();
                });

                render();

            });
        </script>
    @endif

@endsection
