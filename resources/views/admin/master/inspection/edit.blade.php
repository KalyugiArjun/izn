@extends('layouts.app')
@section('page-title')
    {{ __('Edit Inspection') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Master') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('master.inspection.index') }}">{{ __('Inspection List') }}</a></li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Edit') }}</li>
@endsection
@section('content')
    <style>
        .toggle-group-pills {
            display: flex;
            gap: 12px;
            margin-top: 5px;
        }
        .btn-check-pill {
            display: none !important;
        }
        .btn-pill-label {
            border-radius: 30px;
            padding: 8px 24px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            border: 2px solid #e0e0e0;
            background-color: #fcfcfc;
            color: #666;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-pill-label:hover {
            border-color: #bbb;
        }
        /* Day - Yellow */
        .btn-check-pill[value="DAY"]:checked + .btn-pill-label {
            border-color: #ffc107;
            background-color: #ffc107;
            color: #212529 !important;
        }
        /* Night - Black */
        .btn-check-pill[value="NIGHT"]:checked + .btn-pill-label {
            border-color: #212529;
            background-color: #212529;
            color: #ffffff !important;
        }
        /* Yes - Green */
        .btn-check-pill[value="Yes"]:checked + .btn-pill-label,
        .btn-check-pill[value="Positive"]:checked + .btn-pill-label {
            border-color: #198754;
            background-color: #198754;
            color: #ffffff !important;
        }
        /* No - Red */
        .btn-check-pill[value="No"]:checked + .btn-pill-label,
        .btn-check-pill[value="Negative"]:checked + .btn-pill-label {
            border-color: #dc3545;
            background-color: #dc3545;
            color: #ffffff !important;
        }
        
        /* Night Mode Theme styles */
        .card {
            transition: background-color 0.4s ease, color 0.4s ease, border-color 0.4s ease;
        }
        .card.theme-night {
            background-color: #0f172a !important; /* midnight slate blue */
            color: #f8fafc !important;
            border: 1px solid #1e293b !important;
        }
        .card.theme-night h5,
        .card.theme-night h6,
        .card.theme-night label,
        .card.theme-night legend {
            color: #f1f5f9 !important;
        }
        .card.theme-night .text-primary {
            color: #38bdf8 !important; /* brighter sky blue for labels/headings in dark mode */
        }
        .card.theme-night .border-bottom {
            border-bottom-color: #334155 !important;
        }
        .card.theme-night .form-control,
        .card.theme-night .form-select {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
        }
        .card.theme-night .form-control::placeholder {
            color: #64748b !important;
        }
        .card.theme-night .card-header {
            background-color: #1e293b !important; /* slightly lighter than body to look premium */
            border-bottom: 1px solid #334155 !important;
        }
        .card.theme-night .btn-pill-label {
            background-color: #1e293b !important;
            border-color: #475569 !important;
            color: #cbd5e1 !important;
        }
        .card.theme-night .btn-check-pill[value="NIGHT"]:checked + .btn-pill-label {
            background-color: #38bdf8 !important; /* bright color in dark mode */
            border-color: #38bdf8 !important;
            color: #0f172a !important;
        }
    </style>
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-primary text-white py-3 rounded-top-4 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('master.inspection.index') }}" class="btn btn-sm btn-light me-3 font-bold text-primary">
                            <i class="ti ti-arrow-left"></i> {{ __('Back') }}
                        </a>
                        <h5 class="mb-0 text-white"><i class="ti ti-clipboard-list me-2"></i>{{ __('Edit Inspection Entry') }}</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    {!! Form::model($inspection, ['route' => ['master.inspection.update', encrypt($inspection->id)], 'method' => 'POST', 'id' => 'inspectionForm']) !!}
                    
                    <!-- Basic Information Section -->
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3"><i class="ti ti-info-circle me-1"></i>{{ __('Basic Information') }}</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label font-bold" for="date_of_inspection">{{ __('Date of Inspection') }} <span class="text-danger">*</span></label>
                                <input type="date" name="date_of_inspection" id="date_of_inspection" class="form-control" value="{{ $inspection->date_of_inspection }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold" for="location">{{ __('Your Current Location') }}</label>
                                <div class="input-group">
                                    <input type="text" name="location" id="location" class="form-control" value="{{ $inspection->location }}" placeholder="e.g. 37.4219983,-122.084">
                                    <button class="btn btn-outline-secondary" type="button" id="btn-get-location">
                                        <i class="ti ti-map-pin"></i> Get Location
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold" for="officer_name">{{ __('Name of the Officer') }} <span class="text-danger">*</span></label>
                                <input type="text" name="officer_name" id="officer_name" list="officers_list" class="form-control" value="{{ $inspection->officer_name }}" required autocomplete="off">
                                <datalist id="officers_list">
                                    @foreach($officers as $off)
                                        <option value="{{ $off->name }}" data-designation="{{ $off->designationData->name ?? $off->designation ?? 'TRAFFIC INSPECTOR HQ' }}">
                                    @endforeach
                                </datalist>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold" for="designation">{{ __('Designation') }} <span class="text-danger">*</span></label>
                                <input type="text" name="designation" id="designation" class="form-control" value="{{ $inspection->designation }}" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label font-bold" for="movement_type">{{ __('Movement') }} <span class="text-danger">*</span></label>
                                <select name="movement_type" id="movement_type" class="form-select" required>
                                    <option value="">-- {{ __('Select Movement') }} --</option>
                                    @foreach($movements as $m)
                                        <option value="{{ strtoupper($m->name) }}" {{ strtoupper($inspection->movement_type) === strtoupper($m->name) ? 'selected' : '' }}>{{ $m->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- CONDITIONAL SECTIONS -->

                    <!-- 1. REGULAR INSPECTION SECTION -->
                    @php
                        $reg = $inspection->regularInspection;
                    @endphp
                    <div id="sec-regular-inspection" class="movement-section d-none mb-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3"><i class="ti ti-checkbox me-1"></i>{{ __('Regular Inspection Details') }}</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label font-bold">{{ __('Day / Night') }}</label>
                                <div class="toggle-group-pills">
                                    <input type="radio" class="btn-check-pill" name="reg_day_night" id="reg_day" value="DAY" {{ (!$reg || $reg->day_night === 'DAY') ? 'checked' : '' }}>
                                    <label class="btn-pill-label" for="reg_day">
                                        <i class="ti ti-sun me-1"></i> {{ __('DAY') }}
                                    </label>
                                    <input type="radio" class="btn-check-pill" name="reg_day_night" id="reg_night" value="NIGHT" {{ ($reg && $reg->day_night === 'NIGHT') ? 'checked' : '' }}>
                                    <label class="btn-pill-label" for="reg_night">
                                        <i class="ti ti-moon me-1"></i> {{ __('NIGHT') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold" for="reg_type_of_inspection">{{ __('Type of Inspection') }}</label>
                                <select name="reg_type_of_inspection" id="reg_type_of_inspection" class="form-select">
                                    <option value="">-- {{ __('Select Type') }} --</option>
                                    @foreach($type_of_inspections as $toi)
                                        <option value="{{ $toi->name }}" {{ ($reg && $reg->type_of_inspection === $toi->name) ? 'selected' : '' }}>{{ $toi->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Regular Inspection Dynamic Sub-Fields -->
                            <div id="reg-sub-fields" class="col-12 d-none">
                                <div class="row g-3">
                                    <div class="col-md-6 reg-field" id="reg-field-station">
                                        <label class="form-label font-bold" for="reg_station_name">{{ __('Station Name') }}</label>
                                        <select name="reg_station_name" id="reg_station_name" class="form-select">
                                            <option value="">-- {{ __('Select Station') }} --</option>
                                            @foreach($stations as $st)
                                                <option value="{{ $st->name }}" {{ ($reg && $reg->station_name === $st->name) ? 'selected' : '' }}>{{ strtoupper($st->name) }} ({{ strtoupper($st->code) }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 reg-field" id="reg-field-gate">
                                        <label class="form-label font-bold" for="reg_lc_gate_no">{{ __('LC Gate No') }}</label>
                                        <input type="text" name="reg_lc_gate_no" id="reg_lc_gate_no" class="form-control" value="{{ $reg ? $reg->lc_gate_no : '' }}" placeholder="e.g. 54">
                                    </div>
                                    <div class="col-md-6 reg-field" id="reg-field-gate-type">
                                        <label class="form-label font-bold" for="reg_gate_type">{{ __('Gate Type') }}</label>
                                        <select name="reg_gate_type" id="reg_gate_type" class="form-select">
                                            <option value="">-- {{ __('Select Gate Type') }} --</option>
                                            <option value="ENGG" {{ ($reg && $reg->gate_type === 'ENGG') ? 'selected' : '' }}>ENGG</option>
                                            <option value="TRAFFIC" {{ ($reg && $reg->gate_type === 'TRAFFIC') ? 'selected' : '' }}>TRAFFIC</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 reg-field" id="reg-field-block-section">
                                        <label class="form-label font-bold" for="reg_block_section">{{ __('Block Section') }}</label>
                                        <input type="text" name="reg_block_section" id="reg_block_section" class="form-control" value="{{ $reg ? $reg->block_section : '' }}" placeholder="e.g. block section">
                                    </div>
                                    <div class="col-md-6 reg-field" id="reg-field-train-no">
                                        <label class="form-label font-bold" for="reg_train_no">{{ __('Train No') }}</label>
                                        <input type="text" name="reg_train_no" id="reg_train_no" class="form-control" value="{{ $reg ? $reg->train_no : '' }}" placeholder="e.g. 19225">
                                    </div>
                                    <div class="col-md-6 reg-field" id="reg-field-train-type">
                                        <label class="form-label font-bold" for="reg_train_type">{{ __('Train Type') }}</label>
                                        <select name="reg_train_type" id="reg_train_type" class="form-select">
                                            <option value="">-- {{ __('Select Train Type') }} --</option>
                                            <option value="GOODS" {{ ($reg && $reg->train_type === 'GOODS') ? 'selected' : '' }}>GOODS</option>
                                            <option value="PASSENGER" {{ ($reg && $reg->train_type === 'PASSENGER') ? 'selected' : '' }}>PASSENGER</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 reg-field" id="reg-field-lobby">
                                        <label class="form-label font-bold" for="reg_lobby_location">{{ __('Lobby Location') }}</label>
                                        <select name="reg_lobby_location" id="reg_lobby_location" class="form-select">
                                            <option value="">-- {{ __('Select Location') }} --</option>
                                            @foreach($diesel_lobbies as $dl)
                                                <option value="{{ $dl->name }}" {{ ($reg && $reg->lobby_location === $dl->name) ? 'selected' : '' }}>{{ $dl->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 reg-field" id="reg-field-running-room">
                                        <label class="form-label font-bold" for="reg_running_room_location">{{ __('Running Room Location') }}</label>
                                        <select name="reg_running_room_location" id="reg_running_room_location" class="form-select">
                                            <option value="">-- {{ __('Select Location') }} --</option>
                                            @foreach($running_room_locations as $rr)
                                                <option value="{{ $rr->name }}" {{ ($reg && $reg->running_room_location === $rr->name) ? 'selected' : '' }}>{{ $rr->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Regular Inspection Deficiency Check -->
                            <div class="col-12">
                                <label class="form-label font-bold">{{ __('Any Major Deficiency Found During Inspection Check?') }}</label>
                                <div class="toggle-group-pills">
                                    <input type="radio" class="btn-check-pill" name="reg_is_deficient" id="reg_is_deficient_no" value="No" {{ (!$reg || !$reg->is_deficient) ? 'checked' : '' }}>
                                    <label class="btn-pill-label" for="reg_is_deficient_no">
                                        <i class="ti ti-circle-x me-1"></i> {{ __('No') }}
                                    </label>
                                    <input type="radio" class="btn-check-pill" name="reg_is_deficient" id="reg_is_deficient_yes" value="Yes" {{ ($reg && $reg->is_deficient) ? 'checked' : '' }}>
                                    <label class="btn-pill-label" for="reg_is_deficient_yes">
                                        <i class="ti ti-circle-check me-1"></i> {{ __('Yes') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 d-none" id="reg-deficiency-textarea">
                                <label class="form-label font-bold" for="reg_deficiency_text">{{ __('Write Deficiency') }}</label>
                                <textarea name="reg_deficiency_text" id="reg_deficiency_text" rows="3" class="form-control" placeholder="e.g. Describe the deficiency found...">{{ $reg ? $reg->deficiency_text : '' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- 2. AMBUSH CHECK SECTION -->
                    @php
                        $amb = $inspection->ambushInspection;
                    @endphp
                    <div id="sec-ambush-check" class="movement-section d-none mb-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3"><i class="ti ti-eye me-1"></i>{{ __('Ambush Check Details') }}</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label font-bold" for="amb_station_name">{{ __('Station / Place') }}</label>
                                <select name="amb_station_name" id="amb_station_name" class="form-select">
                                    <option value="">-- {{ __('Select Station') }} --</option>
                                    @foreach($stations as $st)
                                        <option value="{{ $st->name }}" {{ ($amb && $amb->station_name === $st->name) ? 'selected' : '' }}>{{ strtoupper($st->name) }} ({{ strtoupper($st->code) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label font-bold" for="amb_from_time">{{ __('From Time') }}</label>
                                <input type="time" name="amb_from_time" id="amb_from_time" class="form-control" value="{{ $amb ? $amb->from_time : '' }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label font-bold" for="amb_to_time">{{ __('To Time') }}</label>
                                <input type="time" name="amb_to_time" id="amb_to_time" class="form-control" value="{{ $amb ? $amb->to_time : '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold">{{ __('Day / Night') }}</label>
                                <div class="toggle-group-pills">
                                    <input type="radio" class="btn-check-pill" name="amb_day_night" id="amb_day" value="DAY" {{ (!$amb || $amb->day_night === 'DAY') ? 'checked' : '' }}>
                                    <label class="btn-pill-label" for="amb_day">
                                        <i class="ti ti-sun me-1"></i> {{ __('DAY') }}
                                    </label>
                                    <input type="radio" class="btn-check-pill" name="amb_day_night" id="amb_night" value="NIGHT" {{ ($amb && $amb->day_night === 'NIGHT') ? 'checked' : '' }}>
                                    <label class="btn-pill-label" for="amb_night">
                                        <i class="ti ti-moon me-1"></i> {{ __('NIGHT') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold" for="amb_type_of_ambush">{{ __('Type of Ambush Check') }}</label>
                                <select name="amb_type_of_ambush" id="amb_type_of_ambush" class="form-select">
                                    <option value="">-- {{ __('Select Type') }} --</option>
                                    @php
                                        $ambTypes = $type_of_ambushes->pluck('name')->toArray();
                                        if (empty($ambTypes)) {
                                            $ambTypes = [
                                                'Shunting',
                                                'Load Stable',
                                                'Engineering Block',
                                                'Checking of Non-Interlocked LC Voice Recordings',
                                                'BA Test'
                                            ];
                                        }
                                    @endphp
                                    @foreach($ambTypes as $name)
                                        <option value="{{ $name }}" {{ ($amb && $amb->type_of_ambush === $name) ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Ambush Check Type-specific Fields -->
                            <div id="amb-sub-fields" class="col-12 d-none">
                                <!-- 2.1 Shunting Sub-Fields -->
                                <div id="amb-field-shunting" class="amb-field-group d-none row g-3">
                                    <div class="col-12">
                                        <label class="form-label font-bold" for="amb_shunting_observation">{{ __('Shunting Observation Of') }}</label>
                                        <textarea name="amb_shunting_observation" id="amb_shunting_observation" rows="3" class="form-control" placeholder="e.g. Write shunting observations">{{ $amb ? $amb->shunting_observation : '' }}</textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label font-bold" for="amb_t_806_no">{{ __('T/806 No') }}</label>
                                        <input type="text" name="amb_t_806_no" id="amb_t_806_no" class="form-control" placeholder="e.g. T/806-1234" value="{{ $amb ? $amb->t_806_no : '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label font-bold" for="amb_shunting_staff_designation">{{ __('Shunting Staff Designation') }}</label>
                                        <select name="amb_shunting_staff_designation" id="amb_shunting_staff_designation" class="form-select">
                                            <option value="">-- {{ __('Select Designation') }} --</option>
                                            <option value="LP/Loco Shunter" {{ ($amb && $amb->shunting_staff_designation === 'LP/Loco Shunter') ? 'selected' : '' }}>LP/Loco Shunter</option>
                                            <option value="Shunting Master" {{ ($amb && $amb->shunting_staff_designation === 'Shunting Master') ? 'selected' : '' }}>Shunting Master</option>
                                            <option value="Points-man" {{ ($amb && $amb->shunting_staff_designation === 'Points-man') ? 'selected' : '' }}>Points-man</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label font-bold" for="amb_shunting_staff_name">{{ __('Shunting Staff Name') }}</label>
                                        <input type="text" name="amb_shunting_staff_name" id="amb_shunting_staff_name" list="shunting_staff_list" class="form-control" placeholder="e.g. Search Staff Name" value="{{ $amb ? $amb->shunting_staff_name : '' }}" autocomplete="off">
                                        <datalist id="shunting_staff_list">
                                            @foreach($employees as $emp)
                                                <option value="{{ $emp->name }}" data-designation-id="{{ $emp->designation_id }}">
                                            @endforeach
                                        </datalist>
                                    </div>
                                </div>

                                <!-- 2.2 Load Stable Sub-Fields -->
                                <div id="amb-field-load-stable" class="amb-field-group d-none row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label font-bold" for="amb_load_type">{{ __('Select Load Type') }}</label>
                                        <select name="amb_load_type" id="amb_load_type" class="form-select">
                                            <option value="">-- {{ __('Select Load Type') }} --</option>
                                            <option value="Loco/Self-Propelled Engine" {{ ($amb && $amb->load_type === 'Loco/Self-Propelled Engine') ? 'selected' : '' }}>Loco/Self-Propelled Engine</option>
                                            <option value="Track Machine" {{ ($amb && $amb->load_type === 'Track Machine') ? 'selected' : '' }}>Track Machine</option>
                                            <option value="Coaching Rake with Loco" {{ ($amb && $amb->load_type === 'Coaching Rake with Loco') ? 'selected' : '' }}>Coaching Rake with Loco</option>
                                            <option value="Coaching Rake without Loco" {{ ($amb && $amb->load_type === 'Coaching Rake without Loco') ? 'selected' : '' }}>Coaching Rake without Loco</option>
                                            <option value="Goods Rake with Loco" {{ ($amb && $amb->load_type === 'Goods Rake with Loco') ? 'selected' : '' }}>Goods Rake with Loco</option>
                                            <option value="Goods Rake without Loco" {{ ($amb && $amb->load_type === 'Goods Rake without Loco') ? 'selected' : '' }}>Goods Rake without Loco</option>
                                            <option value="Mix Load with Loco" {{ ($amb && $amb->load_type === 'Mix Load with Loco') ? 'selected' : '' }}>Mix Load with Loco</option>
                                            <option value="Mix Load without Loco" {{ ($amb && $amb->load_type === 'Mix Load without Loco') ? 'selected' : '' }}>Mix Load without Loco</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 d-none" id="div-amb-loco-engine">
                                        <label class="form-label font-bold" for="amb_loco_engine_type">{{ __('Loco Engine Type') }}</label>
                                        <select name="amb_loco_engine_type" id="amb_loco_engine_type" class="form-select">
                                            <option value="Light Engine" {{ ($amb && $amb->loco_engine_type === 'Light Engine') ? 'selected' : '' }}>Light Engine</option>
                                            <option value="Couple/Multiple Engine" {{ ($amb && $amb->loco_engine_type === 'Couple/Multiple Engine') ? 'selected' : '' }}>Couple/Multiple Engine</option>
                                            <option value="Self-Propelled Engine" {{ ($amb && $amb->loco_engine_type === 'Self-Propelled Engine') ? 'selected' : '' }}>Self-Propelled Engine</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 d-none" id="div-amb-track-machine">
                                        <label class="form-label font-bold" for="amb_track_machine_type">{{ __('Track Machine Type') }}</label>
                                        <select name="amb_track_machine_type" id="amb_track_machine_type" class="form-select">
                                            <option value="Single Machine" {{ ($amb && $amb->track_machine_type === 'Single Machine') ? 'selected' : '' }}>Single Machine</option>
                                            <option value="Multiple Machine" {{ ($amb && $amb->track_machine_type === 'Multiple Machine') ? 'selected' : '' }}>Multiple Machine</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label font-bold" for="amb_total_load">{{ __('Total Number of Load with Type') }}</label>
                                        <input type="number" name="amb_total_load" id="amb_total_load" class="form-control" placeholder="e.g. 5" value="{{ $amb ? $amb->total_load : '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label font-bold" for="amb_stabling_line_no">{{ __('Stabling Line No') }}</label>
                                        <select name="amb_stabling_line_no" id="amb_stabling_line_no" class="form-select">
                                            <option value="">-- {{ __('Select Line') }} --</option>
                                            @for($i=1; $i<=30; $i++)
                                                <option value="Line {{ $i }}" {{ ($amb && $amb->stabling_line_no === "Line $i") ? 'selected' : '' }}>Line {{ $i }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>

                                <!-- 2.3 Engineering Block Sub-Fields -->
                                <div id="amb-field-engg-block" class="amb-field-group d-none row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label font-bold" for="amb_block_place_type">{{ __('Place of Ambush Check') }}</label>
                                        <select name="amb_block_place_type" id="amb_block_place_type" class="form-select">
                                            <option value="Station Section" {{ ($amb && $amb->block_place_type === 'Station Section') ? 'selected' : '' }}>Station Section</option>
                                            <option value="Block Section" {{ ($amb && $amb->block_place_type === 'Block Section') ? 'selected' : '' }}>Block Section</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6" id="div-amb-block-station">
                                        <label class="form-label font-bold" for="amb_block_station_name">{{ __('Station Name') }}</label>
                                        <select name="amb_block_station_name" id="amb_block_station_name" class="form-select">
                                            <option value="">-- {{ __('Select Station') }} --</option>
                                            @foreach($stations as $st)
                                                <option value="{{ $st->name }}" {{ ($amb && $amb->block_station_name === $st->name) ? 'selected' : '' }}>{{ strtoupper($st->name) }} ({{ strtoupper($st->code) }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 d-none" id="div-amb-block-section">
                                        <label class="form-label font-bold" for="amb_block_section_name">{{ __('Enter Block Section') }}</label>
                                        <input type="text" name="amb_block_section_name" id="amb_block_section_name" class="form-control" placeholder="e.g. BE-MB block section" value="{{ $amb ? $amb->block_section_name : '' }}">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label font-bold" for="amb_block_taken_by">{{ __('Block Taken By') }}</label>
                                        <select name="amb_block_taken_by" id="amb_block_taken_by" class="form-select">
                                            <option value="">-- {{ __('Select Taken By') }} --</option>
                                            <option value="Track Machine" {{ ($amb && $amb->block_taken_by === 'Track Machine') ? 'selected' : '' }}>Track Machine</option>
                                            <option value="DMT" {{ ($amb && $amb->block_taken_by === 'DMT') ? 'selected' : '' }}>DMT</option>
                                            <option value="PQRS" {{ ($amb && $amb->block_taken_by === 'PQRS') ? 'selected' : '' }}>PQRS</option>
                                            <option value="Track Machine with Tower Wagon" {{ ($amb && $amb->block_taken_by === 'Track Machine with Tower Wagon') ? 'selected' : '' }}>Track Machine with Tower Wagon</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- 2.4 Voice Recording Sub-Fields -->
                                <div id="amb-field-voice-recording" class="amb-field-group d-none row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label font-bold" for="amb_recording_date">{{ __('Date of Voice Recording') }}</label>
                                        <input type="date" name="amb_recording_date" id="amb_recording_date" class="form-control" value="{{ $amb ? $amb->recording_date : '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label font-bold" for="amb_recording_station">{{ __('Station') }}</label>
                                        <select name="amb_recording_station" id="amb_recording_station" class="form-select">
                                            <option value="">-- {{ __('Select Station') }} --</option>
                                            @foreach($stations as $st)
                                                <option value="{{ $st->name }}" {{ ($amb && $amb->recording_station === $st->name) ? 'selected' : '' }}>{{ strtoupper($st->name) }} ({{ strtoupper($st->code) }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label font-bold" for="amb_recording_section">{{ __('Block Section') }}</label>
                                        <input type="text" name="amb_recording_section" id="amb_recording_section" class="form-control" placeholder="e.g. block section" value="{{ $amb ? $amb->recording_section : '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label font-bold" for="amb_recording_gate_no">{{ __('LC Gate No') }}</label>
                                        <input type="text" name="amb_recording_gate_no" id="amb_recording_gate_no" class="form-control" placeholder="e.g. 54" value="{{ $amb ? $amb->recording_gate_no : '' }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label font-bold" for="amb_recording_train_no">{{ __('Train No') }}</label>
                                        <input type="text" name="amb_recording_train_no" id="amb_recording_train_no" class="form-control" placeholder="e.g. 19225" value="{{ $amb ? $amb->recording_train_no : '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label font-bold" for="amb_voice_recording_train_time">{{ __('Voice Recording Timing (For Information of Train)') }}</label>
                                        <input type="time" name="amb_voice_recording_train_time" id="amb_voice_recording_train_time" class="form-control" value="{{ $amb ? $amb->voice_recording_train_time : '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label font-bold" for="amb_voice_recording_lc_time">{{ __('Voice Recording Timing (For Closure of LC)') }}</label>
                                        <input type="time" name="amb_voice_recording_lc_time" id="amb_voice_recording_lc_time" class="form-control" value="{{ $amb ? $amb->voice_recording_lc_time : '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label font-bold" for="amb_actual_departure_time">{{ __('Actual Departure Time from Station') }}</label>
                                        <input type="time" name="amb_actual_departure_time" id="amb_actual_departure_time" class="form-control" value="{{ $amb ? $amb->actual_departure_time : '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label font-bold" for="amb_actual_arrival_time">{{ __('Actual Arrival Time at Next Station') }}</label>
                                        <input type="time" name="amb_actual_arrival_time" id="amb_actual_arrival_time" class="form-control" value="{{ $amb ? $amb->actual_arrival_time : '' }}">
                                    </div>
                                </div>

                                <!-- 2.5 BA Test Sub-Fields -->
                                <div id="amb-field-ba-test" class="amb-field-group d-none row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label font-bold" for="amb_ba_staff_name">{{ __('Staff Name') }}</label>
                                        <input type="text" name="amb_ba_staff_name" id="amb_ba_staff_name" class="form-control" placeholder="e.g. Staff Name" value="{{ $amb ? $amb->ba_staff_name : '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label font-bold" for="amb_ba_designation">{{ __('Designation') }}</label>
                                        <input type="text" name="amb_ba_designation" id="amb_ba_designation" class="form-control" placeholder="e.g. Designation" value="{{ $amb ? $amb->ba_designation : '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label font-bold">{{ __('Alcohol Test Result') }}</label>
                                        <div class="toggle-group-pills">
                                            <input type="radio" class="btn-check-pill" name="amb_ba_result" id="amb_ba_result_neg" value="Negative" {{ (!$amb || $amb->ba_result === 'Negative') ? 'checked' : '' }}>
                                            <label class="btn-pill-label" for="amb_ba_result_neg">
                                                <i class="ti ti-circle-check me-1"></i> Negative
                                            </label>
                                            <input type="radio" class="btn-check-pill" name="amb_ba_result" id="amb_ba_result_pos" value="Positive" {{ ($amb && $amb->ba_result === 'Positive') ? 'checked' : '' }}>
                                            <label class="btn-pill-label" for="amb_ba_result_pos">
                                                <i class="ti ti-circle-x me-1"></i> Positive
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 d-none" id="div-amb-ba-percentage">
                                        <label class="form-label font-bold" for="amb_ba_alcohol_percentage">{{ __('Alcohol %') }}</label>
                                        <input type="number" step="0.01" name="amb_ba_alcohol_percentage" id="amb_ba_alcohol_percentage" class="form-control" placeholder="e.g. 0.05" value="{{ $amb ? $amb->ba_alcohol_percentage : '' }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Ambush Check Deficiency Check -->
                            <div class="col-12">
                                <label class="form-label font-bold">{{ __('Any Major Deficiency Found During Ambush Check?') }}</label>
                                <div class="toggle-group-pills">
                                    <input type="radio" class="btn-check-pill" name="amb_is_deficient" id="amb_is_deficient_no" value="No" {{ (!$amb || !$amb->is_deficient) ? 'checked' : '' }}>
                                    <label class="btn-pill-label" for="amb_is_deficient_no">
                                        <i class="ti ti-circle-x me-1"></i> {{ __('No') }}
                                    </label>
                                    <input type="radio" class="btn-check-pill" name="amb_is_deficient" id="amb_is_deficient_yes" value="Yes" {{ ($amb && $amb->is_deficient) ? 'checked' : '' }}>
                                    <label class="btn-pill-label" for="amb_is_deficient_yes">
                                        <i class="ti ti-circle-check me-1"></i> {{ __('Yes') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 d-none" id="amb-deficiency-textarea">
                                <label class="form-label font-bold" for="amb_deficiency_text">{{ __('Write Deficiency') }}</label>
                                <textarea name="amb_deficiency_text" id="amb_deficiency_text" rows="3" class="form-control" placeholder="e.g. Describe the deficiency found...">{{ $amb ? $amb->deficiency_text : '' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- 3. SAFETY DRIVE SECTION -->
                    <div id="sec-safety-drive" class="movement-section d-none mb-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3"><i class="ti ti-shield me-1"></i>{{ __('Safety Drive Details') }}</h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label font-bold" for="safety_drive_type">{{ __('Type of Inspection') }}</label>
                                <select name="safety_drive_type" id="safety_drive_type" class="form-select">
                                    <option value="">-- {{ __('Select Type') }} --</option>
                                    <option value="OPERATING SAFETY DRIVE" {{ $inspection->safety_drive_type === 'OPERATING SAFETY DRIVE' ? 'selected' : '' }}>OPERATING SAFETY DRIVE</option>
                                    <option value="SAFETY DRIVE" {{ $inspection->safety_drive_type === 'SAFETY DRIVE' ? 'selected' : '' }}>SAFETY DRIVE</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 4. JOINT SIGNAL SIGHTING SECTION -->
                    <div id="sec-joint-signal-sighting" class="movement-section d-none mb-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3"><i class="ti ti-crosshair me-1"></i>{{ __('Joint Signal Sighting Details') }}</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label font-bold">{{ __('Train Type') }}</label>
                                <div class="mt-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="simple_train_type" id="jss_goods" value="GOODS" {{ $inspection->train_type === 'GOODS' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="jss_goods">{{ __('GOODS') }}</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="simple_train_type" id="jss_passenger" value="PASSENGER" {{ $inspection->train_type === 'PASSENGER' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="jss_passenger">{{ __('PASSENGER') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-bold" for="simple_train_no">{{ __('Train No') }}</label>
                                <input type="text" name="simple_train_no" id="simple_train_no" class="form-control" value="{{ $inspection->train_no }}" placeholder="e.g. 19225">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label font-bold" for="simple_block_section">{{ __('Block Section') }}</label>
                                <input type="text" name="simple_block_section" id="simple_block_section" class="form-control" value="{{ $inspection->block_section }}" placeholder="e.g. block section">
                            </div>
                        </div>
                    </div>

                    <!-- 5. NI DUTY SECTION -->
                    <div id="sec-ni-duty" class="movement-section d-none mb-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3"><i class="ti ti-settings me-1"></i>{{ __('Ni Duty Details') }}</h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label font-bold" for="simple_station_name_ni">{{ __('Station Name') }}</label>
                                <select name="simple_station_name_ni" id="simple_station_name_ni" class="form-select">
                                    <option value="">-- {{ __('Choose Station') }} --</option>
                                    @foreach($stations as $st)
                                        <option value="{{ $st->name }}" {{ $inspection->station_name === $st->name ? 'selected' : '' }}>{{ strtoupper($st->name) }} ({{ strtoupper($st->code) }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 6. JOINT NOTE PREPRATION SECTION -->
                    <div id="sec-joint-note-prep" class="movement-section d-none mb-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3"><i class="ti ti-notes me-1"></i>{{ __('Joint Note Preparation Details') }}</h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label font-bold" for="simple_station_name_jnp">{{ __('Station Name') }}</label>
                                <select name="simple_station_name_jnp" id="simple_station_name_jnp" class="form-select">
                                    <option value="">-- {{ __('Select Station') }} --</option>
                                    @foreach($stations as $st)
                                        <option value="{{ $st->name }}" {{ $inspection->station_name === $st->name ? 'selected' : '' }}>{{ strtoupper($st->name) }} ({{ strtoupper($st->code) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label font-bold" for="simple_reason_jnp">{{ __('Reason') }}</label>
                                <textarea name="simple_reason_jnp" id="simple_reason_jnp" rows="3" class="form-control" placeholder="e.g. Reason ..!">{{ $inspection->reason }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- 7. SURVEY SECTION -->
                    <div id="sec-survey" class="movement-section d-none mb-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3"><i class="ti ti-map-2 me-1"></i>{{ __('Survey Details') }}</h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label font-bold" for="simple_station_name_srv">{{ __('Station Name') }}</label>
                                <select name="simple_station_name_srv" id="simple_station_name_srv" class="form-select">
                                    <option value="">-- {{ __('Select Station') }} --</option>
                                    @foreach($stations as $st)
                                        <option value="{{ $st->name }}" {{ $inspection->station_name === $st->name ? 'selected' : '' }}>{{ strtoupper($st->name) }} ({{ strtoupper($st->code) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label font-bold" for="simple_reason_srv">{{ __('Reason') }}</label>
                                <textarea name="simple_reason_srv" id="simple_reason_srv" rows="3" class="form-control" placeholder="e.g. Describe survey details...">{{ $inspection->reason }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- 8. ATTEND BLOCK SECTION -->
                    <div id="sec-attend-block" class="movement-section d-none mb-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3"><i class="ti ti-tool me-1"></i>{{ __('Attend Block Details') }}</h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label font-bold" for="simple_attend_block_type">{{ __('Select block activity') }}</label>
                                <select name="simple_attend_block_type" id="simple_attend_block_type" class="form-select">
                                    <option value="">-- {{ __('Select Type') }} --</option>
                                    @foreach($attend_blocks as $ab)
                                        <option value="{{ $ab->name }}" {{ $inspection->attend_block_type === $ab->name ? 'selected' : '' }}>{{ $ab->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 9. ON LEAVE SECTION -->
                    <div id="sec-on-leave" class="movement-section d-none mb-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3"><i class="ti ti-calendar-off me-1"></i>{{ __('Leave Details') }}</h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label font-bold" for="simple_leave_type">{{ __('Select Leave Type') }}</label>
                                <select name="simple_leave_type" id="simple_leave_type" class="form-select">
                                    <option value="">-- {{ __('Select Leave Type') }} --</option>
                                    @foreach($type_of_leaves as $tol)
                                        <option value="{{ $tol->name }}" {{ $inspection->leave_type === $tol->name ? 'selected' : '' }}>{{ $tol->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="text-end pt-3 border-top mt-4">
                        <a href="{{ route('master.inspection.index') }}" class="btn btn-light-secondary me-2">{{ __('Cancel') }}</a>
                        <button type="submit" class="btn btn-primary px-4" id="btnSubmit">{{ __('SUBMIT') }}</button>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Script for Dynamic Form Logic -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Officer Selection auto-fills Designation from datalist
            const txtOfficer = document.getElementById('officer_name');
            const txtDesignation = document.getElementById('designation');
            const officersDatalist = document.getElementById('officers_list');
            if (txtOfficer && txtDesignation && officersDatalist) {
                txtOfficer.addEventListener('input', function () {
                    const val = this.value;
                    let foundDesignation = '';
                    const options = officersDatalist.options;
                    for (let i = 0; i < options.length; i++) {
                        if (options[i].value.toLowerCase() === val.toLowerCase()) {
                            foundDesignation = options[i].getAttribute('data-designation') || '';
                            break;
                        }
                    }
                    if (foundDesignation) {
                        txtDesignation.value = foundDesignation;
                    }
                });
            }

            // Shunting Staff Name Filter based on Designation
            const selShuntingDesg = document.getElementById('amb_shunting_staff_designation');
            const txtShuntingName = document.getElementById('amb_shunting_staff_name');
            const shuntingDatalist = document.getElementById('shunting_staff_list');
            if (selShuntingDesg && txtShuntingName && shuntingDatalist) {
                const allNameOptions = Array.from(shuntingDatalist.querySelectorAll('option'));

                function filterShuntingNames(isInitial = false) {
                    const desg = selShuntingDesg.value;
                    if (!isInitial) {
                        txtShuntingName.value = '';
                    }
                    shuntingDatalist.innerHTML = '';

                    let targetIds = [];
                    if (desg === 'LP/Loco Shunter') {
                        targetIds = [73, 74];
                    } else if (desg === 'Shunting Master') {
                        targetIds = [67];
                    } else if (desg === 'Points-man') {
                        targetIds = [63];
                    }

                    let count = 0;
                    allNameOptions.forEach(opt => {
                        const optDesgId = parseInt(opt.getAttribute('data-designation-id'));
                        if (targetIds.includes(optDesgId)) {
                            const newOpt = opt.cloneNode(true);
                            shuntingDatalist.appendChild(newOpt);
                            count++;
                        }
                    });

                    if (desg && count === 0) {
                        txtShuntingName.placeholder = 'No staff on this designation';
                    } else {
                        txtShuntingName.placeholder = 'e.g. Search Staff Name';
                    }
                }

                selShuntingDesg.addEventListener('change', function() {
                    filterShuntingNames(false);
                });
                // Run initially if designation is pre-selected
                filterShuntingNames(true);
            }

            // Geolocation logic
            const btnGetLocation = document.getElementById('btn-get-location');
            const txtLocation = document.getElementById('location');
            
            if (btnGetLocation && txtLocation) {
                btnGetLocation.addEventListener('click', function () {
                    if (navigator.geolocation) {
                        btnGetLocation.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Fetching...';
                        btnGetLocation.disabled = true;
                        navigator.geolocation.getCurrentPosition(function (position) {
                            txtLocation.value = position.coords.latitude + ',' + position.coords.longitude;
                            btnGetLocation.innerHTML = '<i class="ti ti-map-pin"></i> Get Location';
                            btnGetLocation.disabled = false;
                        }, function (error) {
                            alert('Geolocation Error: ' + error.message);
                            btnGetLocation.innerHTML = '<i class="ti ti-map-pin"></i> Get Location';
                            btnGetLocation.disabled = false;
                        });
                    } else {
                        alert('Geolocation is not supported by this browser.');
                    }
                });
            }

            // Movement Type Change Handler
            const selMovement = document.getElementById('movement_type');
            const sections = document.querySelectorAll('.movement-section');

            function handleMovementChange() {
                // Hide all sections first
                sections.forEach(sec => sec.classList.add('d-none'));

                const val = selMovement.value;
                if (!val) return;

                if (val === 'REGULAR INSPECTION') {
                    document.getElementById('sec-regular-inspection').classList.remove('d-none');
                } else if (val === 'AMBUSH CHECK') {
                    document.getElementById('sec-ambush-check').classList.remove('d-none');
                } else if (val === 'SAFETY DRIVE') {
                    document.getElementById('sec-safety-drive').classList.remove('d-none');
                } else if (val === 'JOINT SIGNAL SIGHTING') {
                    document.getElementById('sec-joint-signal-sighting').classList.remove('d-none');
                } else if (val === 'NI DUTY') {
                    document.getElementById('sec-ni-duty').classList.remove('d-none');
                } else if (val === 'JOINT NOTE PREPRATION') {
                    document.getElementById('sec-joint-note-prep').classList.remove('d-none');
                } else if (val === 'SURVEY') {
                    document.getElementById('sec-survey').classList.remove('d-none');
                } else if (val === 'ATTEND BLOCK') {
                    document.getElementById('sec-attend-block').classList.remove('d-none');
                } else if (val === 'ON LEAVE') {
                    document.getElementById('sec-on-leave').classList.remove('d-none');
                }
            }

            if (selMovement) {
                selMovement.addEventListener('change', handleMovementChange);
            }

            // Sync hidden inputs of other simple dropdowns
            const niSelect = document.getElementById('simple_station_name_ni');
            const jnpSelect = document.getElementById('simple_station_name_jnp');
            const srvSelect = document.getElementById('simple_station_name_srv');
            
            // Create a hidden input to send simple station name
            const hiddenStation = document.createElement('input');
            hiddenStation.type = 'hidden';
            hiddenStation.name = 'simple_station_name';
            hiddenStation.value = "{{ $inspection->station_name }}";
            document.getElementById('inspectionForm').appendChild(hiddenStation);

            function syncStationValue() {
                const val = selMovement.value;
                if (val === 'NI DUTY') {
                    hiddenStation.value = niSelect.value;
                } else if (val === 'JOINT NOTE PREPRATION') {
                    hiddenStation.value = jnpSelect.value;
                } else if (val === 'SURVEY') {
                    hiddenStation.value = srvSelect.value;
                } else {
                    hiddenStation.value = '';
                }
            }
            [niSelect, jnpSelect, srvSelect].forEach(select => {
                if (select) select.addEventListener('change', syncStationValue);
            });

            // Sync reason inputs
            const jnpReason = document.getElementById('simple_reason_jnp');
            const srvReason = document.getElementById('simple_reason_srv');
            const hiddenReason = document.createElement('input');
            hiddenReason.type = 'hidden';
            hiddenReason.name = 'simple_reason';
            hiddenReason.value = "{{ $inspection->reason }}";
            document.getElementById('inspectionForm').appendChild(hiddenReason);

            function syncReasonValue() {
                const val = selMovement.value;
                if (val === 'JOINT NOTE PREPRATION') {
                    hiddenReason.value = jnpReason.value;
                } else if (val === 'SURVEY') {
                    hiddenReason.value = srvReason.value;
                } else {
                    hiddenReason.value = '';
                }
            }
            [jnpReason, srvReason].forEach(el => {
                if (el) el.addEventListener('input', syncReasonValue);
            });

            // ----------------------------------------------------------------
            // 1. Regular Inspection Type of Inspection field handler
            // ----------------------------------------------------------------
            const selRegType = document.getElementById('reg_type_of_inspection');
            const regSubFieldsDiv = document.getElementById('reg-sub-fields');
            const regFields = document.querySelectorAll('.reg-field');

            function handleRegTypeChange() {
                regFields.forEach(f => f.classList.add('d-none'));
                regSubFieldsDiv.classList.add('d-none');

                const val = selRegType.value;
                if (!val) return;

                regSubFieldsDiv.classList.remove('d-none');

                // Check conditions (case-insensitive check)
                const upperVal = val.toUpperCase();
                if (['THOROUGH', 'SAFETY', 'SURPRISE', 'NIGHT INSPECTION'].includes(upperVal)) {
                    document.getElementById('reg-field-station').classList.remove('d-none');
                }
                if (['SURPRISE', 'NIGHT INSPECTION', 'LC GATE', 'LC AMBUSH'].includes(upperVal)) {
                    document.getElementById('reg-field-gate').classList.remove('d-none');
                }
                if (upperVal === 'LC GATE') {
                    document.getElementById('reg-field-gate-type').classList.remove('d-none');
                }
                if (['LC GATE', 'LC AMBUSH', 'FOOTPLATE', 'BRAKE-VAN'].includes(upperVal)) {
                    document.getElementById('reg-field-block-section').classList.remove('d-none');
                }
                if (['LC AMBUSH', 'FOOTPLATE', 'BRAKE-VAN'].includes(upperVal)) {
                    document.getElementById('reg-field-train-no').classList.remove('d-none');
                }
                if (['FOOTPLATE', 'BRAKE-VAN'].includes(upperVal)) {
                    document.getElementById('reg-field-train-type').classList.remove('d-none');
                }
                if (upperVal === 'DIESEL LOBBY') {
                    document.getElementById('reg-field-lobby').classList.remove('d-none');
                }
                if (upperVal === 'RUNNING ROOM') {
                    document.getElementById('reg-field-running-room').classList.remove('d-none');
                }
            }

            if (selRegType) {
                selRegType.addEventListener('change', handleRegTypeChange);
            }

            // Deficiency Textarea Toggle for Regular Inspection
            const regDefTextareaDiv = document.getElementById('reg-deficiency-textarea');
            if (regDefTextareaDiv) {
                function toggleRegDeficiency() {
                    const active = document.querySelector('input[name="reg_is_deficient"]:checked');
                    if (active && active.value === 'Yes') {
                        regDefTextareaDiv.classList.remove('d-none');
                    } else {
                        regDefTextareaDiv.classList.add('d-none');
                    }
                }
                document.querySelectorAll('input[name="reg_is_deficient"]').forEach(radio => {
                    radio.addEventListener('change', toggleRegDeficiency);
                });
                toggleRegDeficiency(); // Run on load
            }

            // ----------------------------------------------------------------
            // 2. Ambush Check Type of Ambush field handler
            // ----------------------------------------------------------------
            const selAmbType = document.getElementById('amb_type_of_ambush');
            const ambSubFieldsDiv = document.getElementById('amb-sub-fields');
            const ambFieldGroups = document.querySelectorAll('.amb-field-group');

            function handleAmbTypeChange() {
                ambFieldGroups.forEach(g => g.classList.add('d-none'));
                ambSubFieldsDiv.classList.add('d-none');

                const val = selAmbType.value;
                if (!val) return;

                ambSubFieldsDiv.classList.remove('d-none');

                if (val === 'Shunting') {
                    document.getElementById('amb-field-shunting').classList.remove('d-none');
                } else if (val === 'Load Stable') {
                    document.getElementById('amb-field-load-stable').classList.remove('d-none');
                } else if (val === 'Engineering Block') {
                    document.getElementById('amb-field-engg-block').classList.remove('d-none');
                } else if (val === 'Checking of Non-Interlocked LC Voice Recordings') {
                    document.getElementById('amb-field-voice-recording').classList.remove('d-none');
                } else if (val === 'BA Test') {
                    document.getElementById('amb-field-ba-test').classList.remove('d-none');
                }
            }

            if (selAmbType) {
                selAmbType.addEventListener('change', handleAmbTypeChange);
            }

            // Load Stable Sub-toggles
            const selAmbLoadType = document.getElementById('amb_load_type');
            const divLocoEngine = document.getElementById('div-amb-loco-engine');
            const divTrackMachine = document.getElementById('div-amb-track-machine');
            
            function toggleLoadStableSubFields() {
                if (!selAmbLoadType) return;
                divLocoEngine.classList.add('d-none');
                divTrackMachine.classList.add('d-none');
                if (selAmbLoadType.value === 'Loco/Self-Propelled Engine') {
                    divLocoEngine.classList.remove('d-none');
                } else if (selAmbLoadType.value === 'Track Machine') {
                    divTrackMachine.classList.remove('d-none');
                }
            }
            if (selAmbLoadType) {
                selAmbLoadType.addEventListener('change', toggleLoadStableSubFields);
            }

            // Engg Block Place toggles
            const selAmbBlockPlace = document.getElementById('amb_block_place_type');
            const divBlockStation = document.getElementById('div-amb-block-station');
            const divBlockSection = document.getElementById('div-amb-block-section');
            
            function toggleEnggBlockSubFields() {
                if (!selAmbBlockPlace) return;
                divBlockStation.classList.add('d-none');
                divBlockSection.classList.add('d-none');
                if (selAmbBlockPlace.value === 'Station Section') {
                    divBlockStation.classList.remove('d-none');
                } else if (selAmbBlockPlace.value === 'Block Section') {
                    divBlockSection.classList.remove('d-none');
                }
            }
            if (selAmbBlockPlace) {
                selAmbBlockPlace.addEventListener('change', toggleEnggBlockSubFields);
            }

            // BA Test result toggle
            const divBaPercentage = document.getElementById('div-amb-ba-percentage');
            function toggleBaPercentageField() {
                const active = document.querySelector('input[name="amb_ba_result"]:checked');
                if (active && active.value === 'Positive') {
                    divBaPercentage.classList.remove('d-none');
                } else {
                    divBaPercentage.classList.add('d-none');
                }
            }
            document.querySelectorAll('input[name="amb_ba_result"]').forEach(radio => {
                radio.addEventListener('change', toggleBaPercentageField);
            });

            // Deficiency Textarea Toggle for Ambush Check
            const ambDefTextareaDiv = document.getElementById('amb-deficiency-textarea');
            if (ambDefTextareaDiv) {
                function toggleAmbDeficiency() {
                    const active = document.querySelector('input[name="amb_is_deficient"]:checked');
                    if (active && active.value === 'Yes') {
                        ambDefTextareaDiv.classList.remove('d-none');
                    } else {
                        ambDefTextareaDiv.classList.add('d-none');
                    }
                }
                document.querySelectorAll('input[name="amb_is_deficient"]').forEach(radio => {
                    radio.addEventListener('change', toggleAmbDeficiency);
                });
                toggleAmbDeficiency(); // Run on load
            }

            // RUN INITIAL TRIGGERS TO PRE-POPULATE ACTIVE FIELDS ON LOAD
            handleMovementChange();
            handleRegTypeChange();
            handleAmbTypeChange();
            toggleLoadStableSubFields();
            toggleEnggBlockSubFields();
            toggleBaPercentageField();

            // Night mode background toggling
            const card = document.querySelector('.card');
            const dayNightRadios = document.querySelectorAll('input[name="reg_day_night"], input[name="amb_day_night"]');
            const selMovementLocal = document.getElementById('movement_type');
            
            function updateCardTheme() {
                let isNight = false;
                const regSec = document.getElementById('sec-regular-inspection');
                const ambSec = document.getElementById('sec-ambush-check');
                
                if (regSec && !regSec.classList.contains('d-none')) {
                    const checkedReg = document.querySelector('input[name="reg_day_night"]:checked');
                    if (checkedReg && checkedReg.value === 'NIGHT') {
                        isNight = true;
                    }
                } else if (ambSec && !ambSec.classList.contains('d-none')) {
                    const checkedAmb = document.querySelector('input[name="amb_day_night"]:checked');
                    if (checkedAmb && checkedAmb.value === 'NIGHT') {
                        isNight = true;
                    }
                }
                
                if (card) {
                    if (isNight) {
                        card.classList.add('theme-night');
                    } else {
                        card.classList.remove('theme-night');
                    }
                }
            }

            dayNightRadios.forEach(radio => {
                radio.addEventListener('change', updateCardTheme);
            });

            if (selMovementLocal) {
                selMovementLocal.addEventListener('change', function() {
                    setTimeout(updateCardTheme, 50);
                });
            }

            updateCardTheme();
        });
    </script>
@endsection
