<style>
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #0f172a !important;
        font-weight: 600 !important;
    }
</style>

{{ Form::model($record, [
    'route'  => ['load-stable.clearance-forward-pointsman.update', encrypt($record->id)],
    'method' => 'POST',
]) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6 mb-3">
            {{ Form::label(
                'edit_load_clearance_forward_pointsman_department_id',
                __('Department') . '<span class="text-danger"> *</span>',
                ['class' => 'form-label'],
                false,
            ) }}
            {{ Form::select('department_id', ['' => __('-- Select Department --')] + $departments->toArray(), null, [
                'class' => 'form-select',
                'required' => true,
                'id' => 'edit_load_clearance_forward_pointsman_department_id',
            ]) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label(
                'edit_load_clearance_forward_pointsman_designation_id',
                __('Designation') . '<span class="text-danger"> *</span>',
                ['class' => 'form-label'],
                false,
            ) }}
            {{ Form::select('designation_id', ['' => __('-- Select Designation --')] + $designations->toArray(), null, [
                'class' => 'form-select',
                'required' => true,
                'id' => 'edit_load_clearance_forward_pointsman_designation_id',
            ]) }}
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light btn-rounded" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Update Load Clearance Forward By Pointsman To'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>
{{ Form::close() }}

<script>
    (function () {
        const dept = document.getElementById('edit_load_clearance_forward_pointsman_department_id');
        if (dept) {
            $(dept).off('change.loadClearanceForwardPointsmanEdit').on('change.loadClearanceForwardPointsmanEdit', function () {
                const deptId = this.value;
                const desigSelect = document.getElementById('edit_load_clearance_forward_pointsman_designation_id');

                desigSelect.innerHTML = '<option value="">Loading...</option>';
                if (!deptId) {
                    desigSelect.innerHTML = '<option value="">-- Select Designation --</option>';
                    if (typeof $ !== 'undefined' && $(desigSelect).data('select2')) {
                        $(desigSelect).trigger('change');
                    }
                    return;
                }

                $.ajax({
                    url: @json(route('load-stable.clearance-forward-pointsman.designations')),
                    method: 'GET',
                    dataType: 'json',
                    data: { department_id: deptId },
                    success: function (data) {
                        desigSelect.innerHTML = '<option value="">-- Select Designation --</option>';
                        $.each(data, function (id, name) {
                            desigSelect.innerHTML += `<option value="${id}">${name}</option>`;
                        });
                        if (typeof $ !== 'undefined' && $(desigSelect).data('select2')) {
                            $(desigSelect).trigger('change');
                        }
                    },
                    error: function () {
                        desigSelect.innerHTML = '<option value="">-- Select Designation --</option>';
                        if (typeof $ !== 'undefined' && $(desigSelect).data('select2')) {
                            $(desigSelect).trigger('change');
                        }
                    }
                });
            });
        }
    })();
</script>

