@extends('layouts.app')

@section('page-title')
    {{ __('Ambush Check Records Report') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Home') }}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="javascript:void(0);">{{ __('Reports') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">
        {{ __('Ambush Check Records') }}
    </li>
@endsection

@push('css-page')
<style>
    /* Prevent select/input text from being clipped and override custom.css 12px padding */
    .ambush-filter-card .form-select,
    .ambush-filter-card .form-control {
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
    .ambush-filter-card .form-select {
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
                {{-- 1. Total Ambush Checks --}}
                <div class="col-xl-3 col-md-6 col-sm-6 col-12">
                    <a href="{{ route('report.ambush.index') }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Total Ambush Checks') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ $totalChecks }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-eye fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- 2. Shunting Checks --}}
                <div class="col-xl-3 col-md-6 col-sm-6 col-12">
                    <a href="{{ route('report.ambush.index', ['type_of_ambush_id' => '1']) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Shunting Checks') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ $shuntingChecks }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-train fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- 3. Load Stable Checks --}}
                <div class="col-xl-3 col-md-6 col-sm-6 col-12">
                    <a href="{{ route('report.ambush.index', ['type_of_ambush_id' => '2']) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Load Stable Checks') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ $loadStableChecks }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-truck fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- 4. Engineering Block --}}
                <div class="col-xl-3 col-md-6 col-sm-6 col-12">
                    <a href="{{ route('report.ambush.index', ['type_of_ambush_id' => '3']) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #ff5e62 0%, #ff9966 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Engineering Block') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ $engBlockChecks }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-barrier-block fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- 5. BA Test Checks --}}
                <div class="col-xl-3 col-md-6 col-sm-6 col-12">
                    <a href="{{ route('report.ambush.index', ['type_of_ambush_id' => '5']) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('BA Test Checks') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ $baTestChecks }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-flask fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- 6. LC Voice Recordings --}}
                <div class="col-xl-3 col-md-6 col-sm-6 col-12">
                    <a href="{{ route('report.ambush.index', ['type_of_ambush_id' => '4']) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #434343 0%, #000000 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('LC Voice Recordings') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ $lcVoiceChecks }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-microphone fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- 7. Major Deficiencies --}}
                <div class="col-xl-3 col-md-6 col-sm-6 col-12">
                    <a href="{{ route('report.ambush.index', ['major_deficiency' => 'YES']) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Major Deficiencies') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ $deficiencyChecks }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-alert-triangle fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- 8. Compliant / Normal --}}
                <div class="col-xl-3 col-md-6 col-sm-6 col-12">
                    <a href="{{ route('report.ambush.index', ['major_deficiency' => 'NO']) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #134e5e 0%, #71b280 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Compliant Checks') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ $normalChecks }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-circle-check fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        {{-- Filters Section (3-Row Balanced Form) --}}
        <div class="col-sm-12 mb-3">
            <div class="card border-0 shadow-sm ambush-filter-card" style="border-radius: 12px;">
                <div class="card-header bg-light py-3 border-0 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="ti ti-eye me-2 text-primary fs-4"></i> {{ __('Filter Ambush Check Records') }}
                    </h6>
                    @if(request()->anyFilled(['station_id', 'from_date', 'to_date', 'search']) || (request()->has('type_of_ambush_id') && request('type_of_ambush_id') !== 'all') || (request()->has('day_night') && request('day_night') !== 'all') || (request()->has('major_deficiency') && request('major_deficiency') !== 'all'))
                        <a href="{{ route('report.ambush.index') }}" class="badge bg-danger text-white text-decoration-none px-2 py-1">
                            <i class="ti ti-x me-1"></i>{{ __('Clear Active Filters') }}
                        </a>
                    @endif
                </div>
                <div class="card-body p-4">
                    <form method="GET" action="{{ route('report.ambush.index') }}">

                        {{-- ROW 1: Station, Type of Ambush & Day/Night (3 Equal Columns) --}}
                        <div class="row g-3">
                            {{-- 1. Station --}}
                            <div class="col-md-4 col-sm-6">
                                <label class="form-label small fw-semibold text-muted text-uppercase mb-1">{{ __('Station') }}</label>
                                <select name="station_id" class="form-select no-select2">
                                    <option value="">{{ __('All Stations') }}</option>
                                    @foreach ($stations as $stn)
                                        <option value="{{ $stn->id }}" {{ request('station_id') == $stn->id ? 'selected' : '' }}>{{ $stn->name }} ({{ $stn->code }})</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 2. Type of Ambush Check --}}
                            <div class="col-md-4 col-sm-6">
                                <label class="form-label small fw-semibold text-muted text-uppercase mb-1">{{ __('Type of Ambush Check') }}</label>
                                <select name="type_of_ambush_id" class="form-select no-select2">
                                    <option value="all" {{ request('type_of_ambush_id') === 'all' || !request()->has('type_of_ambush_id') ? 'selected' : '' }}>{{ __('All Types') }}</option>
                                    @foreach ($ambushTypes as $at)
                                        <option value="{{ $at->id }}" {{ request('type_of_ambush_id') == $at->id ? 'selected' : '' }}>{{ $at->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 3. Day / Night --}}
                            <div class="col-md-4 col-sm-12">
                                <label class="form-label small fw-semibold text-muted text-uppercase mb-1">{{ __('Day / Night') }}</label>
                                <select name="day_night" class="form-select no-select2">
                                    <option value="all" {{ request('day_night') === 'all' || !request()->has('day_night') ? 'selected' : '' }}>{{ __('All Shifts') }}</option>
                                    <option value="DAY" {{ request('day_night') === 'DAY' ? 'selected' : '' }}>{{ __('DAY') }}</option>
                                    <option value="NIGHT" {{ request('day_night') === 'NIGHT' ? 'selected' : '' }}>{{ __('NIGHT') }}</option>
                                </select>
                            </div>
                        </div>

                        {{-- ROW 2: Major Deficiency & Keyword Search --}}
                        <div class="row g-3 mt-1">
                            {{-- 4. Any Major Deficiency --}}
                            <div class="col-md-4 col-sm-6">
                                <label class="form-label small fw-semibold text-muted text-uppercase mb-1">{{ __('Major Deficiency') }}</label>
                                <select name="major_deficiency" class="form-select no-select2">
                                    <option value="all" {{ request('major_deficiency') === 'all' || !request()->has('major_deficiency') ? 'selected' : '' }}>{{ __('All') }}</option>
                                    <option value="YES" {{ request('major_deficiency') === 'YES' ? 'selected' : '' }}>{{ __('YES (Deficiency Found)') }}</option>
                                    <option value="NO" {{ request('major_deficiency') === 'NO' ? 'selected' : '' }}>{{ __('NO (Compliant)') }}</option>
                                </select>
                            </div>

                            {{-- 5. Keyword Search (Unified Clean Bar with Inner Icon) --}}
                            <div class="col-md-8 col-sm-6">
                                <label class="form-label small fw-semibold text-muted text-uppercase mb-1">{{ __('Keyword Search') }}</label>
                                <div class="position-relative">
                                    <i class="ti ti-search position-absolute text-muted" style="left: 14px; top: 13px; font-size: 18px; pointer-events: none;"></i>
                                    <input type="text" name="search" value="{{ request('search') }}" 
                                           placeholder="{{ __('Search Officer, Designation, Shunting Obs, T/806 No, Load Type, Line No...') }}" 
                                           class="form-control" 
                                           style="padding-left: 42px !important;">
                                </div>
                            </div>
                        </div>

                        {{-- ROW 3: Date Range & Action Buttons (3 Equal Columns) --}}
                        <div class="row g-3 mt-1 align-items-end">
                            {{-- 6. Date From --}}
                            <div class="col-md-4 col-sm-6">
                                <label class="form-label small fw-semibold text-muted text-uppercase mb-1">{{ __('Ambush Date From') }}</label>
                                <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
                            </div>

                            {{-- 7. Date To --}}
                            <div class="col-md-4 col-sm-6">
                                <label class="form-label small fw-semibold text-muted text-uppercase mb-1">{{ __('Ambush Date To') }}</label>
                                <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
                            </div>

                            {{-- 8. Action Buttons --}}
                            <div class="col-md-4 col-sm-12 d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-fill d-flex align-items-center justify-content-center fw-semibold shadow-sm" style="height: 44px; border-radius: 8px;" title="{{ __('Apply Filters') }}">
                                    <i class="ti ti-filter me-1 fs-5"></i> {{ __('Filter Ambush Records') }}
                                </button>
                                <a href="{{ route('report.ambush.index') }}" class="btn btn-outline-secondary px-3 d-flex align-items-center justify-content-center fw-semibold" style="height: 44px; border-radius: 8px;" title="{{ __('Reset All Filters') }}">
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
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white py-3 border-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div>
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <i class="ti ti-eye me-2 text-primary fs-4"></i> {{ __('Ambush Check Records (Module 2)') }}
                        </h5>
                        <small class="text-muted">{{ __('Displaying :count ambush inspection records matching current filters', ['count' => $records->count()]) }}</small>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        {{-- Excel Export --}}
                        <a href="{{ route('report.ambush.export.excel', request()->all()) }}" class="btn btn-sm btn-outline-success d-flex align-items-center shadow-sm" style="border-radius: 8px; font-weight: 600;">
                            <i class="ti ti-file-spreadsheet me-1 fs-5"></i> {{ __('Download Excel') }}
                        </a>

                        {{-- PDF Export --}}
                        <a href="{{ route('report.ambush.export.pdf', request()->all()) }}" class="btn btn-sm btn-outline-danger d-flex align-items-center shadow-sm" style="border-radius: 8px; font-weight: 600;">
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
                        <table class="table table-hover align-middle mb-0 advance-datatable" id="ambushReportTable">
                            <thead class="bg-light text-uppercase small text-muted" style="letter-spacing: 0.5px;">
                                <tr>
                                    <th style="width: 40px;">#</th>
                                    <th>{{ __('Ambush Check (Date/Time)') }}</th>
                                    <th>{{ __('Inspected By') }}</th>
                                    <th>{{ __('Station / Location') }}</th>
                                    <th>{{ __('From - To Time') }}</th>
                                    <th>{{ __('Shift') }}</th>
                                    <th>{{ __('Ambush Type') }}</th>
                                    <th>{{ __('Observation / Specific Details') }}</th>
                                    <th>{{ __('Deficiency') }}</th>
                                    <th class="text-end" style="width: 100px;">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($records as $idx => $item)
                                    @php
                                        $inspDate = $item->inspection && $item->inspection->inspection_date 
                                            ? \Carbon\Carbon::parse($item->inspection->inspection_date)->format('d M, Y') 
                                            : ($item->created_at ? $item->created_at->format('d M, Y') : '-');

                                        $inspTime = $item->from_time ?? ($item->created_at ? $item->created_at->format('h:i A') : '-');

                                        $officerName = $item->inspection->officer_name 
                                            ?? ($item->inspection->employee->name ?? '-');

                                        $officerDesig = $item->inspection->designation 
                                            ?? ($item->inspection->employee->designation->name ?? '-');

                                        $isShunting = stripos($item->ambushType->name ?? '', 'SHUNTING') !== false || $item->type_of_ambush_id == 1;
                                        $isLoadStable = stripos($item->ambushType->name ?? '', 'LOAD STABLE') !== false || $item->type_of_ambush_id == 2;
                                        $isEngBlock = stripos($item->ambushType->name ?? '', 'ENGINEERING') !== false || $item->type_of_ambush_id == 3;
                                        $isLcVoice = stripos($item->ambushType->name ?? '', 'LC VOICE') !== false || stripos($item->ambushType->name ?? '', 'NON-INTERLOCKED') !== false || $item->type_of_ambush_id == 4;
                                        $isBaTest = stripos($item->ambushType->name ?? '', 'BA TEST') !== false || $item->type_of_ambush_id == 5;
                                    @endphp
                                    <tr>
                                        <td class="text-muted small">{{ $idx + 1 }}</td>
                                        <td>
                                            <a href="javascript:void(0);" class="fw-bold text-primary open-ambush-modal" data-id="{{ $item->id }}" title="{{ __('View Details') }}">
                                                {{ $inspDate }}
                                            </a>
                                            <div class="small text-muted"><i class="ti ti-clock"></i> {{ $inspTime }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $officerName }}</div>
                                            <div class="small text-muted">{{ $officerDesig }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $item->station->name ?? ($item->block_section ?? '-') }}</div>
                                            @if($item->station && $item->station->code)
                                                <div class="small text-muted">Code: <span class="badge bg-light text-dark border">{{ $item->station->code }}</span></div>
                                            @elseif($item->block_section)
                                                <div class="small text-muted">Sec: <span class="badge bg-light text-dark border">{{ $item->block_section }}</span></div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border font-11">
                                                {{ $item->from_time ?? '-' }} - {{ $item->to_time ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if (strtoupper($item->day_night) === 'NIGHT')
                                                <span class="badge bg-dark text-white"><i class="ti ti-moon me-1"></i>NIGHT</span>
                                            @else
                                                <span class="badge bg-warning text-dark"><i class="ti ti-sun me-1"></i>DAY</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($isShunting)
                                                <span class="badge bg-primary"><i class="ti ti-train me-1"></i>SHUNTING</span>
                                            @elseif($isLoadStable)
                                                <span class="badge bg-warning text-dark"><i class="ti ti-truck me-1"></i>LOAD STABLE</span>
                                            @elseif($isEngBlock)
                                                <span class="badge bg-danger text-white"><i class="ti ti-barrier-block me-1"></i>ENGG BLOCK</span>
                                            @elseif($isBaTest)
                                                <span class="badge bg-info text-white"><i class="ti ti-flask me-1"></i>BA TEST</span>
                                            @elseif($isLcVoice)
                                                <span class="badge bg-dark text-white"><i class="ti ti-microphone me-1"></i>LC VOICE</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $item->ambushType->name ?? '-' }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($isShunting)
                                                <div class="small text-dark"><strong>Obs:</strong> {{ Str::limit($item->shunting_observation ?? '-', 30) }}</div>
                                                <div class="small text-muted"><strong>T/806:</strong> {{ $item->t_806_no ?? '-' }}</div>
                                            @elseif($isLoadStable)
                                                <div class="small text-dark"><strong>Load:</strong> {{ $item->load_type ?? '-' }} ({{ $item->total_number_of_load ?? '0' }})</div>
                                                <div class="small text-muted"><strong>Line:</strong> {{ $item->stabling_line_no ?? '-' }} ({{ $item->running_line ?? '-' }})</div>
                                            @elseif($isEngBlock)
                                                <div class="small text-dark"><strong>Taken By:</strong> {{ $item->block_taken_by ?? '-' }}</div>
                                                <div class="small text-muted"><strong>Section:</strong> {{ $item->block_section ?? '-' }}</div>
                                            @elseif($isBaTest)
                                                <div class="small text-dark"><strong>Staff:</strong> {{ $item->staff_name ?? '-' }} <span class="text-muted">({{ $item->ba_staff_designation_name ?? '-' }})</span></div>
                                                <div class="small">
                                                    <strong>BA:</strong> 
                                                    @if(strtoupper($item->alcohol_test) === 'POSITIVE')
                                                        <span class="badge bg-danger py-0 px-1 font-10">POSITIVE ({{ $item->alcohol_percentage ?? 0 }}%)</span>
                                                    @else
                                                        <span class="badge bg-success py-0 px-1 font-10">NEGATIVE</span>
                                                    @endif
                                                </div>
                                            @elseif($isLcVoice)
                                                <div class="small text-dark"><strong>Gate #{{ $item->lc_gate_no ?? '-' }}</strong> | Train #{{ $item->train_no ?? '-' }}</div>
                                                <div class="small text-muted"><strong>Sec:</strong> {{ $item->block_section ?? '-' }}</div>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if (strtoupper($item->major_deficiency) === 'YES')
                                                <span class="badge bg-danger shadow-sm"><i class="ti ti-alert-triangle me-1"></i>YES</span>
                                                @if($item->write_deficiency)
                                                    <div class="small text-danger font-10" title="{{ $item->write_deficiency }}">{{ Str::limit($item->write_deficiency, 20) }}</div>
                                                @endif
                                            @else
                                                <span class="badge bg-success"><i class="ti ti-check me-1"></i>NO</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-1">
                                                {{-- View Modal Button --}}
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-primary open-ambush-modal" data-id="{{ $item->id }}" title="{{ __('View Details') }}" style="border-radius: 6px;">
                                                    <i class="ti ti-eye"></i>
                                                </button>

                                                {{-- Download Single Dossier PDF --}}
                                                <a href="{{ route('report.ambush.export.ambush-pdf', $item->id) }}" class="btn btn-sm btn-icon btn-outline-danger" title="{{ __('Download Ambush PDF') }}" style="border-radius: 6px;">
                                                    <i class="ti ti-file-text"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-5 text-muted">
                                            <i class="ti ti-inbox fs-1 text-muted d-block mb-2"></i>
                                            <h6 class="text-muted fw-semibold">{{ __('No ambush check records found matching the criteria.') }}</h6>
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
    <div class="modal fade" id="ambushDetailModal" tabindex="-1" aria-labelledby="ambushDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;" id="ambushModalBody">
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
    $(document).on('click', '.open-ambush-modal', function() {
        var recordId = $(this).data('id');
        $('#ambushDetailModal').modal('show');
        $('#ambushModalBody').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2 text-muted">Loading Ambush Check Details...</p></div>');

        $.ajax({
            url: "{{ url('report/ambush-check-report') }}/" + recordId + "/show",
            type: "GET",
            success: function(response) {
                $('#ambushModalBody').html(response);
            },
            error: function(xhr) {
                $('#ambushModalBody').html('<div class="p-4 text-center text-danger"><i class="ti ti-alert-triangle fs-1 mb-2"></i><h5>Error Loading Details</h5><p>' + (xhr.responseJSON?.message || 'Unable to fetch ambush check details.') + '</p></div>');
            }
        });
    });
});
</script>
@endpush
