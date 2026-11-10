{{ Form::open([
    'route' => 'document-upload.operating-safety-circular.store',
    'method' => 'POST',
    'files' => true
]) }}

<div class="modal-body">
    <div class="row">

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('circular_number', __('Circular Number') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::text('circular_number', null, [
                'class' => 'form-control',
                'placeholder' => __('Enter circular number'),
                'required' => true
            ]) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('date_of_circular', __('Date of Circular') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::date('date_of_circular', null, [
                'class' => 'form-control',
                'required' => true
            ]) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('subject', __('Subject') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::text('subject', null, [
                'class' => 'form-control',
                'placeholder' => __('Enter subject'),
                'required' => true
            ]) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('pdf_file', __('Upload PDF') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::file('pdf_file', [
                'class' => 'form-control',
                'accept' => '.pdf',
                'required' => true
            ]) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('status', __('Status') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::select('status', [
                1 => __('Active'),
                0 => __('Inactive')
            ], 1, [
                'class' => 'form-select',
                'required' => true
            ]) }}
        </div>

    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light btn-rounded" data-bs-dismiss="modal">
        {{ __('Cancel') }}
    </button>

    {{ Form::submit(__('Create'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>

{{ Form::close() }}