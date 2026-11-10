<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Station Complaints Comprehensive Report</title>
    <style>
        @page {
            margin: 10mm 8mm 12mm 8mm;
            size: a4 landscape;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8px;
            color: #2d3748;
            line-height: 1.25;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #1e3c72;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .header-title {
            font-size: 13px;
            font-weight: bold;
            color: #1e3c72;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-subtitle {
            font-size: 9.5px;
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
            margin-bottom: 8px;
            border-collapse: collapse;
        }
        .summary-box td {
            padding: 4px 6px;
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            text-align: center;
        }
        .summary-box .num {
            font-size: 11px;
            font-weight: bold;
            color: #1e3c72;
        }
        .summary-box .lbl {
            font-size: 7px;
            color: #718096;
            text-transform: uppercase;
            font-weight: 600;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.data-table th {
            background-color: #1e3c72;
            color: #ffffff;
            font-size: 7.5px;
            font-weight: bold;
            padding: 4px 3px;
            border: 1px solid #1e3c72;
            text-align: left;
            text-transform: uppercase;
        }
        table.data-table th.m1 { background-color: #1e3c72; }
        table.data-table th.m2 { background-color: #0d6efd; border-color: #0d6efd; }
        table.data-table th.m3 { background-color: #2b3a4a; border-color: #2b3a4a; }
        table.data-table td {
            font-size: 7.5px;
            padding: 4px 3px;
            border: 1px solid #cbd5e0;
            vertical-align: top;
        }
        table.data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 1px 3px;
            font-size: 7px;
            font-weight: bold;
            border-radius: 2px;
        }
        .badge-critical { background-color: #fed7d7; color: #9b2c2c; }
        .badge-pending { background-color: #feebc8; color: #744210; }
        .badge-rectified { background-color: #c6f6d5; color: #22543d; }
        .badge-closed { background-color: #e2e8f0; color: #4a5568; }
        .badge-assigned { background-color: #bee3f8; color: #2a4365; }
        .badge-reopened { background-color: #feefc3; color: #b7791f; }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 15px;
            border-top: 1px solid #e2e8f0;
            padding-top: 3px;
            font-size: 7.5px;
            color: #a0aec0;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-bold { font-weight: bold; }
    </style>
</head>
<body>

    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td style="width: 70%;">
                <div class="header-title">Station Complaints Records - Comprehensive Report</div>
                <div class="header-subtitle">Saathi App &bull; Complaints System Records (Module 1, 2 & 3)</div>
            </td>
            <td class="meta-text" style="width: 30%;">
                <div><strong>Generated:</strong> {{ $generatedAt }}</div>
                <div><strong>Total Records:</strong> {{ $complaints->count() }}</div>
            </td>
        </tr>
    </table>

    {{-- Summary KPIs --}}
    <table class="summary-box">
        <tr>
            <td>
                <div class="num">{{ $complaints->count() }}</div>
                <div class="lbl">Total Filtered</div>
            </td>
            <td>
                <div class="num">{{ $complaints->where('status', 0)->count() }}</div>
                <div class="lbl">Pending</div>
            </td>
            <td>
                <div class="num">{{ $complaints->where('status', 4)->count() }}</div>
                <div class="lbl">Assigned</div>
            </td>
            <td>
                <div class="num">{{ $complaints->where('status', 1)->count() }}</div>
                <div class="lbl">Rectified</div>
            </td>
            <td>
                <div class="num">{{ $complaints->where('status', 2)->count() }}</div>
                <div class="lbl">Reopened</div>
            </td>
            <td>
                <div class="num">{{ $complaints->where('status', 3)->count() }}</div>
                <div class="lbl">Closed</div>
            </td>
            <td>
                <div class="num">{{ $complaints->where('is_safety_critical', 1)->count() }}</div>
                <div class="lbl">Safety Critical</div>
            </td>
        </tr>
    </table>

    {{-- Data Table with 3 Modules --}}
    <table class="data-table">
        <thead>
            <tr>
                <th colspan="7" class="m1" style="text-align: center; border-right: 2px solid #fff;">MODULE 1: REGISTER COMPLAINT</th>
                <th colspan="4" class="m2" style="text-align: center; border-right: 2px solid #fff;">MODULE 2: ASSIGN & RECTIFY</th>
                <th colspan="3" class="m3" style="text-align: center;">MODULE 3: RE-OPEN & CLOSE</th>
            </tr>
            <tr>
                {{-- Module 1 --}}
                <th style="width: 25px;">#</th>
                <th style="width: 75px;">Complaint No</th>
                <th style="width: 60px;">Date & Time</th>
                <th style="width: 65px;">Station</th>
                <th style="width: 70px;">Dept & Cat</th>
                <th>Issue Description</th>
                <th style="width: 50px;">Safety</th>

                {{-- Module 2 --}}
                <th style="width: 75px;">Assigned To</th>
                <th style="width: 60px;">Assigned Date</th>
                <th style="width: 80px;">Rectifier / Remark</th>
                <th style="width: 60px;">Rectified Date</th>

                {{-- Module 3 --}}
                <th style="width: 70px;">Reopened Info</th>
                <th style="width: 60px;">Status</th>
                <th style="width: 60px;">Closed Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($complaints as $idx => $c)
                @php
                    $regDate = $c->created_at ? $c->created_at->format('d/m/Y') : '-';
                    $regTime = $c->created_at ? $c->created_at->format('H:i') : '';
                    $assignDate = $c->assigned_employee_at ? \Carbon\Carbon::parse($c->assigned_employee_at)->format('d/m/Y H:i') : '-';
                    $rectDate = $c->rectified_at ? \Carbon\Carbon::parse($c->rectified_at)->format('d/m/Y H:i') : ($c->rectification_date ? \Carbon\Carbon::parse($c->rectification_date)->format('d/m/Y') : '-');
                    $closedDate = ($c->status == 3 && $c->feedback_given_at) ? \Carbon\Carbon::parse($c->feedback_given_at)->format('d/m/Y H:i') : ($c->status == 3 ? \Carbon\Carbon::parse($c->updated_at)->format('d/m/Y H:i') : '-');
                    $reopenDate = $c->reopened_at ? \Carbon\Carbon::parse($c->reopened_at)->format('d/m/Y H:i') : '-';
                @endphp
                <tr>
                    {{-- Module 1 --}}
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="fw-bold">{{ $c->complaint_num }}</td>
                    <td>{{ $regDate }}<br><span style="color:#718096;">{{ $regTime }}</span></td>
                    <td><strong>{{ $c->station->name ?? '-' }}</strong><br><span style="color:#718096;">({{ $c->station->code ?? '-' }})</span></td>
                    <td><strong>{{ $c->department->name ?? '-' }}</strong><br><span style="color:#4a5568;">{{ $c->amenityCategory->name ?? '-' }}</span></td>
                    <td>{{ \Illuminate\Support\Str::limit($c->description ?? '-', 80) }}</td>
                    <td class="text-center">
                        @if($c->is_safety_critical)
                            <span class="badge badge-critical">CRITICAL</span>
                        @else
                            <span style="color:#718096;">Normal</span>
                        @endif
                    </td>

                    {{-- Module 2 --}}
                    <td>
                        @if($c->assignedEmployee)
                            <strong>{{ $c->assignedEmployee->name }}</strong><br>
                            <span style="color:#718096;">{{ $c->assignedEmployee->designation->name ?? '' }}</span>
                        @else
                            <span style="color:#a0aec0;">-</span>
                        @endif
                    </td>
                    <td>{{ $assignDate }}</td>
                    <td>
                        @if($c->rectifier_name || $c->rectification_remark)
                            <strong>{{ $c->rectifier_name ?? '-' }}</strong><br>
                            <span style="color:#718096;">{{ \Illuminate\Support\Str::limit($c->rectification_remark ?? '-', 45) }}</span>
                        @else
                            <span style="color:#a0aec0;">-</span>
                        @endif
                    </td>
                    <td>{{ $rectDate }}</td>

                    {{-- Module 3 --}}
                    <td>
                        @if($c->status == 2 || $c->reopened_at)
                            <span class="badge badge-reopened">Reopened</span><br>
                            <span style="color:#e53e3e;">{{ \Illuminate\Support\Str::limit($c->reopen_reason ?? '-', 35) }}</span>
                        @else
                            <span style="color:#a0aec0;">-</span>
                        @endif
                    </td>
                    <td>
                        @if($c->status == 0)
                            <span class="badge badge-pending">Pending</span>
                        @elseif($c->status == 1)
                            <span class="badge badge-rectified">Rectified</span>
                        @elseif($c->status == 2)
                            <span class="badge badge-reopened">Reopened</span>
                        @elseif($c->status == 3)
                            <span class="badge badge-closed">Closed</span>
                        @elseif($c->status == 4)
                            <span class="badge badge-assigned">Assigned</span>
                        @else
                            <span>{{ $c->status }}</span>
                        @endif
                    </td>
                    <td>{{ $closedDate }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="14" class="text-center" style="padding: 20px; color: #a0aec0;">No complaint records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Station Complaints Records Report &bull; Page printed on {{ $generatedAt }} &bull; Confidential - For Internal Railway Administration Only
    </div>

</body>
</html>

