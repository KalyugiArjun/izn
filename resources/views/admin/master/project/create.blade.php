{{ Form::open(['route' => 'project-monitering-system.project.store', 'method' => 'post']) }}
<div class="modal-body">
    <div class="row">

        {{-- Cost --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('cost',
                __('Cost (₹)') . '<span class="text-danger"> *</span>',
                ['class' => 'form-label'], false) }}
            <div class="input-group">
                <span class="input-group-text">₹</span>
                {{ Form::number('cost', null, [
                    'class'       => 'form-control',
                    'placeholder' => '0.00',
                    'step'        => '0.01',
                    'min'         => '0',
                    'required'    => true,
                ]) }}
            </div>
        </div>

        {{-- Department --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('department_id', __('Department'). '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::select('department_id',
                ['' => __('-- Select Department --')] + $departments->toArray(),
                null,
                ['class' => 'form-select'  , 'required' => true],

            ) }}
        </div>

        {{-- Working Agencies --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('working_agencies', __('Working Agencies'). '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::text('working_agencies', null, [
                'class'       => 'form-control',
                'placeholder' => __('Enter working agencies'),
                'required' => true
            ]) }}
        </div>

        {{-- Sanction Date --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('sanction_date', __('Sanction Date'). '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::date('sanction_date', null, ['class' => 'form-control' , 'required' => true]) }}
        </div>

        {{-- TDC Date --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('tdc_date', __('TDC Date'). '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::date('tdc_date', null, ['class' => 'form-control'  , 'required' => true]) }}
        </div>

        {{-- Financial Year --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('financial_year', __('Financial Year'). '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::select('financial_year',
                ['' => __('-- Select Financial Year --')] + $financial_years,
                null,
                ['class' => 'form-select'  , 'required' => true]
            ) }}
        </div>

        {{-- Plan Head --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('plan_head', __('Plan Head'). '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::text('plan_head', null, [
                'class'       => 'form-control',
                'placeholder' => __('Enter plan head'),
                'required' => true
            ]) }}
        </div>

        {{-- Status --}}
        <div class="form-group col-md-12 mb-3">
            {{ Form::label('status', __('Status'). '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::select('status',
                [1 => __('Active'), 0 => __('Inactive')],
                1,
                ['class' => 'form-select' , 'required' => true]
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

