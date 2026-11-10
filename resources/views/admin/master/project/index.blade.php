@extends('layouts.app')

@section('page-title', __('Projects'))

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Project Monitering System') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Projects') }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center g-2">
                    <div class="col">
                        <h5>{{ __('Project List') }}</h5>
                    </div>
                    <div class="col-auto">
                        <a href="#"
                           class="btn btn-secondary customModal"
                           data-size="lg"
                           data-url="{{ route('project-monitering-system.project.create') }}"
                           data-title="{{ __('Create Project') }}">
                            <i class="ti ti-circle-plus align-text-bottom"></i>
                            {{ __('Create Project') }}
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
                                <th>{{ __('Project ID') }}</th>
                                <th>{{ __('Department') }}</th>
                                <th>{{ __('Cost (₹)') }}</th>
                                <th>{{ __('Working Agencies') }}</th>
                                <th>{{ __('Sanction Date') }}</th>
                                <th>{{ __('TDC Date') }}</th>
                                <th>{{ __('Financial Year') }}</th>
                                <th>{{ __('Plan Head') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="text-end">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                             @foreach  ($projects as $index => $project)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                         {{ $project->project_id }}
                                    </td>
                                    <td>{{ $project->department->name ?? '-' }}</td>
                                    <td>₹ {{ number_format($project->cost, 2) }}</td>
                                    <td>{{ $project->working_agencies ?? '-' }}</td>
                                    <td>
                                        {{ $project->sanction_date
                                            ? $project->sanction_date->format('d M Y')
                                            : '-' }}
                                    </td>
                                    <td>
                                        {{ $project->tdc_date
                                            ? $project->tdc_date->format('d M Y')
                                            : '-' }}
                                    </td>
                                    <td>{{ $project->financial_year ?? '-' }}</td>
                                    <td>{{ $project->plan_head ?? '-' }}</td>
                                    <td>
                                        @if ($project->status == 1)
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
                                        {{-- <div class="dropdown">
                                            <a class="dropdown-toggle text-primary opacity-50 arrow-none"
                                               href="#"
                                               data-bs-toggle="dropdown"
                                               aria-haspopup="true"
                                               aria-expanded="false">
                                                <i class="ti ti-dots f-16"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item customModal" href="#"
                                                   data-size="lg"
                                                   data-url="{{ route('project-monitering-system.project.edit', encrypt($project->id)) }}"
                                                   data-title="{{ __('Edit Project') }}">
                                                    <i class="ti ti-edit"></i> {{ __('Edit') }}
                                                </a>
                                                <a href="{{ route('project-monitering-system.project.destroy', encrypt($project->id)) }}"
                                                   class="dropdown-item"
                                                   onclick="return confirm('Are you sure you want to delete this?')">
                                                    <i class="ti ti-trash"></i> {{ __('Delete') }}
                                                </a>
                                            </div>
                                        </div> --}}
                                        <div class="cart-action">


                                                {!! Form::open(['method' => 'DELETE', 'route' => ['project-monitering-system.project.destroy', encrypt($project->id)]]) !!}
                                                <a class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                    data-bs-toggle="tooltip" data-size="lg"
                                                    data-bs-original-title="{{ __('Edit') }}" href="#"
                                                      data-url="{{ route('project-monitering-system.project.edit', encrypt($project->id)) }}"
                                                    data-title="{{ __('Edit Project') }}"> <i data-feather="edit"></i></a>


                                                <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                                    data-bs-toggle="tooltip" data-bs-original-title="{{ __('Detete') }}"
                                                    href="#"> <i data-feather="trash-2"></i></a>

                                                {!! Form::close() !!}
                                            </div>
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

