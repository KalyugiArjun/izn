@extends('layouts.app')
@section('page-title')
    {{ __('Cloth Type') }}
@endsection
@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#">
                {{ __('Cloth Type') }}
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
                            <h5>{{ __('Cloth Type List') }}</h5>
                        </div>
                        <div class="col-auto">
                            @if (Gate::check('create cloth type'))
                                <a class="btn btn-secondary" href="{{ route('cloth-type.create') }}"> <i
                                        class="ti ti-circle-plus align-text-bottom"></i>
                                    {{ __('Create Cloth Type') }}
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
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Gender') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Tax') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clothTypes as $clothType)
                                    <tr>
                                        <td>{{ $clothType->title }} </td>
                                        <td>{{ $clothType->gender }}</td>
                                        <td>{{ priceFormat($clothType->amount) }} </td>
                                        <td>
                                            @if (!empty($clothType->taxs()))
                                                @foreach ($clothType->taxs() as $tax)
                                                    <span>{{ $tax->tax }} ({{ $tax->rate }}%)</span>
                                                @endforeach
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <div class="cart-action">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['cloth-type.destroy', encrypt($clothType->id)]]) !!}
                                                @can('show cloth type')
                                                    <a class="avtar avtar-xs btn-link-warning text-warning"
                                                        data-bs-toggle="tooltip" data-bs-original-title="{{ __('Details') }}"
                                                        href="{{ route('cloth-type.show', \Illuminate\Support\Facades\Crypt::encrypt($clothType->id)) }}">
                                                        <i data-feather="eye"></i></a>
                                                @endcan
                                                @can('edit cloth type')
                                                    <a class="avtar avtar-xs btn-link-secondary text-secondary "
                                                        data-bs-toggle="tooltip" data-bs-original-title="{{ __('Edit') }}"
                                                        href="{{ route('cloth-type.edit', \Illuminate\Support\Facades\Crypt::encrypt($clothType->id)) }}">
                                                        <i data-feather="edit"></i></a>
                                                @endcan
                                                @can('delete cloth type')
                                                    <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                                        data-bs-toggle="tooltip" data-bs-original-title="{{ __('Detete') }}"
                                                        href="#"> <i data-feather="trash-2"></i></a>
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

