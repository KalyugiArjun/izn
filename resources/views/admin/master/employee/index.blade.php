@extends('layouts.app')

@section('page-title', __('Master Employee'))

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Master') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Employee') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>{{ __('Employee List') }}</h5>
                        </div>
                        <div class="col-auto">
                            <a href="#" class="btn btn-secondary customModal" data-size="xl"
                                data-url="{{ route('master.employee.create') }}" data-title="{{ __('Create Employee') }}">
                                <i class="ti ti-circle-plus align-text-bottom"></i>
                                {{ __('Create Employee') }}
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
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('HRMS ID') }}</th>
                                    <th>{{ __('Department') }}</th>
                                    <th>{{ __('Designation') }}</th>
                                    <th>{{ __('Date of Birth') }}</th>
                                    <th>{{ __('Date of Appointment') }}</th>
                                    <th>{{ __('Date of Retirement') }}</th>
                                    <th>{{ __('Jurisdiction Station(s)') }}</th>
                                    <th>{{ __('Date of Posting at Present Working Place') }}</th>
                                    <th>{{ __('Mobile') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Posting At Present Working Place') }}</th>
                                    <th>{{ __('Level') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees as $index => $employee)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2 font-weight-bold">
                                                {{ $employee->name }}
                                            </div>
                                        </td>
                                        <td>
                                            {{ $employee->usmid ?? '-' }}
                                        </td>
                                        <td>{{ $employee->department->name ?? '-' }}</td>
                                        <td>{{ $employee->designation->name ?? '-' }}</td>
                                        <td>
                                            {{ $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format('d-m-Y') : '-' }}
                                        </td>
                                        <td>
                                            {{ $employee->date_of_appointment ? \Carbon\Carbon::parse($employee->date_of_appointment)->format('d-m-Y') : '-' }}
                                        </td>
                                        <td>
                                            {{ $employee->date_of_retirement ? \Carbon\Carbon::parse($employee->date_of_retirement)->format('d-m-Y') : '-' }}
                                        </td>

                                        <td>
                                            @forelse ($employee->stations as $station)
                                                <span class="badge bg-light text-dark border me-1 mb-1">{{ $station->name }} ({{ $station->code }})</span>
                                            @empty
                                                <span class="text-muted">-</span>
                                            @endforelse
                                        </td>
                                        <td>
                                            {{ $employee->date_of_posting_present_station ? \Carbon\Carbon::parse($employee->date_of_posting_present_station)->format('d-m-Y') : '-' }}
                                        </td>
                                        <td>{{ $employee->mobile ?? '-' }}</td>
                                        <td>{{ $employee->email ?? '-' }}</td>
                                        <td>{{ $employee->posting_present_station ?? '-' }}</td>
                                        <td>
                                            @if(!empty($employee->level))
                                                <span class="badge bg-info-subtle text-info">{{ $employee->level }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
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
                                            <div class="cart-action d-inline-flex gap-1">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['master.employee.destroy', encrypt($employee->id)], 'class' => 'd-inline']) !!}
                                                <a class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                    data-bs-toggle="tooltip" data-size="xl"
                                                    data-bs-original-title="{{ __('Edit') }}" href="#"
                                                    data-url="{{ route('master.employee.edit', encrypt($employee->id)) }}"
                                                    data-title="{{ __('Edit Employee') }}"> <i data-feather="edit"></i></a>

                                                <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                                    data-bs-toggle="tooltip" data-bs-original-title="{{ __('Delete') }}"
                                                    href="#"> <i data-feather="trash-2"></i></a>
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
