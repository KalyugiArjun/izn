{{ Form::model($employee, [
    'route' => ['master.eo-employee.update', encrypt($employee->id)],
    'method' => 'POST',
]) }}

<div class="modal-body">
    <div class="row">

        <div class="form-group col-md-6 mb-3">
            {{ Form::label(
                'diesel_lobby_id',
                __('Diesel Lobby') . '<span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false,
            ) }}

            {{ Form::select('diesel_lobby_id', $stations->pluck('name', 'id'), null, [
                'class' => 'form-select',
                'placeholder' => __('-- Select Diesel Lobby --'),
                'required' => true,
            ]) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label(
                'hrms_id',
                __('HRMS ID') . '<span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false,
            ) }}

            {{ Form::text('hrms_id', null, [
                'class' => 'form-control',
                'placeholder' => __('Enter HRMS ID'),
                'required' => true,
            ]) }}
        </div>

        <div class="form-group col-md-12 mb-3">
            {{ Form::label('name', __('Name') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}

            {{ Form::text('name', null, [
                'class' => 'form-control',
                'placeholder' => __('Enter employee name'),
                'required' => true,
            ]) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label(
                'designation_id',
                __('Designation') . '<span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false,
            ) }}

            {{ Form::select('designation_id', $designations->pluck('name', 'id'), null, [
                'class' => 'form-select',
                'placeholder' => __('-- Select Designation --'),
                'required' => true,
            ]) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label(
                'status',
                __('Status') . '<span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false,
            ) }}

            {{ Form::select(
                'status',
                [
                    1 => __('Active'),
                    0 => __('Inactive'),
                ],
                null,
                [
                    'class' => 'form-select',
                    'required' => true,
                ],
            ) }}
        </div>

    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light btn-rounded" data-bs-dismiss="modal">
        {{ __('Cancel') }}
    </button>

    {{ Form::submit(__('Update'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>

{{ Form::close() }}
