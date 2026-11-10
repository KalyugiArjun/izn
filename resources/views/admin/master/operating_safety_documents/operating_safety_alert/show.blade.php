@extends('layouts.app')

@section('page-title')
    {{ __('Operating Safety Alert') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item" aria-current="page">
        {{ __('Operating Safety Alert') }}
    </li>
@endsection

@section('content')
    <style>
        .osa-wrapper {
            max-width: 900px;
            margin: 0 auto;
        }

        .osa-header {
            background: rgb(15 44 84 / 98%);
            border-radius: 18px;
            padding: 22px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(15, 44, 84, .18);
        }

        .osa-header:after {
            content: '';
            position: absolute;
            width: 190px;
            height: 190px;
            right: -75px;
            top: -90px;
            border: 25px solid rgba(255, 255, 255, .06);
            border-radius: 50%;
        }

        .osa-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }

        .osa-back-btn,
        .osa-home-btn {
            width: 40px;
            height: 40px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            background: rgba(255, 255, 255, .12);
            text-decoration: none !important;
            transition: .2s;
            flex-shrink: 0;
        }

        .osa-back-btn:hover,
        .osa-home-btn:hover {
            color: #fff;
            background: rgba(255, 255, 255, .22);
            transform: translateY(-1px);
        }

        .osa-title {
            text-align: center;
        }

        .osa-title h4 {
            margin: 0;
            font-weight: 800;
            letter-spacing: .3px;
            color: white !important;
        }

        .osa-title span {
            font-size: 12px;
            opacity: .78;
        }

        .osa-stats {
            margin-top: 18px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            position: relative;
            z-index: 2;
        }

        .osa-stat-card {
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .10);
            border-radius: 12px;
            padding: 14px 16px;
            text-decoration: none !important;
            transition: all .3s ease;
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .osa-stat-card:hover {
            background: rgba(255, 255, 255, .16);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, .15);
            border-color: rgba(255, 255, 255, .2);
        }

        .osa-stat-card .stat-info .stat-label {
            font-size: 11px;
            font-weight: 600;
            opacity: .8;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .osa-stat-card .stat-info .stat-number {
            font-size: 28px;
            font-weight: 800;
            color: #fff;
            line-height: 1.2;
            margin-top: 2px;
        }

        .osa-stat-card .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, .10);
            flex-shrink: 0;
            font-size: 22px;
            color: #fff;
        }

        .osa-stat-card.blue .stat-icon {
            background: rgba(59, 130, 246, .25);
        }

        .osa-stat-card.green .stat-icon {
            background: rgba(121, 230, 5, .15);
        }

        .osa-stat-card.gold .stat-icon {
            background: rgba(251, 191, 36, .2);
        }

        @media(max-width: 768px) {
            .osa-stats {
                grid-template-columns: repeat(3, 1fr);
                gap: 8px;
            }

            .osa-stat-card {
                padding: 12px 14px;
                flex-direction: column;
                text-align: center;
                gap: 6px;
            }

            .osa-stat-card .stat-info .stat-number {
                font-size: 22px;
            }

            .osa-stat-card .stat-icon {
                width: 36px;
                height: 36px;
                font-size: 18px;
            }
        }

        @media(max-width: 576px) {
            .osa-header {
                border-radius: 14px;
                padding: 17px;
            }

            .osa-title h4 {
                font-size: 17px;
            }

            .osa-title span {
                font-size: 11px;
            }

            .osa-header-top {
                gap: 10px;
            }

            .osa-back-btn,
            .osa-home-btn {
                width: 38px;
                height: 38px;
                min-width: 38px;
            }

            .osa-stats {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .osa-stat-card {
                flex-direction: row;
                text-align: left;
                padding: 12px 16px;
            }

            .osa-stat-card .stat-info .stat-number {
                font-size: 24px;
            }
        }
    </style>

    <div class="row justify-content-center mt-2">
        <div class="col-12 col-xl-8">

            <div class="osa-wrapper">

                <div class="osa-header">

                    <div class="osa-header-top">

                        <a href="{{ route('dashboard') }}" class="osa-back-btn" title="Back">
                            <i class="ti ti-arrow-left"></i>
                        </a>

                        <div class="osa-title">
                            <h4>Operating Safety Alert</h4>
                            <span>Safety Alerts &amp; Notifications</span>
                        </div>

                        <a href="{{ route('dashboard') }}" class="osa-home-btn" title="Home">
                            <i class="ti ti-home"></i>
                        </a>

                    </div>

                    <div class="osa-stats">

                        <a href="{{ route('operating-safety-documents.alert.list', 'all') }}" class="osa-stat-card blue">
                            <div class="stat-info">
                                <div class="stat-label">Total Alerts</div>
                                <div class="stat-number">{{ $stats['total'] }}</div>
                            </div>
                            <div class="stat-icon">
                                <i class="ti ti-alert-circle"></i>
                            </div>
                        </a>

                        <a href="{{ route('operating-safety-documents.alert.list', 'year') }}" class="osa-stat-card green">
                            <div class="stat-info">
                                <div class="stat-label">This Year</div>
                                <div class="stat-number">{{ str_pad($stats['thisYear'], 2, '0', STR_PAD_LEFT) }}</div>
                            </div>
                            <div class="stat-icon">
                                <i class="ti ti-calendar-stats"></i>
                            </div>
                        </a>

                        <a href="{{ route('operating-safety-documents.alert.list', 'month') }}" class="osa-stat-card gold">
                            <div class="stat-info">
                                <div class="stat-label">This Month</div>
                                <div class="stat-number">{{ str_pad($stats['thisMonth'], 2, '0', STR_PAD_LEFT) }}</div>
                            </div>
                            <div class="stat-icon">
                                <i class="ti ti-calendar-month"></i>
                            </div>
                        </a>

                    </div>

                </div>

            </div>

        </div>
    </div>
@endsection
