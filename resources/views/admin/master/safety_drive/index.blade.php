@extends('layouts.app')
@section('page-title')
    {{ __('Safety Drive') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Master') }}</a></li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Safety Drive') }}</li>
@endsection
@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>{{ __('Safety Drive List') }}</h5>
                        </div>
                        {{-- @if (Gate::check('create Type of Safety Drive')) --}}
                        <div class="col-auto">
                            <a href="#" class="btn btn-secondary customModal" data-size="lg"
                                data-url="{{ route('master.safety_drive.create') }}"
                                data-title="{{ __('Create Safety Drive') }}">
                                <i class="ti ti-circle-plus align-text-bottom"></i> {{ __('Create Safety Drive') }}
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
                                    <th>{{ __('Safety Drive Name') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    {{-- @if (Gate::check('edit Type of Safety Drive') || Gate::check('delete Type of Safety Drive')) --}}
                                    <th class="text-end">{{ __('Action') }}</th>
                                    {{-- @endif --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($safety_drives as $index => $safety_drive)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $safety_drive->name }}</td>
                                        <td>
                                            @if ($safety_drive->status == 1)
                                                <span
                                                    class="badge bg-success-subtle text-success">{{ __('Active') }}</span>
                                            @else
                                                <span
                                                    class="badge bg-danger-subtle text-danger">{{ __('Inactive') }}</span>
                                            @endif
                                        </td>
                                        {{-- @if (Gate::check('edit Type of Safety Drive') || Gate::check('delete Type of Safety Drive')) --}}
                                        <td class="text-end">
                                            <div class="cart-action">

                                                {!! Form::open(['method' => 'DELETE', 'route' => ['master.safety_drive.destroy', encrypt($safety_drive->id)]]) !!}
                                                <a class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                    data-bs-toggle="tooltip" data-size="lg"
                                                    data-bs-original-title="{{ __('Edit') }}" href="#"
                                                    data-url="{{ route('master.safety_drive.edit', encrypt($safety_drive->id)) }}"
                                                    data-title="{{ __('Edit Safety Drive') }}"> <i
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

