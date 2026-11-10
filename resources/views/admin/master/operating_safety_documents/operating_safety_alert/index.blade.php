@extends('layouts.app')

@section('page-title')
    {{ __('Operating Safety Alert') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Master') }}</a></li>
    <li class="breadcrumb-item">{{ __('Operating & Safety Documents') }}</li>
    <li class="breadcrumb-item">{{ __('Operating Safety Alert') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header">
                <div class="row align-items-center g-2">
                    <div class="col">
                        <h5>{{ __('Operating Safety Alert List') }}</h5>
                    </div>

                    <div class="col-auto">
                        <a href="#"
                           class="btn btn-secondary customModal"
                           data-size="lg"
                           data-url="{{ route('operating-safety-documents.operating-safety-alert.create') }}"
                           data-title="{{ __('Create Operating Safety Alert') }}">
                            <i class="ti ti-circle-plus"></i>
                            Create Operating Safety Alert
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
                                <th>Alert No.</th>
                                <th>Subject</th>
                                <th>PDF</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($alerts as $index => $alert)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ \Carbon\Carbon::parse($alert->date)->format('d-m-Y') }}</td>
                                <td>{{ $alert->alert_no }}</td>
                                <td>{{ $alert->subject }}</td>

                                <td>
                                    <a href="{{ asset('public/storage/'.$alert->pdf_file) }}"
                                       target="_blank"
                                       class="btn btn-sm btn-light-danger">
                                        <i class="ti ti-file-type-pdf"></i>
                                        View PDF
                                    </a>
                                </td>

                                <td>
                                    @if($alert->status == 1)
                                        <span class="badge bg-success-subtle text-success">Active</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">Inactive</span>
                                    @endif
                                </td>

                                <td class="text-end">
                                    {!! Form::open([
                                        'method' => 'DELETE',
                                        'route' => [
                                            'operating-safety-documents.operating-safety-alert.destroy',
                                            encrypt($alert->id)
                                        ]
                                    ]) !!}

                                    <a href="#"
                                       class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                       data-size="lg"
                                       data-url="{{ route('operating-safety-documents.operating-safety-alert.edit', encrypt($alert->id)) }}"
                                       data-title="{{ __('Edit Operating Safety Alert') }}">
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