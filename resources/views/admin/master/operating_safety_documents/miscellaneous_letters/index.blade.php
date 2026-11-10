@extends('layouts.app')

@section('page-title')
    {{ __('Miscellaneous Letters') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Master') }}</a>
    </li>
    <li class="breadcrumb-item">{{ __('Operating & Safety Documents') }}</li>
    <li class="breadcrumb-item">{{ __('Miscellaneous Letters') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header">
                <div class="row align-items-center g-2">
                    <div class="col">
                        <h5>{{ __('Miscellaneous Letters List') }}</h5>
                    </div>

                    <div class="col-auto">
                        <a href="#"
                           class="btn btn-secondary customModal"
                           data-size="lg"
                           data-url="{{ route('operating-safety-documents.miscellaneous-letters.create') }}"
                           data-title="{{ __('Create Miscellaneous Letter') }}">
                            <i class="ti ti-circle-plus"></i>
                            {{ __('Create Miscellaneous Letter') }}
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
                                <th>Date</th>
                                <th>Letter No.</th>
                                <th>Subject</th>
                                <th>Department</th>
                                <th>PDF</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($letters as $index => $letter)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($letter->date)->format('d-m-Y') }}</td>
                                <td>{{ $letter->letter_no }}</td>
                                <td>{{ $letter->subject }}</td>
                                <td>{{ $letter->department_name ?? '-' }}</td>

                                <td>
                                    <a href="{{ asset('public/storage/' . $letter->pdf_file) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-light-danger">
                                        <i class="ti ti-file-type-pdf"></i>
                                        View PDF
                                    </a>
                                </td>

                                <td>
                                    @if($letter->status == 1)
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                    @endif
                                </td>

                                <td class="text-end">

                                    {!! Form::open([
                                        'method' => 'DELETE',
                                        'route' => [
                                            'operating-safety-documents.miscellaneous-letters.destroy',
                                            encrypt($letter->id)
                                        ]
                                    ]) !!}

                                    <a href="#"
                                       class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                       data-size="lg"
                                       data-url="{{ route('operating-safety-documents.miscellaneous-letters.edit', encrypt($letter->id)) }}"
                                       data-title="{{ __('Edit Miscellaneous Letter') }}">
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