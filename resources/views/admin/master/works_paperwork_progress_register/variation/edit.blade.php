{{ Form::open([
    'route' => ['works-paperwork-progress-register.variation.update', encrypt($record->id)],
    'method' => 'POST',
    'files' => true,
    'id' => 'variation_form',
]) }}

<div class="modal-body">
    <div class="row">

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('identify_new_work_id', 'Work Description', ['class' => 'form-label']) }}
            <span class="text-danger">*</span>

            <select name="identify_new_work_id" id="identify_new_work_id" class="form-control select2" required>

                <option value="">Select Work</option>

                @foreach ($works as $work)
                    <option value="{{ $work->id }}"
                        {{ old('identify_new_work_id', $record->identify_new_work_id) == $work->id ? 'selected' : '' }}>
                        {{ $work->work_description_identification }}
                    </option>
                @endforeach

            </select>

            <small class="text-muted">
                Only works with all Approval &amp; Listing milestones completed are listed here.
            </small>

            @error('identify_new_work_id')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror
        </div>


        <div class="form-group col-md-6 mb-3">
            {{ Form::label('date_of_variation', 'Date of Variation', ['class' => 'form-label']) }}
            <span class="text-danger">*</span>

            {{ Form::date(
                'date_of_variation',
                old('date_of_variation', $record->date_of_variation ? $record->date_of_variation->format('Y-m-d') : ''),
                [
                    'class' => 'form-control',
                    'id' => 'date_of_variation',
                    'required' => true,
                ],
            ) }}

            @error('date_of_variation')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>


        <div class="form-group col-md-12 mb-3">
            {{ Form::label('nature_of_variation', 'Nature of Variation / Revised Scope', ['class' => 'form-label']) }}
            <span class="text-danger">*</span>

            {{ Form::textarea('nature_of_variation', old('nature_of_variation', $record->nature_of_variation), [
                'class' => 'form-control',
                'id' => 'nature_of_variation',
                'rows' => 3,
                'required' => true,
            ]) }}

            @error('nature_of_variation')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>


        <div class="form-group col-md-6 mb-3">
            {{ Form::label('revised_tdc', 'Revised TDC (if any)', ['class' => 'form-label']) }}

            {{ Form::date('revised_tdc', old('revised_tdc', $record->revised_tdc), [
                'class' => 'form-control',
                'id' => 'revised_tdc',
                'required' => true,
            ]) }}

            @error('revised_tdc')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>


        <div class="form-group col-md-6 mb-3">
            {{ Form::label('file', 'File', ['class' => 'form-label']) }}

            {{ Form::file('file', [
                'class' => 'form-control',
                'accept' => 'application/pdf,.pdf',
            ]) }}

            @if ($record->file)
                <div class="mt-2">

                    <a href="{{ asset('public/storage/' . $record->file) }}" target="_blank" class="btn btn-sm btn-danger">

                        <i class="ti ti-file-type-pdf"></i>
                        View Current File

                    </a>

                </div>
            @endif

            <small class="text-muted">
                Only PDF files are allowed. Maximum size 10 MB.
            </small>

            @error('file')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror
        </div>

    </div>
</div>

<div class="modal-footer">

    <button type="submit" class="btn btn-primary">
        Update
    </button>

    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
        Cancel
    </button>

</div>

{{ Form::close() }}

<script>
    (function() {

        const workSelect = document.getElementById('identify_new_work_id');

        if ($.fn.select2) {
            $(workSelect).select2({
                dropdownParent: $(workSelect).closest('.modal'),
                width: '100%',
            });
        }

    })();
</script>
