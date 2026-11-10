{{ Form::open(['url' => 'invoice', 'method' => 'post']) }}
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

        @if (isset($selectedCustomerId) && !empty($selectedCustomerId))
            {{ Form::hidden('customer_id', $selectedCustomerId) }}
        @else
            <div class="form-group">
                {{ Form::label('customer_id', __('Customer'), ['class' => 'form-label']) }}
                {!! Form::select('customer_id', $customer, null, ['class' => 'form-control select2', 'required']) !!}
            </div>
        @endif

        {{-- @if (!empty($order_id))
            {{ Form::hidden('order_id', $order_id) }}
        @else
            <div class="form-group">
                {{ Form::label('order_id', __('Select Order'), ['class' => 'form-label']) }}
                {!! Form::select('order_id', $orders, null, [
                    'class' => 'form-control select2',
                    'placeholder' => __('Select an Order'),
                ]) !!}
            </div>
        @endif --}}
        <div class="form-group">
            {{ Form::label('order_id', __('Order'), ['class' => 'form-label']) }}
            <div id="order_display" class="mt-2 text-primary fw-bold">
                {{ __('No order selected') }}
            </div>
        </div>


        <div class="form-group">
            {{ Form::label('due_date', __('Due Date'), ['class' => 'form-label']) }}
            {{ Form::date('due_date', null, ['class' => 'form-control', 'required' => 'required']) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    {{ Form::submit(__('Create'), ['class' => 'btn btn-secondary ml-10']) }}
</div>
{{ Form::close() }}

<script>
    $(document).on('change', '#customer_id', function() {
        let customerId = $(this).val();
        let orderDisplay = $('#order_display');

        orderDisplay.text("{{ __('Loading...') }}");

        if (customerId) {
            $.ajax({
                url: "{{ route('orders.byCustomer') }}",
                data: {
                    customer_id: customerId
                },
                success: function(data) {
                    orderDisplay.empty();

                    if ($.isEmptyObject(data)) {
                        orderDisplay.text("{{ __('No orders found') }}");
                    } else {
                        let orders = Object.values(data).join(', ');
                        orderDisplay.text(orders);

                    }
                }
            });
        } else {
            orderDisplay.text("{{ __('No order selected') }}");
        }
    });
</script>

