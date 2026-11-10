@extends('layouts.app')

@section('page-title')
    {{ __('Assigned Station(s)') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Master') }}</a></li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Assigned Station') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>{{ __('Assigned Station List') }}</h5>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('master.working-area.create') }}" class="btn btn-secondary">
                                <i class="ti ti-circle-plus align-text-bottom"></i> {{ __('Create Assigned Station') }}
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
                                    <th>{{ __('Division') }}</th>
                                    <th>{{ __('Department') }}</th>
                                    <th>{{ __('Designation') }}</th>
                                    <th>{{ __('Mobile') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Stations') }}</th>
                                    <th>{{ __('Head Station') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach  ($workingAreas as $index => $area)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $area->division->name ?? '-' }}</td>
                                        <td>{{ $area->department->name ?? '-' }}</td>
                                        <td>{{ $area->designation->name ?? '-' }}</td>
                                        <td>{{ $area->mobile ?? '-' }}</td>
                                        <td>{{ $area->email ?? '-' }}</td>
                                        <td>
                                            @foreach ($area->stations as $station)
                                                {{ $station->name }}
                                            @endforeach
                                        </td>
                                        <td>{{ $area->head_station_name ?? '-' }}</td>
                                        <td class="text-end">
                                            {{-- <div class="dropdown">
                                            <a class="dropdown-toggle text-primary opacity-50 arrow-none" href="#"
                                                data-bs-toggle="dropdown">
                                                <i class="ti ti-dots f-16"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="{{ route('master.working-area.edit', encrypt($area->id)) }}">
                                                    <i class="ti ti-edit"></i> {{ __('Edit') }}
                                                </a>
                                                <a href="{{ route('master.working-area.destroy', encrypt($area->id)) }}"
                                                    class="dropdown-item"
                                                    onclick="return confirm('Are you sure you want to delete this?')">
                                                    <i class="ti ti-trash"></i> {{ __('Delete') }}
                                                </a>
                                            </div>
                                        </div> --}}
                                            <div class="cart-action">


                                                {!! Form::open(['method' => 'DELETE', 'route' => ['master.working-area.destroy', encrypt($area->id)]]) !!}


                                                <a class="avtar avtar-xs btn-link-secondary text-secondary"
                                                    href="{{ route('master.working-area.edit', encrypt($area->id)) }}">
                                                   <i data-feather="edit"></i>
                                                </a>
                                                <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                                    data-bs-toggle="tooltip" data-bs-original-title="{{ __('Detete') }}"
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

