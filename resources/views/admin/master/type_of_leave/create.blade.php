{{ Form::open(['route' => 'master.type_of_leave.store', 'method' => 'post']) }}
<div class="modal-body">
    <div class="row">
        
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('name', __('Type of Leave Name') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Type of Leave name'), 'required']) }}
        </div>
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('status', __('Status'). '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::select('status', [1 => __('Active'), 0 => __('Inactive')], 1, ['class' => 'form-select'  , 'required' => true]) }}
        </div> 
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-light btn-rounded" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Create'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>
{{ Form::close() }}

