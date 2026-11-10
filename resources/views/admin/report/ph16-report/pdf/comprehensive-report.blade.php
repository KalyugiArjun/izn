<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PH 16 Works Comprehensive Report</title>
    <style>
        @page {
            margin: 15mm 10mm 15mm 10mm;
            size: a4 landscape;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9px;
            color: #333;
            line-height: 1.3;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #1a365d;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header-title {
            font-size: 14px;
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
            font-size: 8px;
            color: #718096;
            text-align: right;
        }
        .summary-box {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
        }
        .summary-box td {
            padding: 6px 10px;
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            text-align: center;
        }
        .summary-box .num {
            font-size: 13px;
            font-weight: bold;
            color: #2b6cb0;
        }
        .summary-box .lbl {
            font-size: 8px;
            color: #718096;
            text-transform: uppercase;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        table.data-table th {
            background-color: #2b6cb0;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: bold;
            padding: 6px 4px;
            border: 1px solid #2b6cb0;
            text-align: left;
        }
        table.data-table td {
            font-size: 8px;
            padding: 5px 4px;
            border: 1px solid #cbd5e0;
            vertical-align: top;
        }
        table.data-table tr:nth-child(even) td {
            background-color: #f7fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 4px;
            font-size: 7.5px;
            font-weight: bold;
            border-radius: 3px;
        }
        .badge-success { background-color: #c6f6d5; color: #22543d; }
        .badge-warning { background-color: #feebc8; color: #744210; }
        .badge-info { background-color: #bee3f8; color: #2a4365; }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
            font-size: 7.5px;
            color: #a0aec0;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="footer">
        North Eastern Railway, Izzatnagar Division &bull; SAATHI System Generated Report &bull; Page printed automatically
    </div>

    <table class="header-table">
        <tr>
            <td style="width: 70%;">
                <div class="header-title">NORTH EASTERN RAILWAY &bull; IZZATNAGAR DIVISION</div>
                <div class="header-subtitle">PH 16 WORKS COMPREHENSIVE PROGRESS REGISTER & REPORT</div>
            </td>
            <td style="width: 30%;" class="meta-text">
                <div><strong>Generated Date:</strong> {{ $generatedAt }}</div>
                <div><strong>Total Works Listed:</strong> {{ $works->count() }}</div>
            </td>
        </tr>
    </table>

    <table class="summary-box">
        <tr>
            <td>
                <div class="num">{{ $works->count() }}</div>
                <div class="lbl">Total Listed Works</div>
            </td>
            <td>
                <div class="num">{{ $works->filter(fn($w) => ($w->executionends && $w->executionends->count() > 0) || $w->status === 1)->count() }}</div>
                <div class="lbl">Completed Works</div>
            </td>
            <td>
                <div class="num">{{ $works->filter(fn($w) => !($w->executionends && $w->executionends->count() > 0) && $w->status !== 1)->count() }}</div>
                <div class="lbl">In Progress Works</div>
            </td>
            <td>
                <div class="num">{{ $works->sum(fn($w) => $w->inspectionLogs->count()) }}</div>
                <div class="lbl">Total Inspection Logs</div>
            </td>
            <td>
                <div class="num">{{ $works->sum(fn($w) => $w->variations->count()) }}</div>
                <div class="lbl">Total Variations</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;"># ID</th>
                <th style="width: 17%;">Work Description</th>
                <th style="width: 7%;">Identified Date</th>
                <th style="width: 12%;">Concerned TI / Dept</th>
                <th style="width: 9%;">Current Stage</th>
                <th style="width: 5%;">Progress</th>
                <th style="width: 10%;">Joint Survey & Est.</th>
                <th style="width: 10%;">Approval & Vetting</th>
                <th style="width: 9%;">Tendering & ESP/SIP</th>
                <th style="width: 10%;">Execution Details</th>
                <th style="width: 6%;">Insp. / Var.</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($works as $w)
                @php
                    $ti = $w->ph16Ti;
                    $tiName = !empty($w->concern_traffic_inspector) ? $w->concern_traffic_inspector : ($ti && $ti->designation ? $ti->designation->name : 'N/A');
                    $deptName = $ti && $ti->department ? $ti->department->name : '-';
                    $js = $w->jointSurveys->first();
                    $estCount = $w->estimates->count();
                    $app = $w->approvalListings->first();
                    $fv = $w->financeVettingSanctions->first();
                    $tnd = $w->tenderings->first();
                    $esp = $w->esps->first();
                    $sip = $w->sips->first();
                    $exs = $w->executions->first();
                    $exe = $w->executionends->first();
                    $isDone = ($exe !== null) || $w->status === 1;
                @endphp
                <tr>
                    <td><strong>#{{ str_pad($w->id, 3, '0', STR_PAD_LEFT) }}</strong></td>
                    <td>
                        <strong>{{ $w->work_description_identification }}</strong>
                    </td>
                    <td>{{ $w->date_of_identification_of_work ? $w->date_of_identification_of_work->format('d/m/Y') : '-' }}</td>
                    <td>
                        {{ $tiName }}<br>
                        <span style="color: #718096;">Dept: {{ $deptName }}</span>
                    </td>
                    <td>
                        <span class="badge {{ $isDone ? 'badge-success' : 'badge-info' }}">{{ $w->current_stage }}</span>
                    </td>
                    <td><strong>{{ $w->progress_percent }}%</strong></td>
                    <td>
                        <strong>JS:</strong> {{ $js ? ($js->date ?? 'Done') : 'Pending' }}<br>
                        <strong>Est:</strong> {{ $estCount > 0 ? $estCount . ' depts' : 'Pending' }}
                    </td>
                    <td>
                        <strong>App:</strong> {{ $app ? ($app->date ?? 'Done') : 'Pending' }}<br>
                        <strong>Vetting:</strong> {{ $fv ? ($fv->date ?? 'Done') : 'Pending' }}
                    </td>
                    <td>
                        <strong>Tnd:</strong> {{ $tnd ? ($tnd->date ?? 'Done') : 'Pending' }}<br>
                        <strong>Plan:</strong> {{ ($esp ? 'ESP ' : '') . ($sip ? 'SIP' : '') ?: 'Pending' }}
                    </td>
                    <td>
                        <strong>Start:</strong> {{ $exs ? ($exs->date ?? 'Done') : 'Pending' }}<br>
                        <strong>Comp:</strong> {{ $exe ? ($exe->execution_completion_date ?? 'Done') : 'In Prog.' }}
                    </td>
                    <td>
                        Insp: <strong>{{ $w->inspectionLogs->count() }}</strong><br>
                        Var: <strong>{{ $w->variations->count() }}</strong>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" style="text-align: center; padding: 15px;">No PH 16 Works found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>

