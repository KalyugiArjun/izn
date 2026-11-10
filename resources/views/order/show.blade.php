@extends('layouts.app')
@section('page-title')
    {{ orderPrefix() . $order->order_id }} {{ __('Details') }}
@endsection
@php
    $admin_logo = getSettingsValByName('company_logo');
    $settings = settings();
@endphp
@push('script-page')
    <script>
        $(document).on('click', '.print', function() {
            var printContents = document.getElementById('invoice-print').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        });
    </script>
@endpush

@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('order.index') }}">{{ __('Order') }}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#">
                {{ orderPrefix() . $order->order_id }} {{ __('Details') }}
            </a>
        </li>
    </ul>
@endsection

@section('content')
    <div id="invoice-print" class="order row">
        {{-- @if (!in_array($order->status, ['delivered', 'on_hold', 'cancelled']))
            <div class="col-sm-12">
                <div class="d-print-none card mb-3">
                    <div class="card-body p-3">
                        <ul class="list-inline ms-auto mb-0 d-flex justify-content-end flex-wrap">

                            <li class="list-inline-item align-bottom me-2">
                                @can('create invoice')
                                    <a href="#" class="avtar avtar-s btn-link-secondary customModal"
                                        data-bs-toggle="tooltip" data-bs-original-title="{{ __('Create Invoice') }}"
                                        data-size="md"
                                        data-url="{{ route('invoice.create', ['selectedCustomerId' => $order->customer_id, 'order_id' => $order->id]) }}"
                                        data-title="{{ __('Create Invoice') }}">
                                        <i class="ph-duotone ph-receipt f-30"></i>
                                    </a>
                                @endcan
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif --}}

        <div class="tab-pane" id="profile-2" role="tabpanel" aria-labelledby="profile-tab-2">
            <div class="card border invoice-card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-sm-7">
                            <h5 class="mb-0">{{ orderPrefix() . $order->order_id }}</h5>
                        </div>
                        <div class="col-sm-5 text-sm-end"><img class="img-fluid invoice-logo"
                                src=" {{ asset(Storage::url('upload/logo/')) . '/' . (isset($admin_logo) && !empty($admin_logo) ? $admin_logo : 'logo.png') }}"
                                alt="invoice-logo">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">

                        <div class="col-md-6">
                            <h4 class="mb-2"><b>{{ __('Customer') }} :</b></h4>
                            <p class="text-muted mb-1">
                                {{ customerPrefix() }}{{ !empty($order->customers->customers) ? $order->customers->customers->customer_id : '-' }}
                            </p>
                            <p class="text-muted mb-1">
                                {{ !empty($order->customers) ? $order->customers->name : '' }}</p>
                            <p class="text-muted mb-1">
                                {{ !empty($order->customers) ? $order->customers->phone_number : '' }}
                            </p>
                            <p class="text-muted mb-1">
                                {{ !empty($order->customers) && !empty($order->customers->customers) ? $order->customers->customers->address : '' }},<br>
                                {{ !empty($order->customers) && !empty($order->customers->customers) ? $order->customers->customers->city : '' }}
                            </p>
                        </div>

                        <div class="col-md-6">
                            <h4 class="mb-2"><b>{{ __('Measurement Details') }} :</b></h4>
                            <p class="text-muted mb-1">{{ __('Order Date') }} :
                                {{ dateFormat($order->order_date) }}</p>
                            <p class="text-muted mb-1">{{ __('Deadline Date') }} :
                                {{ dateFormat($order->deadline_date) }}</p>
                            <p class="text-muted mb-1">
                                {{ __('Status') }} :
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
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4 align-items-center justify-content-between">
                        <div class="col-auto">
                            <p class="mb-1"><b>{{ __('Cloth Type') }}</b></p>
                            <p class="text-muted">{{ !empty($order->clothTypes) ? $order->clothTypes->title : '-' }}</p>
                        </div>
                        <div class="col-auto">
                            <p class="mb-1"><b>{{ __('Quantity') }}</b></p>
                            <p class="text-muted">{{ $order->quantity }}</p>
                        </div>
                        <div class="col-auto">
                            <p class="mb-1"><b>{{ __('Febric') }}</b></p>
                            <p class="text-muted">{{ $order->febric }}</p>
                        </div>
                        <div class="col-auto">
                            <p class="mb-1"><b>{{ __('Febric Color') }}</b></p>
                            <p class="text-muted">{{ $order->febric_color }}</p>
                        </div>
                        <div class="col-auto">
                            <p class="mb-1"><b>{{ __('Gender') }}</b></p>
                            <p class="text-muted">{{ $order->gender }}</p>
                        </div>

                        <div class="col-auto">
                            <p class="mb-1"><b>{{ __('Responsible') }}</b></p>
                            <p class="text-muted">{{ !empty($order->users) ? $order->users->name : '-' }}</p>
                        </div>
                        <div class="col-auto">
                            <p class="mb-1"><b>{{ __('Note') }}</b></p>
                            <p class="text-muted">{{ $order->notes }}</p>
                        </div>
                    </div>
                    @if (!empty($order->fabric_attachment) || !empty($order->sewing_pattern))
                        <div class="container mb-2">
                            <p class="mb-1">{{ __('Attachments') }}</p>
                            <div class="row g-4">
                                <div class="col-md-3 text-center">
                                    <img src="{{ asset(Storage::url('upload/fabric_attachment/' . $order->fabric_attachment)) }}"
                                        alt="fabric_attachment" class="img-fluid rounded shadow mb-2"
                                        style="height:200px; object-fit:cover;">
                                    <p class="fw-bold mb-0">{{ __('Fabric') }}</p>
                                </div>

                                <div class="col-md-3 text-center">
                                    <img src="{{ asset(Storage::url('upload/sewing_pattern/' . $order->sewing_pattern)) }}"
                                        alt="sewing_pattern" class="img-fluid rounded shadow mb-2"
                                        style="height:200px; object-fit:cover;">
                                    <p class="fw-bold mb-0">{{ __('Sewing Pattern') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Measurement Type') }}</th>
                                    <th>{{ __('Measurement') }}</th>
                                    <th>{{ __('Unit') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->measurement as $details)
                                    <tr>
                                        <td>{{ $details->type }}</td>
                                        <td>{{ $details->measurement }}</td>
                                        <td>{{ $details->unit }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
            <div class="text-center d-print-none mt-5 mb-3">
                <button type="button"
                    class="btn btn-print-invoice btn-secondary m-b-10 m-r-10 print">{{ __('Print') }}</button>
            </div>
        </div>
    </div>
@endsection
@push('css-page')
    <style>
        @media print {
            .row {
                display: flex !important;
                flex-wrap: wrap !important;
            }

            .col-md-3 {
                flex: 0 0 25% !important;
                max-width: 25% !important;
                padding: 0 10px !important;
                text-align: center;
            }

            img {
                max-width: 100% !important;
                height: auto !important;
            }
        }
    </style>
@endpush

