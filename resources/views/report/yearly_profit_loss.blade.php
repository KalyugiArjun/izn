@extends('layouts.app')

@section('page-title')
    {{ __('Profit-Loss Report') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Profit-Loss Report') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card table-card">
                <div class="card-header">
                    <div class="row align-items-center g-2 flex-wrap">
                        <div class="col-12 col-md">
                            <h5>{{ __('Profit Loss Report') }}</h5>
                        </div>
                        <div class="col-12 col-md-auto">
                            {{ Form::open(['method' => 'post', 'route' => 'generate.profit.loss.report', 'id' => 'profit_loss_report_form']) }}
                            <div class="d-flex flex-wrap gap-2 align-items-end">
                                <div>
                                    @php $currentYear = now()->year; @endphp
                                    {{ Form::label('year', __('Year'), ['class' => 'form-label']) }}
                                    {{ Form::selectYear('year', $currentYear - 5, $currentYear + 15, $currentYear, ['class' => 'form-control select2', 'id' => 'year']) }}
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-secondary chart_button">
                                        <i class="ti ti-search align-text-bottom"></i>
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-rounded" id="reset_filter">
                                        <i class="ti ti-refresh align-text-bottom"></i>
                                    </button>
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0" id="profit_loss_report">
                    <p class="text-center">{{ __('No expense data found.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12" id="chart_container" style="display: none;">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <h5 class="mb-1">{{ __('Profit Report') }}</h5>
                            <p class="text-muted mb-2">{{ __('Income vs Expense vs Profit/Loss') }}</p>
                        </div>
                    </div>
                    <div id="incomeExpenseProfitChart"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
    <script type="text/javascript">
        $(document).ready(function() {
            let chart = null;
            const currentYear = new Date().getFullYear();

            // Load initial data on page load
            report();

            // Submit form via AJAX
            $('#profit_loss_report_form').on('submit', function(e) {
                e.preventDefault();
                report();
            });

            // Reset filter
            $('#reset_filter').on('click', function(e) {
                e.preventDefault();
                $('#year').val(currentYear).trigger('change');
                report();
            });

            function report() {
                $.ajax({
                    url: '{{ route('generate.profit.loss.report') }}',
                    method: 'POST',
                    data: {
                        year: $('#year').val()
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#profit_loss_report').html(response.html);

                        if ($('#profit_loss_table').length) {
                            $('#profit_loss_table').append(
                                '<tfoot>' +
                                '<tr>' +
                                '<th>{{ __('Total') }}</th>' +
                                '<th class="text-success">' + response.total_income + '</th>' +
                                '<th class="text-danger">' + response.total_expense + '</th>' +
                                '<th>' + response.total_profit + '</th>' +
                                '</tr>' +
                                '</tfoot>'
                            );
                        }

                        let report = response.report || [];
                        let months = report.map(item => item.month);
                        let incomes = report.map(item => item.income);
                        let expenses = report.map(item => item.expense);
                        let profits = report.map(item => item.profit);
                        let currencySymbol = response.currency_symbol;

                        $('#chart_container').show();

                        function formatNumber(value) {
                            return Number(value).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                        }

                        var options = {
                            chart: {
                                type: 'bar',
                                height: 450,
                                toolbar: { show: false }
                            },
                            colors: ['#2ca58d', '#e74c3c', '#3498db'],
                            dataLabels: { enabled: false },
                            legend: { show: true, position: 'top' },
                            plotOptions: {
                                bar: {
                                    horizontal: false,
                                    columnWidth: '55%',
                                    endingShape: 'rounded'
                                }
                            },
                            stroke: {
                                show: true,
                                width: 2,
                                colors: ['transparent']
                            },
                            series: [
                                { name: 'Income', data: incomes },
                                { name: 'Expense', data: expenses },
                                { name: 'Profit/Loss', data: profits }
                            ],
                            xaxis: {
                                categories: months,
                                labels: { hideOverlappingLabels: true },
                                axisBorder: { show: false },
                                axisTicks: { show: false }
                            },
                            yaxis: {
                                title: { text: 'Amount' }
                            },
                            fill: { opacity: 1 },
                            tooltip: {
                                y: {
                                    formatter: function(val) {
                                        return currencySymbol + formatNumber(val);
                                    }
                                }
                            }
                        };

                        if (chart) chart.destroy();

                        chart = new ApexCharts(document.querySelector('#incomeExpenseProfitChart'), options);
                        chart.render();
                    },
                    error: function(xhr) {
                        let errorMsg = xhr.responseJSON?.message || 'An error occurred while fetching the report.';
                        alert(errorMsg);
                        $('#chart_container').hide();
                    }
                });
            }
        });
    </script>
@endpush

