@extends('layouts.app')

@section('page-title', __('Employee Report'))

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Report') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Employee') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            
            <!-- Card 1: Search Employee Filters -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>{{ __('Search Employee') }}</h5>
                </div>
                <div class="card-body">
                    {{ Form::open(['route' => 'report.master.employee.data', 'method' => 'post']) }}
                    <div class="row align-items-end">
                        {{-- Search Type --}}
                        <div class="form-group col-md-3 mb-3">
                            {{ Form::label('search_type', __('Search Type') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
                            {{ Form::select('search_type',
                            [
                                'date_of_birth' => __('Date of birth'), 
                                'date_of_appointment' => __('Date of Appointment'),
                                'date_of_retirement' => __('Date of Retirement')
                            ], 
                            1, [
                            'class' => 'form-select',
                            'required' => true,
                            ]) }}
                        </div>

                        {{-- From Date --}}
                        <div class="form-group col-md-3 mb-3">
                            {{ Form::label('from_date', __('From Date') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
                            {{ Form::date('from_date', null, [
                                'class' => 'form-control',
                                'required' => true,
                            ]) }}
                        </div>

                        {{-- To Date --}}
                        <div class="form-group col-md-3 mb-3">
                            {{ Form::label('to_date', __('To Date') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
                            {{ Form::date('to_date', null, [
                                'class' => 'form-control',
                                'required' => true,
                            ]) }}
                        </div>

                        {{-- Submit Button --}}
                        <div class="form-group col-md-3 mb-3">
                            {{ Form::submit(__('Search'), ['class' => 'btn btn-primary w-100']) }}
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>

            <!-- Card 2: Employee List Table -->
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Employee List') }}</h5>
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
                                    <th>{{ __('Present Place of Posting') }}</th>
                                    <th>{{ __('Date of Posting at Present Station') }}</th>
                                    <th>{{ __('Mobile') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Status') }}</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $index => $employee)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                {{ $employee->name }}
                                            </div>
                                        </td>
                                        <td>
                                            {{ $employee->usmid }}
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

                                                    {{ $station->name}} ({{ $station->code  }}),

                                            @empty
                                                <span class="text-muted">-</span>
                                            @endforelse
                                        </td>
                                        <td>
                                            {{ $employee->date_of_posting_present_station ? \Carbon\Carbon::parse($employee->date_of_posting_present_station)->format('d-m-Y') : '-' }}
                                        </td>
                                        <td>{{ $employee->mobile ?? '-' }}</td>
                                        <td>{{ $employee->email ?? '-' }}</td>
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

