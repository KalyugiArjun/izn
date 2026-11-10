@extends('layouts.app')
@section('page-title')
    {{ __('Today Order') }}
@endsection
@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#">
                {{ __('Today Order') }}
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
                            <h5>{{ __('Today Order List') }}</h5>
                        </div>
                        <div class="col-auto">

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
                                    <th>{{ __('Deadline') }}</th>
                                    <th>{{ __('Cloth Type') }}</th>
                                    <th>{{ __('Gender') }}</th>
                                    <th>{{ __('Responsible') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    <tr>
                                        <td>{{ orderPrefix() . $order->order_id }} </td>
                                        <td>{{ !empty($order->customers) ? $order->customers->name : '-' }} </td>
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
                                            <div class="cart-action">
                                                @can('show order')
                                                    <a class="avtar avtar-xs btn-link-warning text-warning"
                                                        data-bs-toggle="tooltip" data-bs-original-title="{{ __('Details') }}"
                                                        href="{{ route('order.show', \Illuminate\Support\Facades\Crypt::encrypt($order->id)) }}">
                                                        <i data-feather="eye"></i></a>
                                                @endcan
                                                @can('edit order')
                                                    <a class="avtar avtar-xs btn-link-secondary text-secondary "
                                                        data-bs-toggle="tooltip" data-bs-original-title="{{ __('Edit') }}"
                                                        href="{{ route('order.edit', \Illuminate\Support\Facades\Crypt::encrypt($order->id)) }}">
                                                        <i data-feather="edit"></i></a>
                                                @endcan
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

