@extends('layouts.app')
@php
    $profile = asset(Storage::url('upload/profile/'));
@endphp
@section('page-title')
    {{ __('Customer') }}
@endsection
@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('customer.index') }}">{{ __('Customer') }}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#">
                {{ __('Create') }}
            </a>
        </li>
    </ul>
@endsection

@section('content')
    <div class="row">
        {{ Form::open(['route' => 'customer.store', 'method' => 'post']) }}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center g-2">
                            <div class="col">
                                <h5>{{ __('Customer Create') }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                {{ Form::label('name', __('Name') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
                                {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter name'), 'required' => 'required']) }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('email', __('Email') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
                                {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('Enter email'), 'required' => 'required']) }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('password', __('Password'), ['class' => 'form-label']) }}
                                {{ Form::password('password', ['class' => 'form-control', 'placeholder' => __('Enter password'), 'required' => 'required', 'minlength' => '6']) }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('phone_number', __('Phone Number') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
                                {{ Form::text('phone_number', null, ['class' => 'form-control', 'placeholder' => __('Enter phone number'), 'required' => 'required']) }}
                                <small class="form-text text-muted">
                                    {{ __('Please enter the number with country code. e.g., +91XXXXXXXXXX') }}
                                </small>
                            </div>
                            <div class="form-group col-md-6">
                                {{ Form::label('city', __('City') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
                                {{ Form::text('city', null, ['class' => 'form-control', 'placeholder' => __('Enter city'), 'required' => 'required']) }}
                            </div>
                            <div class="form-group  col-md-6">
                                {{ Form::label('address', __('Address'), ['class' => 'form-label']) }}
                                {{ Form::textarea('address', null, ['class' => 'form-control', 'rows' => 3]) }}
                            </div>
                            <div class="form-group  col-md-6">
                                {{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}
                                {{ Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 3]) }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="row wrapper">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center g-2">
                            <div class="col">
                                <h5>{{ __('Measurement Create') }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
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
                                {{ Form::label('cloth_type', __('Cloth Type') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
                                {!! Form::select('cloth_type', $clothType, null, ['class' => 'form-control  select2', 'required' => 'required']) !!}
                            </div>
                            <div class="form-group col-md-4 col-lg-4">
                                {{ Form::label('responsible', __('Responsible User'), ['class' => 'form-label']) }}
                                {!! Form::select('responsible', $user, old('responsible'), ['class' => 'form-control  select2']) !!}
                            </div>
                        </div>
                        <div class="row">
                            <h5 class="mb-20 mt-10">
                                {{ __('Measurement Type') }}
                                <a href="#" class="btn btn-secondary btn-xs measure_type_clone float-end d-none"><i
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

                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="text-end p-2">
            {{ Form::submit(__('Create'), ['class' => 'btn btn-secondary ml-10']) }}
        </div>
        {{ Form::close() }}
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
                        $('.measure_type_clone').removeClass('d-none');
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

