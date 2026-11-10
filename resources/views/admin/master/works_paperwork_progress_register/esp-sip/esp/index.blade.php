@extends('layouts.app')

@section('page-title')
    {{ __('ESP') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Master') }}</a>
    </li>

    <li class="breadcrumb-item">
        {{ __('PH 16 Works') }}
    </li>

    <li class="breadcrumb-item" aria-current="page">
        {{ __('ESP') }}
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">

            <div class="card">

                <div class="card-header">
                    <div class="row align-items-center g-2">

                        <div class="col">
                            <h5>{{ __('ESP List') }}</h5>
                        </div>

                        <div class="col-auto">
                            <a href="#" class="btn btn-secondary customModal" data-size="lg"
                                data-url="{{ route('works-paperwork-progress-register.esp-sip.esp.create') }}"
                                data-title="{{ __('Create ESP') }}">

                                <i class="ti ti-circle-plus align-text-bottom"></i>
                                {{ __('Create ESP') }}

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
                                    <th>{{ __('Status / Remark') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Supporting Document') }}</th>
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
                                            {{ $record->milestone ?? '-' }}
                                        </td>

                                        <td>
                                            @if ($record->is_completed)
                                                <span class="badge bg-success">
                                                    <i class="ti ti-check me-1"></i>{{ __('Completed') }}
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark font-semibold">
                                                    {{ $record->remark ?? __('Incomplete') }}
                                                </span>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($record->is_completed && $record->date)
                                                {{ $record->date->format('d-m-Y') }}
                                            @else
                                                <span class="text-muted">-</span>
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

                                        <td class="text-end text-nowrap">
                                            {!! Form::open([
                                                'method' => 'DELETE',
                                                'route' => ['works-paperwork-progress-register.esp-sip.esp.destroy', encrypt($record->id)],
                                                'class' => 'd-inline m-0 p-0',
                                                'id' => 'delete-form-' . $record->id,
                                            ]) !!}

                                            <a href="#"
                                                class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                data-bs-toggle="tooltip" data-size="lg"
                                                data-url="{{ route('works-paperwork-progress-register.esp-sip.esp.edit', encrypt($record->id)) }}"
                                                data-title="{{ __('Edit ESP') }}"
                                                title="{{ __('Edit') }}">
                                                <i data-feather="edit"></i>
                                            </a>

                                            <a href="#"
                                                class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                                data-bs-toggle="tooltip"
                                                data-bs-original-title="{{ __('Delete') }}"
                                                title="{{ __('Delete') }}">
                                                <i data-feather="trash-2"></i>
                                            </a>

                                            {!! Form::close() !!}
                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        {{ $records->links() }}
                    </div>

                </div>

            </div>

        </div>
    </div>
@endsection
