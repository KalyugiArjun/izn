@extends('layouts.app')

@section('page-title')
    {{ __('Accident Manual') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item" aria-current="page">{{ __('Accident Manual') }}</li>
@endsection

@section('content')

    <style>
        .am-wrapper {
            max-width: 900px;
            margin: 0 auto;
        }

        .am-header {
            background: rgb(15 44 84 / 98%);
            border-radius: 18px;
            padding: 22px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(15, 44, 84, .18);
        }

        .am-header:after {
            content: '';
            position: absolute;
            width: 190px;
            height: 190px;
            right: -75px;
            top: -90px;
            border: 25px solid rgba(255, 255, 255, .06);
            border-radius: 50%;
        }

        .am-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }

        .am-back-btn,
        .am-home-btn {
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

        .am-back-btn:hover,
        .am-home-btn:hover {
            color: #fff;
            background: rgba(255, 255, 255, .22);
            transform: translateY(-1px);
        }

        .am-title {
            text-align: center;
        }

        .am-title h4 {
            margin: 0;
            font-weight: 800;
            letter-spacing: .3px;
            color: white !important;
        }

        .am-title span {
            font-size: 12px;
            opacity: .78;
        }

        .am-stats {
            margin-top: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            z-index: 2;
        }

        .am-stat {
            background: rgba(255, 255, 255, .10);
            border: 1px solid rgba(255, 255, 255, .10);
            border-radius: 11px;
            padding: 9px 13px;
            font-size: 12px;
        }

        .am-stat strong {
            font-size: 16px;
            margin-right: 4px;
        }

        .am-toolbar {
            background: #fff;
            border: 1px solid #e5eaf0;
            border-radius: 15px;
            padding: 13px;
            margin-top: 15px;
            box-shadow: 0 4px 18px rgba(15, 44, 84, .05);
        }

        .am-search {
            position: relative;
        }

        .am-search>i {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #7b8796;
            font-size: 17px;
            z-index: 2;
        }

        .am-search input {
            height: 46px !important;
            border-radius: 11px !important;
            padding-left: 43px !important;
            padding-right: 40px !important;
            border: 1px solid #dce3eb !important;
            box-shadow: none !important;
        }

        .am-search input:focus {
            border-color: rgb(15 44 84 / 98%);
            box-shadow: 0 0 0 3px rgba(15, 44, 84, .08);
        }

        .am-clear {
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

        .am-list {
            margin-top: 15px;
        }

        .am-document {
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

        .am-document:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: rgb(15 44 84 / 98%);
        }

        .am-document:hover {
            border-color: rgba(15, 44, 84, .28);
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(15, 44, 84, .09);
        }

        .am-document:focus {
            outline: none;
        }

        .am-pdf {
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

        .am-pdf img {
            width: 38px;
            height: 38px;
            object-fit: contain;
        }

        .am-document-info {
            flex: 1;
            min-width: 0;
            padding-right: 5px;
        }

        .am-document-title {
            font-weight: 700;
            color: #172033;
            font-size: 15px;
            line-height: 1.35;
            margin-bottom: 6px;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .am-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 7px;
            color: #7b8796;
            font-size: 12px;
        }

        .am-meta>span:first-child {
            white-space: nowrap;
        }

        .am-badge {
            background: rgba(15, 44, 84, .08);
            color: rgb(15 44 84 / 98%);
            border-radius: 20px;
            padding: 4px 9px;
            font-weight: 600;
            white-space: nowrap;
        }

        .am-view {
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

        .am-view i {
            font-size: 18px;
            line-height: 1;
        }

        .am-view:hover {
            color: #fff !important;
            background: #79e605;
            transform: translateX(2px);
        }

        .am-empty {
            background: #fff;
            border: 1px dashed #cfd7e2;
            border-radius: 16px;
            padding: 45px 20px;
            text-align: center;
            color: #8a94a3;
        }

        .am-empty-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 14px;
            border-radius: 50%;
            background: rgba(15, 44, 84, .07);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .am-empty-icon i {
            font-size: 32px;
            color: rgb(15 44 84 / 98%);
        }

        .am-no-result {
            display: none;
            background: #fff;
            border: 1px dashed #cfd7e2;
            border-radius: 15px;
            padding: 35px 20px;
            text-align: center;
            color: #8a94a3;
        }

        .am-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            margin-top: 18px;
            flex-wrap: wrap;
        }

        .am-page {
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

        .am-page:hover {
            border-color: rgb(15 44 84 / 98%);
            color: rgb(15 44 84 / 98%);
        }

        .am-page.active {
            background: rgb(15 44 84 / 98%);
            color: #fff;
            border-color: rgb(15 44 84 / 98%);
        }

        .am-page.disabled {
            opacity: .45;
            pointer-events: none;
        }

        .am-result-info {
            text-align: center;
            color: #8a94a3;
            font-size: 12px;
            margin-top: 8px;
        }

        @media(max-width: 576px) {

            .am-header {
                border-radius: 14px;
                padding: 17px;
            }

            .am-title h4 {
                font-size: 17px;
            }

            .am-title span {
                font-size: 11px;
            }

            .am-header-top {
                gap: 10px;
            }

            .am-back-btn,
            .am-home-btn {
                width: 38px;
                height: 38px;
                min-width: 38px;
            }

            .am-stats {
                gap: 7px;
                margin-top: 15px;
            }

            .am-stat {
                padding: 7px 10px;
            }

            .am-document {
                padding: 12px 12px 12px 13px;
                gap: 10px;
            }

            .am-pdf {
                width: 44px;
                height: 44px;
                min-width: 44px;
                border-radius: 10px;
            }

            .am-pdf img {
                width: 32px;
                height: 32px;
            }

            .am-document-info {
                padding-right: 2px;
            }

            .am-document-title {
                font-size: 14px;
                line-height: 1.35;
            }

            .am-meta {
                font-size: 11px;
                gap: 5px;
            }

            .am-badge {
                padding: 4px 7px;
            }

            .am-view {
                width: 36px;
                height: 36px;
                min-width: 36px;
                border-radius: 9px;
            }

            .am-view i {
                font-size: 16px;
            }
        }
    </style>

    <div class="row justify-content-center mt-2">
        <div class="col-12 col-xl-8">

            <div class="am-wrapper">

                <div class="am-header">

                    <div class="am-header-top">

                        <a href="{{ route('dashboard') }}" class="am-back-btn" title="Back">
                            <i class="ti ti-arrow-left"></i>
                        </a>

                        <div class="am-title">
                            <h4>Accident Manual</h4>
                            <span>Accident Manual Documents</span>
                        </div>

                        <a href="{{ route('dashboard') }}" class="am-home-btn" title="Home">
                            <i class="ti ti-home"></i>
                        </a>

                    </div>

                    <div class="am-stats">

                        <div class="am-stat">
                            <strong id="totalDocuments">
                                {{ $originals->count() }}
                            </strong>
                            Documents
                        </div>

                        <div class="am-stat">
                            <i class="ti ti-file-text me-1"></i>
                            Original PDFs
                        </div>

                    </div>

                </div>

                @if ($originals->count() > 0)
                    <div class="am-toolbar">

                        <div class="am-search">

                            <i class="ti ti-search"></i>

                            <input type="text" id="amSearch" class="form-control"
                                placeholder="Search Accident Manual document..." autocomplete="off">

                            <button type="button" id="amClear" class="am-clear" title="Clear Search">
                                <i class="ti ti-x"></i>
                            </button>

                        </div>

                    </div>

                    <div class="am-list" id="amDocumentList">

                        @foreach ($originals as $original)
                            <a href="{{ route('operating-safety-documents.accident-manual.show', $original->id) }}"
                                class="am-document" data-search="{{ strtolower($original->original_name) }}">

                                <div class="am-pdf">
                                    <img src="{{ asset('assets/images/pdf.jpg') }}" alt="PDF">
                                </div>

                                <div class="am-document-info">

                                    <div class="am-document-title">
                                        {{ $original->original_name }}
                                    </div>

                                    <div class="am-meta">

                                        <span>
                                            <i class="ti ti-file-text me-1"></i>
                                            Original PDF
                                        </span>

                                        <span>•</span>

                                        <span class="am-badge">
                                            <i class="ti ti-file-symlink me-1"></i>
                                            {{ $original->corrections_count }}
                                            {{ Str::plural('Correction Slip', $original->corrections_count) }}
                                        </span>

                                    </div>

                                </div>

                                <span class="am-view" title="View Document">
                                    <i class="ti ti-arrow-right"></i>
                                </span>

                            </a>
                        @endforeach

                    </div>

                    <div id="amNoResult" class="am-no-result">

                        <div class="am-empty-icon">
                            <i class="ti ti-search-off"></i>
                        </div>

                        <div class="fw-semibold text-dark mb-1">
                            No document found
                        </div>

                        <div>
                            Try another document name.
                        </div>

                    </div>

                    <div class="am-result-info" id="amResultInfo">
                    </div>

                    <div class="am-pagination" id="amPagination">
                    </div>
                @else
                    <div class="am-empty mt-3">

                        <div class="am-empty-icon">
                            <i class="ti ti-file-off"></i>
                        </div>

                        <div class="fw-bold text-dark mb-1">
                            No Accident Manual Document Available
                        </div>

                        <div class="small">
                            No original Accident Manual PDF has been uploaded yet.
                        </div>

                    </div>
                @endif

            </div>

        </div>
    </div>

    @if ($originals->count() > 0)
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const searchInput = document.getElementById('amSearch');
                const clearButton = document.getElementById('amClear');
                const documents = Array.from(
                    document.querySelectorAll('.am-document')
                );
                const pagination = document.getElementById('amPagination');
                const noResult = document.getElementById('amNoResult');
                const resultInfo = document.getElementById('amResultInfo');

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
                        'am-page' +
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
                            'am-page' +
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
                        'am-page' +
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
