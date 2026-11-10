{{ Form::open(['url' => 'users', 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
<div class="modal-body">
    <div class="row">
        @if (\Auth::user()->type != 'super admin')
            <div class="form-group col-md-6">
                {{ Form::label('role', __('Assign Role') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
                {!! Form::select('role', $userRoles, null, ['class' => 'form-control select2', 'required' => 'required']) !!}
            </div>
        @endif
        <div class="form-group col-md-6 mb-3">
            {{ Form::label(
                'department_id',
                __('Department') . '<span class="text-danger"> *</span>',
                ['class' => 'form-label'],
                false,
            ) }}


            {{ Form::select('department_id', ['' => 'Select Department'] + $departments->toArray(), null, [
                'class' => 'form-select',
                'required' => true,
                'id' => 'department_id',
            ]) }}
        </div>

        {{-- Designation --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('designation_id', __('Designation') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}

            {{ Form::select('designation_id', [], null, [
                'class' => 'form-select',
                // 'multiple' => true,
                'required' => true,
                'id' => 'designation_id',
            ]) }}
        </div>
        {{-- Station filter --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('station_filter_id', __('Station / Working Place'), ['class' => 'form-label']) }}
            @php
                $allStations = \App\Models\Station::where('status', 1)->orderBy('name')->get();
            @endphp
            <select id="station_filter_id" class="form-select select2">
                <option value="">{{ __('Select Station') }}</option>
                @foreach($allStations as $st)
                    <option value="{{ $st->id }}">{{ strtoupper($st->name) }} ({{ strtoupper($st->code) }})</option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('employee_id', __('Employee') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}

            {{ Form::select('employee_id', [], null, [
                'class' => 'form-select select2',
                'required' => true,
                'id' => 'employee_id',
            ]) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('email', __('Email') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}

            {{ Form::text('email', null, [
                'class' => 'form-control',
                'id' => 'email', // ✅ correct place
                'placeholder' => __('Enter email'),
                'required' => 'required',
            ]) }}
        </div>

        <div class="form-group col-md-6">
            {{ Form::label('phone_number', __('Phone Number'), ['class' => 'form-label']) }}

            {{ Form::text('phone_number', null, [
                'class' => 'form-control',
                'id' => 'phone_number', // ✅ correct place
                'placeholder' => __('Enter phone number'),
            ]) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('password', __('Password') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::password('password', ['class' => 'form-control', 'placeholder' => __('Enter password'), 'required' => 'required', 'minlength' => '6']) }}
        </div>
        <div
            class="form-group {{ \Auth::user()->type == 'super admin' ? 'col-md-12 col-lg-12' : 'col-md-6 col-lg-6' }}">
            {{ Form::label('profile', __('Profile'), ['class' => 'form-label']) }}
            {{ Form::file('profile', ['class' => 'form-control', 'accept' => '.png', '.jpg', '.jpeg']) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    {{ Form::submit(__('Create'), ['class' => 'btn btn-secondary ml-10']) }}
</div>
{{ Form::close() }}

<script>
    let employeesData = {}; // ✅ GLOBAL (fix)
    $(document).ready(function() {

        $('#employee_id').select2({
            placeholder: "Select Employee",
            allowClear: true,
            width: '100%',
            dropdownParent: $('#customModal') // ⚠️ IMPORTANT (change to your modal ID)
        });

        $('#department_id, #designation_id, #station_filter_id').select2({
            width: '100%',
            dropdownParent: $('#customModal')
        });

    });
    $(document).ready(function() {

        // Load Designations
        $('#department_id').on('change', function() {

            let deptIds = $(this).val(); // array or single
            let desigSelect = $('#designation_id');

            desigSelect.empty().append('<option value="">Select Designation</option>');
            $('#employee_id').empty().append('<option value="">Select Employee</option>');

            if (!deptIds || deptIds.length === 0) return;

            fetch(`{{ route('authority.complain.designations') }}?department_id=${deptIds}`)
                .then(res => res.json())
                .then(data => {

                    $.each(data, function(id, name) {
                        desigSelect.append(`<option value="${id}">${name}</option>`);
                    });

                    desigSelect.trigger('change'); // important for select2
                });
        });

        // Load Employees
        $('#designation_id, #station_filter_id').on('change', function() {
            loadEmployees();
        });

        function loadEmployees() {

            let department_id = $('#department_id').val();
            let designation_id = $('#designation_id').val();
            let station_id = $('#station_filter_id').val() || '';

            if (!department_id || !designation_id) return;

            fetch(
                    `{{ route('employees.filter') }}?department_id=${department_id}&designation_id=${designation_id}&station_id=${station_id}`
                )
                .then(res => res.json())
                .then(data => {

                    let empSelect = $('#employee_id');
                    empSelect.empty().append('<option value="">Select Employee</option>');

                    employeesData = {}; // reset

                    $.each(data, function(index, emp) {

                        // store full data
                        employeesData[emp.id] = emp;

                        empSelect.append(`<option value="${emp.id}">${emp.name}</option>`);
                    });

                    empSelect.trigger('change');
                });
        }

    });
    $('#employee_id').on('change', function() {

        let empId = $(this).val();

        console.log("this is emp data : ", employeesData[empId]);

        if (!empId || !employeesData[empId]) {
            $('#email').val('');
            $('#phone_number').val('');
            return;
        }

        let emp = employeesData[empId];

        $('#email').val(emp.email || '');
        $('#phone_number').val(emp.mobile || '');
    });
</script>

