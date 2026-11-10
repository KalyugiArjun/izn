{{ Form::model($measurementUnit, ['route' => ['measurement-unit.update', encrypt($measurementUnit->id)], 'method' => 'PUT']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group">
            {{ Form::label('unit', __('Unit'), ['class' => 'form-label']) }}
            {{ Form::text('unit', null, ['class' => 'form-control', 'placeholder' => __('Enter unit title'), 'required' => 'required']) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    {{ Form::submit(__('Update'), ['class' => 'btn btn-secondary ml-10']) }}
</div>
{{ Form::close() }}

