{{ Form::model($designation, ['route' => ['master.designation.update', encrypt($designation->id)], 'method' => 'POST']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12 mb-3">
            {{ Form::label('division_id', __('Division') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::select('division_id', $divisions->pluck('name', 'id'), null, [
                'class' => 'form-select',
                'placeholder' => __('-- Select Division --'),
                'required' => true,
                'id' => 'edit_division_id',
            ]) }}
        </div>
        <div class="form-group col-md-12 mb-3">
            {{ Form::label('department_id', __('Department') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::select('department_id', $departments->pluck('name', 'id'), null, [
                'class' => 'form-select',
                'placeholder' => __('-- Select Department --'),
                'required' => true,
                'id' => 'edit_department_id',
            ]) }}
        </div>
        <div class="form-group col-md-12 mb-3">
            {{ Form::label('name', __('Designation Name') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter designation name'), 'required']) }}
        </div>
        <div class="form-group col-md-12 mb-3">
            {{ Form::label('position', __('Position'). '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::text('position', null, ['class' => 'form-control', 'placeholder' => __('Enter position'), 'required']) }}
        </div>
        {{-- <div class="form-group col-md-6 mb-3">
            {{ Form::label('mobile', __('Mobile'), ['class' => 'form-label']) }}
            {{ Form::text('mobile', null, ['class' => 'form-control', 'placeholder' => __('Enter mobile number')]) }}
        </div>
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}
            {{ Form::email('email', null, ['class' => 'form-control', 'placeholder' => __('Enter email address')]) }}
        </div> --}}
        <div class="form-group col-md-12 mb-3">
            {{ Form::label('status', __('Status'). '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::select('status', [1 => __('Active'), 0 => __('Inactive')], null, ['class' => 'form-select', 'required' => true]) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-light btn-rounded" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Update'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>
{{ Form::close() }}

@push('scripts')
    <script>
        // Filter departments by selected division on edit
        document.getElementById('edit_division_id').addEventListener('change', function() {
            const divisionId = this.value;
            const deptSelect = document.getElementById('edit_department_id');
            deptSelect.innerHTML = '<option value="">-- Select Department --</option>';

            if (!divisionId) return;

            fetch(`{{ url('master/get-departments') }}?division_id=${divisionId}`)
                .then(res => res.json())
                .then(data => {
                    data.forEach(dept => {
                        const opt = document.createElement('option');
                        opt.value = dept.id;
                        opt.textContent = dept.name;
                        deptSelect.appendChild(opt);
                    });
                });
        });
    </script>
@endpush

