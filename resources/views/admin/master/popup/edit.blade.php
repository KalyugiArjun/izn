{{ Form::model($popup, [
    'route' => ['master.popup.update', encrypt($popup->id)],
    'method' => 'PUT',
    'files' => true,
]) }}

<div class="modal-body">
    <div class="row">

        <div class="form-group col-md-12 mb-3">
            {{ Form::label('title', __('Title') . ' *', ['class' => 'form-label']) }}
            {{ Form::text('title', $popup->title, [
                'class' => 'form-control',
                'required' => true,
                'placeholder' => __('Enter popup title'),
            ]) }}
        </div>

        <div class="form-group col-md-12 mb-3">
            {{ Form::label('subtitle', __('Subtitle'), ['class' => 'form-label']) }}
            {{ Form::text('subtitle', $popup->subtitle, [
                'class' => 'form-control',
                'placeholder' => __('Enter popup subtitle'),
            ]) }}
        </div>

        <div class="form-group col-md-12 mb-3">
            {{ Form::label('message', __('Message'), ['class' => 'form-label']) }}
            {{ Form::textarea('message', $popup->message, [
                'class' => 'form-control',
                'rows' => 3,
                'placeholder' => __('Enter popup message'),
            ]) }}
        </div>

        <div class="form-group col-md-12 mb-3">
            {{ Form::label('note', __('Note'), ['class' => 'form-label']) }}
            {{ Form::textarea('note', $popup->note, [
                'class' => 'form-control',
                'rows' => 2,
                'placeholder' => __('Enter additional note'),
            ]) }}
        </div>

        <div class="form-group col-md-12 mb-3">
            {{ Form::label('image', __('Popup Image'), ['class' => 'form-label']) }}

            @if ($popup->image)
                <div class="mb-2">
                    <img src="{{ $popup->image_url }}"
                         alt="{{ $popup->title }}"
                         class="rounded border"
                         style="max-width:100%;max-height:150px;object-fit:cover;">
                </div>
            @endif

            {{ Form::file('image', [
                'class' => 'form-control',
                'accept' => 'image/jpeg,image/png,image/webp',
                'id' => 'popup_edit_image',
                'onchange' => 'previewPopupImage(this, "popup_edit_preview")',
            ]) }}

            <small class="text-muted">
                {{ __('Optional. Leave empty to keep the current image. Maximum 2MB.') }}
            </small>

            <div id="popup_edit_preview_wrap" class="mt-2" style="display:none;">
                <img id="popup_edit_preview"
                     src="#"
                     alt="Image preview"
                     class="rounded border"
                     style="max-width:100%;max-height:150px;object-fit:cover;">
            </div>
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('button_text', __('Button Text'), ['class' => 'form-label']) }}
            {{ Form::text('button_text', $popup->button_text, [
                'class' => 'form-control',
                'placeholder' => __('e.g. Login Now'),
            ]) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('button_url', __('Button URL'), ['class' => 'form-label']) }}
            {{ Form::text('button_url', $popup->button_url, [
                'class' => 'form-control',
                'placeholder' => __('e.g. /login or https://example.com'),
            ]) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('starts_at', __('Start Date & Time'), ['class' => 'form-label']) }}
            {{ Form::input(
                'datetime-local',
                'starts_at',
                $popup->starts_at ? $popup->starts_at->format('Y-m-d\TH:i') : null,
                ['class' => 'form-control']
            ) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('ends_at', __('End Date & Time'), ['class' => 'form-label']) }}
            {{ Form::input(
                'datetime-local',
                'ends_at',
                $popup->ends_at ? $popup->ends_at->format('Y-m-d\TH:i') : null,
                ['class' => 'form-control']
            ) }}
        </div>

        <div class="form-group col-md-6 mb-3">
            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}
            {{ Form::select('status', [
                1 => __('Active'),
                0 => __('Inactive'),
            ], $popup->status ? 1 : 0, [
                'class' => 'form-select',
                'required' => true,
            ]) }}
        </div>

    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-light btn-rounded" data-bs-dismiss="modal">
        {{ __('Cancel') }}
    </button>

    {{ Form::submit(__('Update Popup'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>

{{ Form::close() }}

<script>
    if (typeof window.previewPopupImage !== 'function') {
        window.previewPopupImage = function (input, previewId) {
            const preview = document.getElementById(previewId);
            const wrap = document.getElementById(previewId + '_wrap');

            if (!preview || !wrap) return;

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function (event) {
                    preview.src = event.target.result;
                    wrap.style.display = 'block';
                };

                reader.readAsDataURL(input.files[0]);
            } else {
                wrap.style.display = 'none';
            }
        };
    }
</script>