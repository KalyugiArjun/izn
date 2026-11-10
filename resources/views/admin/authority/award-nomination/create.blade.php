{{ Form::open(['route' => 'authority.award-nomination.store', 'method' => 'POST']) }}

<div class="modal-body" style="min-height: 250px;">
    <div class="row">
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('award_nomination_department_id', __('Department'), ['class' => 'form-label fw-bold']) }}

            {{ Form::select('department_id[]', $departments, null, [
                'class' => 'form-select select2',
                'multiple' => true,
                'required' => true,
                'id' => 'award_nomination_department_id',
                'data-placeholder' => __('Select Department(s)'),
            ]) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('award_nomination_designation_id', __('Designation'), ['class' => 'form-label fw-bold']) }}

            {{ Form::select('designation_id[]', [], null, [
                'class' => 'form-select select2',
                'multiple' => true,
                'required' => true,
                'id' => 'award_nomination_designation_id',
                'data-placeholder' => __('Select Designation(s)'),
            ]) }}
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light btn-rounded" data-bs-dismiss="modal">
        {{ __('Cancel') }}
    </button>

    {{ Form::submit(__('Submit Award Nomination Authority'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>

{{ Form::close() }}

<script>
(function () {
    const $modal = $('#customModal');

    // Initialize Select2 explicitly with customModal parent
    function initModalSelects() {
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('#award_nomination_department_id').select2({
                dropdownParent: $modal,
                placeholder: "{{ __('Select Department(s)') }}",
                width: '100%'
            });

            $('#award_nomination_designation_id').select2({
                dropdownParent: $modal,
                placeholder: "{{ __('Select Designation(s)') }}",
                width: '100%'
            });
        }
    }

    setTimeout(initModalSelects, 50);

    $(document)
        .off('change.awardNominationDesignation', '#award_nomination_department_id')
        .on('change.awardNominationDesignation', '#award_nomination_department_id', function () {
            const departmentIds = $(this).val() || [];
            const $designation = $('#award_nomination_designation_id');

            $designation.empty();

            if (departmentIds.length === 0) {
                $designation.trigger('change');
                return;
            }

            $.ajax({
                url: @json(route('authority.award-nomination.designations')),
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
