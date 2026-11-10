{{ Form::open(['url' => 'note', 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group  col-md-12">
            {{ Form::label('title', __('Title') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::text('title', null, ['class' => 'form-control', 'placeholder' => __('Enter note title')]) }}
        </div>
        <div class="form-group  col-md-12">
            {{ Form::label('attachment', __('Attachment'), ['class' => 'form-label']) }}
            {{ Form::file('attachment', ['class' => 'form-control']) }}
        </div>
        <div class="form-group  col-md-12">
            {{ Form::label('description', __('Description') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
            {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 5]) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    {{ Form::submit(__('Create'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>
{{ Form::close() }}

