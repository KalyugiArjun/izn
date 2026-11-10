@extends('layouts.app')

@section('page-title')
    {{ __('Create Assigned Station') }}
@endsection


@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Master') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('master.working-area') }}">{{ __('Assigned Station') }}</a></li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Create') }}</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Create Assigned Station') }}</h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('master.working-area.store') }}" method="POST">
                        @csrf

                        <div class="row">

                            {{-- Division --}}
                            <div class="form-group col-md-6 mb-3">
                                <label class="form-label">
                                    Division <span class="text-danger">*</span>
                                </label>
                                <select name="division_id" id="division_id" class="form-select" required>
                                    <option value="">Select Division</option>
                                    @foreach ($divisions as $division)
                                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Department --}}
                            <div class="form-group col-md-6 mb-3">
                                <label class="form-label">
                                    Department <span class="text-danger">*</span>
                                </label>
                                <select name="department_id" id="department_id" class="form-select" required>
                                    <option value="">Select Department</option>
                                </select>
                            </div>

                            {{-- Designation --}}
                            <div class="form-group col-md-6 mb-3">
                                <label class="form-label">
                                    Designation <span class="text-danger">*</span>
                                </label>
                                <select name="designation_id" id="designation_id" class="form-select" required>
                                    <option value="">Select Designation</option>
                                </select>
                            </div>

                            {{-- Mobile --}}
                            <div class="form-group col-md-6 mb-3">
                                <label class="form-label">Mobile</label><span class="text-danger">*</span>
                                <input type="text" name="mobile" id="mobile" class="form-control" readonly>
                            </div>

                            {{-- Email --}}
                            <div class="form-group col-md-6 mb-3">
                                <label class="form-label">Email</label><span class="text-danger">*</span>
                                <input type="email" name="email" id="email" class="form-control" readonly>
                            </div>

                            {{-- Head Station --}}
                            <div class="form-group col-md-6 mb-3">
                                <label class="form-label">Head Station Name</label><span class="text-danger">*</span>
                                <input type="text" name="head_station_name" class="form-control" required>
                            </div>

                            {{-- Stations --}}
                            <div class="form-group col-md-12 mb-3">
                                <label class="form-label">
                                    Stations <span class="text-danger">*</span>
                                </label>
                                <select name="station_ids[]" id="station_ids"  class="form-control select2-multiple" multiple
                                required>
                                    @foreach ($stations as $station)
                                        <option value="{{ $station->id }}">{{ strtoupper($station->name) }} ({{ strtoupper($station->code) }})</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('master.working-area') }}" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection



<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>



<script>
    $(document).ready(function() {

        console.log("JS Loaded ✅");



        // Division → Departments
        $('#division_id').on('change', function() {

            console.log("Division changed");

            let divisionId = $(this).val();

            $('#department_id').html('<option value="">Select Department</option>');
            $('#designation_id').html('<option value="">Select Designation</option>');
            $('#mobile').val('');
            $('#email').val('');

            if (!divisionId) return;

            $.get("{{ route('ajax.get-departments', '') }}/" + divisionId, function(data) {
                $.each(data, function(i, dept) {
                    $('#department_id').append('<option value="' + dept.id + '">' + dept
                        .name + '</option>');
                });
            });
        });

        // Department → Designation
        $('#department_id').on('change', function() {

            let departmentId = $(this).val();

            $('#designation_id').html('<option value="">Select Designation</option>');
            $('#mobile').val('');
            $('#email').val('');

            if (!departmentId) return;

            $.get("{{ route('ajax.get-designations', '') }}/" + departmentId, function(data) {
                $.each(data, function(i, desig) {
                    $('#designation_id').append('<option value="' + desig.id + '">' +
                        desig.name + '</option>');
                });
            });
        });

        // Designation → Auto fill
        $('#designation_id').on('change', function() {

            let designationId = $(this).val();

            $('#mobile').val('');
            $('#email').val('');

            if (!designationId) return;

            $.get("{{ route('ajax.get-designation-detail', '') }}/"+ designationId, function(data) {
                $('#mobile').val(data.mobile || '');
                $('#email').val(data.email || '');
            });
        });

    });
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



