{{ Form::open([
    'route' => 'works-paperwork-progress-register.inspection-log.store',
    'method' => 'POST',
    'files' => true,
    'id' => 'inspection_log_form',
]) }}

<div class="modal-body">
    <div class="row">

        {{-- 1. Work Description (Full Width col-md-12 with clean form-select) --}}
        <div class="form-group col-md-12 mb-3">
            {{ Form::label('identify_new_work_id', 'Work Description', ['class' => 'form-label']) }}
            <span class="text-danger">*</span>

            <select name="identify_new_work_id" id="identify_new_work_id" class="form-select" required>
                <option value="">Select Work</option>
                @foreach ($works as $work)
                    <option value="{{ $work->id }}" {{ old('identify_new_work_id') == $work->id ? 'selected' : '' }}>
                        {{ $work->work_description_identification }}
                    </option>
                @endforeach
            </select>

            <small class="text-muted d-block mt-1">
                Only works assigned to you are listed here.
            </small>

            @error('identify_new_work_id')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror
        </div>

        {{-- 2. Date of Inspection & Department Inspected (col-md-6 each) --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('date_of_inspection', 'Date of Inspection', ['class' => 'form-label']) }}
            <span class="text-danger">*</span>

            {{ Form::date('date_of_inspection', old('date_of_inspection'), [
                'class' => 'form-control',
                'id' => 'date_of_inspection',
                'required' => true,
            ]) }}

            @error('date_of_inspection')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('department_inspected', 'Department Inspected', ['class' => 'form-label']) }}
            <span class="text-danger">*</span>

            <select name="department_inspected" id="department_inspected" class="form-select" required>
                <option value="">Select Department</option>
                @foreach ($departments as $department)
                    <option value="{{ $department }}"
                        {{ old('department_inspected') == $department ? 'selected' : '' }}>
                        {{ $department }}
                    </option>
                @endforeach
            </select>

            @error('department_inspected')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        {{-- 3. Inspecting Authority & Name of Inspecting Official (col-md-6 each) --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('inspecting_authority', 'Inspecting Authority', ['class' => 'form-label']) }}
            <span class="text-danger">*</span>

            <select name="inspecting_authority" id="inspecting_authority" class="form-select" required>
                <option value="">Select Authority</option>
                @foreach ($authorities as $authority)
                    <option value="{{ $authority }}"
                        {{ old('inspecting_authority') == $authority ? 'selected' : '' }}>
                        {{ $authority }}
                    </option>
                @endforeach
            </select>

            @error('inspecting_authority')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('name_of_inspecting_official', 'Name of Inspecting Official', ['class' => 'form-label']) }}
            <span class="text-danger">*</span>

            {{ Form::text('name_of_inspecting_official', old('name_of_inspecting_official'), [
                'class' => 'form-control',
                'id' => 'name_of_inspecting_official',
                'required' => true,
                'placeholder' => 'Enter name of inspecting official',
            ]) }}

            @error('name_of_inspecting_official')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>


        <div class="form-group col-md-12 mb-3">
            {{ Form::label('work_done_detail', 'Detail of Work Done by the Department at the Time of Inspection', ['class' => 'form-label']) }}
            <span class="text-danger">*</span>

            {{ Form::textarea('work_done_detail', old('work_done_detail'), [
                'class' => 'form-control',
                'id' => 'work_done_detail',
                'rows' => 3,
                'required' => true,
            ]) }}

            @error('work_done_detail')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>


        <div class="form-group col-md-12 mb-3">
            {{ Form::label('photos', 'Photos Taken During Inspection', ['class' => 'form-label']) }}

            <input type="file" name="photos[]" id="photos" class="form-control"
                accept="image/jpeg,image/png,.jpg,.jpeg,.png" multiple>

            <small class="text-muted">
                You can select multiple photos. JPG/PNG only, max 5 MB each. Each photo is automatically
                stamped with the date &amp; time it is added to this record.
            </small>

            @error('photos')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror

            @error('photos.*')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror
        </div>

    </div>
</div>

<div class="modal-footer">

    <button type="submit" class="btn btn-primary">
        Save
    </button>

    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
        Cancel
    </button>

</div>

{{ Form::close() }}


