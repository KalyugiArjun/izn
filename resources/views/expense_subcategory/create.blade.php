{{ Form::open(['route' => 'expense-sub-category.store', 'method' => 'post']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group">
            {{ Form::label('category_id', __('Category') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::select('category_id', $categories, null, ['class' => 'form-control select2', 'placeholder' => __('Select category name'), 'required' => 'required']) }}
        </div>
        <div class="form-group">
            {{ Form::label('name', __('Name') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter sub category name'), 'required' => 'required']) }}
        </div>
    </div>
</div>
<div class="modal-footer">
    {{ Form::submit(__('Create'), ['class' => 'btn btn-secondary ml-10']) }}
</div>
{{ Form::close() }}

