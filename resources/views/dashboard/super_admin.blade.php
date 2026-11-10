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

    <style>
        @media (min-width: 768px) {
            .opdoc-inline-grid {
                grid-template-columns: 1fr 1fr !important;
            }
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
                    const rawCrit = {!! json_encode($result['byDepartment']->pluck('critical')) !!};

                    /* Build gradient fills */
                    const deptGradTotal = deptCtx.getContext('2d').createLinearGradient(0, 0, 0, 260);
                    deptGradTotal.addColorStop(0, 'rgba(0, 150, 136, 0.95)'); /* Teal top */
                    deptGradTotal.addColorStop(1, 'rgba(0, 77, 64, 0.80)'); /* Deep teal bottom */

                    const deptGradCrit = deptCtx.getContext('2d').createLinearGradient(0, 0, 0, 260);
                    deptGradCrit.addColorStop(0, 'rgba(239, 68, 68, 0.95)'); /* Red top */
                    deptGradCrit.addColorStop(1, 'rgba(153, 27, 27, 0.85)'); /* Deep red bottom */

                    new Chart(deptCtx, {
                        type: 'bar',
                        data: {
                            labels: rawLabels,
                            datasets: [{
                                    label: '{{ __('Total Complaints') }}',
                                    data: rawTotals,
                                    backgroundColor: deptGradTotal,
                                    borderRadius: {
                                        topLeft: 7,
                                        topRight: 7,
                                        bottomLeft: 3,
                                        bottomRight: 3
                                    },
                                    borderSkipped: false,
                                    categoryPercentage: 0.65,
                                    barPercentage: 0.75,
                                },
                                {
                                    label: '{{ __('Safety Critical') }}',
                                    data: rawCrit.map(v => v > 0 ? v : null),
                                    backgroundColor: deptGradCrit,
                                    borderRadius: {
                                        topLeft: 7,
                                        topRight: 7,
                                        bottomLeft: 3,
                                        bottomRight: 3
                                    },
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
                            animation: {
                                duration: 900,
                                easing: 'easeOutQuart'
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        pointStyle: 'rectRounded',
                                        padding: 18,
                                        color: textCol,
                                        font: {
                                            size: 12,
                                            weight: '600'
                                        }
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
                                    ticks: {
                                        color: textCol,
                                        font: {
                                            size: 11,
                                            weight: '500'
                                        },
                                        maxRotation: 30,
                                        autoSkip: false
                                    },
                                    grid: {
                                        color: 'transparent'
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
                                        },
                                        stepSize: 1
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


    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const pillButtons = document.querySelectorAll('.opdoc-pill-btn');
            const cols = document.querySelectorAll('.opdoc-col');
            const searchInput = document.getElementById('opdocSearchInput');
            const noResults = document.getElementById('opdocNoResults');

            let activeFilter = 'all';

            function applyFilters() {

                const searchTerm = searchInput.value.trim().toLowerCase();

                let visibleCount = 0;

                cols.forEach(function(col) {

                    const category = (col.dataset.category || '').toLowerCase();
                    const title = (col.dataset.title || '').toLowerCase();
                    const searchData = (col.dataset.search || '').toLowerCase();

                    const matchesCategory =
                        activeFilter === 'all' ||
                        category === activeFilter;

                    const matchesSearch =
                        searchTerm === '' ||
                        title.includes(searchTerm) ||
                        searchData.includes(searchTerm);

                    if (matchesCategory && matchesSearch) {

                        col.style.display = '';

                        visibleCount++;

                    } else {

                        col.style.display = 'none';

                    }

                });

                if (noResults) {

                    noResults.style.display =
                        visibleCount === 0 ? 'block' : 'none';

                }
            }


            pillButtons.forEach(function(btn) {

                btn.addEventListener('click', function() {

                    activeFilter = btn.dataset.filter;

                    pillButtons.forEach(function(b) {

                        b.style.background = '#fff';
                        b.style.color = '#495057';
                        b.style.borderColor = '#e5e7eb';

                    });

                    btn.style.background = '#0d6efd';
                    btn.style.color = '#fff';
                    btn.style.borderColor = '#0d6efd';

                    applyFilters();

                });

            });


            searchInput.addEventListener('input', function() {

                applyFilters();

            });


            applyFilters();

        });
    </script>
@endpush

@section('content')

    {{-- STAT CARDS --}}
    <div class="row g-3 mt-1">

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


    {{-- ROW 2 - DONUT + MONTHLY TREND --}}
    <div class="row g-3 mt-1">

        {{-- Status Donut --}}
        <div class="col-xl-6 col-lg-5">
            <div class="card chart-card h-100">
                <div class="card-header">
                    <h5>{{ __('Status Breakdown') }}</h5>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="chart-legend">
                        <span class="chart-legend-item"><span class="chart-legend-dot"
                                style="background:#EF9F27"></span>{{ __('Open') }}
                            {{ $result['pendingComplaints'] }}</span>
                        <span class="chart-legend-item"><span class="chart-legend-dot"
                                style="background:#28a745"></span>{{ __('Rectified') }}
                            {{ $result['resolvedComplaints'] }}</span>
                        <span class="chart-legend-item"><span class="chart-legend-dot"
                                style="background:#17a2b8"></span>{{ __('Reopened') }}
                            {{ $result['reopenedComplaints'] }}</span>
                        <span class="chart-legend-item"><span class="chart-legend-dot"
                                style="background:#2ca58d"></span>{{ __('Closed') }}
                            {{ $result['closedComplaints'] }}</span>
                        <span class="chart-legend-item"><span class="chart-legend-dot"
                                style="background:#dc3545"></span>{{ __('Critical') }}
                            {{ $result['safetyComplaints'] }}</span>
                    </div>
                    <div style="position:relative;flex:1;height:220px;">

                        <canvas id="statusDonutChart" role="img"
                            aria-label="{{ __('Donut chart showing complaint status distribution') }}">
                            {{ __('Status breakdown') }}: Open {{ $result['pendingComplaints'] }},
                            Rectified {{ $result['resolvedComplaints'] }},
                            Reopened {{ $result['reopenedComplaints'] }},
                            Closed {{ $result['closedComplaints'] }},
                            Critical {{ $result['safetyComplaints'] }}.
                        </canvas>
                    </div>
                    @php $total = $result['totalComplaints'] ?: 1; @endphp
                    <div class="text-center mt-2" style="font-size:.8rem;color:#6c757d;">
                        {{ __('Resolution rate') }}:
                        <strong>{{ round((($result['resolvedComplaints'] + $result['closedComplaints']) / $total) * 100) }}%</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Monthly Trend --}}
        {{-- <div class="col-xl-8 col-lg-7">
            <div class="card chart-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>{{ __('Monthly Overview') }}</h5>
                    <div class="chart-legend mb-0">
                        <span class="chart-legend-item"><span class="chart-legend-dot"
                                style="background:#378ADD"></span>{{ __('New') }}</span>
                        <span class="chart-legend-item"><span class="chart-legend-dot"
                                style="background:#2ca58d;width:14px;height:3px;border-radius:2px;align-self:center"></span>{{ __('Resolved') }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div style="position:relative;height:260px;">
                        <canvas id="monthlyTrendChart" role="img"
                            aria-label="{{ __('Line chart showing monthly complaint trend') }}">
                            {{ __('Monthly new and resolved complaints.') }}
                        </canvas>
                    </div>
                </div>
            </div>
        </div> --}}
        <div class="col-xl-6">
            <div class="card chart-card h-100">
                <div class="card-header">
                    <h5>{{ __('Complaints by Status') }}</h5>
                </div>
                <div class="card-body">
                    <div style="position:relative;height:220px;">
                        <canvas id="statusBarChart" role="img"
                            aria-label="{{ __('Horizontal bar chart of complaints by status') }}">
                            {{ __('Status counts for each complaint type.') }}
                        </canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /row 2 --}}


    {{-- ROW 3 - STATUS BARS + DEPT CHART --}}
    <div class="row g-3 mt-1">

        {{-- Status Horizontal Bar --}}
        {{-- <div class="col-xl-5">
            <div class="card chart-card h-100">
                <div class="card-header">
                    <h5>{{ __('Complaints by Status') }}</h5>
                </div>
                <div class="card-body">
                    <div style="position:relative;height:220px;">
                        <canvas id="statusBarChart" role="img"
                            aria-label="{{ __('Horizontal bar chart of complaints by status') }}">
                            {{ __('Status counts for each complaint type.') }}
                        </canvas>
                    </div>
                </div>
            </div>
        </div> --}}

        {{-- Department Chart --}}
        <div class="col-xl-7">
            <div class="card chart-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>{{ __('Complaints by Department') }}</h5>
                    <div class="chart-legend mb-0">
                        <span class="chart-legend-item"><span class="chart-legend-dot"
                                style="background:rgba(55,138,221,.78)"></span>{{ __('Total') }}</span>
                        <span class="chart-legend-item"><span class="chart-legend-dot"
                                style="background:rgba(220,53,69,.82)"></span>{{ __('Safety Critical') }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @isset($result['byDepartment'])
                        <div style="position:relative;height:220px;">
                            <canvas id="deptChart" role="img"
                                aria-label="{{ __('Grouped bar chart of complaints per department') }}">
                                {{ __('Department-wise complaint breakdown.') }}
                            </canvas>
                        </div>
                    @else
                        {{-- Fallback: progress bars (no extra query needed) --}}
                        @php
                            $deptData = $result['recentComplaints']
                                ->groupBy(fn($c) => $c->department->name ?? __('Unknown'))
                                ->map->count()
                                ->sortDesc()
                                ->take(6);
                            $deptMax = $deptData->max() ?: 1;
                        @endphp
                        <div class="mt-2">
                            @foreach ($deptData as $dept => $count)
                                <div class="dept-bar-row">
                                    <div class="dept-bar-label">
                                        <span>{{ $dept }}</span>
                                        <span>{{ $count }}</span>
                                    </div>
                                    <div class="dept-bar-track">
                                        <div class="dept-bar-fill bg-primary"
                                            data-width="{{ round(($count / $deptMax) * 100) }}" style="width:0%"></div>
                                    </div>
                                </div>
                            @endforeach
                            <p class="text-muted mt-3" style="font-size:.75rem;">
                                {{ __('Based on last 10 complaints. Add $result[\'byDepartment\'] query for full chart.') }}
                            </p>
                        </div>
                    @endisset
                </div>
            </div>
        </div>

    </div>{{-- /row 3 --}}


    {{--  ROW 4 - DAILY TREND (optional) --}}
    @isset($result['dailyTrend'])
        <div class="row g-3 mt-1">
            <div class="col-12">
                <div class="card chart-card">
                    <div class="card-header">
                        <h5>{{ __('Daily Complaints - Last 14 Days') }}</h5>
                    </div>
                    <div class="card-body">
                        <div style="position:relative;height:160px;">
                            <canvas id="dailyChart" role="img"
                                aria-label="{{ __('Area chart of daily complaints over 14 days') }}">
                                {{ __('Daily complaint volume.') }}
                            </canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endisset


    {{-- ROW 5 - RECENT COMPLAINTS TABLE --}}
    <div class="row g-3 mt-1">
        <div class="col-12">
            <div class="card chart-card">
                <div class="card-header">
                    <h5>{{ __('Recent Complaints') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="dt-responsive table-responsive">
                        @if (isset($result['recentComplaints']) && $result['recentComplaints']->count() > 0)
                            <table class="table table-hover advance-datatable mb-0">
                        @else
                            <table class="table table-hover mb-0">
                        @endif
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
                                @forelse($result['recentComplaints'] as $i => $complaint)
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
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            {{ __('No complaints found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>{{-- /recent table --}}




    @if (Auth::user()->type == 'owner')
        <div class="row g-3 mt-1">
            <div class="col-12">

                <!-- Tabs -->
                <ul class="nav nav-tabs mb-3" id="employeeTabs" role="tablist">

                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="birthday-tab" data-bs-toggle="tab"
                            data-bs-target="#birthday" type="button" role="tab">
                            Today Birthday Employees
                            <span class="badge bg-primary ms-1">
                                {{ $birthdays_employees->count() }}
                            </span>
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="appointment-tab" data-bs-toggle="tab" data-bs-target="#appointment"
                            type="button" role="tab">
                            Today Appointment Employees
                            <span class="badge bg-success ms-1">
                                {{ $appointments_employees->count() }}
                            </span>
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="retirement-tab" data-bs-toggle="tab" data-bs-target="#retirement"
                            type="button" role="tab">
                            Today Retirement Employees
                            <span class="badge bg-danger ms-1">
                                {{ $retirements_employees->count() }}
                            </span>
                        </button>
                    </li>

                </ul>

                <!-- Tab Contents -->
                <div class="tab-content" id="employeeTabsContent">

                    <!-- Birthday Employees -->
                    <div class="tab-pane fade show active" id="birthday" role="tabpanel">
                        <div class="card stat-card">
                            <div class="card-header">
                                <h5>Today Birthday Employees</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    @if ($birthdays_employees->count() > 0)
                                        <table class="table table-hover advance-datatable mb-0">
                                        @else
                                            <table class="table table-hover mb-0">
                                    @endif
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('HRMS ID') }}</th>
                                            <th>{{ __('Department') }}</th>
                                            <th>{{ __('Designation') }}</th>
                                            <th>{{ __('Date of Birth') }}</th>
                                            <th>{{ __('Date of Appointment') }}</th>
                                            <th>{{ __('Date of Retirement') }}</th>
                                            <th>{{ __('Present Place of Posting') }}</th>
                                            <th>{{ __('Date of Posting at Present Station') }}</th>
                                            <th>{{ __('Mobile') }}</th>
                                            <th>{{ __('Email') }}</th>
                                            <th>{{ __('Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($birthdays_employees as $index => $employee)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        {{ $employee->name }}
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ $employee->usmid }}
                                                </td>
                                                <td>{{ $employee->department->name ?? '-' }}</td>
                                                <td>{{ $employee->designation->name ?? '-' }}</td>
                                                <td>
                                                    {{ $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format('d-m-Y') : '-' }}
                                                </td>
                                                <td>
                                                    {{ $employee->date_of_appointment ? \Carbon\Carbon::parse($employee->date_of_appointment)->format('d-m-Y') : '-' }}
                                                </td>
                                                <td>
                                                    {{ $employee->date_of_retirement ? \Carbon\Carbon::parse($employee->date_of_retirement)->format('d-m-Y') : '-' }}
                                                </td>

                                                <td>
                                                    @forelse ($employee->stations as $station)
                                                        {{ $station->name }} ({{ $station->code }}),

                                                    @empty
                                                        <span class="text-muted">-</span>
                                                    @endforelse
                                                </td>
                                                <td>
                                                    {{ $employee->date_of_posting_present_station ? \Carbon\Carbon::parse($employee->date_of_posting_present_station)->format('d-m-Y') : '-' }}
                                                </td>
                                                <td>{{ $employee->mobile ?? '-' }}</td>
                                                <td>{{ $employee->email ?? '-' }}</td>
                                                <td>
                                                    @if ($employee->status == 1)
                                                        <span class="badge bg-success-subtle text-success">
                                                            {{ __('Active') }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger">
                                                            {{ __('Inactive') }}
                                                        </span>
                                                    @endif
                                                </td>

                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">
                                                    No birthday employees found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Appointment Anniversary -->
                    <div class="tab-pane fade" id="appointment" role="tabpanel">
                        <div class="card stat-card">
                            <div class="card-header">
                                <h5>Today Appointment Employees</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    @if ($appointments_employees->count() > 0)
                                        <table class="table table-hover advance-datatable mb-0">
                                        @else
                                            <table class="table table-hover mb-0">
                                    @endif
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('HRMS ID') }}</th>
                                            <th>{{ __('Department') }}</th>
                                            <th>{{ __('Designation') }}</th>
                                            <th>{{ __('Date of Birth') }}</th>
                                            <th>{{ __('Date of Appointment') }}</th>
                                            <th>{{ __('Date of Retirement') }}</th>
                                            <th>{{ __('Present Place of Posting') }}</th>
                                            <th>{{ __('Date of Posting at Present Station') }}</th>
                                            <th>{{ __('Mobile') }}</th>
                                            <th>{{ __('Email') }}</th>
                                            <th>{{ __('Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($appointments_employees as $index => $employee)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        {{ $employee->name }}
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ $employee->usmid }}
                                                </td>
                                                <td>{{ $employee->department->name ?? '-' }}</td>
                                                <td>{{ $employee->designation->name ?? '-' }}</td>
                                                <td>
                                                    {{ $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format('d-m-Y') : '-' }}
                                                </td>
                                                <td>
                                                    {{ $employee->date_of_appointment ? \Carbon\Carbon::parse($employee->date_of_appointment)->format('d-m-Y') : '-' }}
                                                </td>
                                                <td>
                                                    {{ $employee->date_of_retirement ? \Carbon\Carbon::parse($employee->date_of_retirement)->format('d-m-Y') : '-' }}
                                                </td>

                                                <td>
                                                    @forelse ($employee->stations as $station)
                                                        {{ $station->name }} ({{ $station->code }}),

                                                    @empty
                                                        <span class="text-muted">-</span>
                                                    @endforelse
                                                </td>
                                                <td>
                                                    {{ $employee->date_of_posting_present_station ? \Carbon\Carbon::parse($employee->date_of_posting_present_station)->format('d-m-Y') : '-' }}
                                                </td>
                                                <td>{{ $employee->mobile ?? '-' }}</td>
                                                <td>{{ $employee->email ?? '-' }}</td>
                                                <td>
                                                    @if ($employee->status == 1)
                                                        <span class="badge bg-success-subtle text-success">
                                                            {{ __('Active') }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger">
                                                            {{ __('Inactive') }}
                                                        </span>
                                                    @endif
                                                </td>

                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">
                                                    No appointment employees found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Retirement Employees -->
                    <div class="tab-pane fade" id="retirement" role="tabpanel">
                        <div class="card stat-card">
                            <div class="card-header">
                                <h5>Today Retirement Employees</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    @if ($retirements_employees->count() > 0)
                                        <table class="table table-hover advance-datatable mb-0">
                                        @else
                                            <table class="table table-hover mb-0">
                                    @endif
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('HRMS ID') }}</th>
                                            <th>{{ __('Department') }}</th>
                                            <th>{{ __('Designation') }}</th>
                                            <th>{{ __('Date of Birth') }}</th>
                                            <th>{{ __('Date of Appointment') }}</th>
                                            <th>{{ __('Date of Retirement') }}</th>
                                            <th>{{ __('Present Place of Posting') }}</th>
                                            <th>{{ __('Date of Posting at Present Station') }}</th>
                                            <th>{{ __('Mobile') }}</th>
                                            <th>{{ __('Email') }}</th>
                                            <th>{{ __('Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($retirements_employees as $index => $employee)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        {{ $employee->name }}
                                                    </div>
                                                </td>
                                                <td>
                                                    {{ $employee->usmid }}
                                                </td>
                                                <td>{{ $employee->department->name ?? '-' }}</td>
                                                <td>{{ $employee->designation->name ?? '-' }}</td>
                                                <td>
                                                    {{ $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format('d-m-Y') : '-' }}
                                                </td>
                                                <td>
                                                    {{ $employee->date_of_appointment ? \Carbon\Carbon::parse($employee->date_of_appointment)->format('d-m-Y') : '-' }}
                                                </td>
                                                <td>
                                                    {{ $employee->date_of_retirement ? \Carbon\Carbon::parse($employee->date_of_retirement)->format('d-m-Y') : '-' }}
                                                </td>

                                                <td>
                                                    @forelse ($employee->stations as $station)
                                                        {{ $station->name }} ({{ $station->code }}),

                                                    @empty
                                                        <span class="text-muted">-</span>
                                                    @endforelse
                                                </td>
                                                <td>
                                                    {{ $employee->date_of_posting_present_station ? \Carbon\Carbon::parse($employee->date_of_posting_present_station)->format('d-m-Y') : '-' }}
                                                </td>
                                                <td>{{ $employee->mobile ?? '-' }}</td>
                                                <td>{{ $employee->email ?? '-' }}</td>
                                                <td>
                                                    @if ($employee->status == 1)
                                                        <span class="badge bg-success-subtle text-success">
                                                            {{ __('Active') }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger">
                                                            {{ __('Inactive') }}
                                                        </span>
                                                    @endif
                                                </td>

                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">
                                                    No retirement employees found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    @endif


    {{-- OPERATING & SAFETY DOCUMENTS SECTION --}}
    <div class="row g-3 mt-3" style="padding-bottom: 50px;">
        <div class="col-12">
            <div style="background:#fff; border-radius:16px; padding:24px; border:1px solid rgba(0,0,0,.06);">

                <div style="text-align:center; margin-bottom:18px;">
                    <h4 style="font-weight:700; margin:0 0 4px 0;">Operating &amp; Safety Documents</h4>
                    <p style="color:#6c757d; font-size:.9rem; margin:0;">One place for all important rules, manuals,
                        letters &amp; alerts</p>
                </div>

                <div
                    style="display:flex; align-items:center; gap:10px; background:#fff; border:1px solid #e5e7eb; border-radius:30px; padding:10px 18px; margin-bottom:16px;">
                    <i class="ti ti-search" style="color:#9ca3af; font-size:18px;"></i>
                    <input type="text" id="opdocSearchInput"
                        placeholder="Search documents by title, number, subject..."
                        style="flex:1; border:none; outline:none; background:transparent; font-size:.9rem; width:100%;">
                    <i class="ti ti-adjustments-horizontal" style="color:#9ca3af; font-size:18px;"></i>
                </div>

                <div id="opdocFilters" style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:18px;">
                    <button type="button" class="opdoc-pill-btn" data-filter="all"
                        style="cursor:pointer; background:#0d6efd; color:#fff; border:1px solid #0d6efd; border-radius:20px; padding:6px 16px; font-size:.85rem; font-weight:500;">All</button>
                    <button type="button" class="opdoc-pill-btn" data-filter="swr"
                        style="cursor:pointer; background:#fff; color:#495057; border:1px solid #e5e7eb; border-radius:20px; padding:6px 16px; font-size:.85rem; font-weight:500;">SWR</button>
                    <button type="button" class="opdoc-pill-btn" data-filter="gsr"
                        style="cursor:pointer; background:#fff; color:#495057; border:1px solid #e5e7eb; border-radius:20px; padding:6px 16px; font-size:.85rem; font-weight:500;">G&amp;SR</button>
                    <button type="button" class="opdoc-pill-btn" data-filter="manuals"
                        style="cursor:pointer; background:#fff; color:#495057; border:1px solid #e5e7eb; border-radius:20px; padding:6px 16px; font-size:.85rem; font-weight:500;">Manuals</button>
                    <button type="button" class="opdoc-pill-btn" data-filter="letters"
                        style="cursor:pointer; background:#fff; color:#495057; border:1px solid #e5e7eb; border-radius:20px; padding:6px 16px; font-size:.85rem; font-weight:500;">Letters</button>
                    <button type="button" class="opdoc-pill-btn" data-filter="alerts"
                        style="cursor:pointer; background:#fff; color:#495057; border:1px solid #e5e7eb; border-radius:20px; padding:6px 16px; font-size:.85rem; font-weight:500;">Alerts</button>
                    <button type="button" class="opdoc-pill-btn" data-filter="episodes"
                        style="cursor:pointer; background:#fff; color:#495057; border:1px solid #e5e7eb; border-radius:20px; padding:6px 16px; font-size:.85rem; font-weight:500;">Episodes</button>
                    <button type="button" class="opdoc-pill-btn" data-filter="drives"
                        style="cursor:pointer; background:#fff; color:#495057; border:1px solid #e5e7eb; border-radius:20px; padding:6px 16px; font-size:.85rem; font-weight:500;">Drives</button>


                    <button type="button" class="opdoc-pill-btn" data-filter="case-study"
                        style="cursor:pointer; background:#fff; color:#495057; border:1px solid #e5e7eb; border-radius:20px; padding:6px 16px; font-size:.85rem; font-weight:500;">
                        Case Study
                    </button>

                    <button type="button" class="opdoc-pill-btn" data-filter="safety-circular"
                        style="cursor:pointer; background:#fff; color:#495057; border:1px solid #e5e7eb; border-radius:20px; padding:6px 16px; font-size:.85rem; font-weight:500;">
                        Safety Circular
                    </button>

                    <button type="button" class="opdoc-pill-btn" data-filter="operating-safety-circular"
                        style="cursor:pointer; background:#fff; color:#495057; border:1px solid #e5e7eb; border-radius:20px; padding:6px 16px; font-size:.85rem; font-weight:500;">
                        Operating Safety Circular
                    </button>

                    <button type="button" class="opdoc-pill-btn" data-filter="jpo"
                        style="cursor:pointer; background:#fff; color:#495057; border:1px solid #e5e7eb; border-radius:20px; padding:6px 16px; font-size:.85rem; font-weight:500;">
                        JPO
                    </button>

                    <button type="button" class="opdoc-pill-btn" data-filter="officer-inspection-notes"
                        style="cursor:pointer; background:#fff; color:#495057; border:1px solid #e5e7eb; border-radius:20px; padding:6px 16px; font-size:.85rem; font-weight:500;">
                        Officer Inspection Notes
                    </button>

                    <button type="button" class="opdoc-pill-btn" data-filter="traffic-inspector-inspection-notes"
                        style="cursor:pointer; background:#fff; color:#495057; border:1px solid #e5e7eb; border-radius:20px; padding:6px 16px; font-size:.85rem; font-weight:500;">
                        Traffic Inspector Inspection Notes
                    </button>

                </div>

                <div id="opdocGrid" class="row g-3">

                    @php
                        $docs = [
                            [
                                'icon' => 'ti-book',
                                'color' => '#0d6efd',
                                'title' => 'Station Working Rule',
                                'lines' => [
                                    ($result['stationWorkingRuleStations'] ?? 0) . ' Stations',
                                    ($result['stationWorkingRuleSlips'] ?? 0) . ' Correction Slips',
                                ],
                                'cat' => 'swr',
                                'route' => 'operating-safety-documents.station-working-rule.stations',
                            ],
                            [
                                'icon' => 'ti-scale',
                                'color' => '#28a745',
                                'title' => 'G&SR',
                                'lines' => [
                                    ($result['gsrOriginals'] ?? 0) . ' Main PDF',
                                    ($result['gsrSlips'] ?? 0) . ' Correction Slips',
                                ],
                                'cat' => 'gsr',
                                'route' => 'operating-safety-documents.gsr.originals',
                            ],
                            [
                                'icon' => 'ti-book',
                                'color' => '#fa6616',
                                'title' => 'Operating Manual',
                                'lines' => [
                                    ($result['operatingManualOriginals'] ?? 0) . ' Main PDF',
                                    ($result['operatingManualSlips'] ?? 0) . ' Correction Slips',
                                ],
                                'cat' => 'manuals',
                                'route' => 'operating-safety-documents.operating-manual.originals',
                            ],
                            [
                                'icon' => 'ti-alert-triangle',
                                'color' => '#dc3545',
                                'title' => 'Accident Manual',
                                'lines' => [
                                    ($result['accidentManualOriginals'] ?? 0) . ' Main PDF',
                                    ($result['accidentManualSlips'] ?? 0) . ' Correction Slips',
                                ],
                                'cat' => 'manuals',
                                'route' => 'operating-safety-documents.accident-manual.originals',
                            ],
                            [
                                'icon' => 'ti-file-text',
                                'color' => '#7c3aed',
                                'title' => 'Miscellaneous Letters',
                                'lines' => [$result['totalMiscellaneousLetters'] . ' Letters'],
                                'cat' => 'letters',
                                'route' => 'operating-safety-documents.miscellaneous-letters.show',
                            ],
                            [
                                'icon' => 'ti-alert-circle',
                                'color' => '#f4b400',
                                'title' => 'Operating Safety Alert',
                                'lines' => [($result['operatingSafetyAlerts'] ?? 0) . ' Alerts'],
                                'cat' => 'alerts',
                                'route' => 'operating-safety-documents.alert.show',
                            ],
                            [
                                'icon' => 'ti-target',
                                'color' => '#0d9488',
                                'title' => 'Operating Safety Episode',
                                'lines' => [($result['operatingSafetyEpisodes'] ?? 0) . ' Episodes'],
                                'cat' => 'episodes',
                                'route' => 'operating-safety-documents.episode.show',
                            ],
                            [
                                'icon' => 'ti-shield-check',
                                'color' => '#0891b2',
                                'title' => 'Operating Safety Drive',
                                'lines' => [($result['operatingSafetyDrives'] ?? 0) . ' Drives'],
                                'cat' => 'drives',
                                'route' => 'operating-safety-documents.drive.show',
                            ],
                            [
                                'icon' => 'ti-file-text',
                                'color' => '#2563eb',
                                'title' => 'Case Study',
                                'lines' => [($result['totalCaseStudy'] ?? 0) . ' Case Studies'],
                                'cat' => 'case-study',
                                'route' => 'operating-safety-documents.case-study.show',
                            ],
                            [
                                'icon' => 'ti-file-certificate',
                                'color' => '#7c3aed',
                                'title' => 'Safety Circular',
                                'lines' => [($result['totalSafetyCircular'] ?? 0) . ' Safety Circulars'],
                                'cat' => 'safety-circular',
                                'route' => 'operating-safety-documents.safety-circular.show',
                            ],
                            [
                                'icon' => 'ti-shield-check',
                                'color' => '#059669',
                                'title' => 'Operating Safety Circular',
                                'lines' => [
                                    ($result['totalOperatingSafetyCircular'] ?? 0) . ' Operating Safety Circulars',
                                ],
                                'cat' => 'operating-safety-circular',
                                'route' => 'operating-safety-documents.operating-safety-circular.show',
                            ],
                            [
                                'icon' => 'ti-file-invoice',
                                'color' => '#ea580c',
                                'title' => 'JPO',
                                'lines' => [($result['totalJpo'] ?? 0) . ' JPO'],
                                'cat' => 'jpo',
                                'route' => 'operating-safety-documents.jpo.show',
                            ],
                            [
                                'icon' => 'ti-clipboard-check',
                                'color' => '#0891b2',
                                'title' => 'Officer Inspection Notes',
                                'lines' => [
                                    ($result['totalOfficerInspectionNotes'] ?? 0) . ' Officer Inspection Notes',
                                ],
                                'cat' => 'officer-inspection-notes',
                                'route' => 'operating-safety-documents.officer-inspection-notes.officers',
                            ],
                            [
                                'icon' => 'ti-clipboard-list',
                                'color' => '#db2777',
                                'title' => 'Traffic Inspector Inspection Notes',
                                'lines' => [
                                    ($result['totalTrafficInspectorInspectionNotes'] ?? 0) .
                                    ' Traffic Inspector Inspection Notes',
                                ],
                                'cat' => 'traffic-inspector-inspection-notes',
                                'route' => 'operating-safety-documents.traffic-inspector-inspection-notes.inspectors',
                            ],
                        ];
                    @endphp

                    @foreach ($docs as $doc)
                        <div class="col-12 col-sm-6 col-lg-3 opdoc-col" data-category="{{ $doc['cat'] }}"
                            data-title="{{ strtolower($doc['title']) }}">
                            <a href="{{ isset($doc['route']) ? route($doc['route']) : '#' }}"
                                style="text-decoration:none; display:flex; flex-direction:column; gap:10px; background:#fff; border:1px solid rgba(0,0,0,.08); border-radius:14px; padding:18px; position:relative; height:100%; min-height:150px; box-shadow:0 1px 3px rgba(0,0,0,.04);">

                                <span
                                    style="display:flex; align-items:center; justify-content:center; width:48px; height:48px; min-width:48px; max-width:48px; border-radius:12px; background:{{ $doc['color'] }}; color:#fff; font-size:22px;">
                                    <i class="ti {{ $doc['icon'] }}"></i>
                                </span>

                                <span style="display:block;">
                                    <span
                                        style="display:block; font-weight:700; margin:0 0 6px 0; font-size:.95rem; color:#212529;">{{ $doc['title'] }}</span>
                                    @foreach ($doc['lines'] as $line)
                                        <span
                                            style="display:block; font-size:.82rem; color:#6c757d; line-height:1.4;">{{ $line }}</span>
                                    @endforeach
                                </span>

                                <span
                                    style="margin-top:auto; font-size:.82rem; font-weight:600; color:#0d6efd; display:inline-flex; align-items:center; gap:4px;">
                                    View <i class="ti ti-arrow-right"></i>
                                </span>
                            </a>
                        </div>
                    @endforeach

                </div>

                <p id="opdocNoResults" class="text-center text-muted mt-3" style="display:none; font-size:.85rem;">
                    No documents found.
                </p>
            </div>
        </div>
    </div>



@endsection
