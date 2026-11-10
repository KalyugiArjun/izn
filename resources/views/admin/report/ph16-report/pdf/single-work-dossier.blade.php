<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PH 16 Work Dossier #{{ $work->id }}</title>
    <style>
        @page {
            margin: 15mm 15mm 15mm 15mm;
            size: a4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #1a365d;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header-title {
            font-size: 15px;
            font-weight: bold;
            color: #1a365d;
            text-transform: uppercase;
        }
        .header-subtitle {
            font-size: 11px;
            color: #4a5568;
            font-weight: bold;
        }
        .meta-text {
            font-size: 8.5px;
            color: #718096;
            text-align: right;
        }
        .work-box {
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            padding: 10px 12px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .work-title {
            font-size: 13px;
            font-weight: bold;
            color: #2b6cb0;
            margin-bottom: 6px;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #1a365d;
            border-bottom: 1px solid #cbd5e0;
            padding-bottom: 4px;
            margin-top: 15px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        table.tbl {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table.tbl th {
            background-color: #edf2f7;
            font-size: 9px;
            font-weight: bold;
            padding: 5px 6px;
            border: 1px solid #cbd5e0;
            text-align: left;
        }
        table.tbl td {
            font-size: 8.5px;
            padding: 5px 6px;
            border: 1px solid #cbd5e0;
            vertical-align: top;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 8px;
            font-weight: bold;
            border-radius: 3px;
        }
        .badge-success { background-color: #c6f6d5; color: #22543d; }
        .badge-warning { background-color: #feebc8; color: #744210; }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
            font-size: 8px;
            color: #a0aec0;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="footer">
        North Eastern Railway, Izzatnagar Division &bull; SAATHI Work Dossier &bull; Generated on {{ $generatedAt }}
    </div>

    @php
        $ti = $work->ph16Ti;
        $tiName = !empty($work->concern_traffic_inspector) ? $work->concern_traffic_inspector : ($ti && $ti->designation ? $ti->designation->name : 'N/A');
        $deptName = $ti && $ti->department ? $ti->department->name : '-';
        $isCompleted = ($work->executionends && $work->executionends->count() > 0) || $work->status === 1;
    @endphp

    <table class="header-table">
        <tr>
            <td style="width: 70%;">
                <div class="header-title">NORTH EASTERN RAILWAY &bull; IZZATNAGAR DIVISION</div>
                <div class="header-subtitle">PH 16 WORK COMPREHENSIVE DOSSIER</div>
            </td>
            <td style="width: 30%;" class="meta-text">
                <div><strong>Work ID:</strong> #2026-{{ str_pad($work->id, 2, '0', STR_PAD_LEFT) }}</div>
                <div><strong>Printed:</strong> {{ $generatedAt }}</div>
            </td>
        </tr>
    </table>

    <div class="work-box">
        <div class="work-title">{{ $work->work_description_identification }}</div>
        <table style="width: 100%; font-size: 9px;">
            <tr>
                <td style="width: 50%;">
                    <strong>Identification Date:</strong> {{ $work->date_of_identification_of_work ? $work->date_of_identification_of_work->format('d/m/Y') : 'N/A' }}<br>
                    <strong>Concerned Inspector:</strong> {{ $tiName }}<br>
                    <strong>Department:</strong> {{ $deptName }}
                </td>
                <td style="width: 50%;">
                    <strong>Current Stage:</strong> <span class="badge {{ $isCompleted ? 'badge-success' : 'badge-warning' }}">{{ $work->current_stage }}</span><br>
                    <strong>Status:</strong> {{ $isCompleted ? 'Completed' : 'In Progress' }}<br>
                    <strong>Progress Estimated:</strong> {{ $work->progress_percent }}%
                </td>
            </tr>
        </table>
    </div>

    {{-- Milestones Summary --}}
    <div class="section-title">1. Paperwork Milestones Timeline</div>
    <table class="tbl">
        <thead>
            <tr>
                <th>Milestone Stage</th>
                <th>Status</th>
                <th>Date</th>
                <th>Days</th>
                <th>Details / Department</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Joint Survey</strong></td>
                <td>{{ $work->jointSurveys->count() > 0 ? 'Completed' : 'Pending' }}</td>
                <td>{{ $work->jointSurveys->first()?->date ?? '-' }}</td>
                <td>{{ $work->jointSurveys->first()?->days_taken ?? '-' }}</td>
                <td>{{ $work->jointSurveys->first()?->milestone ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Estimates</strong></td>
                <td>{{ $work->estimates->count() > 0 ? $work->estimates->count() . ' Depts' : 'Pending' }}</td>
                <td>-</td>
                <td>-</td>
                <td>{{ $work->estimates->pluck('department')->implode(', ') ?: '-' }}</td>
            </tr>
            <tr>
                <td><strong>Review Estimates</strong></td>
                <td>{{ $work->reviewEstimates->count() > 0 ? 'Completed' : 'Pending' }}</td>
                <td>{{ $work->reviewEstimates->first()?->review_estimate_date ?? '-' }}</td>
                <td>{{ $work->reviewEstimates->first()?->days_taken ?? '-' }}</td>
                <td>{{ $work->reviewEstimates->first()?->milestone ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Approval & Listing</strong></td>
                <td>{{ $work->approvalListings->count() > 0 ? 'Completed' : 'Pending' }}</td>
                <td>{{ $work->approvalListings->first()?->date ?? '-' }}</td>
                <td>{{ $work->approvalListings->first()?->days_taken ?? '-' }}</td>
                <td>{{ $work->approvalListings->first()?->milestone ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Finance Vetting & Sanction</strong></td>
                <td>{{ $work->financeVettingSanctions->count() > 0 ? 'Completed' : 'Pending' }}</td>
                <td>{{ $work->financeVettingSanctions->first()?->date ?? '-' }}</td>
                <td>{{ $work->financeVettingSanctions->first()?->days_taken ?? '-' }}</td>
                <td>{{ $work->financeVettingSanctions->first()?->milestone ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Tendering</strong></td>
                <td>{{ $work->tenderings->count() > 0 ? 'Completed' : 'Pending' }}</td>
                <td>{{ $work->tenderings->first()?->date ?? '-' }}</td>
                <td>-</td>
                <td>{{ $work->tenderings->first()?->department ?? '-' }} ({{ $work->tenderings->first()?->milestone ?? '-' }})</td>
            </tr>
            <tr>
                <td><strong>ESP / SIP Plans</strong></td>
                <td>{{ ($work->esps->count() > 0 || $work->sips->count() > 0) ? 'Available' : 'Pending' }}</td>
                <td>ESP: {{ $work->esps->first()?->date ?? '-' }} | SIP: {{ $work->sips->first()?->date ?? '-' }}</td>
                <td>-</td>
                <td>{{ $work->esps->count() }} ESP records, {{ $work->sips->count() }} SIP records</td>
            </tr>
            <tr>
                <td><strong>Execution Start</strong></td>
                <td>{{ $work->executions->count() > 0 ? 'Started' : 'Pending' }}</td>
                <td>{{ $work->executions->first()?->date ?? '-' }}</td>
                <td>{{ $work->executions->first()?->days_taken ?? '-' }}</td>
                <td>Target Date: {{ $work->executions->first()?->work_completion_date ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Execution End / Handover</strong></td>
                <td>{{ $work->executionends->count() > 0 ? 'Handed Over' : 'Pending' }}</td>
                <td>{{ $work->executionends->first()?->execution_completion_date ?? '-' }}</td>
                <td>{{ $work->executionends->first()?->days_taken ?? '-' }}</td>
                <td>Remarks: {{ $work->executionends->first()?->remarks ?? '-' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Variations --}}
    @if ($work->variations->count() > 0)
        <div class="section-title">2. Work Variations (Total: {{ $work->variations->count() }})</div>
        <table class="tbl">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date of Variation</th>
                    <th>Nature of Variation</th>
                    <th>Revised TDC</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($work->variations as $i => $v)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $v->date_of_variation ?? '-' }}</td>
                        <td>{{ $v->nature_of_variation ?? '-' }}</td>
                        <td><strong>{{ $v->revised_tdc ?? '-' }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Inspection Logs --}}
    @if ($work->inspectionLogs->count() > 0)
        <div class="section-title">3. Inspection Logs (Total: {{ $work->inspectionLogs->count() }})</div>
        <table class="tbl">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Inspection Date</th>
                    <th>Authority</th>
                    <th>Official Name</th>
                    <th>Dept Inspected</th>
                    <th>Work Done Detail</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($work->inspectionLogs as $i => $log)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $log->date_of_inspection ? date('d/m/Y', strtotime($log->date_of_inspection)) : '-' }}</td>
                        <td>{{ $log->inspecting_authority ?? '-' }}</td>
                        <td><strong>{{ $log->name_of_inspecting_official ?? '-' }}</strong></td>
                        <td>{{ $log->department_inspected ?? '-' }}</td>
                        <td>{{ $log->work_done_detail ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</body>
</html>

