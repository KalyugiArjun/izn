<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Complaints Dashboard Report</title>
    <style>
        /* ── Reset & Base ── */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 11px;
            color: #212529;
            background: #fff;
            line-height: 1.45;
        }

        /* ── Page Settings ── */
        @page {
            margin: 14mm 16mm 14mm 16mm;
            size: A4 portrait;
        }

        /* ── Utility ── */
        .w-100 {
            width: 100%;
        }

        .w-50 {
            width: 50%;
        }

        .w-33 {
            width: 33.33%;
        }

        .w-25 {
            width: 25%;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-muted {
            color: #6c757d;
        }

        .fw-bold {
            font-weight: 700;
        }

        .mb-1 {
            margin-bottom: 4px;
        }

        .mb-2 {
            margin-bottom: 8px;
        }

        .mb-3 {
            margin-bottom: 14px;
        }

        .mt-1 {
            margin-top: 4px;
        }

        .mt-2 {
            margin-top: 8px;
        }

        .mt-3 {
            margin-top: 14px;
        }

        /* ─────────────────────────────────────────
           HEADER BANNER
        ───────────────────────────────────────── */
        .header-banner {
            background: #378ADD;
            border-radius: 8px;
            padding: 14px 18px;
            margin-bottom: 16px;
        }

        .header-banner table {
            width: 100%;
        }

        .header-title {
            font-size: 18px;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.2;
        }

        .header-sub {
            font-size: 9px;
            color: #cfe4ff;
            margin-top: 3px;
        }

        .header-meta {
            font-size: 8.5px;
            color: #cfe4ff;
            text-align: right;
        }

        .header-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.20);
            border-radius: 4px;
            padding: 3px 9px;
            font-size: 8px;
            color: #fff;
            margin-top: 5px;
        }

        /* ─────────────────────────────────────────
           SECTION HEADING
        ───────────────────────────────────────── */
        .section-heading {
            font-size: 11px;
            font-weight: 700;
            color: #1a5fa8;
            border-left: 3px solid #378ADD;
            padding-left: 8px;
            margin-bottom: 8px;
            margin-top: 14px;
            line-height: 1.3;
        }

        .section-heading.first {
            margin-top: 0;
        }

        hr.divider {
            border: none;
            border-top: 1px solid #e9ecef;
            margin: 12px 0;
        }

        /* ─────────────────────────────────────────
           KPI CARDS  (2 rows × 3)
        ───────────────────────────────────────── */
        .kpi-row {
            width: 100%;
            margin-bottom: 6px;
        }

        .kpi-row td {
            width: 33.33%;
            padding: 0 4px;
            vertical-align: top;
        }

        .kpi-row td:first-child {
            padding-left: 0;
        }

        .kpi-row td:last-child {
            padding-right: 0;
        }

        .kpi-card {
            border-radius: 8px;
            padding: 11px 10px;
            text-align: center;
        }

        .kpi-number {
            font-size: 24px;
            font-weight: 700;
            line-height: 1.1;
        }

        .kpi-label {
            font-size: 8.5px;
            color: #6c757d;
            margin-top: 3px;
        }

        .kpi-sub {
            font-size: 7.5px;
            margin-top: 2px;
        }

        /* KPI colour themes */
        .kpi-blue {
            background: #EAF3FF;
        }

        .kpi-blue .kpi-number {
            color: #378ADD;
        }

        .kpi-orange {
            background: #FFF8EC;
        }

        .kpi-orange .kpi-number {
            color: #EF9F27;
        }

        .kpi-red {
            background: #FDECEA;
        }

        .kpi-red .kpi-number {
            color: #dc3545;
        }

        .kpi-green {
            background: #EAF7F0;
        }

        .kpi-green .kpi-number {
            color: #28a745;
        }

        .kpi-yellow {
            background: #FFF3CD;
        }

        .kpi-yellow .kpi-number {
            color: #856404;
        }

        .kpi-cyan {
            background: #E8F4FD;
        }

        .kpi-cyan .kpi-number {
            color: #17a2b8;
        }

        /* ─────────────────────────────────────────
           STATUS BAR CHART  (pure CSS bars)
        ───────────────────────────────────────── */
        .bar-chart-wrap {
            width: 100%;
        }

        .bar-row {
            margin-bottom: 8px;
        }

        .bar-label-row {
            width: 100%;
            font-size: 8.5px;
            color: #495057;
            margin-bottom: 3px;
        }

        .bar-label-row td:first-child {
            font-weight: 600;
        }

        .bar-label-row td:last-child {
            text-align: right;
            color: #6c757d;
        }

        .bar-track {
            width: 100%;
            background: #f0f0f0;
            border-radius: 4px;
            height: 10px;
            position: relative;
        }

        .bar-fill {
            height: 10px;
            border-radius: 4px;
            display: block;
        }

        /* ─────────────────────────────────────────
           STATUS LEGEND PILLS
        ───────────────────────────────────────── */
        .legend-wrap {
            margin-bottom: 10px;
        }

        .legend-wrap table {
            width: 100%;
        }

        .legend-item {
            text-align: center;
        }

        .legend-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 2px;
            vertical-align: middle;
            margin-right: 3px;
        }

        .legend-text {
            font-size: 8px;
            color: #6c757d;
            vertical-align: middle;
        }

        /* ─────────────────────────────────────────
           MONTHLY TREND TABLE
        ───────────────────────────────────────── */
        .trend-table {
            width: 100%;
            border-collapse: collapse;
        }

        .trend-table th {
            background: #378ADD;
            color: #fff;
            font-size: 8.5px;
            padding: 5px 8px;
            text-align: center;
            font-weight: 600;
        }

        .trend-table td {
            font-size: 8.5px;
            padding: 5px 8px;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
        }

        .trend-table tr:nth-child(even) td {
            background: #f8f9fa;
        }

        .trend-table td:first-child {
            text-align: left;
            font-weight: 600;
            color: #495057;
        }

        /* Mini sparkline bar inside trend table */
        .spark-bar {
            display: inline-block;
            height: 8px;
            border-radius: 2px;
            vertical-align: middle;
        }

        /* ─────────────────────────────────────────
           DEPARTMENT TABLE
        ───────────────────────────────────────── */
        .dept-table {
            width: 100%;
            border-collapse: collapse;
        }

        .dept-table th {
            background: #1a5fa8;
            color: #fff;
            font-size: 8.5px;
            padding: 5px 8px;
            text-align: center;
            font-weight: 600;
        }

        .dept-table th:first-child {
            text-align: left;
        }

        .dept-table td {
            font-size: 8.5px;
            padding: 5px 8px;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
        }

        .dept-table tr:nth-child(even) td {
            background: #f8f9fa;
        }

        .dept-table td:first-child {
            text-align: left;
            font-weight: 600;
            color: #212529;
        }

        .badge {
            display: inline-block;
            border-radius: 4px;
            padding: 1px 6px;
            font-size: 7.5px;
            font-weight: 700;
        }

        .badge-orange {
            background: #fff3cd;
            color: #856404;
        }

        .badge-green {
            background: #d1e7dd;
            color: #155724;
        }

        .badge-cyan {
            background: #cff4fc;
            color: #0c5460;
        }

        .badge-teal {
            background: #d1f2eb;
            color: #146356;
        }

        .badge-red {
            background: #f8d7da;
            color: #842029;
        }

        /* Progress bar inside dept table */
        .dept-bar-wrap {
            width: 100%;
        }

        .dept-bar-outer {
            background: #e9ecef;
            border-radius: 3px;
            height: 7px;
            width: 80px;
            display: inline-block;
            vertical-align: middle;
        }

        .dept-bar-inner {
            height: 7px;
            border-radius: 3px;
            background: #378ADD;
            display: block;
        }

        /* ─────────────────────────────────────────
           STATION TABLE
        ───────────────────────────────────────── */
        .station-table {
            width: 100%;
            border-collapse: collapse;
        }

        .station-table th {
            background: #2ca58d;
            color: #fff;
            font-size: 8.5px;
            padding: 5px 8px;
            font-weight: 600;
        }

        .station-table th:first-child {
            text-align: left;
        }

        .station-table td {
            font-size: 8.5px;
            padding: 5px 8px;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
        }

        .station-table tr:nth-child(even) td {
            background: #f8f9fa;
        }

        .station-table td:first-child {
            text-align: left;
        }

        /* ─────────────────────────────────────────
           DAILY TREND  (dot + count row)
        ───────────────────────────────────────── */
        .daily-table {
            width: 100%;
            border-collapse: collapse;
        }

        .daily-table th {
            font-size: 7.5px;
            color: #6c757d;
            font-weight: 600;
            text-align: center;
            padding-bottom: 4px;
            border-bottom: 1px solid #dee2e6;
        }

        .daily-table td {
            text-align: center;
            font-size: 8px;
            padding: 4px 2px;
        }

        .daily-bar-cell {
            vertical-align: bottom;
        }

        .daily-bar {
            display: block;
            margin: 0 auto 2px;
            border-radius: 2px 2px 0 0;
            background: #2ca58d;
            width: 100%;
        }

        /* ─────────────────────────────────────────
           RECENT COMPLAINTS
        ───────────────────────────────────────── */
        .recent-table {
            width: 100%;
            border-collapse: collapse;
        }

        .recent-table th {
            background: #495057;
            color: #fff;
            font-size: 8px;
            padding: 5px 6px;
            text-align: left;
            font-weight: 600;
        }

        .recent-table td {
            font-size: 8px;
            padding: 4px 6px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: top;
        }

        .recent-table tr:nth-child(even) td {
            background: #f8f9fa;
        }

        .status-pill {
            display: inline-block;
            border-radius: 10px;
            padding: 1px 7px;
            font-size: 7px;
            font-weight: 700;
        }

        .pill-pending {
            background: #fff3cd;
            color: #856404;
        }

        .pill-resolved {
            background: #d1e7dd;
            color: #155724;
        }

        .pill-reopened {
            background: #cff4fc;
            color: #0c5460;
        }

        .pill-closed {
            background: #d1f2eb;
            color: #146356;
        }

        .pill-critical {
            background: #f8d7da;
            color: #842029;
        }

        /* ─────────────────────────────────────────
           FOOTER
        ───────────────────────────────────────── */
        .pdf-footer {
            margin-top: 16px;
            border-top: 1px solid #dee2e6;
            padding-top: 8px;
            text-align: center;
            font-size: 8px;
            color: #adb5bd;
        }

        /* ─────────────────────────────────────────
           PAGE BREAK
        ───────────────────────────────────────── */
        .page-break {
            page-break-after: always;
        }

        /* ─────────────────────────────────────────
           TWO-COLUMN LAYOUT HELPER
        ───────────────────────────────────────── */
        .two-col {
            width: 100%;
            border-collapse: collapse;
        }

        .two-col td {
            vertical-align: top;
            padding: 0;
        }

        .col-left {
            width: 48%;
            padding-right: 8px;
        }

        .col-right {
            width: 48%;
            padding-left: 8px;
        }
    </style>
