@extends('layouts.app')
@section('page-title')
    <a href="{{ route('master.inspection.index') }}" class="btn btn-sm btn-outline-secondary me-2"><i class="ti ti-arrow-left"></i></a>{{ __('Inspection Details') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Master') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('master.inspection.index') }}">{{ __('Inspection List') }}</a></li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Details') }}</li>
@endsection
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-primary text-white py-3 rounded-top-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white"><i class="ti ti-clipboard-list me-2"></i>{{ __('Inspection Record') }} #{{ $inspection->id }}</h5>
                    <a href="{{ route('master.inspection.index') }}" class="btn btn-sm btn-light-secondary"><i class="ti ti-arrow-left"></i> Back to List</a>
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
                                <th>{{ __('Location') }}</th>
                                <td>{{ $inspection->location ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Name of the Officer') }}</th>
                                <td>{{ $inspection->officer_name }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Designation') }}</th>
                                <td>{{ $inspection->designation }}</td>
                            </tr>
                            <tr>
                                <th>{{ __('Movement Type') }}</th>
                                <td><span class="badge bg-primary">{{ $inspection->movement_type }}</span></td>
                            </tr>
                        </table>
                    </div>

                    <!-- CONDITIONAL DETAILS -->

                    <!-- 1. REGULAR INSPECTION DETAILS -->
                    @if($inspection->movement_type === 'REGULAR INSPECTION' && $inspection->regularInspection)
                        <div class="mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3"><i class="ti ti-checkbox me-1"></i>{{ __('Regular Inspection Details') }}</h6>
                            <table class="table table-striped table-bordered">
                                <tr>
                                    <th style="width:30%;">{{ __('Day / Night') }}</th>
                                    <td>{{ $inspection->regularInspection->day_night }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Type of Inspection') }}</th>
                                    <td>{{ $inspection->regularInspection->type_of_inspection }}</td>
                                </tr>
                                @if($inspection->regularInspection->station_name)
                                    <tr>
                                        <th>{{ __('Station Name') }}</th>
                                        <td>{{ $inspection->regularInspection->station_name }}</td>
                                    </tr>
                                @endif
                                @if($inspection->regularInspection->lc_gate_no)
                                    <tr>
                                        <th>{{ __('LC Gate No') }}</th>
                                        <td>{{ $inspection->regularInspection->lc_gate_no }}</td>
                                    </tr>
                                @endif
                                @if($inspection->regularInspection->gate_type)
                                    <tr>
                                        <th>{{ __('Gate Type') }}</th>
                                        <td>{{ $inspection->regularInspection->gate_type }}</td>
                                    </tr>
                                @endif
                                @if($inspection->regularInspection->block_section)
                                    <tr>
                                        <th>{{ __('Block Section') }}</th>
                                        <td>{{ $inspection->regularInspection->block_section }}</td>
                                    </tr>
                                @endif
                                @if($inspection->regularInspection->train_no)
                                    <tr>
                                        <th>{{ __('Train No') }}</th>
                                        <td>{{ $inspection->regularInspection->train_no }}</td>
                                    </tr>
                                @endif
                                @if($inspection->regularInspection->train_type)
                                    <tr>
                                        <th>{{ __('Train Type') }}</th>
                                        <td>{{ $inspection->regularInspection->train_type }}</td>
                                    </tr>
                                @endif
                                @if($inspection->regularInspection->lobby_location)
                                    <tr>
                                        <th>{{ __('Lobby Location') }}</th>
                                        <td>{{ $inspection->regularInspection->lobby_location }}</td>
                                    </tr>
                                @endif
                                @if($inspection->regularInspection->running_room_location)
                                    <tr>
                                        <th>{{ __('Running Room Location') }}</th>
                                        <td>{{ $inspection->regularInspection->running_room_location }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>{{ __('Major Deficiency Found?') }}</th>
                                    <td>
                                        @if($inspection->regularInspection->is_deficient)
                                            <span class="badge bg-danger">Yes</span>
                                        @else
                                            <span class="badge bg-success">No</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($inspection->regularInspection->is_deficient)
                                    <tr>
                                        <th>{{ __('Deficiency Description') }}</th>
                                        <td>{{ $inspection->regularInspection->deficiency_text }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    @endif

                    <!-- 2. AMBUSH CHECK DETAILS -->
                    @if($inspection->movement_type === 'AMBUSH CHECK' && $inspection->ambushInspection)
                        <div class="mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3"><i class="ti ti-eye me-1"></i>{{ __('Ambush Check Details') }}</h6>
                            <table class="table table-striped table-bordered">
                                <tr>
                                    <th style="width:30%;">{{ __('Station / Place') }}</th>
                                    <td>{{ $inspection->ambushInspection->station_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Timing') }}</th>
                                    <td>{{ $inspection->ambushInspection->from_time }} to {{ $inspection->ambushInspection->to_time }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Day / Night') }}</th>
                                    <td>{{ $inspection->ambushInspection->day_night }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Type of Ambush Check') }}</th>
                                    <td>{{ $inspection->ambushInspection->type_of_ambush }}</td>
                                </tr>

                                <!-- Shunting Specific -->
                                @if($inspection->ambushInspection->type_of_ambush === 'Shunting')
                                    <tr>
                                        <th>{{ __('Shunting Observation Of') }}</th>
                                        <td>{{ $inspection->ambushInspection->shunting_observation }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('T/806 No') }}</th>
                                        <td>{{ $inspection->ambushInspection->t_806_no }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Shunting Staff Designation') }}</th>
                                        <td>{{ $inspection->ambushInspection->shunting_staff_designation }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Shunting Staff Name') }}</th>
                                        <td>{{ $inspection->ambushInspection->shunting_staff_name }}</td>
                                    </tr>
                                @endif

                                <!-- Load Stable Specific -->
                                @if($inspection->ambushInspection->type_of_ambush === 'Load Stable')
                                    <tr>
                                        <th>{{ __('Load Type') }}</th>
                                        <td>{{ $inspection->ambushInspection->load_type }}</td>
                                    </tr>
                                    @if($inspection->ambushInspection->loco_engine_type)
                                        <tr>
                                            <th>{{ __('Loco Engine Type') }}</th>
                                            <td>{{ $inspection->ambushInspection->loco_engine_type }}</td>
                                        </tr>
                                    @endif
                                    @if($inspection->ambushInspection->track_machine_type)
                                        <tr>
                                            <th>{{ __('Track Machine Type') }}</th>
                                            <td>{{ $inspection->ambushInspection->track_machine_type }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th>{{ __('Total Load') }}</th>
                                        <td>{{ $inspection->ambushInspection->total_load }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Stabling Line No') }}</th>
                                        <td>{{ $inspection->ambushInspection->stabling_line_no }}</td>
                                    </tr>
                                @endif

                                <!-- Engineering Block Specific -->
                                @if($inspection->ambushInspection->type_of_ambush === 'Engineering Block')
                                    <tr>
                                        <th>{{ __('Block Place Type') }}</th>
                                        <td>{{ $inspection->ambushInspection->block_place_type }}</td>
                                    </tr>
                                    @if($inspection->ambushInspection->block_place_type === 'Station Section')
                                        <tr>
                                            <th>{{ __('Station Name') }}</th>
                                            <td>{{ $inspection->ambushInspection->block_station_name }}</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <th>{{ __('Block Section Name') }}</th>
                                            <td>{{ $inspection->ambushInspection->block_section_name }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th>{{ __('Block Taken By') }}</th>
                                        <td>{{ $inspection->ambushInspection->block_taken_by }}</td>
                                    </tr>
                                @endif

                                <!-- Voice Recordings Specific -->
                                @if($inspection->ambushInspection->type_of_ambush === 'Checking of Non-Interlocked LC Voice Recordings')
                                    <tr>
                                        <th>{{ __('Recording Date') }}</th>
                                        <td>{{ \Carbon\Carbon::parse($inspection->ambushInspection->recording_date)->format('d-m-Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Station') }}</th>
                                        <td>{{ $inspection->ambushInspection->recording_station }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Block Section') }}</th>
                                        <td>{{ $inspection->ambushInspection->recording_section }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('LC Gate No') }}</th>
                                        <td>{{ $inspection->ambushInspection->recording_gate_no }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Train No') }}</th>
                                        <td>{{ $inspection->ambushInspection->recording_train_no }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Voice Recording Timing (Train Info)') }}</th>
                                        <td>{{ $inspection->ambushInspection->voice_recording_train_time }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Voice Recording Timing (LC Closure)') }}</th>
                                        <td>{{ $inspection->ambushInspection->voice_recording_lc_time }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Actual Departure Time') }}</th>
                                        <td>{{ $inspection->ambushInspection->actual_departure_time }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Actual Arrival Time') }}</th>
                                        <td>{{ $inspection->ambushInspection->actual_arrival_time }}</td>
                                    </tr>
                                @endif

                                <!-- BA Test Specific -->
                                @if($inspection->ambushInspection->type_of_ambush === 'BA Test')
                                    <tr>
                                        <th>{{ __('Staff Name') }}</th>
                                        <td>{{ $inspection->ambushInspection->ba_staff_name }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Designation') }}</th>
                                        <td>{{ $inspection->ambushInspection->ba_designation }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('BA Test Result') }}</th>
                                        <td>
                                            @if($inspection->ambushInspection->ba_result === 'Positive')
                                                <span class="badge bg-danger">Positive</span>
                                            @else
                                                <span class="badge bg-success">Negative</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if($inspection->ambushInspection->ba_result === 'Positive')
                                        <tr>
                                            <th>{{ __('Alcohol %') }}</th>
                                            <td>{{ $inspection->ambushInspection->ba_alcohol_percentage }}%</td>
                                        </tr>
                                    @endif
                                @endif

                                <tr>
                                    <th>{{ __('Major Deficiency Found?') }}</th>
                                    <td>
                                        @if($inspection->ambushInspection->is_deficient)
                                            <span class="badge bg-danger">Yes</span>
                                        @else
                                            <span class="badge bg-success">No</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($inspection->ambushInspection->is_deficient)
                                    <tr>
                                        <th>{{ __('Deficiency Description') }}</th>
                                        <td>{{ $inspection->ambushInspection->deficiency_text }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    @endif

                    <!-- OTHER SIMPLE MOVEMENT TYPES DETAILS -->
                    @if(!in_array($inspection->movement_type, ['REGULAR INSPECTION', 'AMBUSH CHECK']))
                        <div class="mb-4">
                            <h6 class="text-primary border-bottom pb-2 mb-3"><i class="ti ti-bookmark me-1"></i>{{ __('Additional Information') }}</h6>
                            <table class="table table-striped table-bordered">
                                @if($inspection->movement_type === 'SAFETY DRIVE')
                                    <tr>
                                        <th style="width:30%;">{{ __('Safety Drive Type') }}</th>
                                        <td>{{ $inspection->safety_drive_type ?? 'N/A' }}</td>
                                    </tr>
                                @elseif($inspection->movement_type === 'JOINT SIGNAL SIGHTING')
                                    <tr>
                                        <th style="width:30%;">{{ __('Train Type') }}</th>
                                        <td>{{ $inspection->train_type ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Train No') }}</th>
                                        <td>{{ $inspection->train_no ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Block Section') }}</th>
                                        <td>{{ $inspection->block_section ?? 'N/A' }}</td>
                                    </tr>
                                @elseif(in_array($inspection->movement_type, ['NI DUTY', 'JOINT NOTE PREPRATION', 'SURVEY']))
                                    <tr>
                                        <th style="width:30%;">{{ __('Station Name') }}</th>
                                        <td>{{ $inspection->station_name ?? 'N/A' }}</td>
                                    </tr>
                                    @if(in_array($inspection->movement_type, ['JOINT NOTE PREPRATION', 'SURVEY']))
                                        <tr>
                                            <th>{{ __('Reason') }}</th>
                                            <td>{{ $inspection->reason ?? 'N/A' }}</td>
                                        </tr>
                                    @endif
                                @elseif($inspection->movement_type === 'ATTEND BLOCK')
                                    <tr>
                                        <th style="width:30%;">{{ __('Attend Block Activity') }}</th>
                                        <td>{{ $inspection->attend_block_type ?? 'N/A' }}</td>
                                    </tr>
                                @elseif($inspection->movement_type === 'ON LEAVE')
                                    <tr>
                                        <th style="width:30%;">{{ __('Leave Type') }}</th>
                                        <td>{{ $inspection->leave_type ?? 'N/A' }}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">No additional details for this movement type.</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
