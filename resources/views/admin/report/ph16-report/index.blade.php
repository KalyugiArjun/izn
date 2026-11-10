@extends('layouts.app')

@section('page-title')
    {{ __('PH 16 Works Comprehensive Report') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Home') }}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="javascript:void(0);">{{ __('Reports') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">
        {{ __('PH 16 Works Report') }}
    </li>
@endsection

@section('content')
    <div class="row">

        {{-- Summary KPI Cards --}}
        <div class="col-xl-12 mb-3">
            <div class="row g-3">
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: #fff; border-radius: 12px;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Total Works') }}</div>
                                    <h3 class="fw-bold mb-0 text-white mt-1">{{ $totalWorks }}</h3>
                                </div>
                                <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                    <i class="ti ti-briefcase fs-3 text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-sm-4 col-6">
                    <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%); color: #fff; border-radius: 12px;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-white-50 small text-uppercase fw-semibold">{{ __('In Progress') }}</div>
                                    <h3 class="fw-bold mb-0 text-white mt-1">{{ $activeWorks }}</h3>
                                </div>
                                <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                    <i class="ti ti-clock-hour-4 fs-3 text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 col-sm-4 col-6">
                    <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: #fff; border-radius: 12px;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Completed') }}</div>
                                    <h3 class="fw-bold mb-0 text-white mt-1">{{ $completedWorks }}</h3>
                                </div>
                                <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                    <i class="ti ti-circle-check fs-3 text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 col-6">
                    <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%); color: #fff; border-radius: 12px;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Inspection Logs') }}</div>
                                    <h3 class="fw-bold mb-0 text-white mt-1">{{ $totalInspections }}</h3>
                                </div>
                                <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                    <i class="ti ti-clipboard-check fs-3 text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-sm-6 col-12">
                    <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #8e2de2 0%, #4a00e0 100%); color: #fff; border-radius: 12px;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Total Variations') }}</div>
                                    <h3 class="fw-bold mb-0 text-white mt-1">{{ $totalVariations }}</h3>
                                </div>
                                <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                    <i class="ti ti-git-fork fs-3 text-white"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters Section --}}
        <div class="col-sm-12 mb-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-light py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="ti ti-filter me-2 text-primary fs-5"></i> {{ __('Filter PH 16 Works') }}
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('report.ph16.index') }}">
                        {{-- Row 1: Selection Dropdowns --}}
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-muted">{{ __('Concerned Inspector (TI)') }}</label>
                                <select name="ph16_ti_id" class="form-select no-select2" style="height: 42px; font-size: 14px; font-weight: 500; color: #1e293b; background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px;">
                                    <option value="">{{ __('All Inspectors') }}</option>
                                    @foreach ($ph16Tis as $ti)
                                        <option value="{{ $ti->id }}" {{ request('ph16_ti_id') == $ti->id ? 'selected' : '' }}>{{ ($ti->designation->name ?? ('TI #' . $ti->id)) . ($ti->department ? ' (' . $ti->department->name . ')' : '') }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-muted">{{ __('Department') }}</label>
                                <select name="department_id" class="form-select no-select2" style="height: 42px; font-size: 14px; font-weight: 500; color: #1e293b; background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px;">
                                    <option value="">{{ __('All Departments') }}</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-muted">{{ __('Status') }}</label>
                                <select name="status" class="form-select no-select2" style="height: 42px; font-size: 14px; font-weight: 500; color: #1e293b; background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px;">
                                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>{{ __('All Status') }}</option>
                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('In Progress / Active') }}</option>
                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                                </select>
                            </div>
                        </div>

                        {{-- Row 2: Date Filters & Action Buttons --}}
                        <div class="row g-3 mt-1 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-muted">{{ __('Identified From') }}</label>
                                <input type="date" name="from_date" class="form-control" style="height: 42px; font-size: 14px; border: 1px solid #cbd5e1; border-radius: 8px;" value="{{ request('from_date') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold text-muted">{{ __('Identified To') }}</label>
                                <input type="date" name="to_date" class="form-control" style="height: 42px; font-size: 14px; border: 1px solid #cbd5e1; border-radius: 8px;" value="{{ request('to_date') }}">
                            </div>

                            <div class="col-md-4">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-grow-1 d-flex align-items-center justify-content-center gap-2" style="height: 42px; border-radius: 8px;" title="{{ __('Apply Filters') }}">
                                        <i class="ti ti-search fs-5"></i>
                                        <span>{{ __('Apply Filters') }}</span>
                                    </button>
                                    <a href="{{ route('report.ph16.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-1 px-3" style="height: 42px; border-radius: 8px;" title="{{ __('Reset Filters') }}">
                                        <i class="ti ti-refresh fs-5"></i>
                                        <span>{{ __('Reset') }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Master Report Table --}}
        <div class="col-sm-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white py-3 d-flex flex-wrap align-items-center justify-content-between border-bottom">
                    <div>
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <i class="ti ti-table me-2 text-primary fs-4"></i> {{ __('PH 16 Works Comprehensive Register') }}
                        </h5>
                        <small class="text-muted">{{ __('Displaying :count work records matching current filters', ['count' => $works->count()]) }}</small>
                    </div>

                    <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
                        {{-- Dedicated Excel Download --}}
                        <a href="{{ route('report.ph16.export.excel', request()->query()) }}" class="btn btn-outline-success btn-sm d-flex align-items-center gap-1 shadow-sm">
                            <i class="ti ti-file-spreadsheet fs-5"></i>
                            <span>{{ __('Download Excel') }}</span>
                        </a>

                        {{-- Dedicated Landscape PDF Download --}}
                        <a href="{{ route('report.ph16.export.pdf', request()->query()) }}" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1 shadow-sm" target="_blank">
                            <i class="ti ti-file-text fs-5"></i>
                            <span>{{ __('Download PDF Report') }}</span>
                        </a>

                        {{-- Print Button --}}
                        <button type="button" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1 shadow-sm" onclick="window.print()">
                            <i class="ti ti-printer fs-5"></i>
                            <span>{{ __('Print') }}</span>
                        </button>
                    </div>
                </div>

                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle advance-datatable" id="ph16ReportTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="min-width: 90px;"># ID</th>
                                    <th style="min-width: 250px;">{{ __('Work Description') }}</th>
                                    <th style="min-width: 110px;">{{ __('Identified Date') }}</th>
                                    <th style="min-width: 170px;">{{ __('Concerned Inspector') }}</th>
                                    <th style="min-width: 160px;">{{ __('Current Stage') }}</th>
                                    <th style="min-width: 120px;">{{ __('Progress') }}</th>
                                    <th style="min-width: 200px;">{{ __('Timeline Milestones') }}</th>
                                    <th style="min-width: 90px;" class="text-center">{{ __('Inspections') }}</th>
                                    <th style="min-width: 90px;" class="text-center">{{ __('Variations') }}</th>
                                    <th style="min-width: 90px;" class="text-center">{{ __('Status') }}</th>
                                    <th style="min-width: 120px;" class="text-end">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($works as $index => $work)
                                    @php
                                        $ti = $work->ph16Ti;
                                        $tiName = $ti && $ti->designation ? $ti->designation->name : ($work->concern_traffic_inspector ?? 'N/A');
                                        $deptName = $ti && $ti->department ? $ti->department->name : '-';
                                        $progress = $work->progress_percent;
                                        $stage = $work->current_stage;
                                        $isCompleted = ($work->executionends && $work->executionends->count() > 0) || $work->status === 1;
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-2 py-1">
                                                #{{ str_pad($work->id, 3, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $work->work_description_identification }}</div>
                                            <small class="text-muted">Work ID: #2026-{{ str_pad($work->id, 2, '0', STR_PAD_LEFT) }}</small>
                                        </td>
                                        <td>
                                            {{ $work->date_of_identification_of_work ? $work->date_of_identification_of_work->format('d M, Y') : '-' }}
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $tiName }}</div>
                                            <small class="text-muted"><i class="ti ti-building me-1"></i>{{ $deptName }}</small>
                                        </td>
                                        <td>
                                            <span class="badge {{ $isCompleted ? 'bg-success' : 'bg-info' }} px-2 py-1">
                                                {{ $stage }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 6px;">
                                                    <div class="progress-bar {{ $progress == 100 ? 'bg-success' : ($progress > 50 ? 'bg-primary' : 'bg-warning') }}" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <span class="small fw-semibold">{{ $progress }}%</span>
                                            </div>
                                        </td>
                                        <td>
                                            {{-- Milestone Badges mini pipeline --}}
                                            <div class="d-flex flex-wrap gap-1">
                                                <span class="badge {{ $work->jointSurveys->count() > 0 ? 'bg-success' : 'bg-light text-muted border' }}" title="{{ __('Joint Survey: ') . ($work->jointSurveys->count() > 0 ? 'Done' : 'Pending') }}">JS</span>
                                                <span class="badge {{ $work->estimates->count() > 0 ? 'bg-success' : 'bg-light text-muted border' }}" title="{{ __('Estimates: ') . $work->estimates->count() . ' recorded' }}">EST</span>
                                                <span class="badge {{ $work->reviewEstimates->count() > 0 ? 'bg-success' : 'bg-light text-muted border' }}" title="{{ __('Review Estimate: ') . ($work->reviewEstimates->count() > 0 ? 'Done' : 'Pending') }}">REV</span>
                                                <span class="badge {{ $work->approvalListings->count() > 0 ? 'bg-success' : 'bg-light text-muted border' }}" title="{{ __('Approval: ') . ($work->approvalListings->count() > 0 ? 'Done' : 'Pending') }}">APP</span>
                                                <span class="badge {{ $work->financeVettingSanctions->count() > 0 ? 'bg-success' : 'bg-light text-muted border' }}" title="{{ __('Finance Vetting: ') . ($work->financeVettingSanctions->count() > 0 ? 'Done' : 'Pending') }}">FV</span>
                                                <span class="badge {{ $work->tenderings->count() > 0 ? 'bg-success' : 'bg-light text-muted border' }}" title="{{ __('Tendering: ') . ($work->tenderings->count() > 0 ? 'Done' : 'Pending') }}">TND</span>
                                                <span class="badge {{ ($work->esps->count() > 0 || $work->sips->count() > 0) ? 'bg-success' : 'bg-light text-muted border' }}" title="{{ __('ESP/SIP: ') . ($work->esps->count() > 0 || $work->sips->count() > 0 ? 'Done' : 'Pending') }}">ESP</span>
                                                <span class="badge {{ $work->executions->count() > 0 ? 'bg-success' : 'bg-light text-muted border' }}" title="{{ __('Execution Start: ') . ($work->executions->count() > 0 ? 'Started' : 'Pending') }}">EXS</span>
                                                <span class="badge {{ $work->executionends->count() > 0 ? 'bg-success' : 'bg-light text-muted border' }}" title="{{ __('Execution End: ') . ($work->executionends->count() > 0 ? 'Completed' : 'Pending') }}">EXE</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if ($work->inspectionLogs->count() > 0)
                                                <span class="badge bg-primary rounded-pill px-2" title="{{ __('Total Inspection Logs') }}">
                                                    {{ $work->inspectionLogs->count() }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($work->variations->count() > 0)
                                                <span class="badge bg-warning text-dark rounded-pill px-2" title="{{ __('Total Variations') }}">
                                                    {{ $work->variations->count() }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($isCompleted)
                                                <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-1">
                                                    {{ __('Completed') }}
                                                </span>
                                            @else
                                                <span class="badge bg-warning bg-opacity-10 text-warning fw-bold px-2 py-1">
                                                    {{ __('In Progress') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-1">
                                                {{-- View Full Work Details Modal --}}
                                                <button type="button" class="btn btn-sm btn-info view-work-details" data-id="{{ $work->id }}" title="{{ __('View Complete A to Z Details') }}">
                                                    <i class="ti ti-eye"></i>
                                                </button>

                                                {{-- Download Single Work PDF Dossier --}}
                                                <a href="{{ route('report.ph16.export.work-pdf', $work->id) }}" class="btn btn-sm btn-outline-danger" title="{{ __('Download Work PDF Dossier') }}" target="_blank">
                                                    <i class="ti ti-download"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-4 text-muted">
                                            <i class="ti ti-folder-off fs-1 d-block mb-2"></i>
                                            {{ __('No PH 16 Works found matching the filter criteria.') }}
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
    <div class="modal fade" id="workDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title text-white fw-bold d-flex align-items-center">
                        <i class="ti ti-file-analytics me-2"></i> {{ __('PH 16 Work Complete A to Z Dossier') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="workDetailContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="mt-2 text-muted">{{ __('Fetching complete work details...') }}</div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
    <script>
        $(document).ready(function() {
            // Destroy Select2 on filter dropdowns so native select options show reliably without glitches
            if (typeof $.fn.select2 !== 'undefined') {
                $('.no-select2').each(function() {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2('destroy');
                    }
                });
            }

            // Click handler to load work details modal
            $(document).on('click', '.view-work-details', function(e) {
                e.preventDefault();
                const workId = $(this).data('id');
                const modal = new bootstrap.Modal(document.getElementById('workDetailModal'));
                modal.show();

                $('#workDetailContent').html(`
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="mt-2 text-muted">{{ __('Loading A to Z details for work...') }}</div>
                    </div>
                `);

                const url = "{{ route('report.ph16.show', ':id') }}".replace(':id', workId);

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(html) {
                        $('#workDetailContent').html(html);
                    },
                    error: function() {
                        $('#workDetailContent').html(`
                            <div class="alert alert-danger mb-0">
                                <i class="ti ti-alert-triangle me-1"></i> {{ __('Unable to load details. Please try again.') }}
                            </div>
                        `);
                    }
                });
            });
        });
    </script>
@endpush

