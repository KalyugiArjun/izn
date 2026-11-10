<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Staff Counseling Comprehensive Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 10px;
            line-height: 1.3;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1e3c72;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .railway-title {
            font-size: 15px;
            font-weight: bold;
            color: #1e3c72;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .division-title {
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 4px;
        }
        .report-badge {
            background-color: #1e3c72;
            color: #ffffff;
            padding: 2px 10px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 4px;
            display: inline-block;
        }
        .kpi-row {
            margin-bottom: 10px;
            width: 100%;
        }
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
        }
        .kpi-table td {
            text-align: center;
            padding: 6px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .kpi-val {
            font-size: 14px;
            font-weight: bold;
            color: #1e3c72;
        }
        .kpi-lbl {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 6px;
        }
        table.data-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            text-align: left;
            text-transform: uppercase;
            font-size: 8px;
        }
        .badge {
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-online { background-color: #e0f2fe; color: #0369a1; }
        .badge-offline { background-color: #f1f5f9; color: #334155; }
        .footer {
            margin-top: 15px;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
            font-size: 8px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

    {{-- Official Header --}}
    <div class="header">
        <div class="railway-title">North Eastern Railway (NER)</div>
        <div class="division-title">Izzatnagar Division (IZN) · Operating Department</div>
        <div class="report-badge">Module 4: Staff Counseling Record Comprehensive Report</div>
    </div>

    {{-- KPI Summary --}}
    <div class="kpi-row">
        <table class="kpi-table">
            <tr>
                <td>
                    <div class="kpi-val">{{ number_format($totalCounselings) }}</div>
                    <div class="kpi-lbl">Total Counselings</div>
                </td>
                <td>
                    <div class="kpi-val">{{ number_format($onlineCount) }}</div>
                    <div class="kpi-lbl">Online Sessions</div>
                </td>
                <td>
                    <div class="kpi-val">{{ number_format($offlineCount) }}</div>
                    <div class="kpi-lbl">Offline Sessions</div>
                </td>
                <td>
                    <div class="kpi-val">{{ number_format($groupCount) }}</div>
                    <div class="kpi-lbl">Group Sessions</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Data Table --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">#</th>
                <th style="width: 75px;">Date & Time</th>
                <th style="width: 110px;">Counselor</th>
                <th style="width: 130px;">Counseled Staff</th>
                <th style="width: 55px;">Mode</th>
                <th style="width: 80px;">Location</th>
                <th>Subject of Counseling</th>
                <th style="width: 60px;">Type</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $rec)
                @php
                    $formattedDate = $rec->counseling_date ? \Carbon\Carbon::parse($rec->counseling_date)->format('d-m-Y') : '-';
                    $formattedTime = $rec->counseling_time ?: '-';
                    $isOnline = str_contains(strtolower($rec->counseling_mode ?? ''), 'online');
                    $loc = strtolower($rec->counseling_location ?? '');
                    $staffList = $rec->resolved_staff_list;

                    $locText = '—';
                    if (!$isOnline) {
                        if ($loc === 'station' && $rec->station) {
                            $locText = 'Station: ' . $rec->station->name . ($rec->lc_gate ? ' (LC: ' . $rec->lc_gate . ')' : '');
                        } elseif ($loc === 'diesel lobby' && $rec->dieselLobby) {
                            $locText = 'Lobby: ' . $rec->dieselLobby->name;
                        } elseif ($loc === 'running train') {
                            $locText = 'Train: ' . ($rec->train_no ?: '-');
                        } else {
                            $locText = ucfirst($rec->counseling_location ?: '-');
                        }
                    }
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $formattedDate }}</strong><br>
                        <span style="color:#64748b;">{{ $formattedTime }}</span>
                    </td>
                    <td>
                        <strong>{{ $rec->counselor_name ?: '-' }}</strong><br>
                        <span style="color:#64748b;">{{ $rec->counselor_designation_name }}</span>
                    </td>
                    <td>
                        @if(empty($staffList))
                            <span style="color:#94a3b8;">-</span>
                        @else
                            @foreach($staffList as $st)
                                <div>• <strong>{{ $st['staff_name'] }}</strong> ({{ $st['designation_name'] }})</div>
                            @endforeach
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $isOnline ? 'badge-online' : 'badge-offline' }}">{{ strtoupper($rec->counseling_mode ?: '-') }}</span>
                        @if($rec->calling_mode)
                            <br><span style="font-size:7px; color:#64748b;">{{ $rec->calling_mode }}</span>
                        @endif
                    </td>
                    <td>{{ $locText }}</td>
                    <td>{{ Str::limit($rec->subject_of_counseling ?: '-', 140) }}</td>
                    <td>
                        <span class="badge {{ strcasecmp($rec->counseling_type, 'Group') === 0 ? 'badge-offline' : 'badge-online' }}">
                            {{ $rec->counseling_type ?: 'Individual' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px; color: #94a3b8;">
                        No staff counseling records found matching the criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        Generated on {{ date('d M, Y h:i A') }} · Saathi Staff Counseling System · Total {{ $records->count() }} Records
    </div>

</body>
</html>

