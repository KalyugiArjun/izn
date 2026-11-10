@extends('layouts.app')

@section('page-title', in_array(strtolower(Auth::user()->type ?? ''), ['super admin', 'owner', 'admin']) ? __('Award Nomination Authority') : __('Award Nomination & Decision Portal'))

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}"><i class="ti ti-home me-1"></i>{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">{{ in_array(strtolower(Auth::user()->type ?? ''), ['super admin', 'owner', 'admin']) ? __('Award Nomination Authority') : __('Award Nomination') }}</li>
@endsection

@section('content')

@if (in_array(strtolower(Auth::user()->type ?? ''), ['super admin', 'owner', 'admin']))
    {{-- ================= ADMIN VIEW: Standard Authority List (No tabs) ================= --}}
    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                {{-- Header --}}
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>{{ __('Award Nomination Authority List') }}</h5>
                        </div>

                        <div class="col-auto d-flex align-items-center gap-2">
                            {{-- Create Award Nomination Button (Left side) --}}
                            <a href="#"
                               class="btn btn-primary customModal"
                               data-size="lg"
                               data-url="{{ route('authority.award-nomination.nominate.create') }}"
                               data-title="{{ __('Create Award Nomination') }}">
                                <i class="ti ti-circle-plus align-text-bottom me-1"></i>
                                {{ __('Create Award Nomination') }}
                            </a>

                            {{-- Create Award Nomination Authority Button (Right side) --}}
                            <a href="#"
                               class="btn btn-secondary customModal"
                               data-size="md"
                               data-url="{{ route('authority.award-nomination.create') }}"
                               data-title="{{ __('Create Award Nomination Authority') }}">
                                <i class="ti ti-circle-plus align-text-bottom me-1"></i>
                                {{ __('Create Award Nomination Authority') }}
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Body --}}
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover advance-datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Department') }}</th>
                                    <th>{{ __('Designation') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($authorities as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>

                                        <td>
                                            <span class="fw-semibold text-dark">{{ $item->department->name ?? '-' }}</span>
                                        </td>

                                        <td>
                                            <span class="badge bg-light-primary text-primary fw-medium">{{ $item->designation->name ?? '-' }}</span>
                                        </td>

                                        <td class="text-end">
                                            <div class="cart-action">

                                                {!! Form::open([
                                                    'method' => 'DELETE',
                                                    'route' => [
                                                        'authority.award-nomination.destroy',
                                                        encrypt($item->id)
                                                    ]
                                                ]) !!}

                                                {{-- Edit --}}
                                                <a class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                   data-bs-toggle="tooltip"
                                                   data-size="lg"
                                                   data-bs-original-title="{{ __('Edit') }}"
                                                   href="#"
                                                   data-url="{{ route('authority.award-nomination.edit', encrypt($item->id)) }}"
                                                   data-title="{{ __('Edit Award Nomination') }}">
                                                    <i data-feather="edit"></i>
                                                </a>

                                                {{-- Delete --}}
                                                <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-original-title="{{ __('Delete') }}"
                                                   href="#">
                                                    <i data-feather="trash-2"></i>
                                                </a>

                                                {!! Form::close() !!}

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            {{ __('No authority records found.') }}
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
@else
    {{-- ================= SR. DOM & REVIEWERS VIEW: 3-Stage Decision Portal ================= --}}
    <style>
        .kpi-card {
            border: 1px solid #eef2f6;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
        }
        .kpi-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }
        #awardNominationTabs {
            border-bottom: none !important;
            gap: 10px !important;
        }
        #awardNominationTabs .nav-item {
            margin-bottom: 0 !important;
        }
        .srdom-nav .nav-link {
            color: #475569;
            font-weight: 600;
            border: 1px solid #e2e8f0;
            padding: 8px 18px;
            border-radius: 50px;
            background: #fff;
            transition: all 0.2s ease;
        }
        .srdom-nav .nav-link:hover {
            background: #f8fafc;
        }
        .srdom-nav .nav-link.active#tab-pending {
            background: #f59e0b !important;
            color: #000 !important;
            border-color: #f59e0b !important;
        }
        .srdom-nav .nav-link.active#tab-forwarded {
            background: #0284c7 !important;
            color: #fff !important;
            border-color: #0284c7 !important;
        }
        .srdom-nav .nav-link.active#tab-approved {
            background: #10b981 !important;
            color: #fff !important;
            border-color: #10b981 !important;
        }
        .srdom-nav .nav-link.active#tab-rejected {
            background: #ef4444 !important;
            color: #fff !important;
            border-color: #ef4444 !important;
        }
        .srdom-nav .nav-link.active#tab-authorities {
            background: #475569 !important;
            color: #fff !important;
            border-color: #475569 !important;
        }
        .badge-status-0 {
            background-color: #fef3c7 !important;
            color: #92400e !important;
            border: 1px solid #fde68a;
        }
        .badge-status-1 {
            background-color: #e0f2fe !important;
            color: #0369a1 !important;
            border: 1px solid #bae6fd;
        }
        .badge-status-2 {
            background-color: #dcfce7 !important;
            color: #166534 !important;
            border: 1px solid #bbf7d0;
        }
        .badge-status-3 {
            background-color: #fee2e2 !important;
            color: #991b1b !important;
            border: 1px solid #fecaca;
        }
        .table-responsive {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
        }
        .srdom-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }
        .srdom-table th {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #475569;
            background-color: #f8fafc !important;
            padding: 12px 14px;
            border-bottom: 2px solid #e2e8f0;
            white-space: nowrap;
        }
        .srdom-table td {
            padding: 12px 14px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }
        .col-action {
            white-space: nowrap !important;
            min-width: 180px;
            text-align: right;
        }
        .rejection-box {
            background-color: #fef2f2 !important;
            border: 1px solid #f87171 !important;
            border-radius: 8px;
            padding: 8px 12px;
            box-sizing: border-box;
            display: block;
        }
        .rejection-box p {
            line-height: 1.5;
            word-break: break-word;
            white-space: normal;
            margin: 4px 0 0 0;
            font-size: 12px;
            color: #b91c1c;
        }
        /* Ensure tab panes and table have enough vertical height so dropdowns always open downwards */
        .tab-pane {
            min-height: 480px;
        }
        .table-responsive {
            min-height: 320px;
        }
        /* Custom Filter Dropdowns - robust downwards searchable dropdowns */
        .custom-filter-dropdown {
            position: relative;
            width: 100%;
        }
        .custom-filter-dropdown .form-select {
            height: 34px !important;
            padding: 4px 28px 4px 10px !important;
            border: 1px solid #ced4da !important;
            border-radius: 6px !important;
            color: #334155;
            font-size: 13px;
            line-height: 24px;
            background-color: #fff;
        }
        .custom-filter-dropdown .dropdown-menu {
            top: 100% !important;
            bottom: auto !important;
            left: 0 !important;
            right: auto !important;
            transform: none !important;
            margin-top: 4px !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15) !important;
            z-index: 1060 !important;
        }
        .custom-filter-dropdown .dropdown-item {
            font-size: 13px;
            padding: 6px 14px;
            cursor: pointer;
            transition: background-color 0.15s ease-in-out;
        }
        .custom-filter-dropdown .dropdown-item:hover {
            background-color: #f1f5f9;
            color: #0f172a;
        }
        .custom-filter-dropdown .dropdown-item.active {
            background-color: #0d6efd !important;
            color: #ffffff !important;
            font-weight: 600;
        }
    </style>

    @if(Auth::user()->isTiSafety())
        {{-- ================= TI SAFETY VIEW (PENDING & FORWARDED ONLY) ================= --}}

        {{-- Top Action & Role Banner --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 my-3">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3 shadow-sm d-inline-flex align-items-center">
                <i class="ti ti-arrow-left me-1"></i> {{ __('Back to Dashboard') }}
            </a>
            <span class="badge bg-light-info text-info px-3 py-2 rounded-pill fs-7 border border-info">
                <i class="ti ti-shield-check me-1"></i> {{ __('TI Safety Division Nomination Queue') }}
            </span>
        </div>

        {{-- KPI Stat Cards for TI Safety (Only 3 Cards: Total, Pending, Forwarded) --}}
        <div class="row g-3 my-3">
            <div class="col-md-4">
                <div class="card kpi-card h-100 mb-0 shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted text-uppercase fw-semibold fs-7 d-block mb-1">{{ __('Total Nominations') }}</span>
                                <h4 class="mb-0 fw-bold text-dark">{{ $stats['total_nominations'] }}</h4>
                            </div>
                            <div class="kpi-icon bg-light-primary text-primary">
                                <i class="ti ti-award"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card kpi-card h-100 mb-0 shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted text-uppercase fw-semibold fs-7 d-block mb-1">{{ __('Pending to Forward') }}</span>
                                <h4 class="mb-0 fw-bold text-warning">{{ $stats['pending'] }}</h4>
                            </div>
                            <div class="kpi-icon bg-light-warning text-warning">
                                <i class="ti ti-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card kpi-card h-100 mb-0 shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted text-uppercase fw-semibold fs-7 d-block mb-1">{{ __('Forwarded to Sr. DOM') }}</span>
                                <h4 class="mb-0 fw-bold text-info">{{ $stats['forwarded'] }}</h4>
                            </div>
                            <div class="kpi-icon bg-light-info text-info">
                                <i class="ti ti-send"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Container Card for TI Safety --}}
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white border-bottom py-3 px-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <ul class="nav nav-pills srdom-nav gap-2 mb-0" id="tiAwardNominationTabs" role="tablist">
                        {{-- Tab 1: Pending Nominations --}}
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $activeTab === 'pending' ? 'active' : '' }}" id="tab-pending"
                                data-bs-toggle="pill" data-bs-target="#pane-ti-pending" type="button" role="tab">
                                <i class="ti ti-clock-pause me-1"></i> {{ __('1. Pending Nominations') }}
                                <span class="badge {{ $activeTab === 'pending' ? 'bg-dark text-white' : 'bg-warning text-dark' }} rounded-pill ms-2">{{ $stats['pending'] }}</span>
                            </button>
                        </li>

                        {{-- Tab 2: Forwarded Nominations --}}
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $activeTab === 'forwarded' ? 'active' : '' }}" id="tab-forwarded"
                                data-bs-toggle="pill" data-bs-target="#pane-ti-forwarded" type="button" role="tab">
                                <i class="ti ti-send me-1"></i> {{ __('2. Forwarded Nominations') }}
                                <span class="badge {{ $activeTab === 'forwarded' ? 'bg-white text-info' : 'bg-info text-white' }} rounded-pill ms-2">{{ $stats['forwarded'] }}</span>
                            </button>
                        </li>
                    </ul>

                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm d-inline-flex align-items-center">
                            <i class="ti ti-arrow-left me-1"></i>
                            {{ __('Back to Dashboard') }}
                        </a>
                        <a href="#" class="btn btn-primary btn-sm rounded-pill customModal px-3 shadow-sm"
                            data-size="lg"
                            data-url="{{ route('authority.award-nomination.nominate.create') }}"
                            data-title="{{ __('Create Award Nomination') }}">
                            <i class="ti ti-circle-plus align-text-bottom me-1"></i>
                            {{ __('Create Award Nomination') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                @php
                    $hasStations = isset($stations) && $stations->isNotEmpty();
                    $hasProficiencies = isset($proficiencies) && $proficiencies->isNotEmpty();
                    if ($hasStations && $hasProficiencies) {
                        $filterColClass = 'col-md-4';
                    } elseif ($hasStations || $hasProficiencies) {
                        $filterColClass = 'col-md-6';
                    } else {
                        $filterColClass = 'col-12';
                    }
                @endphp
                <div class="tab-content" id="tiAwardNominationTabContent">

                    {{-- TAB 1: PENDING NOMINATIONS (STATUS 0) --}}
                    <div class="tab-pane fade {{ $activeTab === 'pending' ? 'show active' : '' }}" id="pane-ti-pending" role="tabpanel">
                        <form method="GET" action="{{ route('authority.award-nomination.index') }}" class="mb-4">
                            <input type="hidden" name="tab" value="pending">
                            <div class="row g-2 align-items-end p-3 bg-light rounded-3 border">
                                @if($hasStations)
                                    <div class="{{ $filterColClass }}">
                                        <label class="form-label text-muted small fw-semibold">{{ __('Station') }}</label>
                                        <div class="dropdown custom-filter-dropdown">
                                            <button class="form-select form-select-sm text-start bg-white shadow-none text-truncate" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" style="cursor: pointer;">
                                                <span class="dropdown-label">{{ request('station_id') && $stations->has(request('station_id')) ? $stations->get(request('station_id')) : __('All Stations') }}</span>
                                            </button>
                                            <div class="dropdown-menu p-1 shadow-lg border border-light-subtle rounded-3" style="width: 100%; min-width: 240px; max-height: 280px; overflow-y: auto; z-index: 1060;">
                                                <a class="dropdown-item py-1 px-3 small filter-opt-item {{ !request('station_id') ? 'active' : '' }}" href="javascript:void(0)" data-value="">{{ __('All Stations') }}</a>
                                                @foreach($stations as $sId => $sName)
                                                    <a class="dropdown-item py-1 px-3 small filter-opt-item {{ request('station_id') == $sId ? 'active' : '' }}" href="javascript:void(0)" data-value="{{ $sId }}">{{ $sName }}</a>
                                                @endforeach
                                            </div>
                                            <input type="hidden" name="station_id" class="filter-hidden-val" value="{{ request('station_id') }}">
                                        </div>
                                    </div>
                                @endif

                                @if($hasProficiencies)
                                    <div class="{{ $filterColClass }}">
                                        <label class="form-label text-muted small fw-semibold">{{ __('Area of Proficiency') }}</label>
                                        <div class="dropdown custom-filter-dropdown">
                                            <button class="form-select form-select-sm text-start bg-white shadow-none text-truncate" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" style="cursor: pointer;">
                                                <span class="dropdown-label">{{ request('proficiency_id') && $proficiencies->has(request('proficiency_id')) ? $proficiencies->get(request('proficiency_id')) : __('All Proficiencies') }}</span>
                                            </button>
                                            <div class="dropdown-menu p-1 shadow-lg border border-light-subtle rounded-3" style="width: 100%; min-width: 260px; max-height: 280px; overflow-y: auto; z-index: 1060;">
                                                <a class="dropdown-item py-1 px-3 small filter-opt-item {{ !request('proficiency_id') ? 'active' : '' }}" href="javascript:void(0)" data-value="">{{ __('All Proficiencies') }}</a>
                                                @foreach($proficiencies as $pId => $pName)
                                                    <a class="dropdown-item py-1 px-3 small filter-opt-item {{ request('proficiency_id') == $pId ? 'active' : '' }}" href="javascript:void(0)" data-value="{{ $pId }}">{{ $pName }}</a>
                                                @endforeach
                                            </div>
                                            <input type="hidden" name="proficiency_id" class="filter-hidden-val" value="{{ request('proficiency_id') }}">
                                        </div>
                                    </div>
                                @endif

                                <div class="{{ $filterColClass }}">
                                    <label class="form-label text-muted small fw-semibold">{{ __('Search') }}</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search staff, certificate, description...">
                                        <button type="submit" class="btn btn-primary"><i class="ti ti-search"></i></button>
                                        @if(request()->hasAny(['station_id', 'proficiency_id', 'search']))
                                            <a href="{{ route('authority.award-nomination.index', ['tab' => 'pending']) }}" class="btn btn-light" title="Reset Filters"><i class="ti ti-refresh"></i></a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 srdom-table" style="min-width: 1100px;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 45px; text-align: center;">#</th>
                                        <th style="width: 120px;">{{ __('Date') }}</th>
                                        <th style="width: 200px;">{{ __('Nominee (Staff)') }}</th>
                                        <th style="width: 150px;">{{ __('Station & Desig') }}</th>
                                        <th style="min-width: 240px;">{{ __('Meritorious Work Description') }}</th>
                                        <th style="width: 120px; text-align: center;">{{ __('Proof Document') }}</th>
                                        <th style="width: 120px; text-align: center;">{{ __('Status') }}</th>
                                        <th class="text-end" style="width: 190px; min-width: 190px;">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($tiPendingNominations as $index => $item)
                                        <tr>
                                            <td class="fw-semibold text-muted text-center">{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="fw-semibold text-dark">{{ $item->date ? \Carbon\Carbon::parse($item->date)->format('d M, Y') : '-' }}</div>
                                                <small class="text-muted">{{ $item->time ? \Carbon\Carbon::parse($item->time)->format('h:i A') : '' }}</small>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $item->name ?? ($item->staff->name ?? '-') }}</div>
                                                <small class="text-muted">USMID: {{ $item->staff->usmid ?? 'N/A' }} | Mobile: {{ $item->staff->mobile ?? 'N/A' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">{{ $item->station->name ?? '-' }}</span>
                                                <div class="small text-muted mt-1">{{ $item->designation->name ?? '-' }}</div>
                                            </td>
                                            <td style="white-space: normal; word-break: break-word;">
                                                <div class="small text-dark" style="line-height: 1.4;">{{ $item->description ?: '-' }}</div>
                                                @if($item->proficiency)
                                                    <span class="badge bg-light-primary text-primary mt-1" style="font-size: 11px;">{{ $item->proficiency->name }}</span>
                                                @endif
                                            </td>
                                            <td style="text-align: center; white-space: nowrap;">
                                                @if(!empty($item->file))
                                                    <a href="{{ asset('storage/' . $item->file) }}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-2 py-1 shadow-sm d-inline-flex align-items-center">
                                                        <i class="ti ti-paperclip me-1"></i>
                                                        <span class="fw-semibold">{{ strtoupper($item->file_type ?? 'Doc') }}</span>
                                                    </a>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                            <td style="text-align: center;">
                                                <span class="badge badge-status-0 px-2 py-1"><i class="ti ti-clock me-1"></i>Pending</span>
                                            </td>
                                            <td class="col-action" style="min-width: 190px;">
                                                <div class="d-inline-flex gap-1 align-items-center">
                                                    <a href="#" class="btn btn-sm btn-info text-white font-bold rounded-pill px-3 py-1 customModal shadow-sm"
                                                        data-size="md"
                                                        data-url="{{ route('authority.award-nomination.nominate.action', encrypt($item->id)) }}"
                                                        data-title="{{ __('Forward to Sr. DOM') }}">
                                                        <i class="ti ti-send me-1"></i>{{ __('Forward to Sr. DOM') }}
                                                    </a>
                                                    <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1 customModal"
                                                        data-size="lg"
                                                        data-url="{{ route('authority.award-nomination.nominate.show', encrypt($item->id)) }}"
                                                        data-title="{{ __('Award Nomination Dossier') }}" title="{{ __('View Dossier') }}">
                                                        <i class="ti ti-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5 text-muted">
                                                <i class="ti ti-circle-check fs-1 text-success d-block mb-2"></i>
                                                <h6 class="fw-bold text-dark">{{ __('No Pending Nominations') }}</h6>
                                                <small class="text-muted">All nominations in your queue have been forwarded.</small>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 2: FORWARDED NOMINATIONS (STATUS >= 1) --}}
                    <div class="tab-pane fade {{ $activeTab === 'forwarded' ? 'show active' : '' }}" id="pane-ti-forwarded" role="tabpanel">
                        <form method="GET" action="{{ route('authority.award-nomination.index') }}" class="mb-4">
                            <input type="hidden" name="tab" value="forwarded">
                            <div class="row g-2 align-items-end p-3 bg-light rounded-3 border">
                                @if($hasStations)
                                    <div class="{{ $filterColClass }}">
                                        <label class="form-label text-muted small fw-semibold">{{ __('Station') }}</label>
                                        <div class="dropdown custom-filter-dropdown">
                                            <button class="form-select form-select-sm text-start bg-white shadow-none text-truncate" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" style="cursor: pointer;">
                                                <span class="dropdown-label">{{ request('station_id') && $stations->has(request('station_id')) ? $stations->get(request('station_id')) : __('All Stations') }}</span>
                                            </button>
                                            <div class="dropdown-menu p-1 shadow-lg border border-light-subtle rounded-3" style="width: 100%; min-width: 240px; max-height: 280px; overflow-y: auto; z-index: 1060;">
                                                <a class="dropdown-item py-1 px-3 small filter-opt-item {{ !request('station_id') ? 'active' : '' }}" href="javascript:void(0)" data-value="">{{ __('All Stations') }}</a>
                                                @foreach($stations as $sId => $sName)
                                                    <a class="dropdown-item py-1 px-3 small filter-opt-item {{ request('station_id') == $sId ? 'active' : '' }}" href="javascript:void(0)" data-value="{{ $sId }}">{{ $sName }}</a>
                                                @endforeach
                                            </div>
                                            <input type="hidden" name="station_id" class="filter-hidden-val" value="{{ request('station_id') }}">
                                        </div>
                                    </div>
                                @endif

                                @if($hasProficiencies)
                                    <div class="{{ $filterColClass }}">
                                        <label class="form-label text-muted small fw-semibold">{{ __('Area of Proficiency') }}</label>
                                        <div class="dropdown custom-filter-dropdown">
                                            <button class="form-select form-select-sm text-start bg-white shadow-none text-truncate" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" style="cursor: pointer;">
                                                <span class="dropdown-label">{{ request('proficiency_id') && $proficiencies->has(request('proficiency_id')) ? $proficiencies->get(request('proficiency_id')) : __('All Proficiencies') }}</span>
                                            </button>
                                            <div class="dropdown-menu p-1 shadow-lg border border-light-subtle rounded-3" style="width: 100%; min-width: 260px; max-height: 280px; overflow-y: auto; z-index: 1060;">
                                                <a class="dropdown-item py-1 px-3 small filter-opt-item {{ !request('proficiency_id') ? 'active' : '' }}" href="javascript:void(0)" data-value="">{{ __('All Proficiencies') }}</a>
                                                @foreach($proficiencies as $pId => $pName)
                                                    <a class="dropdown-item py-1 px-3 small filter-opt-item {{ request('proficiency_id') == $pId ? 'active' : '' }}" href="javascript:void(0)" data-value="{{ $pId }}">{{ $pName }}</a>
                                                @endforeach
                                            </div>
                                            <input type="hidden" name="proficiency_id" class="filter-hidden-val" value="{{ request('proficiency_id') }}">
                                        </div>
                                    </div>
                                @endif

                                <div class="{{ $filterColClass }}">
                                    <label class="form-label text-muted small fw-semibold">{{ __('Search') }}</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search staff, certificate, description...">
                                        <button type="submit" class="btn btn-primary"><i class="ti ti-search"></i></button>
                                        @if(request()->hasAny(['station_id', 'proficiency_id', 'search']))
                                            <a href="{{ route('authority.award-nomination.index', ['tab' => 'forwarded']) }}" class="btn btn-light" title="Reset Filters"><i class="ti ti-refresh"></i></a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 srdom-table" style="min-width: 1200px;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 45px; text-align: center;">#</th>
                                        <th style="width: 120px;">{{ __('Date Forwarded') }}</th>
                                        <th style="width: 200px;">{{ __('Nominee (Staff)') }}</th>
                                        <th style="width: 150px;">{{ __('Station & Desig') }}</th>
                                        <th style="min-width: 220px; max-width: 280px;">{{ __('Meritorious Work Description') }}</th>
                                        <th style="min-width: 240px; max-width: 320px;">{{ __('Your TI Recommendation Comment') }}</th>
                                        <th style="width: 110px; text-align: center;">{{ __('Proof Document') }}</th>
                                        <th style="width: 160px; text-align: center;">{{ __('Current Status') }}</th>
                                        <th class="text-end" style="width: 120px; min-width: 120px;">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($tiForwardedNominations as $index => $item)
                                        <tr>
                                            <td class="fw-semibold text-muted text-center">{{ $loop->iteration }}</td>
                                            <td>
                                                <div class="fw-semibold text-dark">{{ $item->ti_forward_date ? \Carbon\Carbon::parse($item->ti_forward_date)->format('d M, Y') : ($item->updated_at ? $item->updated_at->format('d M, Y') : '-') }}</div>
                                                <small class="text-muted">{{ $item->ti_forward_time ? \Carbon\Carbon::parse($item->ti_forward_time)->format('h:i A') : '' }}</small>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $item->name ?? ($item->staff->name ?? '-') }}</div>
                                                <small class="text-muted">USMID: {{ $item->staff->usmid ?? 'N/A' }} | Mobile: {{ $item->staff->mobile ?? 'N/A' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border">{{ $item->station->name ?? '-' }}</span>
                                                <div class="small text-muted mt-1">{{ $item->designation->name ?? '-' }}</div>
                                            </td>
                                            <td style="white-space: normal; word-break: break-word;">
                                                <div class="small text-dark" style="line-height: 1.4;">{{ $item->description ?: '-' }}</div>
                                                @if($item->proficiency)
                                                    <span class="badge bg-light-primary text-primary mt-1" style="font-size: 11px;">{{ $item->proficiency->name }}</span>
                                                @endif
                                            </td>
                                            <td style="white-space: normal; word-break: break-word;">
                                                @if(!empty($item->ti_comment))
                                                    <div class="p-2 bg-light-info bg-opacity-25 rounded border-start border-3 border-info">
                                                        <span class="d-block text-dark small" style="line-height: 1.4;">{{ $item->ti_comment }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                            <td style="text-align: center; white-space: nowrap;">
                                                @if(!empty($item->file))
                                                    <a href="{{ asset('storage/' . $item->file) }}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-2 py-1 shadow-sm d-inline-flex align-items-center">
                                                        <i class="ti ti-paperclip me-1"></i>
                                                        <span class="fw-semibold">{{ strtoupper($item->file_type ?? 'Doc') }}</span>
                                                    </a>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                            <td style="text-align: center;">
                                                @if($item->status == 1)
                                                    <span class="badge badge-status-1 px-2 py-1"><i class="ti ti-send me-1"></i>Forwarded to Sr. DOM</span>
                                                @elseif($item->status == 2)
                                                    <span class="badge badge-status-2 px-2 py-1"><i class="ti ti-award me-1"></i>Awarded by Sr. DOM</span>
                                                @elseif($item->status == 3)
                                                    <span class="badge badge-status-3 px-2 py-1"><i class="ti ti-x me-1"></i>Rejected by Sr. DOM</span>
                                                @else
                                                    <span class="badge bg-light text-dark px-2 py-1">Forwarded</span>
                                                @endif
                                            </td>
                                            <td class="col-action" style="min-width: 120px;">
                                                <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 customModal text-nowrap"
                                                    data-size="lg"
                                                    data-url="{{ route('authority.award-nomination.nominate.show', encrypt($item->id)) }}"
                                                    data-title="{{ __('Award Nomination Dossier') }}" title="{{ __('View Dossier') }}">
                                                    <i class="ti ti-eye me-1"></i>{{ __('View Dossier') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5 text-muted">
                                                <i class="ti ti-send fs-1 text-muted d-block mb-2"></i>
                                                <h6 class="fw-bold text-dark">{{ __('No Forwarded Nominations Yet') }}</h6>
                                                <small class="text-muted">Nominations forwarded by you with recommendation will appear here.</small>
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
    @elseif(Auth::user()->isSrDom())
        {{-- ================= 3. SR. DOM VIEW: 3-Stage Decision Portal ================= --}}

        {{-- Top Action & Role Banner --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 my-3">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3 shadow-sm d-inline-flex align-items-center">
            <i class="ti ti-arrow-left me-1"></i> {{ __('Back to Dashboard') }}
        </a>
        @if (in_array(strtolower(Auth::user()->type ?? ''), ['super admin', 'owner', 'admin']))
            <span class="badge bg-light-primary text-primary px-3 py-2 rounded-pill fs-7 border border-primary">
                <i class="ti ti-shield-lock me-1"></i> {{ __('Admin Division Award Nomination & Authority Hub') }} ({{ $stats['total_nominations'] }})
            </span>
        @elseif(Auth::user()->isSrDom())
            <span class="badge bg-light-success text-success px-3 py-2 rounded-pill fs-7 border border-success">
                <i class="ti ti-certificate me-1"></i> {{ __('Sr. DOM Award Approval Queue') }}
            </span>
        @elseif(Auth::user()->isTiSafety())
            <span class="badge bg-light-info text-info px-3 py-2 rounded-pill fs-7 border border-info">
                <i class="ti ti-shield-check me-1"></i> {{ __('TI Safety Division Nomination Queue') }}
            </span>
        @else
            <span class="badge bg-light-primary text-primary px-3 py-2 rounded-pill fs-7 border border-primary">
                <i class="ti ti-user-check me-1"></i> {{ __('Viewing Your Personal Submissions') }} ({{ $stats['total_nominations'] }})
            </span>
        @endif
    </div>

    {{-- KPI Stat Cards (Sr. DOM & Reviewers) --}}
    <div class="row g-3 my-3">
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('authority.award-nomination.index', ['tab' => 'pending']) }}" class="text-decoration-none">
                <div class="card kpi-card h-100 mb-0">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted text-uppercase fw-semibold fs-7 d-block mb-1">{{ __('Total Nominations') }}</span>
                                <h4 class="mb-0 fw-bold text-dark">{{ $stats['total_nominations'] }}</h4>
                            </div>
                            <div class="kpi-icon bg-light-primary text-primary">
                                <i class="ti ti-award"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('authority.award-nomination.index', ['tab' => 'pending']) }}" class="text-decoration-none">
                <div class="card kpi-card h-100 mb-0">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted text-uppercase fw-semibold fs-7 d-block mb-1">{{ __('Forwarded Nominations') }}</span>
                                <h4 class="mb-0 fw-bold text-warning">{{ $stats['recommended'] }}</h4>
                            </div>
                            <div class="kpi-icon bg-light-warning text-warning">
                                <i class="ti ti-send"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('authority.award-nomination.index', ['tab' => 'approved']) }}" class="text-decoration-none">
                <div class="card kpi-card h-100 mb-0">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted text-uppercase fw-semibold fs-7 d-block mb-1">{{ __('Approved Nominations') }}</span>
                                <h4 class="mb-0 fw-bold text-success">{{ $stats['awarded'] }}</h4>
                            </div>
                            <div class="kpi-icon bg-light-success text-success">
                                <i class="ti ti-certificate"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6">
            <a href="{{ route('authority.award-nomination.index', ['tab' => 'rejected']) }}" class="text-decoration-none">
                <div class="card kpi-card h-100 mb-0">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted text-uppercase fw-semibold fs-7 d-block mb-1">{{ __('Rejected Nominations') }}</span>
                                <h4 class="mb-0 fw-bold text-danger">{{ $stats['rejected'] }}</h4>
                            </div>
                            <div class="kpi-icon bg-light-danger text-danger">
                                <i class="ti ti-circle-x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Main Container Card --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white border-bottom py-3 px-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <ul class="nav nav-pills srdom-nav gap-2 mb-0" id="awardNominationTabs" role="tablist">
                    {{-- Section 1: Forwarded Nominations --}}
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $activeTab === 'pending' ? 'active' : '' }}" id="tab-pending"
                            data-bs-toggle="pill" data-bs-target="#pane-pending" type="button" role="tab">
                            <i class="ti ti-send me-1"></i> {{ __('1. Forwarded Nominations') }}
                            <span class="badge {{ $activeTab === 'pending' ? 'bg-dark text-white' : 'bg-warning text-dark' }} rounded-pill ms-2">{{ $stats['recommended'] }}</span>
                        </button>
                    </li>

                    {{-- Section 2: Approved Nominations --}}
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $activeTab === 'approved' ? 'active' : '' }}" id="tab-approved"
                            data-bs-toggle="pill" data-bs-target="#pane-approved" type="button" role="tab">
                            <i class="ti ti-certificate me-1"></i> {{ __('2. Approved Nominations') }}
                            <span class="badge {{ $activeTab === 'approved' ? 'bg-white text-success' : 'bg-success text-white' }} rounded-pill ms-2">{{ $stats['awarded'] }}</span>
                        </button>
                    </li>

                    {{-- Section 3: Rejected Nominations --}}
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $activeTab === 'rejected' ? 'active' : '' }}" id="tab-rejected"
                            data-bs-toggle="pill" data-bs-target="#pane-rejected" type="button" role="tab">
                            <i class="ti ti-circle-x me-1"></i> {{ __('3. Rejected Nominations') }}
                            <span class="badge {{ $activeTab === 'rejected' ? 'bg-white text-danger' : 'bg-danger text-white' }} rounded-pill ms-2">{{ $stats['rejected'] }}</span>
                        </button>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    {{-- Button to Go Back to Dashboard --}}
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm d-inline-flex align-items-center">
                        <i class="ti ti-arrow-left me-1"></i>
                        {{ __('Back to Dashboard') }}
                    </a>

                    {{-- Button for Creating Award Nomination --}}
                    <a href="#" class="btn btn-primary btn-sm rounded-pill customModal px-3 shadow-sm"
                        data-size="lg"
                        data-url="{{ route('authority.award-nomination.nominate.create') }}"
                        data-title="{{ __('Create Award Nomination') }}">
                        <i class="ti ti-circle-plus align-text-bottom me-1"></i>
                        {{ __('Create Award Nomination') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            @php
                $hasStations = isset($stations) && $stations->isNotEmpty();
                $hasProficiencies = isset($proficiencies) && $proficiencies->isNotEmpty();
                if ($hasStations && $hasProficiencies) {
                    $filterColClass = 'col-md-4';
                } elseif ($hasStations || $hasProficiencies) {
                    $filterColClass = 'col-md-6';
                } else {
                    $filterColClass = 'col-12';
                }
            @endphp
            <div class="tab-content" id="awardNominationTabContent">

                {{-- ================= TAB 1: PENDING & FORWARDED BY TI ================= --}}
                <div class="tab-pane fade {{ $activeTab === 'pending' ? 'show active' : '' }}" id="pane-pending" role="tabpanel">

                    {{-- Filters --}}
                    <form method="GET" action="{{ route('authority.award-nomination.index') }}" class="mb-4">
                        <input type="hidden" name="tab" value="pending">
                        <div class="row g-2 align-items-end p-3 bg-light rounded-3 border">
                            @if($hasStations)
                                <div class="{{ $filterColClass }}">
                                    <label class="form-label text-muted small fw-semibold">{{ __('Station') }}</label>
                                    <div class="dropdown custom-filter-dropdown">
                                        <button class="form-select form-select-sm text-start bg-white shadow-none text-truncate" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" style="cursor: pointer;">
                                            <span class="dropdown-label">{{ request('station_id') && $stations->has(request('station_id')) ? $stations->get(request('station_id')) : __('All Stations') }}</span>
                                        </button>
                                        <div class="dropdown-menu p-1 shadow-lg border border-light-subtle rounded-3" style="width: 100%; min-width: 240px; max-height: 280px; overflow-y: auto; z-index: 1060;">
                                            <a class="dropdown-item py-1 px-3 small filter-opt-item {{ !request('station_id') ? 'active' : '' }}" href="javascript:void(0)" data-value="">{{ __('All Stations') }}</a>
                                            @foreach($stations as $sId => $sName)
                                                <a class="dropdown-item py-1 px-3 small filter-opt-item {{ request('station_id') == $sId ? 'active' : '' }}" href="javascript:void(0)" data-value="{{ $sId }}">{{ $sName }}</a>
                                            @endforeach
                                        </div>
                                        <input type="hidden" name="station_id" class="filter-hidden-val" value="{{ request('station_id') }}">
                                    </div>
                                </div>
                            @endif

                            @if($hasProficiencies)
                                <div class="{{ $filterColClass }}">
                                    <label class="form-label text-muted small fw-semibold">{{ __('Area of Proficiency') }}</label>
                                    <div class="dropdown custom-filter-dropdown">
                                        <button class="form-select form-select-sm text-start bg-white shadow-none text-truncate" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" style="cursor: pointer;">
                                            <span class="dropdown-label">{{ request('proficiency_id') && $proficiencies->has(request('proficiency_id')) ? $proficiencies->get(request('proficiency_id')) : __('All Proficiencies') }}</span>
                                        </button>
                                        <div class="dropdown-menu p-1 shadow-lg border border-light-subtle rounded-3" style="width: 100%; min-width: 260px; max-height: 280px; overflow-y: auto; z-index: 1060;">
                                            <a class="dropdown-item py-1 px-3 small filter-opt-item {{ !request('proficiency_id') ? 'active' : '' }}" href="javascript:void(0)" data-value="">{{ __('All Proficiencies') }}</a>
                                            @foreach($proficiencies as $pId => $pName)
                                                <a class="dropdown-item py-1 px-3 small filter-opt-item {{ request('proficiency_id') == $pId ? 'active' : '' }}" href="javascript:void(0)" data-value="{{ $pId }}">{{ $pName }}</a>
                                            @endforeach
                                        </div>
                                        <input type="hidden" name="proficiency_id" class="filter-hidden-val" value="{{ request('proficiency_id') }}">
                                    </div>
                                </div>
                            @endif

                            <div class="{{ $filterColClass }}">
                                <label class="form-label text-muted small fw-semibold">{{ __('Search') }}</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search staff, certificate, description...">
                                    <button type="submit" class="btn btn-primary"><i class="ti ti-search"></i></button>
                                    @if(request()->hasAny(['station_id', 'proficiency_id', 'search']))
                                        <a href="{{ route('authority.award-nomination.index', ['tab' => 'pending']) }}" class="btn btn-light" title="Reset Filters"><i class="ti ti-refresh"></i></a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 srdom-table" style="min-width: 1200px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 45px; text-align: center;">#</th>
                                    <th style="width: 110px;">{{ __('Date') }}</th>
                                    <th style="width: 180px;">{{ __('Nominee (Staff)') }}</th>
                                    <th style="width: 140px;">{{ __('Station & Desig') }}</th>
                                    <th style="min-width: 200px; max-width: 260px;">{{ __('Meritorious Work Description') }}</th>
                                    <th style="min-width: 240px; max-width: 300px;">{{ __('TI Recommendation Comment') }}</th>
                                    <th style="width: 120px; min-width: 120px; text-align: center;">{{ __('Document') }}</th>
                                    <th style="width: 130px; text-align: center;">{{ __('Status') }}</th>
                                    <th class="text-end" style="width: 190px; min-width: 190px;">{{ __('Final Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pendingNominations as $index => $item)
                                    <tr>
                                        <td class="fw-semibold text-muted text-center">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $item->date ? \Carbon\Carbon::parse($item->date)->format('d M, Y') : '-' }}</div>
                                            <small class="text-muted">TI Fwd: {{ $item->ti_forward_date ? \Carbon\Carbon::parse($item->ti_forward_date)->format('d M') : ($item->time ? \Carbon\Carbon::parse($item->time)->format('h:i A') : '-') }}</small>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $item->name ?? ($item->staff->name ?? '-') }}</div>
                                            <small class="text-muted">USMID: {{ $item->staff->usmid ?? 'N/A' }} | Mobile: {{ $item->staff->mobile ?? 'N/A' }}</small>
                                            @if(in_array(strtolower(Auth::user()->type ?? ''), ['super admin', 'owner', 'admin']) && $item->creator)
                                                <small class="text-primary d-block mt-1"><i class="ti ti-user me-1"></i>By: {{ $item->creator->name }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $item->station->name ?? '-' }}</span>
                                            <div class="small text-muted mt-1">{{ $item->designation->name ?? '-' }}</div>
                                        </td>
                                        <td style="min-width: 200px; max-width: 260px; white-space: normal; word-break: break-word;">
                                            <div class="small text-dark" style="word-break: break-word; line-height: 1.4;" title="{{ $item->description }}">
                                                {{ $item->description ?: '-' }}
                                            </div>
                                        </td>
                                        <td style="min-width: 240px; max-width: 300px; white-space: normal; word-break: break-word;">
                                            @if($item->status == 1 || !empty($item->ti_comment))
                                                <div class="p-2 bg-light rounded border-start border-3 border-info" style="word-break: break-word; white-space: normal; overflow: hidden;">
                                                    <p class="mb-0 small text-dark fst-italic" style="word-break: break-word; white-space: normal; line-height: 1.4;">"{{ $item->ti_comment ?: 'Recommended for Award' }}"</p>
                                                    <small class="text-muted d-block mt-1"><i class="ti ti-user me-1"></i>TI: {{ $item->ti->name ?? 'TI Safety' }}</small>
                                                </div>
                                            @else
                                                <span class="badge bg-light-warning text-warning border border-warning small">
                                                    <i class="ti ti-clock me-1"></i>Awaiting TI Review
                                                </span>
                                            @endif
                                        </td>
                                        <td style="width: 120px; min-width: 120px; text-align: center; white-space: nowrap;">
                                            @if(!empty($item->file))
                                                <a href="{{ asset('storage/' . $item->file) }}" target="_blank"
                                                   class="btn btn-sm btn-outline-info rounded-pill px-3 py-1 shadow-sm text-nowrap d-inline-flex align-items-center"
                                                   title="{{ __('View Attached Document') }}">
                                                    <i class="ti ti-paperclip me-1"></i>
                                                    <span class="fw-semibold">{{ strtoupper($item->file_type ?? 'File') }}</span>
                                                </a>
                                            @else
                                                <span class="badge bg-light text-muted border px-2 py-1"><i class="ti ti-file-off me-1"></i>No Doc</span>
                                            @endif
                                        </td>
                                        <td style="width: 130px; text-align: center;">
                                            @if($item->status == 0)
                                                <span class="badge badge-status-0 px-2 py-1"><i class="ti ti-clock me-1"></i>Pending</span>
                                            @elseif($item->status == 1)
                                                <span class="badge badge-status-1 px-2 py-1"><i class="ti ti-clipboard-check me-1"></i>TI Recommended</span>
                                            @endif
                                        </td>
                                        <td class="col-action" style="min-width: 190px;">
                                            @php
                                                $isUserAdmin = in_array(strtolower(Auth::user()->type ?? ''), ['super admin', 'owner', 'admin']);
                                                $isUserTi = Auth::user()->isTiSafety();
                                                $isUserSrDom = Auth::user()->isSrDom();
                                            @endphp

                                            <div class="d-inline-flex gap-1 align-items-center">
                                                {{-- Sr. DOM Action Button --}}
                                                @if($isUserSrDom && $item->status == 1)
                                                    <a href="#" class="btn btn-sm btn-success font-bold rounded-pill px-3 py-1 customModal shadow-sm"
                                                        data-size="md"
                                                        data-url="{{ route('authority.award-nomination.nominate.action', encrypt($item->id)) }}"
                                                        data-title="{{ __('Final Decision: Award Certificate') }}">
                                                        <i class="ti ti-certificate me-1"></i>{{ __('Grant Award / Decision') }}
                                                    </a>
                                                {{-- TI Safety Action Button --}}
                                                @elseif($isUserTi && ($item->status == 0 || $item->status == 1))
                                                    <a href="#" class="btn btn-sm btn-info text-white font-bold rounded-pill px-3 py-1 customModal shadow-sm"
                                                        data-size="md"
                                                        data-url="{{ route('authority.award-nomination.nominate.action', encrypt($item->id)) }}"
                                                        data-title="{{ __('Forward to Sr. DOM') }}">
                                                        <i class="ti ti-send me-1"></i>{{ __('Forward to Sr. DOM') }}
                                                    </a>
                                                {{-- Admin Review / Action Button --}}
                                                @elseif($isUserAdmin)
                                                    <a href="#" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1 customModal"
                                                        data-size="md"
                                                        data-url="{{ route('authority.award-nomination.nominate.action', encrypt($item->id)) }}"
                                                        data-title="{{ __('Review Nomination Status') }}">
                                                        <i class="ti ti-edit me-1"></i>{{ __('Action') }}
                                                    </a>
                                                @endif

                                                {{-- View Dossier Modal --}}
                                                <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1 customModal"
                                                    data-size="lg"
                                                    data-url="{{ route('authority.award-nomination.nominate.show', encrypt($item->id)) }}"
                                                    data-title="{{ __('Award Nomination Dossier') }}" title="{{ __('View Dossier') }}">
                                                    <i class="ti ti-eye"></i>
                                                </a>

                                                {{-- Delete (if allowed) --}}
                                                @if($isUserAdmin || ($item->created_by == Auth::id() && $item->status == 0))
                                                    {!! Form::open([
                                                        'method' => 'DELETE',
                                                        'route' => ['authority.award-nomination.nominate.destroy', encrypt($item->id)],
                                                        'style' => 'display:inline;',
                                                    ]) !!}
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1 confirm_dialog" title="{{ __('Delete') }}">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                    {!! Form::close() !!}
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5 text-muted">
                                            <i class="ti ti-circle-check fs-1 text-success d-block mb-2"></i>
                                            <h6 class="fw-bold text-dark">{{ __('No Pending Nominations Found') }}</h6>
                                            <small class="text-muted">All nominations in this queue have been processed.</small>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ================= TAB 2: APPROVED AWARDS ================= --}}
                <div class="tab-pane fade {{ $activeTab === 'approved' ? 'show active' : '' }}" id="pane-approved" role="tabpanel">

                    {{-- Filters --}}
                    <form method="GET" action="{{ route('authority.award-nomination.index') }}" class="mb-4">
                        <input type="hidden" name="tab" value="approved">
                        <div class="row g-2 align-items-end p-3 bg-light rounded-3 border">
                            @if($hasStations)
                                <div class="{{ $filterColClass }}">
                                    <label class="form-label text-muted small fw-semibold">{{ __('Station') }}</label>
                                    <div class="dropdown custom-filter-dropdown">
                                        <button class="form-select form-select-sm text-start bg-white shadow-none text-truncate" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" style="cursor: pointer;">
                                            <span class="dropdown-label">{{ request('station_id') && $stations->has(request('station_id')) ? $stations->get(request('station_id')) : __('All Stations') }}</span>
                                        </button>
                                        <div class="dropdown-menu p-1 shadow-lg border border-light-subtle rounded-3" style="width: 100%; min-width: 240px; max-height: 280px; overflow-y: auto; z-index: 1060;">
                                            <a class="dropdown-item py-1 px-3 small filter-opt-item {{ !request('station_id') ? 'active' : '' }}" href="javascript:void(0)" data-value="">{{ __('All Stations') }}</a>
                                            @foreach($stations as $sId => $sName)
                                                <a class="dropdown-item py-1 px-3 small filter-opt-item {{ request('station_id') == $sId ? 'active' : '' }}" href="javascript:void(0)" data-value="{{ $sId }}">{{ $sName }}</a>
                                            @endforeach
                                        </div>
                                        <input type="hidden" name="station_id" class="filter-hidden-val" value="{{ request('station_id') }}">
                                    </div>
                                </div>
                            @endif

                            @if($hasProficiencies)
                                <div class="{{ $filterColClass }}">
                                    <label class="form-label text-muted small fw-semibold">{{ __('Area of Proficiency') }}</label>
                                    <div class="dropdown custom-filter-dropdown">
                                        <button class="form-select form-select-sm text-start bg-white shadow-none text-truncate" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" style="cursor: pointer;">
                                            <span class="dropdown-label">{{ request('proficiency_id') && $proficiencies->has(request('proficiency_id')) ? $proficiencies->get(request('proficiency_id')) : __('All Proficiencies') }}</span>
                                        </button>
                                        <div class="dropdown-menu p-1 shadow-lg border border-light-subtle rounded-3" style="width: 100%; min-width: 260px; max-height: 280px; overflow-y: auto; z-index: 1060;">
                                            <a class="dropdown-item py-1 px-3 small filter-opt-item {{ !request('proficiency_id') ? 'active' : '' }}" href="javascript:void(0)" data-value="">{{ __('All Proficiencies') }}</a>
                                            @foreach($proficiencies as $pId => $pName)
                                                <a class="dropdown-item py-1 px-3 small filter-opt-item {{ request('proficiency_id') == $pId ? 'active' : '' }}" href="javascript:void(0)" data-value="{{ $pId }}">{{ $pName }}</a>
                                            @endforeach
                                        </div>
                                        <input type="hidden" name="proficiency_id" class="filter-hidden-val" value="{{ request('proficiency_id') }}">
                                    </div>
                                </div>
                            @endif

                            <div class="{{ $filterColClass }}">
                                <label class="form-label text-muted small fw-semibold">{{ __('Search') }}</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search staff, certificate, description...">
                                    <button type="submit" class="btn btn-primary"><i class="ti ti-search"></i></button>
                                    @if(request()->hasAny(['station_id', 'proficiency_id', 'search']))
                                        <a href="{{ route('authority.award-nomination.index', ['tab' => 'approved']) }}" class="btn btn-light" title="Reset Filters"><i class="ti ti-refresh"></i></a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 srdom-table" style="min-width: 1200px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 45px; text-align: center;">#</th>
                                    <th style="width: 110px;">{{ __('Award Date') }}</th>
                                    <th style="width: 180px;">{{ __('Nominee (Staff)') }}</th>
                                    <th style="width: 140px;">{{ __('Station & Desig') }}</th>
                                    <th style="min-width: 200px; max-width: 260px;">{{ __('Meritorious Work Description') }}</th>
                                    <th style="width: 160px;">{{ __('Certificate Number') }}</th>
                                    <th style="min-width: 220px; max-width: 280px;">{{ __('Sr. DOM Remarks / Order') }}</th>
                                    <th style="width: 100px; text-align: center;">{{ __('Document') }}</th>
                                    <th class="text-end" style="width: 160px; min-width: 160px;">{{ __('Certificate / Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($approvedNominations as $index => $item)
                                    <tr>
                                        <td class="fw-semibold text-muted text-center">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $item->checked_date ? \Carbon\Carbon::parse($item->checked_date)->format('d M, Y') : ($item->updated_at ? $item->updated_at->format('d M, Y') : '-') }}</div>
                                            <small class="text-muted">{{ $item->checked_time ? \Carbon\Carbon::parse($item->checked_time)->format('h:i A') : '' }}</small>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $item->name ?? ($item->staff->name ?? '-') }}</div>
                                            <small class="text-muted">USMID: {{ $item->staff->usmid ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $item->station->name ?? '-' }}</span>
                                            <div class="small text-muted mt-1">{{ $item->designation->name ?? '-' }}</div>
                                        </td>
                                        <td style="min-width: 200px; max-width: 260px; white-space: normal; word-break: break-word;">
                                            <div class="small text-dark" style="word-break: break-word; line-height: 1.4;" title="{{ $item->description }}">
                                                {{ $item->description ?: '-' }}
                                            </div>
                                        </td>
                                        <td>
                                            @if(!empty($item->certificate_no))
                                                <span class="badge bg-light-success text-success border border-success fw-bold font-monospace px-2 py-1">
                                                    <i class="ti ti-certificate me-1"></i>{{ $item->certificate_no }}
                                                </span>
                                            @else
                                                <span class="badge bg-light text-muted border">Pending No.</span>
                                            @endif
                                        </td>
                                        <td style="min-width: 220px; max-width: 280px; white-space: normal; word-break: break-word;">
                                            <div class="p-2 bg-light-success bg-opacity-25 rounded border-start border-3 border-success" style="word-break: break-word; white-space: normal;">
                                                <strong class="d-block text-dark small" style="line-height: 1.4;">{{ $item->dom_comment ?: 'Sanctioned Merit Award + Certificate of Excellence by Sr. DOM' }}</strong>
                                                <small class="text-muted d-block mt-1"><i class="ti ti-user-check me-1"></i>Order by: {{ $item->srdom->name ?? 'Sr. DOM' }}</small>
                                            </div>
                                        </td>
                                        <td style="width: 100px; text-align: center; white-space: nowrap;">
                                            @if(!empty($item->file))
                                                <a href="{{ asset('storage/' . $item->file) }}" target="_blank"
                                                   class="btn btn-sm btn-outline-info rounded-pill px-2 py-1 shadow-sm text-nowrap d-inline-flex align-items-center"
                                                   title="{{ __('View Attached Document') }}">
                                                    <i class="ti ti-paperclip me-1"></i>
                                                    <span class="fw-semibold">{{ strtoupper($item->file_type ?? 'Doc') }}</span>
                                                </a>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td class="col-action" style="min-width: 160px;">
                                            <div class="d-inline-flex gap-1 align-items-center">
                                                <a href="{{ route('authority.award-nomination.nominate.certificate', encrypt($item->id)) }}" target="_blank"
                                                    class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 shadow-sm d-inline-flex align-items-center text-nowrap"
                                                    title="{{ __('Print Certificate') }}">
                                                    <i class="ti ti-printer me-1"></i>{{ __('Print Certificate') }}
                                                </a>
                                                <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1 customModal"
                                                    data-size="lg"
                                                    data-url="{{ route('authority.award-nomination.nominate.show', encrypt($item->id)) }}"
                                                    data-title="{{ __('Award Nomination Dossier') }}" title="{{ __('View Dossier') }}">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                                @if(in_array(strtolower(Auth::user()->type ?? ''), ['super admin', 'owner', 'admin']))
                                                    {!! Form::open([
                                                        'method' => 'DELETE',
                                                        'route' => ['authority.award-nomination.nominate.destroy', encrypt($item->id)],
                                                        'style' => 'display:inline;',
                                                    ]) !!}
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1 confirm_dialog" title="{{ __('Delete') }}">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                    {!! Form::close() !!}
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5 text-muted">
                                            <i class="ti ti-award fs-1 text-muted d-block mb-2"></i>
                                            <h6 class="fw-bold text-dark">{{ __('No Approved Awards Found') }}</h6>
                                            <small class="text-muted">Approved nominations and awarded certificates will appear here.</small>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ================= TAB 3: REJECTED NOMINATIONS ================= --}}
                <div class="tab-pane fade {{ $activeTab === 'rejected' ? 'show active' : '' }}" id="pane-rejected" role="tabpanel">

                    {{-- Filters --}}
                    <form method="GET" action="{{ route('authority.award-nomination.index') }}" class="mb-4">
                        <input type="hidden" name="tab" value="rejected">
                        <div class="row g-2 align-items-end p-3 bg-light rounded-3 border">
                            @if($hasStations)
                                <div class="{{ $filterColClass }}">
                                    <label class="form-label text-muted small fw-semibold">{{ __('Station') }}</label>
                                    <div class="dropdown custom-filter-dropdown">
                                        <button class="form-select form-select-sm text-start bg-white shadow-none text-truncate" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" style="cursor: pointer;">
                                            <span class="dropdown-label">{{ request('station_id') && $stations->has(request('station_id')) ? $stations->get(request('station_id')) : __('All Stations') }}</span>
                                        </button>
                                        <div class="dropdown-menu p-1 shadow-lg border border-light-subtle rounded-3" style="width: 100%; min-width: 240px; max-height: 280px; overflow-y: auto; z-index: 1060;">
                                            <a class="dropdown-item py-1 px-3 small filter-opt-item {{ !request('station_id') ? 'active' : '' }}" href="javascript:void(0)" data-value="">{{ __('All Stations') }}</a>
                                            @foreach($stations as $sId => $sName)
                                                <a class="dropdown-item py-1 px-3 small filter-opt-item {{ request('station_id') == $sId ? 'active' : '' }}" href="javascript:void(0)" data-value="{{ $sId }}">{{ $sName }}</a>
                                            @endforeach
                                        </div>
                                        <input type="hidden" name="station_id" class="filter-hidden-val" value="{{ request('station_id') }}">
                                    </div>
                                </div>
                            @endif

                            @if($hasProficiencies)
                                <div class="{{ $filterColClass }}">
                                    <label class="form-label text-muted small fw-semibold">{{ __('Area of Proficiency') }}</label>
                                    <div class="dropdown custom-filter-dropdown">
                                        <button class="form-select form-select-sm text-start bg-white shadow-none text-truncate" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" style="cursor: pointer;">
                                            <span class="dropdown-label">{{ request('proficiency_id') && $proficiencies->has(request('proficiency_id')) ? $proficiencies->get(request('proficiency_id')) : __('All Proficiencies') }}</span>
                                        </button>
                                        <div class="dropdown-menu p-1 shadow-lg border border-light-subtle rounded-3" style="width: 100%; min-width: 260px; max-height: 280px; overflow-y: auto; z-index: 1060;">
                                            <a class="dropdown-item py-1 px-3 small filter-opt-item {{ !request('proficiency_id') ? 'active' : '' }}" href="javascript:void(0)" data-value="">{{ __('All Proficiencies') }}</a>
                                            @foreach($proficiencies as $pId => $pName)
                                                <a class="dropdown-item py-1 px-3 small filter-opt-item {{ request('proficiency_id') == $pId ? 'active' : '' }}" href="javascript:void(0)" data-value="{{ $pId }}">{{ $pName }}</a>
                                            @endforeach
                                        </div>
                                        <input type="hidden" name="proficiency_id" class="filter-hidden-val" value="{{ request('proficiency_id') }}">
                                    </div>
                                </div>
                            @endif

                            <div class="{{ $filterColClass }}">
                                <label class="form-label text-muted small fw-semibold">{{ __('Search') }}</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search staff, certificate, description...">
                                    <button type="submit" class="btn btn-primary"><i class="ti ti-search"></i></button>
                                    @if(request()->hasAny(['station_id', 'proficiency_id', 'search']))
                                        <a href="{{ route('authority.award-nomination.index', ['tab' => 'rejected']) }}" class="btn btn-light" title="Reset Filters"><i class="ti ti-refresh"></i></a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 srdom-table" style="min-width: 1200px;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 45px; text-align: center;">#</th>
                                    <th style="width: 110px;">{{ __('Date Checked') }}</th>
                                    <th style="width: 180px;">{{ __('Nominee (Staff)') }}</th>
                                    <th style="width: 140px;">{{ __('Station & Desig') }}</th>
                                    <th style="min-width: 200px; max-width: 260px;">{{ __('Meritorious Work Description') }}</th>
                                    <th style="width: 110px; text-align: center;">{{ __('Document') }}</th>
                                    <th style="min-width: 260px; max-width: 340px;">{{ __('Reason for Rejection / Remarks') }}</th>
                                    <th class="text-end" style="width: 140px; min-width: 140px;">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rejectedNominations as $index => $item)
                                    <tr>
                                        <td class="fw-semibold text-muted text-center">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $item->checked_date ? \Carbon\Carbon::parse($item->checked_date)->format('d M, Y') : ($item->updated_at ? $item->updated_at->format('d M, Y') : '-') }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $item->name ?? ($item->staff->name ?? '-') }}</div>
                                            <small class="text-muted">USMID: {{ $item->staff->usmid ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">{{ $item->station->name ?? '-' }}</span>
                                            <div class="small text-muted mt-1">{{ $item->designation->name ?? '-' }}</div>
                                        </td>
                                        <td style="min-width: 180px; max-width: 240px; white-space: normal; word-break: break-word;">
                                            <div class="small text-muted" style="word-break: break-word; line-height: 1.4;" title="{{ $item->description }}">
                                                {{ $item->description ?: '-' }}
                                            </div>
                                        </td>
                                        <td style="width: 110px; text-align: center; white-space: nowrap;">
                                            @if(!empty($item->file))
                                                <a href="{{ asset('storage/' . $item->file) }}" target="_blank"
                                                   class="btn btn-sm btn-outline-info rounded-pill px-2 py-1 shadow-sm text-nowrap d-inline-flex align-items-center"
                                                   title="{{ __('View Attached Document') }}">
                                                    <i class="ti ti-paperclip me-1"></i>
                                                    <span class="fw-semibold">{{ strtoupper($item->file_type ?? 'Doc') }}</span>
                                                </a>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td style="min-width: 260px; max-width: 340px; white-space: normal; word-break: break-word;">
                                            <div class="rejection-box">
                                                <span class="badge bg-danger text-white mb-1" style="font-size: 10px; padding: 3px 8px; font-weight: 600;">REJECTED</span>
                                                <p class="mb-0 fw-semibold">{{ $item->dom_comment ?: 'Nomination rejected' }}</p>
                                            </div>
                                        </td>
                                        <td class="col-action" style="min-width: 140px;">
                                            <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 customModal text-nowrap"
                                                data-size="lg"
                                                data-url="{{ route('authority.award-nomination.nominate.show', encrypt($item->id)) }}"
                                                data-title="{{ __('Nomination Details') }}">
                                                <i class="ti ti-eye me-1"></i>{{ __('View Details') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            <i class="ti ti-thumb-up fs-1 text-success d-block mb-2"></i>
                                            <h6 class="fw-bold text-dark">{{ __('No Rejected Nominations') }}</h6>
                                            <small class="text-muted">Nominations marked as rejected will appear here.</small>
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
    @else
        {{-- ================= 4. REGULAR AUTHORIZED USER VIEW: My Personal Award Nominations & Live Tracking ================= --}}

        {{-- Top Action & Role Banner --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 my-3">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3 shadow-sm d-inline-flex align-items-center">
                <i class="ti ti-arrow-left me-1"></i> {{ __('Back to Dashboard') }}
            </a>

            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light-primary text-primary px-3 py-2 rounded-pill fs-7 border border-primary">
                    <i class="ti ti-user-check me-1"></i> {{ __('My Award Nominations Tracker') }}
                </span>

                <a href="#"
                   class="btn btn-warning text-dark font-bold px-3 py-2 rounded-pill customModal d-inline-flex align-items-center shadow-sm"
                   data-size="lg"
                   data-url="{{ route('authority.award-nomination.nominate.create') }}"
                   data-title="{{ __('Create Award Nomination') }}">
                    <i class="ti ti-circle-plus me-1 font-bold"></i>
                    {{ __('Create Award Nomination') }}
                </a>
            </div>
        </div>

        {{-- Scoped KPI Cards (Only showing counts of this user's nominations) --}}
        <div class="row g-3 my-3">
            <div class="col-md">
                <div class="card kpi-card h-100 mb-0 shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted text-uppercase fw-semibold fs-7 d-block mb-1">{{ __('My Total Submissions') }}</span>
                                <h4 class="mb-0 fw-bold text-dark">{{ $stats['total_nominations'] }}</h4>
                            </div>
                            <div class="kpi-icon bg-light-primary text-primary">
                                <i class="ti ti-award"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md">
                <div class="card kpi-card h-100 mb-0 shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted text-uppercase fw-semibold fs-7 d-block mb-1">{{ __('Under Review (TI Safety)') }}</span>
                                <h4 class="mb-0 fw-bold text-warning">{{ $stats['pending'] }}</h4>
                            </div>
                            <div class="kpi-icon bg-light-warning text-warning">
                                <i class="ti ti-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md">
                <div class="card kpi-card h-100 mb-0 shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted text-uppercase fw-semibold fs-7 d-block mb-1">{{ __('Forwarded to Sr. DOM') }}</span>
                                <h4 class="mb-0 fw-bold text-info">{{ $stats['forwarded'] }}</h4>
                            </div>
                            <div class="kpi-icon bg-light-info text-info">
                                <i class="ti ti-send"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md">
                <div class="card kpi-card h-100 mb-0 shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted text-uppercase fw-semibold fs-7 d-block mb-1">{{ __('Award Approved') }}</span>
                                <h4 class="mb-0 fw-bold text-success">{{ $stats['awarded'] }}</h4>
                            </div>
                            <div class="kpi-icon bg-light-success text-success">
                                <i class="ti ti-trophy"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md">
                <div class="card kpi-card h-100 mb-0 shadow-sm border-0">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted text-uppercase fw-semibold fs-7 d-block mb-1">{{ __('Rejected / Returned') }}</span>
                                <h4 class="mb-0 fw-bold text-danger">{{ $stats['rejected'] }}</h4>
                            </div>
                            <div class="kpi-icon bg-light-danger text-danger">
                                <i class="ti ti-x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Personal Nominations Table --}}
        <div class="card border-0 shadow-sm rounded-4 mt-3">
            <div class="card-header bg-white py-3 px-4 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="ti ti-list-details me-2 text-primary"></i>
                        {{ __('My Submitted Award Nominations') }}
                    </h5>
                    <small class="text-muted">{{ __('Track the real-time status of nominations submitted by you for meritorious safety and performance work.') }}</small>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table srdom-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th style="width: 140px;">{{ __('Date & Time') }}</th>
                                <th style="min-width: 180px;">{{ __('Nominee') }}</th>
                                <th style="min-width: 160px;">{{ __('Station & Desig') }}</th>
                                <th style="min-width: 260px;">{{ __('Meritorious Work Description') }}</th>
                                <th style="min-width: 220px;">{{ __('Current Live Stage / Tracker') }}</th>
                                <th style="width: 90px; text-align: center;">{{ __('Proof') }}</th>
                                <th style="width: 160px; text-align: right;">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($awardNominations as $idx => $item)
                                <tr>
                                    <td class="text-muted fw-bold">{{ $idx + 1 }}</td>
                                    <td>
                                        <strong class="d-block text-dark">{{ !empty($item->date) ? \Carbon\Carbon::parse($item->date)->format('d M, Y') : '-' }}</strong>
                                        <small class="text-muted">{{ !empty($item->time) ? \Carbon\Carbon::parse($item->time)->format('h:i A') : '' }}</small>
                                    </td>
                                    <td>
                                        <strong class="d-block text-dark">{{ $item->name ?? ($item->staff->name ?? 'Staff') }}</strong>
                                        <small class="text-muted">USMID: {{ $item->staff->usmid ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1 mb-1 d-inline-block">{{ $item->station->name ?? 'Station' }}</span>
                                        <small class="d-block text-muted">{{ $item->designation->name ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <div style="max-width: 320px; white-space: normal; line-height: 1.45;">
                                            <span class="badge bg-light-primary text-primary border border-primary px-2 py-1 mb-1 d-inline-block" style="font-size: 11px;">
                                                <i class="ti ti-star me-1"></i>{{ $item->proficiency->name ?? 'Merit Work' }}
                                            </span>
                                            <p class="mb-0 text-dark small" style="word-break: break-word;">{{ $item->description }}</p>
                                        </div>
                                    </td>
                                    <td>
                                        @if($item->status == 0)
                                            <span class="badge bg-light-warning text-warning border border-warning px-3 py-2 rounded-pill fw-bold">
                                                <i class="ti ti-clock me-1"></i> {{ __('Under Review by TI Safety') }}
                                            </span>
                                            <small class="text-muted d-block mt-1">Pending review & site verification by TI Safety.</small>
                                        @elseif($item->status == 1)
                                            <span class="badge bg-light-info text-info border border-info px-3 py-2 rounded-pill fw-bold">
                                                <i class="ti ti-send me-1"></i> {{ __('Forwarded to Sr. DOM') }}
                                            </span>
                                            <div class="p-2 bg-light-info bg-opacity-25 rounded border-start border-3 border-info mt-1 small">
                                                <strong>TI Recommendation:</strong> {{ $item->ti_comment ?: 'Recommended' }}
                                            </div>
                                        @elseif($item->status == 2)
                                            <span class="badge bg-light-success text-success border border-success px-3 py-2 rounded-pill fw-bold">
                                                <i class="ti ti-trophy me-1"></i> {{ __('Award Approved by Sr. DOM') }}
                                            </span>
                                            <div class="p-2 bg-light-success bg-opacity-25 rounded border-start border-3 border-success mt-1 small">
                                                <strong>Order:</strong> {{ $item->dom_comment ?: 'Merit Award Approved' }}
                                                @if(!empty($item->certificate_no))
                                                    <span class="badge bg-success text-white mt-1 d-inline-block font-monospace"><i class="ti ti-certificate me-1"></i>{{ $item->certificate_no }}</span>
                                                @endif
                                            </div>
                                        @elseif($item->status == 3)
                                            <span class="badge bg-light-danger text-danger border border-danger px-3 py-2 rounded-pill fw-bold">
                                                <i class="ti ti-x me-1"></i> {{ __('Not Approved') }}
                                            </span>
                                            <div class="p-2 bg-light-danger bg-opacity-25 rounded border-start border-3 border-danger mt-1 small text-danger">
                                                <strong>Reason:</strong> {{ $item->dom_comment ?: 'Nomination was not approved.' }}
                                            </div>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        @if(!empty($item->file))
                                            <a href="{{ asset('storage/' . $item->file) }}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-2 py-1 shadow-sm d-inline-flex align-items-center" title="View Document">
                                                <i class="ti ti-paperclip me-1"></i> {{ strtoupper($item->file_type ?? 'PDF') }}
                                            </a>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        @if($item->status == 2)
                                            <a href="{{ route('authority.award-nomination.nominate.certificate', encrypt($item->id)) }}" target="_blank" class="btn btn-sm btn-success rounded-pill px-3 py-1 shadow-sm d-inline-flex align-items-center">
                                                <i class="ti ti-certificate me-1"></i> {{ __('Certificate') }}
                                            </a>
                                        @endif
                                        <a href="#" class="btn btn-sm btn-outline-secondary rounded-pill px-2 py-1 customModal ms-1"
                                           data-size="lg"
                                           data-url="{{ route('authority.award-nomination.nominate.show', encrypt($item->id)) }}"
                                           data-title="{{ __('Award Nomination Dossier') }}" title="View Details">
                                            <i class="ti ti-eye me-1"></i> {{ __('Details') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="py-4">
                                            <i class="ti ti-award fs-1 text-muted d-block mb-3" style="font-size: 48px;"></i>
                                            <h5 class="fw-bold text-dark mb-1">{{ __('No Award Nominations Submitted Yet') }}</h5>
                                            <p class="text-muted mb-3">{{ __('You have not submitted any award nominations yet. Click below to submit your first nomination.') }}</p>
                                            <a href="#" class="btn btn-primary rounded-pill px-4 py-2 customModal shadow-sm"
                                               data-size="lg"
                                               data-url="{{ route('authority.award-nomination.nominate.create') }}"
                                               data-title="{{ __('Create Award Nomination') }}">
                                                <i class="ti ti-circle-plus me-1 font-bold"></i> {{ __('Create Award Nomination') }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endif
@endsection

@push('script-page')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle clicking an option item
        document.querySelectorAll('.custom-filter-dropdown .filter-opt-item').forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                var val = this.getAttribute('data-value') || '';
                var dropdown = this.closest('.custom-filter-dropdown');
                var hiddenInput = dropdown.querySelector('.filter-hidden-val');
                var form = dropdown.closest('form');
                if (hiddenInput && form) {
                    hiddenInput.value = val;
                    form.submit();
                }
            });
        });

        // Quick jump to option by typing first letter while dropdown is open
        document.querySelectorAll('.custom-filter-dropdown').forEach(function(dropdown) {
            dropdown.addEventListener('keydown', function(e) {
                if (e.key && e.key.length === 1 && /[a-zA-Z0-9]/.test(e.key)) {
                    var char = e.key.toLowerCase();
                    var menu = this.querySelector('.dropdown-menu');
                    if (menu) {
                        var items = menu.querySelectorAll('.filter-opt-item');
                        for (var i = 0; i < items.length; i++) {
                            if (items[i].textContent.trim().toLowerCase().startsWith(char)) {
                                items[i].scrollIntoView({ block: 'nearest' });
                                items[i].focus();
                                break;
                            }
                        }
                    }
                }
            });
        });
    });
</script>
@endpush
