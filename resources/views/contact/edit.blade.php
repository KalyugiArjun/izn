{{ Form::model($contact, ['route' => ['contact.update', encrypt($contact->id)], 'method' => 'PUT']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group  col-md-12">
            {{ Form::label('name', __('Name') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter contact name')]) }}
        </div>
        <div class="form-group  col-md-12">
            {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}
            {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('Enter contact email')]) }}
        </div>
        <div class="form-group  col-md-12">
            {{ Form::label('contact_number', __('Contact Number'), ['class' => 'form-label']) }}
            {{ Form::number('contact_number', null, ['class' => 'form-control', 'placeholder' => __('Enter contact number')]) }}
        </div>
        <div class="form-group  col-md-12">
            {{ Form::label('subject', __('Subject') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::text('subject', null, ['class' => 'form-control', 'placeholder' => __('Enter contact subject')]) }}
        </div>
        <div class="form-group  col-md-12">
            {{ Form::label('message', __('Message') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::textarea('message', null, ['class' => 'form-control', 'rows' => 5]) }}
        </div>
    </div>
</div>
<div class="modal-footer">

    {{ Form::submit(__('Update'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>
{{ Form::close() }}

