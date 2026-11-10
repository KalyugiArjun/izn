@extends('layouts.app')

@section('page-title')
    {{ __('Station Working Rule Correction') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Master') }}</a>
    </li>
    <li class="breadcrumb-item">
        {{ __('Operating & Safety Documents') }}
    </li>
    <li class="breadcrumb-item">
        {{ __('Station Working Rule') }}
    </li>
    <li class="breadcrumb-item" aria-current="page">
        {{ __('Correction') }}
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header">

                    <div class="row align-items-center g-2">

                        <div class="col">

                            <h5>
                                {{ __('Station Working Rule Correction List') }}
                            </h5>

                        </div>

                        <div class="col-auto d-flex gap-2">

                            <a href="#" class="btn btn-secondary customModal" data-size="lg"
                                data-url="{{ route('operating-safety-documents.station-working-rule.correction.create') }}"
                                data-title="{{ __('Create Station Working Rule Correction') }}">

                                <i class="ti ti-circle-plus align-text-bottom"></i>

                                {{ __('Add Correction Slip PDF') }}

                            </a>

                            <a href="{{ route('operating-safety-documents.station-working-rule.original.index') }}"
                                class="btn btn-light-secondary">

                                <i class="ti ti-file"></i>

                                {{ __('Original PDF') }}

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
                                    <th>{{ __('Station Name') }}</th>
                                    <th>{{ __('Original Name') }}</th>
                                    <th>{{ __('Original PDF') }}</th>
                                    <th>{{ __('Correction Slip No.') }}</th>
                                    <th>{{ __('Correction Slip PDF') }}</th>
                                    <th>{{ __('Issue Date') }}</th>
                                    <th>{{ __('Imposition Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>
                                </tr>

                            </thead>

                            <tbody>

                                @foreach ($records as $index => $record)
                                    <tr>

                                        <td>{{ $index + 1 }}</td>

                                        <td>
                                            {{ $record->original->station->name ?? '-' }}

                                            @if (isset($record->original->station->code))
                                                - {{ $record->original->station->code }}
                                            @endif
                                        </td>

                                        <td>
                                            {{ $record->original->original_name ?? '-' }}
                                        </td>

                                        <td>

                                            @if ($record->original && $record->original->original_pdf)
                                                <a href="{{ asset('public/storage/' . $record->original->original_pdf) }}"
                                                    target="_blank" class="btn btn-sm btn-light-danger">

                                                    <i class="ti ti-file-type-pdf"></i>
                                                    View PDF

                                                </a>
                                            @else
                                                -
                                            @endif

                                        </td>

                                        <td>
                                            {{ $record->correction_slip_no }}
                                        </td>

                                        <td>
                                            {{ $record->issue_date ? \Carbon\Carbon::parse($record->issue_date)->format('d-m-Y') : '-' }}
                                        </td>

                                        <td>
                                            {{ $record->imposition_date ? \Carbon\Carbon::parse($record->imposition_date)->format('d-m-Y') : '-' }}
                                        </td>

                                        <td>

                                            @if ($record->correction_slip_pdf)
                                                <a href="{{ asset('public/storage/' . $record->correction_slip_pdf) }}"
                                                    target="_blank" class="btn btn-sm btn-light-danger">

                                                    <i class="ti ti-file-type-pdf"></i>
                                                    View PDF

                                                </a>
                                            @else
                                                -
                                            @endif

                                        </td>

                                        <td>

                                            @if ($record->status == 1)
                                                <span class="badge bg-success-subtle text-success">
                                                    Active
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger">
                                                    Inactive
                                                </span>
                                            @endif

                                        </td>

                                        <td class="text-end">

                                            {!! Form::open([
                                                'method' => 'DELETE',
                                                'route' => ['operating-safety-documents.station-working-rule.correction.destroy', encrypt($record->id)],
                                            ]) !!}

                                            <a href="#"
                                                class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                data-bs-toggle="tooltip" data-size="lg"
                                                data-url="{{ route('operating-safety-documents.station-working-rule.correction.edit', encrypt($record->id)) }}"
                                                data-title="{{ __('Edit Station Working Rule Correction') }}">
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
