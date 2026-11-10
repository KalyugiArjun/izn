@extends('layouts.app')

@section('page-title')
    {{ __('Operating Manual') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item" aria-current="page">{{ __('Operating Manual') }}</li>
@endsection

@section('content')

    <style>
        .om-wrapper {
            max-width: 900px;
            margin: 0 auto;
        }

        .om-header {
            background: rgb(15 44 84 / 98%);
            border-radius: 18px;
            padding: 22px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(15, 44, 84, .18);
        }

        .om-header:after {
            content: '';
            position: absolute;
            width: 190px;
            height: 190px;
            right: -75px;
            top: -90px;
            border: 25px solid rgba(255, 255, 255, .06);
            border-radius: 50%;
        }

        .om-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }

        .om-back-btn,
        .om-home-btn {
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

        .om-back-btn:hover,
        .om-home-btn:hover {
            color: #fff;
            background: rgba(255, 255, 255, .22);
            transform: translateY(-1px);
        }

        .om-title {
            text-align: center;
        }

        .om-title h4 {
            margin: 0;
            font-weight: 800;
            letter-spacing: .3px;
            color: white !important;
        }

        .om-title span {
            font-size: 12px;
            opacity: .78;
        }

        .om-stats {
            margin-top: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            z-index: 2;
        }

        .om-stat {
            background: rgba(255, 255, 255, .10);
            border: 1px solid rgba(255, 255, 255, .10);
            border-radius: 11px;
            padding: 9px 13px;
            font-size: 12px;
        }

        .om-stat strong {
            font-size: 16px;
            margin-right: 4px;
        }

        .om-toolbar {
            background: #fff;
            border: 1px solid #e5eaf0;
            border-radius: 15px;
            padding: 13px;
            margin-top: 15px;
            box-shadow: 0 4px 18px rgba(15, 44, 84, .05);
        }

        .om-search {
            position: relative;
        }

        .om-search>i {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #7b8796;
            font-size: 17px;
            z-index: 2;
        }

        .om-search input {
            height: 46px !important;
            border-radius: 11px !important;
            padding-left: 43px !important;
            padding-right: 40px !important;
            border: 1px solid #dce3eb !important;
            box-shadow: none !important;
        }

        .om-search input:focus {
            border-color: rgb(15 44 84 / 98%);
            box-shadow: 0 0 0 3px rgba(15, 44, 84, .08);
        }

        .om-clear {
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

        .om-list {
            margin-top: 15px;
        }

        .om-document {
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
            cursor: pointer;
            width: 100%;
        }

        .om-document:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: rgb(15 44 84 / 98%);
        }

        .om-document:hover {
            border-color: rgba(15, 44, 84, .28);
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(15, 44, 84, .09);
        }

        .om-document:focus {
            outline: none;
        }

        .om-pdf {
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

        .om-pdf img {
            width: 38px;
            height: 38px;
            object-fit: contain;
        }

        .om-document-info {
            flex: 1;
            min-width: 0;
            padding-right: 5px;
        }

        .om-document-title {
            font-weight: 700;
            color: #172033;
            font-size: 15px;
            line-height: 1.35;
            margin-bottom: 6px;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .om-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 7px;
            color: #7b8796;
            font-size: 12px;
        }

        .om-meta>span:first-child {
            white-space: nowrap;
        }

        .om-badge {
            background: rgba(15, 44, 84, .08);
            color: rgb(15 44 84 / 98%);
            border-radius: 20px;
            padding: 4px 9px;
            font-weight: 600;
            white-space: nowrap;
        }

        .om-view {
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

        .om-view i {
            font-size: 18px;
            line-height: 1;
        }

        .om-view:hover {
            color: #fff !important;
            background: #79e605;
            transform: translateX(2px);
        }

        .om-empty {
            background: #fff;
            border: 1px dashed #cfd7e2;
            border-radius: 16px;
            padding: 45px 20px;
            text-align: center;
            color: #8a94a3;
        }

        .om-empty-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 14px;
            border-radius: 50%;
            background: rgba(15, 44, 84, .07);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .om-empty-icon i {
            font-size: 32px;
            color: rgb(15 44 84 / 98%);
        }

        .om-no-result {
            display: none;
            background: #fff;
            border: 1px dashed #cfd7e2;
            border-radius: 15px;
            padding: 35px 20px;
            text-align: center;
            color: #8a94a3;
        }

        .om-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            margin-top: 18px;
            flex-wrap: wrap;
        }

        .om-page {
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

        .om-page:hover {
            border-color: rgb(15 44 84 / 98%);
            color: rgb(15 44 84 / 98%);
        }

        .om-page.active {
            background: rgb(15 44 84 / 98%);
            color: #fff;
            border-color: rgb(15 44 84 / 98%);
        }

        .om-page.disabled {
            opacity: .45;
            pointer-events: none;
        }

        .om-result-info {
            text-align: center;
            color: #8a94a3;
            font-size: 12px;
            margin-top: 8px;
        }

        @media(max-width: 576px) {

            .om-header {
                border-radius: 14px;
                padding: 17px;
            }

            .om-title h4 {
                font-size: 17px;
            }

            .om-title span {
                font-size: 11px;
            }

            .om-header-top {
                gap: 10px;
            }

            .om-back-btn,
            .om-home-btn {
                width: 38px;
                height: 38px;
                min-width: 38px;
            }

            .om-stats {
                gap: 7px;
                margin-top: 15px;
            }

            .om-stat {
                padding: 7px 10px;
            }

            .om-document {
                padding: 12px 12px 12px 13px;
                gap: 10px;
            }

            .om-pdf {
                width: 44px;
                height: 44px;
                min-width: 44px;
                border-radius: 10px;
            }

            .om-pdf img {
                width: 32px;
                height: 32px;
            }

            .om-document-info {
                padding-right: 2px;
            }

            .om-document-title {
                font-size: 14px;
                line-height: 1.35;
            }

            .om-meta {
                font-size: 11px;
                gap: 5px;
            }

            .om-badge {
                padding: 4px 7px;
            }

            .om-view {
                width: 36px;
                height: 36px;
                min-width: 36px;
                border-radius: 9px;
            }

            .om-view i {
                font-size: 16px;
            }
        }
    </style>

    <div class="row justify-content-center mt-2">
        <div class="col-12 col-xl-8">

            <div class="om-wrapper">

                <div class="om-header">

                    <div class="om-header-top">

                        <a href="{{ route('dashboard') }}" class="om-back-btn" title="Back">
                            <i class="ti ti-arrow-left"></i>
                        </a>

                        <div class="om-title">
                            <h4>Operating Manual</h4>
                            <span>Operating Manual Documents</span>
                        </div>

                        <a href="{{ route('dashboard') }}" class="om-home-btn" title="Home">
                            <i class="ti ti-home"></i>
                        </a>

                    </div>

                    <div class="om-stats">

                        <div class="om-stat">
                            <strong id="totalDocuments">
                                {{ $originals->count() }}
                            </strong>
                            Documents
                        </div>

                        <div class="om-stat">
                            <i class="ti ti-file-text me-1"></i>
                            Original PDFs
                        </div>

                    </div>

                </div>

                @if ($originals->count() > 0)
                    <div class="om-toolbar">

                        <div class="om-search">

                            <i class="ti ti-search"></i>

                            <input type="text" id="omSearch" class="form-control"
                                placeholder="Search Operating Manual document..." autocomplete="off">

                            <button type="button" id="omClear" class="om-clear" title="Clear Search">
                                <i class="ti ti-x"></i>
                            </button>

                        </div>

                    </div>

                    <div class="om-list" id="omDocumentList">

                        @foreach ($originals as $original)
                            <a href="{{ route('operating-safety-documents.operating-manual.show', $original->id) }}"
                                class="om-document" data-search="{{ strtolower($original->original_name) }}">

                                <div class="om-pdf">
                                    <img src="{{ asset('assets/images/pdf.jpg') }}" alt="PDF">
                                </div>

                                <div class="om-document-info">

                                    <div class="om-document-title">
                                        {{ $original->original_name }}
                                    </div>

                                    <div class="om-meta">

                                        <span>
                                            <i class="ti ti-file-text me-1"></i>
                                            Original PDF
                                        </span>

                                        <span>•</span>

                                        <span class="om-badge">
                                            <i class="ti ti-file-symlink me-1"></i>
                                            {{ $original->corrections_count }}
                                            {{ Str::plural('Correction Slip', $original->corrections_count) }}
                                        </span>

                                    </div>

                                </div>

                                <span class="om-view" title="View Document">
                                    <i class="ti ti-arrow-right"></i>
                                </span>

                            </a>
                        @endforeach

                    </div>

                    <div id="omNoResult" class="om-no-result">

                        <div class="om-empty-icon">
                            <i class="ti ti-search-off"></i>
                        </div>

                        <div class="fw-semibold text-dark mb-1">
                            No document found
                        </div>

                        <div>
                            Try another document name.
                        </div>

                    </div>

                    <div class="om-result-info" id="omResultInfo">
                    </div>

                    <div class="om-pagination" id="omPagination">
                    </div>
                @else
                    <div class="om-empty mt-3">

                        <div class="om-empty-icon">
                            <i class="ti ti-file-off"></i>
                        </div>

                        <div class="fw-bold text-dark mb-1">
                            No Operating Manual Document Available
                        </div>

                        <div class="small">
                            No original Operating Manual PDF has been uploaded yet.
                        </div>

                    </div>
                @endif

            </div>

        </div>
    </div>

    @if ($originals->count() > 0)
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const searchInput = document.getElementById('omSearch');
                const clearButton = document.getElementById('omClear');
                const documents = Array.from(
                    document.querySelectorAll('.om-document')
                );
                const pagination = document.getElementById('omPagination');
                const noResult = document.getElementById('omNoResult');
                const resultInfo = document.getElementById('omResultInfo');

                const perPage = 8;

                let currentPage = 1;
                let filteredDocuments = [...documents];

                function render() {

                    const totalPages = Math.ceil(
                        filteredDocuments.length / perPage
                    );

                    if (currentPage > totalPages && totalPages > 0) {
                        currentPage = totalPages;
                    }

                    documents.forEach(function(document) {
                        document.style.display = 'none';
                    });

                    if (filteredDocuments.length === 0) {

                        noResult.style.display = 'block';
                        pagination.innerHTML = '';
                        resultInfo.textContent = '';

                        return;
                    }

                    noResult.style.display = 'none';

                    const start = (currentPage - 1) * perPage;
                    const end = start + perPage;

                    filteredDocuments
                        .slice(start, end)
                        .forEach(function(document) {
                            document.style.display = 'flex';
                        });

                    const from = start + 1;
                    const to = Math.min(
                        end,
                        filteredDocuments.length
                    );

                    resultInfo.textContent =
                        'Showing ' +
                        from +
                        '–' +
                        to +
                        ' of ' +
                        filteredDocuments.length +
                        ' documents';

                    renderPagination(totalPages);
                }

                function renderPagination(totalPages) {

                    pagination.innerHTML = '';

                    if (totalPages <= 1) {
                        return;
                    }

                    const previous = document.createElement('button');

                    previous.type = 'button';

                    previous.className =
                        'om-page' +
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

                    let startPage = Math.max(
                        1,
                        currentPage - 2
                    );

                    let endPage = Math.min(
                        totalPages,
                        currentPage + 2
                    );

                    if (currentPage <= 2) {
                        endPage = Math.min(
                            totalPages,
                            5
                        );
                    }

                    if (currentPage >= totalPages - 1) {
                        startPage = Math.max(
                            1,
                            totalPages - 4
                        );
                    }

                    for (
                        let i = startPage; i <= endPage; i++
                    ) {

                        const button =
                            document.createElement('button');

                        button.type = 'button';

                        button.className =
                            'om-page' +
                            (i === currentPage ? ' active' : '');

                        button.textContent = i;

                        button.addEventListener(
                            'click',
                            function() {
                                currentPage = i;
                                render();
                            }
                        );

                        pagination.appendChild(button);
                    }

                    const next = document.createElement('button');

                    next.type = 'button';

                    next.className =
                        'om-page' +
                        (currentPage === totalPages ?
                            ' disabled' :
                            '');

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

                    const value = this.value
                        .trim()
                        .toLowerCase();

                    clearButton.style.display =
                        value ? 'block' : 'none';

                    filteredDocuments = documents.filter(
                        function(document) {
                            return document.dataset.search
                                .includes(value);
                        }
                    );

                    currentPage = 1;

                    render();
                });

                clearButton.addEventListener('click', function() {

                    searchInput.value = '';

                    clearButton.style.display = 'none';

                    filteredDocuments = [...documents];

                    currentPage = 1;

                    render();

                    searchInput.focus();
                });

                render();

            });
        </script>
    @endif

@endsection
