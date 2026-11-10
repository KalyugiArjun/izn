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
        {{ __('Create') }}
    </li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        {{ __('Create Background Login') }}
                    </h5>
                </div>

                {{ Form::open([
                    'route' => 'frontend-login.background-login.store',
                    'method' => 'POST',
                    'files' => true,
                    'id' => 'background_login_form',
                    'novalidate' => 'novalidate',
                ]) }}

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('background_image', __('Background Image'), ['class' => 'form-label']) }}

                                {{ Form::file('background_image', [
                                    'class' => 'form-control',
                                    'accept' => 'image/jpeg,image/png,image/jpg,image/gif',
                                ]) }}

                                @error('background_image')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::label('login_image', __('Login Image'), ['class' => 'form-label']) }}

                                {{ Form::file('login_image', [
                                    'class' => 'form-control',
                                    'accept' => 'image/jpeg,image/png,image/jpg,image/gif',
                                ]) }}

                                @error('login_image')
                                    <span class="text-danger">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group col-md-6 mb-3">
                            {{ Form::label('status', __('Status') . '<span class="text-danger"> * </span>', ['class' => 'form-label'], false) }}
                            {{ Form::select('status', [1 => __('Active'), 0 => __('Inactive')], 1, ['class' => 'select2', 'required' => true]) }}
                        </div>

                    </div>

                </div>

                <div class="card-footer text-end">
                    <a href="{{ route('frontend-login.background-login.index') }}" class="btn btn-secondary">
                        {{ __('Cancel') }}
                    </a>

                    {{ Form::submit(__('Save'), ['class' => 'btn btn-primary']) }}
                </div>

                {{ Form::close() }}

            </div>

        </div>
    </div>
@endsection
