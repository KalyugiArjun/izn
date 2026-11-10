{{ Form::open([
    'route' => 'works-paperwork-progress-register.execution.store',
    'method' => 'POST',
    'files' => true,
    'id' => 'execution_form',
]) }}

<div class="modal-body">
    <div class="row">

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('identify_new_work_id', 'Work Description', ['class' => 'form-label']) }}
            <span class="text-danger">*</span>

            <select name="identify_new_work_id" id="identify_new_work_id" class="form-control select2" required>

                <option value="">Select Work</option>

                @foreach ($works as $work)
                    <option value="{{ $work->id }}" {{ old('identify_new_work_id') == $work->id ? 'selected' : '' }}>
                        {{ $work->work_description_identification }}
                    </option>
                @endforeach

            </select>

            <small class="text-muted">
                Only works whose ESP / SIP is fully completed are listed here.
            </small>

            @error('identify_new_work_id')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror
        </div>


        <div class="form-group col-md-6 mb-3">
            {{ Form::label('milestone', 'Milestone', ['class' => 'form-label']) }}
            <span class="text-danger">*</span>

            {{ Form::select(
                'milestone',
                [
                    '' => 'Select Milestone',
                    'Work commenced' => 'Work commenced',
                    'TDC — target date of completion' => 'TDC — target date of completion',
                ],
                old('milestone'),
                [
                    'class' => 'form-control',
                    'id' => 'milestone',
                    'required' => true,
                    'disabled' => true,
                ],
            ) }}

            <small class="text-muted" id="milestone_hint">
                Please select Work Description first.
            </small>

            <div class="text-danger mt-1" id="milestone_error" style="display:none;"></div>

            @error('milestone')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>


        <div class="form-group col-md-6 mb-3">
            {{ Form::label('date', 'Date', ['class' => 'form-label']) }}
            <span class="text-danger">*</span>

            {{ Form::date('date', old('date'), [
                'class' => 'form-control',
                'id' => 'execution_date',
                'required' => true,
            ]) }}

            <small class="text-muted" id="execution_date_hint" style="display:none;"></small>

            @error('date')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>


        <div class="form-group col-md-6 mb-3">
            {{ Form::label('days_taken', 'Days Taken', ['class' => 'form-label']) }}
            <span class="text-danger">*</span>
            <input type="text" id="days_taken" class="form-control" placeholder="Days from previous stage" readonly>

            <small class="text-muted">
                Automatically calculated from the previous stage's date.
            </small>
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('work_completion_date', 'Work Completion Date', ['class' => 'form-label']) }}

            <span class="text-danger">*</span>

            {{ Form::date('work_completion_date', old('work_completion_date'), [
                'class' => 'form-control',
                'id' => 'work_completion_date',
                'required' => true,
            ]) }}

            @error('work_completion_date')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('supporting_document', 'Supporting Document (PDF)', ['class' => 'form-label']) }}
            <span class="text-danger">*</span>
            {{ Form::file('supporting_document', [
                'class' => 'form-control',
                'accept' => 'application/pdf,.pdf',
            ]) }}

            <small class="text-muted">
                Only PDF files are allowed. Maximum size 10 MB.
            </small>

            @error('supporting_document')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror
        </div>

    </div>
</div>

<div class="modal-footer">

    <button type="submit" class="btn btn-primary" id="execution_submit_btn">
        Save
    </button>

    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
        Cancel
    </button>

</div>

{{ Form::close() }}

