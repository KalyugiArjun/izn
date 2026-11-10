@extends('layouts.app')

@push('css-page')
    <style>
        .btn-check-pill {
            display: none;
        }
        .btn-pill-label {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 24px;
            border-radius: 30px;
            border: 2px solid #dee2e6;
            background-color: #fff;
            color: #495057;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            gap: 8px;
            user-select: none;
        }
        .btn-pill-label:hover {
            background-color: #f8f9fa;
            border-color: #ced4da;
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
@endpush

@section('page-title')
    {{ __('TI HQ Daily Movement Cum Inspection') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Master') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('master.ti_daily_inspection.index') }}">{{ __('TI Daily Movement Cum Inspection') }}</a></li>
    <li class="breadcrumb-item" aria-current="page">{{ __('New Entry') }}</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-primary text-white py-3 rounded-top-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 text-white"><i class="ti ti-clipboard-list me-2"></i>{{ __('TI HQ Daily Movement Cum Inspection') }}</h5>
                        <small class="text-white-50">{{ __('TI Daily Movement Cum Inspection Form') }}</small>
                    </div>
                    <a href="{{ route('master.ti_daily_inspection.index') }}" class="btn btn-sm btn-light-secondary"><i class="ti ti-arrow-left"></i> Back to List</a>
                </div>
                <div class="card-body p-4">
                    {{ Form::open(['route' => 'master.ti_daily_inspection.store', 'method' => 'POST']) }}
                    
                    <!-- Basic Information Section -->
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3"><i class="ti ti-info-circle me-1"></i>{{ __('Basic Information') }}</h6>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-bold" for="date_of_inspection">{{ __('Date of Inspection') }}</label>
                                <input type="date" name="date_of_inspection" id="date_of_inspection" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-bold" for="station_filter">{{ __('Filter by Station') }}</label>
                                <select id="station_filter" class="form-select select2">
                                    <option value="">-- {{ __('Select Station') }} --</option>
                                    @foreach($stations as $st)
                                        <option value="{{ $st->id }}">{{ strtoupper($st->name) }} ({{ strtoupper($st->code) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-bold" for="created_by">{{ __('Submitted By (Employee)') }}</label>
                                <select name="created_by" id="created_by" class="form-select select2" required>
                                    <option value="">-- {{ __('Select Employee') }} --</option>
                                    @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->mobile }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-bold" for="designation">{{ __('Designation') }}</label>
                                <input type="text" name="designation" id="designation" list="designation_list" class="form-control" placeholder="e.g. TI IZN" required autocomplete="off">
                                <datalist id="designation_list">
                                    @foreach($designations as $desg)
                                        <option value="{{ $desg->name }}">
                                    @endforeach
                                </datalist>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-bold" for="movement">{{ __('Movement') }}</label>
                                <select name="movement" id="movement" class="form-select" required>
                                    <option value="">-- {{ __('Select Movement') }} --</option>
                                    @foreach($movements as $mov)
                                        <option value="{{ $mov->name }}">{{ $mov->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-bold" for="type_of_inspection">{{ __('Type of Inspection') }}</label>
                                <input type="text" name="type_of_inspection" id="type_of_inspection" list="type_of_inspection_list" class="form-control" placeholder="e.g. Footplate" autocomplete="off">
                                <datalist id="type_of_inspection_list">
                                    @foreach($type_of_inspections as $toi)
                                        <option value="{{ $toi->name }}">
                                    @endforeach
                                </datalist>
                            </div>
                        </div>

                        <!-- Day / Night Toggle -->
                        <div class="mb-3">
                            <label class="form-label d-block font-bold">{{ __('Day / Night') }}</label>
                            <div class="d-inline-flex gap-2">
                                <input type="radio" class="btn-check-pill" name="day_night" id="day" value="DAY" checked>
                                <label class="btn-pill-label" for="day"><i class="ti ti-sun"></i> DAY</label>

                                <input type="radio" class="btn-check-pill" name="day_night" id="night" value="NIGHT">
                                <label class="btn-pill-label" for="night"><i class="ti ti-moon"></i> NIGHT</label>
                            </div>
                        </div>
                    </div>

                    <!-- Ambush Check Details Section -->
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3"><i class="ti ti-eye me-1"></i>{{ __('Ambush Check Details') }}</h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-bold" for="type_of_ambush_check">{{ __('Type of Ambush Check') }}</label>
                                <input type="text" name="type_of_ambush_check" id="type_of_ambush_check" list="type_of_ambush_list" class="form-control" placeholder="e.g. Shunting" autocomplete="off">
                                <datalist id="type_of_ambush_list">
                                    @foreach($type_of_ambushes as $toa)
                                        <option value="{{ $toa->name }}">
                                    @endforeach
                                </datalist>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-bold" for="station_yard">{{ __('Station / Yard') }}</label>
                                <input type="text" name="station_yard" id="station_yard" list="station_list" class="form-control" placeholder="e.g. KHUDAGANJ" autocomplete="off">
                                <datalist id="station_list">
                                    @foreach($stations as $st)
                                        <option value="{{ $st->name }}">
                                    @endforeach
                                </datalist>
                            </div>
                        </div>
                    </div>

                    <!-- Inspection Specific Details Section -->
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3"><i class="ti ti-settings me-1"></i>{{ __('Inspection Specific Details') }}</h6>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-bold" for="level_crossing_no">{{ __('Level Crossing No.') }}</label>
                                <input type="text" name="level_crossing_no" id="level_crossing_no" class="form-control" placeholder="e.g. LC-45">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-bold" for="train_number">{{ __('Train Number') }}</label>
                                <input type="text" name="train_number" id="train_number" class="form-control" placeholder="e.g. 12345">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label font-bold" for="section">{{ __('Section') }}</label>
                                <input type="text" name="section" id="section" class="form-control" placeholder="e.g. Enter section">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-bold" for="line_number_load_stable">{{ __('Line Number (Load Stable)') }}</label>
                                <input type="text" name="line_number_load_stable" id="line_number_load_stable" class="form-control" placeholder="e.g. L-5">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-bold" for="type_of_load_load_stable">{{ __('Type of Load (Load Stable)') }}</label>
                                <input type="text" name="type_of_load_load_stable" id="type_of_load_load_stable" class="form-control" placeholder="e.g. EP 22944 + 42 BCNE + 01 BV">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-bold" for="t806_number">{{ __('T/806 Number (Shunting)') }}</label>
                                <input type="text" name="t806_number" id="t806_number" class="form-control" placeholder="e.g. Shunting order no.">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-bold" for="shunting_observation_of">{{ __('Shunting Observation Of') }}</label>
                                <input type="text" name="shunting_observation_of" id="shunting_observation_of" class="form-control" placeholder="e.g. Train no. / load name">
                            </div>
                        </div>

                        {{-- ===== Shunting Staff Section ===== --}}
                        <div class="border rounded-3 p-3 mb-3 bg-light" id="shunting-staff-section">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-primary mb-0"><i class="ti ti-users me-1"></i>{{ __('Shunting Staff Names') }}</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="add-staff-btn">
                                    <i class="ti ti-plus me-1"></i>{{ __('Add More Staff') }}
                                </button>
                            </div>
                            <div id="shunting-staff-list">
                                {{-- First Staff Row (always visible) --}}
                                <div class="row shunting-staff-row mb-2 align-items-end">
                                    <div class="col-md-5">
                                        <label class="form-label">{{ __('Staff Name') }}</label>
                                        <input type="text" name="shunting_staff[0][name]" class="form-control" placeholder="{{ __('Enter staff name') }}">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">{{ __('Designation') }}</label>
                                        <input type="text" name="shunting_staff[0][designation]" list="designation_list" class="form-control" placeholder="{{ __('e.g. P. Way Mate') }}" autocomplete="off">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label d-block opacity-0">x</label>
                                        <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-staff-btn" style="display:none;">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Deficiencies & Action Section -->
                    <div class="mb-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3"><i class="ti ti-alert-triangle me-1"></i>{{ __('Deficiencies & Action') }}</h6>
                        
                        <div class="mb-3">
                            <label class="form-label font-bold" for="major_deficiencies">{{ __('Major Deficiencies Observed') }}</label>
                            <textarea name="major_deficiencies" id="major_deficiencies" class="form-control" rows="3" placeholder="Enter deficiencies or N/A"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-bold" for="action_taken">{{ __('Action Taken') }}</label>
                                <input type="text" name="action_taken" id="action_taken" class="form-control" placeholder="e.g. Issue was fixed on the spot">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-bold" for="remark">{{ __('Remark (if any)') }}</label>
                                <input type="text" name="remark" id="remark" class="form-control" placeholder="Additional remarks">
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <a href="{{ route('master.ti_daily_inspection.index') }}" class="btn btn-light-secondary me-2">{{ __('Cancel') }}</a>
                        <button type="submit" class="btn btn-primary">{{ __('Save Entry') }}</button>
                    </div>

                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ===== Station Employee Filter =====
            const stationFilter = document.getElementById('station_filter');
            const employeeSelect = document.getElementById('created_by');

            if (stationFilter && employeeSelect) {
                $(stationFilter).select2({
                    width: '100%'
                });
                $(employeeSelect).select2({
                    width: '100%',
                    placeholder: "-- Select Employee --"
                });

                $(stationFilter).on('change', function () {
                    const stationId = this.value;
                    fetch(`{{ route('employees.filter') }}?station_id=${stationId}`)
                        .then(res => res.json())
                        .then(data => {
                            $(employeeSelect).empty().append('<option value="">-- Select Employee --</option>');
                            data.forEach(emp => {
                                $(employeeSelect).append(`<option value="${emp.id}">${emp.name} (${emp.mobile})</option>`);
                            });
                            $(employeeSelect).trigger('change');
                        });
                });
            }

            // ===== Day/Night Theme Toggle =====
            const card = document.querySelector('.card');
            const dayNightRadios = document.querySelectorAll('input[name="day_night"]');

            function updateCardTheme() {
                const checkedRadio = document.querySelector('input[name="day_night"]:checked');
                if (checkedRadio && checkedRadio.value === 'NIGHT') {
                    card.classList.add('theme-night');
                } else {
                    card.classList.remove('theme-night');
                }
            }

            dayNightRadios.forEach(radio => {
                radio.addEventListener('change', updateCardTheme);
            });

            updateCardTheme();

            // ===== Dynamic Shunting Staff Rows =====
            let staffCount = 1;
            const staffList = document.getElementById('shunting-staff-list');
            const addStaffBtn = document.getElementById('add-staff-btn');

            function getDesignationOptions() {
                const datalist = document.getElementById('designation_list');
                return datalist ? datalist.innerHTML : '';
            }

            addStaffBtn.addEventListener('click', function () {
                const index = staffCount++;
                const row = document.createElement('div');
                row.className = 'row shunting-staff-row mb-2 align-items-end';
                row.innerHTML = `
                    <div class="col-md-5">
                        <label class="form-label">{{ __('Staff Name') }}</label>
                        <input type="text" name="shunting_staff[${index}][name]" class="form-control" placeholder="{{ __('Enter staff name') }}">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">{{ __('Designation') }}</label>
                        <input type="text" name="shunting_staff[${index}][designation]" list="designation_list" class="form-control" placeholder="{{ __('e.g. P. Way Mate') }}" autocomplete="off">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label d-block opacity-0">x</label>
                        <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-staff-btn">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                `;
                staffList.appendChild(row);
                updateRemoveButtons();
            });

            staffList.addEventListener('click', function (e) {
                if (e.target.closest('.remove-staff-btn')) {
                    const row = e.target.closest('.shunting-staff-row');
                    if (row) row.remove();
                    updateRemoveButtons();
                }
            });

            function updateRemoveButtons() {
                const rows = staffList.querySelectorAll('.shunting-staff-row');
                rows.forEach((row, i) => {
                    const removeBtn = row.querySelector('.remove-staff-btn');
                    if (removeBtn) {
                        removeBtn.style.display = rows.length > 1 ? 'block' : 'none';
                    }
                });
            }

            updateRemoveButtons();
        });
    </script>
@endpush
