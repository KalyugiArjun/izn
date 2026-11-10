{{ Form::open(['route' => 'project-monitering-system.scope_of_work.store', 'method' => 'post']) }}
<div class="modal-body">
    <div class="row">

        {{-- Select Project --}}
        <div class="form-group col-md-12 mb-3">
            {{ Form::label('project_id',
                __('Select Project') . '<span class="text-danger"> *</span>',
                ['class' => 'form-label'], false) }}
            {{ Form::select('project_id',
                ['' => __('-- Select Project --')] + $projects->toArray(),
                null,
                ['class' => 'form-select', 'required' => true]
            ) }}
        </div>

        {{-- Department --}}
        <div class="form-group col-md-12 mb-3">
            {{ Form::label('department_id', __('Department'). '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::select('department_id',
                ['' => __('-- Select Department --')] + $departments->toArray(),
                null,
                ['class' => 'form-select'  , 'required' => true]
            ) }}
        </div>

        {{-- Scope of Work --}}
        <div class="form-group col-md-12 mb-3">
            {{ Form::label('scope_of_work',
                __('Scope of Work') . '<span class="text-danger"> *</span>',
                ['class' => 'form-label'], false) }}
            {{ Form::textarea('scope_of_work', null, [
                'class'       => 'form-control',
                'placeholder' => __('Enter scope of work details...'),
                'rows'        => 4,
                'required'    => true,
            ]) }}
        </div>

        {{-- Status --}}
        <div class="form-group col-md-12 mb-3">
            {{ Form::label('status', __('Status'). '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::select('status',
                [1 => __('Active'), 0 => __('Inactive')],
                1,
                ['class' => 'form-select' , 'required' => true],

            ) }}
        </div>

    </div>
</div>
<div class="modal-footer">
    <button type="button"
            class="btn btn-light btn-rounded"
            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Create'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>
{{ Form::close() }}

