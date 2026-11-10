@extends('layouts.app')
@section('page-title')
    {{ measurementPrefix() . $measurement->measurement_id }} {{ __('Details') }}
@endsection
@php
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
            <a href="{{ route('measurement.index') }}">{{ __('Measurement') }}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#">
                {{ measurementPrefix() . $measurement->measurement_id }} {{ __('Details') }}
            </a>
        </li>
    </ul>
@endsection

@section('content')
    <div id="invoice-print" class="measurement row">
        <div class="tab-pane" id="profile-2" role="tabpanel" aria-labelledby="profile-tab-2">
            <div class="card border invoice-card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-sm-7">
                            <h5 class="mb-0">{{ measurementPrefix() . $measurement->measurement_id }}</h5>
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
                                {{ customerPrefix() }}{{ !empty($measurement->customers->customers) ? $measurement->customers->customers->customer_id : '-' }}
                            </p>
                            <p class="text-muted mb-1">
                                {{ !empty($measurement->customers) ? $measurement->customers->name : '' }}</p>
                            <p class="text-muted mb-1">
                                {{ !empty($measurement->customers) ? $measurement->customers->phone_number : '' }}
                            </p>
                            <p class="text-muted mb-1">
                                {{ !empty($measurement->customers) && !empty($measurement->customers->customers) ? $measurement->customers->customers->address : '' }},<br>
                                {{ !empty($measurement->customers) && !empty($measurement->customers->customers) ? $measurement->customers->customers->city : '' }}
                            </p>
                        </div>

                        <div class="col-md-6">
                            <h4 class="mb-2"><b>{{ __('Measurement Details') }} :</b></h4>
                            <p class="text-muted mb-1">{{ __('Date') }} :
                                {{ dateFormat($measurement->date) }}</p>
                            <p class="text-muted mb-1">
                                {{ __('Cloth Type') }} :
                                {{ !empty($measurement->clothTypes) ? $measurement->clothTypes->title : '-' }}

                            </p>
                            <p class="text-muted">{{ __('Responsible') }} :
                                {{ !empty($measurement->users) ? $measurement->users->name : '-' }}</p>
                        </div>

                    </div>
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
                                @foreach ($measurement->measurement_detail as $details)
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
                <button type="button" class="btn btn-print-invoice btn-secondary m-b-10 m-r-10 print">{{ __('Print') }}</button>
            </div>
        </div>



    </div>
@endsection

