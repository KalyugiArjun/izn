@extends('layouts.app')

@section('page-title')
    {{ __('Operating Manual') }} - {{ $original->original_name }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('operating-safety-documents.operating-manual.originals') }}">
            {{ __('Operating Manual') }}
        </a>
    </li>

    <li class="breadcrumb-item" aria-current="page">
        {{ $original->original_name }}
    </li>
@endsection

@section('content')

    <style>
        .om-detail-wrapper {
            max-width: 900px;
            margin: 0 auto;
        }

        .om-detail-header {
            background: rgb(15 44 84 / 98%);
            border-radius: 18px;
            padding: 22px;
            color: #fff;
            box-shadow: 0 8px 25px rgba(15, 44, 84, .18);
            position: relative;
            overflow: hidden;
        }

        .om-detail-header:after {
            content: '';
            position: absolute;
            width: 210px;
            height: 210px;
            right: -90px;
            top: -110px;
            border: 28px solid rgba(255, 255, 255, .06);
            border-radius: 50%;
        }

        .om-detail-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }

        .om-detail-nav {
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

        .om-detail-nav:hover {
            background: rgba(255, 255, 255, .22);
            color: #fff;
        }

        .om-detail-heading {
            text-align: center;
            max-width: 70%;
        }

        .om-detail-heading h5 {
            margin: 0;
            font-weight: 800;
            word-break: break-word;
            color: white !important;
        }

        .om-detail-heading span {
            display: block;
            font-size: 11px;
            opacity: .78;
            margin-top: 3px;
        }

        .om-document-banner {
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

        .om-main-pdf-icon {
            width: 58px;
            height: 58px;
            min-width: 58px;
            border-radius: 13px;
            background: rgba(15, 44, 84, .07);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .om-main-pdf-icon img {
            width: 42px;
            height: 42px;
            object-fit: contain;
        }

        .om-main-info {
            flex: 1;
            min-width: 0;
        }

        .om-main-name {
            font-weight: 800;
            color: #172033;
            font-size: 15px;
            word-break: break-word;
        }

        .om-main-meta {
            color: #8a94a3;
            font-size: 12px;
            margin-top: 4px;
        }

        .om-action {
            width: 39px;
            height: 39px;
            min-width: 39px;
            border-radius: 10px;
            border: 1px solid #e1e6ed;
            background: rgb(15 44 84 / 98%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: .2s;
        }

        .om-action:hover {
            color: #fff;
            background: #79e605;
            border-color: #79e605;
        }

        .om-tabs {
            background: #fff;
            padding: 5px;
            border: 1px solid #e5eaf0;
            border-radius: 13px;
            margin-top: 15px;
            display: flex;
            gap: 5px;
        }

        .om-tab {
            flex: 1;
            border: 0;
            background: transparent;
            color: #64748b;
            border-radius: 9px;
            padding: 10px 12px;
            font-size: 12px;
            font-weight: 700;
            transition: .2s;
        }

        .om-tab.active {
            background: rgb(15 44 84 / 98%);
            color: #fff;
        }

        .om-tab i {
            margin-right: 4px;
        }

        .om-panel {
            margin-top: 15px;
        }

        .om-original-card {
            background: #fff;
            border: 1px solid #e5eaf0;
            border-radius: 16px;
            padding: 18px;
            box-shadow: 0 4px 18px rgba(15, 44, 84, .05);
        }

        .om-original-top {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .om-large-pdf {
            width: 68px;
            height: 68px;
            min-width: 68px;
            border-radius: 15px;
            background: rgba(15, 44, 84, .07);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .om-large-pdf img {
            width: 48px;
            height: 48px;
            object-fit: contain;
        }

        .om-original-info {
            flex: 1;
        }

        .om-original-info h6 {
            margin: 0 0 5px;
            font-weight: 800;
            color: #172033;
        }

        .om-original-info p {
            margin: 0;
            color: #8a94a3;
            font-size: 12px;
        }

        .om-open-pdf {
            margin-top: 18px;
            width: 100%;
            height: 45px;
            border-radius: 11px;
            background: rgb(15 44 84 / 98%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-weight: 700;
            font-size: 13px;
            gap: 7px;
            transition: .2s;
        }

        .om-open-pdf:hover {
            background: rgb(15 44 84 / 88%);
            color: #fff;
        }

        .om-correction-toolbar {
            background: #fff;
            border: 1px solid #e5eaf0;
            border-radius: 14px;
            padding: 12px;
            margin-bottom: 13px;
        }

        .om-correction-search {
            position: relative;
        }

        .om-correction-search>i {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #8b95a5;
            font-size: 18px;
            z-index: 2;
        }

        .om-correction-search input {
            height: 44px;
            padding-left: 41px;
            padding-right: 38px;
            border-radius: 10px;
            border: 1px solid #dce3eb;
            box-shadow: none;
        }

        .om-correction-search input:focus {
            border-color: rgb(15 44 84 / 98%);
            box-shadow: 0 0 0 3px rgba(15, 44, 84, .08);
        }

        .om-correction-clear {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border: 0;
            background: transparent;
            color: #9ca6b3;
            display: none;
            z-index: 2;
        }

        .om-slip {
            background: #fff;
            border: 1px solid #e5eaf0;
            border-radius: 14px;
            padding: 14px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 13px;
            transition: .2s;
        }

        .om-slip:hover {
            border-color: rgba(15, 44, 84, .28);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(15, 44, 84, .07);
        }

        .om-slip-icon {
            width: 48px;
            height: 48px;
            min-width: 48px;
            border-radius: 11px;
            background: rgba(15, 44, 84, .07);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .om-slip-icon img {
            width: 35px;
            height: 35px;
            object-fit: contain;
        }

        .om-slip-info {
            flex: 1;
            min-width: 0;
        }

        .om-slip-title {
            font-weight: 700;
            color: #172033;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .om-slip-date {
            font-size: 11px;
            color: #8a94a3;
        }

        .om-slip-actions {
            display: flex;
            gap: 5px;
        }

        .om-slip-action {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            border: 1px solid #e1e6ed;
            background: rgb(15 44 84 / 98%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .om-slip-action:hover {
            color: #fff;
            background: #79e605;
            border-color: #79e605;
        }

        .om-empty-detail {
            background: #fff;
            border: 1px dashed #cfd7e2;
            border-radius: 15px;
            padding: 35px 20px;
            text-align: center;
            color: #8a94a3;
        }

        .om-empty-detail>i {
            font-size: 35px;
            color: rgb(15 44 84 / 98%);
        }

        .om-detail-pagination {
            display: flex;
            justify-content: center;
            gap: 6px;
            margin-top: 16px;
            flex-wrap: wrap;
        }

        .om-detail-page {
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
        }

        .om-detail-page:hover {
            color: rgb(15 44 84 / 98%);
            border-color: rgb(15 44 84 / 98%);
        }

        .om-detail-page.active {
            background: rgb(15 44 84 / 98%);
            color: #fff;
            border-color: rgb(15 44 84 / 98%);
        }

        .om-detail-page.disabled {
            opacity: .45;
            pointer-events: none;
        }

        .om-detail-result {
            text-align: center;
            font-size: 12px;
            color: #8a94a3;
            margin-top: 8px;
        }

        .om-no-slip {
            display: none;
            background: #fff;
            border: 1px dashed #cfd7e2;
            border-radius: 15px;
            padding: 30px 20px;
            text-align: center;
            color: #8a94a3;
        }

        .om-no-slip .om-empty-icon {
            margin-bottom: 10px;
        }

        @media(max-width:576px) {

            .om-detail-header {
                border-radius: 14px;
                padding: 17px;
            }

            .om-detail-heading {
                max-width: 65%;
            }

            .om-detail-heading h5 {
                font-size: 15px;
            }

            .om-document-banner {
                padding: 13px;
                gap: 9px;
            }

            .om-main-pdf-icon {
                width: 45px;
                height: 45px;
                min-width: 45px;
            }

            .om-main-pdf-icon img {
                width: 34px;
                height: 34px;
            }

            .om-action {
                width: 35px;
                height: 35px;
                min-width: 35px;
            }

            .om-slip {
                padding: 11px;
                gap: 9px;
            }

            .om-slip-actions {
                flex-direction: column;
            }

            .om-large-pdf {
                width: 52px;
                height: 52px;
                min-width: 52px;
            }

            .om-large-pdf img {
                width: 38px;
                height: 38px;
            }
        }
    </style>

    <div class="row justify-content-center mt-2">
        <div class="col-12 col-xl-8">

            <div class="om-detail-wrapper">

                <div class="om-detail-header">

                    <div class="om-detail-top">

                        <a href="{{ route('operating-safety-documents.operating-manual.originals') }}" class="om-detail-nav">
                            <i class="ti ti-arrow-left"></i>
                        </a>

                        <div class="om-detail-heading">
                            <h5>{{ $original->original_name }}</h5>
                            <span>Operating Manual Document Details</span>
                        </div>

                        <a href="{{ route('dashboard') }}" class="om-detail-nav">
                            <i class="ti ti-home"></i>
                        </a>

                    </div>

                </div>

                <div class="om-document-banner">

                    <div class="om-main-pdf-icon">
                        <img src="{{ asset('assets/images/pdf.jpg') }}" alt="PDF">
                    </div>

                    <div class="om-main-info">

                        <div class="om-main-name">
                            {{ $original->original_name }}
                        </div>

                        <div class="om-main-meta">
                            <i class="ti ti-file-text me-1"></i>
                            Original Operating Manual PDF
                        </div>

                    </div>

                    <a href="{{ asset('public/storage/' . $original->original_pdf) }}" target="_blank" class="om-action"
                        title="Open PDF">

                        <i class="ti ti-eye"></i>

                    </a>

                    <a href="{{ asset('public/storage/' . $original->original_pdf) }}" download class="om-action"
                        title="Download PDF">

                        <i class="ti ti-download"></i>

                    </a>

                </div>

                <div class="om-tabs">

                    <button type="button" class="om-tab active" data-tab="originalTab">

                        <i class="ti ti-file-text"></i>
                        ORIGINAL

                    </button>

                    <button type="button" class="om-tab" data-tab="correctionTab">

                        <i class="ti ti-file-symlink"></i>
                        CORRECTION SLIPS
                        <span class="ms-1">
                            ({{ $corrections->count() }})
                        </span>

                    </button>

                </div>

                <div id="originalTab" class="om-panel">

                    <div class="om-original-card">

                        <div class="om-original-top">

                            <div class="om-large-pdf">
                                <img src="{{ asset('assets/images/pdf.jpg') }}" alt="PDF">
                            </div>

                            <div class="om-original-info">

                                <h6>
                                    {{ $original->original_name }}
                                </h6>

                                <p>
                                    <i class="ti ti-file-text me-1"></i>
                                    Official Original Document
                                </p>

                            </div>

                        </div>

                        <a href="{{ asset('public/storage/' . $original->original_pdf) }}" target="_blank"
                            class="om-open-pdf">

                            <i class="ti ti-eye"></i>
                            View Original PDF

                        </a>

                    </div>

                </div>

                <div id="correctionTab" class="om-panel" style="display:none;">

                    @if ($corrections->count() > 0)
                        <div class="om-correction-toolbar">

                            <div class="om-correction-search">

                                <i class="ti ti-search"></i>

                                <input type="text" id="omCorrectionSearch" class="form-control"
                                    placeholder="Search correction slip number or date...">

                                <button type="button" id="omCorrectionClear" class="om-correction-clear">

                                    <i class="ti ti-x"></i>

                                </button>

                            </div>

                        </div>

                        <div id="omCorrectionList">

                            @foreach ($corrections as $slip)
                                <div class="om-slip"
                                    data-search="{{ strtolower(($slip->correction_slip_no ?? '') . ' ' . ($slip->correction_slip_date ?? '')) }}">

                                    <div class="om-slip-icon">

                                        <img src="{{ asset('assets/images/pdf.jpg') }}" alt="PDF">

                                    </div>

                                    <div class="om-slip-info">

                                        <div class="om-slip-title">

                                            Correction Slip No.
                                            {{ $slip->correction_slip_no }}

                                        </div>

                                        <div class="om-slip-date">

                                            <i class="ti ti-calendar me-1"></i>

                                            Date:
                                            {{ $slip->correction_slip_date ?? '-' }}

                                        </div>

                                    </div>

                                    <div class="om-slip-actions">

                                        <a href="{{ asset('public/storage/' . $slip->correction_slip_pdf) }}"
                                            target="_blank" class="om-slip-action" title="View PDF">

                                            <i class="ti ti-eye"></i>

                                        </a>

                                        <a href="{{ asset('public/storage/' . $slip->correction_slip_pdf) }}" download
                                            class="om-slip-action" title="Download PDF">

                                            <i class="ti ti-download"></i>

                                        </a>

                                    </div>

                                </div>
                            @endforeach

                        </div>

                        <div id="omNoSlip" class="om-no-slip">

                            <div class="om-empty-icon">
                                <i class="ti ti-search-off"></i>
                            </div>

                            <div class="fw-semibold text-dark">
                                No correction slip found
                            </div>

                            <div class="small">
                                Try another slip number or date.
                            </div>

                        </div>

                        <div class="om-detail-result" id="omCorrectionResult">
                        </div>

                        <div class="om-detail-pagination" id="omCorrectionPagination">
                        </div>
                    @else
                        <div class="om-empty-detail">

                            <i class="ti ti-file-off"></i>

                            <div class="fw-bold text-dark mt-2">
                                No Correction Slips
                            </div>

                            <div class="small">
                                No correction slip has been added for this document yet.
                            </div>

                        </div>
                    @endif

                </div>

            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const tabs = document.querySelectorAll('.om-tab');
            const originalTab = document.getElementById('originalTab');
            const correctionTab = document.getElementById('correctionTab');

            tabs.forEach(function(tab) {

                tab.addEventListener('click', function() {

                    tabs.forEach(function(item) {
                        item.classList.remove('active');
                    });

                    tab.classList.add('active');

                    if (tab.dataset.tab === 'originalTab') {

                        originalTab.style.display = 'block';
                        correctionTab.style.display = 'none';

                    } else {

                        originalTab.style.display = 'none';
                        correctionTab.style.display = 'block';

                    }

                });

            });

            const searchInput =
                document.getElementById('omCorrectionSearch');

            if (!searchInput) {
                return;
            }

            const clearButton =
                document.getElementById('omCorrectionClear');

            const slips =
                Array.from(document.querySelectorAll('.om-slip'));

            const pagination =
                document.getElementById('omCorrectionPagination');

            const noSlip =
                document.getElementById('omNoSlip');

            const resultInfo =
                document.getElementById('omCorrectionResult');

            const perPage = 6;

            let currentPage = 1;

            let filteredSlips = [...slips];

            function render() {

                const totalPages =
                    Math.ceil(filteredSlips.length / perPage);

                if (currentPage > totalPages && totalPages > 0) {
                    currentPage = totalPages;
                }

                slips.forEach(function(slip) {
                    slip.style.display = 'none';
                });

                if (filteredSlips.length === 0) {

                    noSlip.style.display = 'block';

                    pagination.innerHTML = '';

                    resultInfo.textContent = '';

                    return;
                }

                noSlip.style.display = 'none';

                const start =
                    (currentPage - 1) * perPage;

                const end =
                    start + perPage;

                filteredSlips
                    .slice(start, end)
                    .forEach(function(slip) {
                        slip.style.display = 'flex';
                    });

                const from = start + 1;

                const to =
                    Math.min(end, filteredSlips.length);

                resultInfo.textContent =
                    'Showing ' + from + '–' + to +
                    ' of ' + filteredSlips.length +
                    ' correction slips';

                renderPagination(totalPages);
            }

            function renderPagination(totalPages) {

                pagination.innerHTML = '';

                if (totalPages <= 1) {
                    return;
                }

                const previous =
                    document.createElement('button');

                previous.type = 'button';

                previous.className =
                    'om-detail-page' +
                    (currentPage === 1 ? ' disabled' : '');

                previous.innerHTML =
                    '<i class="ti ti-chevron-left"></i>';

                previous.addEventListener('click', function() {

                    if (currentPage > 1) {

                        currentPage--;

                        render();

                    }

                });

                pagination.appendChild(previous);

                let startPage =
                    Math.max(1, currentPage - 2);

                let endPage =
                    Math.min(totalPages, currentPage + 2);

                if (currentPage <= 2) {
                    endPage = Math.min(totalPages, 5);
                }

                if (currentPage >= totalPages - 1) {
                    startPage = Math.max(1, totalPages - 4);
                }

                for (
                    let i = startPage; i <= endPage; i++
                ) {

                    const button =
                        document.createElement('button');

                    button.type = 'button';

                    button.className =
                        'om-detail-page' +
                        (i === currentPage ? ' active' : '');

                    button.textContent = i;

                    button.addEventListener('click', function() {

                        currentPage = i;

                        render();

                    });

                    pagination.appendChild(button);
                }

                const next =
                    document.createElement('button');

                next.type = 'button';

                next.className =
                    'om-detail-page' +
                    (currentPage === totalPages ? ' disabled' : '');

                next.innerHTML =
                    '<i class="ti ti-chevron-right"></i>';

                next.addEventListener('click', function() {

                    if (currentPage < totalPages) {

                        currentPage++;

                        render();

                    }

                });

                pagination.appendChild(next);
            }

            searchInput.addEventListener('input', function() {

                const value =
                    this.value.trim().toLowerCase();

                clearButton.style.display =
                    value ? 'block' : 'none';

                filteredSlips =
                    slips.filter(function(slip) {

                        return slip.dataset.search.includes(value);

                    });

                currentPage = 1;

                render();
            });

            clearButton.addEventListener('click', function() {

                searchInput.value = '';

                clearButton.style.display = 'none';

                filteredSlips = [...slips];

                currentPage = 1;

                render();

                searchInput.focus();
            });

            render();

        });
    </script>

@endsection
