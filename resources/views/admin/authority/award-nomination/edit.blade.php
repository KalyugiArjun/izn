{{ Form::open([
    'route' => ['authority.award-nomination.update', encrypt($awardNomination->id)],
    'method' => 'POST'
]) }}

<div class="modal-body" style="min-height: 250px;">
    <div class="row">
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('award_nomination_edit_department_id', __('Department'), ['class' => 'form-label fw-bold']) }}

            {{ Form::select(
                'department_id',
                $departments,
                $awardNomination->department_id,
                [
                    'class' => 'form-select select2',
                    'required' => true,
                    'id' => 'award_nomination_edit_department_id',
                    'data-placeholder' => __('Select Department'),
                ]
            ) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('award_nomination_edit_designation_id', __('Designation'), ['class' => 'form-label fw-bold']) }}

            {{ Form::select(
                'designation_id',
                $designations,
                $awardNomination->designation_id,
                [
                    'class' => 'form-select select2',
                    'required' => true,
                    'id' => 'award_nomination_edit_designation_id',
                    'data-placeholder' => __('Select Designation'),
                ]
            ) }}
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light btn-rounded" data-bs-dismiss="modal">
        {{ __('Cancel') }}
    </button>

    {{ Form::submit(__('Update Award Nomination'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>

{{ Form::close() }}

<script>
(function () {
    const $modal = $('#customModal');

    function initEditModalSelects() {
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('#award_nomination_edit_department_id').select2({
                dropdownParent: $modal,
                placeholder: "{{ __('Select Department') }}",
                width: '100%'
            });

            $('#award_nomination_edit_designation_id').select2({
                dropdownParent: $modal,
                placeholder: "{{ __('Select Designation') }}",
                width: '100%'
            });
        }
    }

    setTimeout(initEditModalSelects, 50);

    $(document)
        .off('change.awardNominationEditDesignation', '#award_nomination_edit_department_id')
        .on('change.awardNominationEditDesignation', '#award_nomination_edit_department_id', function () {
            const departmentId = $(this).val();
            const $designation = $('#award_nomination_edit_designation_id');
            const currentDesignationId = $designation.val();

            $designation.empty();

            if (!departmentId) {
                $designation.trigger('change');
                return;
            }

            $.ajax({
                url: @json(route('authority.award-nomination.designations')),
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
