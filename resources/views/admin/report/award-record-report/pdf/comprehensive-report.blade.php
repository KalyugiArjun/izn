<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Award Records Comprehensive Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 9px;
            line-height: 1.3;
            margin: 0;
            padding: 8px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0b2545;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .railway-title {
            font-size: 14px;
            font-weight: bold;
            color: #0b2545;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .division-title {
            font-size: 10px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 3px;
        }
        .report-badge {
            background-color: #0b2545;
            color: #ffffff;
            padding: 2px 8px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 4px;
            display: inline-block;
        }
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .kpi-table td {
            text-align: center;
            padding: 5px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
        }
        .kpi-val {
            font-size: 13px;
            font-weight: bold;
            color: #0b2545;
        }
        .kpi-lbl {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 4px;
            vertical-align: middle;
        }
        table.data-table thead tr.top-hdr th {
            background-color: #0b2545;
            color: #ffffff;
            font-weight: bold;
            font-size: 8.5px;
            text-align: center;
            text-transform: uppercase;
        }
        table.data-table thead tr.sub-hdr th {
            background-color: #dbe4ee;
            color: #0b2545;
            font-weight: bold;
            font-size: 8px;
            text-align: center;
            text-transform: uppercase;
        }
        table.data-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 4px;
            font-size: 7.5px;
            font-weight: bold;
            border-radius: 3px;
            text-align: center;
        }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-info { background: #e0f2fe; color: #075985; }
        .footer {
            margin-top: 10px;
            font-size: 8px;
            color: #64748b;
            text-align: right;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="railway-title">NORTH EASTERN RAILWAY &bull; IZZATNAGAR DIVISION</div>
        <div class="division-title">SAFETY & OPERATING DEPARTMENT &bull; SAATHI PORTAL</div>
        <div class="report-badge">MODULE 5: AWARD RECORD &bull; OFFICIAL REPORT</div>
    </div>

    {{-- KPI Summary Bar --}}
    <table class="kpi-table">
        <tr>
            <td style="width: 25%;">
                <div class="kpi-lbl">Total Nominations</div>
                <div class="kpi-val">{{ number_format($stats['total'] ?? 0) }}</div>
            </td>
            <td style="width: 25%;">
                <div class="kpi-lbl">Awarded / Approved</div>
                <div class="kpi-val" style="color: #166534;">{{ number_format($stats['awarded'] ?? 0) }}</div>
            </td>
            <td style="width: 25%;">
                <div class="kpi-lbl">Rejected</div>
                <div class="kpi-val" style="color: #991b1b;">{{ number_format($stats['rejected'] ?? 0) }}</div>
            </td>
            <td style="width: 25%;">
                <div class="kpi-lbl">Pending Review</div>
                <div class="kpi-val" style="color: #92400e;">{{ number_format($stats['pending'] ?? 0) }}</div>
            </td>
        </tr>
    </table>

    {{-- Table matching image layout --}}
    <table class="data-table">
        <thead>
            <tr class="top-hdr">
                <th rowspan="2" style="width: 22px;">#</th>
                <th colspan="2">Nomination</th>
                <th colspan="2">Nominee</th>
                <th rowspan="2" style="width: 60px;">Station</th>
                <th rowspan="2" style="width: 75px;">Area of Proficiency</th>
                <th rowspan="2">Description</th>
                <th rowspan="2" style="width: 45px;">Uploaded File</th>
                <th colspan="2">Decision</th>
                <th rowspan="2" style="width: 80px;">Certificate No.</th>
            </tr>
            <tr class="sub-hdr">
                <th style="width: 50px;">Date</th>
                <th style="width: 42px;">Time</th>
                <th style="width: 75px;">Name</th>
                <th style="width: 65px;">Designation</th>
                <th style="width: 55px;">Awarded Date</th>
                <th style="width: 55px;">Rejected Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $rec)
                @php
                    $nomDate = $rec->date ? \Carbon\Carbon::parse($rec->date)->format('d-m-Y') : '-';
                    $nomTime = $rec->time ? \Carbon\Carbon::parse($rec->time)->format('h:i A') : '-';
                    $nomName = $rec->name ?: ($rec->staff->name ?? 'N/A');
                    $nomDesig = $rec->designation->name ?? ($rec->staff->designation->name ?? 'N/A');
                    $stName = $rec->station->name ?? ($rec->station->code ?? '-');

                    $awardedDate = ($rec->status == 2 && $rec->checked_date) ? \Carbon\Carbon::parse($rec->checked_date)->format('d-m-Y') : '-';
                    $rejectedDate = ($rec->status == 3 && $rec->checked_date) ? \Carbon\Carbon::parse($rec->checked_date)->format('d-m-Y') : '-';
                @endphp
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
                    <td style="text-align: center;">{{ $nomDate }}</td>
                    <td style="text-align: center;">{{ $nomTime }}</td>
                    <td>
                        <strong>{{ $nomName }}</strong>
                        @if($rec->staff && $rec->staff->usmid)
                            <div style="font-size: 7.5px; color: #64748b;">USMID: {{ $rec->staff->usmid }}</div>
                        @endif
                    </td>
                    <td>{{ $nomDesig }}</td>
                    <td style="text-align: center;">{{ $stName }}</td>
                    <td>{{ $rec->proficiency->name ?? '-' }}</td>
                    <td style="font-size: 8px;">{{ \Illuminate\Support\Str::limit($rec->description, 80) }}</td>
                    <td style="text-align: center;">
                        @if($rec->file)
                            <span class="badge badge-info">{{ strtoupper($rec->file_type ?: 'FILE') }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if($rec->status == 2)
                            <span class="badge badge-success">{{ $awardedDate }}</span>
                            @if($rec->dom_comment)
                                <div style="font-size: 7px; color: #166534;">{{ \Illuminate\Support\Str::limit($rec->dom_comment, 25) }}</div>
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if($rec->status == 3)
                            <span class="badge badge-danger">{{ $rejectedDate }}</span>
                        @elseif($rec->status == 0)
                            <span class="badge badge-warning">Pending (TI)</span>
                        @elseif($rec->status == 1)
                            <span class="badge badge-info">Recommended</span>
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if($rec->certificate_no)
                            <strong style="color: #166534;">{{ $rec->certificate_no }}</strong>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" style="text-align: center; padding: 20px; color: #94a3b8;">
                        No award records found matching the query.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Generated on: {{ date('d M, Y h:i A') }} &bull; Saathi App &bull; North Eastern Railway
    </div>

</body>
</html>