</head>

<body style="padding: 40px">

    {{-- ═══════════════════════════════════════
     HEADER BANNER
═══════════════════════════════════════ --}}
    <div class="header-banner">
        <table>
            <tr>
                <td style="width:65%">
                    <div class="header-title">Charts Dashboard</div>
                </td>
                <td style="width:35%; vertical-align:top; text-align:right;">
                    <div class="header-meta">
                        <strong style="color:#fff;">Generated</strong><br>
                        {{ $result['generatedAt'] }}<br><br>
                        <strong style="color:#fff;">Period</strong><br>
                        Last 6 Months
                    </div>
                </td>
            </tr>
        </table>
    </div>
    @php
        $total = $result['totalComplaints'] ?: 1;
    @endphp

    {{-- ═══════════════════════════════════════
     ROW 2 - STATUS BREAKDOWN (2-col)
═══════════════════════════════════════ --}}
    <div class="section-heading first">Status Breakdown</div>

    @php
        $statuses = [
            ['label' => 'Open / Pending', 'count' => $result['pendingComplaints'], 'color' => '#EF9F27'],
            ['label' => 'Rectified', 'count' => $result['resolvedComplaints'], 'color' => '#28a745'],
            ['label' => 'Reopened', 'count' => $result['reopenedComplaints'], 'color' => '#17a2b8'],
            ['label' => 'Closed', 'count' => $result['closedComplaints'], 'color' => '#2ca58d'],
            ['label' => 'Safety Critical', 'count' => $result['safetyComplaints'], 'color' => '#dc3545'],
        ];
        $maxCount = max(array_column($statuses, 'count')) ?: 1;
    @endphp

    @php
        // dd($chartPath);
        $imageData = null;

        if (file_exists($chartPath)) {
            $type = pathinfo($chartPath, PATHINFO_EXTENSION);
            $data = file_get_contents($chartPath);
            $imageData = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
    @endphp

    @if ($imageData)
        <div style="text-align:center; margin-bottom:20px;">
            <img src="{{ $imageData }}" style="width:400px;">
        </div>
    @else
        <p style="color:red;">Chart image not found</p>
    @endif

    <table class="two-col">
        <tr>

            <div style="width:100%;">
                <table style="width:100%; border-collapse:collapse;">

                    <tr>
                        <th style="background:#f8f9fa;color:#495057;font-size:8.5px;padding:5px 8px;text-align:left;">
                            Status
                        </th>
                        <th style="background:#f8f9fa;color:#495057;font-size:8.5px;padding:5px 8px;text-align:center;">
                            Count
                        </th>
                        <th style="background:#f8f9fa;color:#495057;font-size:8.5px;padding:5px 8px;text-align:center;">
                            %
                        </th>
                    </tr>

                    @foreach ($statuses as $s)
                        <tr>
                            <td style="padding:5px 8px;border-bottom:1px solid #f0f0f0;font-size:8.5px;">
                                <span
                                    style="display:inline-block;width:9px;height:9px;border-radius:2px;background:{{ $s['color'] }};margin-right:4px;"></span>
                                {{ $s['label'] }}
                            </td>

                            <td style="padding:5px 8px;text-align:center;font-weight:700;color:{{ $s['color'] }};">
                                {{ $s['count'] }}
                            </td>

                            <td style="padding:5px 8px;text-align:center;color:#6c757d;">
                                {{ $total > 0 ? round(($s['count'] / $total) * 100) : 0 }}%
                            </td>
                        </tr>
                    @endforeach

                    <tr>
                        <td style="padding:5px 8px;font-weight:700;">Total</td>
                        <td style="padding:5px 8px;text-align:center;font-weight:700;">
                            {{ $result['totalComplaints'] }}
                        </td>
                        <td style="padding:5px 8px;text-align:center;font-weight:700;">100%</td>
                    </tr>

                </table>
            </div>

            <div class="section-heading first">Complaints by Status</div>


            <div class="bar-chart-wrap">
                @foreach ($statuses as $s)
                    @php $pct = round($s['count']/$maxCount*100); @endphp

                    <div class="bar-row">
                        <div style="display:flex;justify-content:space-between;font-size:8px;margin-bottom:2px;">
                            <span style="font-weight:600;color:#495057;">{{ $s['label'] }}</span>
                            <span style="color:#6c757d;">{{ $s['count'] }}</span>
                        </div>

                        <div class="bar-track" style="width:100%;background:#eee;border-radius:4px;">
                            <span class="bar-fill"
                                style="width:{{ $pct }}%;background:{{ $s['color'] }};
                display:block;height:10px;border-radius:4px;">
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </tr>
    </table>

    {{-- ═══════════════════════════════════════
     ROW 6 - DAILY TREND (last 14 days)
═══════════════════════════════════════ --}}
    <div class="section-heading">Daily Complaints &mdash; Last 14 Days</div>


    @php
        // dd($chartPath);
        $imageDataTwo = null;

        if (file_exists($dailyChartPath)) {
            $type = pathinfo($dailyChartPath, PATHINFO_EXTENSION);
            $data = file_get_contents($dailyChartPath);
            $imageDataTwo = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
    @endphp

    @if ($imageDataTwo)
        <div style="text-align:center; margin-bottom:20px;">
            <img src="{{ $imageDataTwo }}" style="width:800px;">
        </div>
    @else
        <p style="color:red;">Chart image not found</p>
    @endif

    {{-- ═══════════════════════════════════════
     FOOTER
═══════════════════════════════════════ --}}
    <div class="pdf-footer">
        Chart Dashboard &bull; Generated on {{ $result['generatedAt'] }} &bull; CONFIDENTIAL &bull; For internal use
        only
    </div>




</body>

</html>

