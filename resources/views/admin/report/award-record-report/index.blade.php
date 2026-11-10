@extends('layouts.app')

@section('page-title')
    {{ __('Award Records Report') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Home') }}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="javascript:void(0);">{{ __('Reports') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">
        {{ __('Award Records') }}
    </li>
@endsection

@push('css-page')
<style>
    /* Styling for Filter Card Inputs */
    .award-filter-card .form-select,
    .award-filter-card .form-control {
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
    .award-filter-card .form-select {
        padding-right: 36px !important;
        background-position: right 12px center !important;
    }

    /* Ensure DataTables pagination arrows and buttons stay neat and compact */
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
    /* Hide oversized SVGs if injected */
    .dataTables_wrapper svg {
        max-width: 16px !important;
        max-height: 16px !important;
    }
</style>
@endpush

@section('content')
    <div class="row">

        {{-- KPI Metric Summary Cards --}}
        <div class="col-xl-12 mb-3">
            <div class="row g-3">
                {{-- 1. Total Nominations --}}
                <div class="col-xl-3 col-md-6 col-sm-6 col-12">
                    <a href="{{ route('report.award-record.index') }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Total Nominations') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['total']) }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-trophy fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- 2. Awarded --}}
                <div class="col-xl-3 col-md-6 col-sm-6 col-12">
                    <a href="{{ route('report.award-record.index', ['status' => 2]) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Awarded (Approved)') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['awarded']) }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-award fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- 3. Rejected --}}
                <div class="col-xl-3 col-md-6 col-sm-6 col-12">
                    <a href="{{ route('report.award-record.index', ['status' => 3]) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Rejected') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['rejected']) }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-circle-x fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- 4. Pending Review --}}
                <div class="col-xl-3 col-md-6 col-sm-6 col-12">
                    <a href="{{ route('report.award-record.index', ['status' => 0]) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Pending Review') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['pending']) }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-clock-hour-4 fs-3 text-white"></i>
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
            <div class="card border-0 shadow-sm award-filter-card" style="border-radius: 12px;">
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('report.award-record.index') }}" id="awardFilterForm">
                        <div class="row g-2 align-items-end">

                            {{-- 1. Status Filter --}}
                            <div class="col-xl-2 col-md-3 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">{{ __('Status') }}</label>
                                <select name="status" class="form-select no-select2">
                                    <option value="all">{{ __('All Statuses') }}</option>
                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('Pending (TI)') }}</option>
                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('Recommended (TI)') }}</option>
                                    <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>{{ __('Awarded (Approved)') }}</option>
                                    <option value="3" {{ request('status') === '3' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                                </select>
                            </div>

                            {{-- 2. Station Filter --}}
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

                            {{-- 3. Area of Proficiency Filter --}}
                            <div class="col-xl-2 col-md-3 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">{{ __('Area of Proficiency') }}</label>
                                <select name="proficiency_id" class="form-select no-select2">
                                    <option value="all">{{ __('All Areas') }}</option>
                                    @foreach($proficiencies as $prof)
                                        <option value="{{ $prof->id }}" {{ request('proficiency_id') == $prof->id ? 'selected' : '' }}>
                                            {{ $prof->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 4. Date From --}}
                            <div class="col-xl-2 col-md-3 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">{{ __('From Date') }}</label>
                                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                            </div>

                            {{-- 5. Date To --}}
                            <div class="col-xl-2 col-md-3 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">{{ __('To Date') }}</label>
                                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                            </div>

                            {{-- 6. Search Input --}}
                            <div class="col-xl-2 col-md-6 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">{{ __('Search') }}</label>
                                <input type="text" name="search" class="form-control" placeholder="{{ __('Nominee, cert no...') }}" value="{{ request('search') }}">
                            </div>

                            {{-- 7. Submit / Reset --}}
                            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                                <button type="submit" class="btn btn-primary fw-semibold d-inline-flex align-items-center px-4" style="height: 40px; border-radius: 8px;">
                                    <i class="ti ti-filter me-1 fs-5"></i>{{ __('Apply Filter') }}
                                </button>
                                <a href="{{ route('report.award-record.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center" style="height: 40px; width: 40px; border-radius: 8px;" title="{{ __('Reset Filters') }}">
                                    <i class="ti ti-rotate"></i>
                                </a>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Table Section (Clean Standard Style matching other reports) --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">

                {{-- Card Header --}}
                <div class="card-header bg-white py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-3 border-bottom">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="ti ti-trophy text-primary me-2"></i>{{ __('Award Records') }}
                        </h5>
                        <small class="text-muted">
                            {{ __('Total :count records matching your filter criteria', ['count' => count($records)]) }}
                        </small>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        {{-- Excel Export --}}
                        <a href="{{ route('report.award-record.export.excel', request()->all()) }}" class="btn btn-outline-success fw-semibold btn-sm d-flex align-items-center px-3" style="height: 38px; border-radius: 8px;">
                            <i class="ti ti-file-spreadsheet me-1 fs-5"></i>{{ __('Export to Excel') }}
                        </a>

                        {{-- PDF Comprehensive Export --}}
                        <a href="{{ route('report.award-record.export.pdf', request()->all()) }}" class="btn btn-outline-danger fw-semibold btn-sm d-flex align-items-center px-3" style="height: 38px; border-radius: 8px;" target="_blank">
                            <i class="ti ti-file-text me-1 fs-5"></i>{{ __('Comprehensive PDF') }}
                        </a>
                    </div>
                </div>

                {{-- Table View with advance-datatable (Buttons [Excel] [PDF] [Copy] + SEARCH box + clean pagination) --}}
                <div class="card-body p-3">
                    <div class="dt-responsive table-responsive">
                        <table class="table table-hover align-middle mb-0 advance-datatable" id="awardRecordTable">
                            <thead class="table-light text-uppercase small fw-bold" style="background-color: #f1f5f9; color: #475569;">
                                <tr>
                                    <th class="ps-3 py-3" style="width: 45px;">#</th>
                                    <th style="min-width: 130px;">{{ __('Nomination (Date & Time)') }}</th>
                                    <th style="min-width: 170px;">{{ __('Nominee') }}</th>
                                    <th style="min-width: 130px;">{{ __('Station') }}</th>
                                    <th style="min-width: 150px;">{{ __('Area of Proficiency') }}</th>
                                    <th style="min-width: 220px;">{{ __('Description') }}</th>
                                    <th class="text-center" style="min-width: 110px;">{{ __('Uploaded PDF / JPG') }}</th>
                                    <th class="text-center" style="min-width: 140px;">{{ __('Decision') }}</th>
                                    <th class="text-center" style="min-width: 140px;">{{ __('Certificate PDF') }}</th>
                                    <th class="text-center pe-3" style="min-width: 100px;">{{ __('ACTION') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $index => $rec)
                                    @php
                                        $formattedDate = $rec->date ? \Carbon\Carbon::parse($rec->date)->format('d M, Y') : ($rec->created_at ? $rec->created_at->format('d M, Y') : '-');
                                        $formattedTime = $rec->time ? \Carbon\Carbon::parse($rec->time)->format('h:i A') : ($rec->created_at ? $rec->created_at->format('h:i A') : '-');

                                        $nomineeName = $rec->name ?: ($rec->staff->name ?? 'N/A');
                                        $designationName = $rec->designation->name ?? ($rec->staff->designation->name ?? 'N/A');
                                        $stationName = $rec->station->name ?? ($rec->station->code ?? '-');

                                        $fileExt = strtolower(pathinfo($rec->file ?? '', PATHINFO_EXTENSION));
                                        $isPdf = ($fileExt === 'pdf' || strtolower($rec->file_type ?? '') === 'pdf');

                                        $awardedDate = ($rec->status == 2 && $rec->checked_date) ? \Carbon\Carbon::parse($rec->checked_date)->format('d M, Y') : null;
                                        $rejectedDate = ($rec->status == 3 && $rec->checked_date) ? \Carbon\Carbon::parse($rec->checked_date)->format('d M, Y') : null;
                                    @endphp
                                    <tr>
                                        {{-- # --}}
                                        <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>

                                        {{-- Nomination (Date & Time) --}}
                                        <td>
                                            <div class="fw-bold text-dark">{{ $formattedDate }}</div>
                                            <div class="small text-muted"><i class="ti ti-clock me-1"></i>{{ $formattedTime }}</div>
                                        </td>

                                        {{-- Nominee (Name & Designation) --}}
                                        <td>
                                            <div class="fw-bold text-dark">{{ $nomineeName }}</div>
                                            <div class="small text-muted">
                                                <span class="badge bg-light text-secondary border font-11">{{ $designationName }}</span>
                                                @if($rec->staff && $rec->staff->usmid)
                                                    <span class="ms-1 font-11 text-muted">({{ $rec->staff->usmid }})</span>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Station --}}
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $stationName }}</div>
                                            @if($rec->station && $rec->station->code)
                                                <div class="small text-muted font-11">{{ $rec->station->code }}</div>
                                            @endif
                                        </td>

                                        {{-- Area of Proficiency --}}
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-semibold font-12">
                                                <i class="ti ti-star me-1"></i>{{ $rec->proficiency->name ?? '-' }}
                                            </span>
                                        </td>

                                        {{-- Description --}}
                                        <td>
                                            <div class="small text-dark text-truncate" style="max-width: 230px;" title="{{ $rec->description }}">
                                                {{ $rec->description ?: '-' }}
                                            </div>
                                        </td>

                                        {{-- Uploaded PDF / JPG --}}
                                        <td class="text-center">
                                            @if($rec->file)
                                                @if($isPdf)
                                                    <a href="{{ asset('storage/' . $rec->file) }}" target="_blank" class="btn btn-sm btn-outline-danger fw-semibold d-inline-flex align-items-center gap-1 px-2 py-1" style="font-size: 11px; border-radius: 6px;">
                                                        <i class="ti ti-file-text fs-6"></i> {{ __('PDF') }}
                                                    </a>
                                                @else
                                                    <a href="{{ asset('storage/' . $rec->file) }}" target="_blank" class="btn btn-sm btn-outline-primary fw-semibold d-inline-flex align-items-center gap-1 px-2 py-1" style="font-size: 11px; border-radius: 6px;">
                                                        <i class="ti ti-photo fs-6"></i> {{ __('JPG') }}
                                                    </a>
                                                @endif
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>

                                        {{-- Decision (Awarded Date / Rejected Date) --}}
                                        <td class="text-center">
                                            @if($rec->status == 2)
                                                <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-bold font-12 d-inline-flex align-items-center gap-1">
                                                    <i class="ti ti-check"></i> {{ __('AWARDED') }}
                                                </span>
                                                <div class="font-11 text-muted mt-1">{{ $awardedDate }}</div>
                                                @if($rec->dom_comment)
                                                    <div class="font-11 text-success fw-semibold mt-1 text-truncate" style="max-width: 130px;" title="{{ $rec->dom_comment }}">
                                                        {{ $rec->dom_comment }}
                                                    </div>
                                                @endif
                                            @elseif($rec->status == 3)
                                                <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fw-bold font-12 d-inline-flex align-items-center gap-1">
                                                    <i class="ti ti-x"></i> {{ __('REJECTED') }}
                                                </span>
                                                <div class="font-11 text-muted mt-1">{{ $rejectedDate }}</div>
                                            @elseif($rec->status == 0)
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1 font-11 fw-semibold">
                                                    {{ __('PENDING TI') }}
                                                </span>
                                            @elseif($rec->status == 1)
                                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 font-11 fw-semibold">
                                                    {{ __('RECOMMENDED') }}
                                                </span>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>

                                        {{-- Certificate PDF --}}
                                        <td class="text-center">
                                            @if($rec->status == 2 && !empty($rec->certificate_no))
                                                <a href="{{ route('report.award-record.certificate', $rec->id) }}" target="_blank" class="btn btn-sm btn-outline-success fw-bold d-inline-flex align-items-center gap-1 px-2 py-1 shadow-sm" style="font-size: 11px; border-radius: 6px;" title="{{ __('View/Print Certificate') }}">
                                                    <i class="ti ti-certificate fs-6"></i>
                                                    <span>{{ $rec->certificate_no }}</span>
                                                </a>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>

                                        {{-- Action (Matching Image: Seen button & PDF button) --}}
                                        <td class="text-center pe-3">
                                            <div class="d-inline-flex gap-2">
                                                {{-- View Modal Button --}}
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary open-award-modal" data-id="{{ $rec->id }}" title="{{ __('View Dossier') }}" style="border-radius: 6px; width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center;">
                                                    <i class="ti ti-eye fs-5"></i>
                                                </button>

                                                {{-- Download Single Dossier PDF --}}
                                                <a href="{{ route('report.award-record.export.nomination-pdf', $rec->id) }}" class="btn btn-sm btn-icon btn-outline-danger" title="{{ __('Download Dossier PDF') }}" target="_blank" style="border-radius: 6px; width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center;">
                                                    <i class="ti ti-file-text fs-5"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-5">
                                            <div class="py-4">
                                                <i class="ti ti-database-off text-muted" style="font-size: 3rem;"></i>
                                                <h6 class="fw-bold text-muted mt-3">{{ __('No Award Records Found') }}</h6>
                                                <p class="text-muted small mb-0">{{ __('Try adjusting your search criteria or filter parameters.') }}</p>
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
    <div class="modal fade" id="awardDetailModal" tabindex="-1" aria-labelledby="awardDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;" id="awardModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted fw-semibold">Loading Award Nomination Dossier...</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
<script>
    $(document).ready(function () {
        // Initialize Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Re-initialize on DataTable redraws
        if ($.fn.DataTable) {
            $('#awardRecordTable').on('draw.dt', function () {
                $('[data-bs-toggle="tooltip"]').tooltip();
            });
        }

        // Delegated View Modal Click
        $(document).on('click', '.open-award-modal', function (e) {
            e.preventDefault();
            var recordId = $(this).data('id');

            $('#awardDetailModal').modal('show');
            $('#awardModalBody').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2 text-muted fw-semibold">Loading Award Nomination Dossier...</p></div>');

            $.ajax({
                url: "{{ url('report/award-record-report') }}/" + recordId + "/show",
                type: "GET",
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function (response) {
                    $('#awardModalBody').html(response);
                },
                error: function (xhr) {
                    $('#awardModalBody').html('<div class="p-4 text-center text-danger"><i class="ti ti-alert-circle fs-1 mb-2 d-block"></i><p class="fw-semibold mb-0">Failed to load award dossier. Please try again.</p></div>');
                }
            });
        });
    });
</script>
@endpush
