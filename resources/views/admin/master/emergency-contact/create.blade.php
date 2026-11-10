{{ Form::open([
    'route' => 'emergency-contact.store',
    'method' => 'POST',
    'id' => 'emergency_contact_form',
]) }}

<div class="modal-body">

    <div class="row">

        <div class="form-group col-md-4 mb-3">

            {{ Form::label('employee_id', 'Employee', ['class' => 'form-label']) }}
            <span class="text-danger">*</span>

            {{ Form::select('employee_id', $employees->pluck('name', 'id'), old('employee_id'), [
                'class' => 'form-control',
                'required' => true,
                'id' => 'employee_id',
                'placeholder' => 'Select Employee',
            ]) }}

            @error('employee_id')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror

        </div>

        <div class="form-group col-md-4 mb-3">

            {{ Form::label('mobile_number', 'Mobile Number', ['class' => 'form-label']) }}
            <span class="text-danger">*</span>

            {{ Form::text('mobile_number', old('mobile_number'), [
                'class' => 'form-control',
                'required' => true,
                'maxlength' => 20,
                'placeholder' => 'Enter Mobile Number',
            ]) }}

            @error('mobile_number')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror

        </div>

        <div class="form-group col-md-4 mb-3">
            {{ Form::label('department', 'Department', ['class' => 'form-label']) }}
            {{ Form::text('department', '', [
                'class' => 'form-control',
                'id' => 'department',
                'readonly' => true,
            ]) }}
        </div>

        <div class="form-group col-md-4 mb-3">
            {{ Form::label('designation', 'Designation', ['class' => 'form-label']) }}
            {{ Form::text('designation', '', [
                'class' => 'form-control',
                'id' => 'designation',
                'readonly' => true,
            ]) }}
        </div>

        <div class="form-group col-md-4 mb-3">

            {{ Form::label('email', 'Email', ['class' => 'form-label']) }}
            <span class="text-danger">*</span>
            {{ Form::email('email', old('email'), [
                'class' => 'form-control',
                'placeholder' => 'Enter Email',
            ]) }}

            @error('email')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror

        </div>

        <div class="form-group col-md-4 mb-3">

            {{ Form::label('relation', 'Relation', ['class' => 'form-label']) }}
            <span class="text-danger">*</span>

            {{ Form::select(
                'relation',
                [
                    'Father' => 'Father',
                    'Mother' => 'Mother',
                    'Sister' => 'Sister',
                    'Brother' => 'Brother',
                    'Wife' => 'Wife',
                    'Husband' => 'Husband',
                    'Daughter' => 'Daughter',
                    'Son' => 'Son',
                    'Father In Law' => 'Father In Law',
                    'Mother In Law' => 'Mother In Law',
                    'Other' => 'Other',
                ],
                old('relation'),
                [
                    'class' => 'form-control',
                    'required' => true,
                    'id' => 'relation',
                ],
            ) }}

            @error('relation')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror

        </div>

        <div class="form-group col-md-12 mb-3" id="other_relation_div" style="display: none;">

            {{ Form::label('other_relation', 'Other Relation', ['class' => 'form-label']) }}
            <span class="text-danger">*</span>

            {{ Form::text('other_relation', old('other_relation'), [
                'class' => 'form-control',
                'id' => 'other_relation',
                'placeholder' => 'Enter Relation',
            ]) }}

            @error('other_relation')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror

        </div>

        <div class="form-group col-md-12 mb-3">

            {{ Form::label('address', 'Address', ['class' => 'form-label']) }}
            <span class="text-danger">*</span>
            {{ Form::textarea('address', old('address'), [
                'class' => 'form-control',
                'rows' => 3,
                'placeholder' => 'Enter Address',
            ]) }}

            @error('address')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror

        </div>

    </div>

</div>

<div class="modal-footer">

    <button type="submit" class="btn btn-primary">
        Save
    </button>

    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
        Cancel
    </button>

</div>

{{ Form::close() }}

<script>
    const employeesData = @json($employeesData);

    $(document).ready(function() {

        function fillEmployeeDetails() {
            const empId = $('#employee_id').val();
            const data = employeesData[empId];

            $('#department').val(data ? data.department : '');
            $('#designation').val(data ? data.designation : '');
        }

        function toggleOtherRelation() {

            if ($('#relation').val() === 'Other') {

                $('#other_relation_div').show();
                $('#other_relation').prop('required', true);

            } else {

                $('#other_relation_div').hide();
                $('#other_relation').prop('required', false);
                $('#other_relation').val('');

            }
        }

        $('#employee_id').on('change', fillEmployeeDetails);
        $('#relation').on('change', toggleOtherRelation);

        fillEmployeeDetails();
        toggleOtherRelation();

    });
</script>
