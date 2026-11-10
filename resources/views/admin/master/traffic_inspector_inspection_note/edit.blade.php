{{ Form::model($note, [
    'route' => [
        'inspection-notes.traffic-inspector.update',
        encrypt($note->id)
    ],
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
            {{ Form::select(
                'inspecting_traffic_inspector',
                [
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
                ],
                $note->inspecting_traffic_inspector,
                [
                    'class' => 'form-select',
                    'required' => true
                ]
            ) }}
        </div>
        
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('type_of_inspection', __('Type of Inspection') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::select(
                'type_of_inspection',
                $type_of_inspections->pluck('name', 'id'),
                $note->type_of_inspection,
                [
                    'class' => 'form-select',
                    'required' => true
                ]
            ) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('place_of_inspection', __('Place of Inspection') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::text('place_of_inspection', null, [
                'class' => 'form-control',
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
            {{ Form::label('inspection_note_pdf', __('Upload Inspection Note (PDF)'), ['class' => 'form-label']) }}

            {{ Form::file('inspection_note_pdf', [
                'class' => 'form-control',
                'accept' => '.pdf'
            ]) }}

            @if($note->inspection_note_pdf)
                <div class="mt-2">
                    <a href="{{ asset('storage/' . $note->inspection_note_pdf) }}"
                       target="_blank"
                       class="btn btn-sm btn-light-danger">
                        <i class="ti ti-file-type-pdf"></i>
                        {{ __('View Current PDF') }}
                    </a>
                </div>
            @endif
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('status', __('Status') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::select('status', [
                1 => __('Active'),
                0 => __('Inactive')
            ], null, [
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

    {{ Form::submit(__('Update'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>

{{ Form::close() }}