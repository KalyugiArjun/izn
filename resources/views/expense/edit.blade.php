{{ Form::model($expense, ['route' => ['expense.update', encrypt($expense->id)], 'method' => 'put', 'enctype' => 'multipart/form-data']) }}
<div class="modal-body">
    <div class="row">
        <div class="form-group col-md-6 col-lg-6">
            {{ Form::label('expense_id', __('Expense Number'), ['class' => 'form-label']) }}
            <div class="input-group">
                <span class="input-group-text ">
                    {{ expensePrefix() }}
                </span>
                {{ Form::text('expense_id', $billNumber, ['class' => 'form-control', 'placeholder' => __('Enter Expense Number')]) }}
            </div>
        </div>
        <div class="form-group  col-md-6 col-lg-6">
            {{ Form::label('date', __('Date') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::date('date', null, ['class' => 'form-control']) }}
        </div>
        <div class="form-group col-md-6 col-lg-6">
            {{ Form::label('category_id', __('Expense Category') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::select('category_id', $categories, null, ['class' => 'form-control select2', 'id' => 'category_id', 'placeholder' => __('Select Category')]) }}
        </div>

        <div class="form-group col-md-6 col-lg-6">
            {{ Form::label('sub_category_id', __('Expense Subcategory'), ['class' => 'form-label']) }}
            {{ Form::select('sub_category_id', [], $expense->sub_category_id, ['class' => 'form-control select2', 'id' => 'subcategory_id', 'placeholder' => __('Select Subcategory')]) }}
        </div>

        <div class="form-group  col-md-12 col-lg-12">
            {{ Form::label('title', __('Expense Title') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::text('title', null, ['class' => 'form-control', 'placeholder' => __('Enter Expense Title')]) }}
        </div>

        <div class="form-group  col-md-6 col-lg-6">
            {{ Form::label('amount', __('Amount') . '<span class="text-danger"> *</span>', ['class' => 'form-label'], false) }}
            {{ Form::number('amount', null, ['class' => 'form-control', 'placeholder' => __('Enter Expense Amount')]) }}
        </div>
        <div class="form-group  col-md-6 col-lg-6">
            {{ Form::label('receipt', __('Receipt'), ['class' => 'form-label']) }}
            {{ Form::file('receipt', ['class' => 'form-control']) }}
        </div>
        <div class="form-group  col-md-12 col-lg-126">
            {{ Form::label('notes', __('Notes'), ['class' => 'form-label']) }}
            {{ Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 2]) }}
        </div>
    </div>
</div>
<div class="modal-footer">

    {{ Form::submit(__('Update'), ['class' => 'btn btn-secondary btn-rounded']) }}
</div>
{{ Form::close() }}
<script>
    $(document).ready(function() {
        var selectedSubCategory = "{{ $expense->sub_category_id }}";

        // On category change, load subcategories
        $('#category_id').on('change', function() {
            var category_id = $(this).val();
            $('#subcategory_id').empty().append('<option value="">{{ __("Select Subcategory") }}</option>');

            if (category_id) {
                $.ajax({
                    url: "{{ route('subcategory.by.category') }}",
                    type: "GET",
                    data: { category_id: category_id },
                    success: function(data) {
                        $.each(data, function(key, value) {
                            var selected = (value.id == selectedSubCategory) ? 'selected' : '';
                            $('#subcategory_id').append('<option value="' + value.id + '" ' + selected + '>' + value.name + '</option>');
                        });
                    }
                });
            }
        });

        // 👉 Trigger change once on page load (for edit mode)
        $('#category_id').trigger('change');
    });
</script>


