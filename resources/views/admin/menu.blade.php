@php
    $admin_logo = getSettingsValByName('company_logo');
    $ids = parentId();
    $authUser = $ids ? \App\Models\User::find($ids) : null;
    $subscription =
        $authUser && $authUser->subscription ? \App\Models\Subscription::find($authUser->subscription) : null;
    $routeName = \Request::route() ? \Request::route()->getName() : '';
    $pricing_feature_settings = getSettingsValByIdName(1, 'pricing_feature');

    $theme_mode = getSettingsValByName('theme_mode');
    $light_logo = getSettingsValByName('light_logo');
    if (auth()->check() && auth()->user() && auth()->user()->type != 'super admin') {
        $light_logo = getSettingsValByName('company_light_logo');
    }

    $isPlanningWork = false;
    $isPh16Auth = false;
    $isTI = false;
    $isTIHQ = false;
    if (auth()->check()) {
        $user = auth()->user();
        $isPlanningWork = $user->isPlanningWork();
        $isPh16Auth = $user->isPh16Authorized();

        $designationName = '';
        if ($user->designationData) {
            $designationName = strtoupper($user->designationData->name);
        }
        if (
            str_contains($designationName, 'TRAFFIC INSPECTOR HQ') ||
            str_contains($designationName, 'TI HQ') ||
            str_contains($designationName, 'TIHQ')
        ) {
            $isTIHQ = true;
        } elseif (strtolower($user->type) === 'emp' || strtolower($user->type) === 'employee') {
            if (str_contains($designationName, 'TRAFFIC INSPECTOR') || str_contains($designationName, 'TI')) {
                $isTI = true;
            }
        }
    }
@endphp


<style>
    .pc-sidebar .pc-navbar>.pc-item.active>.pc-link:after {
        background: rgba(216, 218, 221, 0.98) !important;
    }

    [data-pc-preset=preset-2] .pc-sidebar .pc-item.active>.pc-link,
    [data-pc-preset=preset-2] .pc-sidebar .pc-item:focus>.pc-link,
    [data-pc-preset=preset-2] .pc-sidebar .pc-item:hover>.pc-link {
        color: #d8daddfa !important;
    }

    [data-pc-preset=preset-2] .pc-sidebar .pc-item.active>.pc-link .pc-micon i,
    [data-pc-preset=preset-2] .pc-sidebar .pc-item.active>.pc-link .pc-micon svg,
    [data-pc-preset=preset-2] .pc-sidebar .pc-item:focus>.pc-link .pc-micon i,
    [data-pc-preset=preset-2] .pc-sidebar .pc-item:focus>.pc-link .pc-micon svg,
    [data-pc-preset=preset-2] .pc-sidebar .pc-item:hover>.pc-link .pc-micon i,
    [data-pc-preset=preset-2] .pc-sidebar .pc-item:hover>.pc-link .pc-micon svg {
        color: #d8daddfa !important;
    }

    .pc-sidebar .pc-link {
        color: #d8daddfa !important;
        display: flex !important;
        align-items: center !important;
        padding: 14px 20px !important;
    }

    .pc-sidebar .pc-mtext {
        line-height: 1.4 !important;
        font-size: 13.5px !important;
        white-space: normal !important;
        display: inline-block !important;
        vertical-align: middle !important;
    }

    .pc-sidebar .pc-micon {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin-right: 12px !important;
        min-width: 20px;
    }

    .pc-sidebar .pc-navbar>.pc-item:hover:not(.active)>.pc-link:after {
        background: rgba(216, 218, 221, 0.98) !important;
    }

    .pc-sidebar .pc-navbar>.pc-item .pc-submenu .pc-item>.pc-link:after {
        background: rgba(216, 218, 221, 0.98) !important;

    }

    .pc-sidebar {
        background: rgba(15, 44, 84, 0.98) !important;
    }

    .pc-sidebar .pc-caption {
        color: #d8daddfa !important;
    }
    
    /* Consistent icons for all sidebar menus and submenus */
    .pc-sidebar .pc-micon {
        width: 22px !important;
        min-width: 22px !important;
        height: 22px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin-right: 10px !important;
        flex-shrink: 0 !important;
    }

    .pc-sidebar .pc-micon i {
        font-size: 17px !important;
        line-height: 1 !important;
    }

    .pc-sidebar .pc-submenu .pc-link {
        display: flex !important;
        align-items: center !important;
        gap: 0 !important;
    }

    .pc-sidebar .pc-submenu .pc-micon {
        margin-right: 9px !important;
    }

    /* Active item highlight */
    .pc-sidebar .pc-item.active > .pc-link .pc-micon,
    .pc-sidebar .pc-item.active > .pc-link .pc-mtext {
        color: #25e5e5 !important;
    }

/* Colorful Outline Sidebar Icons */
.pc-sidebar .pc-micon {
    width: 22px !important;
    min-width: 22px !important;
    height: 22px !important;
    margin-right: 10px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: transparent !important;
    border-radius: 0 !important;
}

/* Outline icons */
.pc-sidebar .pc-micon i {
    font-size: 18px !important;
    color: #60A5FA !important;
    -webkit-text-stroke: 0.2px currentColor;
    transition: color 0.2s ease;
}

/* Different professional outline colors */
.pc-sidebar .pc-item:nth-child(6n+1) .pc-micon i {
    color: #60A5FA !important;
}

.pc-sidebar .pc-item:nth-child(6n+2) .pc-micon i {
    color: #34D399 !important;
}

.pc-sidebar .pc-item:nth-child(6n+3) .pc-micon i {
    color: #FBBF24 !important;
}

.pc-sidebar .pc-item:nth-child(6n+4) .pc-micon i {
    color: #C4B5FD !important;
}

.pc-sidebar .pc-item:nth-child(6n+5) .pc-micon i {
    color: #F9A8D4 !important;
}

.pc-sidebar .pc-item:nth-child(6n) .pc-micon i {
    color: #22D3EE !important;
}

/* SVG outline icons */
.pc-sidebar .pc-micon svg {
    width: 18px !important;
    height: 18px !important;
    fill: none !important;
    stroke: #60A5FA !important;
    stroke-width: 1.8px !important;
}

/* Active menu highlight */
.pc-sidebar .pc-item.active > .pc-link .pc-micon i,
.pc-sidebar .pc-item.active > .pc-link .pc-micon svg {
    color: #25E5E5 !important;
    stroke: #25E5E5 !important;
}

/* No background box or movement */
.pc-sidebar .pc-item:hover > .pc-link .pc-micon {
    background: transparent !important;
    transform: none !important;
}

    /* Keep submenu icons aligned when labels wrap */
    .pc-sidebar .pc-submenu .pc-mtext {
        flex: 1;
        min-width: 0;
    }
/* Hide unwanted default bullet dots in submenus */
.pc-sidebar .pc-submenu .pc-item > .pc-link:before {
    display: none !important;
}

