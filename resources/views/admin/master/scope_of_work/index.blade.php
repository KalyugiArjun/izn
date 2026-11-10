@extends('layouts.app')

@section('page-title', __('Scope of Work'))

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Project Monitering System') }}</a>
    </li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Scope of Work') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center g-2">
                        <div class="col">
                            <h5>{{ __('Scope of Work List') }}</h5>
                        </div>
                        <div class="col-auto">
                            <a href="#" class="btn btn-secondary customModal" data-size="md"
                                data-url="{{ route('project-monitering-system.scope_of_work.create') }}"
                                data-title="{{ __('Create Scope of Work') }}">
                                <i class="ti ti-circle-plus align-text-bottom"></i>
                                {{ __('Create Scope of Work') }}
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
                                    <th>{{ __('Project') }}</th>
                                    <th>{{ __('Scope of Work') }}</th>
                                    <th>{{ __('Department') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($scopes as $index => $scope)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $scope->project->project_id ?? '-' }}</td>

                                        <td>
                                            <div style="max-width: 350px;">
                                                {{ Str::limit($scope->scope_of_work, 80) }}
                                            </div>
                                        </td>

                                        <td>{{ $scope->department->name ?? '-' }}</td>

                                        <td>
                                            @if ($scope->status == 1)
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
                                            <div class="cart-action">
                                                {!! Form::open([
                                                    'method' => 'DELETE',
                                                    'route' => ['project-monitering-system.scope_of_work.destroy', encrypt($scope->id)],
                                                ]) !!}

                                                <a class="avtar avtar-xs btn-link-secondary text-secondary customModal"
                                                    data-bs-toggle="tooltip" data-size="lg" title="{{ __('Edit') }}"
                                                    href="#"
                                                    data-url="{{ route('project-monitering-system.scope_of_work.edit', encrypt($scope->id)) }}"
                                                    data-title="{{ __('Edit Scope of Work') }}">
                                                    <i data-feather="edit"></i>
                                                </a>

                                                <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                                    data-bs-toggle="tooltip" title="{{ __('Delete') }}" href="#">
                                                    <i data-feather="trash-2"></i>
                                                </a>

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

