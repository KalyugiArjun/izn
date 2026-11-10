@php
    $inspDate = $ambush->inspection && $ambush->inspection->inspection_date 
        ? \Carbon\Carbon::parse($ambush->inspection->inspection_date)->format('d M, Y') 
        : ($ambush->created_at ? $ambush->created_at->format('d M, Y') : '-');

    $inspTime = $ambush->from_time ?? ($ambush->created_at ? $ambush->created_at->format('h:i A') : '-');

    $officerName = $ambush->inspection->officer_name 
        ?? ($ambush->inspection->employee->name ?? '-');

    $officerDesig = $ambush->inspection->designation 
        ?? ($ambush->inspection->employee->designation->name ?? '-');

    $typeName = strtoupper($ambush->ambushType->name ?? '');
    $typeId = $ambush->type_of_ambush_id;

    $isShunting = str_contains($typeName, 'SHUNTING') || $typeId == 1;
    $isLoadStable = str_contains($typeName, 'LOAD STABLE') || $typeId == 2;
    $isEngBlock = str_contains($typeName, 'ENGINEERING') || $typeId == 3;
    $isLcVoice = str_contains($typeName, 'VOICE') || str_contains($typeName, 'LC') || $typeId == 4;
    $isBaTest = str_contains($typeName, 'BA TEST') || $typeId == 5;

    $hasDeficiency = strtoupper($ambush->major_deficiency ?? 'NO') === 'YES';
@endphp

{{-- Modal Header --}}
<div class="modal-header border-bottom px-4 py-3" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: #fff; border-top-left-radius: 14px; border-top-right-radius: 14px;">
    <div class="d-flex align-items-center gap-2">
        <div class="bg-white bg-opacity-25 p-2 rounded-circle me-1">
            <i class="ti ti-eye fs-3 text-white"></i>
        </div>
        <div>
            <h5 class="modal-title fw-bold text-white mb-0">
                {{ __('MODULE 2: AMBUSH CHECK RECORD') }} - #{{ $ambush->id }}
            </h5>
            <small class="text-white-50">
                <i class="ti ti-map-pin me-1"></i>{{ $ambush->station->name ?? 'N/A' }} ({{ $ambush->station->code ?? '-' }}) | 
                <i class="ti ti-user me-1"></i>{{ $officerName }} ({{ $officerDesig }}) | 
                <i class="ti ti-calendar me-1"></i>{{ $inspDate }}
            </small>
        </div>
    </div>
    <div class="d-flex align-items-center gap-2">
        @if ($hasDeficiency)
            <span class="badge bg-danger px-3 py-2 fw-semibold shadow-sm"><i class="ti ti-alert-triangle me-1"></i>{{ __('MAJOR DEFICIENCY') }}</span>
        @else
            <span class="badge bg-success px-3 py-2 fw-semibold"><i class="ti ti-check me-1"></i>{{ __('NO DEFICIENCY (COMPLIANT)') }}</span>
        @endif

        @if (strtoupper($ambush->day_night) === 'NIGHT')
            <span class="badge bg-dark text-white px-3 py-2 fw-semibold"><i class="ti ti-moon me-1"></i>{{ __('NIGHT SHIFT') }}</span>
        @else
            <span class="badge bg-warning text-dark px-3 py-2 fw-semibold"><i class="ti ti-sun me-1"></i>{{ __('DAY SHIFT') }}</span>
        @endif

        <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
</div>

