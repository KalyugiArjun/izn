@extends('layouts.app')

@section('page-title')
    {{ __('Traffic Inspector Inspection Notes') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Master') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">
        {{ __('Traffic Inspector Inspection Notes') }}
    </li>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header">
                <div class="row align-items-center g-2">
                    <div class="col">
                        <h5>{{ __('Traffic Inspector Inspection Notes List') }}</h5>
                    </div>

                    <div class="col-auto">
                        <a href="#"
                           class="btn btn-secondary customModal"
                           data-size="lg"
                           data-url="{{ route('inspection-notes.traffic-inspector.create') }}"
                           data-title="{{ __('Create Traffic Inspector Inspection Note') }}">
                            <i class="ti ti-circle-plus align-text-bottom"></i>
                            {{ __('Create Traffic Inspector Inspection Note') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="dt-responsive table-responsive">
                    <table class="table table-hover advance-datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Inspecting Traffic Inspector') }}</th>
                                <th>{{ __('Type of Inspection') }}</th>
                                <th>{{ __('Place of Inspection') }}</th>
                                <th>{{ __('Date of Inspection') }}</th>
                                <th>{{ __('PDF') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="text-end">{{ __('Action') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($notes as $index => $note)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $note->inspecting_traffic_inspector }}</td>
                                <td>{{ $note->inspectionType->name ?? '-' }}</td>
                                <td>{{ $note->place_of_inspection }}</td>
                                <td>{{ \Carbon\Carbon::parse($note->date_of_inspection)->format('d-m-Y') }}</td>

                                <td>
                                    @if($note->inspection_note_pdf)
                                        <a href="{{ asset('public/storage/' . $note->inspection_note_pdf) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-light-danger">
                                            <i class="ti ti-file-type-pdf"></i>
                                            {{ __('View PDF') }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    @if($note->status == 1)
                                        <span class="badge bg-success-subtle text-success">
                                            {{ __('Active') }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">
                                            {{ __('Inactive') }}
                                        </span>
                                    @endif
                                </td>

                                <td class="text-end">

                                    {!! Form::open([
                                        'method' => 'DELETE',
                                        'route' => [
                                            'inspection-notes.traffic-inspector.destroy',
                                            encrypt($note->id)
                                        ]
                                    ]) !!}

                                    <a href="#"
                                       class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                       data-size="lg"
                                       data-bs-toggle="tooltip"
                                       data-url="{{ route('inspection-notes.traffic-inspector.edit', encrypt($note->id)) }}"
                                       data-title="{{ __('Edit Traffic Inspector Inspection Note') }}">
                                        <i data-feather="edit"></i>
                                    </a>

                                    <a href="#"
                                       class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                       data-bs-toggle="tooltip"
                                       data-bs-original-title="{{ __('Delete') }}">
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