<script>
    (function() {

        const workSelect = document.getElementById('identify_new_work_id');
        const milestoneSelect = document.getElementById('milestone');
        const milestoneHint = document.getElementById('milestone_hint');
        const milestoneError = document.getElementById('milestone_error');
        const executionDateInput = document.getElementById('execution_date');
        const executionDateHint = document.getElementById('execution_date_hint');
        const daysInput = document.getElementById('days_taken');
        const form = document.getElementById('execution_form');
        const submitBtn = document.getElementById('execution_submit_btn');

        const milestoneOrder = [
            'Work commenced',
            'TDC — target date of completion',
        ];

        const milestoneStatusUrlBase =
            "{{ url('works-paperwork-progress-register/execution/milestone-status') }}";
        const ignoreId = null; // create form: koi record ignore nahi karna

        let savedMilestones = [];
        let savedDates = {};
        let espSipMaxDate = null;
        let previousMilestoneValue = milestoneSelect.value;
        let milestoneStatusLoaded = false;

        function getBaseDateForSelectedMilestone() {
            const selected = milestoneSelect.value;

            if (!selected) return null;

            const idx = milestoneOrder.indexOf(selected);

            if (idx === 0) {
                return espSipMaxDate;
            }

            const previousMilestone = milestoneOrder[idx - 1];
            return savedDates[previousMilestone] || null;
        }

        function updateDateConstraints() {
            const baseDate = getBaseDateForSelectedMilestone();

            if (!baseDate) {
                executionDateInput.removeAttribute('min');
                executionDateHint.style.display = 'none';
                return;
            }

            executionDateInput.setAttribute('min', baseDate);

            const parts = baseDate.split('-');
            executionDateHint.textContent = 'Date must be on or after ' + parts[2] + '-' + parts[1] + '-' + parts[
                0] + ' (previous stage date).';
            executionDateHint.style.display = '';

            if (executionDateInput.value && executionDateInput.value < baseDate) {
                executionDateInput.value = '';
                daysInput.value = '';
            }
        }

        function calculateDays() {
            const baseDate = getBaseDateForSelectedMilestone();
            const currentDate = executionDateInput.value;

            if (!baseDate || !currentDate) {
                daysInput.value = '';
                return;
            }

            const previousDate = new Date(baseDate + 'T00:00:00');
            const currDate = new Date(currentDate + 'T00:00:00');

            if (isNaN(previousDate.getTime()) || isNaN(currDate.getTime())) {
                daysInput.value = '';
                return;
            }

            const difference = Math.floor((currDate - previousDate) / (1000 * 60 * 60 * 24));
            daysInput.value = (difference >= 0 ? difference : 0) + ' days from previous stage';
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
            milestoneHint.textContent = 'Please select Work Description first.';
            milestoneHint.style.display = '';
            clearMilestoneError();
            savedMilestones = [];
            savedDates = {};
            espSipMaxDate = null;
            previousMilestoneValue = '';
            milestoneStatusLoaded = false;
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
            milestoneHint.textContent = 'Loading milestone status...';
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
                savedDates = (response && response.savedDates) ? response.savedDates : {};
                espSipMaxDate = (response && response.espSipMaxDate) ? response.espSipMaxDate : null;
                milestoneStatusLoaded = true;
                enableMilestone();

                if (!keepValue) {
                    milestoneSelect.value = '';
                    previousMilestoneValue = '';
                }
            }).fail(function(xhr) {
                console.error('Milestone status fetch failed:', xhr.status, xhr.responseText);
                milestoneStatusLoaded = false;
                savedMilestones = [];
                savedDates = {};
                espSipMaxDate = null;
                milestoneSelect.setAttribute('disabled', 'disabled');
                milestoneHint.style.display = 'none';
                showMilestoneError(
                    'Could not load milestone status. Please reselect the Work Description and try again.'
                );
            });
        }

        function validateMilestoneSelection() {
            const selected = milestoneSelect.value;

            clearMilestoneError();

            if (!milestoneStatusLoaded) {
                showMilestoneError('Milestone status not loaded yet. Please reselect the Work Description.');
                return false;
            }

            if (!selected) {
                previousMilestoneValue = '';
                return true;
            }

            if (savedMilestones.includes(selected)) {
                showMilestoneError('This milestone has already been registered for the selected work.');
                milestoneSelect.value = previousMilestoneValue;
                return false;
            }

            const expected = nextExpectedMilestone();

            if (!expected) {
                showMilestoneError('All Execution milestones have already been registered for this work.');
                milestoneSelect.value = previousMilestoneValue;
                return false;
            }

            if (selected !== expected) {
                showMilestoneError('Please register "' + expected + '" first.');
                milestoneSelect.value = previousMilestoneValue;
                return false;
            }

            previousMilestoneValue = selected;
            return true;
        }

        if ($.fn.select2) {
            $(workSelect).select2({
                dropdownParent: $(workSelect).closest('.modal'),
                width: '100%',
            });
        }

        $(document).on('change select2:select', '#identify_new_work_id', function() {
            fetchMilestoneStatus(this.value, false).then(function() {
                updateDateConstraints();
                calculateDays();
            });
        });

        $(document).on('select2:clear', '#identify_new_work_id', function() {
            disableMilestone();
            updateDateConstraints();
            calculateDays();
        });

        $(document).on('change', '#milestone', function() {
            validateMilestoneSelection();
            updateDateConstraints();
            calculateDays();
        });

        $(document).on('change input', '#execution_date', calculateDays);

        $(document).on('submit', '#execution_form', function(e) {
            if (!milestoneStatusLoaded) {
                e.preventDefault();
                if (submitBtn) submitBtn.setAttribute('disabled', 'disabled');
                showMilestoneError(
                    'Milestone status not loaded. Please reselect the Work Description before submitting.'
                );
                return false;
            }

            if (!milestoneSelect.value) {
                e.preventDefault();
                showMilestoneError('Please select a milestone.');
                return false;
            }

            if (!validateMilestoneSelection()) {
                e.preventDefault();
                return false;
            }

            const minDate = executionDateInput.getAttribute('min');
            if (minDate && executionDateInput.value && executionDateInput.value < minDate) {
                e.preventDefault();
                showMilestoneError('Date cannot be before the previous stage date.');
                return false;
            }

            if (submitBtn) submitBtn.setAttribute('disabled', 'disabled');
        });

        // Initial state
        if (workSelect.value) {
            fetchMilestoneStatus(workSelect.value, true).then(function() {
                updateDateConstraints();
                calculateDays();
            });
        } else {
            disableMilestone();
        }

        calculateDays();

    })();
</script>
