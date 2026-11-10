<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Staff Counseling Dossier #{{ $record->id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 15px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1e3c72;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .railway-title {
            font-size: 16px;
            font-weight: bold;
            color: #1e3c72;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .division-title {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 4px;
        }
        .dossier-badge {
            background-color: #1e3c72;
            color: #ffffff;
            padding: 3px 12px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 4px;
            display: inline-block;
        }
        .section-title {
            background-color: #f1f5f9;
            border-left: 4px solid #1e3c72;
            padding: 5px 8px;
            font-size: 11px;
            font-weight: bold;
            color: #1e3c72;
            margin-top: 12px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        table.bordered th, table.bordered td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
        }
        table.bordered th {
            background-color: #f8fafc;
            color: #334155;
            font-weight: bold;
            font-size: 10px;
            text-align: left;
        }
        .meta-label {
            font-weight: bold;
            color: #64748b;
            width: 25%;
        }
        .meta-val {
            color: #0f172a;
            width: 25%;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-online { background-color: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .badge-offline { background-color: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }
        .badge-group { background-color: #fef3c7; color: #b45309; }
        .badge-indiv { background-color: #e0f2fe; color: #0284c7; }
        .content-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px;
            border-radius: 4px;
            font-size: 11px;
            white-space: pre-line;
            line-height: 1.5;
        }
        .footer {
            margin-top: 25px;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
            font-size: 9px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

    @php
        $formattedDate = $record->counseling_date ? \Carbon\Carbon::parse($record->counseling_date)->format('d M, Y') : '-';
        $formattedTime = $record->counseling_time ?: '-';
        $isOnline = str_contains(strtolower($record->counseling_mode ?? ''), 'online');
        $loc = strtolower($record->counseling_location ?? '');
        $staffList = $record->resolved_staff_list;
    @endphp

    {{-- Official Header --}}
    <div class="header">
        <div class="railway-title">North Eastern Railway (NER)</div>
        <div class="division-title">Izzatnagar Division (IZN) · Operating Department</div>
        <div class="dossier-badge">Module 4: Staff Counseling Record Dossier</div>
    </div>

    {{-- 1. Counselor & Overview --}}
    <div class="section-title">1. Counselor & Session Overview</div>
    <table class="bordered">
        <tr>
            <td class="meta-label">Counselor Name:</td>
            <td class="meta-val"><strong>{{ $record->counselor_name ?: '-' }}</strong></td>
            <td class="meta-label">Counselor Designation:</td>
            <td class="meta-val">{{ $record->counselor_designation_name }}</td>
        </tr>
        <tr>
            <td class="meta-label">Date of Counseling:</td>
            <td class="meta-val">{{ $formattedDate }}</td>
            <td class="meta-label">Time of Counseling:</td>
            <td class="meta-val">{{ $formattedTime }}</td>
        </tr>
        <tr>
            <td class="meta-label">Mode of Counseling:</td>
            <td class="meta-val">
                <span class="badge {{ $isOnline ? 'badge-online' : 'badge-offline' }}">{{ strtoupper($record->counseling_mode ?: 'Offline') }}</span>
                @if($record->calling_mode)
                    ({{ $record->calling_mode }})
                @endif
            </td>
            <td class="meta-label">Counseling Type:</td>
            <td class="meta-val">
                <span class="badge {{ strcasecmp($record->counseling_type, 'Group') === 0 ? 'badge-group' : 'badge-indiv' }}">
                    {{ $record->counseling_type ?: 'Individual' }}
                </span>
            </td>
        </tr>
        <tr>
            <td class="meta-label">Location Type:</td>
            <td class="meta-val">{{ ucfirst($record->counseling_location ?: ($isOnline ? 'Online / Remote' : '-')) }}</td>
            <td class="meta-label">GPS Location:</td>
            <td class="meta-val">{{ $record->gps_location ?: 'N/A' }}</td>
        </tr>
    </table>

    {{-- 2. Counseled Staff Members --}}
    <div class="section-title">2. Counseled Staff Members ({{ count($staffList) }})</div>
    <table class="bordered">
        <thead>
            <tr>
                <th style="width: 30px;">#</th>
                <th>Staff Name</th>
                <th>Designation</th>
                <th>Station / Posting</th>
                <th>Employee Code / ID</th>
            </tr>
        </thead>
        <tbody>
            @forelse($staffList as $idx => $st)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td><strong>{{ $st['staff_name'] }}</strong></td>
                    <td>{{ $st['designation_name'] }}</td>
                    <td>{{ $st['station_name'] }}</td>
                    <td>{{ $st['employee_code'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #94a3b8;">No staff members mapped to this record.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- 3. Field Reference / Location Details --}}
    <div class="section-title">3. Specific Location Parameters Captured</div>
    <table class="bordered">
        @if($isOnline)
            <tr>
                <td class="meta-label">Mode:</td>
                <td class="meta-val">Online / Calling</td>
                <td class="meta-label">Calling Channel:</td>
                <td class="meta-val">{{ $record->calling_mode ?: 'Phone / Audio' }}</td>
            </tr>
        @else
            <tr>
                <td class="meta-label">Venue:</td>
                <td class="meta-val">{{ ucfirst($record->counseling_location ?: 'Station') }}</td>
                @if($loc === 'station')
                    <td class="meta-label">Station Name:</td>
                    <td class="meta-val">{{ $record->station ? $record->station->name . ' (' . $record->station->code . ')' : '-' }}</td>
                @elseif($loc === 'diesel lobby')
                    <td class="meta-label">Lobby Name:</td>
                    <td class="meta-val">{{ $record->dieselLobby ? $record->dieselLobby->name : '-' }}</td>
                @elseif($loc === 'running train')
                    <td class="meta-label">Train No:</td>
                    <td class="meta-val">{{ $record->train_no ?: '-' }}</td>
                @else
                    <td class="meta-label">-</td>
                    <td class="meta-val">-</td>
                @endif
            </tr>
            @if($loc === 'station' && $record->lc_gate)
                <tr>
                    <td class="meta-label">LC Gate No:</td>
                    <td colspan="3">{{ $record->lc_gate }}</td>
                </tr>
            @endif
        @endif
    </table>

    {{-- 4. Subject of Counseling --}}
    <div class="section-title">4. Subject of Counseling & Instructions Imparted</div>
    <div class="content-box">
        {{ $record->subject_of_counseling ?: 'No subject details recorded.' }}
    </div>

    {{-- Footer --}}
    <div class="footer">
        Generated on {{ date('d M, Y h:i A') }} · Saathi Staff Counseling System · Module 4 Record #{{ $record->id }}
    </div>

</body>
</html>

