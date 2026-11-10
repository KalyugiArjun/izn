@extends('layouts.app')

@section('page-title')
    {{ __('Emergency Contacts Report') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Master') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">
        {{ __('Emergency Contacts Report') }}
    </li>
@endsection

@section('content')
    <div class="row">

        <div class="col-sm-12">
            <div class="card">

                <div class="card-header">
                    <h5>{{ __('Emergency Contacts Report') }}</h5>
                </div>

                <div class="card-body">

                    <div class="row align-items-end g-3">

                        <div class="form-group col-md-3">
                            <label class="form-label">{{ __('Department') }}</label>
                            <select id="department_id" class="form-control select2">
                                <option value="">{{ __('Select Department') }}</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label class="form-label">{{ __('Designation') }}</label>
                            <select id="designation_id" class="form-control select2" disabled>
                                <option value="">{{ __('Select Designation') }}</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label class="form-label">{{ __('Employee') }}</label>
                            <select id="employee_id" class="form-control select2" disabled>
                                <option value="">{{ __('Select Employee') }}</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <button type="button" id="search_btn" class="btn btn-primary w-100" disabled>
                                <i class="ti ti-search align-text-bottom"></i> {{ __('Search') }}
                            </button>
                        </div>

                    </div>

                </div>

            </div>
        </div>
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header">
                    <h5>{{ __('Report Result') }}</h5>
                </div>

                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table table-hover" id="result_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Department') }}</th>
                                    <th>{{ __('Designation') }}</th>
                                    <th>{{ __('Mobile Number') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Address') }}</th>
                                    <th>{{ __('Relation') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>
                                    {{-- colspan 8 hi hona chahiye, thead mein 8 <th> hain --}}
                                    <td colspan="8" class="text-center text-muted">
                                        {{ __('Please select department, designation and employee to view report.') }}
                                    </td>
                                </tr>
                            </tbody>

                        </table>

                    </div>

                </div>

            </div>
        </div>

    </div>
@endsection

@push('script-page')
    <script>

        $(document).ready(function() {

            const emptyRow =
                `<tr><td colspan="8" class="text-center text-muted">{{ __('Please select department, designation and employee to view report.') }}</td></tr>`;
            function setSelectOptions($select, items, placeholder, enable) {

                let options = `<option value="">${placeholder}</option>`;

                items.forEach(function(item) {
                    options += `<option value="${item.id}">${item.name}</option>`;
                });

                if ($select.hasClass('select2-hidden-accessible')) {
                    $select.select2('destroy');
                }

                $select.html(options);
                $select.prop('disabled', !enable);

                $select.select2({
                    placeholder: placeholder,
                    width: '100%'
                });
            }

            function resetSelect($select, placeholder) {
                setSelectOptions($select, [], placeholder, false);
            }

            function initResultDataTable() {
                if ($.fn.DataTable.isDataTable('#result_table')) {
                    $('#result_table').DataTable().destroy();
                }
                $('#result_table').DataTable({
                    pageLength: 10,
                    lengthChange: true,
                    ordering: true,
                   
                    columnDefs: [{
                        targets: '_all',
                        defaultContent: ''
                    }]
                });
            }

            function destroyResultDataTableIfAny() {
                if ($.fn.DataTable.isDataTable('#result_table')) {
                    $('#result_table').DataTable().destroy();
                }
            }

            setSelectOptions($('#department_id'),
                @json($departments->map(fn($d) => ['id' => $d->id, 'name' => $d->name])),
                '{{ __('Select Department') }}', true);

            resetSelect($('#designation_id'), '{{ __('Select Designation') }}');
            resetSelect($('#employee_id'), '{{ __('Select Employee') }}');

            $('#department_id').on('change', function() {

                const departmentId = $(this).val();

                resetSelect($('#designation_id'), '{{ __('Select Designation') }}');
                resetSelect($('#employee_id'), '{{ __('Select Employee') }}');
                $('#search_btn').prop('disabled', true);

                destroyResultDataTableIfAny();
                $('#result_table tbody').html(emptyRow);

                if (!departmentId) return;

                $.get('{{ route('emergency-contact-report.designations') }}', {
                        department_id: departmentId
                    })
                    .done(function(designations) {
                        setSelectOptions($('#designation_id'), designations,
                            '{{ __('Select Designation') }}', true);
                    })
                    .fail(function(xhr) {
                        alert('{{ __('Failed to load designations.') }}');
                    });
            });

            $('#designation_id').on('change', function() {

                const departmentId = $('#department_id').val();
                const designationId = $(this).val();

                resetSelect($('#employee_id'), '{{ __('Select Employee') }}');
                $('#search_btn').prop('disabled', true);

                destroyResultDataTableIfAny();
                $('#result_table tbody').html(emptyRow);

                if (!designationId) return;

                $.get('{{ route('emergency-contact-report.employees') }}', {
                        department_id: departmentId,
                        designation_id: designationId,
                    })
                    .done(function(employees) {
                        setSelectOptions($('#employee_id'), employees,
                            '{{ __('Select Employee') }}', true);
                    })
                    .fail(function(xhr) {
                        alert('{{ __('Failed to load employees.') }}');
                    });
            });
            $('#employee_id').on('change', function() {
                $('#search_btn').prop('disabled', !$(this).val());
            });
            $('#search_btn').on('click', function() {

                const employeeId = $('#employee_id').val();

                if (!employeeId) return;

                destroyResultDataTableIfAny();
                $('#result_table tbody').html(
                    '<tr><td colspan="8" class="text-center text-muted">{{ __('Loading...') }}</td></tr>'
                );

                $.get('{{ route('emergency-contact-report.search') }}', {
                        employee_id: employeeId
                    })
                    .done(function(html) {
                        $('#result_table tbody').html(html);
                        initResultDataTable();
                    })
                    .fail(function(xhr) {
                        $('#result_table tbody').html(
                            '<tr><td colspan="8" class="text-center text-danger">{{ __('Something went wrong.') }}</td></tr>'
                        );
                    });
            });

        });
    </script>
@endpush
