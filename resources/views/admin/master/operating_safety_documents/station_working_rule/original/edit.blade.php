{{ Form::model($record, [
    'route' => [
        'operating-safety-documents.station-working-rule.original.update',
        encrypt($record->id)
    ],
    'method' => 'POST',
    'files' => true
]) }}

<div class="modal-body">

    <div class="row">

        <div class="form-group col-md-6 mb-3">

            {{ Form::label(
                'station_id',
                __('Station Name') . '<span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false
            ) }}

            <select name="station_id" class="form-select" required>

                <option value="">
                    -- Select Station --
                </option>

                @foreach($stations as $station)

                    <option value="{{ $station->id }}"
                        {{ $record->station_id == $station->id ? 'selected' : '' }}>

                        {{ $station->name }}
                        @if($station->code)
                            - {{ $station->code }}
                        @endif

                    </option>

                @endforeach

            </select>

        </div>

        <div class="form-group col-md-6 mb-3">

            {{ Form::label(
                'original_name',
                __('Original Name') . '<span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false
            ) }}

            {{ Form::text('original_name', null, [
                'class' => 'form-control',
                'required' => true
            ]) }}

        </div>

        <div class="form-group col-md-6 mb-3">

            {{ Form::label(
                'original_pdf',
                __('Original PDF'),
                ['class' => 'form-label']
            ) }}

            {{ Form::file('original_pdf', [
                'class' => 'form-control',
                'accept' => '.pdf'
            ]) }}

            @if($record->original_pdf)

                <div class="mt-2">

                    <a href="{{ asset('public/storage/'.$record->original_pdf) }}"
                       target="_blank"
                       class="btn btn-sm btn-light-danger">

                        <i class="ti ti-file-type-pdf"></i>
                        View Current PDF

                    </a>

                </div>

            @endif

        </div>

        <div class="form-group col-md-6 mb-3">

            {{ Form::label(
                'status',
                __('Status') . '<span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false
            ) }}

            {{ Form::select('status', [
                1 => 'Active',
                0 => 'Inactive'
            ], null, [
                'class' => 'form-select',
                'required' => true
            ]) }}

        </div>

    </div>

</div>

<div class="modal-footer">

    <button type="button"
            class="btn btn-light btn-rounded"
            data-bs-dismiss="modal">
        Cancel
    </button>

    {{ Form::submit('Update', [
        'class' => 'btn btn-secondary btn-rounded'
    ]) }}

</div>

{{ Form::close() }}