{{-- Modal Body --}}
<div class="modal-body p-4" style="background-color: #f8fafc; max-height: calc(85vh - 120px); overflow-y: auto;">

    {{-- ========================================================================= --}}
    {{-- SECTION 1: GENERAL AMBUSH CHECK RECORD                                   --}}
    {{-- ========================================================================= --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 10px; overflow: hidden;">
        <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between" style="background-color: #1e3c72; color: #fff;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white text-dark rounded-pill px-2 py-1 fw-bold fs-6">1</span>
                <h6 class="mb-0 fw-bold text-white text-uppercase letter-spacing-1">{{ __('General Ambush Check Details') }}</h6>
            </div>
            <span class="badge bg-white bg-opacity-25 text-white small">{{ $ambush->ambushType->name ?? 'Inspection' }}</span>
        </div>
        <div class="card-body p-3">
            <div class="row g-3">
                {{-- Ambush Check: Date & Time --}}
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Ambush Check (Date & Time)') }}</label>
                    <div class="fw-bold text-dark fs-6">{{ $inspDate }}</div>
                    <div class="text-primary small"><i class="ti ti-clock me-1"></i>{{ $inspTime }}</div>
                </div>

                {{-- Inspected By: Name & Designation --}}
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Inspected By') }}</label>
                    <div class="fw-bold text-dark fs-6">{{ $officerName }}</div>
                    <div class="text-muted small"><i class="ti ti-id-badge me-1"></i>{{ $officerDesig }}</div>
                </div>

                {{-- Station --}}
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Station') }}</label>
                    <div class="fw-bold text-dark fs-6">{{ $ambush->station->name ?? 'N/A' }}</div>
                    <div class="text-muted small">{{ __('Code:') }} <span class="badge bg-light text-dark border">{{ $ambush->station->code ?? '-' }}</span></div>
                </div>

                {{-- From Time & To Time --}}
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('From Time & To Time') }}</label>
                    <div class="fw-bold text-dark fs-6">
                        <span class="badge bg-light text-dark border p-2">
                            {{ $ambush->from_time ?? '-' }} &nbsp;&rarr;&nbsp; {{ $ambush->to_time ?? '-' }}
                        </span>
                    </div>
                </div>

                {{-- Day / Night --}}
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Day / Night') }}</label>
                    <div>
                        @if (strtoupper($ambush->day_night) === 'NIGHT')
                            <span class="badge bg-dark text-white px-2 py-1"><i class="ti ti-moon me-1"></i>NIGHT</span>
                        @else
                            <span class="badge bg-warning text-dark px-2 py-1"><i class="ti ti-sun me-1"></i>DAY</span>
                        @endif
                    </div>
                </div>

                {{-- Type of Ambush Check --}}
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Type of Ambush Check') }}</label>
                    <div>
                        <span class="badge bg-primary px-2 py-1 fs-6">{{ $ambush->ambushType->name ?? '-' }}</span>
                    </div>
                </div>

                {{-- Diesel Lobby if any --}}
                @if($ambush->lobby)
                    <div class="col-md-3 col-sm-6">
                        <label class="text-muted small fw-semibold d-block">{{ __('Diesel Lobby') }}</label>
                        <div class="fw-bold text-dark">{{ $ambush->lobby->name }}</div>
                    </div>
                @endif

                {{-- Train No if any --}}
                @if($ambush->train_no)
                    <div class="col-md-3 col-sm-6">
                        <label class="text-muted small fw-semibold d-block">{{ __('Train No.') }}</label>
                        <div class="fw-bold text-dark">{{ $ambush->train_no }}</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- SECTION 2: SHUNTING (Red Header as in Reference Image)                     --}}
    {{-- ========================================================================= --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 10px; overflow: hidden; {{ !$isShunting ? 'opacity: 0.9;' : '' }}">
        <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between" style="background-color: #1e3c72; color: #fff;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-danger text-white rounded-pill px-2 py-1 fw-bold fs-6">2</span>
                <h6 class="mb-0 fw-bold text-danger text-uppercase letter-spacing-1" style="color: #ff4d4f !important;">
                    {{ __('SHUNTING') }}
                </h6>
            </div>
            @if($isShunting)
                <span class="badge bg-success text-white small">{{ __('Active in this Record') }}</span>
            @else
                <span class="badge bg-white bg-opacity-25 text-white small">{{ __('Module Reference') }}</span>
            @endif
        </div>
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-md-4 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Shunting Observation Of') }}</label>
                    <div class="p-2 bg-white rounded border fw-semibold text-dark" style="min-height: 40px;">
                        {{ $ambush->shunting_observation ?? '-' }}
                    </div>
                </div>

                <div class="col-md-4 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('T/806 No') }}</label>
                    <div class="p-2 bg-white rounded border fw-semibold text-dark" style="min-height: 40px;">
                        {{ $ambush->t_806_no ?? '-' }}
                    </div>
                </div>

                <div class="col-md-4 col-sm-12">
                    <label class="text-muted small fw-semibold d-block">{{ __('Any Major Deficiency (Shunting)') }}</label>
                    <div class="p-2 bg-white rounded border" style="min-height: 40px;">
                        @if($isShunting && $hasDeficiency)
                            <span class="badge bg-danger"><i class="ti ti-alert-triangle me-1"></i>YES</span>
                            <span class="small text-danger ms-2">{{ $ambush->write_deficiency ?? 'Deficiency recorded.' }}</span>
                        @elseif($isShunting)
                            <span class="badge bg-success"><i class="ti ti-check me-1"></i>NO</span>
                            <span class="small text-muted ms-2">{{ __('No deficiency') }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>

                {{-- Shunting Staff --}}
                <div class="col-12">
                    <label class="text-muted small fw-semibold d-block mb-1">{{ __('Shunting Staff (Name & Designation)') }}</label>
                    <div class="table-responsive bg-white rounded border">
                        <table class="table table-sm table-bordered align-middle mb-0">
                            <thead class="bg-light small text-muted text-uppercase">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>{{ __('Staff Name') }}</th>
                                    <th>{{ __('Designation') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($ambush->shunting_staff_details && $ambush->shunting_staff_details->count() > 0)
                                    @foreach($ambush->shunting_staff_details as $stfIdx => $stf)
                                        <tr>
                                            <td class="text-muted small">{{ $stfIdx + 1 }}</td>
                                            <td class="fw-bold text-dark">{{ $stf['name'] }}</td>
                                            <td><span class="badge bg-light text-dark border">{{ $stf['designation'] }}</span></td>
                                        </tr>
                                    @endforeach
                                @elseif($ambush->staffName)
                                    <tr>
                                        <td class="text-muted small">1</td>
                                        <td class="fw-bold text-dark">{{ $ambush->staffName->name }}</td>
                                        <td><span class="badge bg-light text-dark border">{{ $ambush->designation ?? ($ambush->staffName->designation->name ?? '-') }}</span></td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="3" class="text-muted text-center py-2">{{ __('No shunting staff assigned / recorded.') }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- SECTION 3: LOAD STABLE (Red Header as in Reference Image)                 --}}
    {{-- ========================================================================= --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 10px; overflow: hidden; {{ !$isLoadStable ? 'opacity: 0.9;' : '' }}">
        <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between" style="background-color: #1e3c72; color: #fff;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-danger text-white rounded-pill px-2 py-1 fw-bold fs-6">3</span>
                <h6 class="mb-0 fw-bold text-danger text-uppercase letter-spacing-1" style="color: #ff4d4f !important;">
                    {{ __('LOAD STABLE') }}
                </h6>
            </div>
            @if($isLoadStable)
                <span class="badge bg-info text-white small">{{ __('Active in this Record') }}</span>
            @else
                <span class="badge bg-white bg-opacity-25 text-white small">{{ __('Module Reference') }}</span>
            @endif
        </div>
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Load Type') }}</label>
                    <div class="p-2 bg-white rounded border fw-semibold text-dark" style="min-height: 40px;">
                        {{ $ambush->load_type ?? '-' }}
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Total No. of Loads') }}</label>
                    <div class="p-2 bg-white rounded border fw-semibold text-dark" style="min-height: 40px;">
                        {{ $ambush->total_number_of_load ?? '-' }}
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Running Line / Non-Running Line') }}</label>
                    <div class="p-2 bg-white rounded border fw-semibold text-dark" style="min-height: 40px;">
                        {{ $ambush->running_line ?? '-' }}
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Stabling Line No.') }}</label>
                    <div class="p-2 bg-white rounded border fw-semibold text-dark" style="min-height: 40px;">
                        {{ $ambush->stabling_line_no ?? '-' }}
                    </div>
                </div>

                <div class="col-12">
                    <label class="text-muted small fw-semibold d-block">{{ __('Any Major Deficiency (Load Stable)') }}</label>
                    <div class="p-2 bg-white rounded border" style="min-height: 40px;">
                        @if($isLoadStable && $hasDeficiency)
                            <span class="badge bg-danger"><i class="ti ti-alert-triangle me-1"></i>YES</span>
                            <span class="small text-danger ms-2">{{ $ambush->write_deficiency ?? 'Deficiency recorded.' }}</span>
                        @elseif($isLoadStable)
                            <span class="badge bg-success"><i class="ti ti-check me-1"></i>NO</span>
                            <span class="small text-muted ms-2">{{ __('No deficiency') }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- SECTION 4: ENGINEERING BLOCK (Page 4)                                     --}}
    {{-- ========================================================================= --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 10px; overflow: hidden; {{ !$isEngBlock ? 'opacity: 0.9;' : '' }}">
        <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between" style="background-color: #1e3c72; color: #fff;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-danger text-white rounded-pill px-2 py-1 fw-bold fs-6">4</span>
                <h6 class="mb-0 fw-bold text-danger text-uppercase letter-spacing-1" style="color: #ff4d4f !important;">
                    {{ __('ENGINEERING BLOCK') }}
                </h6>
            </div>
            @if($isEngBlock)
                <span class="badge bg-warning text-dark small">{{ __('Active in this Record') }}</span>
            @else
                <span class="badge bg-white bg-opacity-25 text-white small">{{ __('Module Reference') }}</span>
            @endif
        </div>
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-md-4 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Location of Ambush Check') }}</label>
                    <div class="p-2 bg-white rounded border fw-semibold text-dark" style="min-height: 40px;">
                        {{ $ambush->station->name ?? ($ambush->block_section ?? '-') }}
                    </div>
                </div>

                <div class="col-md-4 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Block Taken By') }}</label>
                    <div class="p-2 bg-white rounded border fw-semibold text-dark" style="min-height: 40px;">
                        {{ $ambush->block_taken_by ?? '-' }}
                    </div>
                </div>

                <div class="col-md-4 col-sm-12">
                    <label class="text-muted small fw-semibold d-block">{{ __('Any Major Deficiency (Engineering Block)') }}</label>
                    <div class="p-2 bg-white rounded border" style="min-height: 40px;">
                        @if($isEngBlock && $hasDeficiency)
                            <span class="badge bg-danger"><i class="ti ti-alert-triangle me-1"></i>YES</span>
                            <span class="small text-danger ms-2">{{ $ambush->write_deficiency ?? 'Deficiency recorded.' }}</span>
                        @elseif($isEngBlock)
                            <span class="badge bg-success"><i class="ti ti-check me-1"></i>NO</span>
                            <span class="small text-muted ms-2">{{ __('No deficiency') }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- SECTION 5: BA TEST (Page 4)                                               --}}
    {{-- ========================================================================= --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 10px; overflow: hidden; {{ !$isBaTest ? 'opacity: 0.9;' : '' }}">
        <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between" style="background-color: #1e3c72; color: #fff;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-danger text-white rounded-pill px-2 py-1 fw-bold fs-6">5</span>
                <h6 class="mb-0 fw-bold text-danger text-uppercase letter-spacing-1" style="color: #ff4d4f !important;">
                    {{ __('BA TEST (BREATH ANALYSER)') }}
                </h6>
            </div>
            @if($isBaTest)
                <span class="badge bg-primary text-white small">{{ __('Active in this Record') }}</span>
            @else
                <span class="badge bg-white bg-opacity-25 text-white small">{{ __('Module Reference') }}</span>
            @endif
        </div>
        <div class="card-body p-3">
            <div class="row g-3">
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Lobby Name') }}</label>
                    <div class="p-2 bg-white rounded border fw-semibold text-dark" style="min-height: 40px;">
                        {{ $ambush->lobby->name ?? '-' }}
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Station Name') }}</label>
                    <div class="p-2 bg-white rounded border fw-semibold text-dark" style="min-height: 40px;">
                        {{ $ambush->station->name ?? '-' }}
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Staff Name') }}</label>
                    <div class="p-2 bg-white rounded border fw-semibold text-dark" style="min-height: 40px;">
                        {{ $ambush->staffName->name ?? '-' }}
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Staff Designation') }}</label>
                    <div class="p-2 bg-white rounded border fw-semibold text-dark" style="min-height: 40px;">
                        {{ $ambush->ba_staff_designation_name }}
                    </div>
                </div>

                <div class="col-md-6 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Alcohol Test Result / Percentage') }}</label>
                    <div class="p-2 bg-white rounded border" style="min-height: 40px;">
                        @if($ambush->alcohol_test)
                            <span class="badge {{ strtolower($ambush->alcohol_test) === 'positive' ? 'bg-danger' : 'bg-success' }}">
                                {{ $ambush->alcohol_test }}
                            </span>
                        @endif
                        @if($ambush->alcohol_percentage)
                            <span class="ms-2 small text-muted"><strong>Percentage:</strong> {{ $ambush->alcohol_percentage }}%</span>
                        @endif
                        @if(!$ambush->alcohol_test && !$ambush->alcohol_percentage)
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>

                <div class="col-md-6 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Any Major Deficiency (BA Test)') }}</label>
                    <div class="p-2 bg-white rounded border" style="min-height: 40px;">
                        @if($isBaTest && $hasDeficiency)
                            <span class="badge bg-danger"><i class="ti ti-alert-triangle me-1"></i>YES</span>
                            <span class="small text-danger ms-2">{{ $ambush->write_deficiency ?? 'Deficiency recorded.' }}</span>
                        @elseif($isBaTest)
                            <span class="badge bg-success"><i class="ti ti-check me-1"></i>NO</span>
                            <span class="small text-muted ms-2">{{ __('No deficiency') }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================================================= --}}
    {{-- SECTION 6: NON-INTERLOCKED LC VOICE RECORDING (Page 4)                    --}}
    {{-- ========================================================================= --}}
    <div class="card border-0 shadow-sm mb-0" style="border-radius: 10px; overflow: hidden; {{ !$isLcVoice ? 'opacity: 0.9;' : '' }}">
        <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between" style="background-color: #1e3c72; color: #fff;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-danger text-white rounded-pill px-2 py-1 fw-bold fs-6">6</span>
                <h6 class="mb-0 fw-bold text-danger text-uppercase letter-spacing-1" style="color: #ff4d4f !important;">
                    {{ __('NON-INTERLOCKED LC VOICE RECORDING') }}
                </h6>
            </div>
            @if($isLcVoice)
                <span class="badge bg-info text-white small">{{ __('Active in this Record') }}</span>
            @else
                <span class="badge bg-white bg-opacity-25 text-white small">{{ __('Module Reference') }}</span>
            @endif
        </div>
        <div class="card-body p-3">
            <div class="row g-3">
                {{-- Context --}}
                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Date of Voice Recording') }}</label>
                    <div class="p-2 bg-white rounded border fw-semibold text-dark" style="min-height: 40px;">
                        {{ $ambush->date_of_voice_recording ?? '-' }}
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Block Section') }}</label>
                    <div class="p-2 bg-white rounded border fw-semibold text-dark" style="min-height: 40px;">
                        {{ $ambush->block_section ?? '-' }}
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('LC Gate No.') }}</label>
                    <div class="p-2 bg-white rounded border fw-semibold text-dark" style="min-height: 40px;">
                        {{ $ambush->lc_gate_no ?? '-' }}
                    </div>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Train No.') }}</label>
                    <div class="p-2 bg-white rounded border fw-semibold text-dark" style="min-height: 40px;">
                        {{ $ambush->train_no ?? '-' }}
                    </div>
                </div>

                {{-- Train Information Voice Clip --}}
                <div class="col-md-6 col-sm-12">
                    <label class="text-muted small fw-semibold d-block">{{ __('Train Information Voice Clip') }}</label>
                    <div class="p-2 bg-white rounded border" style="min-height: 40px;">
                        <span class="small text-muted me-2">Time: <strong>{{ $ambush->voice_recording_time_train ?? '-' }}</strong></span>
                        @if($ambush->voice_recording_train)
                            <a href="{{ asset(Storage::url($ambush->voice_recording_train)) }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2">
                                <i class="ti ti-volume me-1"></i>{{ __('Play / Download Audio') }}
                            </a>
                        @else
                            <span class="badge bg-light text-muted border">No audio clip</span>
                        @endif
                    </div>
                </div>

                {{-- LC Closure Voice Clip --}}
                <div class="col-md-6 col-sm-12">
                    <label class="text-muted small fw-semibold d-block">{{ __('LC Closure Voice Clip') }}</label>
                    <div class="p-2 bg-white rounded border" style="min-height: 40px;">
                        <span class="small text-muted me-2">Time: <strong>{{ $ambush->voice_recording_time_lc ?? '-' }}</strong></span>
                        @if($ambush->voice_recording_lc)
                            <a href="{{ asset(Storage::url($ambush->voice_recording_lc)) }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2">
                                <i class="ti ti-volume me-1"></i>{{ __('Play / Download Audio') }}
                            </a>
                        @else
                            <span class="badge bg-light text-muted border">No audio clip</span>
                        @endif
                    </div>
                </div>

                {{-- Train Movement Times --}}
                <div class="col-md-6 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Actual Departure Time From Station') }}</label>
                    <div class="p-2 bg-white rounded border fw-semibold text-dark" style="min-height: 40px;">
                        {{ $ambush->actual_departure_time ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6 col-sm-6">
                    <label class="text-muted small fw-semibold d-block">{{ __('Actual Arrival Time At Next Station') }}</label>
                    <div class="p-2 bg-white rounded border fw-semibold text-dark" style="min-height: 40px;">
                        {{ $ambush->actual_arrival_time ?? '-' }}
                    </div>
                </div>

                {{-- Review Any Major Deficiency --}}
                <div class="col-12">
                    <label class="text-muted small fw-semibold d-block">{{ __('Any Major Deficiency (LC Voice Recording)') }}</label>
                    <div class="p-2 bg-white rounded border" style="min-height: 40px;">
                        @if($isLcVoice && $hasDeficiency)
                            <span class="badge bg-danger"><i class="ti ti-alert-triangle me-1"></i>YES</span>
                            <span class="small text-danger ms-2">{{ $ambush->write_deficiency ?? 'Deficiency recorded.' }}</span>
                        @elseif($isLcVoice)
                            <span class="badge bg-success"><i class="ti ti-check me-1"></i>NO</span>
                            <span class="small text-muted ms-2">{{ __('No deficiency') }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Modal Footer --}}
<div class="modal-footer bg-white border-top px-4 py-3 d-flex justify-content-between align-items-center">
    <div class="text-muted small">
        <i class="ti ti-info-circle me-1"></i>{{ __('Generated from Saathi Ambush Check Records System') }}
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
            <i class="ti ti-printer me-1"></i>{{ __('Print') }}
        </button>
        <a href="{{ route('report.ambush.export.ambush-pdf', $ambush->id) }}" class="btn btn-outline-danger" target="_blank">
            <i class="ti ti-file-text me-1"></i>{{ __('Download Dossier PDF') }}
        </a>
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
            {{ __('Close') }}
        </button>
    </div>
</div>
