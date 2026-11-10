@extends('layouts.app')

@section('page-title')
    {{ __('Background Login') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">
            {{ __('Master') }}
        </a>
    </li>

    <li class="breadcrumb-item active">
        {{ __('Background Login') }}
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        {{ __('Background Login') }}
                    </h5>

                    @if (!$backgroundLogin)
                        <a href="{{ route('frontend-login.background-login.create') }}"
                           class="btn btn-primary">
                            <i class="ti ti-plus"></i>
                            {{ __('Create') }}
                        </a>
                    @else
                        <a href="{{ route('frontend-login.background-login.edit', encrypt($backgroundLogin->id)) }}"
                           class="btn btn-primary">
                            <i class="ti ti-edit"></i>
                            {{ __('Edit') }}
                        </a>
                    @endif
                </div>

                <div class="card-body">
                    @if ($backgroundLogin)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ __('Background Image') }}</th>
                                        <th>{{ __('Login Image') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th width="150">{{ __('Action') }}</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td>
                                            @if ($backgroundLogin->background_image)
                                                <img
                                                    src="{{ asset('storage/' . $backgroundLogin->background_image) }}"
                                                    alt="Background Image"
                                                    style="width: 150px; height: 90px; object-fit: cover;"
                                                    class="img-thumbnail">
                                            @else
                                                <span class="text-muted">
                                                    {{ __('No background image') }}
                                                </span>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($backgroundLogin->login_image)
                                                <img
                                                    src="{{ asset('storage/' . $backgroundLogin->login_image) }}"
                                                    alt="Login Image"
                                                    style="width: 150px; height: 90px; object-fit: cover;"
                                                    class="img-thumbnail">
                                            @else
                                                <span class="text-muted">
                                                    {{ __('No login image') }}
                                                </span>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($backgroundLogin->status == 1)
                                                <span class="badge bg-success">
                                                    {{ __('Active') }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    {{ __('Inactive') }}
                                                </span>
                                            @endif
                                        </td>

                                        <td>
                                            <a
                                                href="{{ route('frontend-login.background-login.edit', encrypt($backgroundLogin->id)) }}"
                                                class="btn btn-sm btn-primary"
                                                title="{{ __('Edit') }}">
                                                <i class="ti ti-edit"></i>
                                            </a>

                                            <form
                                                action="{{ route('frontend-login.background-login.destroy', encrypt($backgroundLogin->id)) }}"
                                                method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this record?');">

                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    title="{{ __('Delete') }}">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <h5>
                                {{ __('No Background Login configuration found.') }}
                            </h5>

                            <p class="text-muted">
                                {{ __('Create the Background Login configuration to continue.') }}
                            </p>

                            <a href="{{ route('frontend-login.background-login.create') }}"
                               class="btn btn-primary">
                                <i class="ti ti-plus"></i>
                                {{ __('Create Background Login') }}
                            </a>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection
