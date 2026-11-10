{{ Form::model($tax, ['route' => ['tax.update', encrypt($tax->id)], 'method' => 'PUT']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group">
            {{ Form::label('tax', __('Tax'), ['class' => 'form-label']) }}
            {{ Form::text('tax', null, ['class' => 'form-control', 'placeholder' => __('Enter tax title'), 'required' => 'required']) }}
        </div>
        <div class="form-group">
            {{ Form::label('rate', __('Tax Rate (%)'), ['class' => 'form-label']) }}
            {{ Form::number('rate', null, ['class' => 'form-control', 'placeholder' => __('Enter rate'), 'step' => '0.1', 'required']) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    {{ Form::submit(__('Update'), ['class' => 'btn btn-secondary ml-10']) }}
</div>
{{ Form::close() }}

