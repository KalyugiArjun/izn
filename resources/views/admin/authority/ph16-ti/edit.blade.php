
{{ Form::open([
    'route' => ['authority.ph16-ti.update', encrypt($ph16Ti->id)],
    'method' => 'POST'
]) }}

<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('ph16_ti_edit_department_id', __('Department'), ['class' => 'form-label']) }}

            {{ Form::select(
                'department_id',
                $departments,
                $ph16Ti->department_id,
                [
                    'class' => 'form-select',
                    'required' => true,
                    'id' => 'ph16_ti_edit_department_id',
                ]
            ) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('ph16_ti_edit_designation_id', __('Designation'), ['class' => 'form-label']) }}

            {{ Form::select(
                'designation_id',
                $designations,
                $ph16Ti->designation_id,
                [
                    'class' => 'form-select',
                    'required' => true,
                    'id' => 'ph16_ti_edit_designation_id',
                ]
            ) }}
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light btn-rounded" data-bs-dismiss="modal">
        {{ __('Cancel') }}
    </button>

    {{ Form::submit(__('Update PH16 TI'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>

{{ Form::close() }}

<script>
(function () {
    $(document)
        .off('change.ph16TiEditDesignation', '#ph16_ti_edit_department_id')
        .on('change.ph16TiEditDesignation', '#ph16_ti_edit_department_id', function () {
            const departmentId = $(this).val();
            const $designation = $('#ph16_ti_edit_designation_id');
            const currentDesignationId = $designation.val();

            $designation.empty();

            if (!departmentId) {
                $designation.trigger('change');
                return;
            }

            $.ajax({
                url: @json(route('authority.ph16-ti.designations')),
                method: 'GET',
                dataType: 'json',
                data: {
                    department_id: departmentId
                },
                success: function (data) {
                    $.each(data, function (id, name) {
                        $designation.append(new Option(name, id, false, false));
                    });

                    $designation.val(currentDesignationId);
                    $designation.trigger('change');
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    alert('Designation load nahi hui. Please check Console.');
                }
            });
        });
})();
</script>
