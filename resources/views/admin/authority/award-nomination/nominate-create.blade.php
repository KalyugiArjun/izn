{{ Form::open(['route' => 'authority.award-nomination.nominate.store', 'method' => 'POST', 'enctype' => 'multipart/form-data', 'id' => 'awardNominationForm']) }}

<div class="modal-body p-4" style="max-height: calc(85vh - 130px); overflow-y: auto;">
    {{-- Section 1: Staff / Nominee Selection --}}
    <div class="alert alert-primary bg-light-primary border-primary border-opacity-25 d-flex align-items-center mb-3 py-2 px-3">
        <i class="ti ti-user-check fs-4 me-2 text-primary"></i>
        <div>
            <strong class="text-primary">Nominee Details</strong>
            <small class="d-block text-muted">Select an employee from the database to auto-populate designation and station details.</small>
        </div>
    </div>

    @if(isset($isStaffUser) && $isStaffUser && isset($currentUserEmployee) && $currentUserEmployee)
        {{-- ================= SELF-NOMINATION SECTION FOR AUTHORIZED STAFF ================= --}}
        <div class="row g-3 mb-3">
            <div class="col-md-12">
                <label class="form-label fw-bold">{{ __('Nominee (Self)') }}</label>
                <input type="hidden" name="staff_id" id="nominee_staff_id" value="{{ $currentUserEmployee->id }}">
                <div class="input-group">
                    <span class="input-group-text bg-light text-primary"><i class="ti ti-user-check fs-5"></i></span>
                    <input type="text" class="form-control bg-light fw-bold text-dark" value="{{ $currentUserEmployee->name }} (USMID: {{ $currentUserEmployee->usmid ?? 'N/A' }}) - {{ $currentUserEmployee->designation->name ?? 'Staff' }}" readonly>
                </div>
                <small class="text-muted mt-1 d-block"><i class="ti ti-info-circle me-1"></i>{{ __('You are submitting this meritorious award nomination for yourself as an authorized employee.') }}</small>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold">{{ __('Designation') }}</label>
                <input type="hidden" name="designation_id" id="nominee_designation_id" value="{{ $currentUserEmployee->designation_id }}">
                <input type="text" class="form-control bg-light" value="{{ $currentUserEmployee->designation->name ?? '-' }}" readonly>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold">{{ __('Station / Location') }}</label>
                @php
                    $empStation = $currentUserEmployee->stations->first();
                    $empStationId = $empStation ? $empStation->id : ($stations->keys()->first() ?? 1);
                    $empStationName = $empStation ? $empStation->name : ($stations->first() ?? '-');
                @endphp
                <input type="hidden" name="station_id" id="nominee_station_id" value="{{ $empStationId }}">
                <input type="text" class="form-control bg-light" value="{{ $empStationName }}" readonly>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold">{{ __('Traffic Inspector (TI)') }}</label>
                <select name="ti_id" id="nominee_ti_id" class="form-select">
                    <option value="">{{ __('-- Auto-Assign TI --') }}</option>
                    @foreach($tis as $tId => $tName)
                        <option value="{{ $tId }}" {{ (isset($defaultTiId) && $defaultTiId == $tId) ? 'selected' : '' }}>{{ $tName }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    @else
        {{-- ================= ADMIN / APPROVER SELECTION (ANY EMPLOYEE) ================= --}}
        <div class="row g-3 mb-3">
            <div class="col-md-12">
                <label class="form-label fw-bold">{{ __('Select Employee / Staff') }} <span class="text-danger">*</span></label>
                <select name="staff_id" id="nominee_staff_id" class="form-select" required>
                    <option value="">{{ __('-- Search Employee by Name or USMID --') }}</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" data-desig="{{ $emp->designation_id }}" data-dept="{{ $emp->department_id }}">
                            {{ $emp->name }} ({{ $emp->usmid ?? 'ID: '.$emp->id }}) - {{ $emp->designation->name ?? 'Designation N/A' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('Designation') }} <span class="text-danger">*</span></label>
                <select name="designation_id" id="nominee_designation_id" class="form-select" required>
                    <option value="">{{ __('-- Select Designation --') }}</option>
                    @foreach($designations as $dId => $dName)
                        <option value="{{ $dId }}">{{ $dName }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('Station / Location') }} <span class="text-danger">*</span></label>
                <select name="station_id" id="nominee_station_id" class="form-select" required>
                    <option value="">{{ __('-- Select Station --') }}</option>
                    @foreach($stations as $sId => $sName)
                        <option value="{{ $sId }}">{{ $sName }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('Traffic Inspector (TI)') }}</label>
                <select name="ti_id" id="nominee_ti_id" class="form-select">
                    <option value="">{{ __('-- Auto-Assign or Select TI --') }}</option>
                    @foreach($tis as $tId => $tName)
                        <option value="{{ $tId }}">{{ $tName }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    @endif

    {{-- Section 2: Achievement & Incident Details --}}
    <div class="alert alert-info bg-light-info border-info border-opacity-25 d-flex align-items-center mb-3 py-2 px-3">
        <i class="ti ti-award fs-4 me-2 text-info"></i>
        <div>
            <strong class="text-info">Merit & Incident Information</strong>
            <small class="d-block text-muted">Provide the date, area of proficiency and justification for this nomination.</small>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label class="form-label">{{ __('Date of Incident / Work') }} <span class="text-danger">*</span></label>
            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">{{ __('Time') }} <span class="text-danger">*</span></label>
            <input type="time" name="time" class="form-control" value="{{ date('H:i') }}" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">{{ __('Area of Proficiency') }} <span class="text-danger">*</span></label>
            <select name="proficiency_id" class="form-select" required>
                <option value="">{{ __('-- Select Proficiency --') }}</option>
                @foreach($proficiencies as $pId => $pName)
                    <option value="{{ $pId }}">{{ $pName }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-12">
            <label class="form-label fw-bold">{{ __('Reason / Description of Meritorious Work') }} <span class="text-danger">* (Compulsory)</span></label>
            <textarea name="description" rows="3" class="form-control" placeholder="Describe the good work done by the staff (e.g., averting hazard, safe train operation, punctuality)..." required minlength="5"></textarea>
            <small class="text-muted">A clear, detailed description is mandatory for all nominations.</small>
        </div>

        <div class="col-md-12">
            <label class="form-label">{{ __('Supporting Document / Evidence') }} <small class="text-muted">(Optional: PDF, JPG, PNG - Max 10MB)</small></label>
            <input type="file" name="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
        </div>
    </div>

    {{-- Section 3: Authority Decision / Stage --}}
    @if(in_array(strtolower(Auth::user()->type ?? ''), ['super admin', 'owner', 'admin']))
    <div class="alert alert-secondary bg-light-secondary border-secondary border-opacity-25 d-flex align-items-center mb-3 py-2 px-3">
        <i class="ti ti-shield-check fs-4 me-2 text-secondary"></i>
        <div>
            <strong class="text-secondary">Administrative Decision / Stage</strong>
            <small class="d-block text-muted">Admin can create as Pending, Recommend via TI, or Directly Award Certificate.</small>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-12">
            <label class="form-label fw-bold">{{ __('Initial Status / Stage') }}</label>
            <div class="d-flex flex-wrap gap-3">
                <div class="form-check">
                    <input class="form-check-input stage-radio" type="radio" name="nomination_stage" id="stage_pending" value="pending" checked>
                    <label class="form-check-label" for="stage_pending">
                        <span class="badge bg-light-warning text-warning border px-2 py-1"><i class="ti ti-clock me-1"></i>Submit as Pending</span>
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input stage-radio" type="radio" name="nomination_stage" id="stage_recommend" value="recommend">
                    <label class="form-check-label" for="stage_recommend">
                        <span class="badge bg-light-info text-info border px-2 py-1"><i class="ti ti-clipboard-check me-1"></i>Recommend by TI</span>
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input stage-radio" type="radio" name="nomination_stage" id="stage_award" value="award">
                    <label class="form-check-label" for="stage_award">
                        <span class="badge bg-light-success text-success border px-2 py-1"><i class="ti ti-certificate me-1"></i>Directly Award Certificate</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Conditional fields for recommendation --}}
        <div class="col-md-12 stage-extra stage-recommend-fields d-none">
            <label class="form-label">{{ __('TI Recommendation Comment') }}</label>
            <input type="text" name="ti_comment" class="form-control" placeholder="e.g. Good Work Done by SM, A Suitable Award may be given.">
        </div>

        {{-- Conditional fields for direct award --}}
        <div class="col-md-6 stage-extra stage-award-fields d-none">
            <label class="form-label">{{ __('Sr. DOM Approver') }}</label>
            <select name="sr_dom_id" class="form-select">
                @foreach($srDoms as $domId => $domName)
                    <option value="{{ $domId }}">{{ $domName }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6 stage-extra stage-award-fields d-none">
            <label class="form-label">{{ __('Sr. DOM Award Remarks') }}</label>
            <input type="text" name="dom_comment" class="form-control" placeholder="e.g. Give Cash Award 500 / Award of Merit">
        </div>
    </div>
    @else
        <input type="hidden" name="nomination_stage" value="pending">
        <div class="alert alert-info py-2 px-3 small d-flex align-items-center">
            <i class="ti ti-info-circle fs-5 me-2 text-info"></i>
            <div>
                Your nomination will be submitted as <strong>Pending</strong> and forwarded automatically to <strong>TI Safety (Designation 78)</strong> for review & recommendation to Sr. DOM.
            </div>
        </div>
    @endif
</div>

<div class="modal-footer bg-light px-4 py-3" style="position: sticky; bottom: 0; z-index: 1050; border-top: 1px solid #e2e8f0; background-color: #f8fafc !important;">
    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    <button type="submit" class="btn btn-primary rounded-pill px-4">
        <i class="ti ti-check me-1"></i> {{ __('Submit Award Nomination') }}
    </button>
</div>

{{ Form::close() }}

<script>
(function() {
    // Stage radio toggle
    $('.stage-radio').on('change', function() {
        const val = $(this).val();
        if (val === 'award') {
            $('.stage-recommend-fields').removeClass('d-none');
            $('.stage-award-fields').removeClass('d-none');
        } else if (val === 'recommend') {
            $('.stage-recommend-fields').removeClass('d-none');
            $('.stage-award-fields').addClass('d-none');
        } else {
            $('.stage-recommend-fields').addClass('d-none');
            $('.stage-award-fields').addClass('d-none');
        }
    });

    // Auto-fetch employee details on employee dropdown change
    $('#nominee_staff_id').on('change', function() {
        const empId = $(this).val();
        if (!empId) return;

        const url = "{{ url('admin/authority/award-nomination/employee-details') }}/" + empId;
        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.success && res.data) {
                    const data = res.data;
                    if (data.designation_id) {
                        $('#nominee_designation_id').val(data.designation_id).trigger('change');
                    }
                    if (data.station_id) {
                        $('#nominee_station_id').val(data.station_id).trigger('change');
                    }
                    if (data.ti_id) {
                        $('#nominee_ti_id').val(data.ti_id).trigger('change');
                    }
                }
            },
            error: function(err) {
                console.warn('Could not auto-fetch employee details', err);
            }
        });
    });
})();
</script>

