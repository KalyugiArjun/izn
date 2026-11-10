@extends('layouts.app')

@section('page-title')
    {{ __('Expense Report') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item" aria-current="page">{{ __('Expense Report') }}</li>
@endsection

@push('css-page')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/daterangepicker/daterangepicker.css') }}" />
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card table-card">
                <div class="card-header">
                    <div class="row align-items-center g-2 flex-wrap">
                        <div class="col-12 col-md">
                            <h5>{{ __('Expense Report') }}</h5>
                        </div>

                        <div class="col-12 col-md-auto">
                            {{ Form::open(['method' => 'post', 'route' => 'generate.expense.report', 'id' => 'expense-report-form', 'class' => 'expense-date']) }}
                            <div class="d-flex flex-wrap gap-2 align-items-end">
                                <div>
                                    <b>{{ Form::label('category_id', __('Category'), ['class' => 'form-label']) }}</b>
                                    {{ Form::select('category_id', $categories, null, ['class' => 'form-control select2', 'placeholder' => __('Select category')]) }}
                                </div>
                                <div>
                                    <b>{{ Form::label('sub_category_id', __('Sub Category'), ['class' => 'form-label']) }}</b>
                                    {{ Form::select('sub_category_id', $subCategories, null, ['class' => 'form-control select2', 'placeholder' => __('Select sub category')]) }}
                                </div>

                                <div>
                                    <b>{{ Form::label('date_range', __('Date'), ['class' => 'form-label']) }}</b>

                                    {{ Form::text('date_range', request()->input('date_range'), ['class' => 'form-control', 'placeholder' => __('Select Date Range')]) }}
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-secondary">
                                        <i class="ti ti-search align-text-bottom"></i> </button>
                                    <button type="button" class="btn btn-secondary btn-rounded"
                                        onclick="window.location.reload();">
                                        <i class="ti ti-refresh align-text-bottom"></i>
                                    </button>
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
                <div class="card-body pt-0" id="expense_table">
                    @if (isset($expenses) && $expenses->count())
                        @include('report.expense_table')
                    @else
                        <p class="text-center">{{ __('No expense data found.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-page')
    <script type="text/javascript" src="{{ asset('assets/js/daterangepicker/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/daterangepicker/daterangepicker.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();

            $('#date_range').daterangepicker({
                autoApply: true,
                autoUpdateInput: false,
                locale: {
                    format: 'MM/DD/YYYY'
                }
            }, function(start, end) {
                var start_date = start.format('MM/DD/YYYY');
                var end_date = end.format('MM/DD/YYYY');
                $('#date_range').val(start_date + ' - ' + end_date);
            });

            $('#expense-report-form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: '{{ route('generate.expense.report') }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#expense_table').html(response.html);
                        if (response.total_amount) {
                            $('.expense_total').html(response.total_amount);
                        }
                        datatable();
                    },
                    error: function(xhr) {
                        let errorMsg = 'An error occurred while fetching the report.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        alert(errorMsg);
                    }
                });
            });
        });
    </script>
@endpush

