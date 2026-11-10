@extends('layouts.app')

@section('page-title')
    {{ __('Execution End') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Master') }}</a>
    </li>

    <li class="breadcrumb-item">
        {{ __('PH 16 Works') }}
    </li>

    <li class="breadcrumb-item" aria-current="page">
        {{ __('Execution End') }}
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">

            <div class="card">

                <div class="card-header">

                    <div class="row align-items-center g-2">

                        <div class="col">
                            <h5>{{ __('Execution End') }}</h5>
                        </div>

                        <div class="col-auto">

                            <a href="#" class="btn btn-secondary customModal" data-size="lg"
                                data-url="{{ route('works-paperwork-progress-register.executionend.create') }}"
                                data-title="{{ __('Create Execution End') }}">

                                <i class="ti ti-circle-plus align-text-bottom"></i>

                                {{ __('Create Execution End') }}

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

                                    <th>{{ __('Work Description') }}</th>
                                    <th>{{ __('Milestone') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Days Taken') }}</th>
                                    <th>{{ __('Supporting Document') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>

                                </tr>

                            </thead>

                            <tbody>

                                @foreach ($records as $index => $record)
                                    <tr>

                                        <td>
                                            {{ $index + 1 }}
                                        </td>

                                        <td>
                                            {{ $record->identifyNewWork->work_description_identification ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $record->milestone ?? '-' }}
                                        </td>

                                        <td>
                                            @if ($record->date)
                                                {{ $record->date->format('d-m-Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <td>
                                            @if ($record->days_taken !== null)
                                                {{ $record->days_taken }}
                                                days from previous stage
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <td>

                                            @if ($record->supporting_document)
                                                <a href="{{ asset('public/storage/' . $record->supporting_document) }}"
                                                    target="_blank" class="btn btn-sm btn-danger">
                                                    <i class="ti ti-file-type-pdf"></i>
                                                    {{ __('View PDF') }}
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif

                                        </td>

                                        <td class="text-end">

                                            {!! Form::open([
                                                'method' => 'DELETE',
                                                'route' => ['works-paperwork-progress-register.executionend.destroy', encrypt($record->id)],
                                            ]) !!}

                                            <a href="#"
                                                class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                data-bs-toggle="tooltip" data-size="lg"
                                                data-url="{{ route('works-paperwork-progress-register.executionend.edit', encrypt($record->id)) }}"
                                                data-title="{{ __('Edit Execution End') }}">

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
