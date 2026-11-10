
@extends('layouts.app')

@section('page-title', __('PH16 TI Authority'))

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">
        {{ __('PH16 TI Authority') }}
    </li>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">

            {{-- Header --}}
            <div class="card-header">
                <div class="row align-items-center g-2">
                    <div class="col">
                        <h5>{{ __('PH16 TI Authority List') }}</h5>
                    </div>

                    <div class="col-auto">
                        <a href="#"
                           class="btn btn-secondary customModal"
                           data-size="md"
                           data-url="{{ route('authority.ph16-ti.create') }}"
                           data-title="{{ __('Create PH16 TI Authority') }}">
                            <i class="ti ti-circle-plus align-text-bottom"></i>
                            {{ __('Create PH16 TI') }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- Body --}}
            <div class="card-body">

                {{-- Authority List --}}
                <div class="table-responsive">
                    <table class="table table-hover advance-datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Department') }}</th>
                                <th>{{ __('Designation') }}</th>
                                <th class="text-end">{{ __('Action') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($ph16Tis as $index => $ph16Ti)
                                <tr>
                                    <td>{{ $index + 1 }}</td>

                                    <td>
                                        {{ $ph16Ti->department->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $ph16Ti->designation->name ?? '-' }}
                                    </td>

                                    <td class="text-end">
                                        <div class="cart-action">

                                            {!! Form::open([
                                                'method' => 'DELETE',
                                                'route' => [
                                                    'authority.ph16-ti.destroy',
                                                    encrypt($ph16Ti->id)
                                                ]
                                            ]) !!}

                                            {{-- Edit --}}
                                            <a class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                               data-bs-toggle="tooltip"
                                               data-size="lg"
                                               data-bs-original-title="{{ __('Edit') }}"
                                               href="#"
                                               data-url="{{ route('authority.ph16-ti.edit', encrypt($ph16Ti->id)) }}"
                                               data-title="{{ __('Edit PH16 TI') }}">
                                                <i data-feather="edit"></i>
                                            </a>

                                            {{-- Delete --}}
                                            <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                               data-bs-toggle="tooltip"
                                               data-bs-original-title="{{ __('Delete') }}"
                                               href="#">
                                                <i data-feather="trash-2"></i>
                                            </a>

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
