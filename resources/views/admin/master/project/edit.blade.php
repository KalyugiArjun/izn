{{ Form::model($project, [
    'route'  => ['project-monitering-system.project.update', encrypt($project->id)],
    'method' => 'POST',
]) }}
<div class="modal-body">
    <div class="row">

        {{-- Auto-generated Project ID (read-only display) --}}
        <div class="form-group col-md-12 mb-3">
            {{ Form::label('project_id', __('Project ID'), ['class' => 'form-label']) }}
            <div class="input-group">
                <span class="input-group-text">
                    <i class="ti ti-hash"></i>
                </span>
                {{ Form::text('project_id', $project->project_id, [
                    'class'    => 'form-control bg-light',
                    'readonly' => true,
                ]) }}
            </div>
            <small class="text-muted">{{ __('Auto-generated, cannot be changed.') }}</small>
        </div>


        {{-- Cost --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('cost',
                __('Cost (₹)') . '<span class="text-danger"> *</span>',
                ['class' => 'form-label'], false) }}
            <div class="input-group">
                <span class="input-group-text">₹</span>
                {{ Form::number('cost', null, [
                    'class'    => 'form-control',
                    'step'     => '0.01',
                    'min'      => '0',
                    'required' => true,
                ]) }}
            </div>
        </div>

        {{-- Department --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('department_id', __('Department'). '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::select('department_id',
                ['' => __('-- Select Department --')] + $departments->toArray(),
                null,
                ['class' => 'form-select' , 'required' => true]
            ) }}
        </div>

        {{-- Working Agencies --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('working_agencies', __('Working Agencies'). '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::text('working_agencies', null, ['class' => 'form-control' , 'required' => true]) }}
        </div>

        {{-- Sanction Date --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('sanction_date', __('Sanction Date'). '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
          {{ Form::date('sanction_date', $project->sanction_date ? \Carbon\Carbon::parse($project->sanction_date)->format('Y-m-d') : null, ['class' => 'form-control',  , 'required' => true]) }}
        </div>

        {{-- TDC Date --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('tdc_date', __('TDC Date'). '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
{{ Form::date('tdc_date', $project->tdc_date ? \Carbon\Carbon::parse($project->tdc_date)->format('Y-m-d') : null, ['class' => 'form-control', 'required' => true]) }}
        </div>

        {{-- Financial Year --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('financial_year', __('Financial Year'). '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::select('financial_year',
                ['' => __('-- Select Financial Year --')] + $financial_years,
                null,
                ['class' => 'form-select', 'required' => true]
            ) }}
        </div>

        {{-- Plan Head --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('plan_head', __('Plan Head'), ['class' => 'form-label']) }}
            {{ Form::text('plan_head', null, ['class' => 'form-control']) }}
        </div>

        {{-- Status --}}
        <div class="form-group col-md-12 mb-3">
            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}
            {{ Form::select('status',
                [1 => __('Active'), 0 => __('Inactive')],
                null,
                ['class' => 'form-select']
            ) }}
        </div>

    </div>
</div>
<div class="modal-footer">
    <button type="button"
            class="btn btn-light btn-rounded"
            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Update'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>
{{ Form::close() }}

