@extends('layouts.app')
@section('page-title')
    {{ __('Diesel Lobby Location') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Master') }}</a></li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Diesel Lobby Location') }}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>{{ __('Diesel Lobby Location List') }}</h5>
                        </div>
                        {{-- @if (Gate::check('create Diesel Lobby Location')) --}}
                        <div class="col-auto">
                            <a href="#" class="btn btn-secondary customModal" data-size="lg"
                                data-url="{{ route('master.diesel_lobby.create') }}" data-title="{{ __('Create Diesel Lobby Location') }}">
                                <i class="ti ti-circle-plus align-text-bottom"></i> {{ __('Create Diesel Lobby Location') }}
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
                                    <th>{{ __('Diesel Lobby Location Code') }}</th>
                                    <th>{{ __('Diesel Lobby Location Name') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    {{-- @if (Gate::check('edit Diesel Lobby Location') || Gate::check('delete Diesel Lobby Location')) --}}
                                    <th class="text-end">{{ __('Action') }}</th>
                                    {{-- @endif --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach  ($diesellobbies as $index => $diesellobby)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if ($diesellobby->code)
                                                {{ $diesellobby->code }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $diesellobby->name }}</td>
                                        <td>
                                            @if ($diesellobby->status == 1)
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
                                                        data-url="{{ route('master.diesel_lobby.edit', encrypt($diesellobby->id)) }}"
                                                        data-size="lg" data-title="{{ __('Edit Station') }}">
                                                        <i class="ti ti-edit"></i> {{ __('Edit') }}
                                                    </a>


                                                    <a href="{{ route('master.diesel_lobby.destroy', encrypt($diesellobby->id)) }}"
                                                        class="dropdown-item"
                                                        onclick="return confirm('Are you sure you want to delete this?')">
                                                        <i class="ti ti-trash"></i> {{ __('Delete') }}
                                                    </a>

                                                </div>
                                            </div> --}}
                                              <div class="cart-action">


                                                {!! Form::open(['method' => 'DELETE', 'route' => ['master.diesel_lobby.destroy', encrypt($diesellobby->id)]]) !!}
                                                <a class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                    data-bs-toggle="tooltip" data-size="lg"
                                                    data-bs-original-title="{{ __('Edit') }}" href="#"
                                                      data-url="{{ route('master.diesel_lobby.edit', encrypt($diesellobby->id)) }}"
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

