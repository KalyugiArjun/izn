@extends('layouts.app')
@section('page-title')
    {{ __('Designation') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Master') }}</a></li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Designation') }}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>{{ __('Designation List') }}</h5>
                        </div>
                        {{-- @if (Gate::check('create designation')) --}}
                        <div class="col-auto">
                            <a href="#" class="btn btn-secondary customModal" data-size="md"
                                data-url="{{ route('master.designation.create') }}"
                                data-title="{{ __('Create Designation') }}">
                                <i class="ti ti-circle-plus align-text-bottom"></i> {{ __('Create Designation') }}
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
                                    <th>{{ __('Division') }}</th>
                                    <th>{{ __('Department') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Position') }}</th>
                                    {{-- <th>{{ __('Mobile') }}</th>
                                    <th>{{ __('Email') }}</th> --}}
                                    <th>{{ __('Status') }}</th>
                                    {{-- @if (Gate::check('edit designation') || Gate::check('delete designation')) --}}
                                    <th class="text-end">{{ __('Action') }}</th>
                                    {{-- @endif --}}
                                </tr>
                            </thead>
                            <tbody>
                               @foreach  ($designations as $index => $designation)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            {{ $designation->division->name ?? '-' }}
                                        </td>
                                        <td>
                                            {{ $designation->department->name ?? '-' }}
                                        </td>
                                        <td>{{ $designation->name }}</td>
                                        <td>{{ $designation->position ?? '-' }}</td>
                                        {{-- <td>{{ $designation->mobile ?? '-' }}</td>
                                        <td>{{ $designation->email ?? '-' }}</td> --}}
                                        <td>
                                            @if ($designation->status == 1)
                                                <span
                                                    class="badge bg-success-subtle text-success">{{ __('Active') }}</span>
                                            @else
                                                <span
                                                    class="badge bg-danger-subtle text-danger">{{ __('Inactive') }}</span>
                                            @endif
                                        </td>
                                        {{-- @if (Gate::check('edit designation') || Gate::check('delete designation')) --}}
                                        <td class="text-end">
                                            <div class="cart-action">
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['master.designation.destroy', encrypt($designation->id)]]) !!}
                                                <a class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                    data-bs-toggle="tooltip" data-size="lg"
                                                    data-bs-original-title="{{ __('Edit') }}" href="#"
                                                    data-url="{{ route('master.designation.edit', encrypt($designation->id)) }}"
                                                    data-title="{{ __('Edit Designation') }}"> <i data-feather="edit"></i></a>


                                                <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                                    data-bs-toggle="tooltip" data-bs-original-title="{{ __('Detete') }}"
                                                    href="#"> <i data-feather="trash-2"></i></a>

                                                {!! Form::close() !!}
                                            </div>
                                            {{-- <div class="dropdown">
                                                <a class="dropdown-toggle text-primary opacity-50 arrow-none" href="#"
                                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="ti ti-dots f-16"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">

                                                    <a class="dropdown-item customModal" href="#"
                                                        data-url="{{ route('master.designation.edit', encrypt($designation->id)) }}"
                                                        data-title="{{ __('Edit Designation') }}">
                                                        <i class="ti ti-edit"></i> {{ __('Edit') }}
                                                    </a>


                                                    <a href="{{ route('master.designation.destroy', encrypt($designation->id)) }}"
                                                        class="dropdown-item"
                                                        onclick="return confirm('Are you sure you want to delete this?')">
                                                        <i class="ti ti-trash"></i> {{ __('Delete') }}
                                                    </a>

                                                </div>
                                            </div> --}}
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

