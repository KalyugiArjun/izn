@extends('layouts.app')

@section('page-title')
    {{ __('Station Working Rule') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('operating-safety-documents.station-working-rule.stations') }}">
            {{ __('Operating & Safety Documents') }}
        </a>
    </li>
    <li class="breadcrumb-item" aria-current="page">
        {{ __('Station Working Rule') }}
    </li>
@endsection

@section('content')

    <style>
        .swr-wrapper {
            max-width: 900px;
            margin: 0 auto;
        }

        .swr-header {
            background: rgb(15 44 84 / 98%);
            border-radius: 18px;
            padding: 22px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(15, 44, 84, .18);
        }

        .swr-header:after {
            content: '';
            position: absolute;
            width: 190px;
            height: 190px;
            right: -75px;
            top: -90px;
            border: 25px solid rgba(255, 255, 255, .06);
            border-radius: 50%;
        }

        .swr-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }

        .swr-back-btn,
        .swr-home-btn {
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

        .swr-back-btn:hover,
        .swr-home-btn:hover {
            color: #fff;
            background: rgba(255, 255, 255, .22);
            transform: translateY(-1px);
        }

        .swr-title {
            text-align: center;
        }

        .swr-title h4 {
            margin: 0;
            font-weight: 800;
            letter-spacing: .3px;
            color: #fff !important;
        }

        .swr-title span {
            font-size: 12px;
            opacity: .78;
        }

        .swr-stats {
            margin-top: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            z-index: 2;
        }

        .swr-stat {
            background: rgba(255, 255, 255, .10);
            border: 1px solid rgba(255, 255, 255, .10);
            border-radius: 11px;
            padding: 9px 13px;
            font-size: 12px;
        }

        .swr-stat strong {
            font-size: 16px;
            margin-right: 4px;
        }

        .swr-toolbar {
            background: #fff;
            border: 1px solid #e5eaf0;
            border-radius: 15px;
            padding: 13px;
            margin-top: 15px;
            box-shadow: 0 4px 18px rgba(15, 44, 84, .05);
        }

        .swr-search {
            position: relative;
        }

        .swr-search>i {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #7b8796;
            font-size: 17px;
            z-index: 2;
        }

        .swr-search input {
            height: 46px !important;
            border-radius: 11px !important;
            padding-left: 43px !important;
            padding-right: 40px !important;
            border: 1px solid #dce3eb !important;
            box-shadow: none !important;
        }

        .swr-search input:focus {
            border-color: rgb(15 44 84 / 98%);
            box-shadow: 0 0 0 3px rgba(15, 44, 84, .08);
        }

        .swr-clear {
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

        .swr-list {
            margin-top: 15px;
        }

        .station-row {
            background: #fff;
            border: 1px solid #e5eaf0;
            border-radius: 15px;
            padding: 14px 16px 14px 15px;
            margin-bottom: 11px;
            display: flex;
            align-items: center;
            gap: 14px;
            position: relative;
            overflow: hidden;
            text-decoration: none !important;
            transition: all .2s ease;
        }

        .station-row:before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: rgb(15 44 84 / 98%);
        }

        .station-row:hover {
            border-color: rgba(15, 44, 84, .28);
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(15, 44, 84, .09);
        }

        .station-letter {
            width: 52px;
            height: 52px;
            min-width: 52px;
            border-radius: 12px;
            background: rgba(15, 44, 84, .08);
            color: rgb(15 44 84 / 98%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            font-weight: 800;
            flex-shrink: 0;
        }

        .station-info {
            flex: 1;
            min-width: 0;
            padding-right: 5px;
        }

        .station-name {
            display: block;
            font-weight: 700;
            color: #172033;
            font-size: 15px;
            line-height: 1.35;
            margin-bottom: 6px;
            word-break: break-word;
        }

        .station-meta {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 7px;
            color: #7b8796;
            font-size: 12px;
        }

        .station-badge {
            background: rgba(15, 44, 84, .08);
            color: rgb(15 44 84 / 98%);
            border-radius: 20px;
            padding: 4px 9px;
            font-weight: 600;
            white-space: nowrap;
        }

        .station-view {
            width: 40px;
            height: 40px;
            min-width: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgb(15 44 84 / 98%);
            color: #fff !important;
            flex-shrink: 0;
            margin-left: auto;
            transition: all .2s ease;
        }

        .station-row:hover .station-view {
            background: #79e605;
            transform: translateX(2px);
        }

        .swr-empty {
            background: #fff;
            border: 1px dashed #cfd7e2;
            border-radius: 16px;
            padding: 45px 20px;
            text-align: center;
            color: #8a94a3;
            margin-top: 15px;
        }

        .swr-empty-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 14px;
            border-radius: 50%;
            background: rgba(15, 44, 84, .07);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .swr-empty-icon i {
            font-size: 32px;
            color: rgb(15 44 84 / 98%);
        }

        .swr-no-result {
            display: none;
            background: #fff;
            border: 1px dashed #cfd7e2;
            border-radius: 15px;
            padding: 35px 20px;
            text-align: center;
            color: #8a94a3;
            margin-top: 15px;
        }

        .swr-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            margin-top: 18px;
            flex-wrap: wrap;
        }

        .swr-page {
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

        .swr-page:hover {
            border-color: rgb(15 44 84 / 98%);
            color: rgb(15 44 84 / 98%);
        }

        .swr-page.active {
            background: rgb(15 44 84 / 98%);
            color: #fff;
            border-color: rgb(15 44 84 / 98%);
        }

        .swr-page.disabled {
            opacity: .45;
            pointer-events: none;
        }

        .swr-result-info {
            text-align: center;
            color: #8a94a3;
            font-size: 12px;
            margin-top: 8px;
        }

        @media(max-width: 576px) {

            .swr-header {
                border-radius: 14px;
                padding: 17px;
            }

            .swr-title h4 {
                font-size: 17px;
            }

            .swr-title span {
                font-size: 11px;
            }

            .swr-header-top {
                gap: 10px;
            }

            .swr-back-btn,
            .swr-home-btn {
                width: 38px;
                height: 38px;
                min-width: 38px;
            }

            .swr-stats {
                gap: 7px;
                margin-top: 15px;
            }

            .swr-stat {
                padding: 7px 10px;
            }

            .station-row {
                padding: 12px 12px 12px 13px;
                gap: 10px;
            }

            .station-letter {
                width: 44px;
                height: 44px;
                min-width: 44px;
                border-radius: 10px;
                font-size: 16px;
            }

            .station-info {
                padding-right: 2px;
            }

            .station-name {
                font-size: 14px;
            }

            .station-meta {
                font-size: 11px;
                gap: 5px;
            }

            .station-badge {
                padding: 4px 7px;
            }

            .station-view {
                width: 36px;
                height: 36px;
                min-width: 36px;
                border-radius: 9px;
            }

            .station-view i {
                font-size: 16px;
            }
        }
    </style>

    <div class="row justify-content-center mt-2">
        <div class="col-12 col-xl-8">

            <div class="swr-wrapper">

                <div class="swr-header">

                    <div class="swr-header-top">

                        <a href="{{ route('operating-safety-documents.station-working-rule.stations') }}"
                            class="swr-back-btn" title="Back">
                            <i class="ti ti-arrow-left"></i>
                        </a>

                        <div class="swr-title">
                            <h4>Station Working Rule</h4>
                            <span>Operating & Safety Documents</span>
                        </div>

                        <a href="{{ route('dashboard') }}" class="swr-home-btn" title="Home">
                            <i class="ti ti-home"></i>
                        </a>

                    </div>

                    <div class="swr-stats">

                        <div class="swr-stat">
                            <strong id="totalStations">
                                {{ $stations->count() }}
                            </strong>
                            Stations
                        </div>

                        <div class="swr-stat">
                            <i class="ti ti-file-text me-1"></i>
                            Original + Corrections
                        </div>

                    </div>

                </div>

                @if ($stations->count() > 0)
                    <div class="swr-toolbar">

                        <div class="swr-search">

                            <i class="ti ti-search"></i>

                            <input type="text" id="stationSearch" class="form-control"
                                placeholder="{{ __('Search station...') }}" autocomplete="off">

                            <button type="button" id="stationClear" class="swr-clear" title="Clear Search">
                                <i class="ti ti-x"></i>
                            </button>

                        </div>

                    </div>

                    <div class="swr-list" id="stationList">

                        @foreach ($stations as $station)
                            @php
                                $letter = strtoupper(substr($station->name, 0, 1));
                                $slipCount = $station->total_slips ?? 0;
                            @endphp

                            <a href="{{ route('operating-safety-documents.station-working-rule.show', $station->id) }}"
                                class="station-row" data-name="{{ strtolower($station->name) }}">

                                <span class="station-letter">
                                    {{ $letter }}
                                </span>

                                <span class="station-info">

                                    <span class="station-name">
                                        {{ $station->name }}
                                    </span>

                                    <span class="station-meta">

                                        <span>
                                            <i class="ti ti-file-text me-1"></i>
                                            {{ __('Original') }}
                                        </span>

                                        <span>•</span>

                                        <span class="station-badge">
                                            <i class="ti ti-file-symlink me-1"></i>
                                            {{ str_pad($slipCount, 2, '0', STR_PAD_LEFT) }}
                                            {{ __('Correction Slips') }}
                                        </span>

                                    </span>

                                </span>

                                <span class="station-view" title="View Station">
                                    <i class="ti ti-arrow-right"></i>
                                </span>

                            </a>
                        @endforeach

                    </div>

                    <div id="stationNoResult" class="swr-no-result">

                        <div class="swr-empty-icon">
                            <i class="ti ti-search-off"></i>
                        </div>

                        <div class="fw-semibold text-dark mb-1">
                            {{ __('No station found') }}
                        </div>

                        <div>
                            {{ __('Try another station name.') }}
                        </div>

                    </div>

                    <div class="swr-result-info" id="stationResultInfo">
                    </div>

                    <div class="swr-pagination" id="stationPagination">
                    </div>
                @else
                    <div class="swr-empty">

                        <div class="swr-empty-icon">
                            <i class="ti ti-building-off"></i>
                        </div>

                        <div class="fw-bold text-dark mb-1">
                            {{ __('No Station Working Rule Available') }}
                        </div>

                        <div class="small">
                            {{ __('No station working rule has been added yet.') }}
                        </div>

                    </div>
                @endif

            </div>

        </div>
    </div>

    @if ($stations->count() > 0)
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const searchInput = document.getElementById('stationSearch');
                const clearButton = document.getElementById('stationClear');
                const stations = Array.from(
                    document.querySelectorAll('.station-row')
                );

                const pagination = document.getElementById('stationPagination');
                const noResult = document.getElementById('stationNoResult');
                const resultInfo = document.getElementById('stationResultInfo');

                const perPage = 8;

                let currentPage = 1;
                let filteredStations = [...stations];

                function render() {

                    const totalPages = Math.ceil(
                        filteredStations.length / perPage
                    );

                    if (currentPage > totalPages && totalPages > 0) {
                        currentPage = totalPages;
                    }

                    stations.forEach(function(station) {
                        station.style.display = 'none';
                    });

                    if (filteredStations.length === 0) {

                        noResult.style.display = 'block';
                        pagination.innerHTML = '';
                        resultInfo.textContent = '';

                        return;
                    }

                    noResult.style.display = 'none';

                    const start = (currentPage - 1) * perPage;
                    const end = start + perPage;

                    filteredStations
                        .slice(start, end)
                        .forEach(function(station) {
                            station.style.display = 'flex';
                        });

                    const from = start + 1;
                    const to = Math.min(
                        end,
                        filteredStations.length
                    );

                    resultInfo.textContent =
                        'Showing ' +
                        from +
                        '–' +
                        to +
                        ' of ' +
                        filteredStations.length +
                        ' stations';

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
                        'swr-page' +
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
                            'swr-page' +
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
                        'swr-page' +
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

                    filteredStations = stations.filter(
                        function(station) {
                            return station.dataset.name.includes(value);
                        }
                    );

                    currentPage = 1;

                    render();
                });

                clearButton.addEventListener('click', function() {

                    searchInput.value = '';

                    clearButton.style.display = 'none';

                    filteredStations = [...stations];

                    currentPage = 1;

                    render();

                    searchInput.focus();
                });

                render();

            });
        </script>
    @endif

@endsection
