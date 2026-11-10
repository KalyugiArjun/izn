@extends('layouts.app')

@section('page-title', __('Load Clearance Finalized By Visibility'))

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Load Stable') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Load Clearance Finalized By') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>{{ __('Load Clearance Finalized By Visibility List') }}</h5>
                        </div>
                        <div class="col-auto">
                            <a href="#" class="btn btn-secondary customModal" data-size="md"
                                data-url="{{ route('load-stable.clearance-finaliz-by.create') }}"
                                data-title="{{ __('Create Load Clearance Finalized By Visibility') }}">
                                <i class="ti ti-circle-plus align-text-bottom"></i>
                                {{ __('Create Load Clearance Finalized By') }}
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
                                    <th>{{ __('Department') }}</th>
                                    <th>{{ __('Designation') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($records as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->department->name ?? '-' }}</td>
                                        <td>{{ $item->designation->name ?? '-' }}</td>
                                        <td class="text-end">
                                            <div class="cart-action">
                                                {{ Form::open([
                                                    'method' => 'DELETE',
                                                    'route' => ['load-stable.clearance-finaliz-by.destroy', encrypt($item->id)],
                                                ]) }}

                                                <a class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                    data-bs-toggle="tooltip" data-size="lg"
                                                    data-bs-original-title="{{ __('Edit') }}" href="#"
                                                    data-url="{{ route('load-stable.clearance-finaliz-by.edit', encrypt($item->id)) }}"
                                                    data-title="{{ __('Edit Load Clearance Finalized By Visibility') }}">
                                                    <i data-feather="edit"></i>
                                                </a>

                                                <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                                    data-bs-toggle="tooltip" data-bs-original-title="{{ __('Delete') }}"
                                                    href="#">
                                                    <i data-feather="trash-2"></i>
                                                </a>

                                                {{ Form::close() }}
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
