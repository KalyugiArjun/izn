@extends('layouts.app')
@section('page-title')
    {{ __('Running Room Location') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Master') }}</a></li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Running Room Location') }}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>{{ __('Running Room Location List') }}</h5>
                        </div>
                        {{-- @if (Gate::check('create Running Room Location')) --}}
                        <div class="col-auto">
                            <a href="#" class="btn btn-secondary customModal" data-size="lg"
                                data-url="{{ route('master.running_room_location.create') }}"
                                data-title="{{ __('Create Running Room Location') }}">
                                <i class="ti ti-circle-plus align-text-bottom"></i> {{ __('Create Running Room Location') }}
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
                                    <th>{{ __('Running Room Location Code') }}</th>
                                    <th>{{ __('Running Room Location Name') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    {{-- @if (Gate::check('edit Running Room Location') || Gate::check('delete Running Room Location')) --}}
                                    <th class="text-end">{{ __('Action') }}</th>
                                    {{-- @endif --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($running_room_locations as $index => $running_room_location)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if ($running_room_location->code) {{ $running_room_location->code }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $running_room_location->name }}</td>
                                        <td>
                                            @if ($running_room_location->status == 1)
                                                <span
                                                    class="badge bg-success-subtle text-success">{{ __('Active') }}</span>
                                            @else
                                                <span
                                                    class="badge bg-danger-subtle text-danger">{{ __('Inactive') }}</span>
                                            @endif
                                        </td>
                                        {{-- @if (Gate::check('edit station') || Gate::check('delete station')) --}}
                                        <td class="text-end">
                                            <div class="cart-action">

                                                {!! Form::open(['method' => 'DELETE', 'route' => ['master.running_room_location.destroy', encrypt($running_room_location->id)]]) !!}
                                                <a class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                    data-bs-toggle="tooltip" data-size="lg"
                                                    data-bs-original-title="{{ __('Edit') }}" href="#"
                                                    data-url="{{ route('master.running_room_location.edit', encrypt($running_room_location->id)) }}"
                                                    data-title="{{ __('Edit Running Room Location') }}"> <i
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

