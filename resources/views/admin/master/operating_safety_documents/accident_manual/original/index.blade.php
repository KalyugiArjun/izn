@extends('layouts.app')

@section('page-title')
    {{ __('Accident Manual Original') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Master') }}</a>
    </li>
    <li class="breadcrumb-item">{{ __('Operating & Safety Documents') }}</li>
    <li class="breadcrumb-item">{{ __('Accident Manual') }}</li>
    <li class="breadcrumb-item">Original</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">

            <div class="card">

                <div class="card-header">

                    <div class="row align-items-center g-2">

                        <div class="col">
                            <h5>{{ __('Accident Manual Original List') }}</h5>
                        </div>

                        <div class="col-auto d-flex gap-2">

                            <a href="#" class="btn btn-secondary customModal" data-size="md"
                                data-url="{{ route('operating-safety-documents.accident-manual.original.create') }}"
                                data-title="{{ __('Create Accident Manual Original') }}">

                                <i class="ti ti-circle-plus"></i>
                                Add Original PDF

                            </a>

                            <a href="{{ route('operating-safety-documents.accident-manual.correction.index') }}"
                                class="btn btn-light-secondary">

                                <i class="ti ti-file-symlink"></i>
                                Correction Slip PDF

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
                                    <th>Original Name</th>
                                    <th>Original PDF</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>

                            </thead>

                            <tbody>

                                @foreach ($records as $index => $record)
                                    <tr>

                                        <td>{{ $index + 1 }}</td>

                                        <td>{{ $record->original_name }}</td>

                                        <td>

                                            <a href="{{ asset('public/storage/' . $record->original_pdf) }}" target="_blank"
                                                class="btn btn-sm btn-light-danger">

                                                <i class="ti ti-file-type-pdf"></i>
                                                View PDF

                                            </a>

                                        </td>

                                        <td>

                                            @if ($record->status == 1)
                                                <span class="badge bg-success-subtle text-success">Active</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                            @endif

                                        </td>

                                        <td class="text-end">

                                            {!! Form::open([
                                                'method' => 'DELETE',
                                                'route' => ['operating-safety-documents.accident-manual.original.destroy', encrypt($record->id)],
                                            ]) !!}

                                            <a href="#"
                                                class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                data-size="md"
                                                data-url="{{ route('operating-safety-documents.accident-manual.original.edit', encrypt($record->id)) }}"
                                                data-title="{{ __('Edit Accident Manual Original') }}">
                                                <i data-feather="edit"></i>
                                            </a>

                                            <a href="#"
                                                class="avtar avtar-xs btn-link-danger text-danger confirm_dialog">
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
