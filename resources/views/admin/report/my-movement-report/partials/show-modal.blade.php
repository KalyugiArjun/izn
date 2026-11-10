@php
    $inspDate = $record->inspection_date 
        ? \Carbon\Carbon::parse($record->inspection_date)->format('d M, Y') 
        : ($record->created_at ? $record->created_at->format('d M, Y') : '-');

    $inspTime = $record->created_at ? $record->created_at->format('h:i A') : '-';

    $officerName = $record->officer_name ?: ($record->employee->name ?? '-');
    $officerDesig = $record->designation ?: ($record->employee->designation->name ?? '-');
    $officerDept = $record->employee->department->name ?? '-';

    $mName = strtoupper($record->movement->name ?? '');
    $reg = $record->regularInspection;
    $amb = $record->ambushInspection;
    $tName = $reg && $reg->type ? strtoupper($reg->type->name) : '';

    $isRegular = str_contains($mName, 'REGULAR');
    $isAmbush = str_contains($mName, 'AMBUSH');

    $hasDeficiency = false;
    $deficiencyRemarks = null;
    if ($reg && strtoupper($reg->major_deficiency ?? 'NO') === 'YES') {
        $hasDeficiency = true;
        $deficiencyRemarks = $reg->write_deficiency;
    } elseif ($amb && strtoupper($amb->major_deficiency ?? 'NO') === 'YES') {
        $hasDeficiency = true;
        $deficiencyRemarks = $amb->write_deficiency;
    }

    $shift = null;
    if ($reg && $reg->day_night) {
        $shift = strtoupper($reg->day_night);
    } elseif ($amb && $amb->day_night) {
        $shift = strtoupper($amb->day_night);
    }
@endphp

{{-- Modal Header --}}
<div class="modal-header border-bottom px-4 py-3 d-flex align-items-center justify-content-between w-100" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: #fff; border-top-left-radius: 14px; border-top-right-radius: 14px;">
    {{-- Left: Icon + Title + Meta Info --}}
    <div class="d-flex align-items-center gap-3 flex-grow-1 overflow-hidden me-3">
        <div class="bg-white bg-opacity-25 p-2 rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
            <i class="ti ti-walk fs-3 text-white"></i>
        </div>
        <div class="text-truncate">
            <h5 class="modal-title fw-bold text-white mb-1 d-flex align-items-center gap-2 text-truncate">
                <span>{{ __('MODULE 3: MY MOVEMENT RECORD') }} - #{{ $record->id }}</span>
            </h5>
            <div class="d-flex align-items-center flex-wrap gap-2 text-white-50 small">
                <span><i class="ti ti-user me-1 text-white"></i><strong>{{ $officerName }}</strong> ({{ $officerDesig }})</span>
                <span>•</span>
                <span><i class="ti ti-calendar me-1 text-white"></i>{{ $inspDate }} at {{ $inspTime }}</span>
                <span>•</span>
                <span class="badge bg-white bg-opacity-25 text-white px-2 py-1 rounded">{{ $record->movement->name ?? 'Movement' }}</span>
            </div>
        </div>
    </div>

    {{-- Right: Status Badges + Corner Close Button --}}
    <div class="d-flex align-items-center gap-2 ms-auto flex-shrink-0">
        @if ($hasDeficiency)
            <span class="badge bg-danger px-3 py-2 fw-semibold shadow-sm"><i class="ti ti-alert-triangle me-1"></i>{{ __('MAJOR DEFICIENCY') }}</span>
        @elseif ($isRegular || $isAmbush)
            <span class="badge bg-success px-3 py-2 fw-semibold shadow-sm"><i class="ti ti-check me-1"></i>{{ __('NO DEFICIENCY (COMPLIANT)') }}</span>
        @endif

        @if ($shift === 'NIGHT')
            <span class="badge bg-dark text-white px-3 py-2 fw-semibold shadow-sm"><i class="ti ti-moon me-1"></i>{{ __('NIGHT SHIFT') }}</span>
        @elseif ($shift === 'DAY')
            <span class="badge bg-warning text-dark px-3 py-2 fw-semibold shadow-sm"><i class="ti ti-sun me-1"></i>{{ __('DAY SHIFT') }}</span>
        @endif

        {{-- Close Button in Top-Right Corner --}}
        <button type="button" class="btn btn-sm btn-icon rounded-circle ms-2 d-flex align-items-center justify-content-center" data-bs-dismiss="modal" aria-label="Close" style="width: 36px; height: 36px; min-width: 36px; background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.35); color: #ffffff; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.35)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
            <i class="ti ti-x fs-4"></i>
        </button>
    </div>
