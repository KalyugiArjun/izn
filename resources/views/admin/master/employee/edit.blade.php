{{ Form::model($employee, [
    'route' => ['master.employee.update', encrypt($employee->id)],
    'method' => 'POST',
]) }}

<style>
    /* =========================================
       EDIT FORM: Jurisdiction Station Multiselect
       CSS is scoped to this field only
    ========================================= */

    #edit_station_ids + .select2-container {
        width: 100% !important;
        display: block !important;
    }

    /* Main bordered box owns the scrollbar */
    #edit_station_ids + .select2-container
    .select2-selection--multiple {
        display: block !important;
        width: 100% !important;
        min-height: 38px !important;
        max-height: 90px !important;
        height: auto !important;
        padding: 0 5px !important;
        border: 1px solid #ced4da !important;
        border-radius: 6px !important;
        background: #fff !important;
        overflow-x: hidden !important;
        overflow-y: auto !important;
        box-sizing: border-box !important;
    }

    /* Tags container - flex wrap, normal flow */
    #edit_station_ids + .select2-container
    .select2-selection--multiple .select2-selection__rendered {
        display: flex !important;
        flex-wrap: wrap !important;
        align-items: center !important;
        gap: 3px !important;
        width: 100% !important;
        padding: 3px 0 !important;
        margin: 0 !important;
        list-style: none !important;
        box-sizing: border-box !important;
        overflow: visible !important;
        max-height: none !important;
    }

    /* Individual selected station pill */
    #edit_station_ids + .select2-container
    .select2-selection__choice {
        display: inline-flex !important;
        align-items: center !important;
        flex-direction: row !important;
        flex-shrink: 0 !important;
        float: none !important;
        height: 24px !important;
        line-height: 22px !important;
        padding: 0 6px !important;
        margin: 0 !important;
        background: #e8f1ff !important;
        border: 1px solid #b8d2ff !important;
        color: #174ea6 !important;
        border-radius: 4px !important;
        font-size: 11.5px !important;
        font-weight: 500 !important;
        box-sizing: border-box !important;
        white-space: nowrap !important;
    }

    /* Station name */
    #edit_station_ids + .select2-container
    .select2-selection__choice__display {
        order: 1 !important;
        padding: 0 !important;
        margin: 0 !important;
        line-height: 22px !important;
        font-size: 11.5px !important;
        color: #174ea6 !important;
        white-space: nowrap !important;
    }

    /* Remove button */
    #edit_station_ids + .select2-container
    .select2-selection__choice__remove {
        order: 2 !important;
        position: static !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
        float: none !important;
        width: 14px !important;
        height: 22px !important;
        margin: 0 0 0 4px !important;
        padding: 0 0 0 4px !important;
        border: none !important;
        border-left: 1px solid #b8d2ff !important;
        border-radius: 0 !important;
        background: transparent !important;
        color: #174ea6 !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        line-height: 1 !important;
        cursor: pointer !important;
    }

    #edit_station_ids + .select2-container
    .select2-selection__choice__remove:hover {
        color: #dc3545 !important;
        background: transparent !important;
    }

    /* Search input inside multiple select */
    #edit_station_ids + .select2-container
    .select2-search--inline {
        display: inline-flex !important;
        align-items: center !important;
        margin: 0 !important;
        padding: 0 !important;
        height: 24px !important;
    }

    #edit_station_ids + .select2-container
    .select2-search__field {
        height: 22px !important;
        line-height: 22px !important;
        margin: 0 !important;
        padding: 0 4px !important;
        border: none !important;
        outline: none !important;
        font-size: 12px !important;
    }

    /* Scrollable dropdown options list */
    .select2-container--open .select2-results__options {
        max-height: 200px !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
    }

    /* Select All header inside open dropdown */
    .edit-station-select-all-wrap {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        gap: 8px !important;
        padding: 8px 12px !important;
        background: #f8f9fa !important;
        border-bottom: 1px solid #dee2e6 !important;
        box-sizing: border-box !important;
    }

    .edit-station-select-all-label {
        display: inline-flex !important;
        align-items: center !important;
        gap: 7px !important;
        margin: 0 !important;
        cursor: pointer !important;
        font-size: 12.5px !important;
        font-weight: 600 !important;
        color: #212529 !important;
        user-select: none !important;
    }

    .edit-station-select-all-label input[type="checkbox"] {
        margin: 0 !important;
        cursor: pointer !important;
        width: 14px !important;
        height: 14px !important;
    }

    .edit-station-count {
        color: #6c757d !important;
        font-size: 11.5px !important;
        font-weight: 400 !important;
        white-space: nowrap !important;
    }
</style>

