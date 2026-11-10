
@extends('layouts.app')

@section('page-title')
    {{ __('New Work') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">
            {{ __('Master') }}
        </a>
    </li>

    <li class="breadcrumb-item">
        {{ __('PH 16 Works') }}
    </li>

    <li class="breadcrumb-item" aria-current="page">
        {{ __('New Work') }}
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">

                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>{{ __('New Work List') }}</h5>
                        </div>

                        <div class="col-auto">
                            <a href="#"
                                class="btn btn-secondary customModal"
                                data-size="lg"
                                data-url="{{ route('works-paperwork-progress-register.identify-new-work.create') }}"
                                data-title="{{ __('Create New Work') }}"
                                data-description="{{ __('Enter the work description and date, then select the concerned Traffic Inspector.') }}">

                                <i class="ti ti-circle-plus align-text-bottom"></i>
                                {{ __('Create New Work') }}
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
                                    <th>{{ __('Date of Work') }}</th>
                                    <th>{{ __('Concern Traffic Inspector') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($identifyNewWorks as $index => $identifyNewWork)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>

                                        <td>
                                            {{ $identifyNewWork->work_description_identification }}
                                        </td>

                                        <td>
                                            {{ $identifyNewWork->date_of_identification_of_work
                                                ? $identifyNewWork->date_of_identification_of_work->format('d-m-Y')
                                                : '-' }}
                                        </td>

                                        <td>
                                            @php
                                                $inspectorsList = !empty($identifyNewWork->concern_traffic_inspector)
                                                    ? array_filter(array_map('trim', explode(',', $identifyNewWork->concern_traffic_inspector)))
                                                    : [];
                                            @endphp
                                            @if (!empty($inspectorsList))
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach ($inspectorsList as $inspName)
                                                        <span class="badge bg-light-primary text-primary border border-primary px-2 py-1 font-semibold" style="font-size: 0.8rem;">
                                                            {{ $inspName }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($identifyNewWork->status == 1)
                                                <span class="badge bg-success">
                                                    {{ __('Active') }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    {{ __('Inactive') }}
                                                </span>
                                            @endif
                                        </td>

                                        <td class="text-end">
                                            {!! Form::open([
                                                'method' => 'DELETE',
                                                'route' => [
                                                    'works-paperwork-progress-register.identify-new-work.destroy',
                                                    encrypt($identifyNewWork->id),
                                                ],
                                            ]) !!}

                                            <a href="#"
                                                class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                data-bs-toggle="tooltip"
                                                data-size="lg"
                                                data-url="{{ route('works-paperwork-progress-register.identify-new-work.edit', encrypt($identifyNewWork->id)) }}"
                                                data-title="{{ __('Edit New Work') }}"
                                                data-description="{{ __('Update the work details and concerned Traffic Inspector.') }}"
                                                aria-label="{{ __('Edit New Work') }}">

                                                <i data-feather="edit"></i>
                                            </a>

                                            <a href="#"
                                                class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                                data-bs-toggle="tooltip"
                                                data-bs-original-title="{{ __('Delete') }}"
                                                aria-label="{{ __('Delete') }}">

                                                <i data-feather="trash-2"></i>
                                            </a>

                                            {!! Form::close() !!}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            {{ __('No work records found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
