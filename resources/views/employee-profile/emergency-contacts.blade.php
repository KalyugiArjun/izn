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
    <style>
        .advance-datatable tbody tr {
            background: #f7fbff !important;
        }

        .advance-datatable tbody tr:hover {
            background: #eef6ff !important;
        }

        .advance-datatable tbody td {
            vertical-align: middle !important;
            border-color: #dcecfa !important;
        }

        .ec-avatar {
            width: 38px;
            height: 38px;
            min-width: 38px;
            border-radius: 50%;
            background: linear-gradient(145deg, #ff8a80, #ff5252);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-right: 10px;
        }

        .ec-name {
            color: #0b3272;
            font-weight: 600;
        }

        .ec-relation {
            display: inline-block;
            padding: 3px 12px;
            background: #eaf2ff;
            color: #3774bd;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .ec-phone {
            color: #073b82;
            font-weight: 600;
            text-decoration: none;
        }

        .ec-phone:hover {
            text-decoration: underline;
        }
    </style>

    <div class="row mt-3">
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
                            <a href="{{ route('employee-profile.index') }}" class="btn btn-sm btn-secondary">
                                {{ __('Back to Profile') }}
                            </a>
                        </div>



                    </div>

                </div>

                <div class="card-body">

                    @if ($employee->emergencyContacts->count())
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

                                    @foreach ($employee->emergencyContacts as $index => $record)
                                        <tr>

                                            <td>{{ $index + 1 }}</td>

                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="ec-avatar">
                                                        <i class="ti ti-user"></i>
                                                    </div>
                                                    <span class="ec-name">
                                                        {{ $record->name }}
                                                    </span>
                                                </div>
                                            </td>

                                            <td>{{ $employee->department->name ?? '-' }}</td>

                                            <td>{{ $employee->designation->name ?? '-' }}</td>

                                            <td>
                                                <a href="tel:{{ $record->mobile_number }}" class="ec-phone">
                                                    {{ $record->mobile_number }}
                                                </a>
                                            </td>

                                            <td>{{ $record->email ?? '-' }}</td>

                                            <td>{{ $record->address ?? '-' }}</td>

                                            <td>
                                                <span class="ec-relation">
                                                    @if ($record->relation === 'Other')
                                                        {{ $record->other_relation }}
                                                    @else
                                                        {{ $record->relation }}
                                                    @endif
                                                </span>
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
                    @else
                        <div class="text-center p-4 text-muted">
                            {{ __('No emergency contact added yet.') }}
                        </div>
                    @endif

                </div>

            </div>

        </div>
    </div>
@endsection
