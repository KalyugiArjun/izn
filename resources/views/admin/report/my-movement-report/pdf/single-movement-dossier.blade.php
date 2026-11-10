<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Movement Record Dossier #{{ $record->id }}</title>
    <style>
        @page {
            margin: 12mm 10mm 12mm 10mm;
            size: a4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 8.5px;
            color: #2d3748;
            line-height: 1.35;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #1e3c72;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }
        .header-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e3c72;
            text-transform: uppercase;
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
        .banner-box {
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            padding: 8px 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .banner-title {
            font-size: 12px;
            font-weight: bold;
            color: #1e3c72;
            margin-bottom: 2px;
        }
        .section-header {
            background-color: #1e3c72;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            padding: 4px 6px;
            margin-top: 10px;
            margin-bottom: 0;
            text-transform: uppercase;
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
        }
        .section-header.teal { background-color: #0f766e; }
        .section-header.red { background-color: #c53030; }
        table.tbl-module {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table.tbl-module th {
            width: 25%;
            background-color: #edf2f7;
            font-size: 8px;
            font-weight: bold;
            padding: 4px 6px;
            border: 1px solid #cbd5e0;
            text-align: left;
            color: #4a5568;
        }
        table.tbl-module td {
            width: 25%;
            font-size: 8px;
            padding: 4px 6px;
            border: 1px solid #cbd5e0;
            vertical-align: top;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 7.5px;
            font-weight: bold;
            border-radius: 3px;
        }
        .badge-danger { background-color: #fed7d7; color: #9b2c2c; }
        .badge-success { background-color: #c6f6d5; color: #22543d; }
        .badge-primary { background-color: #bee3f8; color: #2a4365; }
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
    </style>
</head>
<body>

    @php
        $inspDate = $record->inspection_date 
            ? \Carbon\Carbon::parse($record->inspection_date)->format('d M, Y') 
            : ($record->created_at ? $record->created_at->format('d M, Y') : '-');

        $inspTime = $record->created_at ? $record->created_at->format('h:i A') : '-';

        $officerName = $record->officer_name ?: ($record->employee->name ?? '-');
        $officerDesig = $record->designation ?: ($record->employee->designation->name ?? '-');
        $officerDept = $record->employee->department->name ?? '-';

        $mName = strtoupper($record->movement->name ?? '');
        $reg = $record->regularInspection;
        $amb = $record->ambushInspection;
        $tName = $reg && $reg->type ? strtoupper($reg->type->name) : '';

        $isRegular = str_contains($mName, 'REGULAR');
        $isAmbush = str_contains($mName, 'AMBUSH');

        $hasDeficiency = false;
        $deficiencyNote = null;
        if($reg && strtoupper($reg->major_deficiency ?? 'NO') === 'YES') {
            $hasDeficiency = true;
            $deficiencyNote = $reg->write_deficiency;
        } elseif($amb && strtoupper($amb->major_deficiency ?? 'NO') === 'YES') {
            $hasDeficiency = true;
            $deficiencyNote = $amb->write_deficiency;
        }

        $generatedAt = \Carbon\Carbon::now()->format('d M, Y h:i A');
    @endphp

    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td style="width: 70%;">
                <div class="header-title">MODULE 3 &bull; MY MOVEMENT RECORD</div>
                <div class="header-subtitle">Saathi App &bull; Officer Inspection Movement Dossier #{{ $record->id }}</div>
            </td>
            <td class="meta-text" style="width: 30%;">
                <div><strong>Generated:</strong> {{ $generatedAt }}</div>
                <div><strong>Record ID:</strong> #{{ $record->id }}</div>
            </td>
        </tr>
    </table>

    {{-- Banner Box --}}
    <div class="banner-box">
        <table style="width: 100%;">
            <tr>
                <td style="width: 75%;">
                    <div class="banner-title">{{ $record->movement->name ?? 'Movement Record' }}</div>
                    <div style="font-size: 9px; color: #4a5568;">
                        <strong>Movement Officer:</strong> {{ $officerName }} ({{ $officerDesig }}) &bull; 
                        <strong>Department:</strong> {{ $officerDept }}
                    </div>
                </td>
                <td style="width: 25%; text-align: right;">
                    @if ($hasDeficiency)
                        <span class="badge badge-danger">MAJOR DEFICIENCY</span>
                    @elseif ($isRegular || $isAmbush)
                        <span class="badge badge-success">COMPLIANT (NO DEFICIENCY)</span>
                    @else
                        <span class="badge badge-primary">{{ $record->movement->name ?? 'RECORD' }}</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- SECTION 1: MOVEMENT & OFFICER INFORMATION (MODULE 3 HEADER) --}}
    <div class="section-header">1. Movement &amp; Officer Overview</div>
    <table class="tbl-module">
        <tr>
            <th>Movement Date</th>
            <td>{{ $inspDate }}</td>
            <th>Movement Time</th>
            <td>{{ $inspTime }}</td>
        </tr>
        <tr>
            <th>Officer Name</th>
            <td>{{ $officerName }}</td>
            <th>Designation</th>
            <td>{{ $officerDesig }}</td>
        </tr>
        <tr>
            <th>Type of Movement</th>
            <td><strong>{{ $record->movement->name ?? '-' }}</strong></td>
            <th>Department</th>
            <td>{{ $officerDept }}</td>
        </tr>
        <tr>
            <th>GPS Location Coordinates</th>
            <td colspan="3">{{ $record->gps_location ?: 'Coordinates not recorded' }}</td>
        </tr>
    </table>

    {{-- SECTION 2: DETAILS CAPTURED (MODULE 3 & REGULAR INSPECTION SPECIFICATIONS) --}}
    <div class="section-header teal">2. Details Captured</div>

    @if($isRegular)
        {{-- Regular Inspection Details --}}
        <table class="tbl-module">
            <tr>
                <th>Type of Regular Inspection</th>
                <td><strong>{{ $reg && $reg->type ? $reg->type->name : 'General Regular Inspection' }}</strong></td>
                <th>Shift / Timing</th>
                <td>{{ $reg && $reg->day_night ? strtoupper($reg->day_night) . ' SHIFT' : '-' }}</td>
            </tr>

            @if(str_contains($tName, 'THOROUGH') || str_contains($tName, 'SAFETY'))
                <tr>
                    <th>Station Name</th>
                    <td colspan="3"><strong>{{ $reg->station ? $reg->station->name . ' (' . $reg->station->code . ')' : '-' }}</strong></td>
                </tr>

            @elseif(str_contains($tName, 'SURPRISE') || str_contains($tName, 'NIGHT'))
                <tr>
                    <th>Station Name</th>
                    <td>{{ $reg->station ? $reg->station->name : '-' }}</td>
                    <th>LC Gate No</th>
                    <td>{{ $reg->gate_no ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Engg / Traffic</th>
                    <td>{{ $reg->engg_traffic ?: '-' }}</td>
                    <th>Block Section</th>
                    <td>{{ $reg->block_section ?: '-' }}</td>
                </tr>

            @elseif(str_contains($tName, 'LC GATE'))
                <tr>
                    <th>LC Gate No</th>
                    <td>{{ $reg->gate_no ?: '-' }}</td>
                    <th>Engg / Traffic</th>
                    <td>{{ $reg->engg_traffic ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Block Section</th>
                    <td colspan="3">{{ $reg->block_section ?: '-' }}</td>
                </tr>

            @elseif(str_contains($tName, 'LC AMBUSH'))
                <tr>
                    <th>LC Gate No</th>
                    <td>{{ $reg->gate_no ?: '-' }}</td>
                    <th>Train No</th>
                    <td>{{ $reg->train_no ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Block Section</th>
                    <td colspan="3">{{ $reg->block_section ?: '-' }}</td>
                </tr>

            @elseif(str_contains($tName, 'FOOTPLATE') || str_contains($tName, 'BRAKE-VAN') || str_contains($tName, 'BRAKE VAN'))
                <tr>
                    <th>Inspection By (Goods / Passenger)</th>
                    <td>{{ $reg->goods_passenger ?: '-' }}</td>
                    <th>Train No</th>
                    <td>{{ $reg->train_no ?: '-' }}</td>
                </tr>
                <tr>
                    <th>Block Section</th>
                    <td colspan="3">{{ $reg->block_section ?: '-' }}</td>
                </tr>

            @elseif(str_contains($tName, 'DIESEL LOBBY'))
                <tr>
                    <th>Diesel Lobby Location</th>
                    <td colspan="3"><strong>{{ $reg->dieselLobby->name ?? ($reg->location_id ?: '-') }}</strong></td>
                </tr>

            @elseif(str_contains($tName, 'RUNNING ROOM'))
                <tr>
                    <th>Running Room Location</th>
                    <td colspan="3"><strong>{{ $reg->runningRoom->name ?? ($reg->location_id ?: '-') }}</strong></td>
                </tr>
            @endif

            <tr>
                <th>Major Deficiency Review</th>
                <td colspan="3">
                    @if($hasDeficiency)
                        <span class="badge badge-danger">YES - DEFICIENCY RECORDED</span>
                        <div style="color: #c53030; margin-top: 4px; font-weight: bold;">
                            {{ $deficiencyNote ?: 'Deficiency reported without specific comments.' }}
                        </div>
                    @else
                        <span class="badge badge-success">NO - COMPLIANT</span>
                    @endif
                </td>
            </tr>
        </table>

    @elseif($isAmbush)
        <table class="tbl-module">
            <tr>
                <th>Ambush Check Type</th>
                <td><strong>{{ $amb && $amb->ambushType ? $amb->ambushType->name : 'Ambush Check' }}</strong></td>
                <th>Station Name</th>
                <td>{{ $amb && $amb->station ? $amb->station->name . ' (' . $amb->station->code . ')' : '-' }}</td>
            </tr>
            @if($amb && $amb->shunting_observation)
                <tr>
                    <th>Observation</th>
                    <td colspan="3">{{ $amb->shunting_observation }}</td>
                </tr>
            @endif
            @if($amb && ($amb->train_no || $amb->lc_gate_no || $amb->block_section))
                <tr>
                    <th>Train No</th>
                    <td>{{ $amb->train_no ?: '-' }}</td>
                    <th>LC Gate / Section</th>
                    <td>{{ ($amb->lc_gate_no ? 'Gate #' . $amb->lc_gate_no : '') . ($amb->block_section ? ' (' . $amb->block_section . ')' : '-') }}</td>
                </tr>
            @endif
            <tr>
                <th>Major Deficiency</th>
                <td colspan="3">
                    @if($hasDeficiency)
                        <span class="badge badge-danger">YES - DEFICIENCY RECORDED</span>
                        <div style="color: #c53030; margin-top: 4px; font-weight: bold;">
                            {{ $deficiencyNote ?: 'Deficiency reported without specific comments.' }}
                        </div>
                    @else
                        <span class="badge badge-success">NO - COMPLIANT</span>
                    @endif
                </td>
            </tr>
        </table>

    @elseif(str_contains($mName, 'SAFETY DRIVE'))
        <table class="tbl-module">
            <tr>
                <th>Type of Safety Drive</th>
                <td colspan="3"><strong>{{ $record->safetyDrive->name ?? '-' }}</strong></td>
            </tr>
        </table>

    @elseif(str_contains($mName, 'JOINT SIGNAL SIGHTING'))
        <table class="tbl-module">
            <tr>
                <th>Inspection By (Goods / Passenger)</th>
                <td>{{ $record->goods_passenger ?: '-' }}</td>
                <th>Train No</th>
                <td>{{ $record->train_no ?: '-' }}</td>
            </tr>
            <tr>
                <th>Block Section</th>
                <td colspan="3">{{ $record->block_section ?: '-' }}</td>
            </tr>
        </table>

    @elseif(str_contains($mName, 'NI DUTY'))
        <table class="tbl-module">
            <tr>
                <th>Station Name</th>
                <td colspan="3"><strong>{{ $record->station ? $record->station->name . ' (' . $record->station->code . ')' : '-' }}</strong></td>
            </tr>
        </table>

    @elseif(str_contains($mName, 'JOINT NOTE'))
        <table class="tbl-module">
            <tr>
                <th>Station Name</th>
                <td>{{ $record->station ? $record->station->name . ' (' . $record->station->code . ')' : '-' }}</td>
                <th>PDF Attached</th>
                <td>{{ $record->joint_note_pdf ? 'Yes (' . basename($record->joint_note_pdf) . ')' : 'No' }}</td>
            </tr>
            <tr>
                <th>Reason</th>
                <td colspan="3">{{ $record->reason ?: '-' }}</td>
            </tr>
        </table>

    @elseif(str_contains($mName, 'SURVEY'))
        <table class="tbl-module">
            <tr>
                <th>Station Name</th>
                <td>{{ $record->station ? $record->station->name . ' (' . $record->station->code . ')' : '-' }}</td>
                <th>PDF Attached</th>
                <td>{{ $record->survey_pdf ? 'Yes (' . basename($record->survey_pdf) . ')' : 'No' }}</td>
            </tr>
            <tr>
                <th>Reason</th>
                <td colspan="3">{{ $record->reason ?: '-' }}</td>
            </tr>
        </table>

    @elseif(str_contains($mName, 'ATTEND BLOCK'))
        <table class="tbl-module">
            <tr>
                <th>Type of Attend Block</th>
                <td colspan="3"><strong>{{ $record->attendBlock->name ?? '-' }}</strong></td>
            </tr>
        </table>

    @elseif(str_contains($mName, 'ON LEAVE'))
        <table class="tbl-module">
            <tr>
                <th>Type of Leave</th>
                <td colspan="3"><strong>{{ $record->typeOfLeave->name ?? '-' }}</strong></td>
            </tr>
        </table>

    @elseif(str_contains($mName, 'OFFICE') || str_contains($mName, 'OTHER'))
        <table class="tbl-module">
            <tr>
                <th>Description / Reason</th>
                <td colspan="3">{{ $record->reason ?: '-' }}</td>
            </tr>
        </table>

    @elseif(str_contains($mName, 'REST') || str_contains($mName, 'COUNSELING'))
        <table class="tbl-module">
            <tr>
                <th>Status</th>
                <td colspan="3">Standard movement registered. No additional parameters required.</td>
            </tr>
        </table>

    @else
        <table class="tbl-module">
            <tr>
                <th>Details / Reason</th>
                <td colspan="3">{{ $record->reason ?: '-' }}</td>
            </tr>
        </table>
    @endif

    {{-- SECTION 3: SYSTEM AUDIT & METADATA --}}
    <div class="section-header" style="background-color: #4a5568;">3. System Audit &amp; Metadata</div>
    <table class="tbl-module">
        <tr>
            <th>Record ID</th>
            <td>#{{ $record->id }}</td>
            <th>Created By User ID</th>
            <td>{{ $record->created_by ?? '-' }}</td>
        </tr>
        <tr>
            <th>System Created At</th>
            <td>{{ $record->created_at ? $record->created_at->format('d M, Y h:i:s A') : '-' }}</td>
            <th>Last Updated At</th>
            <td>{{ $record->updated_at ? $record->updated_at->format('d M, Y h:i:s A') : '-' }}</td>
        </tr>
    </table>

    {{-- Signatures --}}
    <div style="margin-top: 40px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; font-size: 8px; color: #718096;">
                    Verified &amp; Audited by Saathi Movement Module<br>
                    North Eastern Railway &bull; Izzatnagar Division
                </td>
                <td style="width: 50%; text-align: right; font-size: 8px; color: #718096;">
                    ___________________________<br>
                    Inspecting Officer Signature
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        MODULE 3: My Movement Record Dossier &bull; Record #{{ $record->id }} &bull; Confidential - For Internal Railway Administration Only
    </div>

</body>
</html>
