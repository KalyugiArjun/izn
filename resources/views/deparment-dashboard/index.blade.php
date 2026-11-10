@extends('layouts.app')
@section('page-title')
    {{ __('Dashboard') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item" aria-current="page">{{ __('Dashboard') }}</li>
@endsection


@push('style-page')
    <style>
        /* -- Metric Cards -- */
        .stat-card {
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, .06);
            transition: transform .15s, box-shadow .15s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, .08);
        }

        .stat-card .avtar {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-card h4 {
            font-size: 1.6rem;
            font-weight: 700;
        }

        /* -- Chart Cards -- */
        .chart-card {
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, .06);
        }

        .chart-card .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(0, 0, 0, .06);
            padding: 14px 18px;
        }

        .chart-card .card-header h5 {
            font-size: .92rem;
            font-weight: 600;
            letter-spacing: .01em;
            margin: 0;
        }

        .chart-card .card-body {
            padding: 16px 18px;
        }

        /* -- Chart Legend -- */
        .chart-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 12px;
            font-size: .78rem;
            color: #6c757d;
        }

        .chart-legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .chart-legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 2px;
            flex-shrink: 0;
        }

        /* -- Progress Bars -- */
        .dept-bar-row {
            margin-bottom: 10px;
        }

        .dept-bar-label {
            font-size: .78rem;
            color: #6c757d;
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .dept-bar-track {
            height: 8px;
            background: #f0f0f0;
            border-radius: 4px;
            overflow: hidden;
            position: relative;
        }

        .dept-bar-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 1s cubic-bezier(.4, 0, .2, 1);
        }

        .dept-bar-critical {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            border-radius: 4px;
            background: rgba(220, 53, 69, .55);
        }
    </style>
@endpush

