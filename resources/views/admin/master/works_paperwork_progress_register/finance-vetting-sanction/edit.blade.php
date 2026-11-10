{{ Form::open([
    'route' => ['works-paperwork-progress-register.finance-vetting-sanction.update', encrypt($record->id)],
    'method' => 'POST',
    'files' => true,
    'id' => 'fvs_form',
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
                    '' => __('Select Milestone'),
                    'Divisional finance vetting' => 'Divisional finance vetting',
                    'HQ finance vetting' => 'HQ finance vetting',
                    'Work sanctioned' => 'Work sanctioned',
                    'Detailed estimate finalized' => 'Detailed estimate finalized',
                ],
                old('milestone', $record->milestone),
                [
                    'class' => 'form-control',
                    'id' => 'milestone',
                    'required' => true,
                ],
            ) }}

            <small class="text-muted" id="milestone_hint" style="display:none;">
                {{ __('Please select Work Description first.') }}
            </small>

            <div class="text-danger mt-1" id="milestone_error" style="display:none;"></div>

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

        {{-- 4. Completed Section (Date & Supporting Document) --}}
        <div class="col-12" id="completed_section" style="display: none;">
            <div class="row">
                <div class="form-group col-md-6 mb-3">
                    {{ Form::label('date', __('Date'), ['class' => 'form-label']) }}
                    <span class="text-danger">*</span>

                    {{ Form::date('date', old('date', $record->date ? $record->date->format('Y-m-d') : ''), [
                        'class' => 'form-control',
                        'id' => 'fvs_date',
                    ]) }}

                    @error('date')
                        <span class="text-danger d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6 mb-3">
                    {{ Form::label('supporting_document', __('Supporting Document (PDF)'), ['class' => 'form-label']) }}

                    {{ Form::file('supporting_document', [
                        'class' => 'form-control',
                        'id' => 'supporting_document',
                        'accept' => 'application/pdf,.pdf',
                    ]) }}

                    @if ($record->supporting_document)
                        <div class="mt-2">
                            <a href="{{ asset('public/storage/' . $record->supporting_document) }}" target="_blank"
                                class="btn btn-sm btn-danger">
                                <i class="ti ti-file-type-pdf"></i>
                                {{ __('View Current PDF') }}
                            </a>
                        </div>
                    @else
                        <small class="text-muted">
                            {{ __('Only PDF files are allowed. Maximum size 10 MB.') }}
                        </small>
                    @endif

                    @error('supporting_document')
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
    <button type="submit" class="btn btn-primary" id="fvs_submit_btn">
        {{ __('Update') }}
    </button>

    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
        {{ __('Cancel') }}
    </button>
</div>

{{ Form::close() }}

