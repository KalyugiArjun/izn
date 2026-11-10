{{ Form::model($invoice, ['route' => ['invoice.update', encrypt($invoice->id)], 'method' => 'PUT']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group">
            {{ Form::label('invoice_id', __('Invoice Number'), ['class' => 'form-label']) }}
            <div class="input-group">
                <span class="input-group-text ">
                    {{ invoicePrefix() }}
                </span>
                {{ Form::text('invoice_id', $invoiceNumber, ['class' => 'form-control', 'placeholder' => __('Enter invoice Number'), 'required' => 'required']) }}
            </div>
        </div>
        <div class="form-group">
            {{ Form::label('invoice_date', __('Invoice Date'), ['class' => 'form-label']) }}
            {{ Form::date('invoice_date', date('Y-m-d'), ['class' => 'form-control', 'required' => 'required']) }}
        </div>
        <div class="form-group">
            {{ Form::label('customer_id', __('Customer'), ['class' => 'form-label']) }}
            {!! Form::select('customer_id', $customer, null, ['class' => 'form-control  select2', 'required' => 'required']) !!}
        </div>
        <div class="form-group">
            {{ Form::label('due_date', __('Due Date'), ['class' => 'form-label']) }}
            {{ Form::date('due_date', null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    {{ Form::submit(__('Update'), ['class' => 'btn btn-secondary ml-10']) }}
</div>
{{ Form::close() }}

