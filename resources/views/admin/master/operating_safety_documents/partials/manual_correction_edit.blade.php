{{ Form::model($record, [
    'route' => [$updateRoute, encrypt($record->id)],
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

            <select name="original_id" id="edit_manual_original_id" class="form-select" required>

                <option value="">
                    -- Select Original Name --
                </option>

                @foreach ($originals as $original)
                    <option value="{{ $original->id }}" data-pdf="{{ $original->original_pdf }}"
                        {{ $record->original_id == $original->id ? 'selected' : '' }}>

                        {{ $original->original_name }}

                    </option>
                @endforeach

            </select>

        </div>


        <div class="form-group col-md-6 mb-3">

            <label class="form-label">
                Original PDF
            </label>

            <div id="editManualOriginalPdf" class="border rounded p-3">

                @if ($record->original && $record->original->original_pdf)
                    <a href="{{ asset('public/storage/' . $record->original->original_pdf) }}" target="_blank"
                        class="btn btn-sm btn-light-danger">

                        <i class="ti ti-file-type-pdf"></i>
                        View Original PDF

                    </a>
                @else
                    <span class="text-muted">
                        Original PDF not available.
                    </span>
                @endif

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

            {{ Form::label('correction_slip_pdf', __('Correction Slip PDF'), ['class' => 'form-label']) }}

            {{ Form::file('correction_slip_pdf', [
                'class' => 'form-control',
                'accept' => '.pdf',
            ]) }}

            @if ($record->correction_slip_pdf)
                <div class="mt-2">

                    <a href="{{ asset('public/storage/' . $record->correction_slip_pdf) }}" target="_blank"
                        class="btn btn-sm btn-light-danger">

                        <i class="ti ti-file-type-pdf"></i>
                        View Current Correction PDF

                    </a>

                </div>
            @endif

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
                null,
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

    {{ Form::submit('Update', [
        'class' => 'btn btn-secondary btn-rounded',
    ]) }}

</div>

{{ Form::close() }}

<script>
    $(function() {

        // Original Name change -> show its PDF (jQuery delegated listener, Select2-safe)
        $(document).on('change', '#edit_manual_original_id', function() {

            const pdf = $(this).find(':selected').data('pdf');
            const $box = $('#editManualOriginalPdf');

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
