@extends('layouts.app')
@section('page-title')
    {{ __('Load Type') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Master') }}</a></li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Load Type') }}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>{{ __('Load Type List') }}</h5>
                        </div>
                        {{-- @if (Gate::check('create Load Type')) --}}
                        <div class="col-auto">
                            <a href="#" class="btn btn-secondary customModal" data-size="lg"
                                data-url="{{ route('master.load_type.create') }}"
                                data-title="{{ __('Create Load Type') }}">
                                <i class="ti ti-circle-plus align-text-bottom"></i> {{ __('Create Load Type') }}
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
                                    <th>{{ __('Load Type Name') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    {{-- @if (Gate::check('edit Load Type') || Gate::check('delete Load Type')) --}}
                                    <th class="text-end">{{ __('Action') }}</th>
                                    {{-- @endif --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($load_types as $index => $load_type)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $load_type->name }}</td>
                                        <td>
                                            @if ($load_type->status == 1)
                                                <span
                                                    class="badge bg-success-subtle text-success">{{ __('Active') }}</span>
                                            @else
                                                <span
                                                    class="badge bg-danger-subtle text-danger">{{ __('Inactive') }}</span>
                                            @endif
                                        </td>
                                        {{-- @if (Gate::check('edit Load Type') || Gate::check('delete Load Type')) --}}
                                        <td class="text-end">
                                            <div class="cart-action">

                                                {!! Form::open(['method' => 'DELETE', 'route' => ['master.load_type.destroy', encrypt($load_type->id)]]) !!}
                                                <a class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                    data-bs-toggle="tooltip" data-size="lg"
                                                    data-bs-original-title="{{ __('Edit') }}" href="#"
                                                    data-url="{{ route('master.load_type.edit', encrypt($load_type->id)) }}"
                                                    data-title="{{ __('Edit Load Type') }}"> <i
                                                        data-feather="edit"></i></a>


                                                <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                                    data-bs-toggle="tooltip" data-bs-original-title="{{ __('Delete') }}"
                                                    href="#"> <i data-feather="trash-2"></i></a>

                                                {!! Form::close() !!}
                                            </div>
                                        </td>
                                        {{-- @endif --}}

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