<div class="modal-body">
    @if ($errors->any())
        <div class="alert alert-danger mb-3 p-2">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">

        {{-- Name --}}
        <div class="form-group col-md-3 mb-3">
            {{ Form::label('name', __('Name') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::text('name', null, [
                'class' => 'form-control',
                'placeholder' => __('Enter full name'),
                'required' => true,
            ]) }}
        </div>

        {{-- HRMS ID --}}
        <div class="form-group col-md-3 mb-3">
            {{ Form::label('usmid', __('HRMS ID'), ['class' => 'form-label'], false) }}
            {{ Form::text('usmid', null, [
                'class' => 'form-control',
                'placeholder' => __('Enter HRMS ID'),
                'required' => false,
            ]) }}
        </div>

        {{-- Department --}}
        <div class="form-group col-md-3 mb-3">
            {{ Form::label('department_id', __('Department') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::select(
                'department_id',
                ['' => __('-- Select Department --')] + $departments->toArray(),
                $employee->department_id,
                [
                    'class' => 'form-select select2-single',
                    'required' => true,
                    'id' => 'department_id',
                ]
            ) }}
        </div>

        {{-- Designation --}}
        <div class="form-group col-md-3 mb-3">
            {{ Form::label('designation_id', __('Designation') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::select(
                'designation_id',
                ['' => __('-- Select Designation --')] + ($designations ? $designations->toArray() : []),
                $employee->designation_id,
                [
                    'class' => 'form-select select2-single',
                    'required' => true,
                    'id' => 'designation_id',
                ]
            ) }}
        </div>

        {{-- Date of Birth --}}
        <div class="form-group col-md-3 mb-3">
            {{ Form::label('date_of_birth', __('Date of Birth'), ['class' => 'form-label'], false) }}
            {{ Form::date('date_of_birth', null, [
                'class' => 'form-control',
                'required' => false,
            ]) }}
        </div>

        {{-- Date of Appointment --}}
        <div class="form-group col-md-3 mb-3">
            {{ Form::label('date_of_appointment', __('Date of Appointment'), ['class' => 'form-label'], false) }}
            {{ Form::date('date_of_appointment', null, [
                'class' => 'form-control',
                'required' => false,
            ]) }}
        </div>

        {{-- Date of Retirement --}}
        <div class="form-group col-md-3 mb-3">
            {{ Form::label('date_of_retirement', __('Date of Retirement'), ['class' => 'form-label'], false) }}
            {{ Form::date('date_of_retirement', null, [
                'class' => 'form-control',
                'required' => false,
            ]) }}
        </div>

        {{-- Mobile --}}
        <div class="form-group col-md-3 mb-3">
            {{ Form::label('mobile', __('Mobile') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::text('mobile', null, [
                'class' => 'form-control',
                'placeholder' => __('Enter mobile number'),
                'maxlength' => 15,
                'pattern' => '[0-9]+',
                'oninput' => "this.value = this.value.replace(/[^0-9]/g, '')",
                'required' => true,
            ]) }}
        </div>

        {{-- Email --}}
        <div class="form-group col-md-3 mb-3">
            {{ Form::label('email', __('Email ID'), ['class' => 'form-label'], false) }}
            {{ Form::email('email', null, [
                'class' => 'form-control',
                'placeholder' => __('Enter email address'),
            ]) }}
        </div>

        {{-- Jurisdiction Stations --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('station_ids', __('Jurisdiction Station(s)') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}

            {{ Form::select('station_ids[]', $stations, $selectedStations, [
                'class' => 'form-select select2-multiple',
                'multiple' => 'multiple',
                'id' => 'edit_station_ids',
                'required' => true,
            ]) }}

            <small class="text-muted">
                {{ __('Select multiple stations or use Select All Stations.') }}
            </small>
        </div>

        {{-- Date of Posting --}}
        <div class="form-group col-md-3 mb-3">
            {{ Form::label('date_of_posting_present_station', __('Date of Posting at Present Working Place'), ['class' => 'form-label'], false) }}
            {{ Form::date('date_of_posting_present_station', null, [
                'class' => 'form-control',
                'required' => false,
            ]) }}
        </div>

        {{-- Present Working Place --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('posting_present_station', __('Posting At Present Working Place') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::text('posting_present_station', null, [
                'class' => 'form-control',
                'placeholder' => __('Enter posting at present Working Place'),
                'required' => true,
            ]) }}
        </div>

        {{-- Level --}}
        <div class="form-group col-md-3 mb-3">
            {{ Form::label('level', __('Level') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::select(
                'level',
                [
                    '' => __('-- Select Level --'),
                    'LEVEL-1' => 'LEVEL-1',
                    'LEVEL-2' => 'LEVEL-2',
                    'LEVEL-3' => 'LEVEL-3',
                    'LEVEL-4' => 'LEVEL-4',
                    'LEVEL-5' => 'LEVEL-5',
                    'LEVEL-6' => 'LEVEL-6',
                    'LEVEL-7' => 'LEVEL-7',
                    'LEVEL-8' => 'LEVEL-8',
                    'LEVEL-9' => 'LEVEL-9',
                    'LEVEL-10' => 'LEVEL-10',
                    'LEVEL-11' => 'LEVEL-11',
                    'LEVEL-12' => 'LEVEL-12',
                ],
                $employee->level,
                [
                    'class' => 'form-select select2-single',
                    'required' => true,
                    'id' => 'level',
                ]
            ) }}
        </div>

        {{-- Status --}}
        <div class="form-group col-md-3 mb-3">
            {{ Form::label('status', __('Status') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::select(
                'status',
                [
                    1 => __('Active'),
                    0 => __('Inactive'),
                ],
                $employee->status,
                [
                    'class' => 'form-select select2-single',
                    'required' => true,
                    'id' => 'status',
                ]
            ) }}
        </div>

    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light btn-rounded" data-bs-dismiss="modal">
        {{ __('Cancel') }}
    </button>

    {{ Form::submit(__('Update'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>

{{ Form::close() }}

<script>
(function ($) {
    'use strict';

    function initializeEmployeeEditForm() {
        const $stationSelect = $('#edit_station_ids');

        if (!$stationSelect.length) {
            return;
        }

        const $modalParent = $stationSelect.closest('.modal');
        const $dropdownParent = $modalParent.length
            ? $modalParent
            : $(document.body);

        /* Initialize station multiselect once */
        if (!$stationSelect.hasClass('select2-hidden-accessible')) {
            $stationSelect.select2({
                dropdownParent: $dropdownParent,
                placeholder: '-- Select Stations --',
                allowClear: false,
                width: '100%',
                closeOnSelect: false
            });
        }

        function getAllStationValues() {
            return $stationSelect.find('option').map(function () {
                return this.value;
            }).get().filter(function (value) {
                return value !== '';
            });
        }

        function updateStationHeader() {
            const $dropdown = $('.select2-container--open .select2-dropdown');
            const $checkbox = $dropdown.find('.edit-station-select-all-checkbox');

            if (!$checkbox.length) {
                return;
            }

            const allValues = getAllStationValues().map(String);
            const selectedValues = ($stationSelect.val() || []).map(String);

            const selectedCount = allValues.filter(function (value) {
                return selectedValues.includes(value);
            }).length;

            $checkbox.prop(
                'checked',
                allValues.length > 0 && selectedCount === allValues.length
            );

            $checkbox.prop(
                'indeterminate',
                selectedCount > 0 && selectedCount < allValues.length
            );

            $dropdown.find('.edit-station-count').text(
                selectedCount + ' of ' + allValues.length + ' selected'
            );
        }

        /* Avoid duplicate event handlers after modal reload */
        $stationSelect
            .off('.editStationSelectAll')
            .on('select2:open.editStationSelectAll', function () {
                const $dropdown = $('.select2-container--open .select2-dropdown');

                if (!$dropdown.find('.edit-station-select-all-wrap').length) {
                    const $header = $(`
                        <div class="edit-station-select-all-wrap">
                            <label class="edit-station-select-all-label">
                                <input
                                    type="checkbox"
                                    class="edit-station-select-all-checkbox"
                                >
                                <span>Select All Stations</span>
                            </label>
                            <span class="edit-station-count"></span>
                        </div>
                    `);

                    $dropdown.prepend($header);

                    $header.find('.edit-station-select-all-checkbox')
                        .on('change', function () {
                            const values = getAllStationValues();

                            $stationSelect
                                .val(this.checked ? values : [])
                                .trigger('change');

                            updateStationHeader();
                        });
                }

                updateStationHeader();
            })
            .on('change.editStationSelectAll', updateStationHeader);

        /* Initialize single-select dropdowns once */
        $('.select2-single').each(function () {
            const $select = $(this);

            if ($select.hasClass('select2-hidden-accessible')) {
                return;
            }

            $select.select2({
                dropdownParent: $dropdownParent,
                width: '100%'
            });
        });

        /* Department change -> reload Designation options */
        $(document)
            .off('change.employeeDesignation', '#department_id')
            .on('change.employeeDesignation', '#department_id', function () {
                const deptId = $(this).val();
                const $designation = $('#designation_id');

                $designation.empty().append(
                    $('<option>', {
                        value: '',
                        text: '-- Select Designation --'
                    })
                ).val('').trigger('change');

                if (!deptId) {
                    return;
                }

                fetch(
                    `{{ route('authority.complain.designations') }}?department_id=${encodeURIComponent(deptId)}`
                )
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('Unable to load designations');
                        }

                        return response.json();
                    })
                    .then(function (data) {
                        if (Array.isArray(data)) {
                            data.forEach(function (item) {
                                $designation.append($('<option>', {
                                    value: item.id,
                                    text: item.name
                                }));
                            });
                        } else if (typeof data === 'object' && data !== null) {
                            Object.entries(data).forEach(function ([id, name]) {
                                if (typeof name === 'object' && name !== null) {
                                    $designation.append($('<option>', {
                                        value: name.id,
                                        text: name.name
                                    }));
                                } else {
                                    $designation.append($('<option>', {
                                        value: id,
                                        text: name
                                    }));
                                }
                            });
                        }

                        $designation.trigger('change');
                    })
                    .catch(function (error) {
                        console.error('Designation loading error:', error);
                    });
            });
    }

    /* Works for both normal and AJAX-loaded forms */
    $(initializeEmployeeEditForm);

})(jQuery);
</script>
