@extends('layouts.app')
@section('page-title')
    {{ __('Inspection List') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Master') }}</a></li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Inspection List') }}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>{{ __('Inspection Records') }}</h5>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('master.inspection.create') }}" class="btn btn-secondary">
                                <i class="ti ti-circle-plus align-text-bottom"></i> {{ __('Add Inspection') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="dt-responsive table-responsive">
                        <table class="table table-hover advance-datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Designation') }}</th>
                                    <th>{{ __('Movement Type') }}</th>
                                    <th>{{ __('Type of Inspection') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inspections as $index => $inspection)
                                    <tr>
                                        <td>{{ $inspections->firstItem() + $index }}</td>
                                        <td>{{ \Carbon\Carbon::parse($inspection->date_of_inspection)->format('d-m-Y') }}</td>
                                        <td>{{ $inspection->designation }}</td>
                                        <td>
                                            <span class="badge bg-secondary text-primary">{{ $inspection->movement }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $inspection->type_of_inspection }}</span>
                                        </td>
                                        <td class="text-end">
                                            <div class="cart-action">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['master.inspection.destroy', encrypt($inspection->id)], 'style' => 'display:inline;']) !!}
                                                
                                                <a class="avtar avtar-xs btn-link-success text-success"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-original-title="{{ __('View Details') }}"
                                                   href="{{ route('master.inspection.show', encrypt($inspection->id)) }}">
                                                    <i data-feather="eye"></i>
                                                </a>

                                                <a class="avtar avtar-xs btn-link-secondary text-secondary"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-original-title="{{ __('Edit') }}"
                                                   href="{{ route('master.inspection.edit', encrypt($inspection->id)) }}">
                                                    <i data-feather="edit"></i>
                                                </a>

                                                <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-original-title="{{ __('Delete') }}"
                                                   href="#">
                                                    <i data-feather="trash-2"></i>
                                                </a>

                                                {!! Form::close() !!}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $inspections->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
