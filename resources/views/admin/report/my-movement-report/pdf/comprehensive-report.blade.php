<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Movement Records - Comprehensive Report</title>
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
        .badge-danger { background-color: #fed7d7; color: #9b2c2c; }
        .badge-success { background-color: #c6f6d5; color: #22543d; }
        .badge-primary { background-color: #bee3f8; color: #2a4365; }
        .badge-warning { background-color: #feebc8; color: #744210; }
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
        .fw-bold { font-weight: bold; }
    </style>
</head>
<body>

    @php
        $generatedAt = \Carbon\Carbon::now()->format('d M, Y h:i A');
    @endphp

    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td style="width: 70%;">
                <div class="header-title">MODULE 3: MY MOVEMENT RECORD - Comprehensive Report</div>
                <div class="header-subtitle">Saathi App &bull; Inspection Movements, Regular Inspections & Officer Activity Logs</div>
            </td>
            <td class="meta-text" style="width: 30%;">
                <div><strong>Generated:</strong> {{ $generatedAt }}</div>
                <div><strong>Total Movements:</strong> {{ $records->count() }}</div>
            </td>
        </tr>
    </table>

    {{-- Summary KPIs --}}
    <table class="summary-box">
        <tr>
            <td>
                <div class="num">{{ $totalMovements }}</div>
                <div class="lbl">Total Movements</div>
            </td>
            <td>
                <div class="num">{{ $regularCount }}</div>
                <div class="lbl">Regular Inspections</div>
            </td>
            <td>
                <div class="num">{{ $records->filter(fn($r) => str_contains(strtoupper($r->movement->name ?? ''), 'SAFETY'))->count() }}</div>
                <div class="lbl">Safety Drives</div>
            </td>
            <td>
                <div class="num">{{ $records->filter(fn($r) => str_contains(strtoupper($r->movement->name ?? ''), 'LEAVE'))->count() }}</div>
                <div class="lbl">On Leave</div>
            </td>
            <td>
                <div class="num">{{ $deficiencyCount }}</div>
                <div class="lbl" style="color: #c53030;">Major Deficiencies</div>
            </td>
        </tr>
    </table>

    {{-- Data Table --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20px;" class="text-center">#</th>
                <th style="width: 65px;">Date & Time</th>
                <th style="width: 100px;">Movement By</th>
                <th style="width: 90px;">Type of Movement</th>
                <th style="width: 90px;">Inspection Type</th>
                <th style="width: 230px;">Details Captured</th>
                <th style="width: 90px;">Station / Location</th>
                <th style="width: 55px;" class="text-center">Deficiency</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $index => $r)
                @php
                    $mName = strtoupper($r->movement->name ?? '');
                    $reg = $r->regularInspection;
                    $amb = $r->ambushInspection;
                    $tName = $reg && $reg->type ? strtoupper($reg->type->name) : '';
                    
                    $dateStr = $r->inspection_date 
                        ? \Carbon\Carbon::parse($r->inspection_date)->format('d/m/Y') 
                        : ($r->created_at ? $r->created_at->format('d/m/Y') : '-');
                    $timeStr = $r->created_at ? $r->created_at->format('h:i A') : '-';

                    $name = $r->officer_name ?: ($r->employee->name ?? '-');
                    $desig = $r->designation ?: ($r->employee->designation->name ?? '-');
                    
                    $hasDef = ($reg && strtoupper($reg->major_deficiency ?? '') === 'YES') || ($amb && strtoupper($amb->major_deficiency ?? '') === 'YES');
                    $defText = $reg->write_deficiency ?? ($amb->write_deficiency ?? '');
                @endphp
                <tr>
                    {{-- # --}}
                    <td class="text-center">{{ $index + 1 }}</td>

                    {{-- Date & Time --}}
                    <td>
                        <strong>{{ $dateStr }}</strong><br>
                        <span style="color: #718096;">{{ $timeStr }}</span>
                    </td>

                    {{-- Movement By --}}
                    <td>
                        <strong>{{ $name }}</strong><br>
                        <span style="color: #4a5568;">{{ $desig }}</span>
                    </td>

                    {{-- Type of Movement --}}
                    <td>
                        <strong>{{ $r->movement->name ?? '-' }}</strong>
                    </td>

                    {{-- Inspection Type --}}
                    <td>
                        @if($reg && $reg->type)
                            <span class="badge badge-primary">{{ $reg->type->name }}</span>
                            @if($reg->day_night)
                                <div style="color: #718096; font-size: 6.5px;">{{ strtoupper($reg->day_night) }}</div>
                            @endif
                        @elseif($amb && $amb->ambushType)
                            <span class="badge badge-warning">{{ $amb->ambushType->name }}</span>
                            @if($amb->day_night)
                                <div style="color: #718096; font-size: 6.5px;">{{ strtoupper($amb->day_night) }}</div>
                            @endif
                        @else
                            <span style="color: #a0aec0;">-</span>
                        @endif
                    </td>

                    {{-- Details Captured --}}
                    <td>
                        @if(str_contains($mName, 'REGULAR') && $reg)
                            @if(str_contains($tName, 'THOROUGH') || str_contains($tName, 'SAFETY'))
                                <div><strong>Station:</strong> {{ $reg->station ? $reg->station->name . ' (' . $reg->station->code . ')' : '-' }}</div>
                            @elseif(str_contains($tName, 'SURPRISE') || str_contains($tName, 'NIGHT'))
                                <div><strong>Station:</strong> {{ $reg->station ? $reg->station->name : '-' }} &bull; <strong>LC Gate:</strong> {{ $reg->gate_no ?: '-' }} ({{ $reg->engg_traffic ?: '-' }})</div>
                                <div><strong>Block Section:</strong> {{ $reg->block_section ?: '-' }}</div>
                            @elseif(str_contains($tName, 'LC GATE'))
                                <div><strong>LC Gate No:</strong> {{ $reg->gate_no ?: '-' }} ({{ $reg->engg_traffic ?: '-' }})</div>
                                <div><strong>Block Section:</strong> {{ $reg->block_section ?: '-' }}</div>
                            @elseif(str_contains($tName, 'LC AMBUSH'))
                                <div><strong>LC Gate:</strong> {{ $reg->gate_no ?: '-' }} &bull; <strong>Train No:</strong> {{ $reg->train_no ?: '-' }} &bull; <strong>Section:</strong> {{ $reg->block_section ?: '-' }}</div>
                            @elseif(str_contains($tName, 'FOOTPLATE') || str_contains($tName, 'BRAKE-VAN') || str_contains($tName, 'BRAKE VAN'))
                                <div><strong>Inspection By:</strong> {{ $reg->goods_passenger ?: '-' }} &bull; <strong>Train No:</strong> {{ $reg->train_no ?: '-' }}</div>
                                <div><strong>Block Section:</strong> {{ $reg->block_section ?: '-' }}</div>
                            @elseif(str_contains($tName, 'DIESEL LOBBY'))
                                <div><strong>Diesel Lobby Location:</strong> {{ $reg->dieselLobby->name ?? ($reg->location_id ?: '-') }}</div>
                            @elseif(str_contains($tName, 'RUNNING ROOM'))
                                <div><strong>Running Room Location:</strong> {{ $reg->runningRoom->name ?? ($reg->location_id ?: '-') }}</div>
                            @else
                                <div><strong>Details:</strong> {{ $reg->block_section ?: ($reg->station->name ?? 'Regular Inspection logged') }}</div>
                            @endif

                        @elseif(str_contains($mName, 'AMBUSH') && $amb)
                            @if($amb->shunting_observation)
                                <div><strong>Shunting Obs:</strong> {{ \Illuminate\Support\Str::limit($amb->shunting_observation, 35) }}</div>
                            @endif
                            @if($amb->train_no)
                                <div><strong>Train No:</strong> {{ $amb->train_no }}</div>
                            @endif
                            @if($amb->lc_gate_no)
                                <div><strong>LC Gate:</strong> {{ $amb->lc_gate_no }} &bull; <strong>Section:</strong> {{ $amb->block_section ?: '-' }}</div>
                            @endif

                        @elseif(str_contains($mName, 'SAFETY DRIVE'))
                            <div><strong>Type of Safety Drive:</strong> {{ $r->safetyDrive->name ?? '-' }}</div>
                        @elseif(str_contains($mName, 'JOINT SIGNAL SIGHTING'))
                            <div><strong>Inspection By:</strong> {{ $r->goods_passenger ?: '-' }} &bull; <strong>Train:</strong> {{ $r->train_no ?: '-' }} &bull; <strong>Section:</strong> {{ $r->block_section ?: '-' }}</div>
                        @elseif(str_contains($mName, 'NI DUTY'))
                            <div><strong>Station:</strong> {{ $r->station ? $r->station->name . ' (' . $r->station->code . ')' : '-' }}</div>
                        @elseif(str_contains($mName, 'JOINT NOTE'))
                            <div><strong>Station:</strong> {{ $r->station ? $r->station->name : '-' }} &bull; <strong>Reason:</strong> {{ $r->reason ?: '-' }} &bull; <strong>PDF:</strong> {{ $r->joint_note_pdf ? 'Attached' : 'None' }}</div>
                        @elseif(str_contains($mName, 'SURVEY'))
                            <div><strong>Station:</strong> {{ $r->station ? $r->station->name : '-' }} &bull; <strong>Reason:</strong> {{ $r->reason ?: '-' }} &bull; <strong>PDF:</strong> {{ $r->survey_pdf ? 'Attached' : 'None' }}</div>
                        @elseif(str_contains($mName, 'ATTEND BLOCK'))
                            <div><strong>Type of Attend Block:</strong> {{ $r->attendBlock->name ?? '-' }}</div>
                        @elseif(str_contains($mName, 'ON LEAVE'))
                            <div><strong>Type of Leave:</strong> {{ $r->typeOfLeave->name ?? '-' }}</div>
                        @elseif(str_contains($mName, 'OFFICE') || str_contains($mName, 'OTHER'))
                            <div><strong>Description:</strong> {{ $r->reason ?: '-' }}</div>
                        @elseif(str_contains($mName, 'REST') || str_contains($mName, 'COUNSELING'))
                            <div style="color: #718096;"><em>No additional fields</em></div>
                        @else
                            <div>{{ $r->reason ?: '-' }}</div>
                        @endif
                    </td>

                    {{-- Station / Location --}}
                    <td>
                        @php
                            $loc = $r->station ? $r->station->name : null;
                            if(!$loc && $reg && $reg->station) $loc = $reg->station->name;
                            if(!$loc && $amb && $amb->station) $loc = $amb->station->name;
                            if(!$loc && $reg && $reg->dieselLobby) $loc = $reg->dieselLobby->name;
                            if(!$loc && $reg && $reg->runningRoom) $loc = $reg->runningRoom->name;
                        @endphp
                        <div>{{ $loc ?: '-' }}</div>
                        @if($r->gps_location)
                            <div style="color: #718096; font-size: 6.5px;">GPS: {{ $r->gps_location }}</div>
                        @endif
                    </td>

                    {{-- Deficiency --}}
                    <td class="text-center">
                        @if($hasDef)
                            <span class="badge badge-danger">DEFICIENCY</span>
                            @if($defText)
                                <div style="color: #c53030; font-size: 6.5px; margin-top: 2px;">{{ \Illuminate\Support\Str::limit($defText, 30) }}</div>
                            @endif
                        @elseif($reg || $amb)
                            <span class="badge badge-success">COMPLIANT</span>
                        @else
                            <span style="color: #a0aec0;">N/A</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px; color: #a0aec0;">No movement records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        MODULE 3: My Movement Records Report &bull; Page printed on {{ $generatedAt }} &bull; Confidential - For Internal Railway Administration Only
    </div>

</body>
</html>
