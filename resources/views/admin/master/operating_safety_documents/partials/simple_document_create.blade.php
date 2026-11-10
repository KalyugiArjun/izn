{{ Form::open([
    'route' => $storeRoute,
    'method' => 'POST',
    'files' => true,
]) }}

<div class="modal-body">
    <div class="row">

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('date', __('Date') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}

            {{ Form::date('date', null, [
                'class' => 'form-control',
                'required' => true,
            ]) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label($numberField, __($numberLabel) . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}

            {{ Form::text($numberField, null, [
                'class' => 'form-control',
                'placeholder' => 'Enter ' . $numberLabel,
                'required' => true,
            ]) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('subject', __('Subject') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}

            {{ Form::text('subject', null, [
                'class' => 'form-control',
                'placeholder' => 'Enter subject',
                'required' => true,
            ]) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('pdf_file', __('PDF Upload') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}

            {{ Form::file('pdf_file', [
                'class' => 'form-control',
                'accept' => '.pdf',
                'required' => true,
            ]) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('status', __('Status') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}

            {{ Form::select(
                'status',
                [
                    1 => 'Active',
                    0 => 'Inactive',
                ],
                1,
                [
                    'class' => 'form-select',
                    'required' => true,
                ],
            ) }}
        </div>

    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light btn-rounded" data-bs-dismiss="modal">
        Cancel
    </button>

    {{ Form::submit('Create', ['class' => 'btn btn-secondary btn-rounded']) }}
</div>

{{ Form::close() }}
