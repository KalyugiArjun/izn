{{ Form::model($station, ['route' => ['master.diesel_lobby.update', encrypt($station->id)], 'method' => 'POST']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('code', __('Diesel Lobby Location Code'). '<span class="text-danger"> * </span>', ['class' => 'form-label' ], false) }}
            {{ Form::text('code', null, ['class' => 'form-control', 'placeholder' => __('Enter Diesel Lobby Location code') , 'required' => true]) }}
        </div>
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('name', __('Diesel Lobby Location Name') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Diesel Lobby Location name'), 'required']) }}
        </div>
        <div class="form-group col-md-12 mb-3">
            {{ Form::label('status', __('Status'). '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::select('status', [1 => __('Active'), 0 => __('Inactive')], null, ['class' => 'form-select', 'required']) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-light btn-rounded" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Update'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>
{{ Form::close() }}

