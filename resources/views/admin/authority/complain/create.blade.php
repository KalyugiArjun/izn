{{ Form::open(['route' => 'authority.complain.store', 'method' => 'post']) }}
<div class="modal-body">
    <div class="row">


        {{-- Employee --}}
        {{-- Employee --}}
        {{-- <div class="form-group col-md-6 mb-3">
            {{ Form::label('employee_id', __('Employee') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}

            {{ Form::select('employee_id', $employees, null, [
                'class' => 'form-select',
                'required' => true,
                'id' => 'employee_id',
                'placeholder' => __('Select Employee'),
            ]) }}
        </div> --}}

        {{-- Department --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label(
                'department_id',
                __('Department') . '<span class="text-danger"> *</span>',
                ['class' => 'form-label'],
                false,
            ) }}

            {{ Form::select('department_id[]', $departments, null, [
                'class' => 'form-select',
                'multiple' => true,
                'required' => true,
                'id' => 'department_id',
            ]) }}
        </div>

        {{-- Designation --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('designation_id', __('Designation') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}

            {{ Form::select('designation_id[]', [], null, [
                'class' => 'form-select',
                'multiple' => true,
                'required' => true,
                'id' => 'designation_id',
            ]) }}
        </div>



    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-light btn-rounded" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Submit Complaint'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>
{{ Form::close() }}



<script>
(function () {
    // Event delegation: modal dynamically load hone par bhi kaam karega
    $(document)
        .off('change.complaintDesignation', '#department_id')
        .on('change.complaintDesignation', '#department_id', function () {
            const deptIds = $(this).val() || [];
            const desigSelect = $('#designation_id');

            desigSelect.empty();

            if (!deptIds.length) {
                desigSelect.trigger('change');
                return;
            }

            $.ajax({
                url: "{{ route('authority.complain.designations') }}",
                type: 'GET',
                data: {
                    department_id: deptIds.join(',')
                },
                dataType: 'json',
                success: function (data) {
                    $.each(data, function (id, name) {
                        desigSelect.append(
                            new Option(name, id, false, false)
                        );
                    });

                    desigSelect.trigger('change');
                },
                error: function (xhr) {
                    console.error(
                        'Designation API Error:',
                        xhr.status,
                        xhr.responseText
                    );
                    alert('Designation load nahi hui. F12 Console check karein.');
                }
            });
        });
})();
</script>