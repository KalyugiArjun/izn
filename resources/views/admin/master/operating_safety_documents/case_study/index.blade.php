
@extends('layouts.app')

@section('page-title')
    {{ __('Case Study') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Master') }}</a>
    </li>

    <li class="breadcrumb-item">
        {{ __('Operating & Safety Documents') }}
    </li>

    <li class="breadcrumb-item" aria-current="page">
        {{ __('Case Study') }}
    </li>
@endsection

@section('content')

<div class="row">
    <div class="col-sm-12">

        <div class="card">

            <div class="card-header">
                <div class="row align-items-center g-2">

                    <div class="col">
                        <h5>
                            {{ __('Case Study List') }}
                        </h5>
                    </div>

                    <div class="col-auto">
                        <a
                            href="#"
                            class="btn btn-secondary customModal"
                            data-size="lg"
                            data-url="{{ route('operating-safety-documents.case-study.create') }}"
                            data-title="{{ __('Create Case Study') }}">

                            <i class="ti ti-circle-plus align-text-bottom"></i>
                            {{ __('Create Case Study') }}

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
                                <th>Case Study Title</th>
                                <th>Date of Incident</th>
                                <th>Location</th>
                                <th>Department</th>
                                <th>Type of Irregularity</th>
                                <th>Risk Level</th>
                                <th>Key Learning</th>
                                <th>PDF</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($caseStudies as $index => $caseStudy)

                                <tr>

                                    <td>
                                        {{ $index + 1 }}
                                    </td>

                                    <td>
                                        {{ $caseStudy->case_study_title }}
                                    </td>

                                    <td>
                                        {{ $caseStudy->incident_date
                                            ? \Carbon\Carbon::parse($caseStudy->incident_date)->format('d-m-Y')
                                            : '-' }}
                                    </td>

                                    <td>
                                        {{ $caseStudy->location }}
                                    </td>

                                    <td>
                                        {{ DB::table('departments')
                                            ->where('id', $caseStudy->department_id)
                                            ->value('name') ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $caseStudy->type_of_irregularity }}
                                    </td>

                                    <td>
                                        @if($caseStudy->risk_level == 'LOW')
                                            <span class="badge bg-success-subtle text-success">
                                                LOW
                                            </span>
                                        @elseif($caseStudy->risk_level == 'MEDIUM')
                                            <span class="badge bg-warning-subtle text-warning">
                                                MEDIUM
                                            </span>
                                        @elseif($caseStudy->risk_level == 'HIGH')
                                            <span class="badge bg-danger-subtle text-danger">
                                                HIGH
                                            </span>
                                        @elseif($caseStudy->risk_level == 'CRITICAL')
                                            <span class="badge bg-danger text-white">
                                                CRITICAL
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td>
                                        {{ $caseStudy->key_learning }}
                                    </td>

                                    <td>
                                        @if ($caseStudy->pdf_file)

                                            <div>

                                                {{-- <div class="fw-semibold">
                                                    {{ basename($caseStudy->pdf_file) }}
                                                </div> --}}

                                                <a
                                                    href="{{ asset('public/storage/' . $caseStudy->pdf_file) }}"
                                                    target="_blank"
                                                    class="btn btn-sm btn-light-danger mt-1">

                                                    <i class="ti ti-file-type-pdf"></i>
                                                    View PDF

                                                </a>

                                            </div>

                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td>

                                        @if ($caseStudy->status == 1)

                                            <span class="badge bg-success-subtle text-success">
                                                Active
                                            </span>

                                        @else

                                            <span class="badge bg-danger-subtle text-danger">
                                                Inactive
                                            </span>

                                        @endif

                                    </td>

                                    <td class="text-end">

                                        {!! Form::open([
                                            'method' => 'DELETE',
                                            'route' => [
                                                'operating-safety-documents.case-study.destroy',
                                                encrypt($caseStudy->id)
                                            ],
                                        ]) !!}

                                        <a
                                            href="#"
                                            class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                            data-bs-toggle="tooltip"
                                            data-size="lg"
                                            data-url="{{ route(
                                                'operating-safety-documents.case-study.edit',
                                                encrypt($caseStudy->id)
                                            ) }}"
                                            data-title="{{ __('Edit Case Study') }}">

                                            <i data-feather="edit"></i>

                                        </a>

                                        <a
                                            href="#"
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
