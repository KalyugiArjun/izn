@extends('layouts.app')
@section('page-title')
    {{ __('Station/Working Place') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Master') }}</a></li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Station/Working Place') }}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>{{ __('Station/Working Place List') }}</h5>
                        </div>
                        {{-- @if (Gate::check('create Station/Working Place')) --}}
                        <div class="col-auto">
                            <a href="#" class="btn btn-secondary customModal" data-size="lg"
                                data-url="{{ route('master.station.create') }}" data-title="{{ __('Create Station/Working Place') }}">
                                <i class="ti ti-circle-plus align-text-bottom"></i> {{ __('Create Station/Working Place') }}
                            </a>
                        </div>
                        {{-- @endif --}}
                    </div>
                </div>
                <div class="card-body">
                   <div class="dt-responsive table-responsive">
                        <table class="table table-hover advance-datatable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Station/Working Place Code') }}</th>
                                    <th>{{ __('Station/Working Place Name') }}</th>
                                    {{-- <th>{{ __('City') }}</th>
                                    <th>{{ __('State') }}</th>
                                    <th>{{ __('Pincode') }}</th> --}}
                                    <th>{{ __('Status') }}</th>
                                    {{-- @if (Gate::check('edit Station/Working Place') || Gate::check('delete Station/Working Place')) --}}
                                    <th class="text-end">{{ __('Action') }}</th>
                                    {{-- @endif --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach  ($stations as $index => $station)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if ($station->code)
                                                {{ $station->code }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $station->name }}</td>
                                        {{-- <td>{{ $station->city ?? '-' }}</td>
                                        <td>{{ $station->state ?? '-' }}</td>
                                        <td>{{ $station->pincode ?? '-' }}</td> --}}
                                        <td>
                                            @if ($station->status == 1)
                                                <span
                                                    class="badge bg-success-subtle text-success">{{ __('Active') }}</span>
                                            @else
                                                <span
                                                    class="badge bg-danger-subtle text-danger">{{ __('Inactive') }}</span>
                                            @endif
                                        </td>
                                        {{-- @if (Gate::check('edit station') || Gate::check('delete station')) --}}
                                        <td class="text-end">
                                            {{-- <div class="dropdown">
                                                <a class="dropdown-toggle text-primary opacity-50 arrow-none" href="#"
                                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="ti ti-dots f-16"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">

                                                    <a class="dropdown-item customModal" href="#"
                                                        data-url="{{ route('master.station.edit', encrypt($station->id)) }}"
                                                        data-size="lg" data-title="{{ __('Edit Station') }}">
                                                        <i class="ti ti-edit"></i> {{ __('Edit') }}
                                                    </a>


                                                    <a href="{{ route('master.station.destroy', encrypt($station->id)) }}"
                                                        class="dropdown-item"
                                                        onclick="return confirm('Are you sure you want to delete this?')">
                                                        <i class="ti ti-trash"></i> {{ __('Delete') }}
                                                    </a>

                                                </div>
                                            </div> --}}
                                              <div class="cart-action">


                                                {!! Form::open(['method' => 'DELETE', 'route' => ['master.station.destroy', encrypt($station->id)]]) !!}
                                                <a class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                    data-bs-toggle="tooltip" data-size="lg"
                                                    data-bs-original-title="{{ __('Edit') }}" href="#"
                                                      data-url="{{ route('master.station.edit', encrypt($station->id)) }}"
                                                    data-title="{{ __('Edit Station/Working Place') }}"> <i data-feather="edit"></i></a>


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

