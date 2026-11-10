{{ Form::open(['route' => 'master.station.store', 'method' => 'post']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('code', __('Station/Working Place Code'). '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::text('code', null, ['class' => 'form-control', 'placeholder' => __('Enter Station/Working Place code') , 'required' => true]) }}
        </div>
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('name', __('Station/Working Place Name') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Station/Working Place name'), 'required']) }}
        </div>
        {{-- <div class="form-group col-md-12 mb-3">
            {{ Form::label('address', __('Address'). '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::textarea('address', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => __('Enter full address') , 'required' => true]) }}
        </div>
        <div class="form-group col-md-4 mb-3">
            {{ Form::label('city', __('City'). '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::text('city', null, ['class' => 'form-control', 'placeholder' => __('Enter city') , 'required' => true]) }}
        </div>
        <div class="form-group col-md-4 mb-3">
            {{ Form::label('state', __('State'). '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::text('state', null, ['class' => 'form-control', 'placeholder' => __('Enter state') , 'required' => true]) }}
        </div>
        <div class="form-group col-md-4 mb-3">
            {{ Form::label('pincode', __('Pincode'). '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::text('pincode', null, ['class' => 'form-control', 'placeholder' => __('Enter pincode') , 'required' => true]) }}
        </div>
        <div class="form-group col-md-12 mb-3">
            {{ Form::label('status', __('Status'). '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::select('status', [1 => __('Active'), 0 => __('Inactive')], 1, ['class' => 'form-select'  , 'required' => true]) }}
        </div> --}}
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-light btn-rounded" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Create'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>
{{ Form::close() }}

