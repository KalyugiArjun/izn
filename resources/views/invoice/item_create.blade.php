{{ Form::open(array('route' => array('invoice.item.store', $invoice_id),'method'=>'post')) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12">
            {{ Form::label('cloth_type_id', __('Item'),['class'=>'form-label']) }}
            {!! Form::select('cloth_type_id', $items,null,array('class' => 'form-control select2','required'=>'required')) !!}
        </div>
        <div class="form-group  col-md-12">
            {{ Form::label('quantity', __('Quantity'),['class'=>'form-label']) }}
            {{ Form::text('quantity',null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="form-group  col-md-12">
            {{ Form::label('amount', __('Amount'),['class'=>'form-label']) }}
            {{ Form::text('amount',null, array('class' => 'form-control','required'=>'required')) }}
        </div>
        <div class="form-group  col-md-12">
            {{ Form::label('note', __('Note'),['class'=>'form-label']) }}
            {{ Form::textarea('note', null, array('class' => 'form-control','rows'=>2)) }}
        </div>
        <input type="hidden" name="tax" id="tax">
    </div>
</div>
<div class="modal-footer">

    {{Form::submit(__('Create'),array('class'=>'btn btn-secondary ml-10'))}}
</div>
{{Form::close()}}
<script>

    $(document).on('change', '#cloth_type_id', function () {
        var cloth_type_id = $(this).val();
        $.ajax({
            url: "{{route('get.invoice.item.details')}}",
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                'cloth_type_id': cloth_type_id
            },
            cache: false,
            success: function (data) {
                var item = JSON.parse(data);
                $('#quantity').val(1);
                $('#amount').val(item.amount);
                $('#note').val(item.note);
                $('#tax').val(item.taxes);
            },
        });
    });
</script>

