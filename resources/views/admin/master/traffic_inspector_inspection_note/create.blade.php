{{ Form::open([
    'route' => 'inspection-notes.traffic-inspector.store',
    'method' => 'POST',
    'files' => true
]) }}

<div class="modal-body">
    <div class="row">

        <div class="form-group col-md-6 mb-3">
            {{ Form::label(
                'inspecting_traffic_inspector',
                __('Inspecting Traffic Inspector') . '<span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false
            ) }}
        
            {{ Form::select('inspecting_traffic_inspector', [
                'TI RPO'   => __('TI RPO'),
                'TI FGR' => __('TI FGR'),
                'TI FBD'    => __('TI FBD'),
                'TI MRT'    => __('TI MRT'),
                'TI KSJ'    => __('TI KSJ'),
                'TI BC'   => __('TI BC'),
                'TI IZN' => __('TI IZN'),
                'TI PBE/E'    => __('TI PBE/E'),
                'TI PBE/W'    => __('TI PBE/W'),
                'TI LKU'    => __('TI LKU'),
                'TI KPV'   => __('TI KPV'),
                'TI SWR' => __('TI SWR'),
                'TI MPP'    => __('TI MPP'),
                'TI Operating Safety'    => __('TI Operating Safety'),
                'TI STORE'    => __('TI STORE'),
                'TI WP'    => __('TI WP'),
                'TI FOIS'    => __('TI FOIS')
            ],[
                'class' => 'form-select',
                'required' => true
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