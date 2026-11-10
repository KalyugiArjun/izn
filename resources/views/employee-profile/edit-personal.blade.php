{{ Form::open([
    'route' => ['employee-profile.update', encrypt($employee->id), 'personal'],
    'method' => 'POST',
    'id' => 'edit_personal_form',
]) }}

<div class="modal-body">

    <div class="row">

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('name', 'Full Name', ['class' => 'form-label']) }}
            <span class="text-danger">*</span>
            {{ Form::text('name', old('name', $employee->name), [
                'class' => 'form-control',
                'required' => true,
            ]) }}
            @error('name')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('usmid', 'HRMS ID', ['class' => 'form-label']) }}
            {{ Form::text('usmid', old('usmid', $employee->usmid), [
                'class' => 'form-control',
            ]) }}
            @error('usmid')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('department_id', 'Department', ['class' => 'form-label']) }}
            {{ Form::select(
                'department_id',
                $departments->pluck('name', 'id'),
                old('department_id', $employee->department_id),
                ['class' => 'form-control', 'placeholder' => 'Select Department'],
            ) }}
            @error('department_id')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('designation_id', 'Designation', ['class' => 'form-label']) }}
            {{ Form::select(
                'designation_id',
                $designations->pluck('name', 'id'),
                old('designation_id', $employee->designation_id),
                ['class' => 'form-control', 'placeholder' => 'Select Designation'],
            ) }}
            @error('designation_id')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group col-md-4 mb-3">
            {{ Form::label('date_of_birth', 'Date of Birth', ['class' => 'form-label']) }}
            {{ Form::date('date_of_birth', old('date_of_birth', $employee->date_of_birth), [
                'class' => 'form-control',
            ]) }}
            @error('date_of_birth')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group col-md-4 mb-3">
            {{ Form::label('date_of_appointment', 'Date of Appointment', ['class' => 'form-label']) }}
            {{ Form::date('date_of_appointment', old('date_of_appointment', $employee->date_of_appointment), [
                'class' => 'form-control',
            ]) }}
            @error('date_of_appointment')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group col-md-4 mb-3">
            {{ Form::label('date_of_retirement', 'Date of Retirement', ['class' => 'form-label']) }}
            {{ Form::date('date_of_retirement', old('date_of_retirement', $employee->date_of_retirement), [
                'class' => 'form-control',
            ]) }}
            @error('date_of_retirement')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('level', __('Level') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::select(
                'level',
                [
                    '' => __('-- Select Level --'),
                    'LEVEL-1' => 'LEVEL-1',
                    'LEVEL-2' => 'LEVEL-2',
                    'LEVEL-3' => 'LEVEL-3',
                    'LEVEL-4' => 'LEVEL-4',
                    'LEVEL-5' => 'LEVEL-5',
                    'LEVEL-6' => 'LEVEL-6',
                    'LEVEL-7' => 'LEVEL-7',
                    'LEVEL-8' => 'LEVEL-8',
                    'LEVEL-9' => 'LEVEL-9',
                    'LEVEL-10' => 'LEVEL-10',
                    'LEVEL-11' => 'LEVEL-11',
                    'LEVEL-12' => 'LEVEL-12',
                ],
                $employee->level,
                [
                    'class' => 'form-select select2-single',
                    'required' => true,
                    'id' => 'level',
                ],
            ) }}
        </div>

    </div>

</div>

<div class="modal-footer">
    <button type="submit" class="btn btn-primary">Update</button>
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
</div>

{{ Form::close() }}