@push('script-page')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            /* -- Shared helpers -- */
            const isDark = document.documentElement.classList.contains('dark') ||
                window.matchMedia('(prefers-color-scheme: dark)').matches;
            const textCol = isDark ? '#c2c0b6' : '#6c757d';
            const gridCol = isDark ? 'rgba(255,255,255,.06)' : 'rgba(0,0,0,.06)';
            const WHITE = isDark ? '#1e1e2d' : '#ffffff';

            /* ------------------------------
               1.  STATUS DONUT CHART
            ------------------------------ */
            let donutChart;

            const donutCtx = document.getElementById('statusDonutChart');

            if (donutCtx) {

                if (donutChart) {
                    donutChart.destroy();
                }

                donutChart = new Chart(donutCtx, {
                    type: 'doughnut',
                    data: {
                        labels: [
                            'Open / Pending',
                            'Rectified',
                            'Reopened',
                            'Closed',
                            'Safety Critical'
                        ],
                        datasets: [{
                            data: [
                                {{ $result['pendingComplaints'] }},
                                {{ $result['resolvedComplaints'] }},
                                {{ $result['reopenedComplaints'] }},
                                {{ $result['closedComplaints'] }},
                                {{ $result['safetyComplaints'] }}
                            ],
                            backgroundColor: ['#EF9F27', '#28a745', '#17a2b8', '#2ca58d',
                                '#dc3545'
                            ],
                        }]
                    }
                });
            }

            /* ------------------------------
               2.  MONTHLY TREND LINE CHART
            ------------------------------ */
            const trendCtx = document.getElementById('monthlyTrendChart');
            if (trendCtx) {
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($result['organizationByMonth']['label']) !!},
                        datasets: [{
                                label: '{{ __('New Complaints') }}',
                                data: {!! json_encode($result['organizationByMonth']['data']) !!},
                                borderColor: '#378ADD',
                                backgroundColor: 'rgba(55,138,221,0.10)',
                                borderWidth: 2,
                                pointRadius: 4,
                                pointBackgroundColor: '#fff',
                                pointBorderColor: '#378ADD',
                                pointBorderWidth: 2,
                                pointHoverRadius: 6,
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: '{{ __('Payments / Resolved') }}',
                                data: {!! json_encode($result['paymentByMonth']['data']) !!},
                                borderColor: '#2ca58d',
                                backgroundColor: 'transparent',
                                borderWidth: 2,
                                borderDash: [6, 4],
                                pointRadius: 4,
                                pointBackgroundColor: '#fff',
                                pointBorderColor: '#2ca58d',
                                pointBorderWidth: 2,
                                pointHoverRadius: 6,
                                fill: false,
                                tension: 0.4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                bodySpacing: 6,
                                padding: 10
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: textCol,
                                    font: {
                                        size: 11
                                    },
                                    maxRotation: 30
                                },
                                grid: {
                                    color: gridCol
                                },
                                border: {
                                    display: false
                                }
                            },
                            y: {
                                ticks: {
                                    color: textCol,
                                    font: {
                                        size: 11
                                    }
                                },
                                grid: {
                                    color: gridCol
                                },
                                border: {
                                    display: false
                                },
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            /* ------------------------------
               3.  STATUS HORIZONTAL BAR
            ------------------------------ */
            const statusBarCtx = document.getElementById('statusBarChart');
            if (statusBarCtx) {
                new Chart(statusBarCtx, {
                    type: 'bar',
                    data: {
                        labels: [
                            '{{ __('Open') }}',
                            '{{ __('Rectified') }}',
                            '{{ __('Reopened') }}',
                            '{{ __('Closed') }}',
                            '{{ __('Safety Critical') }}'
                        ],
                        datasets: [{
                            label: '{{ __('Count') }}',
                            data: [
                                {{ $result['pendingComplaints'] }},
                                {{ $result['resolvedComplaints'] }},
                                {{ $result['reopenedComplaints'] }},
                                {{ $result['closedComplaints'] }},
                                {{ $result['safetyComplaints'] }}
                            ],
                            backgroundColor: ['#EF9F27', '#28a745', '#17a2b8', '#2ca58d',
                                '#dc3545'
                            ],
                            borderRadius: 6,
                            borderSkipped: false,
                            barThickness: 22
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: ctx => ` ${ctx.parsed.x} complaints`
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: textCol,
                                    font: {
                                        size: 11
                                    }
                                },
                                grid: {
                                    color: gridCol
                                },
                                border: {
                                    display: false
                                },
                                beginAtZero: true
                            },
                            y: {
                                ticks: {
                                    color: textCol,
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    }
                                },
                                grid: {
                                    color: 'transparent'
                                },
                                border: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            /* ------------------------------
               4.  DEPARTMENT BAR CHART
                   (grouped: total + critical)
                   Requires $result['byDepartment']
            ------------------------------ */
            @isset($result['byDepartment'])
                const deptCtx = document.getElementById('deptChart');
                if (deptCtx) {
                    const rawLabels = {!! json_encode($result['byDepartment']->pluck('department_name')) !!};
                    const rawTotals = {!! json_encode($result['byDepartment']->pluck('total')) !!};
                    const rawCrit   = {!! json_encode($result['byDepartment']->pluck('critical')) !!};

                    const deptGradTotal = deptCtx.getContext('2d').createLinearGradient(0, 0, 0, 260);
                    deptGradTotal.addColorStop(0, 'rgba(0, 150, 136, 0.95)');
                    deptGradTotal.addColorStop(1, 'rgba(0, 77, 64, 0.80)');

                    const deptGradCrit = deptCtx.getContext('2d').createLinearGradient(0, 0, 0, 260);
                    deptGradCrit.addColorStop(0, 'rgba(239, 68, 68, 0.95)');
                    deptGradCrit.addColorStop(1, 'rgba(153, 27, 27, 0.85)');

                    new Chart(deptCtx, {
                        type: 'bar',
                        data: {
                            labels: rawLabels,
                            datasets: [
                                {
                                    label: '{{ __('Total Complaints') }}',
                                    data: rawTotals,
                                    backgroundColor: deptGradTotal,
                                    borderRadius: { topLeft: 7, topRight: 7, bottomLeft: 3, bottomRight: 3 },
                                    borderSkipped: false,
                                    categoryPercentage: 0.65,
                                    barPercentage: 0.75,
                                },
                                {
                                    label: '{{ __('Safety Critical') }}',
                                    data: rawCrit.map(v => v > 0 ? v : null),
                                    backgroundColor: deptGradCrit,
                                    borderRadius: { topLeft: 7, topRight: 7, bottomLeft: 3, bottomRight: 3 },
                                    borderSkipped: false,
                                    categoryPercentage: 0.65,
                                    barPercentage: 0.75,
                                    skipNull: true,
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: { duration: 900, easing: 'easeOutQuart' },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        pointStyle: 'rectRounded',
                                        padding: 18,
                                        color: textCol,
                                        font: { size: 12, weight: '600' }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(15,40,76,0.92)',
                                    titleColor: '#ffffff',
                                    bodyColor: '#e2e8f0',
                                    padding: 12,
                                    cornerRadius: 10,
                                    callbacks: {
                                        label: ctx => ` ${ctx.dataset.label}: ${ctx.parsed.y ?? 0}`
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    ticks: { color: textCol, font: { size: 11, weight: '500' }, maxRotation: 30, autoSkip: false },
                                    grid: { color: 'transparent' },
                                    border: { display: false }
                                },
                                y: {
                                    ticks: { color: textCol, font: { size: 11 }, stepSize: 1 },
                                    grid: { color: gridCol },
                                    border: { display: false },
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            @endisset

            /* ------------------------------
               5.  DAILY COMPLAINTS (last 14d)
                   Requires $result['dailyTrend']
            ------------------------------ */
            @isset($result['dailyTrend'])
                const dailyCtx = document.getElementById('dailyChart');
                if (dailyCtx) {
                    new Chart(dailyCtx, {
                        type: 'line',
                        data: {
                            labels: {!! json_encode($result['dailyTrend']->pluck('date')) !!},
                            datasets: [{
                                label: '{{ __('Complaints') }}',
                                data: {!! json_encode($result['dailyTrend']->pluck('count')) !!},
                                borderColor: '#2ca58d',
                                backgroundColor: 'rgba(44,165,141,0.12)',
                                borderWidth: 2,
                                pointRadius: 3,
                                pointBackgroundColor: '#2ca58d',
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        color: textCol,
                                        font: {
                                            size: 10
                                        },
                                        maxRotation: 40,
                                        autoSkip: false
                                    },
                                    grid: {
                                        color: gridCol
                                    },
                                    border: {
                                        display: false
                                    }
                                },
                                y: {
                                    ticks: {
                                        color: textCol,
                                        font: {
                                            size: 11
                                        }
                                    },
                                    grid: {
                                        color: gridCol
                                    },
                                    border: {
                                        display: false
                                    },
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            @endisset

            /* -- Animate progress bars -- */
            document.querySelectorAll('.dept-bar-fill[data-width]').forEach(el => {
                requestAnimationFrame(() => {
                    el.style.width = el.dataset.width + '%';
                });
            });

        });
    </script>
@endpush

@section('content')
    <div class="row">

        <!-- ===== WELCOME USER INFO BANNER ===== -->
        <div class="col-12 mb-3 mt-1">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                <div class="card-body p-4 text-white">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            @php
                                $users = Auth::user();
                                $profile = asset(Storage::url('upload/profile'));
                                $profilePath = 'app/public/upload/profile/' . $users->profile;
                                $publicPath = 'storage/upload/profile/' . $users->profile;
                                $profileExists = !empty($users->profile) && (file_exists(storage_path($profilePath)) || file_exists(public_path($publicPath)));
                            @endphp
                            @if($profileExists)
                                <div class="rounded-circle overflow-hidden d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; border: 2px solid #e0ab3e; background-color: rgba(224, 171, 62, 0.1);">
                                    <img src="{{ $profile . '/' . $users->profile }}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            @else
                                <div class="rounded-circle bg-primary bg-opacity-25 p-2 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; border: 2px solid #e0ab3e;">
                                    <i class="ti ti-user text-warning f-28"></i>
                                </div>
                            @endif
                            <div>
                                <h4 class="mb-1 text-white font-bold" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                                    {{ __('Welcome Back,') }} {{ Auth::user()->name }}
                                </h4>
                                <p class="mb-0 opacity-75 fs-6" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 14px; margin-top: 5px;">
                                    @php
                                        $employee = \App\Models\MasterEmployee::where('mobile', Auth::user()->phone_number)->with('stations')->first();
                                        $stationNames = $employee && $employee->stations->count() > 0 ? $employee->stations->pluck('name')->implode(', ') : 'N/A';

                                        $designationModel = \App\Models\Designation::find(Auth::user()->designation);
                                        $designationName = $designationModel ? $designationModel->name : (Auth::user()->designation ?: 'N/A');
                                    @endphp
                                    <span class="badge me-2" style="font-size: 13px; padding: 7px 14px; background-color: rgba(224, 171, 62, 0.2); color: #ffffff; border: 1px solid rgba(224, 171, 62, 0.4); font-family: 'Plus Jakarta Sans', sans-serif;"><i class="ti ti-briefcase" style="color: #e0ab3e; margin-right: 4px;"></i> DESIGNATION: <span style="font-weight: 700; color: #fce3a1;">{{ strtoupper($designationName) }}</span></span>
                                    <span class="badge" style="font-size: 13px; padding: 7px 14px; background-color: rgba(224, 171, 62, 0.2); color: #ffffff; border: 1px solid rgba(224, 171, 62, 0.4); font-family: 'Plus Jakarta Sans', sans-serif;"><i class="ti ti-map-pin" style="color: #e0ab3e; margin-right: 4px;"></i> STATION: <span style="font-weight: 700; color: #fce3a1;">{{ strtoupper($stationNames) }}</span></span>
                                </p>
                            </div>
                        </div>
                        <div class="text-end d-flex flex-column align-items-md-end align-items-start gap-2">
                            <span class="text-white-50 fs-7" style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 13px; color: rgba(255, 255, 255, 0.75) !important;"><i class="ti ti-calendar" style="color: #e0ab3e; margin-right: 4px;"></i> {{ date('d M Y') }}</span>
                            <a href="{{ route('employee-profile.index') }}" class="btn btn-sm" style="background: rgba(224, 171, 62, 0.2); border: 1px solid rgba(224, 171, 62, 0.5); color: #fce3a1; font-weight: 600; font-size: 13px; border-radius: 8px; padding: 6px 14px;">
                                <i class="ti ti-user me-1" style="color: #e0ab3e;"></i> {{ __('My Profile (A-Z Details)') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $dashIsAwardAuth = Auth::user()->isAwardNominationAuthorized();
            $dashIsTiSafety  = Auth::user()->isTiSafety();
            $dashIsSrDom     = Auth::user()->isSrDom();
            $dashIsPlanning  = Auth::user()->isPlanningWork();
            $dashIsPh16Auth  = Auth::user()->isPh16Authorized();
            $cardColClass    = ($dashIsPlanning && $dashIsPh16Auth) ? 'col-xl-3 col-md-6' : 'col-lg-4 col-md-6';
        @endphp

        {{-- ================= 1. TI SAFETY: AWARD NOMINATION MANAGEMENT SECTION ================= --}}
        @if ($dashIsTiSafety)
            @php
                $tiPendingCount = \App\Models\SelfNomination::where('status', 0)->count();
                $tiForwardedCount = \App\Models\SelfNomination::where('status', '>=', 1)->count();
            @endphp
            <div class="col-12 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h4 class="mb-1 font-bold text-dark" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                            <i class="ti ti-award text-warning me-2"></i> {{ __('Award Nomination Management') }}
                        </h4>
                        <p class="text-muted mb-0 fs-6">{{ __('Review, verify, and forward staff meritorious safety nominations to Sr. DOM.') }}</p>
                    </div>
                    <span class="badge bg-light-info text-info border border-info px-3 py-2 rounded-pill font-bold fs-7">
                        <i class="ti ti-shield-check me-1"></i>{{ __('DESIGNATION: TI SAFETY') }}
                    </span>
                </div>

                <div class="row g-4">
                    <!-- 1. Pending Nominations -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden" style="transition: transform 0.2s, box-shadow 0.2s; border-left: 5px solid #f59e0b !important;">
                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="rounded-3 p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; background: rgba(245, 158, 11, 0.1); color: #d97706;">
                                            <i class="ti ti-clock" style="font-size: 30px;"></i>
                                        </div>
                                        <span class="badge px-3 py-2 rounded-pill font-bold" style="color: #b45309; background: #fef3c7; border: 1px solid #fde68a;">
                                            {{ $tiPendingCount }} {{ __('PENDING') }}
                                        </span>
                                    </div>
                                    <h4 class="font-bold mb-2 text-dark" style="font-size: 1.25rem;">{{ __('1. Pending Nominations') }}</h4>
                                    <p class="text-muted mb-3" style="font-size: 0.9rem; line-height: 1.5;">
                                        {{ __('Review staff award nominations submitted by stations, check meritorious work descriptions, and forward with your recommendation to Sr. DOM.') }}
                                    </p>
                                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-4" style="background-color: #fffbeb; border: 1px dashed #fcd34d;">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="ti ti-alert-circle text-warning fs-5"></i>
                                            <span class="text-muted small fw-semibold">{{ __('Awaiting TI Review:') }}</span>
                                        </div>
                                        <span class="badge bg-warning text-dark font-bold px-3 py-1 fs-6">{{ $tiPendingCount }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('authority.award-nomination.index', ['tab' => 'pending']) }}" class="btn w-100 rounded-3 py-2 font-semibold text-white d-flex align-items-center justify-content-center gap-2" style="background-color: #f59e0b; border-color: #f59e0b;">
                                    <span>{{ __('Open Pending Nominations') }}</span>
                                    <i class="ti ti-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Forwarded Nominations -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden" style="transition: transform 0.2s, box-shadow 0.2s; border-left: 5px solid #06b6d4 !important;">
                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="rounded-3 p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; background: rgba(6, 182, 212, 0.1); color: #0891b2;">
                                            <i class="ti ti-send" style="font-size: 30px;"></i>
                                        </div>
                                        <span class="badge px-3 py-2 rounded-pill font-bold" style="color: #0e7490; background: #cffafe; border: 1px solid #a5f3fc;">
                                            {{ $tiForwardedCount }} {{ __('FORWARDED') }}
                                        </span>
                                    </div>
                                    <h4 class="font-bold mb-2 text-dark" style="font-size: 1.25rem;">{{ __('2. Forwarded Nominations') }}</h4>
                                    <p class="text-muted mb-3" style="font-size: 0.9rem; line-height: 1.5;">
                                        {{ __('Track all nominations recommended and forwarded by you to Sr. DOM for final award decisions and certificates.') }}
                                    </p>
                                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-4" style="background-color: #ecfeff; border: 1px dashed #67e8f9;">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="ti ti-circle-check text-info fs-5"></i>
                                            <span class="text-muted small fw-semibold">{{ __('Total Forwarded to Sr. DOM:') }}</span>
                                        </div>
                                        <span class="badge bg-info text-white font-bold px-3 py-1 fs-6">{{ $tiForwardedCount }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('authority.award-nomination.index', ['tab' => 'forwarded']) }}" class="btn w-100 rounded-3 py-2 font-semibold text-white d-flex align-items-center justify-content-center gap-2" style="background-color: #0891b2; border-color: #0891b2;">
                                    <span>{{ __('Open Forwarded Nominations') }}</span>
                                    <i class="ti ti-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        {{-- ================= 2. SUBMITTING USER BANNER ================= --}}
        @elseif ($dashIsAwardAuth && !$dashIsSrDom)
            <!-- ===== AWARD NOMINATION SECTION ===== -->
            <div class="col-12 mb-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-left: 6px solid #f59e0b !important;">
                    <div class="card-body p-4 text-white">
                        <div class="row align-items-center g-3">
                            <div class="col-lg-7">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 p-3 d-flex align-items-center justify-content-center" style="width: 58px; height: 58px; background: rgba(245, 158, 11, 0.2); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.4); flex-shrink: 0;">
                                        <i class="ti ti-award" style="font-size: 34px;"></i>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                            <h4 class="mb-0 font-bold text-white" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                                                {{ __('Award Nomination Authority') }}
                                            </h4>
                                            <span class="badge" style="background-color: rgba(245, 158, 11, 0.25); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.5); font-size: 11px; padding: 4px 10px; font-weight: 700;">
                                                <i class="ti ti-shield-check me-1"></i>{{ __('AUTHORITY ASSIGNED') }}
                                            </span>
                                            @php
                                                $mySubmittedCount = \App\Models\SelfNomination::where('created_by', Auth::id())->count();
                                            @endphp
                                            <span class="badge" style="background-color: rgba(59, 130, 246, 0.25); color: #93c5fd; border: 1px solid rgba(59, 130, 246, 0.5); font-size: 11px; padding: 4px 10px; font-weight: 700;">
                                                <i class="ti ti-file-text me-1"></i>{{ __('My Submitted: ') }}{{ $mySubmittedCount }}
                                            </span>
                                        </div>
                                        <p class="mb-0 text-white-50 fs-6" style="color: rgba(255, 255, 255, 0.75) !important;">
                                            {{ __('Your designation is assigned by Admin to nominate staff & railway employees for Meritorious Safety & Performance Awards.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5 text-lg-end text-start">
                                <div class="d-flex flex-wrap gap-2 justify-content-lg-end">
                                    <a href="#" class="btn btn-warning text-dark font-bold px-3 py-2 rounded-3 customModal d-inline-flex align-items-center shadow-sm"
                                        data-size="lg"
                                        data-url="{{ route('authority.award-nomination.nominate.create') }}"
                                        data-title="{{ __('Create Award Nomination') }}"
                                        style="background: #f59e0b; border-color: #f59e0b; font-weight: 700;">
                                        <i class="ti ti-circle-plus me-1" style="font-size: 19px;"></i>
                                        {{ __('Create Award Nomination') }}
                                    </a>
                                    <a href="{{ route('authority.award-nomination.index') }}" class="btn btn-outline-light px-3 py-2 rounded-3 d-inline-flex align-items-center font-semibold" style="border-color: rgba(255, 255, 255, 0.35);">
                                        <i class="ti ti-list me-1"></i>
                                        {{ __('View Nominations') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($dashIsPlanning || $dashIsPh16Auth)
            <!-- ===== PH 16 WORKS SECTIONS ===== -->
            <div class="col-12 mb-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h4 class="mb-1 font-bold text-dark" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                            <i class="ti ti-briefcase text-primary me-2"></i> {{ __('PH 16 Works Management') }}
                        </h4>
                        @if ($dashIsPlanning && !$dashIsPh16Auth)
                            <p class="text-muted mb-0 fs-6">{{ __('Direct access to PH 16 New Works, Paperwork Timeline, and Variations') }}</p>
                        @elseif (!$dashIsPlanning && $dashIsPh16Auth)
                            <p class="text-muted mb-0 fs-6">{{ __('Direct access to PH 16 Inspection Log') }}</p>
                        @else
                            <p class="text-muted mb-0 fs-6">{{ __('Direct access to PH 16 New Works, Paperwork Timeline, Variations, and Inspection Logs') }}</p>
                        @endif
                    </div>
                </div>

                <div class="row g-4">
                    @if ($dashIsPlanning)
                        <!-- 1. New Work -->
                        <div class="{{ $cardColClass }}">
                            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden" style="transition: transform 0.2s, box-shadow 0.2s; border-left: 5px solid #2563eb !important;">
                                <div class="card-body p-4 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="rounded-3 p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; background: rgba(37, 99, 235, 0.1); color: #2563eb;">
                                                <i class="ti ti-file-plus" style="font-size: 30px;"></i>
                                            </div>
                                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill font-semibold">PH 16 Section 1</span>
                                        </div>
                                        <h4 class="font-bold mb-2 text-dark" style="font-size: 1.25rem;">{{ __('New Work') }}</h4>
                                        <p class="text-muted mb-4" style="font-size: 0.9rem; line-height: 1.5;">
                                            {{ __('Identify, initiate, draft, and track all newly proposed railway works under PH 16.') }}
                                        </p>
                                    </div>
                                    <a href="{{ route('works-paperwork-progress-register.identify-new-work.index') }}" class="btn btn-primary w-100 rounded-3 py-2 font-semibold d-flex align-items-center justify-content-center gap-2">
                                        <span>{{ __('Open New Work') }}</span>
                                        <i class="ti ti-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Paperwork Timeline -->
                        <div class="{{ $cardColClass }}">
                            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden" style="transition: transform 0.2s, box-shadow 0.2s; border-left: 5px solid #0d9488 !important;">
                                <div class="card-body p-4 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="rounded-3 p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; background: rgba(13, 148, 136, 0.1); color: #0d9488;">
                                                <i class="ti ti-history" style="font-size: 30px;"></i>
                                            </div>
                                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill font-semibold">PH 16 Section 2</span>
                                        </div>
                                        <h4 class="font-bold mb-2 text-dark" style="font-size: 1.25rem;">{{ __('Paperwork Timeline') }}</h4>
                                        <p class="text-muted mb-4" style="font-size: 0.9rem; line-height: 1.5;">
                                            {{ __('Track Joint Survey, Estimates, Review, Sanctions, Tendering, ESP/SIP, and Execution.') }}
                                        </p>
                                    </div>
                                    <a href="{{ route('works-paperwork-progress-register.joint-survey.index') }}" class="btn btn-success w-100 rounded-3 py-2 font-semibold d-flex align-items-center justify-content-center gap-2" style="background-color: #0d9488; border-color: #0d9488;">
                                        <span>{{ __('Open Paperwork Timeline') }}</span>
                                        <i class="ti ti-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Variation -->
                        <div class="{{ $cardColClass }}">
                            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden" style="transition: transform 0.2s, box-shadow 0.2s; border-left: 5px solid #8b5cf6 !important;">
                                <div class="card-body p-4 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="rounded-3 p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                                                <i class="ti ti-git-fork" style="font-size: 30px;"></i>
                                            </div>
                                            <span class="badge bg-purple bg-opacity-10 px-3 py-2 rounded-pill font-semibold" style="color: #8b5cf6; background: rgba(139, 92, 246, 0.1);">PH 16 Section 3</span>
                                        </div>
                                        <h4 class="font-bold mb-2 text-dark" style="font-size: 1.25rem;">{{ __('Variation') }}</h4>
                                        <p class="text-muted mb-4" style="font-size: 0.9rem; line-height: 1.5;">
                                            {{ __('View, initiate, and manage all work variation requests and quantity/budget adjustments.') }}
                                        </p>
                                    </div>
                                    <a href="{{ route('works-paperwork-progress-register.variation.index') }}" class="btn btn-primary w-100 rounded-3 py-2 font-semibold d-flex align-items-center justify-content-center gap-2" style="background-color: #7c3aed; border-color: #7c3aed;">
                                        <span>{{ __('Open Variation') }}</span>
                                        <i class="ti ti-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($dashIsPh16Auth)
                        <!-- 4. Inspection Log -->
                        <div class="{{ $cardColClass }}">
                            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden" style="transition: transform 0.2s, box-shadow 0.2s; border-left: 5px solid #f59e0b !important;">
                                <div class="card-body p-4 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="rounded-3 p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; background: rgba(245, 158, 11, 0.1); color: #d97706;">
                                                <i class="ti ti-clipboard-list" style="font-size: 30px;"></i>
                                            </div>
                                            <span class="badge px-3 py-2 rounded-pill font-semibold" style="color: #d97706; background: rgba(245, 158, 11, 0.15);">PH 16 Section 4</span>
                                        </div>
                                        <h4 class="font-bold mb-2 text-dark" style="font-size: 1.25rem;">{{ __('Inspection Log') }}</h4>
                                        <p class="text-muted mb-4" style="font-size: 0.9rem; line-height: 1.5;">
                                            {{ __('Record field inspections, official visits, progress photos, and department-wise execution details.') }}
                                        </p>
                                    </div>
                                    <a href="{{ route('works-paperwork-progress-register.inspection-log.index') }}" class="btn w-100 rounded-3 py-2 font-semibold text-white d-flex align-items-center justify-content-center gap-2" style="background-color: #d97706; border-color: #d97706;">
                                        <span>{{ __('Open Inspection Log') }}</span>
                                        <i class="ti ti-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @else
        <div class="row g-3">

            <div class="col-lg-3 col-md-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avtar bg-light-primary">
                                    <i class="ti ti-report f-24"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="mb-1">{{ __('Total Complaints') }}</p>
                                <h4 class="mb-0">{{ $result['totalComplaints'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avtar bg-light-secondary">
                                    <i class="ti ti-tool f-24"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="mb-1">{{ __('Rectify Complaints') }}</p>
                                <h4 class="mb-0">{{ $result['resolvedComplaints'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avtar bg-light-info">
                                    <i class="ti ti-repeat f-24"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="mb-1">{{ __('Reopen Complaints') }}</p>
                                <h4 class="mb-0">{{ $result['reopenedComplaints'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avtar bg-light-warning">
                                    <i class="ti ti-clock f-24"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="mb-1">{{ __('Pending Complaints') }}</p>
                                <h4 class="mb-0">{{ $result['pendingComplaints'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <div class="col-lg-3 col-md-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avtar bg-light-success">
                                    <i class="ti ti-circle-check f-24"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="mb-1">{{ __('Closed Complaints') }}</p>
                                <h4 class="mb-0">{{ $result['closedComplaints'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avtar bg-light-danger">
                                    <i class="ti ti-alert-triangle f-24"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="mb-1">{{ __('Safety Critical') }}</p>
                                <h4 class="mb-0">{{ $result['safetyComplaints'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avtar bg-light-soft-dark">
                                    <i class="ti ti-calendar-event f-24"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="mb-1">Today Complaints</p>
                                <h4 class="mb-0">{{ $result['todayComplaints'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avtar bg-light-cyan">
                                    <i class="ti ti-calendar-minus f-24"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="mb-1">Yesterday Complaints</p>
                                <h4 class="mb-0">{{ $result['yesterdayComplaints'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avtar  bg-light-orange">
                                    <i class="ti ti-calendar-stats f-24"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="mb-1">Last Month Complaints</p>
                                <h4 class="mb-0">{{ $result['lastMonthComplaints'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">

                                <div class="avtar bg-light-purple">
                                    <i class="ti ti-alert-circle f-24"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <p class="mb-1">Delay Complaints</p>
                                <h4 class="mb-0">{{ $result['delayComplaints'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                .bg-light-purple {
                    background-color: #f3e8ff !important;
                    color: #6a0deb !important;
                }

                .bg-light-orange {
                    background-color: #fff7e6 !important;
                    color: #fa6616 !important;
                }

                .bg-light-cyan {
                    background-color: #e6fffb !important;
                    color: #09ffff !important;
                }

                .bg-light-soft-dark {
                    background-color: #f5f5f5 !important;
                    color: #262626 !important;
                }
            </style>
        </div>

        <div class="row g-3 mt-1">
            <div class="col-12">
                <div class="card chart-card">
                    <div class="card-header">
                        <h5>{{ __('Recent Complaints') }}</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="dt-responsive table-responsive">
                            <table class="table table-hover advance-datatable mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('Complaint No.') }}</th>
                                        <th>{{ __('Department') }}</th>
                                        <th>{{ __('Station') }}</th>
                                        <th>{{ __('Category') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        <th>{{ __('Safety') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($result['recentComplaints'] as $i => $complaint)
                                        <tr>
                                            <td>{{ $i + 1 }}</td>
                                            <td>
                                                <span class="badge bg-light-secondary text-dark">
                                                    {{ $complaint->complaint_num }}
                                                </span>
                                            </td>
                                            <td>{{ $complaint->department->name ?? '-' }}</td>
                                            <td>{{ $complaint->station->name ?? '-' }}</td>
                                            <td>{{ $complaint->amenityCategory->name ?? '-' }}</td>
                                            <td>
                                                <span title="{{ $complaint->description }}">
                                                    {{ Str::limit($complaint->description, 40) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($complaint->is_safety_critical)
                                                    <span class="badge bg-danger">{{ __('Critical') }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($complaint->status == 0)
                                                    <span class="badge bg-warning text-dark">{{ __('Open') }}</span>
                                                @elseif($complaint->status == 1)
                                                    <span class="badge bg-success">{{ __('Rectified') }}</span>
                                                @elseif($complaint->status == 2)
                                                    <span class="badge bg-info">{{ __('Reopened') }}</span>
                                                @elseif($complaint->status == 3)
                                                    <span class="badge bg-secondary">{{ __('Closed') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $complaint->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        </div>
        @endif
    </div>
@endsection
