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
        @if (\Auth::user()->type == 'owner')
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
                                    <p class="mb-1{{ __('Pending Complaints') }}">{{ __('Pending Complaints') }}</p>
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













            {{--                                    
     ROW 5 - RECENT COMPLAINTS TABLE
                                    --}}
            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="card chart-card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">{{ __('Recent Complaints') }}</h5>
                            @if(\Auth::check() && in_array(strtolower(\Auth::user()->type ?? ''), ['owner', 'super admin']) && Route::has('report.complaints.index'))
                                <a href="{{ route('report.complaints.index') }}" class="btn btn-sm btn-primary">
                                    <i class="ti ti-file-analytics me-1"></i>{{ __('Full Complaints Records Report') }}
                                </a>
                            @endif
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
                                                        <span
                                                            class="badge bg-warning text-dark">{{ __('Open') }}</span>
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
        @elseif (\Auth::user()->type == 'employee')

        @elseif (Auth::user()->type = 'customer')
 <div class="row g-3 mt-1">

                <div class="col-lg-3 col-md-6">
                    <div class="card">
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
                    <div class="card">
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
                    <div class="card">
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
                    <div class="card">
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
                    <div class="card">
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
                    <div class="card">
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
                    <div class="card">
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
                    <div class="card">
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
                    <div class="card">
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
                    <div class="card">
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













            {{--                                    
     ROW 5 - RECENT COMPLAINTS TABLE
                                    --}}
            <div class="row g-3 mt-1">
                <div class="col-12">
                    <div class="card chart-card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">{{ __('Recent Complaints') }}</h5>
                            @if(\Auth::check() && in_array(strtolower(\Auth::user()->type ?? ''), ['owner', 'super admin']) && Route::has('report.complaints.index'))
                                <a href="{{ route('report.complaints.index') }}" class="btn btn-sm btn-primary">
                                    <i class="ti ti-file-analytics me-1"></i>{{ __('Full Complaints Records Report') }}
                                </a>
                            @endif
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
                                                        <span
                                                            class="badge bg-warning text-dark">{{ __('Open') }}</span>
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
            </div>
        @endif
    </div>
@endsection


