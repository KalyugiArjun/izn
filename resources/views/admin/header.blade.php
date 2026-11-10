@php
    $users = \Auth::user();
    $languages = \App\Models\Custom::languages();
    $userLang = \Auth::user()->lang;
    $profile = asset(Storage::url('upload/profile'));
@endphp

<style>
    [data-pc-preset=preset-2] .pc-header .pc-head-link.head-link-secondary {
        background: rgba(216, 218, 221, 0.98) !important;
    }

    [data-pc-preset=preset-2] .pc-header .pc-head-link.head-link-secondary > i {
        color: #0f2c54fa !important;
    }

    .pc-header {
        background: rgba(15, 44, 84, 0.98) !important;
    }

    /* Profile dropdown link alignment */
    .pc-header .header-user-profile > a {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 4px !important;
        min-width: 0 !important;
        box-sizing: border-box !important;
    }

    /* Profile photo and initials avatar: fixed square size */
    .pc-header .header-user-profile .user-avtar,
    .pc-header .header-user-profile .initials-avatar {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;

        width: 40px !important;
        height: 40px !important;
        min-width: 40px !important;
        min-height: 40px !important;
        max-width: 40px !important;
        max-height: 40px !important;

        flex: 0 0 40px !important;
        aspect-ratio: 1 / 1 !important;
        border-radius: 50% !important;
        box-sizing: border-box !important;
    }

    /* Initials such as AD */
    .pc-header .header-user-profile .initials-avatar {
        padding: 0 !important;
        margin: 0 !important;
        line-height: 1 !important;
        overflow: hidden !important;
        white-space: nowrap !important;
        text-align: center !important;
        font-size: 12px !important;
        font-weight: 600 !important;
    }

    /* User icon alignment */
    .pc-header .header-user-profile > a > span:last-child {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
    }

    .pc-header .header-user-profile > a > span:last-child i {
        line-height: 1 !important;
    }

    /* Mobile responsive avatar */
    @media (max-width: 575px) {
        .pc-header .header-user-profile .user-avtar,
        .pc-header .header-user-profile .initials-avatar {
            width: 36px !important;
            height: 36px !important;
            min-width: 36px !important;
            min-height: 36px !important;
            max-width: 36px !important;
            max-height: 36px !important;
            flex-basis: 36px !important;
        }

        .pc-header .header-user-profile > a {
            padding: 4px !important;
            gap: 3px !important;
        }
    }
</style>

<header class="pc-header">
    <div class="header-wrapper">
        <!-- Mobile Media Block -->
        <div class="me-auto pc-mob-drp">
            <ul class="list-unstyled">
                <li class="pc-h-item header-mobile-collapse">
                    <a href="#" class="pc-head-link head-link-secondary ms-0" id="sidebar-hide">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>

                <li class="pc-h-item pc-sidebar-popup">
                    <a href="#" class="pc-head-link head-link-secondary ms-0" id="mobile-collapse">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
            </ul>
        </div>
        <!-- Mobile Media Block End -->

        <div class="ms-auto">
            <ul class="list-unstyled">

                <!-- Language Dropdown -->
                <li class="dropdown pc-h-item"
                    data-bs-toggle="tooltip"
                    data-bs-original-title="{{ __('Language') }}"
                    data-bs-placement="bottom">

                    <a class="pc-head-link head-link-primary dropdown-toggle arrow-none me-0"
                        data-bs-toggle="dropdown"
                        href="#"
                        role="button"
                        aria-haspopup="false"
                        aria-expanded="false">

                        <i class="ti ti-language"></i>
                    </a>

                    <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
                        @foreach ($languages as $language)
                            @if ($language != 'en')
                                <a href="{{ route('language.change', $language) }}"
                                    class="dropdown-item {{ $userLang == $language ? 'active' : '' }}">
                                    <span class="align-middle">
                                        {{ ucfirst($language) }}
                                    </span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </li>

                <!-- Theme Settings -->
                @if (\Auth::user()->type == 'super admin' || \Auth::user()->type == 'owner')
                    <li class="dropdown pc-h-item pc-mega-menu"
                        data-bs-toggle="tooltip"
                        data-bs-original-title="{{ __('Theme Settings') }}"
                        data-bs-placement="bottom">

                        <a href="#"
                            class="pc-head-link head-link-secondary dropdown-toggle arrow-none me-0"
                            data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvas_pc_layout">

                            <i class="ti ti-settings"></i>
                        </a>
                    </li>
                @endif

                <!-- User Profile -->
                <li class="dropdown pc-h-item header-user-profile">
                    <a class="pc-head-link head-link-primary dropdown-toggle arrow-none me-0"
                        data-bs-toggle="dropdown"
                        href="#"
                        role="button"
                        aria-haspopup="false"
                        aria-expanded="false">

                        @php
                            $profilePath = 'app/public/upload/profile/' . $users->profile;
                            $publicPath = 'storage/upload/profile/' . $users->profile;

                            $profileExists =
                                !empty($users->profile) &&
                                (
                                    file_exists(storage_path($profilePath)) ||
                                    file_exists(public_path($publicPath))
                                );
                        @endphp

                        @if ($profileExists)
                            <img src="{{ $profile . '/' . $users->profile }}"
                                alt="Profile"
                                class="user-avtar"
                                style="object-fit: cover;" />
                        @else
                            @php
                                $initials = $users->getInitials();
                                $colorIndex = (
                                    (ord($initials[0] ?? 'A') + ord($initials[1] ?? 'B')) % 8
                                ) + 1;
                            @endphp

                            <span class="initials-avatar bg-avatar-{{ $colorIndex }}">
                                {{ $initials }}
                            </span>
                        @endif

                        <span>
                            <i class="ti ti-user-check"></i>
                        </span>
                    </a>

                    <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                        <div class="dropdown-header">

                            <div class="profile-notification-scroll position-relative"
                                style="max-height: calc(100vh - 280px)">

                                {!! Form::open([
                                    'method' => 'DELETE',
                                    'route' => ['employee-profile.index']
                                ]) !!}

                                <a href="{{ route('employee-profile.index') }}"
                                    class="dropdown-item"
                                    data-actions="Account">
                                    <i class="ti ti-user"></i>
                                    <span>{{ __('Profile') }}</span>
                                </a>

                                {!! Form::close() !!}

                                <a href="{{ route('logout') }}"
                                    class="dropdown-item"
                                    onclick="event.preventDefault(); document.getElementById('frm-logout').submit();">

                                    <i class="ti ti-logout"></i>
                                    <span>{{ __('Logout') }}</span>

                                    <form id="frm-logout"
                                        action="{{ route('logout') }}"
                                        method="POST"
                                        class="d-none">
                                        {{ csrf_field() }}
                                    </form>
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
                <!-- User Profile End -->

            </ul>
        </div>
    </div>
</header>
