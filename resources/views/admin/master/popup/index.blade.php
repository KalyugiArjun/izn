
@extends('layouts.app')

@section('page-title', __('Popup Management'))

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">{{ __('Master') }}</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
        {{ __('Popup Management') }}
    </li>
@endsection

@section('content')
<div class="row popup-page">
    <div class="col-sm-12">
        <div class="card popup-card">

            {{-- Header --}}
            <div class="card-header popup-header">
                <div class="row align-items-center g-3">
                    <div class="col">
                        <div class="popup-heading-icon">
                            <i class="ti ti-layout-grid"></i>
                        </div>

                        <h5 class="mb-1">{{ __('Popup Management') }}</h5>

                        <p class="popup-description mb-0">
                            {{ __('Manage promotional popups displayed on your website.') }}
                        </p>
                    </div>

                    <div class="col-auto">
                        <a href="#"
                           class="btn btn-secondary customModal popup-create-btn"
                           data-size="lg"
                           data-url="{{ route('master.popup.create') }}"
                           data-title="{{ __('Create Popup') }}">
                            <i class="ti ti-circle-plus me-1"></i>
                            {{ __('Create Popup') }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- Body --}}
            <div class="card-body popup-body">

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show popup-alert"
                         role="alert">
                        <i class="ti ti-circle-check me-2"></i>
                        {{ session('success') }}

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="alert"
                                aria-label="{{ __('Close') }}"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show"
                         role="alert">
                        <strong>{{ __('Please check the following errors:') }}</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="alert"
                                aria-label="{{ __('Close') }}"></button>
                    </div>
                @endif

                {{-- Summary --}}
                <div class="popup-summary">
                    <div class="popup-summary-icon">
                        <i class="ti ti-photo"></i>
                    </div>

                    <div>
                        <span class="popup-summary-label">
                            {{ __('Total Popups') }}
                        </span>
                        <strong class="popup-summary-value">
                            {{ $popups->count() }}
                        </strong>
                    </div>
                </div>

                {{-- Table --}}
                <div class="table-responsive popup-table-wrapper">
                    <table class="table table-hover popup-table mb-0">
                        <thead>
                            <tr>
                                <th class="popup-col-number">#</th>
                                <th>{{ __('Image') }}</th>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Subtitle') }}</th>
                                <th>{{ __('Button') }}</th>
                                <th>{{ __('Schedule') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th class="text-end">{{ __('Action') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($popups as $index => $popup)
                                <tr>
                                    <td class="popup-number">
                                        {{ $index + 1 }}
                                    </td>

                                    {{-- Image --}}
                                    <td>
                                        @if ($popup->image)
                                            <a href="{{ $popup->image_url }}"
                                               target="_blank"
                                               rel="noopener noreferrer"
                                               class="popup-image-link"
                                               title="{{ __('View full image') }}">
                                                <div class="popup-image-box">
                                                    <img
                                                        src="{{ $popup->image_url }}"
                                                        alt="{{ $popup->title ?: __('Popup image') }}"
                                                        class="popup-image"
                                                        loading="lazy">
                                                </div>
                                            </a>
                                        @else
                                            <div class="popup-no-image">
                                                <i class="ti ti-photo-off"></i>
                                                <span>{{ __('No image') }}</span>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Title --}}
                                    <td>
                                        <span class="popup-title">
                                            {{ $popup->title ?: '-' }}
                                        </span>
                                    </td>

                                    {{-- Subtitle --}}
                                    <td>
                                        <span class="popup-subtitle">
                                            {{ $popup->subtitle
                                                ? \Illuminate\Support\Str::limit($popup->subtitle, 45)
                                                : '-' }}
                                        </span>
                                    </td>

                                    {{-- Button --}}
                                    <td>
                                        @if ($popup->button_text)
                                            <span class="popup-button-label">
                                                {{ $popup->button_text }}
                                            </span>
                                        @else
                                            <span class="popup-dash">-</span>
                                        @endif
                                    </td>

                                    {{-- Schedule --}}
                                    <td>
                                        <div class="popup-schedule">
                                            <div class="popup-schedule-item">
                                                <span class="popup-schedule-label">
                                                    <i class="ti ti-calendar-event"></i>
                                                    {{ __('From') }}
                                                </span>

                                                <span class="popup-schedule-value">
                                                    {{ $popup->starts_at
                                                        ? $popup->starts_at->format('d-m-Y H:i')
                                                        : __('Immediately') }}
                                                </span>
                                            </div>

                                            <div class="popup-schedule-item">
                                                <span class="popup-schedule-label">
                                                    <i class="ti ti-calendar-time"></i>
                                                    {{ __('To') }}
                                                </span>

                                                <span class="popup-schedule-value">
                                                    {{ $popup->ends_at
                                                        ? $popup->ends_at->format('d-m-Y H:i')
                                                        : __('No expiry') }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td>
                                        @if ($popup->status)
                                            <span class="popup-status popup-active">
                                                <span class="popup-status-dot"></span>
                                                {{ __('Active') }}
                                            </span>
                                        @else
                                            <span class="popup-status popup-inactive">
                                                <span class="popup-status-dot"></span>
                                                {{ __('Inactive') }}
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Actions --}}
                                    <td class="text-end">
                                        <div class="popup-actions">
                                            {!! Form::open([
                                                'method' => 'DELETE',
                                                'route' => [
                                                    'master.popup.destroy',
                                                    encrypt($popup->id),
                                                ],
                                            ]) !!}

                                            <a class="popup-action popup-edit customModal"
                                               data-bs-toggle="tooltip"
                                               data-size="lg"
                                               title="{{ __('Edit') }}"
                                               href="#"
                                               data-url="{{ route('master.popup.edit', encrypt($popup->id)) }}"
                                               data-title="{{ __('Edit Popup') }}">
                                                <i data-feather="edit"></i>
                                            </a>

                                            <a class="popup-action popup-delete confirm_dialog"
                                               data-bs-toggle="tooltip"
                                               title="{{ __('Delete') }}"
                                               href="#">
                                                <i data-feather="trash-2"></i>
                                            </a>

                                            {!! Form::close() !!}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        <div class="popup-empty">
                                            <div class="popup-empty-icon">
                                                <i class="ti ti-photo-off"></i>
                                            </div>

                                            <h6>{{ __('No popups found') }}</h6>

                                            <p>
                                                {{ __('Create your first popup to get started.') }}
                                            </p>

                                            <a href="#"
                                               class="btn btn-secondary customModal popup-empty-btn"
                                               data-size="lg"
                                               data-url="{{ route('master.popup.create') }}"
                                               data-title="{{ __('Create Popup') }}">
                                                <i class="ti ti-plus me-1"></i>
                                                {{ __('Create Popup') }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="popup-footer-note">
                    <i class="ti ti-info-circle"></i>
                    {{ __('Click a popup image to view it in full size.') }}
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    .popup-page {
        --popup-accent: #5b50d6;
        --popup-accent-dark: #463ab8;
        --popup-border: #e6e8f0;
        --popup-text: #292d45;
        --popup-muted: #7b8195;
        --popup-surface: #f7f8fc;
    }

    .popup-card {
        overflow: hidden;
        border: 1px solid var(--popup-border);
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 8px 30px rgba(37, 41, 67, .055);
    }

    /* Header */
    .popup-header {
        padding: 24px;
        background: linear-gradient(120deg, #fff 35%, #f5f3ff 100%);
        border-bottom: 1px solid var(--popup-border);
    }

    .popup-heading-icon {
        display: inline-flex;
        width: 38px;
        height: 38px;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        border: 1px solid #e3dfff;
        border-radius: 11px;
        background: #efedff;
        color: var(--popup-accent);
        font-size: 21px;
    }

    .popup-header h5 {
        color: var(--popup-text);
        font-size: 19px;
        font-weight: 750;
        letter-spacing: -.35px;
    }

    .popup-description {
        color: var(--popup-muted);
        font-size: 12px;
        line-height: 1.8;
    }

    .popup-create-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 11px 17px;
        border: 0;
        border-radius: 10px;
        background: linear-gradient(135deg, #7065e8, #5145bd);
        box-shadow: 0 5px 13px rgba(91, 80, 214, .2);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .popup-create-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(91, 80, 214, .25);
        background: var(--popup-accent-dark);
        color: #fff;
    }

    /* Main body */
    .popup-body {
        padding: 24px;
    }

    .popup-alert {
        border-radius: 10px;
        font-size: 13px;
    }

    /* Total count */
    .popup-summary {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        min-width: 170px;
        margin-bottom: 20px;
        padding: 13px 17px;
        border: 1px solid #e7e3ff;
        border-radius: 12px;
        background: linear-gradient(135deg, #faf9ff, #f1efff);
    }

    .popup-summary-icon {
        display: flex;
        width: 40px;
        height: 40px;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #e7e2ff;
        color: var(--popup-accent);
        font-size: 21px;
    }

    .popup-summary-label {
        display: block;
        margin-bottom: 3px;
        color: var(--popup-muted);
        font-size: 11px;
        font-weight: 600;
    }

    .popup-summary-value {
        display: block;
        color: var(--popup-text);
        font-size: 21px;
        font-weight: 800;
        line-height: 1.2;
    }

    /* Table */
    .popup-table-wrapper {
        overflow-x: auto;
        border: 1px solid var(--popup-border);
        border-radius: 12px;
        background: #fff;
    }

    .popup-table {
        min-width: 1050px;
    }

    .popup-table thead th {
        padding: 16px 14px;
        background: #f7f8fc;
        border-bottom: 1px solid var(--popup-border);
        color: #747a8d;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .55px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .popup-table tbody td {
        padding: 16px 14px;
        border-bottom: 1px solid #eff0f5;
        color: var(--popup-text);
        font-size: 12px;
        vertical-align: middle;
    }

    .popup-table tbody tr {
        transition: background .2s ease;
    }

    .popup-table tbody tr:hover {
        background: #fafaff;
    }

    .popup-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .popup-col-number {
        width: 48px;
    }

    .popup-number {
        color: var(--popup-muted) !important;
        font-weight: 700;
    }

    /* Image preview */
    .popup-image-link {
        display: inline-block;
        text-decoration: none;
    }

    .popup-image-box {
        display: flex;
        width: 150px;
        height: 100px;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border: 1px solid #e1e3ef;
        border-radius: 12px;
        background:
            linear-gradient(45deg, #f2f3f8 25%, transparent 25%),
            linear-gradient(-45deg, #f2f3f8 25%, transparent 25%),
            linear-gradient(45deg, transparent 75%, #f2f3f8 75%),
            linear-gradient(-45deg, transparent 75%, #f2f3f8 75%),
            #fff;
        background-size: 16px 16px;
        background-position: 0 0, 0 8px, 8px -8px, -8px 0;
        box-shadow: 0 3px 10px rgba(37, 41, 67, .06);
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .popup-image {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
        transition: transform .25s ease;
    }

    .popup-image-link:hover .popup-image-box {
        border-color: #bcb4ff;
        box-shadow: 0 5px 15px rgba(91, 80, 214, .12);
    }

    .popup-image-link:hover .popup-image {
        transform: scale(1.04);
    }

    .popup-no-image {
        display: inline-flex;
        width: 110px;
        height: 75px;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 5px;
        border: 1px dashed #d7d9e7;
        border-radius: 10px;
        background: #f8f9fc;
        color: #8b91a4;
        font-size: 10px;
    }

    .popup-no-image i {
        font-size: 23px;
    }

    /* Text labels */
    .popup-title {
        display: inline-block;
        max-width: 180px;
        color: #292d45;
        font-size: 12px;
        font-weight: 750;
        overflow-wrap: anywhere;
    }

    .popup-subtitle {
        display: inline-block;
        max-width: 190px;
        color: var(--popup-muted);
        font-size: 12px;
        line-height: 1.8;
        overflow-wrap: anywhere;
    }

    .popup-dash {
        color: #a0a5b4;
    }

    .popup-button-label {
        display: inline-block;
        max-width: 140px;
        padding: 7px 10px;
        overflow: hidden;
        border: 1px solid #e3dfff;
        border-radius: 8px;
        background: #f4f2ff;
        color: #594dc7;
        font-size: 11px;
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Schedule */
    .popup-schedule {
        display: flex;
        min-width: 145px;
        flex-direction: column;
        gap: 11px;
    }

    .popup-schedule-item {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }

    .popup-schedule-label {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #9297a8;
        font-size: 10px;
        font-weight: 750;
        text-transform: uppercase;
    }

    .popup-schedule-label i {
        font-size: 13px;
    }

    .popup-schedule-value {
        color: #525970;
        font-size: 11px;
        font-weight: 600;
    }

    /* Status */
    .popup-status {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 11px;
        border: 1px solid transparent;
        border-radius: 30px;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }

    .popup-active {
        border-color: #c9ecd5;
        background: #edfaf2;
        color: #21834a;
    }

    .popup-inactive {
        border-color: #f5d6da;
        background: #fff1f2;
        color: #c63e4d;
    }

    .popup-status-dot {
        width: 7px;
        height: 7px;
        flex: 0 0 7px;
        border-radius: 50%;
        background: currentColor;
    }

    /* Action buttons */
    .popup-actions {
        display: inline-flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
    }

    .popup-action {
        display: inline-flex;
        width: 36px;
        height: 36px;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--popup-border);
        border-radius: 10px;
        background: #fff;
        text-decoration: none;
        transition: transform .2s ease, background .2s ease;
    }

    .popup-action svg {
        width: 16px;
        height: 16px;
    }

    .popup-edit {
        color: var(--popup-accent) !important;
    }

    .popup-edit:hover {
        transform: translateY(-1px);
        border-color: #dcd6ff;
        background: #f4f2ff;
    }

    .popup-delete {
        color: #d34454 !important;
    }

    .popup-delete:hover {
        transform: translateY(-1px);
        border-color: #ffd4d9;
        background: #fff0f2;
    }

    /* Empty state */
    .popup-empty {
        padding: 45px 15px;
        text-align: center;
    }

    .popup-empty-icon {
        display: inline-flex;
        width: 64px;
        height: 64px;
        align-items: center;
        justify-content: center;
        border-radius: 18px;
        background: #f0edff;
        color: #8779e4;
        font-size: 31px;
    }

    .popup-empty h6 {
        margin-top: 15px;
        color: var(--popup-text);
        font-size: 15px;
        font-weight: 750;
    }

    .popup-empty p {
        margin-bottom: 17px;
        color: var(--popup-muted);
        font-size: 12px;
    }

    .popup-empty-btn {
        border: 0;
        border-radius: 9px;
        background: var(--popup-accent);
        font-size: 12px;
        font-weight: 700;
    }

    .popup-empty-btn:hover {
        background: var(--popup-accent-dark);
        color: #fff;
    }

    /* Footer hint */
    .popup-footer-note {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-top: 15px;
        color: #8a90a2;
        font-size: 11px;
    }

    .popup-footer-note i {
        color: var(--popup-accent);
        font-size: 15px;
    }

    /* Responsive */
    @media (max-width: 767px) {
        .popup-header,
        .popup-body {
            padding: 16px;
        }

        .popup-header .col-auto {
            width: 100%;
        }

        .popup-create-btn {
            width: 100%;
        }

        .popup-image-box {
            width: 120px;
            height: 80px;
        }

        .popup-summary {
            width: 100%;
        }
    }
</style>
@endsection
