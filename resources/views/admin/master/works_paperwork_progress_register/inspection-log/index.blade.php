@extends('layouts.app')

@section('page-title')
    {{ __('Inspection Log') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Master') }}</a>
    </li>

    <li class="breadcrumb-item">
        {{ __('PH 16 Works') }}
    </li>

    <li class="breadcrumb-item" aria-current="page">
        {{ __('Inspection Log') }}
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">

            <div class="card">

                <div class="card-header">
                    <div class="row align-items-center g-2">

                        <div class="col">
                            <h5>{{ __('Inspection Log') }}</h5>
                        </div>

                        <div class="col-auto">
                            <a href="#" class="btn btn-secondary customModal" data-size="lg"
                                data-url="{{ route('works-paperwork-progress-register.inspection-log.create') }}"
                                data-title="{{ __('Create Inspection Log') }}">

                                <i class="ti ti-circle-plus align-text-bottom"></i>
                                {{ __('Create Inspection Log') }}

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
                                    <th>{{ __('Date of Inspection') }}</th>
                                    <th>{{ __('Department Inspected') }}</th>
                                    <th>{{ __('Inspecting Authority') }}</th>
                                    <th>{{ __('Name of Inspecting Official') }}</th>
                                    <th>{{ __('Work Done Detail') }}</th>
                                    <th>{{ __('Photos') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach ($records as $index => $record)
                                    <tr>

                                        <td>
                                            {{ $records->firstItem() + $index }}
                                        </td>

                                        <td>
                                            {{ $record->identifyNewWork->work_description_identification ?? '-' }}
                                        </td>

                                        <td>
                                            @if ($record->date_of_inspection)
                                                {{ $record->date_of_inspection->format('d-m-Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <td>
                                            {{ $record->department_inspected ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $record->inspecting_authority ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $record->name_of_inspecting_official ?? '-' }}
                                        </td>

                                        <td>
                                            {{ \Illuminate\Support\Str::limit($record->work_done_detail, 60) }}
                                        </td>

                                        <td>
                                            @if ($record->photos->count())
                                                <a href="#" class="btn btn-sm btn-secondary customModal"
                                                    data-size="lg"
                                                    data-url="{{ route('works-paperwork-progress-register.inspection-log.edit', encrypt($record->id)) }}"
                                                    data-title="{{ __('Edit Inspection Log') }}">

                                                    <i class="ti ti-photo"></i>
                                                    {{ $record->photos->count() }} {{ __('Photo(s)') }}

                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>

                                        <td class="text-end">

                                            {!! Form::open([
                                                'method' => 'DELETE',
                                                'route' => ['works-paperwork-progress-register.inspection-log.destroy', encrypt($record->id)],
                                            ]) !!}

                                            <a href="#"
                                                class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                data-bs-toggle="tooltip" data-size="lg"
                                                data-url="{{ route('works-paperwork-progress-register.inspection-log.edit', encrypt($record->id)) }}"
                                                data-title="{{ __('Edit Inspection Log') }}">

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

                    @if ($records->hasPages())
                        <div class="mt-3">
                            {{ $records->links() }}
                        </div>
                    @endif

                </div>

            </div>

        </div>
    </div>
@endsection
