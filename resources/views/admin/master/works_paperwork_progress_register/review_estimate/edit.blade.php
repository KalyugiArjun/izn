{{ Form::open([
    'route' => ['works-paperwork-progress-register.review-estimate.update', encrypt($record->id)],
    'method' => 'POST',
    'id' => 'review_estimate_form',
]) }}

<div class="modal-body">
    <div class="row">

        {{-- 1. Work Description --}}
        <div class="form-group col-12 mb-3">
            {{ Form::label('identify_new_work_id', __('Work Description'), ['class' => 'form-label']) }}
            <span class="text-danger">*</span>
            <select name="identify_new_work_id" id="identify_new_work_id" class="form-control select2" required>
                <option value="">{{ __('Select Work') }}</option>
                @foreach ($works as $work)
                    <option value="{{ $work->id }}"
                        data-previous-date="{{ \Illuminate\Support\Carbon::parse($work->latest_estimate_date)->format('Y-m-d') }}"
                        data-previous-department="{{ $work->latest_estimate_department }}"
                        {{ old('identify_new_work_id', $record->identify_new_work_id) == $work->id ? 'selected' : '' }}>
                        {{ $work->work_description_identification }}
                    </option>
                @endforeach
            </select>
            @error('identify_new_work_id')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror
        </div>

        {{-- 2. Milestone --}}
        <div class="form-group col-12 mb-3">
            {{ Form::label('milestone', __('Milestone'), ['class' => 'form-label']) }}
            <span class="text-danger">*</span>
            {{ Form::select(
                'milestone',
                [
                    'Estimates finalized (after consolidating all departments)' =>
                        'Estimates finalized (after consolidating all departments)',
                ],
                old('milestone', $record->milestone),
                [
                    'class' => 'form-control',
                    'id' => 'milestone',
                    'required' => true,
                ],
            ) }}
            @error('milestone')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror
        </div>

        {{-- 3. Checkbox: Is this milestone completed? --}}
        <div class="col-12 mb-3">
            <div class="p-3 rounded-3 border bg-light">
                <div class="form-check form-switch mb-0 d-flex align-items-center">
                    <input class="form-check-input me-3" type="checkbox" name="is_completed" id="is_completed" value="1"
                        style="width: 2.75rem; height: 1.4rem; cursor: pointer;"
                        {{ old('is_completed', $record->is_completed) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-dark fs-6" for="is_completed" style="cursor: pointer;">
                        {{ __('Is this milestone completed?') }}
                    </label>
                </div>
                <small class="text-muted d-block mt-1 ps-1" id="completion_hint">
                    {{ __('Toggle check if milestone is completed, or leave unchecked to specify a remark.') }}
                </small>
            </div>
        </div>

        {{-- 4. Completed Section (Review Estimate Date) --}}
        <div class="col-12" id="completed_section" style="display: none;">
            <div class="row">
                <div class="form-group col-md-12 mb-3">
                    {{ Form::label('review_estimate_date', __('Review Estimate Date'), ['class' => 'form-label']) }}
                    <span class="text-danger">*</span>
                    {{ Form::date(
                        'review_estimate_date',
                        old('review_estimate_date', $record->review_estimate_date ? $record->review_estimate_date->format('Y-m-d') : ''),
                        [
                            'class' => 'form-control',
                            'id' => 'review_estimate_date',
                        ],
                    ) }}

                    <small id="previous_stage_info" class="text-muted d-block mt-1">
                        {{ __('Select a work to see previous stage details.') }}
                    </small>

                    @error('review_estimate_date')
                        <span class="text-danger d-block">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        {{-- 5. Incomplete Section (Remark Selection) --}}
        <div class="col-12" id="remark_section" style="display: none;">
            <div class="form-group mb-3">
                {{ Form::label('remark', __('Reason / Remark'), ['class' => 'form-label']) }}
                <span class="text-danger">*</span>

                <select name="remark" id="remark" class="form-control form-select">
                    <option value="">{{ __('Select Remark') }}</option>
                    @foreach ($remarks as $remarkOption)
                        <option value="{{ $remarkOption }}"
                            {{ old('remark', $record->remark) == $remarkOption ? 'selected' : '' }}>
                            {{ $remarkOption }}
                        </option>
                    @endforeach
                </select>

                <small class="text-muted">
                    {{ __('Select a remark since the milestone is not completed.') }}
                </small>

                @error('remark')
                    <span class="text-danger d-block">{{ $message }}</span>
                @enderror
            </div>
        </div>

    </div>
</div>

<div class="modal-footer">
    <button type="submit" class="btn btn-primary" id="review_estimate_submit_btn">
        {{ __('Update') }}
    </button>

    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
        {{ __('Cancel') }}
    </button>
</div>

{{ Form::close() }}

<script>
    (function() {
        const form = document.getElementById('review_estimate_form');
        if (!form) return;

        const workSelect = form.querySelector('#identify_new_work_id');
        const dateInput = form.querySelector('#review_estimate_date');
        const previousStageInfo = form.querySelector('#previous_stage_info');
        const submitBtn = form.querySelector('#review_estimate_submit_btn');

        const isCompletedCheckbox = form.querySelector('#is_completed');
        const completedSection = form.querySelector('#completed_section');
        const remarkSection = form.querySelector('#remark_section');
        const remarkSelect = form.querySelector('#remark');

        function updateFormSections() {
            const hasWork = !!workSelect.value;

            if (!hasWork) {
                isCompletedCheckbox.setAttribute('disabled', 'disabled');
                completedSection.style.display = 'none';
                remarkSection.style.display = 'none';

                dateInput.setAttribute('disabled', 'disabled');
                dateInput.removeAttribute('required');

                remarkSelect.setAttribute('disabled', 'disabled');
                remarkSelect.removeAttribute('required');
                return;
            }

            isCompletedCheckbox.removeAttribute('disabled');

            if (isCompletedCheckbox.checked) {
                completedSection.style.display = '';
                remarkSection.style.display = 'none';

                dateInput.removeAttribute('disabled');
                dateInput.setAttribute('required', 'required');

                remarkSelect.setAttribute('disabled', 'disabled');
                remarkSelect.removeAttribute('required');
            } else {
                completedSection.style.display = 'none';
                remarkSection.style.display = '';

                dateInput.setAttribute('disabled', 'disabled');
                dateInput.removeAttribute('required');

                remarkSelect.removeAttribute('disabled');
                remarkSelect.setAttribute('required', 'required');
            }
        }

        function updatePreviousStageInfo() {
            const selectedOption = workSelect.options[workSelect.selectedIndex];
            if (selectedOption && selectedOption.dataset.previousDate) {
                previousStageInfo.textContent = 'Latest Estimate Received Date: ' +
                    selectedOption.dataset.previousDate +
                    ' (' + (selectedOption.dataset.previousDepartment || 'Engineering') + ')';
            } else {
                previousStageInfo.textContent = "{{ __('Select a work to see previous stage details.') }}";
            }
        }

        if ($.fn.select2) {
            $(workSelect).select2({
                dropdownParent: $(form).closest('.modal'),
                width: '100%',
            });
        }

        $(document).on('change select2:select', '#identify_new_work_id', function() {
            updatePreviousStageInfo();
            updateFormSections();
        });

        $(document).on('change', '#is_completed', updateFormSections);

        $(document).on('submit', '#review_estimate_form', function(e) {
            if (!workSelect.value) {
                e.preventDefault();
                return false;
            }

            if (isCompletedCheckbox.checked) {
                if (!dateInput.value) {
                    e.preventDefault();
                    dateInput.focus();
                    return false;
                }
            } else {
                if (!remarkSelect.value) {
                    e.preventDefault();
                    remarkSelect.focus();
                    return false;
                }
            }

            if (submitBtn) submitBtn.setAttribute('disabled', 'disabled');
        });

        if (workSelect.value) {
            updatePreviousStageInfo();
        }
        updateFormSections();
    })();
</script>
