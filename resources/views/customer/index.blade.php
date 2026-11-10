@extends('layouts.app')
@php
    $profile = asset(Storage::url('upload/profile/'));
@endphp
@section('page-title')
    {{ __('Customer') }}
@endsection
@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#">
                {{ __('Customer') }}
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
                            <h5>{{ __('Customer List') }}</h5>
                        </div>
                        <div class="col-auto">
                            @if (Gate::check('create customer'))
                                <a class="btn btn-secondary" href="{{ route('customer.create') }}"
                                   data-title="{{ __('Create Customer') }}"> <i
                                        class="ti ti-circle-plus align-text-bottom"></i>
                                    {{ __('Create Customer') }}
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
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Phone Number') }}</th>
                                    <th>{{ __('City') }}</th>
                                    <th>{{ __('Address') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($customers as $customer)
                                    <tr>
                                        <td>{{ customerPrefix() }}{{ !empty($customer->customers) ? $customer->customers->customer_id : '-' }}
                                        </td>
                                        <td class="table-user">
                                            <img src="{{ !empty($customer->profile) ? $profile . '/' . $customer->profile : $profile . '/avatar.png' }}"
                                                alt="" class="mr-2 avatar-sm rounded-circle user-avatar">
                                            <a href="#"
                                                class="text-body font-weight-semibold">{{ $customer->name }}</a>
                                        </td>
                                        <td>{{ $customer->email }} </td>
                                        <td>{{ !empty($customer->phone_number) ? $customer->phone_number : '-' }} </td>
                                        <td>{{ !empty($customer->customers) ? $customer->customers->city : '-' }} </td>
                                        <td>{{ !empty($customer->customers) ? $customer->customers->address : '-' }} </td>
                                        <td>
                                            <div class="cart-action">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['customer.destroy', encrypt($customer->id)]]) !!}
                                                @can('edit customer')
                                                    <a class="avtar avtar-xs btn-link-secondary text-secondary customModal" data-bs-toggle="tooltip" data-size="lg"
                                                        data-bs-original-title="{{ __('Edit') }}" href="#"
                                                        data-url="{{ route('customer.edit', encrypt($customer->id)) }}"
                                                        data-title="{{ __('Edit Customer') }}"> <i data-feather="edit"></i></a>
                                                @endcan
                                                @can('delete customer')
                                                    <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog" data-bs-toggle="tooltip"
                                                        data-bs-original-title="{{ __('Detete') }}" href="#"> <i
                                                            data-feather="trash-2"></i></a>
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

