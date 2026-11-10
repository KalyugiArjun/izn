{{ Form::open([
    'route' => 'operating-safety-documents.operating-manual.original.store',
    'method' => 'POST',
    'files' => true,
]) }}

<div class="modal-body">

    <div class="form-group mb-3">

        {{ Form::label(
            'original_name',
            __('Original Name') . '<span class="text-danger"> * </span>',
            ['class' => 'form-label'],
            false,
        ) }}

        {{ Form::text('original_name', null, [
            'class' => 'form-control',
            'placeholder' => 'Enter original name',
            'required' => true,
        ]) }}

    </div>

    <div class="form-group mb-3">

        {{ Form::label(
            'original_pdf',
            __('Original PDF') . '<span class="text-danger"> * </span>',
            ['class' => 'form-label'],
            false,
        ) }}

        {{ Form::file('original_pdf', [
            'class' => 'form-control',
            'accept' => '.pdf',
            'required' => true,
        ]) }}

    </div>

    <div class="form-group mb-3">

        {{ Form::label(
            'status',
            __('Status') . '<span class="text-danger"> * </span>',
            ['class' => 'form-label'],
            false,
        ) }}

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

<div class="modal-footer">

    <button type="button" class="btn btn-light btn-rounded" data-bs-dismiss="modal">
        Cancel
    </button>

    {{ Form::submit('Create', [
        'class' => 'btn btn-secondary btn-rounded',
    ]) }}

</div>

{{ Form::close() }}
