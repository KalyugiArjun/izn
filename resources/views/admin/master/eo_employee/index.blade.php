@extends('layouts.app')

@section('page-title')
    {{ __('EO Employee') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Master') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">
        {{ __('EO Employee') }}
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>{{ __('EO Employee List') }}</h5>
                        </div>

                        <div class="col-auto">
                            <a href="#" class="btn btn-secondary customModal" data-size="md"
                                data-url="{{ route('master.eo-employee.create') }}"
                                data-title="{{ __('Create EO Employee') }}">
                                <i class="ti ti-circle-plus align-text-bottom"></i>
                                {{ __('Create EO Employee') }}
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
                                    <th>{{ __('Diesel Lobb') }}</th>
                                    <th>{{ __('HRMS ID') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Designation') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($employees as $index => $employee)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>

                                        <td>
                                            {{ $employee->dieselLobby->name ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $employee->hrms_id }}
                                        </td>

                                        <td>
                                            {{ $employee->name }}
                                        </td>

                                        <td>
                                            {{ $employee->designation->name ?? '-' }}
                                        </td>

                                        <td>
                                            @if ($employee->status == 1)
                                                <span class="badge bg-success-subtle text-success">
                                                    {{ __('Active') }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger">
                                                    {{ __('Inactive') }}
                                                </span>
                                            @endif
                                        </td>

                                        <td class="text-end">
                                            <div class="cart-action">

                                                {!! Form::open([
                                                    'method' => 'DELETE',
                                                    'route' => ['master.eo-employee.destroy', encrypt($employee->id)],
                                                ]) !!}

                                                <a class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                    data-bs-toggle="tooltip" data-size="md"
                                                    data-bs-original-title="{{ __('Edit') }}" href="#"
                                                    data-url="{{ route('master.eo-employee.edit', encrypt($employee->id)) }}"
                                                    data-title="{{ __('Edit EO Employee') }}">
                                                    <i data-feather="edit"></i>
                                                </a>

                                                <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                                    data-bs-toggle="tooltip" data-bs-original-title="{{ __('Delete') }}"
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
                </div>

            </div>
        </div>
    </div>
@endsection
