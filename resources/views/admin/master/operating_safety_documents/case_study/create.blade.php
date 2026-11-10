
{{ Form::open([
    'route' => 'operating-safety-documents.case-study.store',
    'method' => 'POST',
    'files' => true
]) }}

<div class="modal-body">
    <div class="row">

        <div class="form-group col-md-6 mb-3">
            {{ Form::label(
                'case_study_title',
                __('Case Study Title') . ' <span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false
            ) }}

            {{ Form::text('case_study_title', null, [
                'class' => 'form-control',
                'placeholder' => 'Enter case study title',
                'required' => true
            ]) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label(
                'incident_date',
                __('Date of Incident') . ' <span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false
            ) }}

            {{ Form::date('incident_date', null, [
                'class' => 'form-control',
                'required' => true
            ]) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label(
                'location',
                __('Location') . ' <span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false
            ) }}

            {{ Form::text('location', null, [
                'class' => 'form-control',
                'placeholder' => 'Enter location',
                'required' => true
            ]) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label(
                'department_id',
                __('Department') . ' <span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false
            ) }}

            <select name="department_id" id="department_id" class="form-select" required>
                <option value="">-- Select Department --</option>

                @foreach ($departments as $department)
                    <option value="{{ $department->id }}">
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label(
                'type_of_irregularity',
                __('Type of Irregularity') . ' <span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false
            ) }}

            {{ Form::text('type_of_irregularity', null, [
                'class' => 'form-control',
                'placeholder' => 'Enter type of irregularity',
                'required' => true
            ]) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label(
                'risk_level',
                __('Risk Level') . ' <span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false
            ) }}

            {{ Form::select(
                'risk_level',
                [
                    'LOW' => 'LOW',
                    'MEDIUM' => 'MEDIUM',
                    'HIGH' => 'HIGH',
                    'CRITICAL' => 'CRITICAL'
                ],
                null,
                [
                    'class' => 'form-select',
                    'required' => true
                ]
            ) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label(
                'key_learning',
                __('Key Learning') . ' <span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false
            ) }}

            {{ Form::text('key_learning', null, [
                'class' => 'form-control',
                'placeholder' => 'Enter key learning',
                'required' => true
            ]) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label(
                'pdf_file',
                __('Upload PDF') . ' <span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false
            ) }}

            {{ Form::file('pdf_file', [
                'class' => 'form-control',
                'accept' => '.pdf',
                'required' => true
            ]) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label(
                'status',
                __('Status') . ' <span class="text-danger"> * </span>',
                ['class' => 'form-label'],
                false
            ) }}

            {{ Form::select(
                'status',
                [
                    1 => 'Active',
                    0 => 'Inactive'
                ],
                1,
                [
                    'class' => 'form-select',
                    'required' => true
                ]
            ) }}
        </div>

    </div>
</div>

<div class="modal-footer">
    <button
        type="button"
        class="btn btn-light btn-rounded"
        data-bs-dismiss="modal">
        Cancel
    </button>

    {{ Form::submit('Create', [
        'class' => 'btn btn-secondary btn-rounded'
    ]) }}
</div>

{{ Form::close() }}
