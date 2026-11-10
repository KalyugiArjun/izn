@extends('layouts.app')

@section('page-title')
    {{ __('Edit Assigned Station') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Master') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('master.working-area') }}">{{ __('Assigned Station') }}</a></li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Edit') }}</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Edit Assigned Station') }}</h5>
                </div>
                <div class="card-body">
                    {{ Form::model($workingArea, ['route' => ['master.working-area.update', encrypt($workingArea->id)], 'method' => 'POST']) }}
                    <div class="row">

                        {{-- Division --}}
                        <div class="form-group col-md-6 mb-3">
                            {{ Form::label('division_id', __('Division') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
                            {{ Form::select('division_id', ['' => __('Select Division')] + $divisions->pluck('name', 'id')->toArray(), $workingArea->division_id, ['class' => 'form-select', 'id' => 'division_id', 'required']) }}
                        </div>

                        {{-- Department --}}
                        <div class="form-group col-md-6 mb-3">
                            {{ Form::label('department_id', __('Department') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
                            <select name="department_id" id="department_id" class="form-select" required>
                                <option value="">{{ __('Select Department') }}</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->id }}"
                                        {{ $workingArea->department_id == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Designation --}}
                        <div class="form-group col-md-6 mb-3">
                            {{ Form::label('designation_id', __('Designation') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
                            <select name="designation_id" id="designation_id" class="form-select" required>
                                <option value="">{{ __('Select Designation') }}</option>
                                @foreach ($designations as $desig)
                                    <option value="{{ $desig->id }}"
                                        {{ $workingArea->designation_id == $desig->id ? 'selected' : '' }}>
                                        {{ $desig->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Mobile (auto-filled) --}}
                        <div class="form-group col-md-6 mb-3">
                            {{ Form::label('mobile', __('Mobile'), ['class' => 'form-label']) }}
                            {{ Form::text('mobile', $workingArea->mobile, ['class' => 'form-control', 'id' => 'mobile', 'placeholder' => __('Auto filled from designation'), 'readonly']) }}
                        </div>

                        {{-- Email (auto-filled) --}}
                        <div class="form-group col-md-6 mb-3">
                            {{ Form::label('email', __('Email'), ['class' => 'form-label']) }}
                            {{ Form::email('email', $workingArea->email, ['class' => 'form-control', 'id' => 'email', 'placeholder' => __('Auto filled from designation'), 'readonly']) }}
                        </div>

                        {{-- Head Station Name --}}
                        <div class="form-group col-md-6 mb-3">
                            {{ Form::label('head_station_name', __('Head Station Name'), ['class' => 'form-label']) }}
                            {{ Form::text('head_station_name', null, ['class' => 'form-control', 'placeholder' => __('Enter head station name')]) }}
                        </div>

                        {{-- Stations (multiple) --}}
                        <div class="form-group col-md-12 mb-3">
                            {{ Form::label('station_ids', __('Stations') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
                            <select name="station_ids[]" id="station_ids" class="form-select select2-multiple" multiple
                                required>
                                @foreach ($stations as $station)
                                    <option value="{{ $station->id }}"
                                        {{ in_array($station->id, $selectedStations) ? 'selected' : '' }}>
                                        {{ strtoupper($station->name) }} ({{ strtoupper($station->code) }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ __('Hold Ctrl / Cmd to select multiple') }}</small>
                        </div>

                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('master.working-area') }}"
                            class="btn btn-light btn-rounded">{{ __('Cancel') }}</a>
                        {{ Form::submit(__('Update'), ['class' => 'btn btn-secondary btn-rounded']) }}
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function() {

        console.log("js load");

        // Division → load Departments
        $('#division_id').on('change', function() {

            console.log("js work");

            const divisionId = $(this).val();
            $('#department_id').html('<option value="">{{ __('Select Department') }}</option>');
            $('#designation_id').html('<option value="">{{ __('Select Designation') }}</option>');
            $('#mobile').val('');
            $('#email').val('');

            if (!divisionId) return;

            $.get('{{ url('ajax/get-departments') }}/' + divisionId, function(data) {
                $.each(data, function(i, dept) {
                    $('#department_id').append(
                        `<option value="${dept.id}">${dept.name}</option>`);
                });
            });
        });

        // Department → load Designations
        $('#department_id').on('change', function() {
            const departmentId = $(this).val();
            $('#designation_id').html('<option value="">{{ __('Select Designation') }}</option>');
            $('#mobile').val('');
            $('#email').val('');

            if (!departmentId) return;

            $.get('{{ url('ajax/get-designations') }}/' + departmentId, function(data) {
                $.each(data, function(i, desig) {
                    $('#designation_id').append(
                        `<option value="${desig.id}">${desig.name}</option>`);
                });
            });
        });

        // Designation → auto-fill mobile & email
        $('#designation_id').on('change', function() {
            const designationId = $(this).val();
            if (!designationId) return;

            $.get('{{ url('ajax/get-designation-detail') }}/' + designationId, function(data) {
                $('#mobile').val(data.mobile ?? '');
                $('#email').val(data.email ?? '');
            });
        });

    });

    // Select2
</script>

@push('scripts')
    <script>
        $('.select2-multiple').select2({
            placeholder: '{{ __('Select Stations') }}',
            allowClear: true,
            width: '100%'
        });
    </script>
@endpush

