@extends('layouts.app')

@section('page-title')
    {{ __('Variation') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Master') }}</a>
    </li>

    <li class="breadcrumb-item">
        {{ __('PH 16 Works') }}
    </li>

    <li class="breadcrumb-item" aria-current="page">
        {{ __('Variation') }}
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">

            <div class="card">

                <div class="card-header">
                    <div class="row align-items-center g-2">

                        <div class="col">
                            <h5>{{ __('Variation') }}</h5>
                        </div>

                        <div class="col-auto">
                            <a href="#" class="btn btn-secondary customModal" data-size="lg"
                                data-url="{{ route('works-paperwork-progress-register.variation.create') }}"
                                data-title="{{ __('Create Variation') }}">

                                <i class="ti ti-circle-plus align-text-bottom"></i>
                                {{ __('Create Variation') }}

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
                                    <th>{{ __('Date of Variation') }}</th>
                                    <th>{{ __('Nature of Variation / Revised Scope') }}</th>
                                    <th>{{ __('Revised TDC') }}</th>
                                    <th>{{ __('File') }}</th>
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
                                            @if ($record->date_of_variation)
                                                {{ $record->date_of_variation->format('d-m-Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <td>
                                            {{ $record->nature_of_variation ?? '-' }}
                                        </td>

                                        <td>
                                            @if ($record->revised_tdc)
                                                {{ $record->revised_tdc->format('d-m-Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <td>
                                            @if ($record->file)
                                                <a href="{{ asset('public/storage/' . $record->file) }}" target="_blank"
                                                    class="btn btn-sm btn-danger">

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
                                                'route' => ['works-paperwork-progress-register.variation.destroy', encrypt($record->id)],
                                            ]) !!}

                                            <a href="#"
                                                class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                data-bs-toggle="tooltip" data-size="lg"
                                                data-url="{{ route('works-paperwork-progress-register.variation.edit', encrypt($record->id)) }}"
                                                data-title="{{ __('Edit Variation') }}">

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
