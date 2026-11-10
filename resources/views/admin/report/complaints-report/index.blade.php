@extends('layouts.app')

@section('page-title')
    {{ __('Station Complaints Comprehensive Report') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Home') }}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="javascript:void(0);">{{ __('Reports') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">
        {{ __('Station Complaints Records') }}
    </li>
@endsection

@push('css-page')
<style>
    /* Prevent select/input text from being clipped and override custom.css 12px padding */
    .complaints-filter-card .form-select,
    .complaints-filter-card .form-control {
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
    .complaints-filter-card .form-select {
        padding-right: 36px !important;
        background-position: right 12px center !important;
    }
</style>
@endpush

@section('content')
    <div class="row">

        {{-- Summary KPI Metric Cards (Clickable for Quick Filter) --}}
        <div class="col-xl-12 mb-3">
            <div class="row g-3">
                {{-- 1. Total Complaints --}}
                <div class="col-xl-3 col-md-3 col-sm-6 col-6">
                    <a href="{{ route('report.complaints.index') }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm complaints-filter-card mb-0 h-100" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Total Complaints') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ $totalComplaints }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-report fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- 2. Pending / Open --}}
                <div class="col-xl-3 col-md-3 col-sm-6 col-6">
                    <a href="{{ route('report.complaints.index', ['status' => '0']) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm complaints-filter-card mb-0 h-100" style="background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Pending / Open') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ $pendingComplaints }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-clock-hour-4 fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- 3. Rectified --}}
                <div class="col-xl-3 col-md-3 col-sm-6 col-6">
                    <a href="{{ route('report.complaints.index', ['status' => '1']) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm complaints-filter-card mb-0 h-100" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Rectified') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ $rectifiedComplaints }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-tools fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- 4. Closed --}}
                <div class="col-xl-3 col-md-3 col-sm-6 col-6">
                    <a href="{{ route('report.complaints.index', ['status' => '3']) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm complaints-filter-card mb-0 h-100" style="background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Closed') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ $closedComplaints }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-circle-check fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- 5. Safety Critical --}}
                <div class="col-xl-3 col-md-3 col-sm-6 col-6">
                    <a href="{{ route('report.complaints.index', ['is_safety_critical' => '1']) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm complaints-filter-card mb-0 h-100" style="background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Safety Critical') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ $criticalComplaints }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-alert-triangle fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- 6. Reopened --}}
                <div class="col-xl-3 col-md-3 col-sm-6 col-6">
                    <a href="{{ route('report.complaints.index', ['status' => '2']) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm complaints-filter-card mb-0 h-100" style="background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Reopened') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ $reopenedComplaints }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-refresh fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- 7. Assigned --}}
                <div class="col-xl-3 col-md-3 col-sm-6 col-6">
                    <a href="{{ route('report.complaints.index', ['status' => '4']) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm complaints-filter-card mb-0 h-100" style="background: linear-gradient(135deg, #0052d4 0%, #4364f7 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Assigned') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ $assignedComplaints }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-user-check fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- 8. Transferred --}}
                <div class="col-xl-3 col-md-3 col-sm-6 col-6">
                    <a href="{{ route('report.complaints.index', ['status' => '6']) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm complaints-filter-card mb-0 h-100" style="background: linear-gradient(135deg, #614385 0%, #516395 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Transferred') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ $transferredCount }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-arrows-left-right fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        {{-- Filters Section (Spacious 3-Row Balanced Form) --}}
        <div class="col-sm-12 mb-3">
            <div class="card border-0 shadow-sm complaints-filter-card" style="border-radius: 12px;">
                <div class="card-header bg-light py-3 border-0 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="ti ti-filter me-2 text-primary fs-5"></i> {{ __('Filter Station Complaints') }}
                    </h6>
                    @if(request()->anyFilled(['department_id', 'station_id', 'amenity_category_id', 'from_date', 'to_date', 'search']) || (request()->has('status') && request('status') !== 'all') || (request()->has('is_safety_critical') && request('is_safety_critical') !== 'all'))
                        <a href="{{ route('report.complaints.index') }}" class="badge bg-danger text-white text-decoration-none px-2 py-1">
                            <i class="ti ti-x me-1"></i>{{ __('Clear Active Filters') }}
                        </a>
                    @endif
                </div>
                <div class="card-body p-4">
                    <form method="GET" action="{{ route('report.complaints.index') }}">

                        {{-- ROW 1: Department, Station & Complaint Type (3 Equal Columns) --}}
                        <div class="row g-3">
                            {{-- 1. Department --}}
                            <div class="col-md-4 col-sm-6">
                                <label class="form-label small fw-semibold text-muted text-uppercase">{{ __('Department') }}</label>
                                <select name="department_id" class="form-select no-select2" style="height: 42px; font-size: 14px; font-weight: 500; color: #1e293b; background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px;">
                                    <option value="">{{ __('All Departments') }}</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 2. Station --}}
                            <div class="col-md-4 col-sm-6">
                                <label class="form-label small fw-semibold text-muted text-uppercase">{{ __('Station') }}</label>
                                <select name="station_id" class="form-select no-select2" style="height: 42px; font-size: 14px; font-weight: 500; color: #1e293b; background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px;">
                                    <option value="">{{ __('All Stations') }}</option>
                                    @foreach ($stations as $stn)
                                        <option value="{{ $stn->id }}" {{ request('station_id') == $stn->id ? 'selected' : '' }}>{{ $stn->name }} ({{ $stn->code }})</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 3. Complaint Type / Category --}}
                            <div class="col-md-4 col-sm-12">
                                <label class="form-label small fw-semibold text-muted text-uppercase">{{ __('Complaint Type') }}</label>
                                <select name="amenity_category_id" class="form-select no-select2" style="height: 42px; font-size: 14px; font-weight: 500; color: #1e293b; background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px;">
                                    <option value="">{{ __('All Complaint Types (Categories)') }}</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ request('amenity_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- ROW 2: Status, Safety Critical & Keyword Search (3 Equal Columns) --}}
                        <div class="row g-3 mt-1">
                            {{-- 4. Status --}}
                            <div class="col-md-4 col-sm-6">
                                <label class="form-label small fw-semibold text-muted text-uppercase">{{ __('Status') }}</label>
                                <select name="status" class="form-select no-select2" style="height: 42px; font-size: 14px; font-weight: 500; color: #1e293b; background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px;">
                                    <option value="all" {{ request('status') === 'all' || !request()->has('status') ? 'selected' : '' }}>{{ __('All Statuses') }}</option>
                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>{{ __('Pending / Open') }}</option>
                                    <option value="4" {{ request('status') === '4' ? 'selected' : '' }}>{{ __('Assigned') }}</option>
                                    <option value="5" {{ request('status') === '5' ? 'selected' : '' }}>{{ __('Reassigned') }}</option>
                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>{{ __('Rectified') }}</option>
                                    <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>{{ __('Reopened') }}</option>
                                    <option value="3" {{ request('status') === '3' ? 'selected' : '' }}>{{ __('Closed') }}</option>
                                    <option value="6" {{ request('status') === '6' ? 'selected' : '' }}>{{ __('Transferred') }}</option>
                                </select>
                            </div>

                            {{-- 5. Critical / Non-Critical --}}
                            <div class="col-md-4 col-sm-6">
                                <label class="form-label small fw-semibold text-muted text-uppercase">{{ __('Critical / Non-Critical') }}</label>
                                <select name="is_safety_critical" class="form-select no-select2" style="height: 42px; font-size: 14px; font-weight: 500; color: #1e293b; background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px;">
                                    <option value="all" {{ request('is_safety_critical') === 'all' || !request()->has('is_safety_critical') ? 'selected' : '' }}>{{ __('All') }}</option>
                                    <option value="1" {{ request('is_safety_critical') === '1' ? 'selected' : '' }}>{{ __('Safety Critical') }}</option>
                                    <option value="0" {{ request('is_safety_critical') === '0' ? 'selected' : '' }}>{{ __('Non-Critical') }}</option>
                                </select>
                            </div>

                            {{-- 6. Keyword Search --}}
                            <div class="col-md-4 col-sm-12">
                                <label class="form-label small fw-semibold text-muted text-uppercase">{{ __('Keyword Search') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted" style="border: 1px solid #cbd5e1; border-right: none; border-radius: 8px 0 0 8px;"><i class="ti ti-search"></i></span>
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search Complaint No, Issue, Rectifier...') }}" class="form-control border-start-0" style="height: 42px; font-size: 14px; font-weight: 500; color: #1e293b; background-color: #ffffff; border: 1px solid #cbd5e1; border-left: none; border-radius: 0 8px 8px 0;">
                                </div>
                            </div>
                        </div>

                        {{-- ROW 3: Date Range & Action Buttons (3 Equal Columns) --}}
                        <div class="row g-3 mt-1 align-items-end">
                            {{-- 7. Complaint Date From --}}
                            <div class="col-md-4 col-sm-6">
                                <label class="form-label small fw-semibold text-muted text-uppercase">{{ __('Complaint Date From') }}</label>
                                <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control" style="height: 42px; font-size: 14px; font-weight: 500; color: #1e293b; background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px;">
                            </div>

                            {{-- 8. Complaint Date To --}}
                            <div class="col-md-4 col-sm-6">
                                <label class="form-label small fw-semibold text-muted text-uppercase">{{ __('Complaint Date To') }}</label>
                                <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control" style="height: 42px; font-size: 14px; font-weight: 500; color: #1e293b; background-color: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px;">
                            </div>

                            {{-- 9. Action Buttons (Filter & Reset) --}}
                            <div class="col-md-4 col-sm-12 d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-fill d-flex align-items-center justify-content-center fw-semibold shadow-sm" style="height: 42px; border-radius: 8px;" title="{{ __('Apply Filters') }}">
                                    <i class="ti ti-filter me-1 fs-5"></i> {{ __('Filter Complaints') }}
                                </button>
                                <a href="{{ route('report.complaints.index') }}" class="btn btn-outline-secondary px-3 d-flex align-items-center justify-content-center fw-semibold" style="height: 42px; border-radius: 8px;" title="{{ __('Reset All Filters') }}">
                                    <i class="ti ti-refresh me-1"></i> {{ __('Reset') }}
                                </a>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        {{-- Main Table Section --}}
        <div class="col-sm-12">
            <div class="card border-0 shadow-sm complaints-filter-card" style="border-radius: 12px;">
                <div class="card-header bg-white py-3 border-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <i class="ti ti-table me-2 text-primary fs-4"></i> {{ __('Station Complaints Records') }}
                        </h5>
                        <small class="text-muted">{{ __('Displaying :count complaints matching current filters', ['count' => $complaints->count()]) }}</small>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        {{-- Excel Export --}}
                        <a href="{{ route('report.complaints.export.excel', request()->all()) }}" class="btn btn-sm btn-outline-success d-flex align-items-center shadow-sm" style="border-radius: 8px; font-weight: 600;">
                            <i class="ti ti-file-spreadsheet me-1 fs-5"></i> {{ __('Download Excel') }}
                        </a>

                        {{-- PDF Export --}}
                        <a href="{{ route('report.complaints.export.pdf', request()->all()) }}" class="btn btn-sm btn-outline-danger d-flex align-items-center shadow-sm" style="border-radius: 8px; font-weight: 600;">
                            <i class="ti ti-file-type-pdf me-1 fs-5"></i> {{ __('Download PDF Report') }}
                        </a>

                        {{-- Print Table --}}
                        <button type="button" onclick="window.print();" class="btn btn-sm btn-outline-secondary d-flex align-items-center shadow-sm" style="border-radius: 8px; font-weight: 600;">
                            <i class="ti ti-printer me-1 fs-5"></i> {{ __('Print') }}
                        </button>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="dt-responsive table-responsive">
                        <table class="table table-hover align-middle mb-0 advance-datatable" id="complaintsReportTable">
                            <thead class="bg-light text-uppercase small text-muted" style="letter-spacing: 0.5px;">
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>{{ __('Complaint No') }}</th>
                                    {{-- Module 1: Register Complaint --}}
                                    <th>{{ __('Station & Dept') }}</th>
                                    <th>{{ __('Type & Issue') }}</th>
                                    <th>{{ __('Registered At') }}</th>
                                    <th>{{ __('Critical') }}</th>
                                    <th>{{ __('Timeline') }}</th>
                                    {{-- Module 2: Assign & Rectify --}}
                                    <th>{{ __('Assigned To') }}</th>
                                    <th>{{ __('Rectified By / Remark') }}</th>
                                    {{-- Status & Module 3 --}}
                                    <th>{{ __('Status') }}</th>
                                    <th class="text-end" style="width: 110px;">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($complaints as $idx => $item)
                                    <tr>
                                        <td class="text-muted small">{{ $idx + 1 }}</td>
                                        <td>
                                            <a href="javascript:void(0);" class="fw-bold text-primary open-complaint-modal" data-id="{{ $item->id }}" title="{{ __('View Details') }}">
                                                {{ $item->complaint_num }}
                                            </a>
                                            @if($item->reported_before)
                                                <div><span class="badge bg-light-warning text-dark border font-10">{{ __('Repeated') }}</span></div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $item->station->name ?? '-' }} ({{ $item->station->code ?? '-' }})</div>
                                            <div class="small text-muted">{{ $item->department->name ?? '-' }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $item->amenityCategory->name ?? '-' }}</div>
                                            <div class="small text-muted" style="max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $item->description }}">
                                                {{ $item->description ?? '-' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}</div>
                                            <div class="small text-muted">{{ $item->created_at ? $item->created_at->format('h:i A') : '' }}</div>
                                        </td>
                                        <td>
                                            @if ($item->is_safety_critical)
                                                <span class="badge bg-danger shadow-sm">{{ __('Critical') }}</span>
                                            @else
                                                <span class="badge bg-light-secondary text-secondary">{{ __('Normal') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->timeline)
                                                <span class="badge bg-light-info text-info border">{{ $item->timeline }} {{ __('Hrs') }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->assignedEmployee)
                                                <div class="fw-semibold text-dark">{{ $item->assignedEmployee->name }}</div>
                                                <div class="small text-muted">{{ $item->assignedEmployee->designation->name ?? '' }}</div>
                                                @if($item->assigned_employee_at)
                                                    <div class="small text-muted font-11"><i class="ti ti-clock"></i> {{ \Carbon\Carbon::parse($item->assigned_employee_at)->format('d M, h:i A') }}</div>
                                                @endif
                                            @elseif($item->forwardedToDesignation)
                                                <div class="small text-info fw-semibold">{{ __('Forwarded:') }} {{ $item->forwardedToDesignation->name }}</div>
                                            @else
                                                <span class="badge bg-light-warning text-dark">{{ __('Not Assigned') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->rectifier_name || $item->rectification_remark)
                                                <div class="fw-semibold text-success">{{ $item->rectifier_name ?? '-' }}</div>
                                                @if($item->rectifier_designation)
                                                    <div class="small text-muted">{{ $item->rectifier_designation }}</div>
                                                @endif
                                                <div class="small text-muted" style="max-width: 180px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $item->rectification_remark }}">
                                                    {{ $item->rectification_remark ?? '-' }}
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->status == 0)
                                                <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                            @elseif ($item->status == 1)
                                                <span class="badge bg-success">{{ __('Rectified') }}</span>
                                            @elseif ($item->status == 2)
                                                <span class="badge bg-info text-white">{{ __('Reopened') }}</span>
                                                @if($item->reopen_reason)
                                                    <div class="small text-danger font-11" title="{{ $item->reopen_reason }}">Reason: {{ Str::limit($item->reopen_reason, 20) }}</div>
                                                @endif
                                            @elseif ($item->status == 3)
                                                <span class="badge bg-secondary">{{ __('Closed') }}</span>
                                            @elseif ($item->status == 4)
                                                <span class="badge bg-primary">{{ __('Assigned') }}</span>
                                            @elseif ($item->status == 5)
                                                <span class="badge bg-dark">{{ __('Reassigned') }}</span>
                                            @elseif ($item->status == 6)
                                                <span class="badge bg-warning text-dark"><i class="ti ti-arrows-left-right"></i> {{ __('Transferred') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-1">
                                                {{-- View Modal Button --}}
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-primary open-complaint-modal" data-id="{{ $item->id }}" title="{{ __('View 3-Module Record') }}" style="border-radius: 6px;">
                                                    <i class="ti ti-eye"></i>
                                                </button>

                                                {{-- Download Single Dossier PDF --}}
                                                <a href="{{ route('report.complaints.export.complaint-pdf', $item->id) }}" class="btn btn-sm btn-icon btn-outline-danger" title="{{ __('Download Complaint PDF') }}" style="border-radius: 6px;">
                                                    <i class="ti ti-file-text"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center py-5 text-muted">
                                            <i class="ti ti-inbox fs-1 text-muted d-block mb-2"></i>
                                            <h6 class="text-muted fw-semibold">{{ __('No complaints found matching the criteria.') }}</h6>
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
    <div class="modal fade" id="complaintDetailModal" tabindex="-1" aria-labelledby="complaintDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;" id="complaintModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
<script>
$(document).ready(function() {
    $(document).on('click', '.open-complaint-modal', function() {
        var complaintId = $(this).data('id');
        $('#complaintDetailModal').modal('show');
        $('#complaintModalBody').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2 text-muted">Loading Complaint Details...</p></div>');

        $.ajax({
            url: "{{ url('report/complaints-report') }}/" + complaintId + "/show",
            type: "GET",
            success: function(response) {
                $('#complaintModalBody').html(response);
            },
            error: function(xhr) {
                $('#complaintModalBody').html('<div class="p-4 text-center text-danger"><i class="ti ti-alert-triangle fs-1 mb-2"></i><h5>Error Loading Details</h5><p>' + (xhr.responseJSON?.message || 'Unable to fetch complaint details.') + '</p></div>');
            }
        });
    });
});
</script>
@endpush
