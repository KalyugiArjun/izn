{{ Form::open([
    'route' => 'inspection-notes.officer.store',
    'method' => 'POST',
    'files' => true
]) }}

<div class="modal-body">
    <div class="row">
        
        <div class="form-group col-md-6 mb-3">
            {{ Form::label(
                'inspecting_officers',
                __('Inspecting Officer(s)') . '<span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false
            ) }}
        
            {{ Form::select('inspecting_officers', [
                'Sr.DOM'   => __('Sr.DOM'),
                'Sr.DOM/G' => __('Sr.DOM/G'),
                'DOM/G'    => __('DOM/G'),
                'AOM/M'    => __('AOM/M'),
                'AOM/G'    => __('AOM/G'),
                
                'AOM/CHG'   => __('AOM/CHG'),
                'PCOM' => __('PCOM'),
                'CFTM'    => __('CFTM'),
                'CPTM'    => __('CPTM'),
                'CTPM'    => __('CTPM'),
                'GM'    => __('GM'),
                'AGM'    => __('AGM'),
                
                'PCSO' => __('PCSO'),
                'DRM'    => __('DRM'),
                'ADRM'    => __('ADRM'),
                'SAFETY AUDIT'    => __('SAFETY AUDIT'),
            ],[
                'class' => 'form-select',
                'required' => true,
                'multiple' => true
            ]) }}
        </div>
        
        <div class="form-group col-md-6 mb-3">
            <label class="form-label font-bold" for="type_of_inspection">{{ __('Type of Inspection') }}</label>
            <select name="type_of_inspection" id="type_of_inspection">
                @foreach($type_of_inspections as $toi)
                    <option value="{{ $toi->id }}">
                        {{ $toi->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('place_of_inspection', __('Place of Inspection') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::text('place_of_inspection', null, [
                'class' => 'form-control',
                'placeholder' => __('Enter place of inspection'),
                'required' => true
            ]) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('date_of_inspection', __('Date of Inspection') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::date('date_of_inspection', null, [
                'class' => 'form-control',
                'required' => true
            ]) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('inspection_note_pdf', __('Upload Inspection Note (PDF)') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::file('inspection_note_pdf', [
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