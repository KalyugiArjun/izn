{{ Form::open(['route' => 'master.employee.store', 'method' => 'post']) }}

<style>
    /* Station field - normal size */
    #station_ids + .select2-container {
        width: 100% !important;
        display: block;
    }

    #station_ids + .select2-container .select2-selection--multiple {
        min-height: 38px !important;
        height: auto !important;
        max-height: 90px !important;
        overflow: hidden !important;
        border: 1px solid #ced4da;
        border-radius: 6px;
        padding: 3px 6px;
        background: #fff;
        box-sizing: border-box;
    }

    /* Scroll selected stations inside the field */
    #station_ids + .select2-container
    .select2-selection--multiple .select2-selection__rendered {
        display: flex !important;
        flex-wrap: wrap !important;
        align-content: flex-start;
        gap: 3px;
        max-height: 78px !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        padding: 0 4px 3px 0 !important;
        margin: 0 !important;
        box-sizing: border-box;
    }

    /* Selected station tags */
    #station_ids + .select2-container .select2-selection__choice {
        float: none !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        max-width: 100%;
        white-space: normal;
        overflow-wrap: anywhere;
        background: #e8f1ff !important;
        border: 1px solid #b8d2ff !important;
        color: #174ea6 !important;
        border-radius: 4px;
        padding: 4px 8px !important;
        margin: 2px 3px 2px 0 !important;
        font-size: 12px;
        line-height: 20px !important;
        box-sizing: border-box;
    }

    /* Station name */
    #station_ids + .select2-container
    .select2-selection__choice__display {
        order: 1 !important;
        padding: 0 !important;
        margin: 0 !important;
        line-height: 20px !important;
    }

    /* Individual remove button - proper spacing after cross */
    #station_ids + .select2-container
    .select2-selection__choice__remove {
        order: 2 !important;
        position: static !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
        float: none !important;
        color: #174ea6 !important;
        background: transparent !important;
        border: none !important;
        border-left: 1px solid #b8d2ff !important;
        border-radius: 0 !important;
        margin: 0 0 0 5px !important;
        padding: 0 0 0 7px !important;
        font-size: 17px !important;
        font-weight: 600 !important;
        line-height: 18px !important;
        cursor: pointer;
    }

    #station_ids + .select2-container
    .select2-selection__choice__remove:hover {
        color: #dc3545 !important;
    }

    /* Scrollable station dropdown */
    .select2-container--open .select2-results__options {
        max-height: 220px !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
    }

    /* Select All header */
    .station-select-all-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .station-select-all-header label {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 0;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
    }

    .station-select-all-header .station-count {
        color: #6c757d;
        font-size: 12px;
        white-space: nowrap;
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
            ]) }}
        </div>

        {{-- Department --}}
        <div class="form-group col-md-3 mb-3">
            {{ Form::label('department_id', __('Department') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::select('department_id', ['' => __('-- Select Department --')] + $departments->toArray(), null, [
                'class' => 'form-select select2-single',
                'required' => true,
                'id' => 'department_id',
            ]) }}
        </div>

        {{-- Designation --}}
        <div class="form-group col-md-3 mb-3">
            {{ Form::label('designation_id', __('Designation') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::select('designation_id', ['' => __('-- Select Designation --')] + ($designations ? $designations->toArray() : []), null, [
                'class' => 'form-select select2-single',
                'required' => true,
                'id' => 'designation_id',
            ]) }}
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

            {{ Form::select('station_ids[]', $stations, null, [
                'class' => 'form-select',
                'multiple' => 'multiple',
                'id' => 'station_ids',
                'required' => true,
            ]) }}

            <small class="text-muted">
                {{ __('Select individual stations or use Select All Stations.') }}
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
                'placeholder' => __('Enter posting at present working place'),
                'required' => true,
            ]) }}
        </div>

        {{-- Level --}}
        <div class="form-group col-md-3 mb-3">
            {{ Form::label('level', __('Level') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::select('level', [
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
            ], null, [
                'class' => 'form-select select2-single',
                'required' => true,
                'id' => 'level',
            ]) }}
        </div>

        {{-- Status --}}
        <div class="form-group col-md-3 mb-3">
            {{ Form::label('status', __('Status') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::select('status', [
                1 => __('Active'),
                0 => __('Inactive'),
            ], 1, [
                'class' => 'form-select select2-single',
                'required' => true,
                'id' => 'status',
            ]) }}
        </div>

    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light btn-rounded" data-bs-dismiss="modal">
        {{ __('Cancel') }}
    </button>

    {{ Form::submit(__('Create'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>

{{ Form::close() }}

<script>
    $(function () {
        const $modalParent = $('#department_id').closest('.modal');
        const $stationSelect = $('#station_ids');

        // Initialize station multiselect
        if (!$stationSelect.hasClass('select2-hidden-accessible')) {
            $stationSelect.select2({
                dropdownParent: $modalParent.length
                    ? $modalParent
                    : $(document.body),
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
            const $checkbox = $dropdown.find('.station-select-all-checkbox');

            if (!$checkbox.length) return;

            const allValues = getAllStationValues();
            const selectedValues = ($stationSelect.val() || []).map(String);

            const selectedCount = allValues.filter(function (value) {
                return selectedValues.includes(String(value));
            }).length;

            $checkbox.prop(
                'checked',
                allValues.length > 0 && selectedCount === allValues.length
            );

            $checkbox.prop(
                'indeterminate',
                selectedCount > 0 && selectedCount < allValues.length
            );

            $dropdown.find('.station-count').text(
                selectedCount + ' of ' + allValues.length + ' selected'
            );
        }

        // Add Select All checkbox to station dropdown
        $stationSelect
            .off('.stationSelectAll')
            .on('select2:open.stationSelectAll', function () {
                const $dropdown = $('.select2-container--open .select2-dropdown');

                if (!$dropdown.find('.station-select-all-header').length) {
                    const $header = $(`
                        <div class="station-select-all-header">
                            <label>
                                <input type="checkbox"
                                       class="station-select-all-checkbox">
                                <span>Select All Stations</span>
                            </label>
                            <span class="station-count"></span>
                        </div>
                    `);

                    $dropdown.prepend($header);

                    $header.find('.station-select-all-checkbox').on(
                        'change',
                        function () {
                            const values = getAllStationValues();

                            $stationSelect
                                .val(this.checked ? values : [])
                                .trigger('change');

                            updateStationHeader();
                        }
                    );
                }

                updateStationHeader();
            })
            .on('change.stationSelectAll', updateStationHeader);

        // Initialize other single-select fields
        $('.select2-single').each(function () {
            const $select = $(this);

            if (!$select.hasClass('select2-hidden-accessible')) {
                $select.select2({
                    dropdownParent: $modalParent.length
                        ? $modalParent
                        : $(document.body),
                    width: '100%'
                });
            }
        });

        // Load designations after department selection
        $(document)
            .off('change.employeeCreateDesignation', '#department_id')
            .on('change.employeeCreateDesignation', '#department_id', function () {
                const deptId = $(this).val();
                const $designation = $('#designation_id');

                $designation.empty().append(
                    $('<option>', {
                        value: '',
                        text: '-- Select Designation --'
                    })
                ).val('').trigger('change');

                if (!deptId) return;

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
    });
</script>
