{{ Form::open([
    'route' => ['coaching-rake-type.update', encrypt($record->id)],
    'method' => 'POST',
    'id' => 'coaching_rake_type_form',
]) }}

<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-12 mb-3">
            {{ Form::label('name', 'Name', ['class' => 'form-label']) }}
            <span class="text-danger">*</span>
            {{ Form::text('name', old('name', $record->name), [
                'class' => 'form-control',
                'required' => true,
            ]) }}
            @error('name')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group col-md-12 mb-3">
            {{ Form::label('status', 'Status', ['class' => 'form-label']) }}
            <span class="text-danger">*</span>
            {{ Form::select('status', [1 => 'Active', 0 => 'Inactive'], old('status', $record->status), [
                'class' => 'form-control',
                'required' => true,
            ]) }}
            @error('status')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="submit" class="btn btn-primary">Update</button>
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
</div>

{{ Form::close() }}
