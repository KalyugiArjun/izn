@extends('layouts.app')
@section('page-title')
    <a href="{{ route('master.ti_daily_inspection.index') }}" class="btn btn-sm btn-outline-secondary me-2"><i class="ti ti-arrow-left"></i></a>{{ __('Inspection Details') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Master') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('master.ti_daily_inspection.index') }}">{{ __('TI Daily Inspiration Data') }}</a></li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Details') }}</li>
@endsection
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-primary text-white py-3 rounded-top-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white"><i class="ti ti-clipboard-list me-2"></i>{{ __('TI Daily Inspiration Record') }} #{{ $inspection->id }}</h5>
                    <a href="{{ route('master.ti_daily_inspection.index') }}" class="btn btn-sm btn-light-secondary"><i class="ti ti-arrow-left"></i> Back to List</a>
                </div>
                <div class="card-body p-4">
                    <!-- Basic Info -->
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3"><i class="ti ti-info-circle me-1"></i>{{ __('Basic Information') }}</h6>
                        <table class="table table-striped table-bordered">
                            <tr>
                                <th style="width:30%;">{{ __('Date of Inspection') }}</th>
                                <td>{{ \Carbon\Carbon::parse($inspection->date_of_inspection)->format('d-m-Y') }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Submitted By') }}</th>
                                <td>{{ $inspection->employee ? $inspection->employee->name . ' (' . $inspection->employee->mobile . ')' : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Designation') }}</th>
                                <td>{{ $inspection->designation ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Movement') }}</th>
                                <td>{{ $inspection->movement ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Type of Inspection') }}</th>
                                <td>{{ $inspection->type_of_inspection ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Day / Night') }}</th>
                                <td>
                                    @if(strtoupper($inspection->day_night) === 'DAY')
                                        <span class="badge bg-warning text-dark">{{ $inspection->day_night }}</span>
                                    @else
                                        <span class="badge bg-dark text-white">{{ $inspection->day_night }}</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Ambush Check Details -->
                    @if($inspection->type_of_ambush_check || $inspection->station_yard)
                        <div class="mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3"><i class="ti ti-eye me-1"></i>{{ __('Ambush Check Details') }}</h6>
                            <table class="table table-striped table-bordered">
                                @if($inspection->type_of_ambush_check)
                                    <tr>
                                        <th style="width:30%;">{{ __('Type of Ambush Check') }}</th>
                                        <td>{{ $inspection->type_of_ambush_check }}</td>
                                    </tr>
                                @endif
                                @if($inspection->station_yard)
                                    <tr>
                                        <th style="width:30%;">{{ __('Station / Yard') }}</th>
                                        <td>{{ $inspection->station_yard }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    @endif

                    <!-- Inspection Specific Details -->
                    @if($inspection->level_crossing_no || $inspection->train_number || $inspection->section || $inspection->line_number_load_stable || $inspection->type_of_load_load_stable || $inspection->t806_number || $inspection->shunting_observation_of)
                        <div class="mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3"><i class="ti ti-settings me-1"></i>{{ __('Inspection Specific Details') }}</h6>
                            <table class="table table-striped table-bordered">
                                @if($inspection->level_crossing_no)
                                    <tr>
                                        <th style="width:30%;">{{ __('Level Crossing No.') }}</th>
                                        <td>{{ $inspection->level_crossing_no }}</td>
                                    </tr>
                                @endif
                                @if($inspection->train_number)
                                    <tr>
                                        <th>{{ __('Train Number') }}</th>
                                        <td>{{ $inspection->train_number }}</td>
                                    </tr>
                                @endif
                                @if($inspection->section)
                                    <tr>
                                        <th>{{ __('Section') }}</th>
                                        <td>{{ $inspection->section }}</td>
                                    </tr>
                                @endif
                                @if($inspection->line_number_load_stable)
                                    <tr>
                                        <th>{{ __('Line Number (Load Stable)') }}</th>
                                        <td>{{ $inspection->line_number_load_stable }}</td>
                                    </tr>
                                @endif
                                @if($inspection->type_of_load_load_stable)
                                    <tr>
                                        <th>{{ __('Type of Load (Load Stable)') }}</th>
                                        <td>{{ $inspection->type_of_load_load_stable }}</td>
                                    </tr>
                                @endif
                                @if($inspection->t806_number)
                                    <tr>
                                        <th>{{ __('T/806 Number (Shunting)') }}</th>
                                        <td>{{ $inspection->t806_number }}</td>
                                    </tr>
                                @endif
                                @if($inspection->shunting_observation_of)
                                    <tr>
                                        <th>{{ __('Shunting Observation Of') }}</th>
                                        <td>{{ $inspection->shunting_observation_of }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    @endif

                    <!-- Deficiencies & Actions -->
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3"><i class="ti ti-alert-triangle me-1"></i>{{ __('Deficiencies & Action') }}</h6>
                        <table class="table table-striped table-bordered">
                            <tr>
                                <th style="width:30%;">{{ __('Major Deficiencies Observed') }}</th>
                                <td>{{ $inspection->major_deficiencies ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Action Taken') }}</th>
                                <td>{{ $inspection->action_taken ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Remark') }}</th>
                                <td>{{ $inspection->remark ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
