
{{ Form::model($identifyNewWork, [
    'route' => ['works-paperwork-progress-register.identify-new-work.update', encrypt($identifyNewWork->id)],
    'method' => 'PUT',
]) }}

<div class="modal-body">
    <div class="row">

        <div class="form-group col-md-6 mb-3">
            {{ Form::label(
                'work_description_identification',
                __('Work Description') . ' <span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false,
            ) }}

            {{ Form::text('work_description_identification', null, [
                'class' => 'form-control',
                'placeholder' => __('Enter work description'),
                'required' => true,
            ]) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label(
                'date_of_identification_of_work',
                __('Date of Work') . ' <span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false,
            ) }}

            {{ Form::date(
                'date_of_identification_of_work',
                $identifyNewWork->date_of_identification_of_work
                    ? $identifyNewWork->date_of_identification_of_work->format('Y-m-d')
                    : null,
                [
                    'class' => 'form-control',
                    'required' => true,
                ],
            ) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label(
                'ph16_ti_ids',
                __('Concern Traffic Inspector') . ' <span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false,
            ) }}

            <select name="ph16_ti_ids[]" id="ph16_ti_ids" class="form-control select2" multiple="multiple" required data-placeholder="{{ __('Select Concern Traffic Inspector') }}">
                @foreach ($inspectors as $id => $name)
                    <option value="{{ $id }}" {{ in_array($id, (array) old('ph16_ti_ids', $selectedInspectorIds ?? [])) ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            <small class="text-muted">{{ __('You can select one or multiple traffic inspectors.') }}</small>

            @error('ph16_ti_ids')
                <small class="text-danger d-block">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label(
                'status',
                __('Status') . ' <span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false,
            ) }}

            {{ Form::select(
                'status',
                [
                    1 => __('Active'),
                    0 => __('Inactive'),
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
        {{ __('Cancel') }}
    </button>

    {{ Form::submit(__('Update'), [
        'class' => 'btn btn-secondary btn-rounded',
    ]) }}
</div>

{{ Form::close() }}

<script>
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('#ph16_ti_ids').select2({
            dropdownParent: $('#ph16_ti_ids').closest('.modal'),
            placeholder: "{{ __('Select Concern Traffic Inspector') }}",
            width: '100%'
        });
    }
</script>
