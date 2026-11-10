<div class="dt-responsive table-responsive">
    <table class="table table-hover advance-datatable">
        <thead>
            <tr>
                <th>{{ __('Expense') }}</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Category') }}</th>
                <th>{{ __('Sub Category') }}</th>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Amount') }}</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($expenses as $expense)
                <tr role="row">
                    <td>{{ expensePrefix() . $expense->expense_id }} </td>
                    <td> {{ $expense->title }} </td>
                    <td> {{ $expense->category->name }} </td>
                    <td> {{ $expense->subCategory->name }} </td>
                    <td> {{ dateFormat($expense->date) }} </td>
                    <td> {{ priceFormat($expense->amount) }} </td>

                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5">{{ __('Total Amount') }}</th>
                <th class="expense_total">{{ priceFormat(number_format($expenses->sum('amount'), 2)) }}</th>
            </tr>
        </tfoot>
    </table>
</div>


