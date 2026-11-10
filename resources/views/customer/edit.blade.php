{{ Form::model($user, ['route' => ['customer.update', encrypt($user->id)], 'method' => 'PUT']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6">
            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter name'), 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}
            {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('Enter email'), 'required' => 'required']) }}
        </div>
        <div class="form-group col-md-6">
            {{ Form::label('phone_number', __('Phone Number'), ['class' => 'form-label']) }}
            {{ Form::text('phone_number', null, ['class' => 'form-control', 'placeholder' => __('Enter phone number'), 'required' => 'required']) }}
            <small class="form-text text-muted">
                {{ __('Please enter the number with country code. e.g., +91XXXXXXXXXX') }}
            </small>

        </div>
        <div class="form-group col-md-6">
            {{ Form::label('city', __('City'), ['class' => 'form-label']) }}
            {{ Form::text('city', !empty($user) ? $user->customers->city : null, ['class' => 'form-control', 'placeholder' => __('Enter city'), 'required' => 'required']) }}
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('address', __('Address'), ['class' => 'form-label']) }}
            {{ Form::textarea('address', !empty($user) ? $user->customers->address : null, ['class' => 'form-control', 'rows' => 3]) }}
        </div>
        <div class="form-group  col-md-6">
            {{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}
            {{ Form::textarea('notes', !empty($user) ? $user->customers->notes : null, ['class' => 'form-control', 'rows' => 3]) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    {{ Form::submit(__('Update'), ['class' => 'btn btn-secondary ml-10']) }}
</div>
{{ Form::close() }}

