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

    <li class="breadcrumb-item">
        {{ __('Background Login') }}
    </li>

    <li class="breadcrumb-item active">
        {{ __('Edit') }}
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">

            <div class="card">

                <div class="card-header">
                    <h5 class="mb-0">
                        {{ __('Edit Background Login') }}
                    </h5>
                </div>

                {{ Form::open([
                    'route' => ['frontend-login.background-login.update', encrypt($backgroundLogin->id)],
                    'method' => 'PUT',
                    'files' => true,
                    'id' => 'background_login_form',
                    'novalidate' => 'novalidate',
                ]) }}

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">

                                {{ Form::label('background_image', __('Background Image'), ['class' => 'form-label']) }}

                                @if ($backgroundLogin->background_image)
                                    <div class="mb-3">
                                        <img src="{{ asset('storage/' . $backgroundLogin->background_image) }}"
                                            alt="Background Image" style="max-width: 300px; max-height: 180px;"
                                            class="img-thumbnail">
                                    </div>
                                @endif

                                {{ Form::file('background_image', [
                                    'class' => 'form-control',
                                    'accept' => 'image/jpeg,image/png,image/jpg,image/gif',
                                ]) }}

                                <small class="text-muted">
                                    {{ __('Leave empty to keep the current image.') }}
                                </small>

                                @error('background_image')
                                    <span class="text-danger d-block">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                {{ Form::label('login_image', __('Login Image'), ['class' => 'form-label']) }}

                                @if ($backgroundLogin->login_image)
                                    <div class="mb-3">
                                        <img src="{{ asset('storage/' . $backgroundLogin->login_image) }}"
                                            alt="Login Image" style="max-width: 300px; max-height: 180px;"
                                            class="img-thumbnail">
                                    </div>
                                @endif

                                {{ Form::file('login_image', [
                                    'class' => 'form-control',
                                    'accept' => 'image/jpeg,image/png,image/jpg,image/gif',
                                ]) }}

                                <small class="text-muted">
                                    {{ __('Leave empty to keep the current image.') }}
                                </small>

                                @error('login_image')
                                    <span class="text-danger d-block">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">

                                {{ Form::label('status', __('Status'), ['class' => 'form-label']) }}

                                {{ Form::select(
                                    'status',
                                    [
                                        '1' => __('Active'),
                                        '0' => __('Inactive'),
                                    ],
                                    $backgroundLogin->status,
                                    [
                                        'class' => 'form-control select2',
                                        'required' => true,
                                    ],
                                ) }}

                                @error('status')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror

                            </div>
                        </div>

                    </div>

                </div>

                <div class="card-footer text-end">

                    <a href="{{ route('frontend-login.background-login.index') }}" class="btn btn-secondary">
                        {{ __('Cancel') }}
                    </a>

                    {{ Form::submit(__('Update'), ['class' => 'btn btn-primary']) }}

                </div>

                {{ Form::close() }}

            </div>

        </div>
    </div>
@endsection
