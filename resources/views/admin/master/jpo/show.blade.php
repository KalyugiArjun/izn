@extends('layouts.app')

@section('page-title')
    {{ __('JPO') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item" aria-current="page">{{ __('JPO') }}</li>
@endsection

@section('content')

    <style>
        .ml-wrapper {
            max-width: 900px;
            margin: 0 auto;
        }

        .ml-header {
            background: rgb(15 44 84 / 98%);
            border-radius: 18px;
            padding: 22px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(15, 44, 84, .18);
        }

        .ml-header:after {
            content: '';
            position: absolute;
            width: 190px;
            height: 190px;
            right: -75px;
            top: -90px;
            border: 25px solid rgba(255, 255, 255, .06);
            border-radius: 50%;
        }

        .ml-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }

        .ml-back-btn,
        .ml-home-btn {
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

        .ml-back-btn:hover,
        .ml-home-btn:hover {
            color: #fff;
            background: rgba(255, 255, 255, .22);
            transform: translateY(-1px);
        }

        .ml-title {
            text-align: center;
        }

        .ml-title h4 {
            margin: 0;
            font-weight: 800;
            letter-spacing: .3px;
            color: white !important;
        }

        .ml-title span {
            font-size: 12px;
            opacity: .78;
        }

        .ml-stats {
            margin-top: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            z-index: 2;
        }

        .ml-stat {
            background: rgba(255, 255, 255, .10);
            border: 1px solid rgba(255, 255, 255, .10);
            border-radius: 11px;
            padding: 9px 13px;
            font-size: 12px;
        }

        .ml-stat strong {
            font-size: 16px;
            margin-right: 4px;
        }

        .ml-toolbar {
            background: #fff;
            border: 1px solid #e5eaf0;
            border-radius: 15px;
            padding: 13px;
            margin-top: 15px;
            box-shadow: 0 4px 18px rgba(15, 44, 84, .05);
        }

        .ml-search {
            position: relative;
        }

        .ml-search>i {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #7b8796;
            font-size: 17px;
            z-index: 2;
        }

        .ml-search input {
            height: 46px !important;
            border-radius: 11px !important;
            padding-left: 43px !important;
            padding-right: 40px !important;
            border: 1px solid #dce3eb !important;
            box-shadow: none !important;
        }

        .ml-search input:focus {
            border-color: rgb(15 44 84 / 98%);
            box-shadow: 0 0 0 3px rgba(15, 44, 84, .08);
        }

        .ml-clear {
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

        .ml-list {
            margin-top: 15px;
        }

        .ml-letter {
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
        }

        .ml-letter:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: rgb(15 44 84 / 98%);
        }

        .ml-letter:hover {
            border-color: rgba(15, 44, 84, .28);
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(15, 44, 84, .09);
        }

        .ml-pdf {
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

        .ml-pdf img {
            width: 38px;
            height: 38px;
            object-fit: contain;
        }

        .ml-letter-info {
            flex: 1;
            min-width: 0;
            padding-right: 5px;
        }

        .ml-letter-subject {
            font-weight: 700;
            color: #172033;
            font-size: 15px;
            line-height: 1.35;
            margin-bottom: 6px;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .ml-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 7px;
            color: #7b8796;
            font-size: 12px;
        }

        .ml-meta-item {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .ml-meta-item i {
            font-size: 14px;
        }

        .ml-meta-label {
            font-weight: 600;
            color: #475569;
        }

        .ml-badge {
            background: rgba(15, 44, 84, .08);
            color: rgb(15 44 84 / 98%);
            border-radius: 20px;
            padding: 4px 9px;
            font-weight: 600;
            white-space: nowrap;
        }

        .ml-view {
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

        .ml-view i {
            font-size: 18px;
            line-height: 1;
        }

        .ml-view:hover {
            color: #fff !important;
            background: #79e605;
            transform: translateX(2px);
        }

        .ml-view-download {
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
            margin-left: 5px;
        }

        .ml-view-download i {
            font-size: 18px;
            line-height: 1;
        }

        .ml-view-download:hover {
            color: #fff !important;
            background: #475569;
            transform: translateX(2px);
        }

        .ml-empty {
            background: #fff;
            border: 1px dashed #cfd7e2;
            border-radius: 16px;
            padding: 45px 20px;
            text-align: center;
            color: #8a94a3;
        }

        .ml-empty-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 14px;
            border-radius: 50%;
            background: rgba(15, 44, 84, .07);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ml-empty-icon i {
            font-size: 32px;
            color: rgb(15 44 84 / 98%);
        }

        .ml-no-result {
            display: none;
            background: #fff;
            border: 1px dashed #cfd7e2;
            border-radius: 15px;
            padding: 35px 20px;
            text-align: center;
            color: #8a94a3;
        }

        .ml-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            margin-top: 18px;
            flex-wrap: wrap;
        }

        .ml-page {
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

        .ml-page:hover {
            border-color: rgb(15 44 84 / 98%);
            color: rgb(15 44 84 / 98%);
        }

        .ml-page.active {
            background: rgb(15 44 84 / 98%);
            color: #fff;
            border-color: rgb(15 44 84 / 98%);
        }

        .ml-page.disabled {
            opacity: .45;
            pointer-events: none;
        }

        .ml-result-info {
            text-align: center;
            color: #8a94a3;
            font-size: 12px;
            margin-top: 8px;
        }

        .ml-letter-actions {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-shrink: 0;
        }

        @media(max-width: 576px) {

            .ml-header {
                border-radius: 14px;
                padding: 17px;
            }

            .ml-title h4 {
                font-size: 17px;
            }

            .ml-title span {
                font-size: 11px;
            }

            .ml-header-top {
                gap: 10px;
            }

            .ml-back-btn,
            .ml-home-btn {
                width: 38px;
                height: 38px;
                min-width: 38px;
            }

            .ml-stats {
                gap: 7px;
                margin-top: 15px;
            }

            .ml-stat {
                padding: 7px 10px;
            }

            .ml-letter {
                padding: 12px 12px 12px 13px;
                gap: 10px;
                flex-wrap: wrap;
            }

            .ml-pdf {
                width: 44px;
                height: 44px;
                min-width: 44px;
                border-radius: 10px;
            }

            .ml-pdf img {
                width: 32px;
                height: 32px;
            }

            .ml-letter-info {
                padding-right: 2px;
                flex: 1 1 100%;
                order: 2;
            }

            .ml-letter-subject {
                font-size: 14px;
                line-height: 1.35;
            }

            .ml-meta {
                font-size: 11px;
                gap: 5px;
            }

            .ml-badge {
                padding: 4px 7px;
            }

            .ml-view,
            .ml-view-download {
                width: 36px;
                height: 36px;
                min-width: 36px;
                border-radius: 9px;
            }

            .ml-view i,
            .ml-view-download i {
                font-size: 16px;
            }

            .ml-letter-actions {
                order: 3;
                margin-left: auto;
            }
        }
    </style>

    <div class="row justify-content-center mt-2">
        <div class="col-12 col-xl-8">

            <div class="ml-wrapper">

                <div class="ml-header">

                    <div class="ml-header-top">

                        <a href="{{ route('dashboard') }}" class="ml-back-btn" title="Back">
                            <i class="ti ti-arrow-left"></i>
                        </a>

                        <div class="ml-title">
                            <h4>JPO</h4>
                            <span>All JPO Records &amp; Notices</span>
                        </div>

                        <a href="{{ route('dashboard') }}" class="ml-home-btn" title="Home">
                            <i class="ti ti-home"></i>
                        </a>

                    </div>

                    <div class="ml-stats">

                        <div class="ml-stat">
                            <strong id="totalLetters">
                                {{ $records->total() }}
                            </strong>
                            JPOs
                        </div>

                        <div class="ml-stat">
                            <i class="ti ti-file-text me-1"></i>
                            PDF Files
                        </div>

                    </div>

                </div>

                @if ($records->count() > 0)
                    <div class="ml-toolbar">

                        <div class="ml-search">

                            <i class="ti ti-search"></i>

                            <input type="text" id="mlSearch" class="form-control"
                                placeholder="Search by subject, JPO no., date..." autocomplete="off">

                            <button type="button" id="mlClear" class="ml-clear" title="Clear Search">
                                <i class="ti ti-x"></i>
                            </button>

                        </div>

                    </div>

                    <div class="ml-list" id="mlLetterList">

                        @foreach ($records as $record)
                            <div class="ml-letter"
                                data-search="{{ strtolower(($record->subject ?? '') . ' ' . ($record->jpo_number ?? '') . ' ' . ($record->date_of_jpo ?? '')) }}">

                                <div class="ml-pdf">
                                    <img src="{{ asset('assets/images/pdf.jpg') }}" alt="PDF">
                                </div>

                                <div class="ml-letter-info">

                                    <div class="ml-letter-subject">
                                        {{ $record->subject ?? 'Untitled' }}
                                    </div>

                                    <div class="ml-meta">

                                        <span class="ml-meta-item">
                                            <i class="ti ti-calendar"></i>
                                            <span class="ml-meta-label">Date:</span>
                                            {{ $record->date_of_jpo ? \Carbon\Carbon::parse($record->date_of_jpo)->format('d-m-Y') : '-' }}
                                        </span>

                                        <span>•</span>

                                        <span class="ml-meta-item">
                                            <i class="ti ti-file-text"></i>
                                            <span class="ml-meta-label">JPO No.:</span>
                                            {{ $record->jpo_number ?? '-' }}
                                        </span>

                                    </div>

                                </div>

                                <div class="ml-letter-actions">

                                    <a href="{{ asset('public/storage/' . $record->inspection_note_pdf) }}" target="_blank"
                                        class="ml-view" title="View PDF">
                                        <i class="ti ti-eye"></i>
                                    </a>

                                    <a href="{{ asset('public/storage/' . $record->inspection_note_pdf) }}" download
                                        class="ml-view" title="Download PDF">
                                        <i class="ti ti-download"></i>
                                    </a>

                                </div>

                            </div>
                        @endforeach

                    </div>

                    <div id="mlNoResult" class="ml-no-result">

                        <div class="ml-empty-icon">
                            <i class="ti ti-search-off"></i>
                        </div>

                        <div class="fw-semibold text-dark mb-1">
                            No JPO found
                        </div>

                        <div>
                            Try another search term.
                        </div>

                    </div>

                    <div class="ml-result-info" id="mlResultInfo">
                    </div>

                    <div class="ml-pagination" id="mlPagination">
                    </div>
                @else
                    <div class="ml-empty mt-3">

                        <div class="ml-empty-icon">
                            <i class="ti ti-file-off"></i>
                        </div>

                        <div class="fw-bold text-dark mb-1">
                            No JPO Records Available
                        </div>

                        <div class="small">
                            No JPO records have been uploaded yet.
                        </div>

                    </div>
                @endif

            </div>

        </div>
    </div>

    @if ($records->count() > 0)
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const searchInput = document.getElementById('mlSearch');
                const clearButton = document.getElementById('mlClear');
                const letters = Array.from(
                    document.querySelectorAll('.ml-letter')
                );
                const pagination = document.getElementById('mlPagination');
                const noResult = document.getElementById('mlNoResult');
                const resultInfo = document.getElementById('mlResultInfo');

                const perPage = 8;

                let currentPage = 1;
                let filteredLetters = [...letters];

                function render() {

                    const totalPages = Math.ceil(
                        filteredLetters.length / perPage
                    );

                    if (currentPage > totalPages && totalPages > 0) {
                        currentPage = totalPages;
                    }

                    letters.forEach(function(letter) {
                        letter.style.display = 'none';
                    });

                    if (filteredLetters.length === 0) {

                        noResult.style.display = 'block';
                        pagination.innerHTML = '';
                        resultInfo.textContent = '';

                        return;
                    }

                    noResult.style.display = 'none';

                    const start = (currentPage - 1) * perPage;
                    const end = start + perPage;

                    filteredLetters
                        .slice(start, end)
                        .forEach(function(letter) {
                            letter.style.display = 'flex';
                        });

                    const from = start + 1;
                    const to = Math.min(
                        end,
                        filteredLetters.length
                    );

                    resultInfo.textContent =
                        'Showing ' +
                        from +
                        '–' +
                        to +
                        ' of ' +
                        filteredLetters.length +
                        ' JPOs';

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
                        'ml-page' +
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
                            'ml-page' +
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
                        'ml-page' +
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

                    filteredLetters = letters.filter(
                        function(letter) {
                            return letter.dataset.search
                                .includes(value);
                        }
                    );

                    currentPage = 1;

                    render();
                });

                clearButton.addEventListener('click', function() {

                    searchInput.value = '';

                    clearButton.style.display = 'none';

                    filteredLetters = [...letters];

                    currentPage = 1;

                    render();

                    searchInput.focus();
                });

                render();

            });
        </script>
    @endif

@endsection
