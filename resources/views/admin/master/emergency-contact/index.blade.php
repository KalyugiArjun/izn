@extends('layouts.app')

@section('page-title')
    {{ __('Emergency Contact') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Master') }}</a>
    </li>

    <li class="breadcrumb-item" aria-current="page">
        {{ __('Emergency Contact') }}
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">

            <div class="card">

                <div class="card-header">

                    <div class="row align-items-center g-2">

                        <div class="col">
                            <h5>{{ __('Emergency Contact') }}</h5>
                        </div>

                        <div class="col-auto">

                            <a href="#" class="btn btn-secondary customModal" data-size="lg"
                                data-url="{{ route('emergency-contact.create') }}"
                                data-title="{{ __('Create Emergency Contact') }}">
                                <i class="ti ti-circle-plus align-text-bottom"></i>
                                {{ __('Create Emergency Contact') }}
                            </a>

                        </div>

                    </div>

                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-hover advance-datatable">

                            <thead>

                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Department') }}</th>
                                    <th>{{ __('Designation') }}</th>
                                    <th>{{ __('Mobile Number') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Address') }}</th>
                                    <th>{{ __('Relation') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>
                                </tr>

                            </thead>

                            <tbody>

                                @foreach ($records as $index => $record)
                                    <tr>

                                        <td>{{ $index + 1 }}</td>

                                        <td>{{ $record->employee->name ?? $record->name }}</td>

                                        <td>{{ $record->employee->department->name ?? '-' }}</td>

                                        <td>{{ $record->employee->designation->name ?? '-' }}</td>

                                        <td>{{ $record->mobile_number }}</td>

                                        <td>{{ $record->email ?? '-' }}</td>

                                        <td>{{ $record->address ?? '-' }}</td>

                                        <td>
                                            @if ($record->relation === 'Other')
                                                {{ $record->other_relation }}
                                            @else
                                                {{ $record->relation }}
                                            @endif
                                        </td>

                                        <td class="text-end">

                                            {!! Form::open([
                                                'method' => 'DELETE',
                                                'route' => ['emergency-contact.destroy', encrypt($record->id)],
                                            ]) !!}

                                            <a href="#"
                                                class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                data-bs-toggle="tooltip" data-size="lg"
                                                data-url="{{ route('emergency-contact.edit', encrypt($record->id)) }}"
                                                data-title="{{ __('Edit Emergency Contact') }}">

                                                <i data-feather="edit"></i>

                                            </a>

                                            <a href="#"
                                                class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                                data-bs-toggle="tooltip" data-bs-original-title="{{ __('Delete') }}">

                                                <i data-feather="trash-2"></i>

                                            </a>

                                            {!! Form::close() !!}

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