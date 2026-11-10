@extends('layouts.app')
@section('page-title')
{{ __('System Settings') }}
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
<li class="breadcrumb-item" aria-current="page"> {{ __('System Settings') }}</li>
@endsection
@php
$admin_logo = getSettingsValByName('company_logo');
$profile = asset(Storage::url('upload/profile'));
$activeTab = session('tab', 'user_profile_settings');
@endphp
@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4">
                        <ul class="nav flex-column nav-tabs account-tabs mb-3" id="myTab" role="tablist">
                            @if (Gate::check('manage account settings'))
                            <li class="nav-item">
                                <a class="nav-link {{ empty($activeTab) || $activeTab == 'user_profile_settings' ? ' active ' : '' }}"
                                    id="profile-tab-1" data-bs-toggle="tab" href="#user_profile_settings"
                                    role="tab" aria-selected="true">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="ti ti-user-check me-2 f-20"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <h5 class="mb-0">{{ __('User Profile') }}</h5>
                                            <small
                                                class="text-muted">{{ __('User Account Profile Settings') }}</small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            @endif
                            @if (Gate::check('manage password settings'))
                            <li class="nav-item">
                                <a class="nav-link  {{ empty($activeTab) || $activeTab == 'password_settings' ? ' active ' : '' }}"
                                    id="profile-tab-2" data-bs-toggle="tab" href="#password_settings" role="tab"
                                    aria-selected="true">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="ti ti-key me-2 f-20"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <h5 class="mb-0">{{ __('Password') }}</h5>
                                            <small class="text-muted">{{ __('Password Settings') }}</small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            @endif
                            @if (Gate::check('manage general settings'))
                            <li class="nav-item">
                                <a class="nav-link {{ empty($activeTab) || $activeTab == 'general_settings' ? ' active ' : '' }}"
                                    id="profile-tab-3" data-bs-toggle="tab" href="#general_settings" role="tab"
                                    aria-selected="true">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="ti ti-settings me-2 f-20"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <h5 class="mb-0">{{ __('General') }}</h5>
                                            <small class="text-muted">{{ __('General Settings') }}</small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            @endif
                            {{-- @if (Gate::check('manage company settings'))
                            <li class="nav-item">
                                <a class="nav-link {{ empty($activeTab) || $activeTab == 'company_settings' ? ' active ' : '' }}"
                                    id="profile-tab-4" data-bs-toggle="tab" href="#company_settings" role="tab"
                                    aria-selected="true">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="ti ti-building me-2 f-20"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <h5 class="mb-0">{{ __('Company') }}</h5>
                                            <small class="text-muted">{{ __('Company Settings') }}</small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            @endif --}}
                            @if (Gate::check('manage email settings'))
                            <li class="nav-item">
                                <a class="nav-link {{ empty($activeTab) || $activeTab == 'email_SMTP_settings' ? ' active ' : '' }} "
                                    id="profile-tab-5" data-bs-toggle="tab" href="#email_SMTP_settings"
                                    role="tab" aria-selected="true">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="ti ti-mail me-2 f-20"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <h5 class="mb-0">{{ __('Email') }}</h5>
                                            <small class="text-muted">{{ __('Email SMTP Settings') }}</small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            @endif

                            @if (Gate::check('manage seo settings'))
                            <li class="nav-item">
                                <a class="nav-link {{ empty($activeTab) || $activeTab == 'site_SEO_settings' ? ' active ' : '' }} "
                                    id="profile-tab-7" data-bs-toggle="tab" href="#site_SEO_settings" role="tab"
                                    aria-selected="true">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="ti ti-sitemap me-2 f-20"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <h5 class="mb-0">{{ __('Site SEO') }}</h5>
                                            <small class="text-muted">{{ __('Site SEO Settings') }}</small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            @endif


                            {{-- @if (Gate::check('manage twilio settings'))
                            <li class="nav-item">
                                <a class="nav-link {{ empty($activeTab) || $activeTab == 'twilio' ? ' active ' : '' }} "
                                    id="profile-tab-9" data-bs-toggle="tab" href="#twilio" role="tab"
                                    aria-selected="true">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="ti ti-message-dots me-2 f-20"></i>
                                        </div>
                                        <div class="flex-grow-1 ms-2">
                                            <h5 class="mb-0">{{ __('twilio Settings') }}</h5>
                                            <small class="text-muted">{{ __('twilio Settings') }}</small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            @endif --}}
                        </ul>
                    </div>
                    <div class="col-lg-8">
                        <div class="tab-content">
                            @if (Gate::check('manage account settings'))
                            <div class="tab-pane {{ empty($activeTab) || $activeTab == 'user_profile_settings' ? ' active show ' : '' }}"
                                id="user_profile_settings" role="tabpanel"
                                aria-labelledby="user_profile_settings">
                                {{ Form::model($loginUser, ['route' => ['setting.account'], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0">
                                        <img src="{{ !empty($loginUser->profile) ? $profile . '/' . $loginUser->profile : $profile . '/avatar.png' }}"
                                            alt="user-image" class="img-fluid rounded-circle wid-80" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}
                                            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter your name'), 'required' => 'required']) }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('email', __('Email Address'), ['class' => 'form-label']) }}
                                            {{ Form::text('email', null, ['class' => 'form-control', 'placeholder' => __('Enter your email'), 'required' => 'required']) }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('phone_number', __('Phone Number'), ['class' => 'form-label']) }}
                                            {{ Form::number('phone_number', null, ['class' => 'form-control', 'placeholder' => __('Enter your Phone Number')]) }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('profile', __('Profile'), ['class' => 'form-label']) }}
                                            {{ Form::file('profile', ['class' => 'form-control']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-6"></div>
                                    <div class="col-6 text-end">
                                        {{ Form::submit(__('Save'), ['class' => 'btn btn-secondary btn-rounded']) }}
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                            @endif
                            @if (Gate::check('manage password settings'))
                            <div class="tab-pane {{ !empty($activeTab) && $activeTab == 'password_settings' ? ' active show ' : '' }}"
                                id="password_settings" role="tabpanel" aria-labelledby="password_settings">
                                {{ Form::model($loginUser, ['route' => ['setting.password'], 'method' => 'post']) }}
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {{ Form::label('current_password', __('Current Password'), ['class' => 'form-label']) }}
                                            {{ Form::password('current_password', ['class' => 'form-control', 'placeholder' => __('Enter your current password'), 'required' => 'required']) }}
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {{ Form::label('new_password', __('New Password'), ['class' => 'form-label']) }}
                                            {{ Form::password('new_password', ['class' => 'form-control', 'placeholder' => __('Enter your new password'), 'required' => 'required']) }}
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {{ Form::label('confirm_password', __('Confirm New Password'), ['class' => 'form-label']) }}
                                            {{ Form::password('confirm_password', ['class' => 'form-control', 'placeholder' => __('Enter your confirm new password'), 'required' => 'required']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-6"></div>
                                    <div class="col-6 text-end">
                                        {{ Form::submit(__('Save'), ['class' => 'btn btn-secondary btn-rounded']) }}
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                            @endif
                            @if (Gate::check('manage general settings'))
                            <div class="tab-pane {{ !empty($activeTab) && $activeTab == 'general_settings' ? ' active show ' : '' }}"
                                id="general_settings" role="tabpanel" aria-labelledby="general_settings">
                                {{ Form::model($settings, ['route' => ['setting.general'], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('application_name', __('Application Name'), ['class' => 'form-label']) }}
                                            {{ Form::text('application_name', !empty($settings['app_name']) ? $settings['app_name'] : env('APP_NAME'), ['class' => 'form-control', 'placeholder' => __('Enter your application name'), 'required' => 'required']) }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {{ Form::label('copyright', __('Copyright'), ['class' => 'form-label']) }}
                                            {{ Form::text('copyright', $settings['copyright'], ['class' => 'form-control', 'placeholder' => __('Enter copyright ')]) }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {{ Form::label('logo', __('Logo'), ['class' => 'form-label']) }}
                                            <a href="{{ asset(Storage::url('upload/logo/')) . '/' . (isset($admin_logo) && !empty($admin_logo) ? $admin_logo : 'logo.png') }}"
                                                target="_blank"><i class="ti ti-eye ms-2 f-15"></i></a>
                                            {{ Form::file('logo', ['class' => 'form-control', 'accept' => '.png']) }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {{ Form::label('favicon', __('Favicon'), ['class' => 'form-label']) }}
                                            <a href="{{ asset(Storage::url('upload/logo')) . '/' . $settings['company_favicon'] }}"
                                                target="_blank"><i class="ti ti-eye ms-2 f-15"></i></a>
                                            {{ Form::file('favicon', ['class' => 'form-control', 'accept' => '.png']) }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {{ Form::label('light_logo', __('Light Logo'), ['class' => 'form-label']) }}
                                            <a href="{{ asset(Storage::url('upload/logo')) . '/' . $settings['light_logo'] }}"
                                                target="_blank"><i class="ti ti-eye ms-2 f-15"></i></a>
                                            {{ Form::file('light_logo', ['class' => 'form-control', 'accept' => '.png']) }}
                                        </div>
                                    </div>
                                    @if (\Auth::user()->type == 'super admin')
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            {{ Form::label('landing_logo', __('Landing Page Logo'), ['class' => 'form-label']) }}
                                            <a href="{{ asset(Storage::url('upload/logo/landing_logo.png')) }}"
                                                target="_blank"><i class="ti ti-eye ms-2 f-15"></i></a>
                                            {{ Form::file('landing_logo', ['class' => 'form-control', 'accept' => '.png']) }}
                                        </div>
                                    </div>
                                    <!--<div class="col-md-4">-->
                                    <!--    <div class="form-group">-->
                                    <!--        {{ Form::label('landing_logo', __('Owner Email Verification'), ['class' => 'form-label']) }}-->
                                    <!--        <div class="flex-shrink-0">-->
                                    <!--            <div class="form-check form-switch">-->
                                    <!--                <input class="form-check-input" type="checkbox"-->
                                    <!--                    id="owner_email_verification"-->
                                    <!--                    name="owner_email_verification"-->
                                    <!--                    {{ $settings['owner_email_verification'] == 'on' ? 'checked' : '' }}>-->
                                    <!--            </div>-->
                                    <!--        </div>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                    <!--<div class="col-md-4">-->
                                    <!--    <div class="form-group">-->
                                    <!--        {{ Form::label('landing_logo', __('Registration Page'), ['class' => 'form-label']) }}-->
                                    <!--        <div class="flex-shrink-0">-->
                                    <!--            <div class="form-check form-switch">-->
                                    <!--                <input class="form-check-input" type="checkbox"-->
                                    <!--                    id="register_page" name="register_page"-->
                                    <!--                    {{ $settings['register_page'] == 'on' ? 'checked' : '' }}>-->
                                    <!--            </div>-->
                                    <!--        </div>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                    <!--<div class="col-md-4">-->
                                    <!--    <div class="form-group">-->
                                    <!--        {{ Form::label('landing_logo', __('Landing Page'), ['class' => 'form-label']) }}-->
                                    <!--        <div class="flex-shrink-0">-->
                                    <!--            <div class="form-check form-switch">-->
                                    <!--                <input class="form-check-input" type="checkbox"-->
                                    <!--                    id="landing_page" name="landing_page"-->
                                    <!--                    {{ $settings['landing_page'] == 'on' ? 'checked' : '' }}>-->
                                    <!--            </div>-->
                                    <!--        </div>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                    <!--<div class="col-md-4">-->
                                    <!--    <div class="form-group">-->
                                    <!--        {{ Form::label('pricing_feature', __('Pricing Feature'), ['class' => 'form-label']) }}-->
                                    <!--        <div class="flex-shrink-0">-->
                                    <!--            <div class="form-check form-switch">-->
                                    <!--                <input type="hidden" name="pricing_feature"-->
                                    <!--                    value="off">-->
                                    <!--                <input class="form-check-input" type="checkbox"-->
                                    <!--                    id="pricing_feature" name="pricing_feature"-->
                                    <!--                    value="on"-->
                                    <!--                    {{ $settings['pricing_feature'] == 'on' ? 'checked' : '' }}>-->
                                    <!--            </div>-->
                                    <!--        </div>-->
                                    <!--    </div>-->
                                    <!--</div>-->
                                    @endif
                                </div>
                                <div class="row mt-3">
                                    <div class="col-6"></div>
                                    <div class="col-6 text-end">
                                        {{ Form::submit(__('Save'), ['class' => 'btn btn-secondary btn-rounded']) }}
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                            @endif
                            @if (Gate::check('manage company settings'))
                            <div class="tab-pane {{ !empty($activeTab) && $activeTab == 'company_settings' ? ' active show ' : '' }}"
                                id="company_settings" role="tabpanel" aria-labelledby="company_settings">
                                {{ Form::model($settings, ['route' => ['setting.company'], 'method' => 'post']) }}
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        {{ Form::label('company_name', __('Name'), ['class' => 'form-label']) }}
                                        {{ Form::text('company_name', $settings['company_name'], ['class' => 'form-control', 'placeholder' => __('Enter company name')]) }}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{ Form::label('company_email', __('Email'), ['class' => 'form-label']) }}
                                        {{ Form::text('company_email', $settings['company_email'], ['class' => 'form-control', 'placeholder' => __('Enter company email')]) }}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{ Form::label('company_phone', __('Phone Number'), ['class' => 'form-label']) }}
                                        {{ Form::text('company_phone', $settings['company_phone'], ['class' => 'form-control', 'placeholder' => __('Enter company phone')]) }}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{ Form::label('company_address', __('Address'), ['class' => 'form-label']) }}
                                        {{ Form::textarea('company_address', $settings['company_address'], ['class' => 'form-control', 'rows' => '2']) }}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{ Form::label('CURRENCY_SYMBOL', __('Currency Icon'), ['class' => 'form-label']) }}
                                        {{ Form::text('CURRENCY_SYMBOL', $settings['CURRENCY_SYMBOL'], ['class' => 'form-control', 'placeholder' => __('Enter currency symbol')]) }}
                                    </div>
                                    <div class="col-md-6">
                                        {{ Form::label('timezone', __('Timezone'), ['class' => 'form-label text-dark']) }}
                                        <select type="text" name="timezone" class="form-control basic-select"
                                            id="timezone">
                                            <option value="">{{ __('Select Timezone') }}</option>
                                            @foreach ($timezones as $k => $timezone)
                                            <option value="{{ $k }}"
                                                {{ $settings['timezone'] == $k ? 'selected' : '' }}>
                                                {{ $timezone }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group col-md-6">
                                        {{ Form::label('customer_number_prefix', __('Customer Number Prefix'), ['class' => 'form-label']) }}
                                        {{ Form::text('customer_number_prefix', $settings['customer_number_prefix'], ['class' => 'form-control', 'placeholder' => __('Enter customer number prefix')]) }}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{ Form::label('measurement_number_prefix', __('Measurement Number Prefix'), ['class' => 'form-label']) }}
                                        {{ Form::text('measurement_number_prefix', $settings['measurement_number_prefix'], ['class' => 'form-control', 'placeholder' => __('Enter measurement number prefix')]) }}
                                    </div>

                                    <div class="form-group col-md-6">
                                        {{ Form::label('expense_number_prefix', __('Expense Number Prefix'), ['class' => 'form-label']) }}
                                        {{ Form::text('expense_number_prefix', $settings['expense_number_prefix'], ['class' => 'form-control', 'placeholder' => __('Enter expense number prefix')]) }}
                                    </div>
                                    <div class="form-group col-md-3">
                                        {{ Form::label('company_zipcode', __('System Date Format'), ['class' => 'form-label']) }}
                                        <div class="">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="company_date_format1"
                                                    name="company_date_format" class="custom-control-input"
                                                    value="M j, Y"
                                                    {{ $settings['company_date_format'] == 'M j, Y' ? 'checked' : '' }}>
                                                <label class="custom-control-label"
                                                    for="company_date_format1">{{ date('M d,Y') }}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="company_date_format2"
                                                    name="company_date_format" class="custom-control-input"
                                                    value="y-m-d"
                                                    {{ $settings['company_date_format'] == 'y-m-d' ? 'checked' : '' }}>
                                                <label class="custom-control-label"
                                                    for="company_date_format2">{{ date('y-m-d') }}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="company_date_format3"
                                                    name="company_date_format" class="custom-control-input"
                                                    value="d-m-y"
                                                    {{ $settings['company_date_format'] == 'd-m-y' ? 'checked' : '' }}>
                                                <label class="custom-control-label"
                                                    for="company_date_format3">{{ date('d-m-y') }}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="company_date_format4"
                                                    name="company_date_format" class="custom-control-input"
                                                    value="m-d-y"
                                                    {{ $settings['company_date_format'] == 'm-d-y' ? 'checked' : '' }}>
                                                <label class="custom-control-label"
                                                    for="company_date_format4">{{ date('m-d-y') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-3">
                                        {{ Form::label('company_zipcode', __('System Time Format'), ['class' => 'form-label']) }}
                                        <div class="">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="company_time_format1"
                                                    name="company_time_format" class="custom-control-input"
                                                    value="H:i"
                                                    {{ $settings['company_time_format'] == 'H:i' ? 'checked' : '' }}>
                                                <label class="custom-control-label"
                                                    for="company_time_format1">{{ date('H:i') }}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="company_time_format2"
                                                    name="company_time_format" class="custom-control-input"
                                                    value="g:i A"
                                                    {{ $settings['company_time_format'] == 'g:i A' ? 'checked' : '' }}>
                                                <label class="custom-control-label"
                                                    for="company_time_format2">{{ date('g:i A') }}</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="company_time_format3"
                                                    name="company_time_format" class="custom-control-input"
                                                    value="g:i a"
                                                    {{ $settings['company_time_format'] == 'g:i a' ? 'checked' : '' }}>
                                                <label class="custom-control-label"
                                                    for="company_time_format3">{{ date('g:i a') }}</label>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                                <div class="row mt-3">
                                    <div class="col-6"></div>
                                    <div class="col-6 text-end">
                                        {{ Form::submit(__('Save'), ['class' => 'btn btn-secondary btn-rounded']) }}
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                            @endif
                            @if (Gate::check('manage email settings'))
                            <div class="tab-pane {{ !empty($activeTab) && $activeTab == 'email_SMTP_settings' ? ' active show ' : '' }}"
                                id="email_SMTP_settings" role="tabpanel" aria-labelledby="email_SMTP_settings">
                                {{ Form::model($settings, ['route' => ['setting.smtp'], 'method' => 'post']) }}
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        {{ Form::label('sender_name', __('Sender Name'), ['class' => 'form-label']) }}
                                        {{ Form::text('sender_name', $settings['FROM_NAME'], ['class' => 'form-control', 'placeholder' => __('Enter sender name')]) }}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{ Form::label('sender_email', __('Sender Email'), ['class' => 'form-label']) }}
                                        {{ Form::text('sender_email', $settings['FROM_EMAIL'], ['class' => 'form-control', 'placeholder' => __('Enter sender email')]) }}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{ Form::label('server_driver', __('SMTP Driver'), ['class' => 'form-label']) }}
                                        {{ Form::text('server_driver', $settings['SERVER_DRIVER'], ['class' => 'form-control', 'placeholder' => __('Enter smtp driver')]) }}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{ Form::label('server_host', __('SMTP Host'), ['class' => 'form-label']) }}
                                        {{ Form::text('server_host', $settings['SERVER_HOST'], ['class' => 'form-control ', 'placeholder' => __('Enter smtp host')]) }}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{ Form::label('server_username', __('SMTP Username'), ['class' => 'form-label']) }}
                                        {{ Form::text('server_username', $settings['SERVER_USERNAME'], ['class' => 'form-control', 'placeholder' => __('Enter smtp username')]) }}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{ Form::label('server_password', __('SMTP Password'), ['class' => 'form-label']) }}
                                        {{ Form::text('server_password', $settings['SERVER_PASSWORD'], ['class' => 'form-control', 'placeholder' => __('Enter smtp password')]) }}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{ Form::label('server_encryption', __('SMTP Encryption'), ['class' => 'form-label']) }}
                                        {{ Form::text('server_encryption', $settings['SERVER_ENCRYPTION'], ['class' => 'form-control', 'placeholder' => __('Enter smtp encryption')]) }}
                                    </div>
                                    <div class="form-group col-md-6">
                                        {{ Form::label('server_port', __('SMTP Port'), ['class' => 'form-label']) }}
                                        {{ Form::text('server_port', $settings['SERVER_PORT'], ['class' => 'form-control', 'placeholder' => __('Enter smtp port')]) }}
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-6"></div>
                                    <div class="col-6  text-end">
                                        <a href="#" data-size="md"
                                            data-url="{{ route('setting.smtp.test') }}"
                                            data-title="{{ __('Add Email') }}"
                                            class='btn btn-primary btn-rounded customModal me-1'>
                                            {{ __('Test Mail') }} </a>
                                        {{ Form::submit(__('Save'), ['class' => 'btn btn-secondary btn-rounded']) }}
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                            @endif



                            @if (Gate::check('manage seo settings'))
                            <div class="tab-pane {{ !empty($activeTab) && $activeTab == 'site_SEO_settings' ? ' active show ' : '' }}"
                                id="site_SEO_settings" role="tabpanel" aria-labelledby="site_SEO_settings">
                                {{ Form::model($settings, ['route' => ['setting.site.seo'], 'method' => 'post', 'enctype' => 'multipart/form-data']) }}
                                <div class="row">

                                    <div class="col-xl-12 col-lg-12">
                                        <div class="card">
                                            <div class="card-body">

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            {{ Form::label('meta_seo_title', __('Meta Title'), ['class' => 'form-label']) }}
                                                            {{ Form::text('meta_seo_title', $settings['meta_seo_title'], ['class' => 'form-control', 'placeholder' => __('Enter meta SEO title'), 'required' => 'required']) }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            {{ Form::label('meta_seo_keyword', __('Meta Keyword'), ['class' => 'form-label']) }}
                                                            {{ Form::text('meta_seo_keyword', $settings['meta_seo_keyword'], ['class' => 'form-control', 'placeholder' => __('Enter meta SEO keyword'), 'required' => 'required']) }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            {{ Form::label('meta_seo_description', __('Meta Description'), ['class' => 'form-label']) }}
                                                            {{ Form::textarea('meta_seo_description', $settings['meta_seo_description'], ['class' => 'form-control', 'placeholder' => __('Enter meta SEO description'), 'required' => 'required', 'rows' => '2']) }}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            {{ Form::label('meta_seo_image', __('Meta Image'), ['class' => 'form-label']) }}
                                                            {{ Form::file('meta_seo_image', ['class' => 'form-control']) }}
                                                        </div>
                                                    </div>
                                                    @if (!empty($settings['meta_seo_image']))
                                                    <div class="col-12 mt-20">
                                                        <img src="{{ asset(Storage::url('upload/seo')) . '/' . $settings['meta_seo_image'] }}"
                                                            class="setting-logo" alt="">
                                                    </div>
                                                    @endif
                                                </div>
                                                <div class="row mt-3">
                                                    <div class="col-6"></div>
                                                    <div class="col-6 text-end">
                                                        {{ Form::submit(__('Save'), ['class' => 'btn btn-secondary btn-rounded']) }}
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                            @endif


                            @if (Gate::check('manage twilio settings'))
                            <div class="tab-pane {{ !empty($activeTab) && $activeTab == 'twilio' ? ' active show ' : '' }}"
                                id="twilio" role="tabpanel" aria-labelledby="twilio">

                                {{ Form::model($settings, ['route' => ['setting.twilio'], 'method' => 'post']) }}

                                <div class="row">
                                    <div class="form-group col-md-12">
                                        {{ Form::label('twilio_sid', __('twilio SID'), ['class' => 'form-label']) }}
                                        {{ Form::text('twilio_sid', $settings['twilio_sid'], ['class' => 'form-control  mb-2', 'placeholder' => __('Enter SID')]) }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        {{ Form::label('twilio_token', __('twilio Token'), ['class' => 'form-label']) }}
                                        {{ Form::text('twilio_token', $settings['twilio_token'], ['class' => 'form-control  mb-2', 'placeholder' => __('Enter Token')]) }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        {{ Form::label('twilio_from_number', __('twilio From Number'), ['class' => 'form-label']) }}
                                        {{ Form::text('twilio_from_number', $settings['twilio_from_number'], ['class' => 'form-control  mb-2', 'placeholder' => __('Enter From Numner')]) }}
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-6"></div>
                                    <div class="col-6 text-end">
                                        {{ Form::submit(__('Save'), ['class' => 'btn btn-secondary btn-rounded']) }}
                                    </div>
                                </div>
                                {{ Form::close() }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

