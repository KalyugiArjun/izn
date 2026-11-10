@extends('layouts.app')

@section('page-title')
    {{ __('G&SR') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item" aria-current="page">{{ __('G&SR') }}</li>
@endsection

@section('content')

    <style>
        .gsr-wrapper {
            max-width: 900px;
            margin: 0 auto;
        }

        .gsr-header {
            background: rgb(15 44 84 / 98%);
            border-radius: 18px;
            padding: 22px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(15, 44, 84, .18);
        }

        .gsr-header:after {
            content: '';
            position: absolute;
            width: 190px;
            height: 190px;
            right: -75px;
            top: -90px;
            border: 25px solid rgba(255, 255, 255, .06);
            border-radius: 50%;
        }

        .gsr-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }

        .gsr-back-btn,
        .gsr-home-btn {
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

        .gsr-back-btn:hover,
        .gsr-home-btn:hover {
            color: #fff;
            background: rgba(255, 255, 255, .22);
            transform: translateY(-1px);
        }

        .gsr-title {
            text-align: center;
        }

        .gsr-title h4 {
            margin: 0;
            font-weight: 800;
            letter-spacing: .3px;
            color: white !important;
        }

        .gsr-title span {
            font-size: 12px;
            opacity: .78;
        }

        .gsr-stats {
            margin-top: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            z-index: 2;
        }

        .gsr-stat {
            background: rgba(255, 255, 255, .10);
            border: 1px solid rgba(255, 255, 255, .10);
            border-radius: 11px;
            padding: 9px 13px;
            font-size: 12px;
        }

        .gsr-stat strong {
            font-size: 16px;
            margin-right: 4px;
        }

        .gsr-toolbar {
            background: #fff;
            border: 1px solid #e5eaf0;
            border-radius: 15px;
            padding: 13px;
            margin-top: 15px;
            box-shadow: 0 4px 18px rgba(15, 44, 84, .05);
        }

        .gsr-search {
            position: relative;
        }

        .gsr-search>i {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #7b8796;
            font-size: 17px;
            z-index: 2;
        }

        .gsr-search input {
            height: 46px !important;
            border-radius: 11px !important;
            padding-left: 43px !important;
            padding-right: 40px !important;
            border: 1px solid #dce3eb !important;
            box-shadow: none !important;
        }

        .gsr-search input:focus {
            border-color: rgb(15 44 84 / 98%);
            box-shadow: 0 0 0 3px rgba(15, 44, 84, .08);
        }

        .gsr-clear {
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

        .gsr-list {
            margin-top: 15px;
        }

        .gsr-document {
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

        .gsr-document:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: rgb(15 44 84 / 98%);
        }

        .gsr-document:hover {
            border-color: rgba(15, 44, 84, .28);
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(15, 44, 84, .09);
        }

        .gsr-document:focus {
            outline: none;
        }

        .gsr-pdf {
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

        .gsr-pdf img {
            width: 38px;
            height: 38px;
            object-fit: contain;
        }

        .gsr-document-info {
            flex: 1;
            min-width: 0;
            padding-right: 5px;
        }

        .gsr-document-title {
            font-weight: 700;
            color: #172033;
            font-size: 15px;
            line-height: 1.35;
            margin-bottom: 6px;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .gsr-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 7px;
            color: #7b8796;
            font-size: 12px;
        }

        .gsr-meta>span:first-child {
            white-space: nowrap;
        }

        .gsr-badge {
            background: rgba(15, 44, 84, .08);
            color: rgb(15 44 84 / 98%);
            border-radius: 20px;
            padding: 4px 9px;
            font-weight: 600;
            white-space: nowrap;
        }

        .gsr-view {
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

        .gsr-view i {
            font-size: 18px;
            line-height: 1;
        }

        .gsr-view:hover {
            color: #fff !important;
            background: #79e605;
            transform: translateX(2px);
        }

        .gsr-empty {
            background: #fff;
            border: 1px dashed #cfd7e2;
            border-radius: 16px;
            padding: 45px 20px;
            text-align: center;
            color: #8a94a3;
        }

        .gsr-empty-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 14px;
            border-radius: 50%;
            background: rgba(15, 44, 84, .07);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .gsr-empty-icon i {
            font-size: 32px;
            color: rgb(15 44 84 / 98%);
        }

        .gsr-no-result {
            display: none;
            background: #fff;
            border: 1px dashed #cfd7e2;
            border-radius: 15px;
            padding: 35px 20px;
            text-align: center;
            color: #8a94a3;
        }

        .gsr-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            margin-top: 18px;
            flex-wrap: wrap;
        }

        .gsr-page {
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

        .gsr-page:hover {
            border-color: rgb(15 44 84 / 98%);
            color: rgb(15 44 84 / 98%);
        }

        .gsr-page.active {
            background: rgb(15 44 84 / 98%);
            color: #fff;
            border-color: rgb(15 44 84 / 98%);
        }

        .gsr-page.disabled {
            opacity: .45;
            pointer-events: none;
        }

        .gsr-result-info {
            text-align: center;
            color: #8a94a3;
            font-size: 12px;
            margin-top: 8px;
        }

        @media(max-width: 576px) {

            .gsr-header {
                border-radius: 14px;
                padding: 17px;
            }

            .gsr-title h4 {
                font-size: 17px;
            }

            .gsr-title span {
                font-size: 11px;
            }

            .gsr-header-top {
                gap: 10px;
            }

            .gsr-back-btn,
            .gsr-home-btn {
                width: 38px;
                height: 38px;
                min-width: 38px;
            }

            .gsr-stats {
                gap: 7px;
                margin-top: 15px;
            }

            .gsr-stat {
                padding: 7px 10px;
            }

            .gsr-document {
                padding: 12px 12px 12px 13px;
                gap: 10px;
            }

            .gsr-pdf {
                width: 44px;
                height: 44px;
                min-width: 44px;
                border-radius: 10px;
            }

            .gsr-pdf img {
                width: 32px;
                height: 32px;
            }

            .gsr-document-info {
                padding-right: 2px;
            }

            .gsr-document-title {
                font-size: 14px;
                line-height: 1.35;
            }

            .gsr-meta {
                font-size: 11px;
                gap: 5px;
            }

            .gsr-badge {
                padding: 4px 7px;
            }

            .gsr-view {
                width: 36px;
                height: 36px;
                min-width: 36px;
                border-radius: 9px;
            }

            .gsr-view i {
                font-size: 16px;
            }
        }
    </style>

    <div class="row justify-content-center mt-2">
        <div class="col-12 col-xl-8">

            <div class="gsr-wrapper">

                <div class="gsr-header">

                    <div class="gsr-header-top">

                        <a href="{{ route('dashboard') }}" class="gsr-back-btn" title="Back">
                            <i class="ti ti-arrow-left"></i>
                        </a>

                        <div class="gsr-title">
                            <h4>G&SR</h4>
                            <span>General & Subsidiary Rules</span>
                        </div>

                        <a href="{{ route('dashboard') }}" class="gsr-home-btn" title="Home">
                            <i class="ti ti-home"></i>
                        </a>

                    </div>

                    <div class="gsr-stats">

                        <div class="gsr-stat">
                            <strong id="totalDocuments">
                                {{ $originals->count() }}
                            </strong>
                            Documents
                        </div>

                        <div class="gsr-stat">
                            <i class="ti ti-file-text me-1"></i>
                            Original PDFs
                        </div>

                    </div>

                </div>

                @if ($originals->count() > 0)
                    <div class="gsr-toolbar">

                        <div class="gsr-search">

                            <i class="ti ti-search"></i>

                            <input type="text" id="gsrSearch" class="form-control" placeholder="Search G&SR document..."
                                autocomplete="off">

                            <button type="button" id="gsrClear" class="gsr-clear" title="Clear Search">
                                <i class="ti ti-x"></i>
                            </button>

                        </div>

                    </div>

                    <div class="gsr-list" id="gsrDocumentList">

                        @foreach ($originals as $original)
                            <a href="{{ route('operating-safety-documents.gsr.show', $original->id) }}" class="gsr-document"
                                data-search="{{ strtolower($original->original_name) }}">

                                <div class="gsr-pdf">
                                    <img src="{{ asset('assets/images/pdf.jpg') }}" alt="PDF">
                                </div>

                                <div class="gsr-document-info">

                                    <div class="gsr-document-title">
                                        {{ $original->original_name }}
                                    </div>

                                    <div class="gsr-meta">

                                        <span>
                                            <i class="ti ti-file-text me-1"></i>
                                            Original PDF
                                        </span>

                                        <span>•</span>

                                        <span class="gsr-badge">
                                            <i class="ti ti-file-symlink me-1"></i>
                                            {{ $original->corrections_count }}
                                            {{ Str::plural('Correction Slip', $original->corrections_count) }}
                                        </span>

                                    </div>

                                </div>

                                <span class="gsr-view" title="View Document">
                                    <i class="ti ti-arrow-right"></i>
                                </span>

                            </a>
                        @endforeach

                    </div>

                    <div id="gsrNoResult" class="gsr-no-result">

                        <div class="gsr-empty-icon">
                            <i class="ti ti-search-off"></i>
                        </div>

                        <div class="fw-semibold text-dark mb-1">
                            No document found
                        </div>

                        <div>
                            Try another document name.
                        </div>

                    </div>

                    <div class="gsr-result-info" id="gsrResultInfo">
                    </div>

                    <div class="gsr-pagination" id="gsrPagination">
                    </div>
                @else
                    <div class="gsr-empty mt-3">

                        <div class="gsr-empty-icon">
                            <i class="ti ti-file-off"></i>
                        </div>

                        <div class="fw-bold text-dark mb-1">
                            No G&SR Document Available
                        </div>

                        <div class="small">
                            No original G&SR PDF has been uploaded yet.
                        </div>

                    </div>
                @endif

            </div>

        </div>
    </div>

    @if ($originals->count() > 0)
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const searchInput = document.getElementById('gsrSearch');
                const clearButton = document.getElementById('gsrClear');
                const documents = Array.from(
                    document.querySelectorAll('.gsr-document')
                );
                const pagination = document.getElementById('gsrPagination');
                const noResult = document.getElementById('gsrNoResult');
                const resultInfo = document.getElementById('gsrResultInfo');

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
                        'gsr-page' +
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
                            'gsr-page' +
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
                        'gsr-page' +
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
