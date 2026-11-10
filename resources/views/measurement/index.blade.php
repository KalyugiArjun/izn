@extends('layouts.app')
@section('page-title')
    {{ __('Measurement') }}
@endsection
@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#">
                {{ __('Measurement') }}
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
                            <h5>{{ __('Measurement List') }}</h5>
                        </div>
                        <div class="col-auto">
                            @if (Gate::check('create measurement'))
                                <a class="btn btn-secondary" href="{{ route('measurement.create') }}"> <i
                                        class="ti ti-circle-plus align-text-bottom"></i>
                                    {{ __('Create Measurement') }}
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
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Cloth Type') }}</th>
                                    <th>{{ __('Responsible') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($measurements as $measurement)
                                    <tr>
                                        <td>{{ measurementPrefix() . $measurement->measurement_id }} </td>
                                        <td>{{ !empty($measurement->customers) ? $measurement->customers->name : '-' }} </td>
                                        <td>{{ dateFormat($measurement->date) }}</td>
                                        <td>{{ !empty($measurement->clothTypes) ? $measurement->clothTypes->title : '-' }}</td>
                                        <td>{{ !empty($measurement->users) ? $measurement->users->name : '-' }}</td>
                                        <td>
                                            <div class="cart-action">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['measurement.destroy', encrypt($measurement->id)]]) !!}
                                                @can('show measurement')
                                                    <a class="avtar avtar-xs btn-link-warning text-warning" data-bs-toggle="tooltip"
                                                        data-bs-original-title="{{ __('Details') }}"
                                                        href="{{ route('measurement.show', encrypt($measurement->id)) }}">
                                                        <i data-feather="eye"></i></a>
                                                @endcan
                                                @can('edit measurement')
                                                    <a class="avtar avtar-xs btn-link-secondary text-secondary " data-bs-toggle="tooltip"
                                                        data-bs-original-title="{{ __('Edit') }}"
                                                        href="{{ route('measurement.edit', encrypt($measurement->id)) }}">
                                                        <i data-feather="edit"></i></a>
                                                @endcan
                                                @can('delete measurement')
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

