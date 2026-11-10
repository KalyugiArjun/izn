@extends('layouts.app')
@section('page-title')
    {{ __('Order') }}
@endsection
@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#">
                {{ __('Order') }}
            </a>
        </li>
    </ul>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>{{ __('Order List') }}</h5>
                        </div>
                        <div class="col-auto">
                            @if (Gate::check('create order'))
                                <a class="btn btn-secondary" href="{{ route('order.create') }}">
                                    <i class="ti ti-circle-plus align-text-bottom"></i>
                                    {{ __('Create Order') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0">
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
                                    <th>{{ __('Action') }}</th>
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

                                        <td>
                                            <div class="cart-action">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['order.destroy', encrypt($order->id)]]) !!}

                                                {{-- View Invoice or Create Invoice --}}
                                                {{-- @can('create invoice payment')
                                                    @if (!empty($order->invoice))
                                                        <a class="avtar avtar-xs btn-link-success text-success"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-original-title="{{ __('View Invoice') }}"
                                                            href="{{ route('invoice.show', \Illuminate\Support\Facades\Crypt::encrypt($order->invoice->id)) }}">
                                                            <i class="ti ti-file-text f-18"></i>
                                                        </a>
                                                    @else
                                                        <a href="#" class="avtar avtar-xs btn-link-secondary customModal"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-original-title="{{ __('Create Invoice') }}" data-size="md"
                                                            data-url="{{ route('invoice.create', ['selectedCustomerId' => $order->customer_id, 'order_id' => $order->id]) }}"
                                                            data-title="{{ __('Create Invoice') }}">
                                                            <i class="ti ti-circle-plus f-18"></i>
                                                        </a>
                                                    @endif
                                                @endcan --}}

                                                {{-- View Order --}}
                                                @can('show order')
                                                    <a class="avtar avtar-xs btn-link-warning text-warning"
                                                        data-bs-toggle="tooltip" data-bs-original-title="{{ __('Details') }}"
                                                        href="{{ route('order.show', \Illuminate\Support\Facades\Crypt::encrypt($order->id)) }}">
                                                        <i data-feather="eye"></i>
                                                    </a>
                                                @endcan

                                                {{-- Edit Order --}}
                                                @can('edit order')
                                                    <a class="avtar avtar-xs btn-link-secondary text-secondary"
                                                        data-bs-toggle="tooltip" data-bs-original-title="{{ __('Edit') }}"
                                                        href="{{ route('order.edit', \Illuminate\Support\Facades\Crypt::encrypt($order->id)) }}">
                                                        <i data-feather="edit"></i>
                                                    </a>
                                                @endcan

                                                {{-- Delete Order --}}
                                                @can('delete order')
                                                    <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                                        data-bs-toggle="tooltip" data-bs-original-title="{{ __('Delete') }}"
                                                        href="#">
                                                        <i data-feather="trash-2"></i>
                                                    </a>
                                                @endcan

                                                {!! Form::close() !!}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

