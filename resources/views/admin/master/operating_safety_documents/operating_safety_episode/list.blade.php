@extends('layouts.app')

@section('page-title')
    {{ $title }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('operating-safety-documents.episode.show') }}">
            {{ __('Operating Safety Episode') }}
        </a>
    </li>

    <li class="breadcrumb-item" aria-current="page">
        {{ $title }}
    </li>
@endsection

@section('content')

    <style>
        .ose-list-wrapper {
            max-width: 900px;
            margin: 0 auto;
        }

        .ose-list-header {
            background: rgb(15 44 84 / 98%);
            border-radius: 18px;
            padding: 22px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(15, 44, 84, .18);
        }

        .ose-list-header:after {
            content: '';
            position: absolute;
            width: 190px;
            height: 190px;
            right: -75px;
            top: -90px;
            border: 25px solid rgba(255, 255, 255, .06);
            border-radius: 50%;
        }

        .ose-list-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }

        .ose-list-back-btn,
        .ose-list-home-btn {
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

        .ose-list-back-btn:hover,
        .ose-list-home-btn:hover {
            color: #fff;
            background: rgba(255, 255, 255, .22);
            transform: translateY(-1px);
        }

        .ose-list-title {
            text-align: center;
        }

        .ose-list-title h4 {
            margin: 0;
            font-weight: 800;
            letter-spacing: .3px;
            color: white !important;
        }

        .ose-list-title span {
            font-size: 12px;
            opacity: .78;
        }

        .ose-list-stats {
            margin-top: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            z-index: 2;
            flex-wrap: wrap;
        }

        .ose-list-stat {
            background: rgba(255, 255, 255, .10);
            border: 1px solid rgba(255, 255, 255, .10);
            border-radius: 11px;
            padding: 9px 13px;
            font-size: 12px;
        }

        .ose-list-stat strong {
            font-size: 16px;
            margin-right: 4px;
        }

        .ose-list-stat.highlight {
            background: rgba(121, 230, 5, .12);
            border-color: rgba(121, 230, 5, .2);
        }

        .ose-list-toolbar {
            background: #fff;
            border: 1px solid #e5eaf0;
            border-radius: 15px;
            padding: 13px !important;
            margin-top: 15px;
            box-shadow: 0 4px 18px rgba(15, 44, 84, .05);
        }

        .ose-list-search {
            position: relative;
        }

        .ose-list-search>i {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #7b8796;
            font-size: 17px;
            z-index: 2;
        }

        .ose-list-search input {
            height: 46px !important;
            border-radius: 11px !important;
            padding-left: 43px !important;
            padding-right: 40px !important;
            border: 1px solid #dce3eb !important;
            box-shadow: none !important;
        }

        .ose-list-search input:focus {
            border-color: rgb(15 44 84 / 98%);
            box-shadow: 0 0 0 3px rgba(15, 44, 84, .08);
        }

        .ose-list-clear {
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

        .ose-list-container {
            margin-top: 15px;
        }

        .ose-list-episode {
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

        .ose-list-episode:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: rgb(15 44 84 / 98%);
            border-radius: 0 3px 3px 0;
        }

        .ose-list-episode:hover {
            border-color: rgb(15 44 84 / 98%);
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(15, 44, 84, .09);
        }

        .ose-list-pdf {
            width: 52px;
            height: 52px;
            min-width: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .ose-list-pdf img {
            width: 38px;
            height: 38px;
            object-fit: contain;
        }

        .ose-list-episode-info {
            flex: 1;
            min-width: 0;
            padding-right: 5px;
        }

        .ose-list-episode-title {
            font-weight: 700;
            color: #172033;
            font-size: 15px;
            line-height: 1.35;
            margin-bottom: 6px;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .ose-list-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 7px;
            color: #7b8796;
            font-size: 12px;
        }

        .ose-list-badge {
            background: rgb(15 44 84 / 98%);
            color: #fff;
            border-radius: 20px;
            padding: 4px 9px;
            font-weight: 600;
            white-space: nowrap;
        }

        .ose-list-actions {
            display: flex;
            align-items: center;
            gap: 5px;
            flex-shrink: 0;
        }

        .ose-list-view {
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

        .ose-list-view i {
            font-size: 18px;
            line-height: 1;
        }

        .ose-list-view:hover {
            color: #fff !important;
            background: #79e605;
            transform: translateX(2px);
        }

        .ose-list-view-download {
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

        .ose-list-view-download i {
            font-size: 18px;
            line-height: 1;
        }

        .ose-list-view-download:hover {
            color: #fff !important;
            background: #475569;
            transform: translateX(2px);
        }

        .ose-list-empty {
            background: #fff;
            border: 1px dashed #cfd7e2;
            border-radius: 16px;
            padding: 45px 20px;
            text-align: center;
            color: #8a94a3;
        }

        .ose-list-empty-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 14px;
            border-radius: 50%;
            background: rgba(15, 44, 84, .07);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ose-list-empty-icon i {
            font-size: 32px;
            color: rgb(15 44 84 / 98%);
        }

        .ose-list-no-result {
            display: none;
            background: #fff;
            border: 1px dashed #cfd7e2;
            border-radius: 15px;
            padding: 35px 20px;
            text-align: center;
            color: #8a94a3;
        }

        .ose-list-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            margin-top: 18px;
            flex-wrap: wrap;
        }

        .ose-list-page {
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

        .ose-list-page:hover {
            border-color: rgb(15 44 84 / 98%);
            color: rgb(15 44 84 / 98%);
        }

        .ose-list-page.active {
            background: rgb(15 44 84 / 98%);
            color: #fff;
            border-color: rgb(15 44 84 / 98%);
        }

        .ose-list-page.disabled {
            opacity: .45;
            pointer-events: none;
        }

        .ose-list-result-info {
            text-align: center;
            color: #8a94a3;
            font-size: 12px;
            margin-top: 8px;
        }

        @media(max-width: 576px) {

            .ose-list-header {
                border-radius: 14px;
                padding: 17px;
            }

            .ose-list-title h4 {
                font-size: 17px;
            }

            .ose-list-title span {
                font-size: 11px;
            }

            .ose-list-header-top {
                gap: 10px;
            }

            .ose-list-back-btn,
            .ose-list-home-btn {
                width: 38px;
                height: 38px;
                min-width: 38px;
            }

            .ose-list-stats {
                gap: 7px;
                margin-top: 15px;
            }

            .ose-list-stat {
                padding: 7px 10px;
            }

            .ose-list-episode {
                padding: 12px 12px 12px 13px;
                gap: 10px;
                flex-wrap: wrap;
            }

            .ose-list-pdf {
                width: 44px;
                height: 44px;
                min-width: 44px;
                border-radius: 10px;
            }

            .ose-list-pdf img {
                width: 32px;
                height: 32px;
            }

            .ose-list-episode-info {
                padding-right: 2px;
                flex: 1 1 100%;
                order: 2;
            }

            .ose-list-episode-title {
                font-size: 14px;
                line-height: 1.35;
            }

            .ose-list-meta {
                font-size: 11px;
                gap: 5px;
            }

            .ose-list-badge {
                padding: 4px 7px;
            }

            .ose-list-view,
            .ose-list-view-download {
                width: 36px;
                height: 36px;
                min-width: 36px;
                border-radius: 9px;
            }

            .ose-list-view i,
            .ose-list-view-download i {
                font-size: 16px;
            }

            .ose-list-actions {
                order: 3;
                margin-left: auto;
            }
        }
    </style>

    <div class="row justify-content-center mt-2">
        <div class="col-12 col-xl-8">

            <div class="ose-list-wrapper">

                <div class="ose-list-header">

                    <div class="ose-list-header-top">

                        <a href="{{ route('operating-safety-documents.episode.show') }}" class="ose-list-back-btn"
                            title="Back">
                            <i class="ti ti-arrow-left"></i>
                        </a>

                        <div class="ose-list-title">
                            <h4>{{ $title }}</h4>
                            <span>Filtered Safety Episodes</span>
                        </div>

                        <a href="{{ route('dashboard') }}" class="ose-list-home-btn" title="Home">
                            <i class="ti ti-home"></i>
                        </a>

                    </div>

                    <div class="ose-list-stats">

                        <div class="ose-list-stat highlight">
                            <strong id="oseListTotal">{{ $records->count() }}</strong>
                            Total Episodes
                        </div>

                        <div class="ose-list-stat">
                            <i class="ti ti-file-text me-1"></i>
                            PDF Files
                        </div>

                        <div class="ose-list-stat"
                            style="background: rgba(15, 44, 84, .15); border-color: rgba(15, 44, 84, .2);">
                            <i class="ti ti-target me-1"></i>
                            {{ $filter === 'all' ? 'All' : ($filter === 'year' ? 'Year' : 'Month') }}
                        </div>

                    </div>

                </div>

                @if ($records->count() > 0)
                    <div class="ose-list-toolbar">

                        <div class="ose-list-search">

                            <i class="ti ti-search"></i>

                            <input type="text" id="oseListSearch" class="form-control"
                                placeholder="Search by Episode No. or Subject..." autocomplete="off">

                            <button type="button" id="oseListClear" class="ose-list-clear" title="Clear Search">
                                <i class="ti ti-x"></i>
                            </button>

                        </div>

                    </div>

                    <div class="ose-list-container" id="oseListContainer">

                        @foreach ($records as $record)
                            <div class="ose-list-episode"
                                data-search="{{ strtolower(($record->episode_no ?? '') . ' ' . ($record->subject ?? '')) }}">

                                <div class="ose-list-pdf">
                                    <img src="{{ asset('assets/images/pdf.jpg') }}" alt="PDF">
                                </div>

                                <div class="ose-list-episode-info">

                                    <div class="ose-list-episode-title">
                                        Episode No. {{ $record->episode_no }}
                                    </div>

                                    <div class="ose-list-meta">

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

                                        <span class="ose-list-badge">
                                            <i class="ti ti-target me-1"></i>
                                            Safety Episode
                                        </span>

                                    </div>

                                </div>

                                <div class="ose-list-actions">

                                    <a href="{{ asset('public/storage/' . $record->pdf_file) }}" target="_blank"
                                        class="ose-list-view" title="View PDF">
                                        <i class="ti ti-eye"></i>
                                    </a>

                                    <a href="{{ asset('public/storage/' . $record->pdf_file) }}" download
                                        class="ose-list-view" title="Download PDF">
                                        <i class="ti ti-download"></i>
                                    </a>

                                </div>

                            </div>
                        @endforeach

                    </div>

                    <div id="oseListNoResult" class="ose-list-no-result">

                        <div class="ose-list-empty-icon">
                            <i class="ti ti-search-off"></i>
                        </div>

                        <div class="fw-semibold text-dark mb-1">
                            No episode found
                        </div>

                        <div>
                            Try another episode number or subject.
                        </div>

                    </div>

                    <div class="ose-list-result-info" id="oseListResultInfo">
                    </div>

                    <div class="ose-list-pagination" id="oseListPagination">
                    </div>
                @else
                    <div class="ose-list-empty mt-3">

                        <div class="ose-list-empty-icon">
                            <i class="ti ti-file-off"></i>
                        </div>

                        <div class="fw-bold text-dark mb-1">
                            No Episodes Found
                        </div>

                        <div class="small">
                            No episodes match the selected filter.
                        </div>

                        <a href="{{ route('operating-safety-documents.episode.show') }}"
                            class="btn btn-outline-primary mt-3 rounded-pill px-4">
                            <i class="ti ti-arrow-left me-1"></i>
                            Back to All Episodes
                        </a>

                    </div>
                @endif

            </div>

        </div>
    </div>

    @if ($records->count() > 0)
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const searchInput = document.getElementById('oseListSearch');
                const clearButton = document.getElementById('oseListClear');
                const episodes = Array.from(document.querySelectorAll('.ose-list-episode'));
                const pagination = document.getElementById('oseListPagination');
                const noResult = document.getElementById('oseListNoResult');
                const resultInfo = document.getElementById('oseListResultInfo');
                const totalEpisodes = document.getElementById('oseListTotal');

                const perPage = 8;

                let currentPage = 1;
                let filteredEpisodes = [...episodes];

                function render() {

                    const totalPages = Math.ceil(filteredEpisodes.length / perPage);

                    if (currentPage > totalPages && totalPages > 0) {
                        currentPage = totalPages;
                    }

                    episodes.forEach(function(episode) {
                        episode.style.display = 'none';
                    });

                    if (filteredEpisodes.length === 0) {

                        noResult.style.display = 'block';
                        pagination.innerHTML = '';
                        resultInfo.textContent = '';
                        totalEpisodes.textContent = '0';

                        return;
                    }

                    noResult.style.display = 'none';

                    const start = (currentPage - 1) * perPage;
                    const end = Math.min(start + perPage, filteredEpisodes.length);

                    filteredEpisodes.slice(start, end).forEach(function(episode) {
                        episode.style.display = 'flex';
                    });

                    const from = start + 1;

                    resultInfo.textContent =
                        'Showing ' + from + '–' + end + ' of ' + filteredEpisodes.length + ' episodes';

                    totalEpisodes.textContent = filteredEpisodes.length;

                    renderPagination(totalPages);
                }

                function renderPagination(totalPages) {

                    pagination.innerHTML = '';

                    if (totalPages <= 1) {
                        return;
                    }

                    const previous = document.createElement('button');
                    previous.type = 'button';
                    previous.className = 'ose-list-page' + (currentPage === 1 ? ' disabled' : '');
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
                        button.className = 'ose-list-page' + (i === currentPage ? ' active' : '');
                        button.textContent = i;
                        button.addEventListener('click', function() {
                            currentPage = i;
                            render();
                        });
                        pagination.appendChild(button);
                    }

                    const next = document.createElement('button');
                    next.type = 'button';
                    next.className = 'ose-list-page' + (currentPage === totalPages ? ' disabled' : '');
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

                    filteredEpisodes = episodes.filter(function(episode) {
                        return episode.dataset.search.includes(value);
                    });

                    currentPage = 1;
                    render();
                });

                clearButton.addEventListener('click', function() {

                    searchInput.value = '';
                    clearButton.style.display = 'none';
                    filteredEpisodes = [...episodes];
                    currentPage = 1;
                    render();
                    searchInput.focus();
                });

                render();

            });
        </script>
    @endif

@endsection
