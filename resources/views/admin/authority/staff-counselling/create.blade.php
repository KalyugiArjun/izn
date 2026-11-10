<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #e2e8f0 !important;
        color: #0f172a !important;
        border: 1px solid #94a3b8 !important;
        border-radius: 5px !important;
        font-weight: 600 !important;
        font-size: 13px !important;
        padding: 4px 8px !important;
        display: inline-flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
        color: #0f172a !important;
        font-weight: 600 !important;
        font-size: 13px !important;
        padding-left: 2px !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #475569 !important;
        background: transparent !important;
        border: none !important;
        font-weight: bold !important;
        margin-right: 5px !important;
        order: -1 !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #ef4444 !important;
        background: transparent !important;
    }
    .select2-container--default .select2-selection--multiple .select2-search__field {
        color: #0f172a !important;
    }
</style>

{{ Form::open(['route' => 'authority.staff-counselling.store', 'method' => 'POST']) }}

<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('staff_counselling_department_id', __('Department'), ['class' => 'form-label']) }}

            {{ Form::select('department_id[]', $departments, null, [
                'class' => 'form-select',
                'multiple' => true,
                'required' => true,
                'id' => 'staff_counselling_department_id',
            ]) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('staff_counselling_designation_id', __('Designation'), ['class' => 'form-label']) }}

            {{ Form::select('designation_id[]', [], null, [
                'class' => 'form-select',
                'multiple' => true,
                'required' => true,
                'id' => 'staff_counselling_designation_id',
            ]) }}
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light btn-rounded" data-bs-dismiss="modal">
        {{ __('Cancel') }}
    </button>

    {{ Form::submit(__('Submit Staff Counselling'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>

{{ Form::close() }}

<script>
(function () {
    $(document)
        .off('change.staffCounsellingDesignation', '#staff_counselling_department_id')
        .on('change.staffCounsellingDesignation', '#staff_counselling_department_id', function () {
            const departmentIds = $(this).val() || [];
            const $designation = $('#staff_counselling_designation_id');

            $designation.empty();

            if (departmentIds.length === 0) {
                $designation.trigger('change');
                return;
            }

            $.ajax({
                url: @json(route('authority.staff-counselling.designations')),
                method: 'GET',
                dataType: 'json',
                data: {
                    department_id: departmentIds.join(',')
                },
                success: function (data) {
                    $.each(data, function (id, name) {
                        $designation.append(new Option(name, id, false, false));
                    });

                    $designation.trigger('change');
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    alert('Designation load nahi hua. Please check Console.');
                }
            });
        });
})();
</script>

