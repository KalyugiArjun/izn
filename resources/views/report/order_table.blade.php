                    <div class="dt-responsive table-responsive">
                        <table class="table table-hover advance-datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Order Date') }}</th>
                                    <th>{{ __('Deadline') }}</th>
                                    <th>{{ __('Cloth Type') }}</th>
                                    <th>{{ __('Gender') }}</th>
                                    <th>{{ __('Responsible') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Invoice') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td>{{ orderPrefix() . $order->order_id }} </td>
                                        <td>{{ !empty($order->customers) ? $order->customers->name : '-' }} </td>
                                        <td>{{ dateFormat($order->order_date) }}</td>
                                        <td>{{ dateFormat($order->deadline_date) }}</td>
                                        <td>{{ !empty($order->clothTypes) ? $order->clothTypes->title : '-' }}</td>
                                        <td>{{ $order->gender }}</td>
                                        <td>{{ !empty($order->users) ? $order->users->name : '-' }}</td>
                                        <td>
                                            @if ($order->status == 'pending')
                                                <span
                                                    class="badge text-bg-warning">{{ \App\Models\Order::$status[$order->status] }}</span>
                                            @elseif($order->status == 'in_progress')
                                                <span
                                                    class="badge text-bg-primary">{{ \App\Models\Order::$status[$order->status] }}</span>
                                            @elseif($order->status == 'completed')
                                                <span
                                                    class="badge text-bg-success">{{ \App\Models\Order::$status[$order->status] }}</span>
                                            @elseif($order->status == 'delivered')
                                                <span
                                                    class="badge text-bg-danger">{{ \App\Models\Order::$status[$order->status] }}</span>
                                            @else
                                                <span
                                                    class="badge text-bg-info">{{ \App\Models\Order::$status[$order->status] }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if (!empty($order->invoices))
                                                <a href="{{ route('invoice.show', encrypt($order->invoices->id)) }}">
                                                    {{ invoicePrefix() . $order->invoices->invoice_id }}
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>

