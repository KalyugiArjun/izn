{{ Form::model($slider, [
    'route'  => ['master.slider.update', encrypt($slider->id)],
    'method' => 'POST',
    'files'  => true,
]) }}
<div class="modal-body">
    <div class="row">

        {{-- Title --}}
        <div class="form-group col-md-12 mb-3">
            {{ Form::label('title', __('Title'), ['class' => 'form-label']) }}
            {{ Form::text('title', null, [
                'class'       => 'form-control',
                'placeholder' => __('Enter slider title'),
            ]) }}
        </div>

        {{-- Subtitle --}}
        <div class="form-group col-md-12 mb-3">
            {{ Form::label('subtitle', __('Subtitle'), ['class' => 'form-label']) }}
            {{ Form::textarea('subtitle', null, [
                'class' => 'form-control',
                'rows'  => 3,
            ]) }}
        </div>

        {{-- Current Image + Upload New --}}
        <div class="form-group col-md-12 mb-3">
            {{ Form::label('image', __('Slider Image'), ['class' => 'form-label']) }}

            {{-- Show current image --}}
            @if ($slider->image)
                <div class="mb-2">
                    <p class="text-muted small mb-1">{{ __('Current Image:') }}</p>
                    <img src="{{ $slider->image_url }}"
                         alt="{{ $slider->title }}"
                         class="rounded border"
                         id="edit_current_img"
                         style="max-height: 120px; max-width: 100%; object-fit: cover;">
                </div>
            @endif

            {{ Form::file('image', [
                'class'    => 'form-control',
                'accept'   => 'image/jpg,image/jpeg,image/png,image/webp',
                'id'       => 'edit_image_input',
                'onchange' => 'previewEditImage(this)',
            ]) }}
            <small class="text-muted">{{ __('Leave empty to keep current image. Max: 2MB') }}</small>

            {{-- New image preview --}}
            <div class="mt-2" id="edit_preview_wrap" style="display:none;">
                <p class="text-muted small mb-1">{{ __('New Image Preview:') }}</p>
                <img id="edit_preview"
                     src="#"
                     alt="Preview"
                     class="rounded border"
                     style="max-height: 120px; max-width: 100%; object-fit: cover;">
            </div>
        </div>

        {{-- Button Text --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('button_text', __('Button Text'), ['class' => 'form-label']) }}
            {{ Form::text('button_text', null, [
                'class'       => 'form-control',
                'placeholder' => __('e.g. Learn More'),
            ]) }}
        </div>

        {{-- Button URL --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('button_url', __('Button URL'), ['class' => 'form-label']) }}
            {{ Form::text('button_url', null, [
                'class'       => 'form-control',
                'placeholder' => __('e.g. https://example.com'),
            ]) }}
        </div>

        {{-- Sort Order --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('sort_order', __('Sort Order'), ['class' => 'form-label']) }}
            {{ Form::number('sort_order', null, [
                'class' => 'form-control',
                'min'   => '0',
            ]) }}
        </div>

        {{-- Status --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}
            {{ Form::select('status',
                [1 => __('Active'), 0 => __('Inactive')],
                null,
                ['class' => 'form-select']
            ) }}
        </div>

    </div>
</div>
<div class="modal-footer">
    <button type="button"
            class="btn btn-light btn-rounded"
            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Update'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>
{{ Form::close() }}

@push('scripts')
<script>
    function previewEditImage(input) {
        const preview     = document.getElementById('edit_preview');
        const previewWrap = document.getElementById('edit_preview_wrap');
        const currentImg  = document.getElementById('edit_current_img');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src               = e.target.result;
                previewWrap.style.display = 'block';
                // Dim the current image to show it will be replaced
                if (currentImg) currentImg.style.opacity = '0.4';
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            previewWrap.style.display = 'none';
            if (currentImg) currentImg.style.opacity = '1';
        }
    }
</script>
@endpush

