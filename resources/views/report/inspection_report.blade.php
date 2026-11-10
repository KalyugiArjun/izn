@extends('layouts.app')

@section('page-title', __('Movement Report'))

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Report') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Movement') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">

            <!-- Filters Card -->
            <div class="card mb-4 shadow-sm border-0 rounded-4">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0 text-primary"><i class="ti ti-filter me-2"></i>{{ __('Filter Movement Records') }}</h5>
                </div>
                <div class="card-body">
                    {{ Form::open(['route' => 'master.report.inspection', 'method' => 'GET']) }}
                    <div class="row align-items-end g-3">

                        {{-- Movement Type Filter --}}
                        <div class="form-group col-md-3">
                            {{ Form::label('movement', __('Movement Type'), ['class' => 'form-label font-bold']) }}
                            {{ Form::select('movement', $movements, request('movement'), [
                                'class' => 'form-select select2',
                                'placeholder' => __('All Movements')
                            ]) }}
                        </div>

                        {{-- Station Filter --}}
                        <div class="form-group col-md-3">
                            {{ Form::label('station', __('Station / Yard'), ['class' => 'form-label font-bold']) }}
                            {{ Form::select('station', $stationsList, request('station'), [
                                'class' => 'form-select select2',
                                'placeholder' => __('All Stations')
                            ]) }}
                        </div>

                        {{-- From Date --}}
                        <div class="form-group col-md-2">
                            {{ Form::label('start_date', __('From Date'), ['class' => 'form-label font-bold']) }}
                            {{ Form::date('start_date', request('start_date'), [
                                'class' => 'form-control'
                            ]) }}
                        </div>

                        {{-- To Date --}}
                        <div class="form-group col-md-2">
                            {{ Form::label('end_date', __('To Date'), ['class' => 'form-label font-bold']) }}
                            {{ Form::date('end_date', request('end_date'), [
                                'class' => 'form-control'
                            ]) }}
                        </div>

                        {{-- Buttons --}}
                        <div class="form-group col-md-auto d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4" style="white-space:nowrap;">
                                <i class="ti ti-search me-1"></i>{{ __('Search') }}
                            </button>
                            <a href="{{ route('master.report.inspection') }}" class="btn btn-light-secondary px-4" style="white-space:nowrap;">
                                <i class="ti ti-refresh me-1"></i>{{ __('Reset') }}
                            </a>
                        </div>

                    </div>
                    {{ Form::close() }}
                </div>
            </div>

            <!-- Report Records Table -->
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary"><i class="ti ti-clipboard-list me-2"></i>{{ __('Movement Report Results') }}</h5>
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
                                    <th>{{ __('Station / Yard') }}</th>
                                    <th>{{ __('Deficiencies') }}</th>
                                    <th>{{ __('Action Taken') }}</th>
                                    <th>{{ __('Submitted By') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inspections as $index => $inspection)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="font-bold text-nowrap">{{ \Carbon\Carbon::parse($inspection->date_of_inspection)->format('d-m-Y') }}</td>
                                        <td><span class="badge bg-light text-dark border">{{ $inspection->designation }}</span></td>
                                        <td>
                                            <span class="badge bg-secondary text-primary font-bold">{{ $inspection->movement }}</span>
                                        </td>
                                        <td>
                                            @if($inspection->type_of_inspection)
                                                <span class="badge bg-info-subtle text-info border border-info">{{ $inspection->type_of_inspection }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="font-bold">{{ $inspection->station_yard ?? 'N/A' }}</td>
                                        <td>
                                            <div class="text-wrap" style="max-width: 200px;">
                                                {{ $inspection->major_deficiencies ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-wrap" style="max-width: 200px;">
                                                {{ $inspection->action_taken ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($inspection->employee)
                                                <span class="font-bold text-dark">{{ $inspection->employee->name }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="cart-action">
                                                <a class="avtar avtar-xs btn-link-success text-success"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-original-title="{{ __('View Details') }}"
                                                   href="{{ route('master.inspection.show', encrypt($inspection->id)) }}">
                                                    <i data-feather="eye"></i>
                                                </a>
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
