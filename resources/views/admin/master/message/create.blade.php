{{ Form::open(['route' => 'master.message.store', 'method' => 'post', 'files' => true]) }}
<div class="modal-body">
    <div class="row">

        {{-- Title / Name --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('title', __('Name / Title'), ['class' => 'form-label']) }}
            {{ Form::text('title', null, [
                'class'       => 'form-control',
                'placeholder' => __('e.g. John Doe / Chairman'),
            ]) }}
        </div>

        {{-- Designation --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('designation', __('Designation'), ['class' => 'form-label']) }}
            {{ Form::text('designation', null, [
                'class'       => 'form-control',
                'placeholder' => __('e.g. Director, CEO, HOD'),
            ]) }}
        </div>

        {{-- Image Upload --}}
        <div class="form-group col-md-12 mb-3">
            {{ Form::label('image', __('Photo / Image'), ['class' => 'form-label']) }}
            {{ Form::file('image', [
                'class'    => 'form-control',
                'accept'   => 'image/jpg,image/jpeg,image/png,image/webp',
                'id'       => 'create_msg_image',
                'onchange' => 'previewMsgImage(this,"create_msg_preview","create_msg_preview_wrap")',
            ]) }}
            <small class="text-muted">{{ __('Allowed: jpg, jpeg, png, webp. Max: 2MB') }}</small>

            {{-- Preview --}}
            <div class="mt-2 d-none" id="create_msg_preview_wrap">
                <img id="create_msg_preview"
                     src="#"
                     alt="Preview"
                     class="rounded-circle border"
                     style="width:80px;height:80px;object-fit:cover;">
            </div>
        </div>

        {{-- Sort Order --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('sort_order', __('Sort Order'), ['class' => 'form-label']) }}
            {{ Form::number('sort_order', 0, [
                'class' => 'form-control',
                'min'   => '0',
            ]) }}
        </div>

        {{-- Status --}}
        <div class="form-group col-md-6 mb-3">
            {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}
            {{ Form::select('status',
                [1 => __('Active'), 0 => __('Inactive')],
                1,
                ['class' => 'form-select']
            ) }}
        </div>

        {{-- Message - TinyMCE --}}
        <div class="form-group col-md-12 mb-3">
            {{ Form::label('message',
                __('Message') . '<span class="text-danger"> *</span>',
                ['class' => 'form-label'], false) }}
            {{ Form::textarea('message', null, [
                'id' => 'classic-editor',
                'placeholder' => __('Write message here...'),
                'rows'        => 6,
                'required'    => true,
            ]) }}
        </div>

    </div>
</div>
<div class="modal-footer">
    <button type="button"
            class="btn btn-light btn-rounded"
            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
    {{ Form::submit(__('Create'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>
{{ Form::close() }}

@push('scripts')
<script src="https://cdn.tiny.cloud/1/YOUR_TINYMCE_API_KEY/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    // ── TinyMCE init ──
    tinymce.init({
        selector: '#create_message_editor',
        height: 300,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'charmap',
            'anchor', 'searchreplace', 'visualblocks', 'code',
            'insertdatetime', 'table', 'wordcount'
        ],
        toolbar:
            'undo redo | blocks | bold italic underline | ' +
            'alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist outdent indent | removeformat | code',
        content_style: 'body { font-family: -apple-system, sans-serif; font-size: 14px; }',
        branding: false,
        promotion: false,
    });

    // ── Image preview ──
    function previewMsgImage(input, previewId, wrapId) {
        const preview = document.getElementById(previewId);
        const wrap    = document.getElementById(wrapId);
        if (input.files && input.files[0]) {
            const reader  = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                wrap.classList.remove('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            wrap.classList.add('d-none');
        }
    }
</script>
@endpush

