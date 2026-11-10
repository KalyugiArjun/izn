{{ Form::open(['route' => 'authority.rectify.store', 'method' => 'post']) }}

<div class="modal-body">
    <div class="row">

        {{-- Department --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label(
                'rectify_department_id',
                __('Department') . '<span class="text-danger"> *</span>',
                ['class' => 'form-label'],
                false
            ) }}

            {{ Form::select('department_id[]', $departments, null, [
                'class' => 'form-select',
                'multiple' => true,
                'required' => true,
                'id' => 'rectify_department_id',
            ]) }}
        </div>

        {{-- Designation --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label(
                'rectify_designation_id',
                __('Designation') . '<span class="text-danger"> *</span>',
                ['class' => 'form-label'],
                false
            ) }}

            {{ Form::select('designation_id[]', [], null, [
                'class' => 'form-select',
                'multiple' => true,
                'required' => true,
                'id' => 'rectify_designation_id',
            ]) }}
        </div>

    </div>
</div>

<div class="modal-footer">
    <button
        type="button"
        class="btn btn-light btn-rounded"
        data-bs-dismiss="modal"
    >
        {{ __('Cancel') }}
    </button>

    {{ Form::submit(__('Submit Rectify'), [
        'class' => 'btn btn-secondary btn-rounded'
    ]) }}
</div>

{{ Form::close() }}

<script>
(function () {
    // Delegated event: works when the form is loaded inside a modal.
    $(document)
        .off('change.rectifyDesignation', '#rectify_department_id')
        .on('change.rectifyDesignation', '#rectify_department_id', function () {

            const departmentIds = $(this).val() || [];
            const $designation = $('#rectify_designation_id');

            // Clear previous options.
            $designation.empty();

            if (departmentIds.length === 0) {
                $designation.trigger('change');
                return;
            }

            $.ajax({
                url: @json(route('authority.rectify.designations')),
                method: 'GET',
                dataType: 'json',
                data: {
                    department_id: departmentIds.join(',')
                },

                success: function (data) {
                    if (data && typeof data === 'object') {
                        $.each(data, function (id, name) {
                            $designation.append(
                                new Option(name, id, false, false)
                            );
                        });
                    }

                    // Refresh Select2 if initialized.
                    $designation.trigger('change');
                },

                error: function (xhr) {
                    console.error(
                        'Rectify designation AJAX error:',
                        xhr.status,
                        xhr.responseText
                    );

                    alert('Designation load nahi hua. Browser Console check karein.');
                }
            });
        });
})();
</script>