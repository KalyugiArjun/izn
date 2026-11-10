@extends('layouts.app')

@section('page-title')
    {{ $title }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('operating-safety-documents.drive.show') }}">
            {{ __('Operating Safety Drive') }}
        </a>
    </li>

    <li class="breadcrumb-item" aria-current="page">
        {{ $title }}
    </li>
@endsection

@section('content')

    <style>
        .osd-list-wrapper {
            max-width: 900px;
            margin: 0 auto;
        }

        .osd-list-header {
            background: rgb(15 44 84 / 98%);
            border-radius: 18px;
            padding: 22px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(15, 44, 84, .18);
        }

        .osd-list-header:after {
            content: '';
            position: absolute;
            width: 190px;
            height: 190px;
            right: -75px;
            top: -90px;
            border: 25px solid rgba(255, 255, 255, .06);
            border-radius: 50%;
        }

        .osd-list-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }

        .osd-list-back-btn,
        .osd-list-home-btn {
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

        .osd-list-back-btn:hover,
        .osd-list-home-btn:hover {
            color: #fff;
            background: rgba(255, 255, 255, .22);
            transform: translateY(-1px);
        }

        .osd-list-title {
            text-align: center;
        }

        .osd-list-title h4 {
            margin: 0;
            font-weight: 800;
            letter-spacing: .3px;
            color: white !important;
        }

        .osd-list-title span {
            font-size: 12px;
            opacity: .78;
        }

        .osd-list-stats {
            margin-top: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            z-index: 2;
            flex-wrap: wrap;
        }

        .osd-list-stat {
            background: rgba(255, 255, 255, .10);
            border: 1px solid rgba(255, 255, 255, .10);
            border-radius: 11px;
            padding: 9px 13px;
            font-size: 12px;
        }

        .osd-list-stat strong {
            font-size: 16px;
            margin-right: 4px;
        }

        .osd-list-stat.highlight {
            background: rgba(121, 230, 5, .12);
            border-color: rgba(121, 230, 5, .2);
        }

        .osd-list-toolbar {
            background: #fff;
            border: 1px solid #e5eaf0;
            border-radius: 15px;
            padding: 13px !important;
            margin-top: 15px;
            box-shadow: 0 4px 18px rgba(15, 44, 84, .05);
        }

        .osd-list-search {
            position: relative;
        }

        .osd-list-search>i {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #7b8796;
            font-size: 17px;
            z-index: 2;
        }

        .osd-list-search input {
            height: 46px !important;
            border-radius: 11px !important;
            padding-left: 43px !important;
            padding-right: 40px !important;
            border: 1px solid #dce3eb !important;
            box-shadow: none !important;
        }

        .osd-list-search input:focus {
            border-color: rgb(15 44 84 / 98%);
            box-shadow: 0 0 0 3px rgba(15, 44, 84, .08);
        }

        .osd-list-clear {
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

        .osd-list-container {
            margin-top: 15px;
        }

        .osd-list-drive {
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

        .osd-list-drive:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: rgb(15 44 84 / 98%);
            border-radius: 0 3px 3px 0;
        }

        .osd-list-drive:hover {
            border-color: rgb(15 44 84 / 98%);
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(15, 44, 84, .09);
        }

        .osd-list-pdf {
            width: 52px;
            height: 52px;
            min-width: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .osd-list-pdf img {
            width: 38px;
            height: 38px;
            object-fit: contain;
        }

        .osd-list-drive-info {
            flex: 1;
            min-width: 0;
            padding-right: 5px;
        }

        .osd-list-drive-title {
            font-weight: 700;
            color: #172033;
            font-size: 15px;
            line-height: 1.35;
            margin-bottom: 6px;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .osd-list-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 7px;
            color: #7b8796;
            font-size: 12px;
        }

        .osd-list-badge {
            background: rgb(15 44 84 / 98%);
            color: #fff;
            border-radius: 20px;
            padding: 4px 9px;
            font-weight: 600;
            white-space: nowrap;
        }

        .osd-list-actions {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-shrink: 0;
        }

        .osd-list-view {
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

        .osd-list-view i {
            font-size: 18px;
            line-height: 1;
        }

        .osd-list-view:hover {
            color: #fff !important;
            background: #79e605;
            transform: translateX(2px);
        }

        .osd-list-view-download {
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

        .osd-list-view-download i {
            font-size: 18px;
            line-height: 1;
        }

        .osd-list-view-download:hover {
            color: #fff !important;
            background: #475569;
            transform: translateX(2px);
        }

        .osd-list-empty {
            background: #fff;
            border: 1px dashed #cfd7e2;
            border-radius: 16px;
            padding: 45px 20px;
            text-align: center;
            color: #8a94a3;
        }

        .osd-list-empty-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 14px;
            border-radius: 50%;
            background: rgba(15, 44, 84, .07);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .osd-list-empty-icon i {
            font-size: 32px;
            color: rgb(15 44 84 / 98%);
        }

        .osd-list-no-result {
            display: none;
            background: #fff;
            border: 1px dashed #cfd7e2;
            border-radius: 15px;
            padding: 35px 20px;
            text-align: center;
            color: #8a94a3;
        }

        .osd-list-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            margin-top: 18px;
            flex-wrap: wrap;
        }

        .osd-list-page {
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

        .osd-list-page:hover {
            border-color: rgb(15 44 84 / 98%);
            color: rgb(15 44 84 / 98%);
        }

        .osd-list-page.active {
            background: rgb(15 44 84 / 98%);
            color: #fff;
            border-color: rgb(15 44 84 / 98%);
        }

        .osd-list-page.disabled {
            opacity: .45;
            pointer-events: none;
        }

        .osd-list-result-info {
            text-align: center;
            color: #8a94a3;
            font-size: 12px;
            margin-top: 8px;
        }

        @media(max-width: 576px) {

            .osd-list-header {
                border-radius: 14px;
                padding: 17px;
            }

            .osd-list-title h4 {
                font-size: 17px;
            }

            .osd-list-title span {
                font-size: 11px;
            }

            .osd-list-header-top {
                gap: 10px;
            }

            .osd-list-back-btn,
            .osd-list-home-btn {
                width: 38px;
                height: 38px;
                min-width: 38px;
            }

            .osd-list-stats {
                gap: 7px;
                margin-top: 15px;
            }

            .osd-list-stat {
                padding: 7px 10px;
            }

            .osd-list-drive {
                padding: 12px 12px 12px 13px;
                gap: 10px;
                flex-wrap: wrap;
            }

            .osd-list-pdf {
                width: 44px;
                height: 44px;
                min-width: 44px;
                border-radius: 10px;
            }

            .osd-list-pdf img {
                width: 32px;
                height: 32px;
            }

            .osd-list-drive-info {
                padding-right: 2px;
                flex: 1 1 100%;
                order: 2;
            }

            .osd-list-drive-title {
                font-size: 14px;
                line-height: 1.35;
            }

            .osd-list-meta {
                font-size: 11px;
                gap: 5px;
            }

            .osd-list-badge {
                padding: 4px 7px;
            }

            .osd-list-view,
            .osd-list-view-download {
                width: 36px;
                height: 36px;
                min-width: 36px;
                border-radius: 9px;
            }

            .osd-list-view i,
            .osd-list-view-download i {
                font-size: 16px;
            }

            .osd-list-actions {
                order: 3;
                margin-left: auto;
            }
        }
    </style>

    <div class="row justify-content-center mt-2">
        <div class="col-12 col-xl-8">

            <div class="osd-list-wrapper">

                <div class="osd-list-header">

                    <div class="osd-list-header-top">

                        <a href="{{ route('operating-safety-documents.drive.show') }}" class="osd-list-back-btn"
                            title="Back">
                            <i class="ti ti-arrow-left"></i>
                        </a>

                        <div class="osd-list-title">
                            <h4>{{ $title }}</h4>
                            <span>Filtered Safety Drives</span>
                        </div>

                        <a href="{{ route('dashboard') }}" class="osd-list-home-btn" title="Home">
                            <i class="ti ti-home"></i>
                        </a>

                    </div>

                    <div class="osd-list-stats">

                        <div class="osd-list-stat highlight">
                            <strong id="osdListTotal">{{ $records->count() }}</strong>
                            Total Drives
                        </div>

                        <div class="osd-list-stat">
                            <i class="ti ti-file-text me-1"></i>
                            PDF Files
                        </div>

                        <div class="osd-list-stat"
                            style="background: rgba(15, 44, 84, .15); border-color: rgba(15, 44, 84, .2);">
                            <i class="ti ti-file-check me-1"></i>
                            {{ $filter === 'all' ? 'All' : ($filter === 'year' ? 'Year' : 'Month') }}
                        </div>

                    </div>

                </div>

                @if ($records->count() > 0)
                    <div class="osd-list-toolbar">

                        <div class="osd-list-search">

                            <i class="ti ti-search"></i>

                            <input type="text" id="osdListSearch" class="form-control"
                                placeholder="Search by Drive No. or Subject..." autocomplete="off">

                            <button type="button" id="osdListClear" class="osd-list-clear" title="Clear Search">
                                <i class="ti ti-x"></i>
                            </button>

                        </div>

                    </div>

                    <div class="osd-list-container" id="osdListContainer">

                        @foreach ($records as $record)
                            <div class="osd-list-drive"
                                data-search="{{ strtolower(($record->drive_no ?? '') . ' ' . ($record->subject ?? '')) }}">

                                <div class="osd-list-pdf">
                                    <img src="{{ asset('assets/images/pdf.jpg') }}" alt="PDF">
                                </div>

                                <div class="osd-list-drive-info">

                                    <div class="osd-list-drive-title">
                                        Drive No. {{ $record->drive_no }}
                                    </div>

                                    <div class="osd-list-meta">

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

                                        <span class="osd-list-badge">
                                            <i class="ti ti-file-check me-1"></i>
                                            Safety Drive
                                        </span>

                                    </div>

                                </div>

                                <div class="osd-list-actions">

                                    <a href="{{ asset('public/storage/' . $record->pdf_file) }}" target="_blank"
                                        class="osd-list-view" title="View PDF">
                                        <i class="ti ti-eye"></i>
                                    </a>

                                    <a href="{{ asset('public/storage/' . $record->pdf_file) }}" download
                                        class="osd-list-view" title="Download PDF">
                                        <i class="ti ti-download"></i>
                                    </a>

                                </div>

                            </div>
                        @endforeach

                    </div>

                    <div id="osdListNoResult" class="osd-list-no-result">

                        <div class="osd-list-empty-icon">
                            <i class="ti ti-search-off"></i>
                        </div>

                        <div class="fw-semibold text-dark mb-1">
                            No drive found
                        </div>

                        <div>
                            Try another drive number or subject.
                        </div>

                    </div>

                    <div class="osd-list-result-info" id="osdListResultInfo">
                    </div>

                    <div class="osd-list-pagination" id="osdListPagination">
                    </div>
                @else
                    <div class="osd-list-empty mt-3">

                        <div class="osd-list-empty-icon">
                            <i class="ti ti-file-off"></i>
                        </div>

                        <div class="fw-bold text-dark mb-1">
                            No Drives Found
                        </div>

                        <div class="small">
                            No drives match the selected filter.
                        </div>

                        <a href="{{ route('operating-safety-documents.drive.show') }}"
                            class="btn btn-outline-primary mt-3 rounded-pill px-4">
                            <i class="ti ti-arrow-left me-1"></i>
                            Back to All Drives
                        </a>

                    </div>
                @endif

            </div>

        </div>
    </div>

    @if ($records->count() > 0)
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const searchInput = document.getElementById('osdListSearch');
                const clearButton = document.getElementById('osdListClear');
                const drives = Array.from(document.querySelectorAll('.osd-list-drive'));
                const pagination = document.getElementById('osdListPagination');
                const noResult = document.getElementById('osdListNoResult');
                const resultInfo = document.getElementById('osdListResultInfo');
                const totalDrives = document.getElementById('osdListTotal');

                const perPage = 8;

                let currentPage = 1;
                let filteredDrives = [...drives];

                function render() {

                    const totalPages = Math.ceil(filteredDrives.length / perPage);

                    if (currentPage > totalPages && totalPages > 0) {
                        currentPage = totalPages;
                    }

                    drives.forEach(function(drive) {
                        drive.style.display = 'none';
                    });

                    if (filteredDrives.length === 0) {

                        noResult.style.display = 'block';
                        pagination.innerHTML = '';
                        resultInfo.textContent = '';
                        totalDrives.textContent = '0';

                        return;
                    }

                    noResult.style.display = 'none';

                    const start = (currentPage - 1) * perPage;
                    const end = Math.min(start + perPage, filteredDrives.length);

                    filteredDrives.slice(start, end).forEach(function(drive) {
                        drive.style.display = 'flex';
                    });

                    const from = start + 1;

                    resultInfo.textContent =
                        'Showing ' + from + '–' + end + ' of ' + filteredDrives.length + ' drives';

                    totalDrives.textContent = filteredDrives.length;

                    renderPagination(totalPages);
                }

                function renderPagination(totalPages) {

                    pagination.innerHTML = '';

                    if (totalPages <= 1) {
                        return;
                    }

                    const previous = document.createElement('button');
                    previous.type = 'button';
                    previous.className = 'osd-list-page' + (currentPage === 1 ? ' disabled' : '');
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
                        button.className = 'osd-list-page' + (i === currentPage ? ' active' : '');
                        button.textContent = i;
                        button.addEventListener('click', function() {
                            currentPage = i;
                            render();
                        });
                        pagination.appendChild(button);
                    }

                    const next = document.createElement('button');
                    next.type = 'button';
                    next.className = 'osd-list-page' + (currentPage === totalPages ? ' disabled' : '');
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

                    filteredDrives = drives.filter(function(drive) {
                        return drive.dataset.search.includes(value);
                    });

                    currentPage = 1;
                    render();
                });

                clearButton.addEventListener('click', function() {

                    searchInput.value = '';
                    clearButton.style.display = 'none';
                    filteredDrives = [...drives];
                    currentPage = 1;
                    render();
                    searchInput.focus();
                });

                render();

            });
        </script>
    @endif

@endsection
