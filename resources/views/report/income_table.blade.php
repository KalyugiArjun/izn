<div class="dt-responsive table-responsive">
    <table class="table table-hover advance-datatable">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Customer') }}</th>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Due Date') }}</th>
                <th>{{ __('Amount') }}</th>
                <th>{{ __('Status') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($incomes as $invoice)
                <tr>
                    <td>{{ invoicePrefix() . $invoice->invoice_id }} </td>
                    <td>{{ !empty($invoice->customers) ? $invoice->customers->name : '-' }} </td>
                    <td>{{ dateFormat($invoice->invoice_date) }}</td>
                    <td>{{ dateFormat($invoice->due_date) }}</td>
                    <td>{{ priceFormat($invoice->getInvoiceTotalAmount()) }}</td>
                    <td>
                        @if ($invoice->status == 'paid')
                            <span
                                class="badge text-bg-success">{{ \App\Models\Invoice::$status[$invoice->status] }}</span>
                        @elseif($invoice->status == 'partial_paid')
                            <span
                                class="badge text-bg-warning">{{ \App\Models\Invoice::$status[$invoice->status] }}</span>
                        @else
                            <span
                                class="badge text-bg-danger">{{ \App\Models\Invoice::$status[$invoice->status] }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4">{{ __('Total Amount') }}</th>
                <th class="income_total">
                    {{ priceFormat(
                        number_format(
                            $incomes->sum(function ($invoice) {
                                return $invoice->getInvoiceTotalAmount();
                            }),
                            2,
                        ),
                    ) }}
                </th>
            </tr>
        </tfoot>

    </table>
</div>