</div>

{{-- Modal Body --}}
<div class="modal-body p-4" style="background-color: #f8fafc; max-height: calc(85vh - 120px); overflow-y: auto;">

    {{-- SECTION 1: HEADER & OFFICER INFO (MODULE 3 TOP LEVEL) --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 10px; overflow: hidden;">
        <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between" style="background-color: #1e3c72; color: #fff;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white text-dark rounded-pill px-2 py-1 fw-bold fs-6">1</span>
                <h6 class="mb-0 fw-bold text-white text-uppercase letter-spacing-1">{{ __('Movement & Officer Overview') }}</h6>
            </div>
            <span class="badge bg-white bg-opacity-25 text-white small">{{ $record->movement->name ?? 'Movement' }}</span>
        </div>
        <div class="card-body p-3">
            <div class="row g-3">
                {{-- Movement Date & Time --}}
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Movement Date & Time') }}</label>
                    <div class="fw-bold text-dark fs-6">{{ $inspDate }}</div>
                    <div class="text-primary small"><i class="ti ti-clock me-1"></i>{{ $inspTime }}</div>
                </div>

                {{-- Movement By: Name & Designation --}}
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Movement By (Officer)') }}</label>
                    <div class="fw-bold text-dark fs-6">{{ $officerName }}</div>
                    <div class="text-muted small">
                        <span class="badge bg-light text-dark border">{{ $officerDesig }}</span>
                    </div>
                </div>

                {{-- Department --}}
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Department') }}</label>
                    <div class="fw-semibold text-dark">{{ $officerDept }}</div>
                </div>

                {{-- Type of Movement --}}
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Type of Movement') }}</label>
                    <span class="badge bg-primary px-2 py-1 fs-6">
                        {{ $record->movement->name ?? '-' }}
                    </span>
                </div>

                {{-- GPS Location --}}
                <div class="col-12 border-top pt-2 mt-2">
                    <label class="text-muted small fw-semibold d-block">{{ __('GPS Location Recorded') }}</label>
                    <div class="d-flex align-items-center gap-2">
                        @if($record->gps_location)
                            <span class="fw-semibold text-dark"><i class="ti ti-map-pin text-danger me-1"></i>{{ $record->gps_location }}</span>
                            <a href="https://maps.google.com/?q={{ $record->gps_location }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2">
                                <i class="ti ti-external-link me-1"></i>{{ __('Open in Google Maps') }}
                            </a>
                        @else
                            <span class="text-muted small">{{ __('No GPS coordinates captured for this movement.') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 2: DETAILS CAPTURED (PER MODULE SPECIFICATIONS) --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 10px; overflow: hidden;">
        <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between" style="background-color: #0f766e; color: #fff;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white text-dark rounded-pill px-2 py-1 fw-bold fs-6">2</span>
                <h6 class="mb-0 fw-bold text-white text-uppercase letter-spacing-1">{{ __('Details Captured') }}</h6>
            </div>
            <span class="badge bg-white bg-opacity-25 text-white small">
                @if($isRegular)
                    {{ $reg && $reg->type ? $reg->type->name : 'Regular Inspection' }}
                @elseif($isAmbush)
                    {{ $amb && $amb->ambushType ? $amb->ambushType->name : 'Ambush Check' }}
                @else
                    {{ $record->movement->name ?? 'Captured Data' }}
                @endif
            </span>
        </div>
        <div class="card-body p-3">

            {{-- CASE A: REGULAR INSPECTION --}}
            @if($isRegular)
                <div class="row g-3">
                    <div class="col-md-4 col-sm-6">
                        <label class="text-muted small fw-semibold d-block">{{ __('Type of Inspection') }}</label>
                        <div class="fw-bold text-primary fs-6">{{ $reg && $reg->type ? $reg->type->name : 'General Regular Inspection' }}</div>
                    </div>

                    <div class="col-md-4 col-sm-6">
                        <label class="text-muted small fw-semibold d-block">{{ __('Shift / Timing') }}</label>
                        <div class="fw-semibold text-dark">
                            {{ $shift ? $shift . ' SHIFT' : '-' }}
                        </div>
                    </div>

                    {{-- Dynamic fields according to Regular Inspection types --}}
                    @if(str_contains($tName, 'THOROUGH') || str_contains($tName, 'SAFETY'))
                        <div class="col-md-4 col-sm-6">
                            <label class="text-muted small fw-semibold d-block">{{ __('Station Name') }}</label>
                            <div class="fw-bold text-dark fs-6">{{ $reg->station ? $reg->station->name . ' (' . $reg->station->code . ')' : '-' }}</div>
                        </div>

                    @elseif(str_contains($tName, 'SURPRISE') || str_contains($tName, 'NIGHT'))
                        <div class="col-md-4 col-sm-6">
                            <label class="text-muted small fw-semibold d-block">{{ __('Station Name') }}</label>
                            <div class="fw-bold text-dark fs-6">{{ $reg->station ? $reg->station->name : '-' }}</div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="text-muted small fw-semibold d-block">{{ __('LC Gate No') }}</label>
                            <div class="fw-bold text-dark fs-6">{{ $reg->gate_no ?: '-' }}</div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="text-muted small fw-semibold d-block">{{ __('Engg / Traffic') }}</label>
                            <div class="fw-semibold text-dark">{{ $reg->engg_traffic ?: '-' }}</div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="text-muted small fw-semibold d-block">{{ __('Block Section') }}</label>
                            <div class="fw-semibold text-dark">{{ $reg->block_section ?: '-' }}</div>
                        </div>

                    @elseif(str_contains($tName, 'LC GATE'))
                        <div class="col-md-4 col-sm-6">
                            <label class="text-muted small fw-semibold d-block">{{ __('LC Gate No') }}</label>
                            <div class="fw-bold text-dark fs-6">{{ $reg->gate_no ?: '-' }}</div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="text-muted small fw-semibold d-block">{{ __('Engg / Traffic') }}</label>
                            <div class="fw-semibold text-dark">{{ $reg->engg_traffic ?: '-' }}</div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="text-muted small fw-semibold d-block">{{ __('Block Section') }}</label>
                            <div class="fw-semibold text-dark">{{ $reg->block_section ?: '-' }}</div>
                        </div>

                    @elseif(str_contains($tName, 'LC AMBUSH'))
                        <div class="col-md-4 col-sm-6">
                            <label class="text-muted small fw-semibold d-block">{{ __('LC Gate No') }}</label>
                            <div class="fw-bold text-dark fs-6">{{ $reg->gate_no ?: '-' }}</div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="text-muted small fw-semibold d-block">{{ __('Train No') }}</label>
                            <div class="fw-bold text-dark fs-6">{{ $reg->train_no ?: '-' }}</div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="text-muted small fw-semibold d-block">{{ __('Block Section') }}</label>
                            <div class="fw-semibold text-dark">{{ $reg->block_section ?: '-' }}</div>
                        </div>

                    @elseif(str_contains($tName, 'FOOTPLATE') || str_contains($tName, 'BRAKE-VAN') || str_contains($tName, 'BRAKE VAN'))
                        <div class="col-md-4 col-sm-6">
                            <label class="text-muted small fw-semibold d-block">{{ __('Inspection By (Goods / Passenger)') }}</label>
                            <div class="fw-bold text-dark fs-6">{{ $reg->goods_passenger ?: '-' }}</div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="text-muted small fw-semibold d-block">{{ __('Train No') }}</label>
                            <div class="fw-bold text-dark fs-6">{{ $reg->train_no ?: '-' }}</div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="text-muted small fw-semibold d-block">{{ __('Block Section') }}</label>
                            <div class="fw-semibold text-dark">{{ $reg->block_section ?: '-' }}</div>
                        </div>

                    @elseif(str_contains($tName, 'DIESEL LOBBY'))
                        <div class="col-md-4 col-sm-6">
                            <label class="text-muted small fw-semibold d-block">{{ __('Diesel Lobby Location') }}</label>
                            <div class="fw-bold text-dark fs-6">{{ $reg->dieselLobby->name ?? ($reg->location_id ?: '-') }}</div>
                        </div>

                    @elseif(str_contains($tName, 'RUNNING ROOM'))
                        <div class="col-md-4 col-sm-6">
                            <label class="text-muted small fw-semibold d-block">{{ __('Running Room Location') }}</label>
                            <div class="fw-bold text-dark fs-6">{{ $reg->runningRoom->name ?? ($reg->location_id ?: '-') }}</div>
                        </div>
                    @endif

                    {{-- Deficiency Section for Regular Inspection --}}
                    <div class="col-12 border-top pt-3 mt-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="text-muted small fw-semibold mb-0">{{ __('Major Deficiency Review') }}</label>
                            @if($hasDeficiency)
                                <span class="badge bg-danger px-3 py-1"><i class="ti ti-alert-triangle me-1"></i>YES</span>
                            @else
                                <span class="badge bg-success px-3 py-1"><i class="ti ti-check me-1"></i>NO</span>
                            @endif
                        </div>
                        @if($hasDeficiency)
                            <div class="alert alert-danger mb-0 py-2 px-3 border-danger">
                                <div class="fw-bold mb-1"><i class="ti ti-alert-circle me-1"></i>{{ __('Deficiency Description / Remarks') }}:</div>
                                <div class="text-dark">{{ $deficiencyRemarks ?: 'Deficiency reported without specific notes.' }}</div>
                            </div>
                        @else
                            <div class="text-muted small">
                                <i class="ti ti-circle-check text-success me-1"></i>{{ __('Inspection conducted satisfactorily with no major deficiencies observed.') }}
                            </div>
                        @endif
                    </div>
                </div>

            {{-- CASE B: AMBUSH CHECK --}}
            @elseif($isAmbush)
                <div class="row g-3">
                    <div class="col-md-4 col-sm-6">
                        <label class="text-muted small fw-semibold d-block">{{ __('Ambush Check Type') }}</label>
                        <div class="fw-bold text-primary fs-6">{{ $amb && $amb->ambushType ? $amb->ambushType->name : 'Ambush Check' }}</div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <label class="text-muted small fw-semibold d-block">{{ __('Station Name') }}</label>
                        <div class="fw-bold text-dark fs-6">{{ $amb && $amb->station ? $amb->station->name . ' (' . $amb->station->code . ')' : '-' }}</div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <label class="text-muted small fw-semibold d-block">{{ __('Shift / Timing') }}</label>
                        <div class="fw-semibold text-dark">{{ $shift ? $shift . ' SHIFT' : '-' }}</div>
                    </div>

                    @if($amb)
                        @if($amb->shunting_observation)
                            <div class="col-12">
                                <label class="text-muted small fw-semibold d-block">{{ __('Shunting / Ambush Observation') }}</label>
                                <div class="p-3 bg-white rounded border fw-semibold text-dark">{{ $amb->shunting_observation }}</div>
                            </div>
                        @endif
                        @if($amb->t_806_no)
                            <div class="col-md-4 col-sm-6">
                                <label class="text-muted small fw-semibold d-block">{{ __('T/806 No') }}</label>
                                <div class="fw-bold text-dark">{{ $amb->t_806_no }}</div>
                            </div>
                        @endif
                        @if($amb->train_no)
                            <div class="col-md-4 col-sm-6">
                                <label class="text-muted small fw-semibold d-block">{{ __('Train No') }}</label>
                                <div class="fw-bold text-dark">{{ $amb->train_no }}</div>
                            </div>
                        @endif
                        @if($amb->lc_gate_no)
                            <div class="col-md-4 col-sm-6">
                                <label class="text-muted small fw-semibold d-block">{{ __('LC Gate No') }}</label>
                                <div class="fw-bold text-dark">{{ $amb->lc_gate_no }}</div>
                            </div>
                        @endif
                        @if($amb->block_section)
                            <div class="col-md-4 col-sm-6">
                                <label class="text-muted small fw-semibold d-block">{{ __('Block Section') }}</label>
                                <div class="fw-semibold text-dark">{{ $amb->block_section }}</div>
                            </div>
                        @endif
                    @endif

                    {{-- Deficiency Review for Ambush --}}
                    <div class="col-12 border-top pt-3 mt-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="text-muted small fw-semibold mb-0">{{ __('Major Deficiency Review') }}</label>
                            @if($hasDeficiency)
                                <span class="badge bg-danger px-3 py-1"><i class="ti ti-alert-triangle me-1"></i>YES</span>
                            @else
                                <span class="badge bg-success px-3 py-1"><i class="ti ti-check me-1"></i>NO</span>
                            @endif
                        </div>
                        @if($hasDeficiency)
                            <div class="alert alert-danger mb-0 py-2 px-3 border-danger">
                                <div class="fw-bold mb-1"><i class="ti ti-alert-circle me-1"></i>{{ __('Deficiency Description / Remarks') }}:</div>
                                <div class="text-dark">{{ $deficiencyRemarks ?: 'Deficiency reported without specific notes.' }}</div>
                            </div>
                        @else
                            <div class="text-muted small">
                                <i class="ti ti-circle-check text-success me-1"></i>{{ __('Ambush check conducted satisfactorily with no major deficiencies.') }}
                            </div>
                        @endif
                    </div>
                </div>

            {{-- CASE C: SAFETY DRIVE --}}
            @elseif(str_contains($mName, 'SAFETY DRIVE'))
                <div class="row g-3">
                    <div class="col-md-6 col-sm-12">
                        <label class="text-muted small fw-semibold d-block">{{ __('Type of Safety Drive') }}</label>
                        <div class="p-3 bg-white rounded border fw-bold text-dark fs-6">
                            <i class="ti ti-shield-check text-warning me-2"></i>{{ $record->safetyDrive->name ?? '-' }}
                        </div>
                    </div>
                </div>

            {{-- CASE D: JOINT SIGNAL SIGHTING --}}
            @elseif(str_contains($mName, 'JOINT SIGNAL SIGHTING'))
                <div class="row g-3">
                    <div class="col-md-4 col-sm-6">
                        <label class="text-muted small fw-semibold d-block">{{ __('Inspection By') }}</label>
                        <div class="fw-bold text-dark fs-6">{{ $record->goods_passenger ?: '-' }}</div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <label class="text-muted small fw-semibold d-block">{{ __('Train No') }}</label>
                        <div class="fw-bold text-dark fs-6">{{ $record->train_no ?: '-' }}</div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <label class="text-muted small fw-semibold d-block">{{ __('Block Section') }}</label>
                        <div class="fw-semibold text-dark fs-6">{{ $record->block_section ?: '-' }}</div>
                    </div>
                </div>

            {{-- CASE E: NI DUTY --}}
            @elseif(str_contains($mName, 'NI DUTY'))
                <div class="row g-3">
                    <div class="col-md-6 col-sm-12">
                        <label class="text-muted small fw-semibold d-block">{{ __('Station Name') }}</label>
                        <div class="p-3 bg-white rounded border fw-bold text-dark fs-6">
                            <i class="ti ti-building text-primary me-2"></i>{{ $record->station ? $record->station->name . ' (' . $record->station->code . ')' : '-' }}
                        </div>
                    </div>
                </div>

            {{-- CASE F: JOINT NOTE PREPARATION --}}
            @elseif(str_contains($mName, 'JOINT NOTE'))
                <div class="row g-3">
                    <div class="col-md-4 col-sm-6">
                        <label class="text-muted small fw-semibold d-block">{{ __('Station Name') }}</label>
                        <div class="fw-bold text-dark fs-6">{{ $record->station ? $record->station->name . ' (' . $record->station->code . ')' : '-' }}</div>
                    </div>
                    <div class="col-md-8 col-sm-6">
                        <label class="text-muted small fw-semibold d-block">{{ __('Reason') }}</label>
                        <div class="fw-semibold text-dark">{{ $record->reason ?: '-' }}</div>
                    </div>
                    <div class="col-12 border-top pt-2">
                        <label class="text-muted small fw-semibold d-block mb-1">{{ __('Joint Note PDF') }}</label>
                        @if($record->joint_note_pdf)
                            <a href="{{ asset('storage/' . $record->joint_note_pdf) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="ti ti-file-text me-1"></i>{{ __('Download Attached Joint Note PDF') }}
                            </a>
                        @else
                            <span class="text-muted small">{{ __('No PDF document attached.') }}</span>
                        @endif
                    </div>
                </div>

            {{-- CASE G: SURVEY --}}
            @elseif(str_contains($mName, 'SURVEY'))
                <div class="row g-3">
                    <div class="col-md-4 col-sm-6">
                        <label class="text-muted small fw-semibold d-block">{{ __('Station Name') }}</label>
                        <div class="fw-bold text-dark fs-6">{{ $record->station ? $record->station->name . ' (' . $record->station->code . ')' : '-' }}</div>
                    </div>
                    <div class="col-md-8 col-sm-6">
                        <label class="text-muted small fw-semibold d-block">{{ __('Reason') }}</label>
                        <div class="fw-semibold text-dark">{{ $record->reason ?: '-' }}</div>
                    </div>
                    <div class="col-12 border-top pt-2">
                        <label class="text-muted small fw-semibold d-block mb-1">{{ __('Survey PDF') }}</label>
                        @if($record->survey_pdf)
                            <a href="{{ asset('storage/' . $record->survey_pdf) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="ti ti-file-text me-1"></i>{{ __('Download Attached Survey PDF') }}
                            </a>
                        @else
                            <span class="text-muted small">{{ __('No PDF document attached.') }}</span>
                        @endif
                    </div>
                </div>

            {{-- CASE H: ATTEND BLOCK --}}
            @elseif(str_contains($mName, 'ATTEND BLOCK'))
                <div class="row g-3">
                    <div class="col-md-6 col-sm-12">
                        <label class="text-muted small fw-semibold d-block">{{ __('Type of Attend Block') }}</label>
                        <div class="p-3 bg-white rounded border fw-bold text-dark fs-6">
                            <i class="ti ti-traffic-cone text-warning me-2"></i>{{ $record->attendBlock->name ?? '-' }}
                        </div>
                    </div>
                </div>

            {{-- CASE I: ON LEAVE --}}
            @elseif(str_contains($mName, 'ON LEAVE'))
                <div class="row g-3">
                    <div class="col-md-6 col-sm-12">
                        <label class="text-muted small fw-semibold d-block">{{ __('Type of Leave') }}</label>
                        <div class="p-3 bg-white rounded border fw-bold text-dark fs-6">
                            <i class="ti ti-calendar-off text-danger me-2"></i>{{ $record->typeOfLeave->name ?? '-' }}
                        </div>
                    </div>
                </div>

            {{-- CASE J: OFFICE WORK & OTHER WORK --}}
            @elseif(str_contains($mName, 'OFFICE') || str_contains($mName, 'OTHER'))
                <div class="row g-3">
                    <div class="col-12">
                        <label class="text-muted small fw-semibold d-block">{{ __('Description / Reason') }}</label>
                        <div class="p-3 bg-white rounded border text-dark fs-6">
                            {{ $record->reason ?: '-' }}
                        </div>
                    </div>
                </div>

            {{-- CASE K: REST & STAFF COUNSELING --}}
            @elseif(str_contains($mName, 'REST') || str_contains($mName, 'COUNSELING'))
                <div class="p-4 bg-white rounded border text-center">
                    <i class="ti ti-circle-check text-success fs-1 mb-2 d-block"></i>
                    <h6 class="fw-bold text-dark mb-1">{{ $record->movement->name ?? 'Rest / Counseling' }}</h6>
                    <p class="text-muted small mb-0">{{ __('No additional fields or parameters captured for this type of movement.') }}</p>
                </div>

            {{-- DEFAULT FALLBACK --}}
            @else
                <div class="row g-3">
                    <div class="col-12">
                        <label class="text-muted small fw-semibold d-block">{{ __('Remarks / Information') }}</label>
                        <div class="p-3 bg-white rounded border text-dark fs-6">
                            {{ $record->reason ?: '-' }}
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- SECTION 3: SYSTEM AUDIT & RECORD TIMESTAMPS --}}
    <div class="card border-0 shadow-sm" style="border-radius: 10px; overflow: hidden;">
        <div class="card-body p-3 bg-light">
            <div class="row g-3 small text-muted">
                <div class="col-md-4 col-sm-6">
                    <div><i class="ti ti-calendar me-1"></i>{{ __('Created At') }}: <strong>{{ $record->created_at ? $record->created_at->format('d M, Y h:i:s A') : '-' }}</strong></div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div><i class="ti ti-clock me-1"></i>{{ __('Last Updated') }}: <strong>{{ $record->updated_at ? $record->updated_at->format('d M, Y h:i:s A') : '-' }}</strong></div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div><i class="ti ti-fingerprint me-1"></i>{{ __('Record ID') }}: <strong>#{{ $record->id }}</strong></div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Modal Footer --}}
<div class="modal-footer bg-white border-top px-4 py-3 d-flex justify-content-between align-items-center">
    <div class="text-muted small">
        <i class="ti ti-info-circle me-1"></i>{{ __('Generated from Saathi My Movement Records System') }}
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
            <i class="ti ti-printer me-1"></i>{{ __('Print') }}
        </button>
        <a href="{{ route('report.my-movement.export.movement-pdf', $record->id) }}" class="btn btn-outline-danger" target="_blank">
            <i class="ti ti-file-text me-1"></i>{{ __('Download Dossier PDF') }}
        </a>
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
            {{ __('Close') }}
        </button>
    </div>
</div>
