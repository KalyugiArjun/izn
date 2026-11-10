<table class="table table-bordered" id="profit_loss_table">
    <thead>
        <tr>
            <th>{{ __('Month') }}</th>
            <th>{{ __('Income') }}</th>
            <th>{{ __('Expense') }}</th>
            <th>{{ __('Profit / Loss') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($report as $row)
            <tr>
                <td>{{ $row['month'] }}</td>
                <td>{{ priceFormat(number_format($row['income'], 2)) }}</td>
                <td>{{ priceFormat(number_format($row['expense'], 2)) }}</td>
                <td class="{{ $row['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ priceFormat(number_format($row['profit'], 2)) }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>


