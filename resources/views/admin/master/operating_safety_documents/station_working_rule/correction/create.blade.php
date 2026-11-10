{{ Form::open([
    'route' => 'operating-safety-documents.station-working-rule.correction.store',
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

            <select name="station_id" id="station_id" class="form-select" required>

                <option value="">
                    -- Select Station --
                </option>

                @foreach ($stations as $station)
                    <option value="{{ $station->id }}">
                        {{ $station->name }}
                        @if (!empty($station->code))
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

            <select name="original_id" id="original_id" class="form-select" required disabled>

                <option value="">
                    -- Select Original Name --
                </option>

            </select>

        </div>


        <div class="form-group col-md-12 mb-3">

            <label class="form-label">
                {{ __('Original PDF') }}
            </label>

            <div id="original_pdf_box" class="border rounded p-3 bg-light">

                <span class="text-muted">
                    Select Original Name to view PDF.
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
                'issue_date',
                __('Issue Date') . ' <span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false,
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
                false,
            ) }}

            {{ Form::date('imposition_date', null, [
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

        // Station change -> reload Original Name options (Select2-safe: jQuery delegated listener)
        $(document).on('change', '#station_id', function() {

            const stationId = $(this).val();
            const $originalSelect = $('#original_id');
            const $originalPdfBox = $('#original_pdf_box');

            $originalSelect.empty()
                .append('<option value="">-- Select Original Name --</option>')
                .prop('disabled', true)
                .trigger('change');

            $originalPdfBox.html('<span class="text-muted">Select Original Name to view PDF.</span>');

            if (!stationId) return;

            $originalSelect.empty()
                .append('<option value="">Loading Original Name...</option>')
                .trigger('change');

            fetch("{{ url('operating-safety-documents/station-working-rule/correction/originals') }}/" +
                    stationId, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                .then(r => {
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    return r.json();
                })
                .then(data => {

                    $originalSelect.empty().append(
                        '<option value="">-- Select Original Name --</option>');

                    if (!Array.isArray(data) || data.length === 0) {
                        $originalSelect.append('<option value="">No Original Name Found</option>')
                            .prop('disabled', true)
                            .trigger('change');
                        return;
                    }

                    data.forEach(o => {
                        $originalSelect.append(
                            $('<option>', {
                                value: o.id,
                                text: o.original_name,
                                'data-pdf': o.original_pdf || ''
                            })
                        );
                    });

                    $originalSelect.prop('disabled', false).trigger('change');
                })
                .catch(() => {
                    $originalSelect.empty()
                        .append('<option value="">Unable to load Original Name</option>')
                        .prop('disabled', true)
                        .trigger('change');
                    $originalPdfBox.html(
                        '<span class="text-danger">Unable to load Original Name.</span>');
                });
        });

        // Original Name change -> show its PDF
        $(document).on('change', '#original_id', function() {

            const pdf = $(this).find(':selected').data('pdf');
            const $box = $('#original_pdf_box');

            if (!pdf) {
                $box.html('<span class="text-danger">Original PDF not available.</span>');
                return;
            }

            const fileName = String(pdf).split('/').pop();
            const pdfUrl = "{{ asset('public/storage') }}/" + pdf;

            $box.html(`<div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div><div class="fw-semibold">${fileName}</div><small class="text-muted">Original PDF</small></div>
            <a href="${pdfUrl}" target="_blank" class="btn btn-sm btn-light-danger"><i class="ti ti-eye"></i> View PDF</a>
        </div>`);
        });

    });
</script>
