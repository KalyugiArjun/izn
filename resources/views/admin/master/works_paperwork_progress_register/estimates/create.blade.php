{{ Form::open([
    'route' => 'works-paperwork-progress-register.estimates.store',
    'method' => 'POST',
    'files' => true,
    'id' => 'estimate_form',
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
                    <option value="{{ $work->id }}" {{ old('identify_new_work_id') == $work->id ? 'selected' : '' }}>
                        {{ $work->work_description_identification }}
                    </option>
                @endforeach
            </select>

            @error('identify_new_work_id')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror
        </div>

        {{-- 2. Department --}}
        <div class="form-group col-12 mb-3">
            {{ Form::label('department', __('Department'), ['class' => 'form-label']) }}
            <span class="text-danger">*</span>

            {{ Form::select(
                'department',
                [
                    '' => __('Select Department'),
                    'Engineering' => 'Engineering',
                    'TRD' => 'TRD',
                    'Signal' => 'Signal',
                    'Electrical/G' => 'Electrical/G',
                ],
                old('department'),
                [
                    'class' => 'form-control',
                    'id' => 'department',
                    'required' => true,
                    'disabled' => true,
                ],
            ) }}

            <small class="text-muted" id="department_hint">
                {{ __('Please select Work Description first.') }}
            </small>

            <div class="text-danger mt-1" id="department_error" style="display:none;"></div>

            @error('department')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror
        </div>

        {{-- 3. Checkbox: Is this milestone completed? --}}
        <div class="col-12 mb-3">
            <div class="p-3 rounded-3 border bg-light">
                <div class="form-check form-switch mb-0 d-flex align-items-center">
                    <input class="form-check-input me-3" type="checkbox" name="is_completed" id="is_completed" value="1"
                        style="width: 2.75rem; height: 1.4rem; cursor: pointer;"
                        {{ old('is_completed') ? 'checked' : '' }} disabled>
                    <label class="form-check-label fw-bold text-dark fs-6" for="is_completed" style="cursor: pointer;">
                        {{ __('Is this milestone completed?') }}
                    </label>
                </div>
                <small class="text-muted d-block mt-1 ps-1" id="completion_hint">
                    {{ __('Please select Work Description and Department first.') }}
                </small>
            </div>
        </div>

        {{-- 4. Completed Section (Called Date, Call Letter, Received Date, Cost, Received PDF) --}}
        <div class="col-12" id="completed_section" style="display: none;">
            <div class="row">
                <div class="form-group col-md-6 mb-3">
                    {{ Form::label('estimate_called', __('Estimate Called Date'), ['class' => 'form-label']) }}
                    <span class="text-danger">*</span>

                    {{ Form::date('estimate_called', old('estimate_called'), [
                        'class' => 'form-control',
                        'id' => 'estimate_called',
                        'disabled' => true,
                    ]) }}

                    @error('estimate_called')
                        <span class="text-danger d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6 mb-3">
                    {{ Form::label('call_letter', __('Call Letter (PDF)'), ['class' => 'form-label']) }}

                    {{ Form::file('call_letter', [
                        'class' => 'form-control',
                        'id' => 'call_letter',
                        'accept' => 'application/pdf,.pdf',
                        'disabled' => true,
                    ]) }}

                    <small class="text-muted">
                        {{ __('Only PDF files are allowed. Maximum size 10 MB.') }}
                    </small>

                    @error('call_letter')
                        <span class="text-danger d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6 mb-3">
                    {{ Form::label('estimate_received', __('Estimate Received Date'), ['class' => 'form-label']) }}

                    {{ Form::date('estimate_received', old('estimate_received'), [
                        'class' => 'form-control',
                        'id' => 'estimate_received',
                        'disabled' => true,
                    ]) }}

                    @error('estimate_received')
                        <span class="text-danger d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-6 mb-3">
                    {{ Form::label('cost', __('Estimated Cost (₹)'), ['class' => 'form-label']) }}

                    {{ Form::number('cost', old('cost'), [
                        'class' => 'form-control',
                        'id' => 'cost',
                        'step' => '0.01',
                        'min' => '0',
                        'placeholder' => __('Enter Cost in ₹'),
                        'disabled' => true,
                    ]) }}

                    @error('cost')
                        <span class="text-danger d-block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-md-12 mb-3">
                    {{ Form::label('received_estimate', __('Received Estimate (PDF)'), ['class' => 'form-label']) }}

                    {{ Form::file('received_estimate', [
                        'class' => 'form-control',
                        'id' => 'received_estimate',
                        'accept' => 'application/pdf,.pdf',
                        'disabled' => true,
                    ]) }}

                    <small class="text-muted">
                        {{ __('Only PDF files are allowed. Maximum size 10 MB.') }}
                    </small>

                    @error('received_estimate')
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

                <select name="remark" id="remark" class="form-control form-select" disabled>
                    <option value="">{{ __('Select Remark') }}</option>
                    @foreach ($remarks as $remarkOption)
                        <option value="{{ $remarkOption }}" {{ old('remark') == $remarkOption ? 'selected' : '' }}>
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
    <button type="submit" class="btn btn-primary" id="estimate_submit_btn">
        {{ __('Save') }}
    </button>

    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
        {{ __('Cancel') }}
    </button>
</div>

{{ Form::close() }}

<script>
    (function() {
        const workSelect = document.getElementById('identify_new_work_id');
        const departmentSelect = document.getElementById('department');
        const departmentHint = document.getElementById('department_hint');
        const departmentError = document.getElementById('department_error');
        const submitBtn = document.getElementById('estimate_submit_btn');

        const isCompletedCheckbox = document.getElementById('is_completed');
        const completionHint = document.getElementById('completion_hint');
        const completedSection = document.getElementById('completed_section');
        const estimateCalledInput = document.getElementById('estimate_called');
        const callLetterInput = document.getElementById('call_letter');
        const estimateReceivedInput = document.getElementById('estimate_received');
        const costInput = document.getElementById('cost');
        const receivedEstimateInput = document.getElementById('received_estimate');
        const remarkSection = document.getElementById('remark_section');
        const remarkSelect = document.getElementById('remark');

        const departmentOrder = [
            'Engineering',
            'TRD',
            'Signal',
            'Electrical/G',
        ];

        const departmentStatusUrlBase =
            "{{ url('works-paperwork-progress-register/estimates/department-status') }}";
        const ignoreId = null;

        let savedDepartments = [];
        let previousDepartmentValue = departmentSelect.value;
        let departmentStatusLoaded = false;

        function updateFormSections() {
            const hasDepartment = !!departmentSelect.value;

            if (!hasDepartment) {
                isCompletedCheckbox.setAttribute('disabled', 'disabled');
                completionHint.textContent = "{{ __('Please select Work Description and Department first.') }}";
                completedSection.style.display = 'none';
                remarkSection.style.display = 'none';

                estimateCalledInput.setAttribute('disabled', 'disabled');
                estimateCalledInput.removeAttribute('required');

                callLetterInput.setAttribute('disabled', 'disabled');
                estimateReceivedInput.setAttribute('disabled', 'disabled');
                costInput.setAttribute('disabled', 'disabled');
                receivedEstimateInput.setAttribute('disabled', 'disabled');

                remarkSelect.setAttribute('disabled', 'disabled');
                remarkSelect.removeAttribute('required');
                return;
            }

            // Department selected -> enable completion checkbox
            isCompletedCheckbox.removeAttribute('disabled');
            completionHint.textContent = "{{ __('Toggle check if milestone is completed, or leave unchecked to specify a remark.') }}";

            if (isCompletedCheckbox.checked) {
                completedSection.style.display = '';
                remarkSection.style.display = 'none';

                estimateCalledInput.removeAttribute('disabled');
                estimateCalledInput.setAttribute('required', 'required');

                callLetterInput.removeAttribute('disabled');
                estimateReceivedInput.removeAttribute('disabled');
                costInput.removeAttribute('disabled');
                receivedEstimateInput.removeAttribute('disabled');

                remarkSelect.setAttribute('disabled', 'disabled');
                remarkSelect.removeAttribute('required');
                remarkSelect.value = '';
            } else {
                completedSection.style.display = 'none';
                remarkSection.style.display = '';

                estimateCalledInput.setAttribute('disabled', 'disabled');
                estimateCalledInput.removeAttribute('required');
                estimateCalledInput.value = '';

                callLetterInput.setAttribute('disabled', 'disabled');
                estimateReceivedInput.setAttribute('disabled', 'disabled');
                costInput.setAttribute('disabled', 'disabled');
                receivedEstimateInput.setAttribute('disabled', 'disabled');

                remarkSelect.removeAttribute('disabled');
                remarkSelect.setAttribute('required', 'required');
            }
        }

        function showDepartmentError(message) {
            departmentError.textContent = message;
            departmentError.style.display = 'block';
            if (submitBtn) submitBtn.setAttribute('disabled', 'disabled');
        }

        function clearDepartmentError() {
            departmentError.textContent = '';
            departmentError.style.display = 'none';
            if (submitBtn) submitBtn.removeAttribute('disabled');
        }

        function disableDepartment() {
            departmentSelect.value = '';
            departmentSelect.setAttribute('disabled', 'disabled');
            departmentHint.textContent = "{{ __('Please select Work Description first.') }}";
            departmentHint.style.display = '';
            clearDepartmentError();
            savedDepartments = [];
            previousDepartmentValue = '';
            departmentStatusLoaded = false;
            updateFormSections();
        }

        function enableDepartment() {
            departmentSelect.removeAttribute('disabled');
            departmentHint.style.display = 'none';
        }

        function nextExpectedDepartment() {
            for (let i = 0; i < departmentOrder.length; i++) {
                if (!savedDepartments.includes(departmentOrder[i])) {
                    return departmentOrder[i];
                }
            }
            return null;
        }

        function fetchDepartmentStatus(workId, keepValue) {
            if (!workId) {
                disableDepartment();
                return $.Deferred().resolve().promise();
            }

            departmentStatusLoaded = false;
            departmentSelect.setAttribute('disabled', 'disabled');
            departmentHint.textContent = "{{ __('Loading department status...') }}";
            departmentHint.style.display = '';
            clearDepartmentError();

            let url = departmentStatusUrlBase + '/' + workId;
            if (ignoreId) {
                url += '?ignore_id=' + encodeURIComponent(ignoreId);
            }

            return $.ajax({
                url: url,
                method: 'GET',
                dataType: 'json',
            }).done(function(response) {
                savedDepartments = (response && response.saved) ? response.saved : [];
                departmentStatusLoaded = true;
                enableDepartment();

                if (!keepValue) {
                    departmentSelect.value = '';
                    previousDepartmentValue = '';
                }
                updateFormSections();
            }).fail(function(xhr) {
                console.error('Department status fetch failed:', xhr.status, xhr.responseText);
                departmentStatusLoaded = false;
                savedDepartments = [];
                departmentSelect.setAttribute('disabled', 'disabled');
                departmentHint.style.display = 'none';
                showDepartmentError(
                    "{{ __('Could not load department status. Please reselect the Work Description.') }}"
                );
                updateFormSections();
            });
        }

        function validateDepartmentSelection() {
            const selected = departmentSelect.value;
            clearDepartmentError();

            if (!departmentStatusLoaded) {
                showDepartmentError("{{ __('Department status not loaded yet. Please reselect the Work Description.') }}");
                updateFormSections();
                return false;
            }

            if (!selected) {
                previousDepartmentValue = '';
                updateFormSections();
                return true;
            }

            if (savedDepartments.includes(selected)) {
                showDepartmentError("{{ __('An estimate for this department has already been registered for the selected work.') }}");
                departmentSelect.value = previousDepartmentValue;
                updateFormSections();
                return false;
            }

            const expected = nextExpectedDepartment();
            if (!expected) {
                showDepartmentError("{{ __('Estimates for all departments have already been registered for this work.') }}");
                departmentSelect.value = previousDepartmentValue;
                updateFormSections();
                return false;
            }

            if (selected !== expected) {
                showDepartmentError('{{ __('Please first submit the') }} "' + expected + '" {{ __('department estimate before selecting this one.') }}');
                departmentSelect.value = previousDepartmentValue;
                updateFormSections();
                return false;
            }

            previousDepartmentValue = selected;
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
            fetchDepartmentStatus(this.value, false);
        });

        $(document).on('select2:clear', '#identify_new_work_id', function() {
            disableDepartment();
        });

        $(document).on('change', '#department', validateDepartmentSelection);

        $(document).on('change', '#is_completed', updateFormSections);

        $(document).on('submit', '#estimate_form', function(e) {
            if (!departmentStatusLoaded) {
                e.preventDefault();
                if (submitBtn) submitBtn.setAttribute('disabled', 'disabled');
                showDepartmentError("{{ __('Department status not loaded. Please reselect the Work Description before submitting.') }}");
                return false;
            }

            if (!departmentSelect.value) {
                e.preventDefault();
                showDepartmentError("{{ __('Please select a department.') }}");
                return false;
            }

            if (!validateDepartmentSelection()) {
                e.preventDefault();
                return false;
            }

            if (isCompletedCheckbox.checked) {
                if (!estimateCalledInput.value) {
                    e.preventDefault();
                    estimateCalledInput.focus();
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
            fetchDepartmentStatus(workSelect.value, true);
        } else {
            disableDepartment();
        }

        updateFormSections();
    })();
</script>
