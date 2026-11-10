{{ Form::model($user, ['route' => ['users.update', encrypt($user->id)], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
<div class="modal-body">
    <div class="row">
        @if (\Auth::user()->type != 'super admin')
            <div class="form-group col-md-6">
                {{ Form::label('role', __('Assign Role') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
                {!! Form::select('role', $userRoles, !empty($user->roles) ? $user->roles[0]->id : null, [
                    'class' => 'form-control select2',
                    'required' => 'required',
                ]) !!}
            </div>
        @endif

        {{-- Department --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('department_id', __('Department') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::select(
                'department_id',
                ['' => '-- Select Department --'] + $departments->toArray(),
                $user->employee->department_id ?? null,
                [
                    'class' => 'form-select',
                    'required' => true,
                    'id' => 'edit_department_id',
                ],
            ) }}
        </div>

        {{-- Designation --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('designation_id', __('Designation') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::select(
                'designation_id',
                ['' => '-- Select Designation --'] + $designations->toArray(),
                $user->employee->designation_id ?? null,
                [
                    'class' => 'form-select',
                    'required' => true,
                    'id' => 'edit_designation_id',
                ],
            ) }}
        </div>

        {{-- Station filter --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('edit_station_filter_id', __('Station / Working Place'), ['class' => 'form-label']) }}
            @php
                $allStations = \App\Models\Station::where('status', 1)->orderBy('name')->get();
                $associatedStationIds = $user->employee ? $user->employee->stations->pluck('id')->toArray() : [];
                $initialStationId = reset($associatedStationIds) ?: '';
            @endphp
            <select id="edit_station_filter_id" class="form-select select2">
                <option value="">{{ __('Select Station') }}</option>
                @foreach($allStations as $st)
                    <option value="{{ $st->id }}" {{ $st->id == $initialStationId ? 'selected' : '' }}>{{ strtoupper($st->name) }} ({{ strtoupper($st->code) }})</option>
                @endforeach
            </select>
        </div>
        {{-- Employee --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('employee_id', __('Employee') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            @php
                $linkedEmp = $user->resolved_employee ?? $user->employee ?? $user->masterEmployee;
            @endphp
            {{ Form::select(
                'employee_id',
                $linkedEmp ? [$linkedEmp->id => $linkedEmp->name] : [],
                $linkedEmp ? $linkedEmp->id : ($user->employee_id ?? null),
                [
                    'class' => 'form-select select2',
                    'required' => true,
                    'id' => 'edit_employee_id',
                ],
            ) }}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('email', __('Email') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('Enter Email'), 'required' => 'required',   'readonly' => true  ]) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('phone_number', __('Phone Number'), ['class' => 'form-label'], false) }}
            {{ Form::text('phone_number', null, ['class' => 'form-control', 'placeholder' => __('Enter Phone Number') ,   'readonly' => true]) }}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('is_active', __('User Status'), ['class' => 'form-label']) }}
            {{ Form::select('is_active', ['1' => 'Active', '0' => 'Inactive'], $user->is_active, ['class' => 'form-control']) }}
        </div>

        <div
            class="form-group {{ \Auth::user()->type == 'super admin' ? 'col-md-6 col-lg-6' : 'col-md-12 col-lg-12' }} mb-3">
            {{ Form::label('profile', __('Profile'), ['class' => 'form-label']) }}
            <div class="d-flex align-items-center gap-3">
                <div class="position-relative flex-shrink-0">
                    @php
                        $profilePath = 'app/public/upload/profile/' . $user->profile;
                        $publicPath = 'storage/upload/profile/' . $user->profile;
                        $profileExists = !empty($user->profile) && (file_exists(storage_path($profilePath)) || file_exists(public_path($publicPath)));
                    @endphp
                    @if($profileExists)
                        <img src="{{ asset(Storage::url('upload/profile')) . '/' . $user->profile }}" 
                             alt="" 
                             class="rounded-circle border" 
                             style="width: 58px; height: 58px; min-width: 58px; min-height: 58px; max-width: 58px; max-height: 58px; object-fit: cover; border-color: #cbd5e1 !important; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                    @else
                        @php
                            $initials = $user->getInitials();
                            $colorIndex = (ord($initials[0] ?? 'A') + ord($initials[1] ?? 'B')) % 8 + 1;
                        @endphp
                        <span class="initials-avatar avatar-lg bg-avatar-{{ $colorIndex }}">{{ $initials }}</span>
                    @endif
                </div>
                <div class="flex-grow-1">
                    {{ Form::file('profile', ['class' => 'form-control']) }}
                    <small class="text-muted mt-1 d-block" style="font-size: 0.75rem;">{{ __('Upload a new image to replace the current one.') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    {{ Form::submit(__('Update'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>
{{ Form::close() }}

<script>
    var editEmployeesData = window.editEmployeesData || {};
    window.editEmployeesData = editEmployeesData;

    $(document).ready(function() {

        // Initialize Select2
        $('#edit_employee_id').select2({
            placeholder: "Select Employee",
            allowClear: true,
            width: '100%',
            dropdownParent: $('#customModal')
        });

        $('#edit_department_id, #edit_designation_id, #edit_station_filter_id').select2({
            width: '100%',
            dropdownParent: $('#customModal')
        });

        // On department change → reload designations
        $('#edit_department_id').on('change', function() {
            const deptId = $(this).val();
            const desigSelect = $('#edit_designation_id');

            desigSelect.empty().append('<option value="">Select Designation</option>');
            $('#edit_employee_id').empty().append('<option value="">Select Employee</option>');

            if (!deptId) return;

            fetch(`{{ route('authority.complain.designations') }}?department_id=${deptId}`)
                .then(res => res.json())
                .then(data => {
                    $.each(data, function(id, name) {
                        desigSelect.append(`<option value="${id}">${name}</option>`);
                    });
                    desigSelect.trigger('change');
                });
        });

        // On designation or station change → reload employees
        $('#edit_designation_id, #edit_station_filter_id').on('change', function() {
            loadEditEmployees();
        });

        function loadEditEmployees() {
            const department_id = $('#edit_department_id').val();
            const designation_id = $('#edit_designation_id').val();
            const station_id = $('#edit_station_filter_id').val() || '';

            if (!department_id || !designation_id) return;

            fetch(
                    `{{ route('employees.filter') }}?department_id=${department_id}&designation_id=${designation_id}&station_id=${station_id}`
                )
                .then(res => res.json())
                .then(data => {
                    const empSelect = $('#edit_employee_id');
                    const currentEmployeeId = "{{ $user->employee_id ?? '' }}";

                    empSelect.empty().append('<option value="">Select Employee</option>');
                    editEmployeesData = {};

                    $.each(data, function(index, emp) {
                        editEmployeesData[emp.id] = emp;
                        const selected = emp.id == currentEmployeeId ? 'selected' : '';
                        empSelect.append(
                            `<option value="${emp.id}" ${selected}>${emp.name}</option>`);
                    });

                    empSelect.trigger('change');
                });
        }

        // ✅ On page load: pre-populate designations & employees based on existing user data
        const userData = @json($user);
        const initialDeptId = userData.department_id;
        const initialDesigId = userData.designation;
        const initialEmpId = userData.name;
        const initialStationId = "{{ $initialStationId }}";

        if (initialDeptId) {
            console.log("this section is working");

            // Set department manually (important)
            $('#edit_department_id').val(initialDeptId).trigger('change');

            fetch(`{{ route('authority.complain.designations') }}?department_id=${initialDeptId}`)
                .then(res => res.json())
                .then(data => {

                    const desigSelect = $('#edit_designation_id');
                    desigSelect.empty().append('<option value="">Select Designation</option>');

                    $.each(data, function(id, name) {
                        desigSelect.append(`<option value="${id}">${name}</option>`);
                    });

                    // ✅ Set designation AFTER loading
                    desigSelect.val(initialDesigId).trigger('change');

                    if (initialDesigId) {
                        fetch(
                                `{{ route('employees.filter') }}?department_id=${initialDeptId}&designation_id=${initialDesigId}&station_id=${initialStationId}`
                            )
                            .then(res => res.json())
                            .then(data => {
                                const empSelect = $('#edit_employee_id');
                                empSelect.empty().append('<option value="">Select Employee</option>');

                                $.each(data, function(index, emp) {
                                    editEmployeesData[emp.id] = emp;
                                    empSelect.append(
                                        `<option value="${emp.id}">${emp.name}</option>`);
                                });

                                // ✅ Set employee AFTER loading
                                empSelect.val(initialEmpId).trigger('change');
                            });
                    }
                });
        }

    });
    $('#edit_employee_id').on('change', function() {

        let empId = $(this).val();

        if (!empId || !editEmployeesData[empId]) {
            $('#email').val('');
            $('#phone_number').val('');
            return;
        }

        let emp = editEmployeesData[empId];

        $('#email').val(emp.email || '');
        $('#phone_number').val(emp.mobile || '');
    });
</script>

