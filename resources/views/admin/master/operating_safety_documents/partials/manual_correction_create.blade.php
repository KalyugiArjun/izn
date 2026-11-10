{{ Form::open([
    'route' => $storeRoute,
    'method' => 'POST',
    'files' => true,
]) }}

<div class="modal-body">

    <div class="row">

        <div class="form-group col-md-6 mb-3">

            {{ Form::label(
                'original_id',
                __('Original Name') . '<span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false,
            ) }}

            <select name="original_id" id="manual_original_id" class="form-select" required>

                <option value="">
                    -- Select Original Name --
                </option>

                @foreach ($originals as $original)
                    <option value="{{ $original->id }}" data-pdf="{{ $original->original_pdf }}">

                        {{ $original->original_name }}

                    </option>
                @endforeach

            </select>

        </div>


        <div class="form-group col-md-6 mb-3">

            <label class="form-label">
                {{ __('Original PDF') }}
            </label>

            <div id="manualOriginalPdf" class="border rounded p-3">

                <span class="text-muted">
                    Select original name to view PDF.
                </span>

            </div>

        </div>


        <div class="form-group col-md-6 mb-3">

            {{ Form::label(
                'correction_slip_no',
                __('Correction Slip No.') . '<span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false,
            ) }}

            {{ Form::text('correction_slip_no', null, [
                'class' => 'form-control',
                'placeholder' => 'Enter correction slip number',
                'required' => true,
            ]) }}

        </div>


        <div class="form-group col-md-6 mb-3">

            {{ Form::label(
                'correction_slip_date',
                __('Correction Slip Date') . '<span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false,
            ) }}

            {{ Form::date('correction_slip_date', null, [
                'class' => 'form-control',
                'required' => true,
            ]) }}

        </div>


        <div class="form-group col-md-6 mb-3">

            {{ Form::label(
                'correction_slip_pdf',
                __('Correction Slip PDF') . '<span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false,
            ) }}

            {{ Form::file('correction_slip_pdf', [
                'class' => 'form-control',
                'accept' => '.pdf',
                'required' => true,
            ]) }}

        </div>


        <div class="form-group col-md-6 mb-3">

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

<script>
    $(function() {

        // Original Name change -> show its PDF (jQuery delegated listener, Select2-safe)
        $(document).on('change', '#manual_original_id', function() {

            const pdf = $(this).find(':selected').data('pdf');
            const $box = $('#manualOriginalPdf');

            if (pdf) {
                $box.html(`
                <a href="{{ asset('public/storage') }}/${pdf}"
                   target="_blank"
                   class="btn btn-sm btn-light-danger">
                    <i class="ti ti-file-type-pdf"></i>
                    View Original PDF
                </a>
            `);
            } else {
                $box.html('<span class="text-muted">Original PDF not available.</span>');
            }
        });

    });
</script>
