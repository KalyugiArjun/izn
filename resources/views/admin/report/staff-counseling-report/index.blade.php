@extends('layouts.app')

@section('page-title')
    {{ __('Module 4: Staff Counseling Record Report') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Home') }}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="javascript:void(0);">{{ __('Reports') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">
        {{ __('Staff Counseling Records') }}
    </li>
@endsection

@push('css-page')
<style>
    /* Filter Card Inputs */
    .counseling-filter-card .form-select,
    .counseling-filter-card .form-control {
        height: 44px !important;
        min-height: 44px !important;
        padding-top: 8px !important;
        padding-bottom: 8px !important;
        padding-left: 14px !important;
        padding-right: 14px !important;
        font-size: 14px !important;
        line-height: 1.5 !important;
        color: #1e293b !important;
        background-color: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 8px !important;
        box-shadow: none !important;
    }
    .counseling-filter-card .form-select {
        padding-right: 36px !important;
        background-position: right 12px center !important;
    }
    /* Compact DataTables pagination */
    .dataTables_paginate .paginate_button {
        min-width: 34px !important;
        height: 34px !important;
        padding: 4px 10px !important;
        margin: 0 2px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 6px !important;
        font-size: 13px !important;
    }
    .dataTables_paginate .paginate_button.current {
        background: #1e3c72 !important;
        border-color: #1e3c72 !important;
        color: #ffffff !important;
    }
</style>
@endpush

@section('content')
    <div class="row">

        {{-- KPI Metric Summary Cards --}}
        <div class="col-xl-12 mb-3">
            <div class="row g-3">
                {{-- 1. Total Counselings --}}
                <div class="col-xl-3 col-md-6 col-sm-6 col-12">
                    <a href="{{ route('report.staff-counseling.index') }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Total Counseling Records') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($totalCounselings) }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-users fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- 2. Online Counselings --}}
                <div class="col-xl-3 col-md-6 col-sm-6 col-12">
                    <a href="{{ route('report.staff-counseling.index', ['counseling_mode' => 'online']) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Online Counseling') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($onlineCounselings) }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-device-laptop fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- 3. Offline Counselings --}}
                <div class="col-xl-3 col-md-6 col-sm-6 col-12">
                    <a href="{{ route('report.staff-counseling.index', ['counseling_mode' => 'offline']) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Offline Counseling') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($offlineCounselings) }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-map-pin fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- 4. Group Counselings --}}
                <div class="col-xl-3 col-md-6 col-sm-6 col-12">
                    <a href="{{ route('report.staff-counseling.index', ['counseling_type' => 'Group']) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Group Counseling') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($groupCounselings) }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-user-check fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="col-12 mb-3">
            <div class="card border-0 shadow-sm counseling-filter-card" style="border-radius: 12px;">
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('report.staff-counseling.index') }}" id="counselingFilterForm">
                        <div class="row g-2 align-items-end">

                            {{-- 1. Counselor Filter --}}
                            <div class="col-xl-3 col-md-4 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">{{ __('Counselor Name') }}</label>
                                <select name="counselor_name" class="form-select no-select2">
                                    <option value="all">{{ __('All Counselors') }}</option>
                                    @foreach($counselors as $cn)
                                        <option value="{{ $cn }}" {{ request('counselor_name') == $cn ? 'selected' : '' }}>
                                            {{ $cn }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 2. Mode of Counseling Filter --}}
                            <div class="col-xl-2 col-md-4 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">{{ __('Mode of Counseling') }}</label>
                                <select name="counseling_mode" class="form-select no-select2">
                                    <option value="all">{{ __('All Modes') }}</option>
                                    <option value="online" {{ strtolower(request('counseling_mode')) === 'online' ? 'selected' : '' }}>Online</option>
                                    <option value="offline" {{ strtolower(request('counseling_mode')) === 'offline' ? 'selected' : '' }}>Offline</option>
                                </select>
                            </div>

                            {{-- 3. Location Filter --}}
                            <div class="col-xl-2 col-md-4 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">{{ __('Location Type') }}</label>
                                <select name="counseling_location" class="form-select no-select2">
                                    <option value="all">{{ __('All Locations') }}</option>
                                    <option value="station" {{ strtolower(request('counseling_location')) === 'station' ? 'selected' : '' }}>Station</option>
                                    <option value="diesel lobby" {{ strtolower(request('counseling_location')) === 'diesel lobby' ? 'selected' : '' }}>Diesel Lobby</option>
                                    <option value="running train" {{ strtolower(request('counseling_location')) === 'running train' ? 'selected' : '' }}>Running Train</option>
                                </select>
                            </div>

                            {{-- 4. Station Filter --}}
                            <div class="col-xl-2 col-md-3 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">{{ __('Station') }}</label>
                                <select name="station_id" class="form-select no-select2">
                                    <option value="all">{{ __('All Stations') }}</option>
                                    @foreach($stations as $st)
                                        <option value="{{ $st->id }}" {{ request('station_id') == $st->id ? 'selected' : '' }}>
                                            {{ $st->name }} ({{ $st->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 5. Counseling Type Filter --}}
                            <div class="col-xl-3 col-md-3 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">{{ __('Counseling Type') }}</label>
                                <select name="counseling_type" class="form-select no-select2">
                                    <option value="all">{{ __('All Types') }}</option>
                                    <option value="Individual" {{ request('counseling_type') === 'Individual' ? 'selected' : '' }}>Individual</option>
                                    <option value="Group" {{ request('counseling_type') === 'Group' ? 'selected' : '' }}>Group</option>
                                </select>
                            </div>

                            {{-- 6. Date Range: From Date --}}
                            <div class="col-xl-3 col-md-3 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">{{ __('From Date') }}</label>
                                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                            </div>

                            {{-- 7. Date Range: To Date --}}
                            <div class="col-xl-3 col-md-3 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">{{ __('To Date') }}</label>
                                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                            </div>

                            {{-- 8. Keyword Search --}}
                            <div class="col-xl-4 col-md-4 col-sm-8">
                                <label class="form-label small fw-semibold text-muted mb-1">{{ __('Search Keywords') }}</label>
                                <input type="text" name="search" class="form-control" placeholder="{{ __('Search counselor, subject, train no, LC gate...') }}" value="{{ request('search') }}">
                            </div>

                            {{-- 9. Action Buttons --}}
                            <div class="col-xl-2 col-md-2 col-sm-4 d-flex gap-2">
                                <button type="submit" class="btn btn-primary fw-semibold d-flex align-items-center justify-content-center flex-grow-1" style="height: 44px; border-radius: 8px;">
                                    <i class="ti ti-filter me-1 fs-5"></i>{{ __('Filter') }}
                                </button>
                                <a href="{{ route('report.staff-counseling.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="height: 44px; width: 44px; border-radius: 8px;" title="{{ __('Reset Filters') }}">
                                    <i class="ti ti-rotate"></i>
                                </a>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Table Section --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">

                {{-- Card Header --}}
                <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="ti ti-users text-primary fs-3"></i>
                            <span>{{ __('Module 4: Staff Counseling Records') }}</span>
                        </h5>
                        <p class="text-muted small mb-0 mt-1">
                            {{ __('Showing :count counseling records matching your criteria', ['count' => $records->count()]) }}
                        </p>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        {{-- Excel Export --}}
                        <a href="{{ route('report.staff-counseling.export.excel', request()->all()) }}" class="btn btn-outline-success fw-semibold btn-sm d-flex align-items-center px-3" style="height: 38px; border-radius: 8px;">
                            <i class="ti ti-file-spreadsheet me-1 fs-5"></i>{{ __('Export to Excel') }}
                        </a>

                        {{-- PDF Comprehensive Export --}}
                        <a href="{{ route('report.staff-counseling.export.pdf', request()->all()) }}" class="btn btn-outline-danger fw-semibold btn-sm d-flex align-items-center px-3" style="height: 38px; border-radius: 8px;" target="_blank">
                            <i class="ti ti-file-text me-1 fs-5"></i>{{ __('Comprehensive PDF') }}
                        </a>
                    </div>
                </div>

                {{-- Table View --}}
                <div class="card-body p-3">
                    <div class="dt-responsive table-responsive">
                        <table class="table table-hover align-middle mb-0 advance-datatable" id="staffCounselingTable">
                            <thead class="table-light text-uppercase small fw-bold" style="background-color: #f1f5f9; color: #475569;">
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>{{ __('Counseling (Date & Time)') }}</th>
                                    <th>{{ __('Counselor') }}</th>
                                    <th>{{ __('Counseling Staff') }}</th>
                                    <th>{{ __('Mode of Counseling') }}</th>
                                    <th>{{ __('Location') }}</th>
                                    <th style="min-width: 200px;">{{ __('Details Captured') }}</th>
                                    <th class="text-center">{{ __('Counseling Type') }}</th>
                                    <th class="text-center pe-3" style="min-width: 100px;">{{ __('ACTION') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $index => $rec)
                                    @php
                                        $formattedDate = $rec->counseling_date
                                            ? \Carbon\Carbon::parse($rec->counseling_date)->format('d M, Y')
                                            : ($rec->created_at ? $rec->created_at->format('d M, Y') : '-');
                                        $formattedTime = $rec->counseling_time ?: ($rec->created_at ? $rec->created_at->format('h:i A') : '-');

                                        $mode = strtolower($rec->counseling_mode ?? '');
                                        $isOnline = str_contains($mode, 'online');
                                        $loc = strtolower($rec->counseling_location ?? '');

                                        $staffList = $rec->resolved_staff_list;
                                    @endphp
                                    <tr>
                                        {{-- # Index --}}
                                        <td class="text-muted fw-semibold">{{ $index + 1 }}</td>

                                        {{-- Counseling (Date & Time) --}}
                                        <td>
                                            <div class="fw-bold text-dark">{{ $formattedDate }}</div>
                                            <div class="small text-muted"><i class="ti ti-clock me-1"></i>{{ $formattedTime }}</div>
                                        </td>

                                        {{-- Counselor --}}
                                        <td>
                                            <div class="fw-bold text-dark">{{ $rec->counselor_name ?: '-' }}</div>
                                            <div class="small text-muted">{{ $rec->counselor_designation_name }}</div>
                                        </td>

                                        {{-- Counseling Staff --}}
                                        <td>
                                            @if(empty($staffList))
                                                <span class="text-muted small">-</span>
                                            @else
                                                <div class="d-flex flex-column gap-1">
                                                    @foreach($staffList as $st)
                                                        <div class="d-flex align-items-center gap-1">
                                                            <span class="fw-semibold text-dark small">{{ $st['staff_name'] }}</span>
                                                            <span class="badge bg-light text-muted border font-11 px-1 py-0">{{ $st['designation_name'] }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Mode of Counseling --}}
                                        <td>
                                            @if($isOnline)
                                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 fw-semibold">
                                                    <i class="ti ti-device-laptop me-1"></i>ONLINE
                                                </span>
                                                @if($rec->calling_mode)
                                                    <div class="font-11 text-muted mt-1">Via: {{ $rec->calling_mode }}</div>
                                                @endif
                                            @else
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-semibold">
                                                    <i class="ti ti-map-pin me-1"></i>OFFLINE
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Location --}}
                                        <td>
                                            @if($isOnline)
                                                <span class="text-muted fw-bold">—</span>
                                            @else
                                                @if($loc === 'station')
                                                    <span class="badge bg-secondary-subtle text-dark fw-semibold">Station</span>
                                                    @if($rec->station)
                                                        <div class="small text-dark fw-semibold mt-1">{{ $rec->station->name }} ({{ $rec->station->code }})</div>
                                                    @endif
                                                @elseif($loc === 'diesel lobby')
                                                    <span class="badge bg-warning-subtle text-dark fw-semibold">Diesel Lobby</span>
                                                    @if($rec->dieselLobby)
                                                        <div class="small text-dark fw-semibold mt-1">{{ $rec->dieselLobby->name }}</div>
                                                    @endif
                                                @elseif($loc === 'running train')
                                                    <span class="badge bg-dark text-white fw-semibold">Running Train</span>
                                                    @if($rec->train_no)
                                                        <div class="small text-dark fw-semibold mt-1">Train #{{ $rec->train_no }}</div>
                                                    @endif
                                                @else
                                                    <span class="text-muted">{{ ucfirst($rec->counseling_location ?: '-') }}</span>
                                                @endif
                                            @endif
                                        </td>

                                        {{-- Details Captured (Subject / LC Gate / Train / Specific fields) --}}
                                        <td>
                                            <div class="small text-dark fw-semibold">
                                                {{ Str::limit($rec->subject_of_counseling ?: '-', 80) }}
                                            </div>
                                            <div class="d-flex flex-wrap gap-2 mt-1 font-11 text-muted">
                                                @if($rec->lc_gate)
                                                    <span><strong>LC Gate:</strong> {{ $rec->lc_gate }}</span>
                                                @endif
                                                @if($rec->train_no && $loc !== 'running train')
                                                    <span><strong>Train:</strong> {{ $rec->train_no }}</span>
                                                @endif
                                                @if($rec->gps_location)
                                                    <a href="https://maps.google.com/?q={{ $rec->gps_location }}" target="_blank" class="text-primary text-decoration-none">
                                                        <i class="ti ti-map-pin me-1"></i>GPS
                                                    </a>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Counseling Type (Individual / Group) --}}
                                        <td class="text-center">
                                            @if(strcasecmp($rec->counseling_type, 'Group') === 0)
                                                <span class="badge rounded-pill bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 font-12 fw-semibold">
                                                    <i class="ti ti-users me-1"></i>GROUP
                                                </span>
                                            @else
                                                <span class="badge rounded-pill bg-info-subtle text-info border border-info-subtle px-3 py-1 font-12 fw-semibold">
                                                    <i class="ti ti-user me-1"></i>INDIVIDUAL
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Action (Matching Image 2: Seen button & PDF button) --}}
                                        <td class="text-center pe-3">
                                            <div class="d-inline-flex gap-2">
                                                {{-- View Modal Button --}}
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary open-counseling-modal" data-id="{{ $rec->id }}" title="{{ __('View Details') }}" style="border-radius: 6px; width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center;">
                                                    <i class="ti ti-eye fs-5"></i>
                                                </button>

                                                {{-- Download Single Dossier PDF --}}
                                                <a href="{{ route('report.staff-counseling.export.counseling-pdf', $rec->id) }}" class="btn btn-sm btn-icon btn-outline-danger" title="{{ __('Download Counseling PDF') }}" target="_blank" style="border-radius: 6px; width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center;">
                                                    <i class="ti ti-file-text fs-5"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <div class="py-4">
                                                <i class="ti ti-database-off text-muted" style="font-size: 3rem;"></i>
                                                <h6 class="fw-bold text-muted mt-3">{{ __('No Staff Counseling Records Found') }}</h6>
                                                <p class="text-muted small mb-0">{{ __('Try adjusting your filter parameters or search terms.') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- Detail Modal --}}
    <div class="modal fade" id="counselingDetailModal" tabindex="-1" aria-labelledby="counselingDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;" id="counselingModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted fw-semibold">Loading Staff Counseling Details...</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
<script>
    $(document).ready(function () {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Re-initialize on DataTable redraws
        if ($.fn.DataTable) {
            $('#staffCounselingTable').on('draw.dt', function () {
                $('[data-bs-toggle="tooltip"]').tooltip();
            });
        }

        // Delegated View Modal Click
        $(document).on('click', '.open-counseling-modal', function (e) {
            e.preventDefault();
            var recordId = $(this).data('id');

            $('#counselingDetailModal').modal('show');
            $('#counselingModalBody').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2 text-muted fw-semibold">Loading Staff Counseling Details...</p></div>');

            $.ajax({
                url: "{{ url('report/staff-counseling-report') }}/" + recordId + "/show",
                type: "GET",
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function (response) {
                    $('#counselingModalBody').html(response);
                },
                error: function (xhr) {
                    $('#counselingModalBody').html('<div class="p-4 text-center text-danger"><i class="ti ti-alert-circle fs-1 mb-2 d-block"></i><p class="fw-semibold mb-0">Failed to load counseling details. Please try again.</p></div>');
                }
            });
        });
    });
</script>
@endpush

