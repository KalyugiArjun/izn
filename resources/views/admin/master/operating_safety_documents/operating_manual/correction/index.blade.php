@extends('layouts.app')

@section('page-title')
    {{ __('Operating Manual Correction') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Master') }}</a>
    </li>
    <li class="breadcrumb-item">{{ __('Operating & Safety Documents') }}</li>
    <li class="breadcrumb-item">{{ __('Operating Manual') }}</li>
    <li class="breadcrumb-item">Correction</li>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12">

        <div class="card">

            <div class="card-header">

                <div class="row align-items-center g-2">

                    <div class="col">
                        <h5>{{ __('Operating Manual Correction List') }}</h5>
                    </div>

                    <div class="col-auto d-flex gap-2">

                        <a href="#"
                           class="btn btn-secondary customModal"
                           data-size="lg"
                           data-url="{{ route('operating-safety-documents.operating-manual.correction.create') }}"
                           data-title="{{ __('Create Operating Manual Correction') }}">
                            <i class="ti ti-circle-plus"></i>
                            Add Correction PDF
                        </a>

                        <a href="{{ route('operating-safety-documents.operating-manual.original.index') }}"
                           class="btn btn-light-secondary">
                            <i class="ti ti-file"></i>
                            Original PDF
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
                                <th>Correction Slip No.</th>
                                <th>Correction Slip Date</th>
                                <th>Correction Slip PDF</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>

                        </thead>

                        <tbody>

                        @foreach($records as $index => $record)

                            <tr>

                                <td>{{ $index + 1 }}</td>

                                <td>{{ $record->original->original_name ?? '-' }}</td>

                                <td>

                                    @if($record->original && $record->original->original_pdf)

                                        <a href="{{ asset('public/storage/'.$record->original->original_pdf) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-light-danger">
                                            <i class="ti ti-file-type-pdf"></i>
                                            View PDF
                                        </a>

                                    @endif

                                </td>

                                <td>{{ $record->correction_slip_no }}</td>

                                <td>
                                    {{ \Carbon\Carbon::parse($record->correction_slip_date)->format('d-m-Y') }}
                                </td>

                                <td>

                                    <a href="{{ asset('public/storage/'.$record->correction_slip_pdf) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-light-danger">
                                        <i class="ti ti-file-type-pdf"></i>
                                        View PDF
                                    </a>

                                </td>

                                <td>

                                    @if($record->status == 1)
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                    @endif

                                </td>

                                <td class="text-end">

                                    {!! Form::open([
                                        'method' => 'DELETE',
                                        'route' => [
                                            'operating-safety-documents.operating-manual.correction.destroy',
                                            encrypt($record->id)
                                        ]
                                    ]) !!}

                                    <a href="#"
                                       class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                       data-size="lg"
                                       data-url="{{ route('operating-safety-documents.operating-manual.correction.edit', encrypt($record->id)) }}"
                                       data-title="{{ __('Edit Operating Manual Correction') }}">
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