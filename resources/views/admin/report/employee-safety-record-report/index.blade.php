@extends('layouts.app')

@section('page-title')
    {{ __('Module 6: Employee Safety Record Report') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Home') }}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="javascript:void(0);">{{ __('Reports') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">
        {{ __('Employee Safety Records') }}
    </li>
@endsection

@push('css-page')
<style>
    /* Styling for Filter Card Inputs */
    .safety-filter-card .form-select,
    .safety-filter-card .form-control {
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
    .safety-filter-card .form-select {
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
                {{-- 1. Total Safety Records --}}
                <div class="col-xl-3 col-md-6 col-sm-6 col-12">
                    <a href="{{ route('report.employee-safety.index', ['record_type' => 'all']) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Total Safety Records') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['total']) }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-shield-check fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- 2. Medical (PME) Records --}}
                <div class="col-xl-3 col-md-6 col-sm-6 col-12">
                    <a href="{{ route('report.employee-safety.index', ['record_type' => 'medical']) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Medical (PME)') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['medical']) }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-stethoscope fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- 3. Refresher Course Records --}}
                <div class="col-xl-3 col-md-6 col-sm-6 col-12">
                    <a href="{{ route('report.employee-safety.index', ['record_type' => 'refresher']) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Refresher Courses') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['refresher']) }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-refresh fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- 4. Gradation Records --}}
                <div class="col-xl-3 col-md-6 col-sm-6 col-12">
                    <a href="{{ route('report.employee-safety.index', ['record_type' => 'gradation']) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Gradation Records') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($stats['gradation']) }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-chart-bar fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        {{-- Filter Section (Matching Parameters from image: Station, Date Range, Type) --}}
        <div class="col-12 mb-3">
            <div class="card border-0 shadow-sm safety-filter-card" style="border-radius: 12px;">
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('report.employee-safety.index') }}" id="safetyFilterForm">
                        <div class="row g-2 align-items-end">

                            {{-- 1. Type of Safety Record --}}
                            <div class="col-xl-3 col-md-4 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">{{ __('Type of Safety Record') }}</label>
                                <select name="record_type" class="form-select no-select2">
                                    <option value="all" {{ request('record_type') === 'all' || !request()->has('record_type') ? 'selected' : '' }}>{{ __('All Types (Medical, Refresher, Gradation)') }}</option>
                                    <option value="medical" {{ request('record_type') === 'medical' ? 'selected' : '' }}>{{ __('Medical (PME)') }}</option>
                                    <option value="refresher" {{ request('record_type') === 'refresher' ? 'selected' : '' }}>{{ __('Refresher Course') }}</option>
                                    <option value="gradation" {{ request('record_type') === 'gradation' ? 'selected' : '' }}>{{ __('Staff Gradation') }}</option>
                                </select>
                            </div>

                            {{-- 2. Station Filter --}}
                            <div class="col-xl-3 col-md-4 col-sm-6">
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

                            {{-- 3. Date Range: From Date --}}
                            <div class="col-xl-2 col-md-2 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">{{ __('From Date') }}</label>
                                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                            </div>

                            {{-- 4. Date Range: To Date --}}
                            <div class="col-xl-2 col-md-2 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">{{ __('To Date') }}</label>
                                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                            </div>

                            {{-- 5. Search Keywords --}}
                            <div class="col-xl-2 col-md-4 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">{{ __('Search') }}</label>
                                <input type="text" name="search" class="form-control" placeholder="{{ __('Staff name, cert no...') }}" value="{{ request('search') }}">
                            </div>

                            {{-- 6. Buttons --}}
                            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                                <button type="submit" class="btn btn-primary fw-semibold d-inline-flex align-items-center px-4" style="height: 40px; border-radius: 8px;">
                                    <i class="ti ti-filter me-1 fs-5"></i>{{ __('Filter Records') }}
                                </button>
                                <a href="{{ route('report.employee-safety.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center" style="height: 40px; width: 40px; border-radius: 8px;" title="{{ __('Reset Filters') }}">
                                    <i class="ti ti-rotate"></i>
                                </a>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Table Section (Clean Standard Styling matching My Movement Records) --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">

                {{-- Card Header --}}
                <div class="card-header bg-white py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-3 border-bottom">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="ti ti-shield-check text-primary me-2"></i>{{ __('MODULE 6: EMPLOYEE SAFETY RECORD') }}
                        </h5>
                        <small class="text-muted">
                            {{ __('Total :count safety records matching your criteria', ['count' => count($records)]) }}
                        </small>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        {{-- Excel Export --}}
                        <a href="{{ route('report.employee-safety.export.excel', request()->all()) }}" class="btn btn-outline-success fw-semibold btn-sm d-flex align-items-center px-3" style="height: 38px; border-radius: 8px;">
                            <i class="ti ti-file-spreadsheet me-1 fs-5"></i>{{ __('Export to Excel') }}
                        </a>

                        {{-- PDF Comprehensive Export --}}
                        <a href="{{ route('report.employee-safety.export.pdf', request()->all()) }}" class="btn btn-outline-danger fw-semibold btn-sm d-flex align-items-center px-3" style="height: 38px; border-radius: 8px;" target="_blank">
                            <i class="ti ti-file-text me-1 fs-5"></i>{{ __('Comprehensive PDF') }}
                        </a>
                    </div>
                </div>

                {{-- Table with Advance DataTable --}}
                <div class="card-body p-3">
                    <div class="dt-responsive table-responsive">
                        <table class="table table-hover align-middle mb-0 advance-datatable" id="employeeSafetyTable">
                            <thead class="table-light text-uppercase small fw-bold" style="background-color: #f1f5f9; color: #475569;">
                                <tr>
                                    <th class="ps-3 py-3" style="width: 45px;">#</th>
                                    <th style="min-width: 110px;">{{ __('Type') }}</th>
                                    <th style="min-width: 170px;">{{ __('Staff Name') }}</th>
                                    <th style="min-width: 140px;">{{ __('Designation') }}</th>
                                    <th style="min-width: 130px;">{{ __('Station') }}</th>
                                    <th style="min-width: 130px;">{{ __('Due Date') }}</th>
                                    <th style="min-width: 160px;">{{ __('Certificate / Marks') }}</th>
                                    <th class="text-center" style="min-width: 120px;">{{ __('Status') }}</th>
                                    <th class="text-center pe-3" style="min-width: 95px;">{{ __('ACTION') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $index => $rec)
                                    @php
                                        $formattedDueDate = $rec->due_date ? \Carbon\Carbon::parse($rec->due_date)->format('d M, Y') : '-';
                                        $formattedExamDate = $rec->exam_date ? \Carbon\Carbon::parse($rec->exam_date)->format('d M, Y') : null;
                                    @endphp
                                    <tr>
                                        {{-- # --}}
                                        <td class="text-center text-muted fw-semibold">{{ $index + 1 }}</td>

                                        {{-- Type of Safety Record --}}
                                        <td>
                                            <span class="badge {{ $rec->type_badge }} px-2 py-1 fw-bold font-12 d-inline-flex align-items-center gap-1">
                                                <i class="ti {{ $rec->type_icon }}"></i>
                                                {{ strtoupper($rec->type) }}
                                            </span>
                                        </td>

                                        {{-- Staff Name & USMID --}}
                                        <td>
                                            <div class="fw-bold text-dark">{{ $rec->staff_name }}</div>
                                            @if($rec->usmid)
                                                <div class="small text-muted font-11">USMID: {{ $rec->usmid }}</div>
                                            @endif
                                        </td>

                                        {{-- Designation --}}
                                        <td>
                                            <span class="badge bg-light text-dark border px-2 py-1 fw-semibold font-11">
                                                {{ $rec->designation }}
                                            </span>
                                        </td>

                                        {{-- Station --}}
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $rec->station }}</div>
                                        </td>

                                        {{-- Due Date --}}
                                        <td>
                                            <div class="fw-bold text-dark">{{ $formattedDueDate }}</div>
                                            @if($formattedExamDate && $formattedExamDate !== $formattedDueDate)
                                                <div class="small text-muted font-11">Exam: {{ $formattedExamDate }}</div>
                                            @endif
                                        </td>

                                        {{-- Certificate No / Marks & Grade --}}
                                        <td>
                                            @if($rec->type === 'Gradation')
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="fw-bold text-dark">{{ $rec->total_marks }} / 100</span>
                                                    <span class="badge bg-primary text-white font-11 px-2 py-1 ms-1">Grade: {{ $rec->grade }}</span>
                                                </div>
                                            @else
                                                <div class="fw-semibold text-dark font-12">
                                                    <i class="ti ti-file-certificate me-1 text-primary"></i>{{ $rec->certificate_no }}
                                                </div>
                                                @if(!empty($rec->institution))
                                                    <div class="small text-muted font-11">{{ $rec->institution }}</div>
                                                @endif
                                            @endif
                                        </td>

                                        {{-- Status --}}
                                        <td class="text-center">
                                            <span class="badge {{ $rec->due_status['class'] }} px-2 py-1 fw-bold font-11 d-inline-flex align-items-center gap-1">
                                                <i class="ti {{ $rec->due_status['icon'] }}"></i>
                                                {{ $rec->due_status['text'] }}
                                            </span>
                                        </td>

                                        {{-- Action (Seen Modal + PDF) --}}
                                        <td class="text-center pe-3">
                                            <div class="d-inline-flex gap-2">
                                                {{-- View Modal Button --}}
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary open-safety-modal" data-type="{{ strtolower($rec->type) }}" data-id="{{ $rec->id }}" title="{{ __('View Details') }}" style="border-radius: 6px; width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center;">
                                                    <i class="ti ti-eye fs-5"></i>
                                                </button>

                                                {{-- Download Single Dossier PDF --}}
                                                <a href="{{ route('report.employee-safety.export.single-pdf', ['type' => strtolower($rec->type), 'id' => $rec->id]) }}" class="btn btn-sm btn-icon btn-outline-danger" title="{{ __('Download PDF') }}" target="_blank" style="border-radius: 6px; width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center;">
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
                                                <h6 class="fw-bold text-muted mt-3">{{ __('No Employee Safety Records Found') }}</h6>
                                                <p class="text-muted small mb-0">{{ __('Try adjusting your filters or date range.') }}</p>
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
    <div class="modal fade" id="safetyDetailModal" tabindex="-1" aria-labelledby="safetyDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;" id="safetyModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted fw-semibold">Loading Employee Safety Details...</p>
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
            $('#employeeSafetyTable').on('draw.dt', function () {
                $('[data-bs-toggle="tooltip"]').tooltip();
            });
        }

        // Delegated View Modal Click
        $(document).on('click', '.open-safety-modal', function (e) {
            e.preventDefault();
            var recordType = $(this).data('type');
            var recordId = $(this).data('id');

            $('#safetyDetailModal').modal('show');
            $('#safetyModalBody').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2 text-muted fw-semibold">Loading Employee Safety Details...</p></div>');

            $.ajax({
                url: "{{ url('report/employee-safety-record-report') }}/" + recordType + "/" + recordId + "/show",
                type: "GET",
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function (response) {
                    $('#safetyModalBody').html(response);
                },
                error: function (xhr) {
                    $('#safetyModalBody').html('<div class="p-4 text-center text-danger"><i class="ti ti-alert-circle fs-1 mb-2 d-block"></i><p class="fw-semibold mb-0">Failed to load details. Please try again.</p></div>');
                }
            });
        });
    });
</script>
@endpush
