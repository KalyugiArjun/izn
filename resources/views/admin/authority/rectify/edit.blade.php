{{ Form::model($rectify, [
    'route'  => ['authority.rectify.update', encrypt($rectify->id)],
    'method' => 'POST',
]) }}
<div class="modal-body">
    <div class="row">

         {{-- <div class="form-group col-md-6 mb-3">
            {{ Form::label('employee_id', __('Employee') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}

            {{ Form::select('employee_id', ['' => __('-- Select Employee --')] + $employees->toArray(), null, [
                'class' => 'form-select',
                'id' => 'edit_employee_id',
            ]) }}
        </div> --}}

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('department_id',
                __('Department') . '<span class="text-danger"> *</span>',
                ['class' => 'form-label'], false) }}
            {{ Form::select('department_id',
                ['' => __('-- Select Department --')] + $departments->toArray(),
                null,
                ['class' => 'form-select', 'required' => true, 'id' => 'edit_rectify_department_id']
            ) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('designation_id', __('Designation'). '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::select('designation_id',
                ['' => __('-- Select Designation --')] + $designations->toArray(),
                null,
                ['class' => 'form-select', 'required' => true,'id' => 'edit_rectify_designation_id']
            ) }}
        </div>

    </div>
</div>
<div class="modal-footer">
    <button type="button"
            class="btn btn-light btn-rounded"
            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Update Rectify'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>
{{ Form::close() }}

<script>
    document.getElementById('edit_rectify_department_id').addEventListener('change', function () {
        const deptId = this.value;
        const desigSelect = document.getElementById('edit_rectify_designation_id');

        desigSelect.innerHTML = '<option value="">-- Select Designation --</option>';
        if (!deptId) return;

        fetch(`{{ route('authority.rectify.designations') }}?department_id=${deptId}`)
            .then(res => res.json())
            .then(data => {
                Object.entries(data).forEach(([id, name]) => {
                    desigSelect.innerHTML += `<option value="${id}">${name}</option>`;
                });
            });
    });
</script>

