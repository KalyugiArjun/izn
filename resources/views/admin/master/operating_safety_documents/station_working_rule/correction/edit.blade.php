{{ Form::model($record, [
    'route' => ['operating-safety-documents.station-working-rule.correction.update', encrypt($record->id)],
    'method' => 'POST',
    'files' => true,
]) }}

<div class="modal-body">

    <div class="row">

        <div class="form-group col-md-6 mb-3">

            {{ Form::label(
                'station_id',
                __('Station Name') . '<span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false,
            ) }}

            <select name="station_id" id="edit_station_id" class="form-select" required>

                <option value="">
                    -- Select Station --
                </option>

                @foreach ($stations as $station)
                    <option value="{{ $station->id }}" {{ $selectedStationId == $station->id ? 'selected' : '' }}>

                        {{ $station->name }}

                        @if ($station->code)
                            - {{ $station->code }}
                        @endif

                    </option>
                @endforeach

            </select>

        </div>


        <div class="form-group col-md-6 mb-3">

            {{ Form::label(
                'original_id',
                __('Original Name') . '<span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false,
            ) }}

            <select name="original_id" id="edit_original_id" class="form-select" required>

                <option value="">
                    -- Select Original Name --
                </option>

                @foreach ($originals as $original)
                    <option value="{{ $original->id }}" {{ $record->original_id == $original->id ? 'selected' : '' }}>

                        {{ $original->original_name }}

                    </option>
                @endforeach

            </select>

        </div>


        <div class="form-group col-md-6 mb-3">

            <label class="form-label">
                Original PDF
            </label>

            <div id="editOriginalPdfBox" class="border rounded p-3">

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
        'issue_date',
        __('Issue Date') . ' <span class="text-danger"> * </span>',
        ['class' => 'form-label'],
        false
    ) }}

    {{ Form::date('issue_date', null, [
        'class' => 'form-control',
        'required' => true,
    ]) }}
</div>
        
        <div class="form-group col-md-6 mb-3">
    {{ Form::label(
        'imposition_date',
        __('Imposition Date') . ' <span class="text-danger"> * </span>',
        ['class' => 'form-label'],
        false
    ]) }}

    {{ Form::date('imposition_date', null, [
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

        // Station change -> reload Original Name options (Select2-safe: jQuery delegated listener)
        $(document).on('change', '#edit_station_id', function() {

            const stationId = $(this).val();
            const $originalSelect = $('#edit_original_id');
            const $pdfBox = $('#editOriginalPdfBox');

            $originalSelect.empty()
                .append('<option value="">-- Select Original Name --</option>')
                .trigger('change');

            $pdfBox.html('');

            if (!stationId) return;

            fetch('{{ url('operating-safety-documents/station-working-rule/correction/originals') }}/' +
                    stationId)
                .then(r => r.json())
                .then(data => {
                    data.forEach(o => {
                        $originalSelect.append($('<option>', {
                            value: o.id,
                            text: o.original_name
                        }));
                    });
                    $originalSelect.trigger('change');
                });
        });

        // Original Name change -> fetch and show its PDF
        $(document).on('change', '#edit_original_id', function() {

            const originalId = $(this).val();
            const $pdfBox = $('#editOriginalPdfBox');

            if (!originalId) {
                $pdfBox.html('');
                return;
            }

            fetch('{{ url('operating-safety-documents/station-working-rule/correction/original') }}/' +
                    originalId)
                .then(r => r.json())
                .then(data => {
                    if (data.original_pdf) {
                        $pdfBox.html(
                        `<a href="{{ asset('public/storage') }}/${data.original_pdf}" target="_blank"
                        class="btn btn-sm btn-light-danger"><i class="ti ti-file-type-pdf"></i> View Original PDF</a>`);
                    } else {
                        $pdfBox.html('<span class="text-muted">Original PDF not available.</span>');
                    }
                });
        });

    });
</script>
