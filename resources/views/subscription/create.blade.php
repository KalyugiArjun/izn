{{ Form::open(['url' => 'subscriptions']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group">
            {{ Form::label('title', __('Title') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::text('title', null, ['class' => 'form-control', 'placeholder' => __('Enter subscription title'), 'required' => 'required']) }}
        </div>
        <div class="form-group">
            {{ Form::label('interval', __('Interval') .'<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {!! Form::select('interval', $intervals, null, ['class' => 'form-control select2', 'required' => 'required']) !!}
        </div>
        <div class="form-group">
            {{ Form::label('package_amount', __('Package Amount'), ['class' => 'form-label']) }}
            {{ Form::number('package_amount', null, ['class' => 'form-control', 'placeholder' => __('Enter package amount'), 'step' => '0.01']) }}
        </div>
        <div class="form-group">
            {{ Form::label('user_limit', __('User Limit') .'<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::number('user_limit', null, ['class' => 'form-control', 'placeholder' => __('Enter user limit'), 'required' => 'required']) }}
        </div>
        <div class="form-group">
            {{ Form::label('customer_limit', __('Customer Limit') .'<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::number('customer_limit', null, ['class' => 'form-control', 'placeholder' => __('Enter customer limit'), 'required' => 'required']) }}
        </div>
        <div class="form-group">
            {{ Form::label('employee_limit', __('Employee Limit') .'<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::number('employee_limit', null, ['class' => 'form-control', 'placeholder' => __('Enter Employee limit'), 'required' => 'required']) }}
        </div>
        <div class="form-group">
            {{ Form::label('cloth_type_limit', __('Cloth Type Limit') .'<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::number('cloth_type_limit', null, ['class' => 'form-control', 'placeholder' => __('Enter cloth type limit'), 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            <div class="form-check form-switch custom-switch-v1 mb-2">
                <input type="checkbox" class="form-check-input input-secondary" name="enabled_logged_history"
                    id="enabled_logged_history">
                {{ Form::label('enabled_logged_history', __('Show User Logged History'), ['class' => 'form-label']) }}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    {{ Form::submit(__('Create'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>
{{ Form::close() }}

