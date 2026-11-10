@extends('layouts.app')
@section('page-title')
    {{ __('Joint Signal Sighting') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Master') }}</a></li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Joint Signal Sighting') }}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>{{ __('Joint Signal Sighting List') }}</h5>
                        </div>
                        {{-- @if (Gate::check('create Joint Signal Sighting')) --}}
                        <div class="col-auto">
                            <a href="#" class="btn btn-secondary customModal" data-size="lg"
                                data-url="{{ route('master.joint_signal_sighting.create') }}"
                                data-title="{{ __('Create Joint Signal Sighting') }}">
                                <i class="ti ti-circle-plus align-text-bottom"></i> {{ __('Create Joint Signal Sighting') }}
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
                                    <th>{{ __('Joint Signal Sighting Name') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    {{-- @if (Gate::check('edit Joint Signal Sighting') || Gate::check('delete Joint Signal Sighting')) --}}
                                    <th class="text-end">{{ __('Action') }}</th>
                                    {{-- @endif --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($joint_signal_sightings as $index => $joint_signal_sighting)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $joint_signal_sighting->name }}</td>
                                        <td>
                                            @if ($joint_signal_sighting->status == 1)
                                                <span
                                                    class="badge bg-success-subtle text-success">{{ __('Active') }}</span>
                                            @else
                                                <span
                                                    class="badge bg-danger-subtle text-danger">{{ __('Inactive') }}</span>
                                            @endif
                                        </td>
                                        {{-- @if (Gate::check('edit Joint Signal Sighting') || Gate::check('delete Joint Signal Sighting')) --}}
                                        <td class="text-end">
                                            <div class="cart-action">

                                                {!! Form::open(['method' => 'DELETE', 'route' => ['master.joint_signal_sighting.destroy', encrypt($joint_signal_sighting->id)]]) !!}
                                                <a class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                    data-bs-toggle="tooltip" data-size="lg"
                                                    data-bs-original-title="{{ __('Edit') }}" href="#"
                                                    data-url="{{ route('master.joint_signal_sighting.edit', encrypt($joint_signal_sighting->id)) }}"
                                                    data-title="{{ __('Edit Joint Signal Sighting') }}"> <i
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

