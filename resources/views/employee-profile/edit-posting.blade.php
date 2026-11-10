{{ Form::open([
    'route' => ['employee-profile.update', encrypt($employee->id), 'posting'],
    'method' => 'POST',
    'id' => 'edit_posting_form',
]) }}

<div class="modal-body">

    <div class="row">

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('posting_present_station', 'Present Working Place / Station', ['class' => 'form-label']) }}
            {{ Form::text('posting_present_station', old('posting_present_station', $employee->posting_present_station), [
                'class' => 'form-control',
                'placeholder' => 'e.g. DRM Office Izzatnagar / Jasoda',
            ]) }}
            @error('posting_present_station')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('date_of_posting_present_station', 'Date of Joining at Station', ['class' => 'form-label']) }}
            {{ Form::date(
                'date_of_posting_present_station',
                old('date_of_posting_present_station', $employee->date_of_posting_present_station),
                [
                    'class' => 'form-control',
                ],
            ) }}
            @error('date_of_posting_present_station')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group col-md-12 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="form-label mb-0 fw-bold">{{ __('Jurisdiction Station(s) Assigned') }}</label>
                <span class="badge bg-light text-primary border">{{ count($selectedStations ?? []) }} stations currently assigned</span>
            </div>
            @if(isset($stations) && count($stations) > 0)
                <select name="station_ids[]" id="edit_posting_station_ids" class="form-select select2-multiple" multiple="multiple" style="width: 100%;">
                    @foreach($stations as $sId => $sName)
                        <option value="{{ $sId }}" {{ in_array($sId, $selectedStations ?? []) ? 'selected' : '' }}>
                            {{ $sName }}
                        </option>
                    @endforeach
                </select>
                <small class="text-muted mt-1 d-block"><i class="ti ti-info-circle me-1"></i>Search and assign stations under this employee's jurisdiction scope.</small>
            @endif
            @error('station_ids')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror
        </div>

    </div>

</div>

<div class="modal-footer">
    <button type="submit" class="btn btn-primary"><i class="ti ti-check me-1"></i> {{ __('Update Posting') }}</button>
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
</div>

{{ Form::close() }}

<script>
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('#edit_posting_station_ids').select2({
            dropdownParent: $('#customModal'),
            placeholder: 'Select Station(s)',
            allowClear: true,
            width: '100%'
        });
    }
</script>
