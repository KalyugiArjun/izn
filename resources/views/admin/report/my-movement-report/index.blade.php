@extends('layouts.app')

@section('page-title')
    {{ __('My Movement Records Report') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Home') }}</a>
    </li>
    <li class="breadcrumb-item">
        <a href="javascript:void(0);">{{ __('Reports') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">
        {{ __('My Movement Records') }}
    </li>
@endsection

@push('css-page')
<style>
    /* Styling for Filter Card Inputs */
    .movement-filter-card .form-select,
    .movement-filter-card .form-control {
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
    .movement-filter-card .form-select {
        padding-right: 36px !important;
        background-position: right 12px center !important;
    }
    .movement-badge-custom {
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.3px;
        padding: 5px 10px;
        border-radius: 6px;
    }
    .table-details-box {
        max-width: 320px;
        font-size: 13px;
        line-height: 1.4;
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
        background: #00897b !important;
        border-color: #00897b !important;
        color: #ffffff !important;
    }
    /* Hide any oversized SVGs if injected */
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
                {{-- 1. Total Movements --}}
                <div class="col-xl-3 col-md-6 col-sm-6 col-12">
                    <a href="{{ route('report.my-movement.index') }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Total Movement Records') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($totalMovements) }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-walk fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- 2. Regular Inspections --}}
                <div class="col-xl-3 col-md-6 col-sm-6 col-12">
                    @php
                        $regularMovementId = null;
                        foreach($movements as $mId => $mName) {
                            if(str_contains(strtoupper($mName), 'REGULAR')) {
                                $regularMovementId = $mId;
                                break;
                            }
                        }
                    @endphp
                    <a href="{{ route('report.my-movement.index', ['movement_id' => $regularMovementId ?? '']) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Regular Inspections') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($regularInspections) }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-clipboard-check fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- 3. Safety Drives --}}
                <div class="col-xl-3 col-md-6 col-sm-6 col-12">
                    @php
                        $safetyMovementId = null;
                        foreach($movements as $mId => $mName) {
                            if(str_contains(strtoupper($mName), 'SAFETY DRIVE')) {
                                $safetyMovementId = $mId;
                                break;
                            }
                        }
                    @endphp
                    <a href="{{ route('report.my-movement.index', ['movement_id' => $safetyMovementId ?? '']) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Safety Drives') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($safetyDrives) }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-shield-check fs-3 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                {{-- 4. Major Deficiencies --}}
                <div class="col-xl-3 col-md-6 col-sm-6 col-12">
                    <a href="{{ route('report.my-movement.index', ['deficiency' => 'YES']) }}" class="text-decoration-none">
                        <div class="card border-0 shadow-sm mb-0 h-100" style="background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); color: #fff; border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-3px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="text-white-50 small text-uppercase fw-semibold">{{ __('Major Deficiencies') }}</div>
                                        <h3 class="fw-bold mb-0 text-white mt-1">{{ number_format($majorDeficiencies) }}</h3>
                                    </div>
                                    <div class="bg-white bg-opacity-25 p-2 rounded-circle">
                                        <i class="ti ti-alert-triangle fs-3 text-white"></i>
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
            <div class="card border-0 shadow-sm movement-filter-card" style="border-radius: 12px;">
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('report.my-movement.index') }}" id="movementFilterForm">
                        <div class="row g-2 align-items-end">

                            {{-- 1. Officer Filter (Dropdown of all distinct officers) --}}
                            <div class="col-xl-3 col-md-4 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">{{ __('Movement By (Officer)') }}</label>
                                <select name="officer_name" class="form-select no-select2">
                                    <option value="all">{{ __('All Officers') }}</option>
                                    @foreach($officers as $officer)
                                        <option value="{{ $officer }}" {{ request('officer_name') == $officer ? 'selected' : '' }}>
                                            {{ $officer }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 2. Type of Movement Filter --}}
                            <div class="col-xl-3 col-md-4 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">{{ __('Type of Movement') }}</label>
                                <select name="movement_id" class="form-select no-select2" id="movement_id_filter">
                                    <option value="all">{{ __('All Movements') }}</option>
                                    @foreach($movements as $mId => $mName)
                                        <option value="{{ $mId }}" {{ request('movement_id') == $mId ? 'selected' : '' }}>
                                            {{ $mName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 3. Regular Inspection Type Filter --}}
                            <div class="col-xl-2 col-md-4 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">{{ __('Inspection Type') }}</label>
                                <select name="type_of_inspection_id" class="form-select no-select2">
                                    <option value="all">{{ __('All Inspection Types') }}</option>
                                    @foreach($inspectionTypes as $itId => $itName)
                                        <option value="{{ $itId }}" {{ request('type_of_inspection_id') == $itId ? 'selected' : '' }}>
                                            {{ $itName }}
                                        </option>
                                    @endforeach
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

                            {{-- 5. Deficiency Filter --}}
                            <div class="col-xl-2 col-md-3 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">{{ __('Deficiency') }}</label>
                                <select name="deficiency" class="form-select no-select2">
                                    <option value="all">{{ __('All Records') }}</option>
                                    <option value="YES" {{ request('deficiency') == 'YES' ? 'selected' : '' }}>{{ __('Major Deficiency (YES)') }}</option>
                                    <option value="NO" {{ request('deficiency') == 'NO' ? 'selected' : '' }}>{{ __('Compliant (NO)') }}</option>
                                </select>
                            </div>

                            {{-- 6. Date From --}}
                            <div class="col-xl-2 col-md-3 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">{{ __('From Date') }}</label>
                                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                            </div>

                            {{-- 7. Date To --}}
                            <div class="col-xl-2 col-md-3 col-sm-6">
                                <label class="form-label small fw-semibold text-muted mb-1">{{ __('To Date') }}</label>
                                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                            </div>

                            {{-- 8. Search Keyword --}}
                            <div class="col-xl-5 col-md-6 col-sm-12">
                                <label class="form-label small fw-semibold text-muted mb-1">{{ __('Search Details') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0" style="border-radius: 8px 0 0 8px; border-color: #cbd5e1;">
                                        <i class="ti ti-search text-muted"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="{{ __('Search by Officer, Designation, Train No, Block Section, Reason, Gate...') }}" value="{{ request('search') }}">
                                </div>
                            </div>

                            {{-- 9. Filter Buttons --}}
                            <div class="col-xl-3 col-md-3 col-sm-12 d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-100 fw-semibold d-flex align-items-center justify-content-center" style="height: 44px; border-radius: 8px;">
                                    <i class="ti ti-filter me-1 fs-5"></i>{{ __('Apply Filter') }}
                                </button>
                                <a href="{{ route('report.my-movement.index') }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="height: 44px; min-width: 44px; border-radius: 8px;" title="{{ __('Reset Filters') }}">
                                    <i class="ti ti-refresh fs-5"></i>
                                </a>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Table & Export Section --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">

                {{-- Card Header with Action Buttons --}}
                <div class="card-header bg-white py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-3 border-bottom">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="ti ti-walk text-primary me-2"></i>{{ __('My Movement Records') }}
                        </h5>
                        <small class="text-muted">
                            {{ __('Total :count records matching your filter criteria', ['count' => count($records)]) }}
                        </small>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        {{-- Excel Export --}}
                        <a href="{{ route('report.my-movement.export.excel', request()->all()) }}" class="btn btn-outline-success fw-semibold btn-sm d-flex align-items-center px-3" style="height: 38px; border-radius: 8px;">
                            <i class="ti ti-file-spreadsheet me-1 fs-5"></i>{{ __('Export to Excel') }}
                        </a>

                        {{-- PDF Comprehensive Export --}}
                        <a href="{{ route('report.my-movement.export.pdf', request()->all()) }}" class="btn btn-outline-danger fw-semibold btn-sm d-flex align-items-center px-3" style="height: 38px; border-radius: 8px;" target="_blank">
                            <i class="ti ti-file-text me-1 fs-5"></i>{{ __('Comprehensive PDF') }}
                        </a>
                    </div>
                </div>

                {{-- Table View with advance-datatable (Auto pagination matching Image 3, 0 huge arrows) --}}
                <div class="card-body p-3">
                    <div class="dt-responsive table-responsive">
                        <table class="table table-hover align-middle mb-0 advance-datatable" id="myMovementTable">
                            <thead class="table-light text-uppercase small fw-bold" style="background-color: #f1f5f9; color: #475569;">
                                <tr>
                                    <th class="ps-3 py-3" style="width: 45px;">#</th>
                                    <th style="min-width: 130px;">{{ __('Date & Time') }}</th>
                                    <th style="min-width: 170px;">{{ __('Movement By') }}</th>
                                    <th style="min-width: 140px;">{{ __('Type of Movement') }}</th>
                                    <th style="min-width: 140px;">{{ __('Inspection Type') }}</th>
                                    <th style="min-width: 260px;">{{ __('Details Captured') }}</th>
                                    <th style="min-width: 140px;">{{ __('Station / Loc') }}</th>
                                    <th style="min-width: 100px;">{{ __('Deficiency') }}</th>
                                    <th class="text-center pe-3" style="min-width: 110px;">{{ __('ACTION') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records as $index => $rec)
                                    @php
                                        $mName = strtoupper($rec->movement->name ?? '');
                                        $reg = $rec->regularInspection;
                                        $amb = $rec->ambushInspection;
                                        $tName = $reg && $reg->type ? strtoupper($reg->type->name) : '';
                                        
                                        $formattedDate = $rec->inspection_date 
                                            ? \Carbon\Carbon::parse($rec->inspection_date)->format('d M, Y') 
                                            : ($rec->created_at ? $rec->created_at->format('d M, Y') : '-');
                                        
                                        $formattedTime = $rec->created_at ? $rec->created_at->format('h:i A') : '-';
                                        
                                        $officerName = $rec->officer_name ?: ($rec->employee->name ?? '-');
                                        $officerDesig = $rec->designation ?: ($rec->employee->designation->name ?? '-');

                                        // Resolve Station
                                        $displayStation = null;
                                        if($rec->station) {
                                            $displayStation = $rec->station->name . ' (' . $rec->station->code . ')';
                                        } elseif($reg && $reg->station) {
                                            $displayStation = $reg->station->name . ' (' . $reg->station->code . ')';
                                        } elseif($amb && $amb->station) {
                                            $displayStation = $amb->station->name . ' (' . $amb->station->code . ')';
                                        } elseif($reg && $reg->dieselLobby) {
                                            $displayStation = $reg->dieselLobby->name;
                                        } elseif($reg && $reg->runningRoom) {
                                            $displayStation = $reg->runningRoom->name;
                                        }

                                        // Resolve Deficiency
                                        $hasMajorDeficiency = false;
                                        $deficiencyNote = null;
                                        if($reg && strtoupper($reg->major_deficiency ?? '') === 'YES') {
                                            $hasMajorDeficiency = true;
                                            $deficiencyNote = $reg->write_deficiency;
                                        } elseif($amb && strtoupper($amb->major_deficiency ?? '') === 'YES') {
                                            $hasMajorDeficiency = true;
                                            $deficiencyNote = $amb->write_deficiency;
                                        }
                                        $hasEvaluatedDeficiency = ($reg !== null) || ($amb !== null);
                                    @endphp
                                    <tr>
                                        {{-- Serial --}}
                                        <td class="ps-3 text-muted small fw-semibold">
                                            {{ $index + 1 }}
                                        </td>

                                        {{-- Movement Date & Time --}}
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $formattedDate }}</div>
                                            <div class="text-muted small"><i class="ti ti-clock me-1 text-primary"></i>{{ $formattedTime }}</div>
                                        </td>

                                        {{-- Movement By --}}
                                        <td>
                                            <div class="fw-bold text-dark">{{ $officerName }}</div>
                                            <div class="text-muted small">
                                                <span class="badge bg-light text-dark border">{{ $officerDesig }}</span>
                                            </div>
                                        </td>

                                        {{-- Type of Movement --}}
                                        <td>
                                            @if(str_contains($mName, 'REGULAR'))
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle movement-badge-custom">
                                                    <i class="ti ti-clipboard-check me-1"></i>{{ $rec->movement->name ?? 'Regular Inspection' }}
                                                </span>
                                            @elseif(str_contains($mName, 'AMBUSH'))
                                                <span class="badge bg-info-subtle text-info border border-info-subtle movement-badge-custom">
                                                    <i class="ti ti-eye me-1"></i>{{ $rec->movement->name ?? 'Ambush Check' }}
                                                </span>
                                            @elseif(str_contains($mName, 'SAFETY'))
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle movement-badge-custom">
                                                    <i class="ti ti-shield me-1"></i>{{ $rec->movement->name ?? 'Safety Drive' }}
                                                </span>
                                            @elseif(str_contains($mName, 'LEAVE'))
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle movement-badge-custom">
                                                    <i class="ti ti-calendar-off me-1"></i>{{ $rec->movement->name ?? 'On Leave' }}
                                                </span>
                                            @elseif(str_contains($mName, 'REST'))
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle movement-badge-custom">
                                                    <i class="ti ti-coffee me-1"></i>{{ $rec->movement->name ?? 'Rest' }}
                                                </span>
                                            @else
                                                <span class="badge bg-light text-dark border movement-badge-custom">
                                                    <i class="ti ti-activity me-1"></i>{{ $rec->movement->name ?? '-' }}
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Inspection Type --}}
                                        <td>
                                            @if($reg && $reg->type)
                                                <span class="badge bg-light text-dark border fw-semibold">
                                                    {{ $reg->type->name }}
                                                </span>
                                            @elseif($amb && $amb->ambushType)
                                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle fw-semibold">
                                                    {{ $amb->ambushType->name }}
                                                </span>
                                            @elseif(str_contains($mName, 'REGULAR'))
                                                <span class="text-muted small">General Regular</span>
                                            @elseif(str_contains($mName, 'AMBUSH'))
                                                <span class="text-muted small">Ambush Observation</span>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>

                                        {{-- Details Captured (Dynamic per Module specifications) --}}
                                        <td>
                                            <div class="table-details-box">
                                                {{-- Regular Inspection Breakdown --}}
                                                @if(str_contains($mName, 'REGULAR'))
                                                    @if($reg)
                                                        @if(str_contains($tName, 'THOROUGH') || str_contains($tName, 'SAFETY'))
                                                            <div><span class="text-muted small">{{ __('Station') }}:</span> <strong>{{ $reg->station ? $reg->station->name . ' (' . $reg->station->code . ')' : '-' }}</strong></div>
                                                        @elseif(str_contains($tName, 'SURPRISE') || str_contains($tName, 'NIGHT'))
                                                            <div><span class="text-muted small">{{ __('Station') }}:</span> <strong>{{ $reg->station ? $reg->station->name : '-' }}</strong></div>
                                                            <div class="small text-muted">{{ __('LC Gate') }}: <strong>{{ $reg->gate_no ?: '-' }}</strong> ({{ $reg->engg_traffic ?: '-' }})</div>
                                                            <div class="small text-muted">{{ __('Section') }}: <strong>{{ $reg->block_section ?: '-' }}</strong></div>
                                                        @elseif(str_contains($tName, 'LC GATE'))
                                                            <div><span class="text-muted small">{{ __('LC Gate') }}:</span> <strong>{{ $reg->gate_no ?: '-' }}</strong> ({{ $reg->engg_traffic ?: '-' }})</div>
                                                            <div class="small text-muted">{{ __('Section') }}: <strong>{{ $reg->block_section ?: '-' }}</strong></div>
                                                        @elseif(str_contains($tName, 'LC AMBUSH'))
                                                            <div><span class="text-muted small">{{ __('LC Gate') }}:</span> <strong>{{ $reg->gate_no ?: '-' }}</strong></div>
                                                            <div class="small text-muted">{{ __('Train') }}: <strong>{{ $reg->train_no ?: '-' }}</strong> | {{ __('Sec') }}: <strong>{{ $reg->block_section ?: '-' }}</strong></div>
                                                        @elseif(str_contains($tName, 'FOOTPLATE') || str_contains($tName, 'BRAKE-VAN') || str_contains($tName, 'BRAKE VAN'))
                                                            <div><span class="text-muted small">{{ __('Inspection By') }}:</span> <strong>{{ $reg->goods_passenger ?: '-' }}</strong></div>
                                                            <div class="small text-muted">{{ __('Train') }}: <strong>{{ $reg->train_no ?: '-' }}</strong> | {{ __('Sec') }}: <strong>{{ $reg->block_section ?: '-' }}</strong></div>
                                                        @elseif(str_contains($tName, 'DIESEL LOBBY'))
                                                            <div><span class="text-muted small">{{ __('Location') }}:</span> <strong>{{ $reg->dieselLobby->name ?? ($reg->location_id ?: '-') }}</strong></div>
                                                        @elseif(str_contains($tName, 'RUNNING ROOM'))
                                                            <div><span class="text-muted small">{{ __('Location') }}:</span> <strong>{{ $reg->runningRoom->name ?? ($reg->location_id ?: '-') }}</strong></div>
                                                        @else
                                                            <div><span class="text-muted small">{{ __('Section/Station') }}:</span> <strong>{{ $reg->block_section ?: ($reg->station->name ?? 'Regular Inspection logged') }}</strong></div>
                                                        @endif
                                                    @else
                                                        <span class="text-muted small">{{ __('Regular inspection record logged') }}</span>
                                                    @endif

                                                {{-- Ambush Check Breakdown --}}
                                                @elseif(str_contains($mName, 'AMBUSH'))
                                                    @if($amb)
                                                        @if($amb->shunting_observation)
                                                            <div><span class="text-muted small">{{ __('Observation') }}:</span> <strong>{{ Str::limit($amb->shunting_observation, 35) }}</strong></div>
                                                        @endif
                                                        @if($amb->train_no)
                                                            <div class="small text-muted">{{ __('Train No') }}: <strong>{{ $amb->train_no }}</strong></div>
                                                        @endif
                                                        @if($amb->lc_gate_no)
                                                            <div class="small text-muted">{{ __('LC Gate') }}: <strong>{{ $amb->lc_gate_no }}</strong> | {{ __('Sec') }}: <strong>{{ $amb->block_section ?: '-' }}</strong></div>
                                                        @elseif($amb->block_section)
                                                            <div class="small text-muted">{{ __('Section') }}: <strong>{{ $amb->block_section }}</strong></div>
                                                        @endif
                                                        @if(!$amb->shunting_observation && !$amb->train_no && !$amb->lc_gate_no)
                                                            <div class="small text-muted">{{ __('Ambush Check at') }} <strong>{{ $amb->station->name ?? 'Station' }}</strong></div>
                                                        @endif
                                                    @else
                                                        <span class="text-muted small">{{ __('Ambush check inspection logged') }}</span>
                                                    @endif

                                                {{-- Module 3 Specific Types --}}
                                                @elseif(str_contains($mName, 'SAFETY DRIVE'))
                                                    <div><span class="text-muted small">{{ __('Type of Safety Drive') }}:</span> <strong>{{ $rec->safetyDrive->name ?? '-' }}</strong></div>
                                                @elseif(str_contains($mName, 'JOINT SIGNAL SIGHTING'))
                                                    <div><span class="text-muted small">{{ __('Inspection By') }}:</span> <strong>{{ $rec->goods_passenger ?: '-' }}</strong></div>
                                                    <div class="small text-muted">{{ __('Train') }}: <strong>{{ $rec->train_no ?: '-' }}</strong> | {{ __('Sec') }}: <strong>{{ $rec->block_section ?: '-' }}</strong></div>
                                                @elseif(str_contains($mName, 'NI DUTY'))
                                                    <div><span class="text-muted small">{{ __('Station Name') }}:</span> <strong>{{ $rec->station ? $rec->station->name . ' (' . $rec->station->code . ')' : '-' }}</strong></div>
                                                @elseif(str_contains($mName, 'JOINT NOTE'))
                                                    <div><span class="text-muted small">{{ __('Station') }}:</span> <strong>{{ $rec->station ? $rec->station->name : '-' }}</strong></div>
                                                    <div class="small text-muted text-truncate" title="{{ $rec->reason }}">{{ __('Reason') }}: {{ $rec->reason ?: '-' }}</div>
                                                    @if($rec->joint_note_pdf)
                                                        <a href="{{ asset('storage/' . $rec->joint_note_pdf) }}" target="_blank" class="badge bg-primary text-white text-decoration-none mt-1">
                                                            <i class="ti ti-file-text me-1"></i>{{ __('View Joint Note PDF') }}
                                                        </a>
                                                    @endif
                                                @elseif(str_contains($mName, 'SURVEY'))
                                                    <div><span class="text-muted small">{{ __('Station') }}:</span> <strong>{{ $rec->station ? $rec->station->name : '-' }}</strong></div>
                                                    <div class="small text-muted text-truncate" title="{{ $rec->reason }}">{{ __('Reason') }}: {{ $rec->reason ?: '-' }}</div>
                                                    @if($rec->survey_pdf)
                                                        <a href="{{ asset('storage/' . $rec->survey_pdf) }}" target="_blank" class="badge bg-primary text-white text-decoration-none mt-1">
                                                            <i class="ti ti-file-text me-1"></i>{{ __('View Survey PDF') }}
                                                        </a>
                                                    @endif
                                                @elseif(str_contains($mName, 'ATTEND BLOCK'))
                                                    <div><span class="text-muted small">{{ __('Type of Attend Block') }}:</span> <strong>{{ $rec->attendBlock->name ?? '-' }}</strong></div>
                                                @elseif(str_contains($mName, 'ON LEAVE'))
                                                    <div><span class="text-muted small">{{ __('Type of Leave') }}:</span> <strong>{{ $rec->typeOfLeave->name ?? '-' }}</strong></div>
                                                @elseif(str_contains($mName, 'OFFICE') || str_contains($mName, 'OTHER'))
                                                    <div class="small text-muted">{{ __('Description') }}:</div>
                                                    <div class="fw-semibold text-dark text-truncate" title="{{ $rec->reason }}">{{ $rec->reason ?: '-' }}</div>
                                                @elseif(str_contains($mName, 'REST') || str_contains($mName, 'COUNSELING'))
                                                    <span class="badge bg-light text-muted border">{{ __('No additional fields') }}</span>
                                                @else
                                                    <div class="small text-muted">{{ $rec->reason ?: '-' }}</div>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Station / Location --}}
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $displayStation ?: '-' }}</div>
                                            @if($rec->gps_location)
                                                <a href="https://maps.google.com/?q={{ $rec->gps_location }}" target="_blank" class="small text-primary text-decoration-none" title="{{ $rec->gps_location }}">
                                                    <i class="ti ti-map-pin me-1"></i>{{ __('GPS') }}
                                                </a>
                                            @endif
                                        </td>

                                        {{-- Deficiency --}}
                                        <td>
                                            @if($hasMajorDeficiency)
                                                <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 font-12 fw-semibold" data-bs-toggle="tooltip" title="{{ $deficiencyNote ?: 'Major Deficiency Noted' }}">
                                                    <i class="ti ti-alert-triangle me-1"></i>{{ __('YES') }}
                                                </span>
                                            @elseif($hasEvaluatedDeficiency)
                                                <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3 py-1 font-12 fw-semibold">
                                                    <i class="ti ti-check me-1"></i>{{ __('NO') }}
                                                </span>
                                            @else
                                                <span class="badge rounded-pill bg-light text-muted border px-3 py-1 font-12">{{ __('N/A') }}</span>
                                            @endif
                                        </td>

                                        {{-- Action --}}
                                        <td class="text-center pe-3">
                                            <div class="d-inline-flex gap-2">
                                                {{-- View Modal Button --}}
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-secondary open-movement-modal" data-id="{{ $rec->id }}" title="{{ __('View Details') }}" style="border-radius: 6px; width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center;">
                                                    <i class="ti ti-eye fs-5"></i>
                                                </button>

                                                {{-- Download Single Dossier PDF --}}
                                                <a href="{{ route('report.my-movement.export.movement-pdf', $rec->id) }}" class="btn btn-sm btn-icon btn-outline-danger" title="{{ __('Download Movement PDF') }}" target="_blank" style="border-radius: 6px; width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center;">
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
                                                <h6 class="fw-bold text-muted mt-3">{{ __('No Movement Records Found') }}</h6>
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
    <div class="modal fade" id="movementDetailModal" tabindex="-1" aria-labelledby="movementDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;" id="movementModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted fw-semibold">Loading Movement Record Details...</p>
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

        // Re-init tooltips when DataTable draws
        if ($.fn.DataTable) {
            $('#myMovementTable').on('draw.dt', function () {
                $('[data-bs-toggle="tooltip"]').tooltip();
            });
        }

        // View Modal Click - delegated on document so it works on every DataTable page, search, or filter
        $(document).on('click', '.open-movement-modal', function (e) {
            e.preventDefault();
            var recordId = $(this).data('id');
            
            $('#movementDetailModal').modal('show');
            $('#movementModalBody').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2 text-muted fw-semibold">Loading Movement Record Details...</p></div>');

            $.ajax({
                url: "{{ url('report/my-movement-report') }}/" + recordId + "/show",
                type: "GET",
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function (response) {
                    $('#movementModalBody').html(response);
                },
                error: function (xhr) {
                    $('#movementModalBody').html('<div class="p-4 text-center text-danger"><i class="ti ti-alert-circle fs-1 mb-2 d-block"></i><p class="fw-semibold mb-0">Failed to load movement record details. Please try again.</p></div>');
                }
            });
        });
    });
</script>
@endpush