/* Sidebar icons visibility and alignment */
.pc-sidebar .pc-micon {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 22px !important;
    min-width: 22px !important;
    height: 22px !important;
    margin-right: 9px !important;
    flex-shrink: 0 !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.pc-sidebar .pc-micon i {
    display: inline-block !important;
    font-size: 17px !important;
    line-height: 1 !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.pc-sidebar .pc-micon svg {
    display: block !important;
    width: 18px !important;
    height: 18px !important;
    flex-shrink: 0 !important;
    visibility: visible !important;
    opacity: 1 !important;
}


    /* Sidebar smooth scrolling */
    .navbar-content,
    .navbar-content .simplebar-content-wrapper {
        scroll-behavior: smooth !important;
    }

    /* Sidebar items smooth transitions */
    .pc-sidebar .pc-link {
        transition: background 0.15s ease, color 0.15s ease, transform 0.15s ease !important;
    }
    .pc-sidebar .pc-item {
        transition: background 0.15s ease !important;
    }
    .pc-sidebar .pc-submenu {
        transition: opacity 0.2s ease;
    }

</style>

<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="#" class="b-brand text-primary">
                @if ($theme_mode == 'dark')
                    <img src="{{ asset(Storage::url('upload/logo/')) . '/' . (isset($light_logo) && !empty($light_logo) ? $light_logo : 'logo.png') }}"
                        alt="" class="logo logo-lg" />
                @else
                    <img src="{{ asset(Storage::url('upload/logo')) . '/' . (isset($admin_logo) && !empty($admin_logo) ? $admin_logo : 'logo.png') }}"
                        alt="" style="height: 75px;" class="logo logo-lg" />
                @endif
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">
                @if ($isPlanningWork || $isPh16Auth)
                    <li class="pc-item pc-caption">
                        <label>{{ __('Home') }}</label>
                        <i class="ti ti-dashboard"></i>
                    </li>
                    <li class="pc-item {{ in_array($routeName, ['dashboard', 'home', '']) ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-home"></i></span>
                            <span class="pc-mtext">{{ __('Dashboard') }}</span>
                        </a>
                    </li>
                    <li class="pc-item {{ Str::startsWith($routeName, 'employee-profile') ? 'active' : '' }}">
                        <a href="{{ route('employee-profile.index') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-user"></i></span>
                            <span class="pc-mtext">{{ __('My Profile') }}</span>
                        </a>
                    </li>

                    <li class="pc-item pc-caption">
                        <label>{{ __('PH 16 Works') }}</label>
                        <i class="ti ti-briefcase"></i>
                    </li>

                    @if ($isPlanningWork)
                        {{-- 1. New Work --}}
                        <li class="pc-item {{ Str::startsWith($routeName, 'works-paperwork-progress-register.identify-new-work') ? 'active' : '' }}">
                            <a href="{{ route('works-paperwork-progress-register.identify-new-work.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-file-plus"></i></span>
                                <span class="pc-mtext">{{ __('New Work') }}</span>
                            </a>
                        </li>

                        {{-- 2. Paperwork timeline --}}
                        <li class="pc-item pc-hasmenu {{ Str::startsWith($routeName, 'works-paperwork-progress-register.joint-survey') || Str::startsWith($routeName, 'works-paperwork-progress-register.estimates') || Str::startsWith($routeName, 'works-paperwork-progress-register.review-estimate') || Str::startsWith($routeName, 'works-paperwork-progress-register.approval-listing') || Str::startsWith($routeName, 'works-paperwork-progress-register.finance-vetting-sanction') || Str::startsWith($routeName, 'works-paperwork-progress-register.tendering') || Str::startsWith($routeName, 'works-paperwork-progress-register.esp-sip.esp') || Str::startsWith($routeName, 'works-paperwork-progress-register.esp-sip.sip') || Str::startsWith($routeName, 'works-paperwork-progress-register.execution') || Str::startsWith($routeName, 'works-paperwork-progress-register.executionend') ? 'active pc-trigger' : '' }}">
                            <a href="javascript:void(0);" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-history"></i></span>
                                <span class="pc-mtext">{{ __('Paperwork timeline') }}</span>
                                <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                            </a>

                            <ul class="pc-submenu" style="display: {{ Str::startsWith($routeName, 'works-paperwork-progress-register.joint-survey') || Str::startsWith($routeName, 'works-paperwork-progress-register.estimates') || Str::startsWith($routeName, 'works-paperwork-progress-register.review-estimate') || Str::startsWith($routeName, 'works-paperwork-progress-register.approval-listing') || Str::startsWith($routeName, 'works-paperwork-progress-register.finance-vetting-sanction') || Str::startsWith($routeName, 'works-paperwork-progress-register.tendering') || Str::startsWith($routeName, 'works-paperwork-progress-register.esp-sip.esp') || Str::startsWith($routeName, 'works-paperwork-progress-register.esp-sip.sip') || Str::startsWith($routeName, 'works-paperwork-progress-register.execution') || Str::startsWith($routeName, 'works-paperwork-progress-register.executionend') ? 'block' : 'none' }}">
                                <li class="pc-item {{ $routeName == 'works-paperwork-progress-register.joint-survey.index' ? 'active' : '' }}">
                                    <a href="{{ route('works-paperwork-progress-register.joint-survey.index') }}" class="pc-link">{{ __('Joint Survey') }}</a>
                                </li>
                                <li class="pc-item {{ $routeName == 'works-paperwork-progress-register.estimates.index' ? 'active' : '' }}">
                                    <a href="{{ route('works-paperwork-progress-register.estimates.index') }}" class="pc-link">{{ __('Estimates') }}</a>
                                </li>
                                <li class="pc-item {{ $routeName == 'works-paperwork-progress-register.review-estimate.index' ? 'active' : '' }}">
                                    <a href="{{ route('works-paperwork-progress-register.review-estimate.index') }}" class="pc-link">{{ __('Review Estimates') }}</a>
                                </li>
                                <li class="pc-item {{ $routeName == 'works-paperwork-progress-register.approval-listing.index' ? 'active' : '' }}">
                                    <a href="{{ route('works-paperwork-progress-register.approval-listing.index') }}" class="pc-link">{{ __('Approval & Listing') }}</a>
                                </li>
                                <li class="pc-item {{ $routeName == 'works-paperwork-progress-register.finance-vetting-sanction.index' ? 'active' : '' }}">
                                    <a href="{{ route('works-paperwork-progress-register.finance-vetting-sanction.index') }}" class="pc-link">{{ __('Finance Vetting & Sanction') }}</a>
                                </li>
                                <li class="pc-item {{ $routeName == 'works-paperwork-progress-register.tendering.index' ? 'active' : '' }}">
                                    <a href="{{ route('works-paperwork-progress-register.tendering.index') }}" class="pc-link">{{ __('Tendering') }}</a>
                                </li>
                                <li class="pc-item {{ $routeName == 'works-paperwork-progress-register.esp-sip.esp.index' ? 'active' : '' }}">
                                    <a href="{{ route('works-paperwork-progress-register.esp-sip.esp.index') }}" class="pc-link">{{ __('ESP') }}</a>
                                </li>
                                <li class="pc-item {{ $routeName == 'works-paperwork-progress-register.esp-sip.sip.index' ? 'active' : '' }}">
                                    <a href="{{ route('works-paperwork-progress-register.esp-sip.sip.index') }}" class="pc-link">{{ __('Sip') }}</a>
                                </li>
                                <li class="pc-item {{ $routeName == 'works-paperwork-progress-register.execution.index' ? 'active' : '' }}">
                                    <a href="{{ route('works-paperwork-progress-register.execution.index') }}" class="pc-link">{{ __('Execution Start') }}</a>
                                </li>
                                <li class="pc-item {{ $routeName == 'works-paperwork-progress-register.executionend.index' ? 'active' : '' }}">
                                    <a href="{{ route('works-paperwork-progress-register.executionend.index') }}" class="pc-link">{{ __('Execution End') }}</a>
                                </li>
                            </ul>
                        </li>

                        {{-- 3. Variation --}}
                        <li class="pc-item {{ Str::startsWith($routeName, 'works-paperwork-progress-register.variation') ? 'active' : '' }}">
                            <a href="{{ route('works-paperwork-progress-register.variation.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-git-fork"></i></span>
                                <span class="pc-mtext">{{ __('Variation') }}</span>
                            </a>
                        </li>
                    @endif

                    @if ($isPh16Auth)
                        {{-- 4. Inspection Log --}}
                        <li class="pc-item {{ Str::startsWith($routeName, 'works-paperwork-progress-register.inspection-log') ? 'active' : '' }}">
                            <a href="{{ route('works-paperwork-progress-register.inspection-log.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-clipboard-list"></i></span>
                                <span class="pc-mtext">{{ __('Inspection Log') }}</span>
                            </a>
                        </li>
                    @endif

                    @if (\Auth::check() && (\Auth::user()->isTiSafety() || \Auth::user()->isAwardNominationAuthorized()))
                        @php
                            $tiPendingCount = \App\Models\SelfNomination::where('status', 0)->count();
                            $tiForwardedCount = \App\Models\SelfNomination::where('status', '>=', 1)->count();
                            $tiActiveTab = request('tab', 'pending');
                            $isTiAwardActive = Str::startsWith($routeName, 'authority.award-nomination');
                        @endphp
                        <li class="pc-item pc-caption">
                            <label>{{ __('Award Nomination') }}</label>
                            <i class="ti ti-award"></i>
                        </li>
                        <li class="pc-item pc-hasmenu {{ $isTiAwardActive ? 'pc-trigger active' : '' }}">
                            <a href="#!" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-award"></i></span>
                                <span class="pc-mtext">{{ __('Award Nomination') }}</span>
                                <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                            </a>
                            <ul class="pc-submenu" style="display: {{ $isTiAwardActive ? 'block' : 'none' }};">
                                <li class="pc-item {{ ($isTiAwardActive && $tiActiveTab === 'pending') ? 'active' : '' }}">
                                    <a href="{{ route('authority.award-nomination.index', ['tab' => 'pending']) }}" class="pc-link">
                                        <span class="pc-mtext">{{ __('1. Pending Nominations') }}</span>
                                        <span class="badge bg-warning text-dark rounded-pill ms-auto">{{ $tiPendingCount }}</span>
                                    </a>
                                </li>
                                <li class="pc-item {{ ($isTiAwardActive && $tiActiveTab === 'forwarded') ? 'active' : '' }}">
                                    <a href="{{ route('authority.award-nomination.index', ['tab' => 'forwarded']) }}" class="pc-link">
                                        <span class="pc-mtext">{{ __('2. Forwarded Nominations') }}</span>
                                        <span class="badge bg-info text-white rounded-pill ms-auto">{{ $tiForwardedCount }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    
                @elseif ($isTI)
   <li class="pc-item pc-caption">
    <span class="pc-micon">
        <i class="ti ti-home"></i>
    </span>
    <label>{{ __('Home') }}</label>
</li>
                    <li class="pc-item {{ in_array($routeName, ['dashboard', 'home', '']) ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-home"></i></span>
                            <span class="pc-mtext">{{ __('Dashboard') }}</span>
                        </a>
                    </li>
                    <li class="pc-item {{ Str::startsWith($routeName, 'employee-profile') ? 'active' : '' }}">
                        <a href="{{ route('employee-profile.index') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-user"></i></span>
                            <span class="pc-mtext">{{ __('My Profile') }}</span>
                        </a>
                    </li>
                    <li class="pc-item pc-caption">
                        <label>{{ __('Traffic Inspector') }}</label>
                        <i class="ti ti-clipboard-list"></i>
                    </li>
                    <li class="pc-item {{ Str::startsWith($routeName, 'report.employee.safety') || Str::startsWith($routeName, 'report.employee-safety') ? 'active' : '' }}">
                        <a href="{{ route('report.employee-safety.index') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-shield-check"></i></span>
                            <span class="pc-mtext">{{ __('Employee Safety Record') }}</span>
                        </a>
                    </li>
                    <li
                        class="pc-item {{ Str::startsWith($routeName, 'master.inspection') && !Str::startsWith($routeName, 'master.ti_daily_inspection') ? 'active' : '' }}">
                        <a href="{{ route('master.inspection.index') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-list"></i></span>
                            <span class="pc-mtext">{{ __('Inspection List') }}</span>
                        </a>
                    </li>
                    <li
                        class="pc-item {{ Str::startsWith($routeName, 'master.ti_daily_inspection') ? 'active' : '' }}">
                        <a href="{{ route('master.ti_daily_inspection.index') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-clipboard-list"></i></span>
                            <span class="pc-mtext">{{ __('TI Daily Movement Cum Inspection') }}</span>
                        </a>
                    </li>
                    @if (\Auth::check() && (\Auth::user()->isTiSafety() || \Auth::user()->isAwardNominationAuthorized()))
                        @php
                            $tiPendingCount = \App\Models\SelfNomination::where('status', 0)->count();
                            $tiForwardedCount = \App\Models\SelfNomination::where('status', '>=', 1)->count();
                            $tiActiveTab = request('tab', 'pending');
                            $isTiAwardActive = Str::startsWith($routeName, 'authority.award-nomination');
                        @endphp
                        <li class="pc-item pc-hasmenu {{ $isTiAwardActive ? 'pc-trigger active' : '' }}">
                            <a href="#!" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-award"></i></span>
                                <span class="pc-mtext">{{ __('Award Nomination') }}</span>
                                <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                            </a>
                            <ul class="pc-submenu" style="display: {{ $isTiAwardActive ? 'block' : 'none' }};">
                                <li class="pc-item {{ ($isTiAwardActive && $tiActiveTab === 'pending') ? 'active' : '' }}">
                                    <a href="{{ route('authority.award-nomination.index', ['tab' => 'pending']) }}" class="pc-link">
                                        <span class="pc-mtext">{{ __('1. Pending Nominations') }}</span>
                                        <span class="badge bg-warning text-dark rounded-pill ms-auto">{{ $tiPendingCount }}</span>
                                    </a>
                                </li>
                                <li class="pc-item {{ ($isTiAwardActive && $tiActiveTab === 'forwarded') ? 'active' : '' }}">
                                    <a href="{{ route('authority.award-nomination.index', ['tab' => 'forwarded']) }}" class="pc-link">
                                        <span class="pc-mtext">{{ __('2. Forwarded Nominations') }}</span>
                                        <span class="badge bg-info text-white rounded-pill ms-auto">{{ $tiForwardedCount }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif
                @else
                    <li class="pc-item pc-caption">
                        <label>{{ __('Home') }}</label>
                        <i class="ti ti-dashboard"></i>
                    </li>
                    <li class="pc-item {{ in_array($routeName, ['dashboard', 'home', '']) ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-home"></i></span>
                            <span class="pc-mtext">{{ __('Dashboard') }}</span>
                        </a>
                    </li>
                    <li class="pc-item {{ Str::startsWith($routeName, 'employee-profile') ? 'active' : '' }}">
                        <a href="{{ route('employee-profile.index') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-user"></i></span>
                            <span class="pc-mtext">{{ __('My Profile') }}</span>
                        </a>
                    </li>

                    <li class="pc-item {{ in_array($routeName, ['charts-dashboard']) ? 'active' : '' }}">
                        <a href="{{ route('charts-dashboard') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-chart-bar"></i></span>
                            <span class="pc-mtext">{{ __('Charts Dashboard') }}</span>
                        </a>
                    </li>

                    <li class="pc-item {{ in_array($routeName, ['dashboard-by-compliments', '']) ? 'active' : '' }}">
                        <a href="{{ route('dashboard-by-compliments') }}" class="pc-link">
                            <span class="pc-micon"><i class="ti ti-alert-circle"></i></span>
                            <span class="pc-mtext">{{ __('Stations Dashboard') }}</span>
                        </a>
                    </li>
                    <li class="pc-item {{ in_array($routeName, ['dashboard-by-calendar', '']) ? 'active' : '' }}">
                        <a href="{{ route('dashboard-by-calendar') }}" class="pc-link">
                            <span class="pc-micon"><i data-feather="calendar"></i></span>
                            <span class="pc-mtext">{{ __('Calendar Dashboard') }}</span>
                        </a>
                    </li>
                    @if (\Auth::check() && \Auth::user()->isSrDom() && !in_array(strtolower(\Auth::user()->type ?? ''), ['super admin', 'owner', 'admin']))
                        @php
                            $srdomPendingCount = \App\Models\SelfNomination::where('status', 1)->count();
                            $srdomApprovedCount = \App\Models\SelfNomination::where('status', 2)->count();
                            $srdomRejectedCount = \App\Models\SelfNomination::where('status', 3)->count();
                            $srdomActiveTab = request('tab', 'pending');
                            $isDomAwardActive = Str::startsWith($routeName, 'authority.award-nomination');
                        @endphp
                        <li class="pc-item pc-hasmenu {{ $isDomAwardActive ? 'pc-trigger active' : '' }}">
                            <a href="#!" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-award"></i></span>
                                <span class="pc-mtext">{{ __('Award Nomination') }}</span>
                                <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                            </a>
                            <ul class="pc-submenu" style="display: {{ $isDomAwardActive ? 'block' : 'none' }};">
                                <li class="pc-item {{ ($isDomAwardActive && $srdomActiveTab === 'pending') ? 'active' : '' }}">
                                    <a href="{{ route('authority.award-nomination.index', ['tab' => 'pending']) }}" class="pc-link">
                                        <span class="pc-mtext">{{ __('1. Forwarded Nominations') }}</span>
                                        <span class="badge bg-warning text-dark rounded-pill ms-auto">{{ $srdomPendingCount }}</span>
                                    </a>
                                </li>
                                <li class="pc-item {{ ($isDomAwardActive && $srdomActiveTab === 'approved') ? 'active' : '' }}">
                                    <a href="{{ route('authority.award-nomination.index', ['tab' => 'approved']) }}" class="pc-link">
                                        <span class="pc-mtext">{{ __('2. Approved Nominations') }}</span>
                                        <span class="badge bg-success text-white rounded-pill ms-auto">{{ $srdomApprovedCount }}</span>
                                    </a>
                                </li>
                                <li class="pc-item {{ ($isDomAwardActive && $srdomActiveTab === 'rejected') ? 'active' : '' }}">
                                    <a href="{{ route('authority.award-nomination.index', ['tab' => 'rejected']) }}" class="pc-link">
                                        <span class="pc-mtext">{{ __('3. Rejected Nominations') }}</span>
                                        <span class="badge bg-danger text-white rounded-pill ms-auto">{{ $srdomRejectedCount }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @elseif (\Auth::check() && \Auth::user()->isTiSafety() && !in_array(strtolower(\Auth::user()->type ?? ''), ['super admin', 'owner', 'admin']))
                        @php
                            $tiPendingCount = \App\Models\SelfNomination::where('status', 0)->count();
                            $tiForwardedCount = \App\Models\SelfNomination::where('status', '>=', 1)->count();
                            $tiActiveTab = request('tab', 'pending');
                            $isTiAwardActive = Str::startsWith($routeName, 'authority.award-nomination');
                        @endphp
                        <li class="pc-item pc-hasmenu {{ $isTiAwardActive ? 'pc-trigger active' : '' }}">
                            <a href="#!" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-award"></i></span>
                                <span class="pc-mtext">{{ __('Award Nomination') }}</span>
                                <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                            </a>
                            <ul class="pc-submenu" style="display: {{ $isTiAwardActive ? 'block' : 'none' }};">
                                <li class="pc-item {{ ($isTiAwardActive && $tiActiveTab === 'pending') ? 'active' : '' }}">
                                    <a href="{{ route('authority.award-nomination.index', ['tab' => 'pending']) }}" class="pc-link">
                                        <span class="pc-mtext">{{ __('1. Pending Nominations') }}</span>
                                        <span class="badge bg-warning text-dark rounded-pill ms-auto">{{ $tiPendingCount }}</span>
                                    </a>
                                </li>
                                <li class="pc-item {{ ($isTiAwardActive && $tiActiveTab === 'forwarded') ? 'active' : '' }}">
                                    <a href="{{ route('authority.award-nomination.index', ['tab' => 'forwarded']) }}" class="pc-link">
                                        <span class="pc-mtext">{{ __('2. Forwarded Nominations') }}</span>
                                        <span class="badge bg-info text-white rounded-pill ms-auto">{{ $tiForwardedCount }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @elseif (\Auth::check() && \Auth::user()->isAwardNominationAuthorized() && !in_array(strtolower(\Auth::user()->type ?? ''), ['super admin', 'owner', 'admin']))
                        <li class="pc-item {{ Str::startsWith($routeName, 'authority.award-nomination') ? 'active' : '' }}">
                            <a href="{{ route('authority.award-nomination.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-award"></i></span>
                                <span class="pc-mtext">{{ __('Award Nomination') }}</span>
                            </a>
                        </li>
                    @endif
                    @if (\Auth::user() && \Auth::user()->type == 'super admin')
                        @if (Gate::check('manage user'))
                            <li
                                class="pc-item {{ in_array($routeName, ['users.index', 'users.show']) ? 'active' : '' }}">
                                <a href="{{ route('users.index') }}" class="pc-link">
                                    <span class="pc-micon"><i class="ti ti-user-plus"></i></span>
                                    <span class="pc-mtext">{{ __('Employees') }}</span>
                                </a>
                            </li>
                        @endif
                    @else
                        @if (Gate::check('manage user') || Gate::check('manage role') || Gate::check('manage logged history'))
                            <li
                                class="pc-item pc-hasmenu {{ in_array($routeName, ['users.index', 'logged.history', 'role.index', 'role.create', 'role.edit']) ? 'pc-trigger active' : '' }}">
                                <a href="#!" class="pc-link">
                                    <span class="pc-micon">
                                        <i class="ti ti-users"></i>
                                    </span>
                                    <span class="pc-mtext">{{ __('Staff Management') }}</span>
                                    <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                                </a>
                                <ul class="pc-submenu"
                                    style="display: {{ in_array($routeName, ['users.index', 'logged.history', 'role.index', 'role.create', 'role.edit']) ? 'block' : 'none' }}">
                                    @if (Gate::check('manage user'))
                                        <li
                                            class="pc-item {{ in_array($routeName, ['users.index']) ? 'active' : '' }}">
                                            <a class="pc-link"
                                                href="{{ route('users.index') }}">{{ __('Users') }}</a>
                                        </li>
                                    @endif
                                    @if (Gate::check('manage role'))
                                        <li
                                            class="pc-item  {{ in_array($routeName, ['role.index', 'role.create', 'role.edit']) ? 'active' : '' }}">
                                            <a class="pc-link" href="{{ route('role.index') }}">{{ __('Roles') }}
                                            </a>
                                        </li>
                                    @endif
                                    @if ($pricing_feature_settings == 'off' || $subscription->enabled_logged_history == 1)
                                        @if (Gate::check('manage logged history'))
                                            <li
                                                class="pc-item  {{ in_array($routeName, ['logged.history']) ? 'active' : '' }}">
                                                <a class="pc-link"
                                                    href="{{ route('logged.history') }}">{{ __('Logged History') }}</a>
                                            </li>
                                        @endif
                                    @endif
                                </ul>
                            </li>
                        @endif
                    @endif

                    @if (\Auth::user() && in_array(\Auth::user()->type, ['owner', 'super admin', 'admin']))
                        <li class="pc-item pc-caption">
                            <label>{{ __('Master') }}</label>
                            <i class="ti ti-dashboard"></i>
                        </li>

                        {{-- Master Menu --}}
                        <li
                            class="pc-item pc-hasmenu {{ Str::startsWith($routeName, 'master.') && !Str::startsWith($routeName, 'master.inspection') && !Str::startsWith($routeName, 'master.ti_daily_inspection') ? 'active pc-trigger' : '' }}">
                            <a href="javascript:void(0);" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-database"></i></span>
                                <span class="pc-mtext">{{ __('Master') }}</span>
                                <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                            </a>

                            <ul class="pc-submenu"
                                style="display: {{ Str::startsWith($routeName, 'master.') && !Str::startsWith($routeName, 'master.inspection') && !Str::startsWith($routeName, 'master.ti_daily_inspection') ? 'block' : 'none' }}">

                                <li class="pc-item {{ $routeName == 'master.division' ? 'active' : '' }}">
                                    <a href="{{ route('master.division') }}" class="pc-link">Division</a>
                                </li>

                                <li class="pc-item {{ $routeName == 'master.department' ? 'active' : '' }}">
                                    <a href="{{ route('master.department') }}" class="pc-link">Department</a>
                                </li>

                                <li class="pc-item {{ $routeName == 'master.designation' ? 'active' : '' }}">
    <a href="{{ route('master.designation') }}" class="pc-link">
        <span class="pc-micon">
           <i class="ti ti-user-check" aria-hidden="true"></i>
        </span>
        <span class="pc-mtext">{{ __('Designation') }}</span>
    </a>
</li>

                                <li class="pc-item {{ $routeName == 'master.station' ? 'active' : '' }}">
                                    <a href="{{ route('master.station') }}" class="pc-link">Station/Working Place</a>
                                </li>

                                <li class="pc-item {{ $routeName == 'master.diesel_lobby' ? 'active' : '' }}">
                                    <a href="{{ route('master.diesel_lobby') }}" class="pc-link">Diesel Lobby
                                        Location</a>
                                </li>

                                <li
                                    class="pc-item {{ $routeName == 'master.running_room_location' ? 'active' : '' }}">
                                    <a href="{{ route('master.running_room_location') }}" class="pc-link">Running
                                        Room
                                        Location</a>
                                </li>

                                <li class="pc-item {{ $routeName == 'master.movement' ? 'active' : '' }}">
                                    <a href="{{ route('master.movement') }}" class="pc-link">Movement</a>
                                </li>

                                <li class="pc-item {{ $routeName == 'master.type_of_inspection' ? 'active' : '' }}">
                                    <a href="{{ route('master.type_of_inspection') }}" class="pc-link">Type of
                                        Inspection</a>
                                </li>

                                <li class="pc-item {{ $routeName == 'master.type_of_ambush' ? 'active' : '' }}">
                                    <a href="{{ route('master.type_of_ambush') }}" class="pc-link">Type of Ambush</a>
                                </li>

                                <li class="pc-item {{ $routeName == 'master.safety_drive' ? 'active' : '' }}">
                                    <a href="{{ route('master.safety_drive') }}" class="pc-link">Safety Drive</a>
                                </li>

                                <li
                                    class="pc-item {{ $routeName == 'master.joint_signal_sighting' ? 'active' : '' }}">
                                    <a href="{{ route('master.joint_signal_sighting') }}" class="pc-link">Joint
                                        Signal
                                        Sighting</a>
                                </li>

                               <li class="pc-item {{ $routeName == 'master.attend_block' ? 'active' : '' }}">
    <a href="{{ route('master.attend_block') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-calendar" aria-hidden="true"></i>
        </span>
        <span class="pc-mtext">{{ __('Attend Block') }}</span>
    </a>
</li>

                               
                                    <li class="pc-item {{ $routeName == 'master.type_of_leave' ? 'active' : '' }}">
    <a href="{{ route('master.type_of_leave') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-calendar-minus"></i>
        </span>
        <span class="pc-mtext">{{ __('Type of Leave') }}</span>
    </a>
</li>
                                

                                <li class="pc-item {{ $routeName == 'master.load_type' ? 'active' : '' }}">
                                    <a href="{{ route('master.load_type') }}" class="pc-link">Load Type</a>
                                </li>

                                <li class="pc-item {{ $routeName == 'master.line-no' ? 'active' : '' }}">
                                    <a href="{{ route('master.line-no') }}" class="pc-link">Line No.</a>
                                </li>
                                <li class="pc-item {{ $routeName == 'master.amenity-category' ? 'active' : '' }}">
                                    <a href="{{ route('master.amenity-category') }}" class="pc-link">Amenity
                                        Category</a>
                                </li>


                                <li class="pc-item {{ $routeName == 'master.employee.index' ? 'active' : '' }}">
                                    <a href="{{ route('master.employee.index') }}" class="pc-link">
                                        <span class="pc-micon"><i class="ti ti-users"></i></span>
                                        <span class="pc-mtext">{{ __('Employees') }}</span>
                                    </a>
                                </li>

                                <li class="pc-item {{ $routeName == 'master.eo-employee.index' ? 'active' : '' }}">
                                    <a href="{{ route('master.eo-employee.index') }}" class="pc-link">
                                        {{ __('EO Employees') }}
                                    </a>
                                </li>

                                <li class="pc-item {{ $routeName == 'master.slider.index' ? 'active' : '' }}">
                                    <a href="{{ route('master.slider.index') }}" class="pc-link">
                                        <span class="pc-mtext">{{ __('Slider') }}</span>
                                    </a>
                                </li>

                                <li class="pc-item {{ $routeName == 'master.message.index' ? 'active' : '' }}">
                                    <a href="{{ route('master.message.index') }}" class="pc-link">
                                        <span class="pc-mtext">{{ __('Messages') }}</span>
                                    </a>
                                </li>
                              
{{-- Popup Management --}}
<li class="pc-item {{ Str::startsWith($routeName, 'master.popup') ? 'active' : '' }}">
    <a href="{{ route('master.popup.index') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-photo"></i>
        </span>
        <span class="pc-mtext">{{ __('Popup Management') }}</span>
    </a>
</li>


                                <li class="pc-item {{ $routeName == 'coaching-rake-type.index' ? 'active' : '' }}">
                                    <a href="{{ route('coaching-rake-type.index') }}" class="pc-link">
                                        <span class="pc-mtext">{{ __('Coaching Rake Type') }}</span>
                                    </a>
                                </li>

                                <li class="pc-item {{ $routeName == 'goods-rake-type.index' ? 'active' : '' }}">
                                    <a href="{{ route('goods-rake-type.index') }}" class="pc-link">
                                        <span class="pc-mtext">{{ __('Goods Rake Type') }}</span>
                                    </a>
                                </li>


                                <li class="pc-item {{ $routeName == 'area-of-proficiency.index' ? 'active' : '' }}">
                                    <a href="{{ route('area-of-proficiency.index') }}" class="pc-link">
                                        <span class="pc-mtext">{{ __('Area of Proficiency') }}</span>
                                    </a>
                                </li>



                            </ul>
                        </li>



                        {{-- Operating & Safety Documents start --}}
                        <li
                            class="pc-item pc-hasmenu {{ Str::startsWith($routeName, 'operating-safety-documents.') ? 'active pc-trigger' : '' }}">

                            <a href="javascript:void(0);" class="pc-link">
                                <span class="pc-micon">
                                    <i class="ti ti-file-upload"></i>
                                </span>

                                <span class="pc-mtext">
                                    {{ __('Operating & Safety Documents') }}
                                </span>

                                <span class="pc-arrow">
                                    <i data-feather="chevron-right"></i>
                                </span>
                            </a>

                            <ul class="pc-submenu"
                                style="display: {{ Str::startsWith($routeName, 'operating-safety-documents.') ? 'block' : 'none' }}">

                                {{-- <li
                                    class="pc-item {{ Str::startsWith($routeName, 'operating-safety-documents.station-working-rule') ? 'active' : '' }}">
                                    <a href="{{ route('operating-safety-documents.station-working-rule.index') }}"
                                        class="pc-link">
                                        <span class="pc-micon">
                                            <i class="ti ti-building-warehouse"></i>
                                        </span>
                                        <span class="pc-mtext">{{ __('Station Working Rule') }}</span>
                                    </a>
                                </li> --}}

                                <li class="pc-item pc-hasmenu">
                                    <a href="javascript:void(0);" class="pc-link">
                                        <span class="pc-micon">
                                            <i class="ti ti-building-warehouse"></i>
                                        </span>

                                        <span class="pc-mtext">
                                            Station Working Rule
                                        </span>

                                        <span class="pc-arrow">
                                            <i data-feather="chevron-right"></i>
                                        </span>
                                    </a>

                                    <ul class="pc-submenu">

                                        <li class="pc-item">
                                            <a href="{{ route('operating-safety-documents.station-working-rule.original.index') }}"
                                                class="pc-link">
                                                <span class="pc-micon">
                                                    <i class="ti ti-file"></i>
                                                </span>
                                                <span class="pc-mtext">
                                                    Original
                                                </span>
                                            </a>
                                        </li>

                                        <li class="pc-item">
                                            <a href="{{ route('operating-safety-documents.station-working-rule.correction.index') }}"
                                                class="pc-link">
                                                <span class="pc-micon">
                                                    <i class="ti ti-file-symlink"></i>
                                                </span>
                                                <span class="pc-mtext">
                                                    Correction
                                                </span>
                                            </a>
                                        </li>

                                    </ul>
                                </li>

                                {{-- <li
                                    class="pc-item {{ Str::startsWith($routeName, 'operating-safety-documents.g-sr') ? 'active' : '' }}">
                                    <a href="{{ route('operating-safety-documents.g-sr.index') }}" class="pc-link">
                                        <span class="pc-micon">
                                            <i class="ti ti-file-text"></i>
                                        </span>
                                        <span class="pc-mtext">{{ __('G&SR') }}</span>
                                    </a>
                                </li> --}}

                                <li class="pc-item pc-hasmenu">

                                    <a href="javascript:void(0);" class="pc-link">

                                        <span class="pc-micon">
                                            <i class="ti ti-file-text"></i>
                                        </span>

                                        <span class="pc-mtext">
                                            G&SR
                                        </span>

                                        <span class="pc-arrow">
                                            <i data-feather="chevron-right"></i>
                                        </span>

                                    </a>

                                    <ul class="pc-submenu">

                                        <li class="pc-item">


                                            <a href="{{ route('operating-safety-documents.g-sr.original.index') }}"
                                                class="pc-link">
                                                <span class="pc-micon">
                                                    <i class="ti ti-file"></i>
                                                </span>

                                                <span class="pc-mtext">
                                                    {{ __('Original') }}
                                                </span>

                                            </a>
                                        </li>

                                        <li class="pc-item">
                                            <a href="{{ route('operating-safety-documents.g-sr.correction.index') }}"
                                                class="pc-link">
                                                <span class="pc-micon">
                                                    <i class="ti ti-file-symlink"></i>
                                                </span>

                                                <span class="pc-mtext">
                                                    {{ __('Correction') }}
                                                </span>
                                            </a>
                                        </li>

                                    </ul>

                                </li>

                                {{-- <li
                                    class="pc-item {{ Str::startsWith($routeName, 'operating-safety-documents.operating-manual') ? 'active' : '' }}">
                                    <a href="{{ route('operating-safety-documents.operating-manual.index') }}"
                                        class="pc-link">
                                        <span class="pc-micon">
                                            <i class="ti ti-book"></i>
                                        </span>
                                        <span class="pc-mtext">{{ __('Operating Manual') }}</span>
                                    </a>
                                </li> --}}

                                <li
                                    class="pc-item pc-hasmenu
    {{ Str::startsWith($routeName, 'operating-safety-documents.operating-manual.') ? 'active pc-trigger' : '' }}">

                                    <a href="javascript:void(0);" class="pc-link">

                                        <span class="pc-micon">
                                            <i class="ti ti-book"></i>
                                        </span>

                                        <span class="pc-mtext">
                                            {{ __('Operating Manual') }}
                                        </span>

                                        <span class="pc-arrow">
                                            <i data-feather="chevron-right"></i>
                                        </span>

                                    </a>

                                    <ul class="pc-submenu"
                                        style="display: {{ Str::startsWith($routeName, 'operating-safety-documents.operating-manual.') ? 'block' : 'none' }}">

                                        <li
                                            class="pc-item {{ Str::startsWith($routeName, 'operating-safety-documents.operating-manual.original') ? 'active' : '' }}">

                                            <a href="{{ route('operating-safety-documents.operating-manual.original.index') }}"
                                                class="pc-link">

                                                <span class="pc-micon">
                                                    <i class="ti ti-file"></i>
                                                </span>

                                                <span class="pc-mtext">
                                                    {{ __('Original') }}
                                                </span>

                                            </a>

                                        </li>

                                        <li
                                            class="pc-item {{ Str::startsWith($routeName, 'operating-safety-documents.operating-manual.correction') ? 'active' : '' }}">

                                            <a href="{{ route('operating-safety-documents.operating-manual.correction.index') }}"
                                                class="pc-link">

                                                <span class="pc-micon">
                                                    <i class="ti ti-file-symlink"></i>
                                                </span>

                                                <span class="pc-mtext">
                                                    {{ __('Correction') }}
                                                </span>

                                            </a>

                                        </li>

                                    </ul>
                                </li>

                                {{-- <li
                                    class="pc-item {{ Str::startsWith($routeName, 'operating-safety-documents.accident-manual') ? 'active' : '' }}">
                                    <a href="{{ route('operating-safety-documents.accident-manual.index') }}"
                                        class="pc-link">
                                        <span class="pc-micon">
                                            <i class="ti ti-alert-triangle"></i>
                                        </span>
                                        <span class="pc-mtext">{{ __('Accident Manual') }}</span>
                                    </a>
                                </li> --}}

                                <li
                                    class="pc-item pc-hasmenu
    {{ Str::startsWith($routeName, 'operating-safety-documents.accident-manual.') ? 'active pc-trigger' : '' }}">

                                    <a href="javascript:void(0);" class="pc-link">

                                        <span class="pc-micon">
                                            <i class="ti ti-alert-triangle"></i>
                                        </span>

                                        <span class="pc-mtext">
                                            {{ __('Accident Manual') }}
                                        </span>

                                        <span class="pc-arrow">
                                            <i data-feather="chevron-right"></i>
                                        </span>

                                    </a>

                                    <ul class="pc-submenu"
                                        style="display: {{ Str::startsWith($routeName, 'operating-safety-documents.accident-manual.') ? 'block' : 'none' }}">

                                        <li
                                            class="pc-item {{ Str::startsWith($routeName, 'operating-safety-documents.accident-manual.original') ? 'active' : '' }}">

                                            <a href="{{ route('operating-safety-documents.accident-manual.original.index') }}"
                                                class="pc-link">

                                                <span class="pc-micon">
                                                    <i class="ti ti-file"></i>
                                                </span>

                                                <span class="pc-mtext">
                                                    {{ __('Original') }}
                                                </span>

                                            </a>

                                        </li>

                                        <li
                                            class="pc-item {{ Str::startsWith($routeName, 'operating-safety-documents.accident-manual.correction') ? 'active' : '' }}">

                                            <a href="{{ route('operating-safety-documents.accident-manual.correction.index') }}"
                                                class="pc-link">

                                                <span class="pc-micon">
                                                    <i class="ti ti-file-symlink"></i>
                                                </span>

                                                <span class="pc-mtext">
                                                    {{ __('Correction') }}
                                                </span>

                                            </a>

                                        </li>

                                    </ul>
                                </li>

                                <li
                                    class="pc-item {{ Str::startsWith($routeName, 'operating-safety-documents.miscellaneous-letters') ? 'active' : '' }}">
                                    <a href="{{ route('operating-safety-documents.miscellaneous-letters.index') }}"
                                        class="pc-link">
                                        <span class="pc-micon">
                                            <i class="ti ti-mail"></i>
                                        </span>
                                        <span class="pc-mtext">{{ __('Miscellaneous Letters') }}</span>
                                    </a>
                                </li>

                                <li
                                    class="pc-item {{ Str::startsWith($routeName, 'operating-safety-documents.operating-safety-alert') ? 'active' : '' }}">
                                    <a href="{{ route('operating-safety-documents.operating-safety-alert.index') }}"
                                        class="pc-link">
                                        <span class="pc-micon">
                                            <i class="ti ti-bell"></i>
                                        </span>
                                        <span class="pc-mtext">{{ __('Operating Safety Alert') }}</span>
                                    </a>
                                </li>

                                <li
                                    class="pc-item {{ Str::startsWith($routeName, 'operating-safety-documents.operating-safety-episode') ? 'active' : '' }}">
                                    <a href="{{ route('operating-safety-documents.operating-safety-episode.index') }}"
                                        class="pc-link">
                                        <span class="pc-micon">
                                            <i class="ti ti-alert-circle"></i>
                                        </span>
                                        <span class="pc-mtext">{{ __('Operating Safety Episode') }}</span>
                                    </a>
                                </li>

                                <li
                                    class="pc-item {{ Str::startsWith($routeName, 'operating-safety-documents.operating-safety-drive') ? 'active' : '' }}">
                                    <a href="{{ route('operating-safety-documents.operating-safety-drive.index') }}"
                                        class="pc-link">
                                        <span class="pc-micon">
                                            <i class="ti ti-car"></i>
                                        </span>
                                        <span class="pc-mtext">{{ __('Operating Safety Drive') }}</span>
                                    </a>
                                </li>

                                <li
                                    class="pc-item {{ Str::startsWith($routeName, 'operating-safety-documents.case-study') ? 'active' : '' }}">

                                    <a href="{{ route('operating-safety-documents.case-study.index') }}"
                                        class="pc-link">

                                        <span class="pc-micon">
                                            <i class="ti ti-file"></i>
                                        </span>

                                        <span class="pc-mtext">
                                            {{ __('Case Study') }}
                                        </span>

                                    </a>

                                </li>


                                <li
                                    class="pc-item {{ Str::startsWith($routeName, 'document-upload.safety-circular') ? 'active' : '' }}">
                                    <a href="{{ route('document-upload.safety-circular.index') }}" class="pc-link">
                                        <span class="pc-micon">
                                            <i class="ti ti-file-text"></i>
                                        </span>
                                        <span class="pc-mtext">{{ __('Safety Circular') }}</span>
                                    </a>
                                </li>

                                <li
                                    class="pc-item {{ Str::startsWith($routeName, 'document-upload.operating-safety-circular') ? 'active' : '' }}">
                                    <a href="{{ route('document-upload.operating-safety-circular.index') }}"
                                        class="pc-link">
                                        <span class="pc-micon">
                                            <i class="ti ti-file-text"></i>
                                        </span>
                                        <span class="pc-mtext">{{ __('Operating Safety Circular') }}</span>
                                    </a>
                                </li>

                                <li
                                    class="pc-item {{ Str::startsWith($routeName, 'document-upload.jpo') ? 'active' : '' }}">
                                    <a href="{{ route('document-upload.jpo.index') }}" class="pc-link">
                                        <span class="pc-micon">
                                            <i class="ti ti-file-text"></i>
                                        </span>
                                        <span class="pc-mtext">{{ __('JPO') }}</span>
                                    </a>
                                </li>

                                <li
                                    class="pc-item {{ Str::startsWith($routeName, 'inspection-notes.officer') ? 'active' : '' }}">
                                    <a href="{{ route('inspection-notes.officer.index') }}" class="pc-link">
                                        <span class="pc-micon">
                                            <i class="ti ti-user"></i>
                                        </span>
                                        <span class="pc-mtext">{{ __('Officer Inspection Notes') }}</span>
                                    </a>
                                </li>

                                <li
                                    class="pc-item {{ Str::startsWith($routeName, 'inspection-notes.traffic-inspector') ? 'active' : '' }}">
                                    <a href="{{ route('inspection-notes.traffic-inspector.index') }}"
                                        class="pc-link">
                                        <span class="pc-micon">
                                            <i class="ti ti-user-check"></i>
                                        </span>
                                        <span class="pc-mtext">{{ __('Traffic Inspector Inspection Notes') }}</span>
                                    </a>
                                </li>


                            </ul>
                        </li>

                        {{-- Operating & Safety Documents end --}}


                        <li class="pc-item {{ Str::startsWith($routeName, 'master.inspection') ? 'active' : '' }}">
                            <a href="{{ route('master.inspection.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-clipboard-list"></i></span>
                                <span class="pc-mtext">{{ __('Inspection List') }}</span>
                            </a>
                        </li>

                        <li
                            class="pc-item {{ Str::startsWith($routeName, 'master.ti_daily_inspection') ? 'active' : '' }}">
                            <a href="{{ route('master.ti_daily_inspection.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-clipboard-list"></i></span>
                                <span class="pc-mtext">{{ __('TI Daily Movement Cum Inspection') }}</span>
                            </a>
                        </li>

                        <li class="pc-item pc-caption">
                            <label>{{ __('Authority') }}</label>
                            <i class="ti ti-dashboard"></i>
                        </li>

                        {{-- Master Menu --}}
                        <li
                            class="pc-item pc-hasmenu {{ Str::startsWith($routeName, 'authority.') ? 'active pc-trigger' : '' }}">
                            <a href="javascript:void(0);" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-user"></i></span>
                                <span class="pc-mtext">{{ __('Authority') }}</span>
                                <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                            </a>

                            <ul class="pc-submenu"
                                style="display: {{ Str::startsWith($routeName, 'authority.') ? 'block' : 'none' }}">

                                <li class="pc-item {{ $routeName == 'authority.complain.index' ? 'active' : '' }}">
                                    <a href="{{ route('authority.complain.index') }}" class="pc-link">Create
                                        Complaint</a>
                                </li>

                                <li class="pc-item {{ $routeName == 'authority.rectify.index' ? 'active' : '' }}">
                                    <a href="{{ route('authority.rectify.index') }}" class="pc-link">Create
                                        Rectify</a>
                                </li>

                                <li class="pc-item {{ $routeName == 'authority.ph16-ti.index' ? 'active' : '' }}">
                                    <a href="{{ route('authority.ph16-ti.index') }}" class="pc-link">
                                        <span class="pc-micon">
                                            <i class="ti ti-clipboard-check"></i>
                                        </span>
                                        <span class="pc-mtext">{{ __('PH16 TI') }}</span>
                                    </a>
                                </li>

                                <li class="pc-item {{ Str::startsWith($routeName, 'authority.officer-inspection-notes') ? 'active' : '' }}">
                                    <a href="{{ route('authority.officer-inspection-notes.index') }}" class="pc-link">
                                        <span class="pc-micon">
                                            <i class="ti ti-notes"></i>
                                        </span>
                                        <span class="pc-mtext">{{ __('Officer Inspection Notes') }}</span>
                                    </a>
                                </li>

                                <li class="pc-item {{ Str::startsWith($routeName, 'authority.award-nomination') ? 'active' : '' }}">
                                    <a href="{{ route('authority.award-nomination.index') }}" class="pc-link">
                                        <span class="pc-micon">
                                            <i class="ti ti-award"></i>
                                        </span>
                                        <span class="pc-mtext">{{ __('Award Nomination') }}</span>
                                    </a>
                                </li>

                                <li class="pc-item {{ Str::startsWith($routeName, 'authority.my-movement') ? 'active' : '' }}">
                                    <a href="{{ route('authority.my-movement.index') }}" class="pc-link">
                                        <span class="pc-micon">
                                            <i class="ti ti-walk"></i>
                                        </span>
                                        <span class="pc-mtext">{{ __('My Movement') }}</span>
                                    </a>
                                </li>

                                <li class="pc-item {{ Str::startsWith($routeName, 'authority.ambush-check') ? 'active' : '' }}">
                                    <a href="{{ route('authority.ambush-check.index') }}" class="pc-link">
                                        <span class="pc-micon">
                                            <i class="ti ti-eye"></i>
                                        </span>
                                        <span class="pc-mtext">{{ __('Ambush Check') }}</span>
                                    </a>
                                </li>

                                <li class="pc-item {{ Str::startsWith($routeName, 'authority.load-stable-clearance') ? 'active' : '' }}">
                                    <a href="{{ route('authority.load-stable-clearance.index') }}" class="pc-link">
                                        <span class="pc-micon">
                                            <i class="ti ti-truck"></i>
                                        </span>
                                        <span class="pc-mtext">{{ __('Load Stable & Clearance') }}</span>
                                    </a>
                                </li>

                                <li class="pc-item {{ Str::startsWith($routeName, 'authority.staff-counselling') ? 'active' : '' }}">
                                    <a href="{{ route('authority.staff-counselling.index') }}" class="pc-link">
                                        <span class="pc-micon">
                                            <i class="ti ti-messages"></i>
                                        </span>
                                        <span class="pc-mtext">{{ __('Staff Counselling') }}</span>
                                    </a>
                                </li>

                                <li class="pc-item {{ Str::startsWith($routeName, 'authority.pme-refresher-gradation') ? 'active' : '' }}">
                                    <a href="{{ route('authority.pme-refresher-gradation.index') }}" class="pc-link">
                                        <span class="pc-micon">
                                            <i class="ti ti-file-certificate"></i>
                                        </span>
                                        <span class="pc-mtext">{{ __('PME / REFRESHER / GRADATION') }}</span>
                                    </a>
                                </li>

                            </ul>
                        </li>

                        <li class="pc-item pc-caption">
                            <label>{{ __('Staff Visibility') }}</label>
                            <i class="ti ti-eye"></i>
                        </li>

                        {{-- Staff Visibility Menu --}}
                        <li
                            class="pc-item pc-hasmenu {{ Str::startsWith($routeName, 'staff-visibility.') ? 'active pc-trigger' : '' }}">
                            <a href="javascript:void(0);" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-users"></i></span>
                                <span class="pc-mtext">{{ __('Staff Visibility') }}</span>
                                <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                            </a>

                            <ul class="pc-submenu"
                                style="display: {{ Str::startsWith($routeName, 'staff-visibility.') ? 'block' : 'none' }}">

                                {{-- 1. Staff Counseling (Online Station) --}}
                                <li class="pc-item {{ Str::startsWith($routeName, 'staff-visibility.counselling-online') ? 'active' : '' }}">
                                    <a href="{{ route('staff-visibility.counselling-online.index') }}" class="pc-link">
                                        <span class="pc-micon"><i class="ti ti-messages"></i></span>
                                        <span class="pc-mtext">{{ __('Staff Counseling (Online Station)') }}</span>
                                    </a>
                                </li>

                                {{-- 2. Staff Counseling (Offline Station) --}}
                                <li class="pc-item {{ Str::startsWith($routeName, 'staff-visibility.counselling-offline') ? 'active' : '' }}">
                                    <a href="{{ route('staff-visibility.counselling-offline.index') }}" class="pc-link">
                                        <span class="pc-micon"><i class="ti ti-message-circle"></i></span>
                                        <span class="pc-mtext">{{ __('Staff Counseling (Offline Station)') }}</span>
                                    </a>
                                </li>

                                {{-- 3. Ambush Check (Shunting) --}}
                                <li class="pc-item {{ Str::startsWith($routeName, 'staff-visibility.ambush-shunting') ? 'active' : '' }}">
                                    <a href="{{ route('staff-visibility.ambush-shunting.index') }}" class="pc-link">
                                        <span class="pc-micon"><i class="ti ti-eye"></i></span>
                                        <span class="pc-mtext">{{ __('Ambush Check (Shunting)') }}</span>
                                    </a>
                                </li>

                                {{-- 4. Ambush Check (BA Test) --}}
                                <li class="pc-item {{ Str::startsWith($routeName, 'staff-visibility.ambush-ba-test') ? 'active' : '' }}">
                                    <a href="{{ route('staff-visibility.ambush-ba-test.index') }}" class="pc-link">
                                        <span class="pc-micon"><i class="ti ti-activity"></i></span>
                                        <span class="pc-mtext">{{ __('Ambush Check (BA Test)') }}</span>
                                    </a>
                                </li>

                                {{-- 5. Medical --}}
                                <li class="pc-item {{ Str::startsWith($routeName, 'staff-visibility.medical') ? 'active' : '' }}">
                                    <a href="{{ route('staff-visibility.medical.index') }}" class="pc-link">
                                        <span class="pc-micon"><i class="ti ti-heart"></i></span>
                                        <span class="pc-mtext">{{ __('Medical') }}</span>
                                    </a>
                                </li>

                                {{-- 6. Refresher --}}
                                <li class="pc-item {{ Str::startsWith($routeName, 'staff-visibility.refresher') ? 'active' : '' }}">
                                    <a href="{{ route('staff-visibility.refresher.index') }}" class="pc-link">
                                        <span class="pc-micon"><i class="ti ti-refresh"></i></span>
                                        <span class="pc-mtext">{{ __('Refresher') }}</span>
                                    </a>
                                </li>

                                {{-- 7. Gradation --}}
                                <li class="pc-item {{ Str::startsWith($routeName, 'staff-visibility.gradation') ? 'active' : '' }}">
                                    <a href="{{ route('staff-visibility.gradation.index') }}" class="pc-link">
                                        <span class="pc-micon"><i class="ti ti-award"></i></span>
                                        <span class="pc-mtext">{{ __('Gradation') }}</span>
                                    </a>
                                </li>

                            </ul>
                        </li>

                        {{-- <li
                        --}}

                        <li class="pc-item pc-caption">
                            <label>{{ __('Load Stable') }}</label>
                            <i class="ti ti-truck"></i>
                        </li>

                        {{-- Load Stable Menu --}}
                        <li
                            class="pc-item pc-hasmenu {{ Str::startsWith($routeName, 'load-stable.') ? 'active pc-trigger' : '' }}">
                            <a href="javascript:void(0);" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-truck-delivery"></i></span>
                                <span class="pc-mtext">{{ __('Load Stable') }}</span>
                                <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                            </a>

                            <ul class="pc-submenu"
                                style="display: {{ Str::startsWith($routeName, 'load-stable.') ? 'block' : 'none' }}">

                                {{-- 1. New Entry --}}
                                <li class="pc-item {{ Str::startsWith($routeName, 'load-stable.new-entry') ? 'active' : '' }}">
                                    <a href="{{ route('load-stable.new-entry.index') }}" class="pc-link">
                                        <span class="pc-micon"><i class="ti ti-file-plus"></i></span>
                                        <span class="pc-mtext">{{ __('New Entry') }}</span>
                                    </a>
                                </li>

                                {{-- 2. New Entry Forwarded To --}}
                                <li class="pc-item {{ Str::startsWith($routeName, 'load-stable.forwarded-to') ? 'active' : '' }}">
                                    <a href="{{ route('load-stable.forwarded-to.index') }}" class="pc-link">
                                        <span class="pc-micon"><i class="ti ti-send"></i></span>
                                        <span class="pc-mtext">{{ __('New Entry Forwarded To') }}</span>
                                    </a>
                                </li>

                                {{-- 3. New Entry Finaliz By --}}
                                <li class="pc-item {{ Str::startsWith($routeName, 'load-stable.finaliz-by') ? 'active' : '' }}">
                                    <a href="{{ route('load-stable.finaliz-by.index') }}" class="pc-link">
                                        <span class="pc-micon"><i class="ti ti-circle-check"></i></span>
                                        <span class="pc-mtext">{{ __('New Entry Finalized By') }}</span>
                                    </a>
                                </li>

                                {{-- 4. Load Clearance Forward By SM To --}}
                                <li class="pc-item {{ Str::startsWith($routeName, 'load-stable.clearance-forward-sm') ? 'active' : '' }}">
                                    <a href="{{ route('load-stable.clearance-forward-sm.index') }}" class="pc-link">
                                        <span class="pc-micon"><i class="ti ti-share"></i></span>
                                        <span class="pc-mtext">{{ __('Load Clearance Forward By SM To') }}</span>
                                    </a>
                                </li>

                                {{-- 5. Load Clearance Entry By --}}
                                <li class="pc-item {{ Str::startsWith($routeName, 'load-stable.clearance-entry-by') ? 'active' : '' }}">
                                    <a href="{{ route('load-stable.clearance-entry-by.index') }}" class="pc-link">
                                        <span class="pc-micon"><i class="ti ti-edit"></i></span>
                                        <span class="pc-mtext">{{ __('Load Clearance Entry By') }}</span>
                                    </a>
                                </li>

                                {{-- 6. Load Clearance Forward By Pointsman To --}}
                                <li class="pc-item {{ Str::startsWith($routeName, 'load-stable.clearance-forward-pointsman') ? 'active' : '' }}">
                                    <a href="{{ route('load-stable.clearance-forward-pointsman.index') }}" class="pc-link">
                                        <span class="pc-micon"><i class="ti ti-arrow-forward-up"></i></span>
                                        <span class="pc-mtext">{{ __('Load Clearance Forward By Pointsman To') }}</span>
                                    </a>
                                </li>

                                {{-- 7. Load Clearance Finaliz By --}}
                                <li class="pc-item {{ Str::startsWith($routeName, 'load-stable.clearance-finaliz-by') ? 'active' : '' }}">
                                    <a href="{{ route('load-stable.clearance-finaliz-by.index') }}" class="pc-link">
                                        <span class="pc-micon"><i class="ti ti-clipboard-check"></i></span>
                                        <span class="pc-mtext">{{ __('Load Clearance Finalized By') }}</span>
                                    </a>
                                </li>

                            </ul>
                        </li>

                        {{-- <li
                            class="pc-item pc-hasmenu {{ Str::startsWith($routeName, 'project-monitering-system.project') || Str::startsWith($routeName, 'project-monitering-system.scope_of_work') ? 'active pc-trigger' : '' }}">
                            <a href="javascript:void(0);" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-clipboard-list"></i></span>
                                <span class="pc-mtext">{{ __('Project Monitoring') }}</span>
                                <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                            </a>

                            <ul class="pc-submenu"
                                style="display: {{ Str::startsWith($routeName, 'project-monitering-system.project') || Str::startsWith($routeName, 'project-monitering-system.scope_of_work') ? 'block' : 'none' }}">

                                <li
                                    class="pc-item {{ $routeName == 'project-monitering-system.project.index' ? 'active' : '' }}">
                                    <a href="{{ route('project-monitering-system.project.index') }}"
                                        class="pc-link">

                                        {{ __('Project') }}
                                    </a>
                                </li>

                                <li
                                    class="pc-item {{ $routeName == 'project-monitering-system.scope_of_work.index' ? 'active' : '' }}">
                                    <a href="{{ route('project-monitering-system.scope_of_work.index') }}"
                                        class="pc-link">

                                        {{ __('Scope of Work') }}
                                    </a>
                                </li>

                            </ul>
                        </li> --}}
                    @endif

                    {{-- PH 16 Works --}}


                    @if (\Auth::user() && \Auth::user()->type == 'owner' || \Auth::user()->type == 'emp')
                        <li
                            class="pc-item {{ Str::startsWith($routeName, 'emergency-contact.index') ? 'active' : '' }}">
                            <a href="{{ route('emergency-contact.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-clipboard-list"></i></span>
                                <span class="pc-mtext">{{ __('Emergency Contact') }}</span>
                            </a>
                        </li>
                    @endif

                    {{-- <li class="pc-item pc-caption">
                    <label>{{ __('') }}</label>
                    <i class="ti ti-dashboard"></i>
                </li> --}}

                    {{-- Master Menu --}}
                    @if (\Auth::user() && \Auth::user()->type == 'Department Admin')
                        <li class="pc-item {{ in_array($routeName, ['all-complaints', '']) ? 'active' : '' }}">
                            <a href="{{ route('all-complaints') }}" class="pc-link">
                                <span class="pc-micon"><i data-feather="users"></i></span>
                                <span class="pc-mtext">{{ __('All Complints') }}</span>
                            </a>
                        </li>
                        <li class="pc-item pc-caption">
                            <label>{{ __('Master') }}</label>
                            <i class="ti ti-dashboard"></i>
                        </li>

                        {{-- Master Menu --}}
                        <li
                            class="pc-item pc-hasmenu {{ Str::startsWith($routeName, 'master.') ? 'active pc-trigger' : '' }}">
                            <a href="javascript:void(0);" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-database"></i></span>
                                <span class="pc-mtext">{{ __('Master') }}</span>
                                <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                            </a>

                            <ul class="pc-submenu"
                                style="display: {{ Str::startsWith($routeName, 'master.') ? 'block' : 'none' }}">

            <li class="pc-item {{ $routeName == 'master.designation' ? 'active' : '' }}">
    <a href="{{ route('master.designation') }}" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-calendar-minus"></i>
        </span>
        <span class="pc-mtext">Designation</span>
    </a>
</li>


                                <li class="pc-item {{ $routeName == 'master.employee.index' ? 'active' : '' }}">
                                    <a href="{{ route('master.employee.index') }}" class="pc-link">
                                        <span class="pc-micon"><i class="ti ti-users"></i></span>
                                        <span class="pc-mtext">{{ __('Employees') }}</span>
                                    </a>
                                </li>

                                @if ($isTIHQ)
                                    <li class="pc-item {{ $routeName == 'master.movement' ? 'active' : '' }}">
                                        <a href="{{ route('master.movement') }}"
                                            class="pc-link">{{ __('Movement') }}</a>
                                    </li>
                                @endif



                            </ul>
                        </li>

                        @if ($isTIHQ)
                            <li class="pc-item pc-caption">
                                <label>{{ __('Reports') }}</label>
                                <i class="ti ti-dashboard"></i>
                            </li>

                        

                            {{-- Reports Menu --}}
                            <li
                                class="pc-item pc-hasmenu {{ Str::startsWith($routeName, 'master.report.') ? 'active pc-trigger' : '' }}">

                                <a href="javascript:void(0);" class="pc-link">
                                    <span class="pc-micon"><i class="ti ti-database"></i></span>
                                    <span class="pc-mtext">{{ __('Report') }}</span>
                                    <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                                </a>

                                <ul class="pc-submenu"
                                    style="display: {{ Str::startsWith($routeName, 'master.report.') ? 'block' : 'none' }}">

                                    <li
                                        class="pc-item {{ $routeName == 'master.report.inspection' ? 'active' : '' }}">
                                        <a href="{{ route('master.report.inspection') }}"
                                            class="pc-link">{{ __('Movement Report') }}</a>
                                    </li>

                                </ul>
                            </li>
                        @endif
                    @endif

                    {{-- PH 16 Works for Admin (Hidden from Admin sidebar per request, completely preserved for future use) --}}
                    @if (false && \Auth::user() && \Auth::user()->type == 'owner')
                        <li class="pc-item pc-caption">
                            <label>{{ __('PH 16 Works') }}</label>
                            <i class="ti ti-dashboard"></i>
                        </li>

                     
{{-- Reports Menu --}}
<li
    class="pc-item {{ Str::startsWith($routeName, 'works-paperwork-progress-register.identify-new-work.index') ? 'active' : '' }}">
    <a href="{{ route('works-paperwork-progress-register.identify-new-work.index') }}"
        class="pc-link">
        <span class="pc-micon"><i class="ti ti-clipboard-list"></i></span>
        <span class="pc-mtext">{{ __('New Work') }}</span>
    </a>
</li>

                        <li
                            class="pc-item pc-hasmenu {{ Str::startsWith($routeName, 'works-paperwork-progress-register.joint-survey') || Str::startsWith($routeName, 'works-paperwork-progress-register.estimates') || Str::startsWith($routeName, 'works-paperwork-progress-register.review-estimate') || Str::startsWith($routeName, 'works-paperwork-progress-register.approval-listing') || Str::startsWith($routeName, 'works-paperwork-progress-register.finance-vetting-sanction') || Str::startsWith($routeName, 'works-paperwork-progress-register.tendering') || Str::startsWith($routeName, 'works-paperwork-progress-register.esp-sip.esp') || Str::startsWith($routeName, 'works-paperwork-progress-register.esp-sip.sip') || Str::startsWith($routeName, 'works-paperwork-progress-register.execution') || Str::startsWith($routeName, 'works-paperwork-progress-register.executionend') ? 'active pc-trigger' : '' }}">
                            <a href="javascript:void(0);" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-clipboard-list"></i></span>
                                <span class="pc-mtext">{{ __('Paperwork timeline') }}</span>
                                <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                            </a>

                            <ul class="pc-submenu"
                                style="display: {{ Str::startsWith($routeName, 'works-paperwork-progress-register.joint-survey') || Str::startsWith($routeName, 'works-paperwork-progress-register.estimates') || Str::startsWith($routeName, 'works-paperwork-progress-register.review-estimate') || Str::startsWith($routeName, 'works-paperwork-progress-register.approval-listing') || Str::startsWith($routeName, 'works-paperwork-progress-register.finance-vetting-sanction') || Str::startsWith($routeName, 'works-paperwork-progress-register.tendering') || Str::startsWith($routeName, 'works-paperwork-progress-register.esp-sip.esp') || Str::startsWith($routeName, 'works-paperwork-progress-register.esp-sip.sip') || Str::startsWith($routeName, 'works-paperwork-progress-register.execution') || Str::startsWith($routeName, 'works-paperwork-progress-register.executionend') ? 'block' : 'none' }}">

                                <li
                                    class="pc-item {{ $routeName == 'works-paperwork-progress-register.joint-survey.index' ? 'active' : '' }}">
                                    <a href="{{ route('works-paperwork-progress-register.joint-survey.index') }}"
                                        class="pc-link">

                                        {{ __('Joint Survey') }}
                                    </a>
                                </li>

                                <li
                                    class="pc-item {{ $routeName == 'works-paperwork-progress-register.estimates.index' ? 'active' : '' }}">
                                    <a href="{{ route('works-paperwork-progress-register.estimates.index') }}"
                                        class="pc-link">

                                        {{ __('Estimates') }}
                                    </a>
                                </li>

                                <li
                                    class="pc-item {{ $routeName == 'works-paperwork-progress-register.review-estimate.index' ? 'active' : '' }}">
                                    <a href="{{ route('works-paperwork-progress-register.review-estimate.index') }}"
                                        class="pc-link">

                                        {{ __('Review Estimates') }}
                                    </a>
                                </li>

                                <li
                                    class="pc-item {{ $routeName == 'works-paperwork-progress-register.approval-listing.index' ? 'active' : '' }}">
                                    <a href="{{ route('works-paperwork-progress-register.approval-listing.index') }}"
                                        class="pc-link">

                                        {{ __('Approval & Listing') }}
                                    </a>
                                </li>

                                <li
                                    class="pc-item {{ $routeName == 'works-paperwork-progress-register.finance-vetting-sanction.index' ? 'active' : '' }}">
                                    <a href="{{ route('works-paperwork-progress-register.finance-vetting-sanction.index') }}"
                                        class="pc-link">

                                        {{ __('Finance Vetting & Sanction') }}
                                    </a>
                                </li>

                                <li
                                    class="pc-item {{ $routeName == 'works-paperwork-progress-register.tendering.index' ? 'active' : '' }}">
                                    <a href="{{ route('works-paperwork-progress-register.tendering.index') }}"
                                        class="pc-link">

                                        {{ __('Tendering') }}
                                    </a>
                                </li>

                                <li
                                    class="pc-item {{ $routeName == 'works-paperwork-progress-register.esp-sip.esp.index' ? 'active' : '' }}">
                                    <a href="{{ route('works-paperwork-progress-register.esp-sip.esp.index') }}"
                                        class="pc-link">

                                        {{ __('ESP') }}
                                    </a>
                                </li>

                                <li
                                    class="pc-item {{ $routeName == 'works-paperwork-progress-register.esp-sip.sip.index' ? 'active' : '' }}">
                                    <a href="{{ route('works-paperwork-progress-register.esp-sip.sip.index') }}"
                                        class="pc-link">

                                        {{ __('Sip') }}
                                    </a>
                                </li>

                                <li
                                    class="pc-item {{ $routeName == 'works-paperwork-progress-register.execution.index' ? 'active' : '' }}">
                                    <a href="{{ route('works-paperwork-progress-register.execution.index') }}"
                                        class="pc-link">

                                        {{ __('Execution Start') }}
                                    </a>
                                </li>


                                <li
                                    class="pc-item {{ $routeName == 'works-paperwork-progress-register.executionend.index' ? 'active' : '' }}">
                                    <a href="{{ route('works-paperwork-progress-register.executionend.index') }}"
                                        class="pc-link">

                                        {{ __('Execution End') }}
                                    </a>
                                </li>


                            </ul>
                        </li>

                        <li
                            class="pc-item {{ $routeName == 'works-paperwork-progress-register.variation.index' ? 'active' : '' }}">
                            <a href="{{ route('works-paperwork-progress-register.variation.index') }}"
                                class="pc-link">
                                <span class="pc-micon"><i class="ti ti-clipboard-list"></i></span>
                                {{ __('Variation') }}
                            </a>
                        </li>
                        <li
                            class="pc-item {{ $routeName == 'works-paperwork-progress-register.inspection-log.index' ? 'active' : '' }}">
                            <a href="{{ route('works-paperwork-progress-register.inspection-log.index') }}"
                                class="pc-link">
                                <span class="pc-micon"><i class="ti ti-clipboard-list"></i></span>
                                {{ __('Inspection Log') }}
                            </a>
                        </li>
                    @endif
                    {{--  --}}


                    @if (\Auth::user() && in_array(strtolower(\Auth::user()->type), ['owner', 'admin', 'super admin', 'department admin']))
                        <li class="pc-item pc-caption">
                            <label>{{ __('Reports') }}</label>
                            <i class="ti ti-dashboard"></i>
                        </li>

                        {{-- Reports Menu --}}
                        <li
                            class="pc-item pc-hasmenu {{ Str::startsWith($routeName, 'report.') ? 'active pc-trigger' : '' }}">

                            <a href="javascript:void(0);" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-database"></i></span>
                                <span class="pc-mtext">{{ __('Report') }}</span>
                                <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                            </a>

                            <ul class="pc-submenu"
                                style="display: {{ Str::startsWith($routeName, 'report.') ? 'block' : 'none' }}">

                                <li class="pc-item {{ $routeName == 'report.master.employee' ? 'active' : '' }}">
                                    <a href="{{ route('report.master.employee') }}" class="pc-link">
                                        <span class="pc-micon"><i class="ti ti-users"></i></span>
                                        <span class="pc-mtext">{{ __('Employee Report') }}</span>
                                    </a>
                                </li>
                                <li class="pc-item {{ Str::startsWith($routeName, 'report.employee-safety') || Str::startsWith($routeName, 'report.employee.safety') ? 'active' : '' }}">
                                    <a href="{{ route('report.employee-safety.index') }}" class="pc-link">
                                        <span class="pc-micon"><i class="ti ti-shield-check"></i></span>
                                        <span class="pc-mtext">{{ __('Employee Safety') }}</span>
                                    </a>
                                </li>
                                <li class="pc-item {{ Str::startsWith($routeName, 'emergency-contact-report.index') ? 'active' : '' }}">
                                    <a href="{{ route('emergency-contact-report.index') }}" class="pc-link">
                                        <span class="pc-micon"><i class="ti ti-phone-call"></i></span>
                                        <span class="pc-mtext">{{ __('Emergency Contact Report') }}</span>
                                    </a>
                                </li>
                                <li class="pc-item {{ Str::startsWith($routeName, 'report.ph16') ? 'active' : '' }}">
                                    <a href="{{ route('report.ph16.index') }}" class="pc-link">
                                        <span class="pc-micon"><i class="ti ti-chart-bar"></i></span>
                                        <span class="pc-mtext">{{ __('PH 16 Works Report') }}</span>
                                    </a>
                                </li>
                                @if (Route::has('report.complaints.index'))
                                    <li class="pc-item {{ Str::startsWith($routeName, 'report.complaints') ? 'active' : '' }}">
                                        <a href="{{ route('report.complaints.index') }}" class="pc-link">
                                            <span class="pc-micon"><i class="ti ti-shield-check"></i></span>
                                            <span class="pc-mtext">{{ __('Station Complaints Records') }}</span>
                                        </a>
                                    </li>
                                @endif
                                @if (Route::has('report.ambush.index'))
                                    <li class="pc-item {{ Str::startsWith($routeName, 'report.ambush') ? 'active' : '' }}">
                                        <a href="{{ route('report.ambush.index') }}" class="pc-link">
                                            <span class="pc-micon"><i class="ti ti-eye"></i></span>
                                            <span class="pc-mtext">{{ __('Ambush Check Records') }}</span>
                                        </a>
                                    </li>
                                @endif
                                @if (Route::has('report.my-movement.index'))
                                    <li class="pc-item {{ Str::startsWith($routeName, 'report.my-movement') ? 'active' : '' }}">
                                        <a href="{{ route('report.my-movement.index') }}" class="pc-link">
                                            <span class="pc-micon"><i class="ti ti-walk"></i></span>
                                            <span class="pc-mtext">{{ __('My Movement Records') }}</span>
                                        </a>
                                    </li>
                                @endif
                                @if (Route::has('report.staff-counseling.index'))
                                    <li class="pc-item {{ Str::startsWith($routeName, 'report.staff-counseling') ? 'active' : '' }}">
                                        <a href="{{ route('report.staff-counseling.index') }}" class="pc-link">
                                            <span class="pc-micon"><i class="ti ti-users"></i></span>
                                            <span class="pc-mtext">{{ __('Staff Counseling Records') }}</span>
                                        </a>
                                    </li>
                                @endif
                                @if (Route::has('report.award-record.index'))
                                    <li class="pc-item {{ Str::startsWith($routeName, 'report.award-record') ? 'active' : '' }}">
                                        <a href="{{ route('report.award-record.index') }}" class="pc-link">
                                            <span class="pc-micon"><i class="ti ti-award"></i></span>
                                            <span class="pc-mtext">{{ __('Award Records') }}</span>
                                        </a>
                                    </li>
                                @endif




                            </ul>
                        </li>
                    @endif



                    @if (\Auth::user() && \Auth::user()->type == 'owner')
                        <li class="pc-item pc-caption">
                            <label>{{ __('Frontend Login') }}</label>
                            <i class="ti ti-dashboard"></i>
                        </li>

                        {{-- Reports Menu --}}
                        <li
                            class="pc-item pc-hasmenu {{ Str::startsWith($routeName, 'frontend-login.') ? 'active pc-trigger' : '' }}">

                            <a href="javascript:void(0);" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-database"></i></span>
                                <span class="pc-mtext">{{ __('Frontend Login') }}</span>
                                <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                            </a>

                            <ul class="pc-submenu"
                                style="display: {{ Str::startsWith($routeName, 'frontend-login.') ? 'block' : 'none' }}">

                                <li
                                    class="pc-item {{ $routeName == 'frontend-login.background-login.index' ? 'active' : '' }}">
                                    <a href="{{ route('frontend-login.background-login.index') }}" class="pc-link">
                                        Frontend Login</a>
                                </li>

                            </ul>
                        </li>
                    @endif




                    {{-- <li
                    class="pc-item {{ in_array($routeName, ['dashboard-by-calendar' , '']) ? 'active' : '' }}">
                    <a href="{{ route('dashboard-by-calendar') }}" class="pc-link">
                        <span class="pc-micon"><i data-feather="calendar"></i></span>
                        <span class="pc-mtext">{{ __('Assign Complints') }}</span>
                    </a>
                </li> --}}

                    {{-- @if (Gate::check('manage order calendar') || Gate::check('manage expense') || Gate::check('manage invoice') || Gate::check('manage customer') || Gate::check('manage measurement') || Gate::check('manage order') || Gate::check('manage today order delivery') || Gate::check('manage today order') || Gate::check('manage contact') || Gate::check('manage note'))
                    <li class="pc-item pc-caption">
                        <label>{{ __('Business Management') }}</label>
                        <i class="ti ti-chart-arcs"></i>
                    </li>

                    @if (Gate::check('manage customer'))
                        <li class="pc-item {{ in_array($routeName, ['customer.index']) ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('customer.index') }}">
                                <span class="pc-micon"><i data-feather="user-check"></i></span>
                                <span class="pc-mtext">{{ __('Employee') }}</span>
                            </a>
                        </li>
                    @endif


                    @if (Gate::check('manage order calendar'))
                        <li class="pc-item {{ in_array($routeName, ['calendar']) ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('calendar') }}">
                                <span class="pc-micon"><i data-feather="calendar"></i></span>
                                <span class="pc-mtext">{{ __('Calendar') }}</span>
                            </a>
                        </li>
                    @endif


                    @if (Gate::check('manage contact'))
                        <li class="pc-item {{ in_array($routeName, ['contact.index']) ? 'active' : '' }}">
                            <a class="pc-link" href="{{ route('contact.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-phone-call"></i></span>
                                <span class="pc-mtext" class="pc-mtext">{{ __('Employee Contact Diary') }}</span>
                            </a>
                        </li>
                    @endif


                    @if (Gate::check('manage note'))
                        <li class="pc-item {{ in_array($routeName, ['note.index']) ? 'active' : '' }} ">
                            <a class="pc-link" href="{{ route('note.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-notebook"></i></span>
                                <span class="pc-mtext" class="pc-mtext">{{ __('Notice Board') }}</span>
                            </a>
                        </li>
                    @endif
                @endif --}}

                    {{-- @if (Gate::check('manage measurement unit') || Gate::check('manage tax') || Gate::check('manage cloth type') || Gate::check('manage notification'))
                    <li class="pc-item pc-caption">
                        <label>{{ __('System Configuration') }}</label>
                        <i class="ti ti-chart-arcs"></i>
                    </li>

                    @if (Gate::check('manage notification'))
                        <li class="pc-item {{ in_array($routeName, ['notification.index']) ? 'active' : '' }} ">
                            <a href="{{ route('notification.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-bell"></i></span>
                                <span class="pc-mtext">{{ __('Email Notification') }}</span>
                            </a>
                        </li>
                    @endif
                @endif --}}


                    @if (Gate::check('manage pricing packages') ||
                            Gate::check('manage pricing transation') ||
                            Gate::check('manage account settings') ||
                            Gate::check('manage password settings') ||
                            Gate::check('manage general settings') ||
                            Gate::check('manage email settings') ||
                            Gate::check('manage payment settings') ||
                            Gate::check('manage company settings') ||
                            Gate::check('manage seo settings') ||
                            Gate::check('manage google recaptcha settings'))
                        <li class="pc-item pc-caption">
                            <label>{{ __('System Settings') }}</label>
                            <i class="ti ti-chart-arcs"></i>
                        </li>

                        @if (Gate::check('manage FAQ') || Gate::check('manage Page'))
                            <li
                                class="pc-item pc-hasmenu {{ in_array($routeName, ['homepage.index', 'FAQ.index', 'pages.index', 'footerSetting']) ? 'active' : '' }}">
                                
                                   <a href="javascript:void(0);" class="pc-link">

                                    <span class="pc-micon">
                                        <i class="ti ti-layout-rows"></i>
                                    </span>
                                    <span class="pc-mtext">{{ __('CMS') }}</span>
                                    <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                                </a>
                                <ul class="pc-submenu"
                                    style="display: {{ in_array($routeName, ['homepage.index', 'FAQ.index', 'pages.index', 'footerSetting']) ? 'block' : 'none' }}">
                                    @if (Gate::check('manage home page'))
                                        <li
                                            class="pc-item {{ in_array($routeName, ['homepage.index']) ? 'active' : '' }} ">
                                            <a href="{{ route('homepage.index') }}"
                                                class="pc-link">{{ __('Home Page') }}</a>
                                        </li>
                                    @endif
                                    @if (Gate::check('manage Page'))
                                        <li
                                            class="pc-item {{ in_array($routeName, ['pages.index']) ? 'active' : '' }} ">
                                            <a href="{{ route('pages.index') }}"
                                                class="pc-link">{{ __('Custom Page') }}</a>
                                        </li>
                                    @endif
                                    @if (Gate::check('manage FAQ'))
                                        <li
                                            class="pc-item {{ in_array($routeName, ['FAQ.index']) ? 'active' : '' }} ">
                                            <a href="{{ route('FAQ.index') }}"
                                                class="pc-link">{{ __('FAQ') }}</a>
                                        </li>
                                    @endif
                                    @if (Gate::check('manage footer'))
                                        <li
                                            class="pc-item {{ in_array($routeName, ['footerSetting']) ? 'active' : '' }} ">
                                            <a href="{{ route('footerSetting') }}"
                                                class="pc-link">{{ __('Footer') }}</a>
                                        </li>
                                    @endif
                                    @if (Gate::check('manage auth page'))
                                        <li
                                            class="pc-item {{ in_array($routeName, ['authPage.index']) ? 'active' : '' }} ">
                                            <a href="{{ route('authPage.index') }}"
                                                class="pc-link">{{ __('Auth Page') }}</a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif


                        {{-- @if (Gate::check('manage account settings') || Gate::check('manage password settings') || Gate::check('manage general settings') || Gate::check('manage email settings') || Gate::check('manage payment settings') || Gate::check('manage company settings') || Gate::check('manage seo settings') || Gate::check('manage google recaptcha settings'))
                        <li class="pc-item {{ in_array($routeName, ['setting.index']) ? 'active' : '' }} ">
                            <a href="{{ route('setting.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-settings"></i></span>
                                <span class="pc-mtext">{{ __('Settings') }}</span>
                            </a>
                        </li>
                    @endif --}}

                    @endif
                @endif
            </ul>
            <div class="w-100 text-center">
                <div class="badge theme-version badge rounded-pill bg-light text-dark f-12"></div>
            </div>
        </div>
    </div>



<script>
(function () {
    function addSidebarIcons() {
        const sidebar = document.querySelector('.pc-sidebar');

        if (!sidebar) return;

        const iconRules = [
            // Dashboard
            [/dashboard|home page/i, 'ti ti-dashboard'],

            // Users and roles
            [/employee|staff|user/i, 'ti ti-users'],
            [/roles?|authority|complaint|rectify/i, 'ti ti-shield-lock'],
// Master
[/designation|sanction post/i, 'ti ti-id-badge'],
[/attend(?:ance)?\s*block/i, 'ti ti-calendar-check'],
[/type\s*of\s*block/i, 'ti ti-category'],
[/division|department|organization/i, 'ti ti-building'],
[/station|working place|location|lobby|room/i, 'ti ti-map-pin'],

            // Paperwork Timeline
            [/approval\s*&?\s*listing/i, 'ti ti-list-check'],
            [/execution\s*start/i, 'ti ti-player-play'],
            [/execution\s*end/i, 'ti ti-circle-check'],
            [/paperwork\s*timeline|timeline/i, 'ti ti-history'],

            // Calendar and movement
            [/movement|calendar/i, 'ti ti-calendar'],

            // Inspection and reports
            [/ambush/i, 'ti ti-eye'],
            [/inspection|safety|audit/i, 'ti ti-clipboard-check'],
            [/report|chart|statistics/i, 'ti ti-chart-bar'],

            // Master data and media
            [/master|database/i, 'ti ti-database'],
            [/popup|slider|image|photo/i, 'ti ti-photo'],

            // Messages and documents
            [/message|letter|mail/i, 'ti ti-mail'],
            [/document|file|original|correction|circular|jpo/i, 'ti ti-file-text'],
            [/manual|book|study|case study/i, 'ti ti-book'],

            // Alerts and projects
            [/alert|emergency|accident/i, 'ti ti-alert-triangle'],
            [/project|scope of work|new work/i, 'ti ti-briefcase'],

            // Finance and approvals
            [/estimate|finance|payment|variation/i, 'ti ti-currency-rupee'],
            [/approval|sanction|execution/i, 'ti ti-checklist'],
            [/tender|survey|inspection log/i, 'ti ti-clipboard-list'],

            // Other sections
            [/safety drive|vehicle|rake|load type|load stable|clearance/i, 'ti ti-truck'],
            [/leave|attendance|block/i, 'ti ti-calendar-check'],
            [/amenity|facility|proficiency/i, 'ti ti-award'],
            [/frontend|login|auth page/i, 'ti ti-login'],
            [/setting|configuration|cms|custom page|footer|faq/i, 'ti ti-settings'],
            [/import|upload/i, 'ti ti-cloud-upload'],
            [/logged history|history/i, 'ti ti-history'],
            [/\b(esp|sip)\b/i, 'ti ti-file-check'],
            [/goods/i, 'ti ti-package'],
            [/coaching/i, 'ti ti-train'],
            [/training/i, 'ti ti-school'],
            [/counsel|counselling/i, 'ti ti-messages'],
            [/pme|refresher|gradation/i, 'ti ti-file-certificate']
        ];

        sidebar.querySelectorAll('a.pc-link').forEach(function (link) {
            // Existing icons ko duplicate nahi karna
            if (link.querySelector('.pc-micon')) return;

            const textElement = link.querySelector('.pc-mtext');

            const label = (
                textElement?.textContent ||
                link.getAttribute('aria-label') ||
                link.textContent ||
                ''
            ).replace(/\s+/g, ' ').trim();

            if (!label) return;

            let iconClass = 'ti ti-point';

            for (const [pattern, icon] of iconRules) {
                if (pattern.test(label)) {
                    iconClass = icon;
                    break;
                }
            }

            const wrapper = document.createElement('span');
            wrapper.className = 'pc-micon';

            const icon = document.createElement('i');
            icon.className = iconClass;
            icon.setAttribute('aria-hidden', 'true');

            wrapper.appendChild(icon);
            link.insertBefore(wrapper, link.firstChild);
        });
    }

    // Page load timing ko handle karega
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', addSidebarIcons);
    } else {
        addSidebarIcons();
    }
})();
</script>


<script>
(function () {
    'use strict';
    // =========================================================================
    // Sidebar Smooth Scroll & Position Retention
    // Prevents sidebar reset to top on navigation & centers active item
    // =========================================================================
    function getScrollContainer() {
        return document.querySelector('.navbar-content .simplebar-content-wrapper') 
            || document.querySelector('.navbar-content') 
            || document.querySelector('.pc-sidebar .navbar-wrapper');
    }

    function saveSidebarScroll() {
        const scroller = getScrollContainer();
        if (scroller && scroller.scrollTop > 0) {
            sessionStorage.setItem('sidebar_scroll_position', String(scroller.scrollTop));
        }
    }

    function restoreSidebarScroll() {
        const scroller = getScrollContainer();
        if (!scroller) return;

        const saved = sessionStorage.getItem('sidebar_scroll_position');
        const activeItem = document.querySelector('.pc-sidebar .pc-item.active');

        if (saved !== null && parseInt(saved, 10) > 0) {
            scroller.scrollTop = parseInt(saved, 10);
        } else if (activeItem) {
            activeItem.scrollIntoView({ block: 'center', inline: 'nearest', behavior: 'smooth' });
        }
    }

    // Save scroll position when clicking any sidebar navigation link
    document.addEventListener('click', function (e) {
        const link = e.target.closest('.pc-sidebar a');
        if (link) {
            const href = link.getAttribute('href');
            if (href && href !== '#' && !href.startsWith('javascript:')) {
                saveSidebarScroll();
            }
        }
    }, true);

    // Save before unload
    window.addEventListener('beforeunload', saveSidebarScroll);

    // Restore on load with slight delay for SimpleBar
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(restoreSidebarScroll, 50);
            setTimeout(restoreSidebarScroll, 200);
        });
    } else {
        setTimeout(restoreSidebarScroll, 50);
        setTimeout(restoreSidebarScroll, 200);
    }

    window.addEventListener('load', function () {
        setTimeout(restoreSidebarScroll, 80);
        setTimeout(restoreSidebarScroll, 300);
    });
})();
</script>

</nav>
