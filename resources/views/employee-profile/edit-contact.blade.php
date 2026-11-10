{{ Form::open([
    'route' => ['employee-profile.update', encrypt($employee->id), 'contact'],
    'method' => 'POST',
    'id' => 'edit_contact_form',
]) }}

<div class="modal-body">

    <div class="row">

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('mobile', 'Mobile Number', ['class' => 'form-label']) }}
            <span class="text-danger">*</span>
            {{ Form::text('mobile', old('mobile', $employee->mobile), [
                'class' => 'form-control',
                'required' => true,
                'maxlength' => 20,
            ]) }}
            @error('mobile')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('email', 'Email ID', ['class' => 'form-label']) }}
            <span class="text-danger">*</span>
            {{ Form::email('email', old('email', $employee->email), [
                'class' => 'form-control',
                'required' => true,
            ]) }}
            @error('email')
                <span class="text-danger d-block">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('status', 'Status', ['class' => 'form-label']) }}
            <span class="text-danger">*</span>
            {{ Form::select('status', [1 => 'Active', 2 => 'Inactive'], old('status', $employee->status), [
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
