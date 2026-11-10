
{{ Form::open([
    'route' => 'master.popup.store',
    'method' => 'POST',
    'files' => true,
    'id' => 'popupCreateForm',
    'enctype' => 'multipart/form-data',
]) }}

<div class="modal-body popup-form">

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">

        {{-- Title --}}
        <div class="form-group col-md-12 mb-3">
            {{ Form::label('title', __('Title') . ' *', ['class' => 'form-label']) }}

            {{ Form::text('title', old('title'), [
                'class' => 'form-control',
                'placeholder' => __('Enter popup title'),
                'required' => true,
                'maxlength' => 255,
            ]) }}
        </div>

        {{-- Subtitle --}}
        <div class="form-group col-md-12 mb-3">
            {{ Form::label('subtitle', __('Subtitle'), ['class' => 'form-label']) }}

            {{ Form::text('subtitle', old('subtitle'), [
                'class' => 'form-control',
                'placeholder' => __('Enter popup subtitle'),
                'maxlength' => 255,
            ]) }}
        </div>

        {{-- Message --}}
        <div class="form-group col-md-12 mb-3">
            {{ Form::label('message', __('Message'), ['class' => 'form-label']) }}

            {{ Form::textarea('message', old('message'), [
                'class' => 'form-control',
                'rows' => 3,
                'placeholder' => __('Enter popup message'),
            ]) }}
        </div>

        {{-- Note --}}
        <div class="form-group col-md-12 mb-3">
            {{ Form::label('note', __('Note'), ['class' => 'form-label']) }}

            {{ Form::textarea('note', old('note'), [
                'class' => 'form-control',
                'rows' => 2,
                'placeholder' => __('Enter additional note'),
            ]) }}
        </div>

        {{-- Popup Image --}}
        <div class="form-group col-md-12 mb-3">
            {{ Form::label('image', __('Popup Image'), ['class' => 'form-label']) }}

            {{ Form::file('image', [
                'class' => 'form-control',
                'accept' => '.jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp',
                'id' => 'popup_create_image',
            ]) }}

            <small class="text-muted d-block mt-1">
                Optional. JPG, JPEG, PNG or WEBP. Maximum 10MB.
            </small>

            <div id="popup_create_preview_wrap" class="popup-preview mt-3" hidden>
                <img
                    id="popup_create_preview"
                    src=""
                    alt="Popup image preview"
                >

                <button
                    type="button"
                    id="popup_remove_image"
                    class="btn btn-sm btn-outline-danger mt-2"
                >
                    <i class="ti ti-trash me-1"></i>
                    Remove Image
                </button>
            </div>
        </div>

        {{-- Button Text --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('button_text', __('Button Text'), ['class' => 'form-label']) }}

            {{ Form::text('button_text', old('button_text'), [
                'class' => 'form-control',
                'placeholder' => __('e.g. Login Now'),
                'maxlength' => 100,
            ]) }}
        </div>

        {{-- Button URL --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('button_url', __('Button URL'), ['class' => 'form-label']) }}

            {{ Form::text('button_url', old('button_url'), [
                'class' => 'form-control',
                'placeholder' => __('e.g. /login or https://example.com'),
                'maxlength' => 2048,
            ]) }}
        </div>

        {{-- Start Date --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('starts_at', __('Start Date & Time'), ['class' => 'form-label']) }}

            {{ Form::input('datetime-local', 'starts_at', old('starts_at'), [
                'class' => 'form-control',
            ]) }}

            <small class="text-muted">
                Leave empty to start immediately.
            </small>
        </div>

        {{-- End Date --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('ends_at', __('End Date & Time'), ['class' => 'form-label']) }}

            {{ Form::input('datetime-local', 'ends_at', old('ends_at'), [
                'class' => 'form-control',
            ]) }}

            <small class="text-muted">
                Leave empty for no expiry.
            </small>
        </div>

        {{-- Status --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}

            {{ Form::select('status', [
                1 => __('Active'),
                0 => __('Inactive'),
            ], old('status', 1), [
                'class' => 'form-select',
                'required' => true,
            ]) }}
        </div>

    </div>
</div>

{{-- Footer --}}
<div class="modal-footer popup-form-footer">
    <button
        type="button"
        class="btn btn-light btn-rounded"
        data-bs-dismiss="modal"
    >
        {{ __('Cancel') }}
    </button>

    <button
        type="submit"
        id="popupCreateSubmit"
        class="btn btn-secondary btn-rounded"
    >
        <i class="ti ti-device-floppy me-1"></i>
        {{ __('Create Popup') }}
    </button>
</div>

{{ Form::close() }}

<style>
    .popup-form {
        padding: 20px;
    }

    .popup-form .form-label {
        font-weight: 600;
        color: #343a40;
        margin-bottom: 7px;
    }

    .popup-form .form-control,
    .popup-form .form-select {
        border: 1px solid #dfe3eb;
        border-radius: 8px;
        min-height: 42px;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .popup-form textarea.form-control {
        min-height: auto;
        resize: vertical;
    }

    .popup-form .form-control:focus,
    .popup-form .form-select:focus {
        border-color: #7c6be8;
        box-shadow: 0 0 0 3px rgba(124, 107, 232, .12);
    }

    .popup-preview {
        padding: 14px;
        border: 1px dashed #cbd0d8;
        border-radius: 10px;
        background: #f8f9fc;
    }

    .popup-preview img {
        display: block;
        max-width: 100%;
        max-height: 220px;
        margin: 0 auto;
        object-fit: contain;
        border-radius: 8px;
    }

    .popup-form-footer {
        border-top: 1px solid #edf0f4;
        padding: 15px 20px;
        gap: 8px;
    }

    #popupCreateSubmit:disabled {
        opacity: .7;
        cursor: not-allowed;
    }
</style>

<script>
(function () {
    const form = document.getElementById('popupCreateForm');

    if (!form || form.dataset.initialized === '1') {
        return;
    }

    form.dataset.initialized = '1';

    const fileInput = document.getElementById('popup_create_image');
    const previewWrap = document.getElementById('popup_create_preview_wrap');
    const previewImage = document.getElementById('popup_create_preview');
    const removeButton = document.getElementById('popup_remove_image');
    const submitButton = document.getElementById('popupCreateSubmit');

    const MAX_FILE_SIZE = 10 * 1024 * 1024;
    let previewUrl = null;
    let isSubmitting = false;

    function clearPreview() {
        if (previewUrl) {
            URL.revokeObjectURL(previewUrl);
            previewUrl = null;
        }

        previewImage.removeAttribute('src');
        previewWrap.hidden = true;
    }

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            clearPreview();

            const file = this.files && this.files[0];

            if (!file) {
                return;
            }

            const allowedTypes = [
                'image/jpeg',
                'image/png',
                'image/webp'
            ];

            if (!allowedTypes.includes(file.type)) {
                alert('Please select a JPG, PNG or WEBP image.');
                this.value = '';
                return;
            }

            // Maximum image size: 10 MB
            if (file.size > MAX_FILE_SIZE) {
                alert('Image size must not exceed 10MB.');
                this.value = '';
                return;
            }

            previewUrl = URL.createObjectURL(file);
            previewImage.src = previewUrl;
            previewWrap.hidden = false;
        });
    }

    if (removeButton && fileInput) {
        removeButton.addEventListener('click', function () {
            fileInput.value = '';
            clearPreview();
        });
    }

    form.addEventListener('submit', function (event) {
        if (isSubmitting) {
            event.preventDefault();
            return;
        }

        if (!form.checkValidity()) {
            event.preventDefault();
            form.reportValidity();
            return;
        }

        // Validate the selected file again before submitting.
        const file = fileInput && fileInput.files
            ? fileInput.files[0]
            : null;

        if (file && file.size > MAX_FILE_SIZE) {
            event.preventDefault();
            alert('Image size must not exceed 10MB.');
            return;
        }

        isSubmitting = true;

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';
        }
    });
})();
</script>
