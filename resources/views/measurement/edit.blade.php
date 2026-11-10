@extends('layouts.app')
@section('page-title')
    {{ __('Edit Measurement') }}
@endsection
@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('measurement.index') }}">{{ __('Measurement') }}</a>
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
                    {{ Form::model($measurement, ['route' => ['measurement.update', encrypt($measurement->id)], 'method' => 'PUT']) }}
                    <div class="row">
                        <div class="form-group col-md-4 col-lg-4">
                            <div class="form-group">
                                {{ Form::label('measurement_id', __('Measurement Number'), ['class' => 'form-label']) }}
                                <div class="input-group">
                                    <span class="input-group-text ">
                                        {{ measurementPrefix() }}
                                    </span>
                                    {{ Form::text('measurement_id', $measurementNumber, ['class' => 'form-control', 'placeholder' => __('Enter measurement Number')]) }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-4 col-lg-4">
                            {{ Form::label('date', __('Date') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
                            {{ Form::date('date', date('Y-m-d'), ['class' => 'form-control', 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-md-4 col-lg-4">
                            {{ Form::label('customer', __('Customer') . '<span/ class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
                            {!! Form::select('customer', $customer, null, ['class' => 'form-control  select2']) !!}
                        </div>
                        <div class="form-group col-md-4 col-lg-4">
                            {{ Form::label('cloth_type', __('Cloth Type') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
                            {!! Form::select('cloth_type', $clothType, null, ['class' => 'form-control select2  select2']) !!}
                        </div>
                        <div class="form-group col-md-4 col-lg-4">
                            {{ Form::label('responsible', __('Responsible User'), ['class' => 'form-label']) }}
                            {!! Form::select('responsible', $user, null, ['class' => 'form-control  select2']) !!}
                        </div>
                    </div>
                    <div class="row">
                        <h5 class="mb-20 mt-10">
                            {{ __('Measurement Type') }}
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
                                    @foreach ($measurement->measurement_detail as $details)
                                        <tr class="measure_type">
                                            <td><input type="text" class="form-control" name="type[]"
                                                    value="{{ $details->type }}"></td>
                                            <td><input type="number" class="form-control" name="measurement[]"
                                                    step="0.1" value="{{ $details->measurement }}"></td>
                                            <td><input type="text" class="form-control" name="unit[]"
                                                    value="{{ $details->unit }}"></td>
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

            $('#cloth_type').on('change', function() {
                var cloty_type_id = $(this).val();
                $.ajax({
                    url: '{{ route('measurement.type') }}',
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        'cloty_type_id': cloty_type_id
                    },
                    cache: false,
                    success: function(data) {
                        var response = JSON.parse(data);
                        var tbody = $('table tbody');
                        tbody.empty(); // Clear existing rows

                        response.forEach(function(item) {
                            var row = '<tr class="measure_type">' +
                                '<td><input type="text" class="form-control" name="type[]" value="' +
                                item.title + '"></td>' +
                                '<td><input type="number" class="form-control" name="measurement[]" step="0.1" value=""></td>' +
                                '<td><input type="text" class="form-control" name="unit[]" value="' +
                                item.units.unit + '"></td>' +
                                '<td> <a href="#" class="f-20 text-danger cloth_type_remove btn-sm"><i class="ti ti-trash"></i></a></td>' +
                                '</tr>';
                            tbody.append(row);
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("An error occurred: " + error);
                    }
                });
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

