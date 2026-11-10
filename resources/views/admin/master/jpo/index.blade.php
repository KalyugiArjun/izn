@extends('layouts.app')

@section('page-title')
    {{ __('JPO') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Master') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">
        {{ __('JPO') }}
    </li>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header">
                <div class="row align-items-center g-2">
                    <div class="col">
                        <h5>{{ __('JPO List') }}</h5>
                    </div>

                    <div class="col-auto">
                        <a href="#"
                           class="btn btn-secondary customModal"
                           data-size="lg"
                           data-url="{{ route('document-upload.jpo.create') }}"
                           data-title="{{ __('Create JPO') }}">
                            <i class="ti ti-circle-plus align-text-bottom"></i>
                            {{ __('Create JPO') }}
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
                                <th>{{ __('JPO Number') }}</th>
                                <th>{{ __('Date of JPO') }}</th>
                                <th>{{ __('Subject') }}</th>
                                <th>{{ __('PDF') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="text-end">{{ __('Action') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                        @foreach($jpos as $index => $jpo)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $jpo->jpo_number }}</td>
                                <td>{{ \Carbon\Carbon::parse($jpo->date_of_jpo)->format('d-m-Y') }}</td>
                                <td>{{ $jpo->subject }}</td>

                                <td>
                                    @if($jpo->pdf_file)
                                        <a href="{{ asset('storage/' . $jpo->pdf_file) }}"
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
                                    @if($jpo->status == 1)
                                        <span class="badge bg-success-subtle text-success">{{ __('Active') }}</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">{{ __('Inactive') }}</span>
                                    @endif
                                </td>

                                <td class="text-end">
                                    {!! Form::open([
                                        'method' => 'DELETE',
                                        'route' => [
                                            'document-upload.jpo.destroy',
                                            encrypt($jpo->id)
                                        ]
                                    ]) !!}

                                    <a href="#"
                                       class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                       data-size="lg"
                                       data-bs-toggle="tooltip"
                                       data-url="{{ route('document-upload.jpo.edit', encrypt($jpo->id)) }}"
                                       data-title="{{ __('Edit JPO') }}">
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