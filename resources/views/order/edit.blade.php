@extends('layouts.app')
@section('page-title')
    {{ __('Edit Order') }}
@endsection
@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('order.index') }}">{{ __('Order') }}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#">
                {{ __('Edit') }}
            </a>
        </li>
    </ul>
@endsection

@section('content')
    <div class="row wrapper">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{ Form::model($order, ['route' => ['order.update', encrypt($order->id)], 'method' => 'PUT', 'enctype' => 'multipart/form-data']) }}
                    <div class="row">
                        <div class="form-group col-md-4 col-lg-4">
                            <div class="form-group">
                                {{ Form::label('order_id', __('Order Number'), ['class' => 'form-label']) }}
                                <div class="input-group">
                                    <span class="input-group-text ">
                                        {{ orderPrefix() }}
                                    </span>
                                    {{ Form::text('order_id', $orderNumber, ['class' => 'form-control', 'placeholder' => __('Enter order Number')]) }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-4 col-lg-4">
                            {{ Form::label('customer_id', __('Customer') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
                            {!! Form::select('customer_id', $customer, null, ['class' => 'form-control  select2']) !!}
                        </div>
                        <div class="form-group col-md-4 col-lg-4">
                            {{ Form::label('order_date', __('Order Date') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
                            {{ Form::date('order_date', null, ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-4 col-lg-4">
                            {{ Form::label('deadline_date', __('Deadline Date') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
                            {{ Form::date('deadline_date', null, ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-4 col-lg-4">
                            {{ Form::label('gender', __('Gender') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
                            {!! Form::select('gender', $gender, null, ['class' => 'form-control  select2 select2']) !!}
                        </div>
                        <div class="form-group col-md-4 col-lg-4">
                            {{ Form::label('cloth_type', __('Cloth Type') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
                            {!! Form::select('cloth_type', $clothType, null, ['class' => 'form-control  select2']) !!}
                        </div>

                        <div class="form-group col-md-4 col-lg-4">
                            {{ Form::label('quantity', __('Quantity') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
                            {{ Form::number('quantity', null, ['class' => 'form-control', 'placeholder' => __('Enter quantity'), 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-4 col-lg-4">
                            {{ Form::label('febric', __('Febric') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
                            {{ Form::text('febric', null, ['class' => 'form-control', 'placeholder' => __('Enter febric'), 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-4 col-lg-4">
                            {{ Form::label('febric_color', __('Febric Color') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
                            {{ Form::text('febric_color', null, ['class' => 'form-control', 'placeholder' => __('Enter febric color'), 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-4 col-lg-4">
                            {{ Form::label('fabric_attachment', __('Fabric Attachment'), ['class' => 'form-label']) }}
                            @if (!empty($order->fabric_attachment))
                                <a href="{{ asset(Storage::url('upload/fabric_attachment/')) . '/' . $order->fabric_attachment }}"
                                    target="_blank" title="{{ __('show fabric attachment') }}">
                                    <i class="ti ti-eye ms-2 f-15"></i>
                                </a>
                            @endif
                            {{ Form::file('fabric_attachment', ['class' => 'form-control', 'accept' => '.png', '.jpg', '.jpeg']) }}
                        </div>
                        <div class="form-group col-md-4 col-lg-4">
                            {{ Form::label('sewing_pattern', __('Swing Pattren'), ['class' => 'form-label']) }}
                            @if (!empty($order->sewing_pattern))
                                <a href="{{ asset(Storage::url('upload/sewing_pattern/')) . '/' . $order->sewing_pattern }}"
                                    target="_blank" title="{{ __('show sewing pattern') }}">
                                    <i class="ti ti-eye ms-2 f-15"></i>
                                </a>
                            @endif
                            {{ Form::file('sewing_pattern', ['class' => 'form-control', 'accept' => '.png', '.jpg', '.jpeg']) }}
                        </div>

                        <div class="form-group col-md-4 col-lg-4">
                            {{ Form::label('responsible', __('Responsible User') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
                            {!! Form::select('responsible', $user, null, ['class' => 'form-control  select2', 'required' => 'required']) !!}
                        </div>
                        <div class="form-group col-md-4 col-lg-4">
                            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}
                            {!! Form::select('status', $status, null, [
                                'class' => 'form-control  select2 select2',
                                'required' => 'required',
                            ]) !!}
                        </div>
                        <div class="form-group col-md-4 col-lg-4">
                            {{ Form::label('notes', __('Note'), ['class' => 'form-label']) }}
                            {{ Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => __('Enter notes')]) }}
                        </div>
                    </div>
                    <div class="row">
                        <h5 class="mb-20 mt-10">
                            {{ __('Measurement Detail') }}
                            <a href="#" class="btn btn-secondary btn-xs measure_type_clone float-end "><i
                                    class="ti ti-plus"></i></a>
                        </h5>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('Type') }}</th>
                                        <th>{{ __('Measurement') }}</th>
                                        <th>{{ __('Unit') }}</th>
                                        <th>{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->measurement as $measurement)
                                        <tr class="measure_type">
                                            <td><input type="text" class="form-control" name="type[]"
                                                    value="{{ $measurement->type }}"></td>
                                            <td><input type="number" class="form-control" name="measurement[]"
                                                    step="0.1" value="{{ $measurement->measurement }}"></td>
                                            <td><input type="text" class="form-control" name="unit[]"
                                                    value="{{ $measurement->unit }}"></td>
                                            <td><a href="#" class="f-20 text-danger cloth_type_remove btn-sm"><i
                                                        class="ti ti-trash"></i></a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-20">
                        <div class="form-group text-end">
                            {{ Form::submit(__('Update'), ['class' => 'btn btn-secondary ml-10']) }}
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script-page')
    <script>
        $(document).ready(function() {
            $(document).on('change', '#cloth_type,#customer_id', function(e) {
                var cloty_type_id = $('#cloth_type').val();
                var customer_id = $('#customer_id').val();
                if (customer_id != '' && cloty_type_id != '') {
                    $.ajax({
                        url: '{{ route('customer.measurement') }}',
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            'cloty_type_id': cloty_type_id,
                            'customer_id': customer_id
                        },
                        cache: false,
                        success: function(data) {
                            if (data != '') {
                                var response = JSON.parse(data);
                                var tbody = $('table tbody');
                                tbody.empty(); // Clear existing rows
                                response.forEach(function(item) {

                                    var row = '<tr class="measure_type">' +
                                        '<td><input type="text" class="form-control" name="type[]" value="' +
                                        item.type + '"></td>' +
                                        '<td><input type="number" class="form-control" name="measurement[]" step="0.1" value="' +
                                        item.measurement + '"></td>' +
                                        '<td><input type="text" class="form-control" name="unit[]" value="' +
                                        item.unit + '"></td>' +
                                        '<td> <a href="#" class="f-20 text-danger cloth_type_remove btn-sm"><i class="ti ti-trash"></i></a></td>' +
                                        '</tr>';
                                    tbody.append(row);
                                });
                                $('.measure_type_clone').removeClass('d-none');
                            }

                        },
                        error: function(xhr, status, error) {
                            console.error("An error occurred: " + error);
                        }
                    });
                }

            });
        });

        $('.wrapper').on('click', '.cloth_type_remove', function() {
            $(this).parent().parent().remove();
        });

        $('.wrapper').on('click', '.measure_type_clone', function() {
            let $clone = $(this).closest('.wrapper').find('.measure_type').first().clone();
            $clone.find('input').val('');
            $clone.appendTo($('table tbody'));
        });
    </script>
@endpush

