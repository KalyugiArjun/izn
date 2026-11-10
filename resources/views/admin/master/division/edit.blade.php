{{ Form::model($division, ['route' => ['master.division.update', encrypt($division->id)], 'method' => 'POST']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12 mb-3">
            {{ Form::label('name', __('Division Name') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter division name'), 'required']) }}
        </div>
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

