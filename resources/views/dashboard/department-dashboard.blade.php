@extends('layouts.app')
@section('page-title')
    {{ __('Deparment Dashboard') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item" aria-current="page">{{ __('Home > Deparment Dashboard') }}</li>
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

@push('script-page')
    @if (\Auth::user()->type == 'owner')
        <script>
            var options = {
                chart: {
                    type: 'area',
                    height: 450,
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#2ca58d', '#e74c3c'], // Income (green), Expense (red)
                dataLabels: {
                    enabled: false
                },
                legend: {
                    show: true,
                    position: 'top'
                },
                markers: {
                    size: 1,
                    colors: ['#fff'],
                    strokeColors: ['#2ca58d', '#e74c3c'],
                    strokeWidth: 1,
                    shape: 'circle',
                    hover: {
                        size: 4
                    }
                },
                stroke: {
                    width: 2,
                    curve: 'smooth'
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        type: 'vertical',
                        inverseColors: false,
                        opacityFrom: 0.5,
                        opacityTo: 0
                    }
                },
                grid: {
                    show: false
                },
                series: [{
                        name: "Income",
                        data: {!! json_encode($result['incomeExpenseByMonth']['income']) !!}
                    },
                    {
                        name: "Expense",
                        data: {!! json_encode($result['incomeExpenseByMonth']['expense']) !!}
                    }
                ],
                xaxis: {
                    categories: {!! json_encode(@$result['incomeExpenseByMonth']['label']) !!},
                    tooltip: {
                        enabled: false
                    },
                    labels: {
                        hideOverlappingLabels: true
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector('#incomeExpenseByMonth'), options);
            chart.render();
        </script>
    @elseif (\Auth::user()->type == 'employee')
        <script>
            var options = {
                chart: {
                    type: 'bar',
                    height: 450,
                    toolbar: {
                        show: false
                    }
                },
                colors: ['#4CAF50', '#F44336'], // Completed (green), Pending (red)
                dataLabels: {
                    enabled: false
                },
                legend: {
                    show: true,
                    position: 'top'
                },
                markers: {
                    size: 1,
                    colors: ['#fff'],
                    strokeColors: ['#4CAF50', '#F44336'],
                    strokeWidth: 1,
                    shape: 'circle',
                    hover: {
                        size: 4
                    }
                },
                stroke: {
                    width: 2,
                    curve: 'smooth'
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        type: 'vertical',
                        inverseColors: false,
                        opacityFrom: 0.5,
                        opacityTo: 0
                    }
                },
                grid: {
                    show: false
                },
                series: [{
                        name: "Completed",
                        data: {!! json_encode($result['totalOrderStatus']['completed']) !!}
                    },
                    {
                        name: "Pending",
                        data: {!! json_encode($result['totalOrderStatus']['pending']) !!}
                    }
                ],
                xaxis: {
                    categories: {!! json_encode(@$result['totalOrderStatus']['label']) !!},
                    tooltip: {
                        enabled: false
                    },
                    labels: {
                        hideOverlappingLabels: true
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    min: 0,
                    tickAmount: 5, // max 5 ticks
                    forceNiceScale: false,
                    labels: {
                        formatter: function(val) {
                            return Number.isInteger(val) ? val : ''; // only integers
                        }
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector('#totalOrderStatus'), options);
            chart.render();
        </script>
    @endif
@endpush
@section('content')
    <div class="row">

        <div class="row g-3 mt-1">
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

        {{-- -- Complaints By Department ------------------------------ --}}
        <div class="col-12 mt-4">
            <h5 class="fw-semibold mb-3">{{ __('Complaints By Department') }}</h5>

            <div class="row g-3">
                @php
                    $deptColors = [
                        'bg-light-yellow' => ['bg' => '#fffde7', 'icon' => 'ti ti-tools', 'badge' => '#f9a825'],
                        'bg-light-green' => ['bg' => '#e8f5e9', 'icon' => 'ti ti-bolt', 'badge' => '#2e7d32'],
                        'bg-light-blue' => ['bg' => '#e3f2fd', 'icon' => 'ti ti-antenna', 'badge' => '#1565c0'],
                        'bg-light-purple' => [
                            'bg' => '#f3e8ff',
                            'icon' => 'ti ti-shopping-cart',
                            'badge' => '#6a0deb',
                        ],
                        'bg-light-teal' => ['bg' => '#e0f2f1', 'icon' => 'ti ti-settings', 'badge' => '#00695c'],
                        'bg-light-orange' => ['bg' => '#fff7e6', 'icon' => 'ti ti-shield', 'badge' => '#fa6616'],
                    ];
                    $colorKeys = array_keys($deptColors);
                @endphp

                @forelse($result['byDepartment'] as $index => $dept)
                    @php
                        $colorKey = $colorKeys[$index % count($colorKeys)];
                        $colorInfo = $deptColors[$colorKey];
                    @endphp

                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 stat-card" style="border-left: 4px solid {{ $colorInfo['badge'] }} !important;">
                            <div class="card-body p-4">

                                {{-- Header --}}
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <p class="mb-0 fw-bold text-muted small text-uppercase" style="letter-spacing: .5px;">
                                            {{ $dept->department_name }}
                                        </p>
                                        <h3 class="fw-bold mb-0 text-dark" style="font-size: 1.6rem;">{{ number_format($dept->total) }}</h3>
                                    </div>
                                    <div class="avtar"
                                        style="background-color: {{ $colorInfo['badge'] }}20;
                                        color: {{ $colorInfo['badge'] }};
                                        border-radius: 50%; width:38px; height:38px;
                                        display:flex; align-items:center; justify-content:center;">
                                        <i class="{{ $colorInfo['icon'] }} f-20"></i>
                                    </div>
                                </div>

                                {{-- Status row --}}
                                <div class="row g-2">
                                    <div class="col-3">
                                        <a href="{{ route('department.dashboard.data.view', ['department_id' => $dept->id, 'status' => 'pending']) }}" class="text-decoration-none">
                                            <div class="metric-badge metric-pending" style="padding: 10px 4px !important;">
                                                <p style="font-size: 9px !important; margin-bottom: 2px !important;">Pending</p>
                                                <h5 style="font-size: 14px !important;">{{ $dept->pending }}</h5>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-3">
                                        <a href="{{ route('department.dashboard.data.view', ['department_id' => $dept->id, 'status' => 'rectified']) }}" class="text-decoration-none">
                                            <div class="metric-badge metric-rectified" style="padding: 10px 4px !important;">
                                                <p style="font-size: 9px !important; margin-bottom: 2px !important;">Rectified</p>
                                                <h5 style="font-size: 14px !important;">{{ $dept->rectified }}</h5>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-3">
                                        <a href="{{ route('department.dashboard.data.view', ['department_id' => $dept->id, 'status' => 'reopened']) }}" class="text-decoration-none">
                                            <div class="metric-badge metric-reopened" style="padding: 10px 4px !important;">
                                                <p style="font-size: 9px !important; margin-bottom: 2px !important;">Re-raised</p>
                                                <h5 style="font-size: 14px !important;">{{ $dept->reopened }}</h5>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-3">
                                        <a href="{{ route('department.dashboard.data.view', ['department_id' => $dept->id, 'status' => 'closed']) }}" class="text-decoration-none">
                                            <div class="metric-badge metric-closed" style="padding: 10px 4px !important;">
                                                <p style="font-size: 9px !important; margin-bottom: 2px !important;">Closed</p>
                                                <h5 style="font-size: 14px !important;">{{ $dept->closed }}</h5>
                                            </div>
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-muted">No department data found.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
@endsection


