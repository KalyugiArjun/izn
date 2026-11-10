<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Employee Safety Record Comprehensive Report</title>
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
            border-bottom: 2px solid #1e3c72;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .railway-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e3c72;
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
            background-color: #1e3c72;
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
            color: #1e3c72;
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
            padding: 5px 6px;
            vertical-align: middle;
        }
        table.data-table thead tr th {
            background-color: #1e3c72;
            color: #ffffff;
            font-weight: bold;
            font-size: 8.5px;
            text-align: left;
            text-transform: uppercase;
        }
        table.data-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 7.5px;
            font-weight: bold;
            border-radius: 3px;
            text-align: center;
        }
        .badge-medical { background: #e0f2fe; color: #0369a1; }
        .badge-refresher { background: #dcfce7; color: #15803d; }
        .badge-gradation { background: #fef3c7; color: #b45309; }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-warning { background: #fef3c7; color: #92400e; }
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
        <div class="report-badge">MODULE 6: EMPLOYEE SAFETY RECORD &bull; OFFICIAL REPORT</div>
    </div>

    {{-- KPI Summary Bar --}}
    <table class="kpi-table">
        <tr>
            <td style="width: 25%;">
                <div class="kpi-lbl">Total Safety Records</div>
                <div class="kpi-val">{{ number_format($stats['total'] ?? 0) }}</div>
            </td>
            <td style="width: 25%;">
                <div class="kpi-lbl">Medical (PME) Records</div>
                <div class="kpi-val" style="color: #0369a1;">{{ number_format($stats['medical'] ?? 0) }}</div>
            </td>
            <td style="width: 25%;">
                <div class="kpi-lbl">Refresher Courses</div>
                <div class="kpi-val" style="color: #15803d;">{{ number_format($stats['refresher'] ?? 0) }}</div>
            </td>
            <td style="width: 25%;">
                <div class="kpi-lbl">Gradation Records</div>
                <div class="kpi-val" style="color: #b45309;">{{ number_format($stats['gradation'] ?? 0) }}</div>
            </td>
        </tr>
    </table>

    {{-- Table matching image layout --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px; text-align: center;">#</th>
                <th style="width: 60px;">Record Type</th>
                <th style="width: 120px;">Staff Name</th>
                <th style="width: 90px;">Designation</th>
                <th style="width: 90px;">Station</th>
                <th style="width: 70px;">Exam Date</th>
                <th style="width: 70px;">Due Date</th>
                <th style="width: 90px;">Certificate No / Marks</th>
                <th style="width: 60px; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $rec)
                @php
                    $examDate = $rec->exam_date ? \Carbon\Carbon::parse($rec->exam_date)->format('d-m-Y') : '-';
                    $dueDate = $rec->due_date ? \Carbon\Carbon::parse($rec->due_date)->format('d-m-Y') : '-';
                    
                    $typeClass = match($rec->type) {
                        'Medical' => 'badge-medical',
                        'Refresher' => 'badge-refresher',
                        'Gradation' => 'badge-gradation',
                        default => 'badge-medical'
                    };
                @endphp
                <tr>
                    <td style="text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
                    <td>
                        <span class="badge {{ $typeClass }}">{{ strtoupper($rec->type) }}</span>
                    </td>
                    <td>
                        <strong>{{ $rec->staff_name }}</strong>
                        @if($rec->usmid)
                            <div style="font-size: 7.5px; color: #64748b;">USMID: {{ $rec->usmid }}</div>
                        @endif
                    </td>
                    <td>{{ $rec->designation }}</td>
                    <td>{{ $rec->station }}</td>
                    <td>{{ $examDate }}</td>
                    <td><strong>{{ $dueDate }}</strong></td>
                    <td>
                        @if($rec->type === 'Gradation')
                            <strong>{{ $rec->total_marks }} / 100</strong> (Grade: {{ $rec->grade }})
                        @else
                            {{ $rec->certificate_no }}
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <span class="badge {{ str_contains($rec->due_status['text'], 'OVERDUE') ? 'badge-danger' : (str_contains($rec->due_status['text'], 'DUE SOON') ? 'badge-warning' : 'badge-success') }}">
                            {{ $rec->due_status['text'] }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 20px; color: #94a3b8;">
                        No employee safety records found matching your query.
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