<script>
    (function() {
        const workSelect = document.getElementById('identify_new_work_id');
        const milestoneSelect = document.getElementById('milestone');
        const milestoneHint = document.getElementById('milestone_hint');
        const milestoneError = document.getElementById('milestone_error');
        const submitBtn = document.getElementById('fvs_submit_btn');

        const isCompletedCheckbox = document.getElementById('is_completed');
        const completionHint = document.getElementById('completion_hint');
        const completedSection = document.getElementById('completed_section');
        const fvsDateInput = document.getElementById('fvs_date');
        const supportingDocInput = document.getElementById('supporting_document');
        const remarkSection = document.getElementById('remark_section');
        const remarkSelect = document.getElementById('remark');

        const milestoneOrder = [
            'Divisional finance vetting',
            'HQ finance vetting',
            'Work sanctioned',
            'Detailed estimate finalized',
        ];

        const milestoneStatusUrlBase =
            "{{ url('works-paperwork-progress-register/finance-vetting-sanction/milestone-status') }}";
        const ignoreId = "{{ encrypt($record->id) }}";

        let savedMilestones = [];
        let previousMilestoneValue = milestoneSelect.value;
        let milestoneStatusLoaded = false;

        function updateFormSections() {
            const hasMilestone = !!milestoneSelect.value;

            if (!hasMilestone) {
                isCompletedCheckbox.setAttribute('disabled', 'disabled');
                completionHint.textContent = "{{ __('Please select Work Description and Milestone first.') }}";
                completedSection.style.display = 'none';
                remarkSection.style.display = 'none';

                fvsDateInput.setAttribute('disabled', 'disabled');
                fvsDateInput.removeAttribute('required');

                supportingDocInput.setAttribute('disabled', 'disabled');

                remarkSelect.setAttribute('disabled', 'disabled');
                remarkSelect.removeAttribute('required');
                return;
            }

            isCompletedCheckbox.removeAttribute('disabled');
            completionHint.textContent = "{{ __('Toggle check if milestone is completed, or leave unchecked to specify a remark.') }}";

            if (isCompletedCheckbox.checked) {
                completedSection.style.display = '';
                remarkSection.style.display = 'none';

                fvsDateInput.removeAttribute('disabled');
                fvsDateInput.setAttribute('required', 'required');

                supportingDocInput.removeAttribute('disabled');

                remarkSelect.setAttribute('disabled', 'disabled');
                remarkSelect.removeAttribute('required');
            } else {
                completedSection.style.display = 'none';
                remarkSection.style.display = '';

                fvsDateInput.setAttribute('disabled', 'disabled');
                fvsDateInput.removeAttribute('required');

                supportingDocInput.setAttribute('disabled', 'disabled');

                remarkSelect.removeAttribute('disabled');
                remarkSelect.setAttribute('required', 'required');
            }
        }

        function showMilestoneError(message) {
            milestoneError.textContent = message;
            milestoneError.style.display = 'block';
            if (submitBtn) submitBtn.setAttribute('disabled', 'disabled');
        }

        function clearMilestoneError() {
            milestoneError.textContent = '';
            milestoneError.style.display = 'none';
            if (submitBtn) submitBtn.removeAttribute('disabled');
        }

        function disableMilestone() {
            milestoneSelect.value = '';
            milestoneSelect.setAttribute('disabled', 'disabled');
            milestoneHint.textContent = "{{ __('Please select Work Description first.') }}";
            milestoneHint.style.display = '';
            clearMilestoneError();
            savedMilestones = [];
            previousMilestoneValue = '';
            milestoneStatusLoaded = false;
            updateFormSections();
        }

        function enableMilestone() {
            milestoneSelect.removeAttribute('disabled');
            milestoneHint.style.display = 'none';
        }

        function nextExpectedMilestone() {
            for (let i = 0; i < milestoneOrder.length; i++) {
                if (!savedMilestones.includes(milestoneOrder[i])) {
                    return milestoneOrder[i];
                }
            }
            return null;
        }

        function fetchMilestoneStatus(workId, keepValue) {
            if (!workId) {
                disableMilestone();
                return $.Deferred().resolve().promise();
            }

            milestoneStatusLoaded = false;
            milestoneSelect.setAttribute('disabled', 'disabled');
            milestoneHint.textContent = "{{ __('Loading milestone status...') }}";
            milestoneHint.style.display = '';
            clearMilestoneError();

            let url = milestoneStatusUrlBase + '/' + workId;
            if (ignoreId) {
                url += '?ignore_id=' + encodeURIComponent(ignoreId);
            }

            return $.ajax({
                url: url,
                method: 'GET',
                dataType: 'json',
            }).done(function(response) {
                savedMilestones = (response && response.saved) ? response.saved : [];
                milestoneStatusLoaded = true;
                enableMilestone();

                if (!keepValue) {
                    milestoneSelect.value = '';
                    previousMilestoneValue = '';
                }
                updateFormSections();
            }).fail(function(xhr) {
                console.error('Milestone status fetch failed:', xhr.status, xhr.responseText);
                milestoneStatusLoaded = false;
                savedMilestones = [];
                milestoneSelect.setAttribute('disabled', 'disabled');
                milestoneHint.style.display = 'none';
                showMilestoneError(
                    "{{ __('Could not load milestone status. Please reselect the Work Description.') }}"
                );
                updateFormSections();
            });
        }

        function validateMilestoneSelection() {
            const selected = milestoneSelect.value;
            clearMilestoneError();

            if (!milestoneStatusLoaded) {
                showMilestoneError("{{ __('Milestone status not loaded yet. Please reselect the Work Description.') }}");
                updateFormSections();
                return false;
            }

            if (!selected) {
                previousMilestoneValue = '';
                updateFormSections();
                return true;
            }

            if (savedMilestones.includes(selected)) {
                showMilestoneError("{{ __('This milestone has already been registered for the selected work.') }}");
                milestoneSelect.value = previousMilestoneValue;
                updateFormSections();
                return false;
            }

            const expected = nextExpectedMilestone();
            if (!expected) {
                showMilestoneError("{{ __('All Finance Vetting & Sanction milestones have already been registered for this work.') }}");
                milestoneSelect.value = previousMilestoneValue;
                updateFormSections();
                return false;
            }

            if (selected !== expected) {
                showMilestoneError('{{ __('Please first submit the milestone') }} "' + expected + '" {{ __('before selecting this milestone.') }}');
                milestoneSelect.value = previousMilestoneValue;
                updateFormSections();
                return false;
            }

            previousMilestoneValue = selected;
            updateFormSections();
            return true;
        }

        if ($.fn.select2) {
            $(workSelect).select2({
                dropdownParent: $(workSelect).closest('.modal'),
                width: '100%',
            });
        }

        $(document).on('change select2:select', '#identify_new_work_id', function() {
            fetchMilestoneStatus(this.value, false);
        });

        $(document).on('select2:clear', '#identify_new_work_id', function() {
            disableMilestone();
        });

        $(document).on('change', '#milestone', validateMilestoneSelection);

        $(document).on('change', '#is_completed', updateFormSections);

        $(document).on('submit', '#fvs_form', function(e) {
            if (!milestoneStatusLoaded) {
                e.preventDefault();
                if (submitBtn) submitBtn.setAttribute('disabled', 'disabled');
                showMilestoneError("{{ __('Milestone status not loaded. Please reselect the Work Description before submitting.') }}");
                return false;
            }

            if (!milestoneSelect.value) {
                e.preventDefault();
                showMilestoneError("{{ __('Please select a milestone.') }}");
                return false;
            }

            if (!validateMilestoneSelection()) {
                e.preventDefault();
                return false;
            }

            if (isCompletedCheckbox.checked) {
                if (!fvsDateInput.value) {
                    e.preventDefault();
                    fvsDateInput.focus();
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
            fetchMilestoneStatus(workSelect.value, true);
        } else {
            disableMilestone();
        }

        updateFormSections();
    })();
</script>
