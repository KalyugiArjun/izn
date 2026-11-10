{{ Form::open(['route' => 'authority.my-movement.store', 'method' => 'POST']) }}

<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('my_movement_department_id', __('Department'), ['class' => 'form-label']) }}

            {{ Form::select('department_id[]', $departments, null, [
                'class' => 'form-select',
                'multiple' => true,
                'required' => true,
                'id' => 'my_movement_department_id',
            ]) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('my_movement_designation_id', __('Designation'), ['class' => 'form-label']) }}

            {{ Form::select('designation_id[]', [], null, [
                'class' => 'form-select',
                'multiple' => true,
                'required' => true,
                'id' => 'my_movement_designation_id',
            ]) }}
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light btn-rounded" data-bs-dismiss="modal">
        {{ __('Cancel') }}
    </button>

    {{ Form::submit(__('Submit My Movement'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>

{{ Form::close() }}

<script>
(function () {
    $(document)
        .off('change.myMovementDesignation', '#my_movement_department_id')
        .on('change.myMovementDesignation', '#my_movement_department_id', function () {
            const departmentIds = $(this).val() || [];
            const $designation = $('#my_movement_designation_id');

            $designation.empty();

            if (departmentIds.length === 0) {
                $designation.trigger('change');
                return;
            }

            $.ajax({
                url: @json(route('authority.my-movement.designations')),
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
