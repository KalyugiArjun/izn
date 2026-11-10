@extends('layouts.app')

@section('page-title')
    {{ __('Traffic Inspector Inspection Notes') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('operating-safety-documents.traffic-inspector-inspection-notes.inspectors') }}">
            {{ __('Traffic Inspector Inspection Notes') }}
        </a>
    </li>
    <li class="breadcrumb-item">
        <a
            href="{{ route('operating-safety-documents.traffic-inspector-inspection-notes.types', urlencode($inspectorName)) }}">
            {{ $inspectorName }}
        </a>
    </li>
    <li class="breadcrumb-item" aria-current="page">
        {{ $type->name }}
    </li>
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
            max-width: 70%;
        }

        .ml-title h4 {
            margin: 0;
            font-weight: 800;
            letter-spacing: .3px;
            color: #fff !important;
            word-break: break-word;
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
            flex-wrap: wrap;
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

        .ml-filter-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .ml-search {
            position: relative;
            flex: 1;
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
            border-color: rgb(15 44 84 / 98%) !important;
            box-shadow: 0 0 0 3px rgba(15, 44, 84, .08) !important;
        }

        .ml-date {
            width: 190px;
        }

        .ml-date input {
            height: 46px !important;
            border-radius: 11px !important;
            border: 1px solid #dce3eb !important;
            box-shadow: none !important;
        }

        .ml-date input:focus {
            border-color: rgb(15 44 84 / 98%) !important;
            box-shadow: 0 0 0 3px rgba(15, 44, 84, .08) !important;
        }

        .ml-filter-btn {
            height: 46px;
            padding: 0 16px;
            border: 0;
            border-radius: 11px;
            background: rgb(15 44 84 / 98%);
            color: #fff !important;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none !important;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            white-space: nowrap;
            transition: .2s;
        }

        .ml-filter-btn:hover {
            color: #fff !important;
            background: #79e605;
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
            background: rgb(15 44 84 / 98%);
            color: #fff;
            border-radius: 20px;
            padding: 4px 9px;
            font-weight: 600;
            white-space: nowrap;
        }

        .ml-meta-item i {
            font-size: 14px;
        }

        .ml-meta-label {
            font-weight: 600;
            color: #fff;
        }

        .ml-letter-actions {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-shrink: 0;
            margin-left: auto;
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
        }

        .ml-view i {
            font-size: 18px;
            line-height: 1;
        }

        .ml-view:hover {
            color: #fff !important;
            background: #79e605;
            transform: translateY(-1px);
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
        }

        .ml-view-download i {
            font-size: 18px;
            line-height: 1;
        }

        .ml-view-download:hover {
            color: #fff !important;
            background: #475569;
            transform: translateY(-1px);
        }

        .ml-empty {
            background: #fff;
            border: 1px dashed #cfd7e2;
            border-radius: 16px;
            padding: 45px 20px;
            text-align: center;
            color: #8a94a3;
            margin-top: 15px;
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
            margin-top: 15px;
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

        @media(max-width: 767px) {

            .ml-filter-row {
                flex-direction: column;
                align-items: stretch;
            }

            .ml-search {
                width: 100%;
            }

            .ml-date {
                width: 100%;
            }

            .ml-filter-btn {
                width: 100%;
            }
        }

        @media(max-width: 576px) {

            .ml-header {
                border-radius: 14px;
                padding: 17px;
            }

            .ml-title {
                max-width: 65%;
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
                flex: 1;
                min-width: 0;
                padding-right: 0;
            }

            .ml-meta {
                font-size: 11px;
                gap: 5px;
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
                margin-left: auto;
            }
        }
    </style>

    <div class="row justify-content-center mt-2">
        <div class="col-12 col-xl-8">

            <div class="ml-wrapper">

                <div class="ml-header">

                    <div class="ml-header-top">

                        <a href="{{ route('operating-safety-documents.traffic-inspector-inspection-notes.types', urlencode($inspectorName)) }}"
                            class="ml-back-btn" title="Back">
                            <i class="ti ti-arrow-left"></i>
                        </a>

                        <div class="ml-title">
                            <h4>{{ $type->name }}</h4>
                            <span>{{ $inspectorName }}</span>
                        </div>

                        <a href="{{ route('dashboard') }}" class="ml-home-btn" title="Home">
                            <i class="ti ti-home"></i>
                        </a>

                    </div>

                    <div class="ml-stats">

                        <div class="ml-stat">
                            <strong id="totalItems">
                                {{ $records->count() }}
                            </strong>
                            {{ $records->count() == 1 ? 'Note' : 'Notes' }}
                        </div>

                        <div class="ml-stat">
                            <i class="ti ti-file-text me-1"></i>
                            PDF Files
                        </div>

                    </div>

                </div>

                @if ($records->count() > 0)
                    <div class="ml-toolbar">

                        <div class="ml-filter-row">

                            <div class="ml-search">

                                <i class="ti ti-search"></i>

                                <input type="text" id="mlSearch" class="form-control"
                                    placeholder="Search by subject or place..." autocomplete="off">

                            </div>

                            <div class="ml-date">
                                <input type="date" id="mlDate" class="form-control">
                            </div>

                            <button type="button" id="mlReset" class="ml-filter-btn">
                                <i class="ti ti-refresh"></i>
                                Reset
                            </button>

                        </div>

                    </div>

                    <div class="ml-list" id="mlLetterList">

                        @foreach ($records as $record)
                            <div class="ml-letter"
                                data-search="{{ strtolower(($record->subject ?? '') . ' ' . ($record->place_of_inspection ?? '') . ' ' . ($record->date_of_inspection ?? '')) }}"
                                data-date="{{ $record->date_of_inspection ? \Carbon\Carbon::parse($record->date_of_inspection)->format('Y-m-d') : '' }}">

                                <div class="ml-pdf">
                                    <img src="{{ asset('assets/images/pdf.jpg') }}" alt="PDF">
                                </div>

                                <div class="ml-letter-info">

                                    <div class="ml-meta">

                                        <span class="ml-meta-item">
                                            <i class="ti ti-calendar"></i>
                                            <span class="ml-meta-label">Date Of Inspection:</span>
                                            {{ $record->date_of_inspection ? \Carbon\Carbon::parse($record->date_of_inspection)->format('d-m-Y') : '-' }}
                                        </span>

                                        <span class="ml-meta-item">
                                            <i class="ti ti-map-pin"></i>
                                            <span class="ml-meta-label">Place Of Inspection:</span>
                                            {{ $record->place_of_inspection ?? '-' }}
                                        </span>

                                        <span class="ml-meta-item">
                                            <i class="ti ti-user"></i>
                                            <span class="ml-meta-label">Traffic Inspector:</span>
                                            {{ $inspectorName ?? '-' }}
                                        </span>

                                        <span class="ml-meta-item">
                                            <i class="ti ti-files"></i>
                                            <span class="ml-meta-label">Type of Inspection:</span>
                                            {{ $type->name }}
                                        </span>

                                    </div>

                                </div>

                                <div class="ml-letter-actions">

                                    @if (!empty($record->inspection_note_pdf))
                                        <a href="{{ asset('public/storage/' . $record->inspection_note_pdf) }}"
                                            target="_blank" class="ml-view" title="View PDF">
                                            <i class="ti ti-eye"></i>
                                        </a>

                                        <a href="{{ asset('public/storage/' . $record->inspection_note_pdf) }}" download
                                            class="ml-view" title="Download PDF">
                                            <i class="ti ti-download"></i>
                                        </a>
                                    @endif

                                </div>

                            </div>
                        @endforeach

                    </div>

                    <div id="mlNoResult" class="ml-no-result">

                        <div class="ml-empty-icon">
                            <i class="ti ti-search-off"></i>
                        </div>

                        <div class="fw-semibold text-dark mb-1">
                            No inspection notes found
                        </div>

                        <div>
                            Try another search term or date.
                        </div>

                    </div>

                    <div class="ml-result-info" id="mlResultInfo">
                    </div>

                    <div class="ml-pagination" id="mlPagination">
                    </div>
                @else
                    <div class="ml-empty">

                        <div class="ml-empty-icon">
                            <i class="ti ti-file-off"></i>
                        </div>

                        <div class="fw-bold text-dark mb-1">
                            No Inspection Notes Available
                        </div>

                        <div class="small">
                            <strong>{{ $inspectorName }}</strong> ke <strong>{{ $type->name }}</strong> me koi
                            inspection note nahi mila.
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
                const dateInput = document.getElementById('mlDate');
                const resetButton = document.getElementById('mlReset');

                const items = Array.from(
                    document.querySelectorAll('.ml-letter')
                );

                const pagination = document.getElementById('mlPagination');
                const noResult = document.getElementById('mlNoResult');
                const resultInfo = document.getElementById('mlResultInfo');

                const perPage = 8;

                let currentPage = 1;
                let filteredItems = [...items];

                function render() {

                    const totalPages = Math.ceil(
                        filteredItems.length / perPage
                    );

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

                        return;
                    }

                    noResult.style.display = 'none';

                    const start = (currentPage - 1) * perPage;
                    const end = start + perPage;

                    filteredItems
                        .slice(start, end)
                        .forEach(function(item) {
                            item.style.display = 'flex';
                        });

                    const from = start + 1;
                    const to = Math.min(
                        end,
                        filteredItems.length
                    );

                    resultInfo.textContent =
                        'Showing ' +
                        from +
                        '–' +
                        to +
                        ' of ' +
                        filteredItems.length +
                        ' inspection notes';

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

                function applyFilters() {

                    const searchValue =
                        searchInput.value
                        .trim()
                        .toLowerCase();

                    const selectedDate =
                        dateInput.value;

                    filteredItems =
                        items.filter(function(item) {

                            const searchText =
                                item.dataset.search || '';

                            const recordDate =
                                item.dataset.date || '';

                            const matchesSearch = !searchValue ||
                                searchText.includes(
                                    searchValue
                                );

                            const matchesDate = !selectedDate ||
                                recordDate === selectedDate;

                            return (
                                matchesSearch &&
                                matchesDate
                            );
                        });

                    currentPage = 1;

                    render();
                }

                searchInput.addEventListener(
                    'input',
                    applyFilters
                );

                dateInput.addEventListener(
                    'change',
                    applyFilters
                );

                resetButton.addEventListener(
                    'click',
                    function() {

                        searchInput.value = '';

                        dateInput.value = '';

                        filteredItems = [...items];

                        currentPage = 1;

                        render();

                    }
                );

                render();

            });
        </script>
    @endif

@endsection
