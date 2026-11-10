<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ambush Check Dossier #{{ $ambush->id }}</title>
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
        $inspDate = $ambush->inspection && $ambush->inspection->inspection_date 
            ? \Carbon\Carbon::parse($ambush->inspection->inspection_date)->format('d M, Y') 
            : ($ambush->created_at ? $ambush->created_at->format('d M, Y') : '-');

        $inspTime = $ambush->from_time ?? ($ambush->created_at ? $ambush->created_at->format('h:i A') : '-');

        $officerName = $ambush->inspection->officer_name 
            ?? ($ambush->inspection->employee->name ?? '-');

        $officerDesig = $ambush->inspection->designation 
            ?? ($ambush->inspection->employee->designation->name ?? '-');

        $typeName = strtoupper($ambush->ambushType->name ?? '');
        $typeId = $ambush->type_of_ambush_id;

        $isShunting = str_contains($typeName, 'SHUNTING') || $typeId == 1;
        $isLoadStable = str_contains($typeName, 'LOAD STABLE') || $typeId == 2;
        $isEngBlock = str_contains($typeName, 'ENGINEERING') || $typeId == 3;
        $isLcVoice = str_contains($typeName, 'VOICE') || str_contains($typeName, 'LC') || $typeId == 4;
        $isBaTest = str_contains($typeName, 'BA TEST') || $typeId == 5;

        $hasDeficiency = strtoupper($ambush->major_deficiency ?? 'NO') === 'YES';
    @endphp

    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td style="width: 70%;">
                <div class="header-title">MODULE 2 &bull; AMBUSH CHECK RECORD</div>
                <div class="header-subtitle">Saathi App &bull; Safety Ambush Inspection Dossier #{{ $ambush->id }}</div>
            </td>
            <td class="meta-text" style="width: 30%;">
                <div><strong>Generated:</strong> {{ $generatedAt }}</div>
                <div><strong>Shift:</strong> {{ $ambush->day_night ?? 'DAY' }}</div>
            </td>
        </tr>
    </table>

    {{-- Banner Box --}}
    <div class="banner-box">
        <table style="width: 100%;">
            <tr>
                <td>
                    <div class="banner-title">{{ $ambush->ambushType->name ?? 'Ambush Check' }} &bull; Station: {{ $ambush->station->name ?? 'N/A' }}</div>
                    <div style="font-size: 8.5px; color: #4a5568;">
                        <strong>Date:</strong> {{ $inspDate }} &bull;
                        <strong>Time:</strong> {{ $ambush->from_time ?? '-' }} to {{ $ambush->to_time ?? '-' }} &bull;
                        <strong>Inspecting Officer:</strong> {{ $officerName }} ({{ $officerDesig }})
                    </div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    @if($hasDeficiency)
                        <span class="badge badge-danger">MAJOR DEFICIENCY</span>
                    @else
                        <span class="badge badge-success">NO DEFICIENCY (COMPLIANT)</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- SECTION 1: GENERAL --}}
    <div class="section-header">General Ambush Check Details</div>
    <table class="tbl-module">
        <tr>
            <th>Ambush Check Date</th>
            <td>{{ $inspDate }}</td>
            <th>Ambush Check Time</th>
            <td>{{ $inspTime }}</td>
        </tr>
        <tr>
            <th>Inspected By (Name)</th>
            <td>{{ $officerName }}</td>
            <th>Inspected By (Designation)</th>
            <td>{{ $officerDesig }}</td>
        </tr>
        <tr>
            <th>Station (Name & Code)</th>
            <td>{{ $ambush->station->name ?? '-' }} ({{ $ambush->station->code ?? '-' }})</td>
            <th>From Time & To Time</th>
            <td>{{ $ambush->from_time ?? '-' }} &rarr; {{ $ambush->to_time ?? '-' }}</td>
        </tr>
        <tr>
            <th>Day / Night Shift</th>
            <td>{{ $ambush->day_night ?? '-' }}</td>
            <th>Type of Ambush Check</th>
            <td>{{ $ambush->ambushType->name ?? '-' }}</td>
        </tr>
    </table>

    {{-- SECTION 2: SHUNTING --}}
    <div class="section-header red">1. Shunting Details</div>
    <table class="tbl-module">
        <tr>
            <th>Shunting Observation Of</th>
            <td>{{ $isShunting ? ($ambush->shunting_observation ?? '-') : 'N/A' }}</td>
            <th>T/806 No</th>
            <td>{{ $isShunting ? ($ambush->t_806_no ?? '-') : 'N/A' }}</td>
        </tr>
        <tr>
            <th>Shunting Staff</th>
            <td colspan="3">
                @if($isShunting && $ambush->shunting_staff_details && $ambush->shunting_staff_details->count() > 0)
                    @foreach($ambush->shunting_staff_details as $stf)
                        <div>&bull; <strong>{{ $stf['name'] }}</strong> ({{ $stf['designation'] }})</div>
                    @endforeach
                @elseif($isShunting && $ambush->staffName)
                    <div>&bull; <strong>{{ $ambush->staffName->name }}</strong> ({{ $ambush->designation ?? '-' }})</div>
                @else
                    <span style="color:#718096;">{{ $isShunting ? 'None recorded' : 'N/A' }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <th>Any Major Deficiency</th>
            <td colspan="3">
                @if($isShunting)
                    {{ $hasDeficiency ? 'YES - ' . ($ambush->write_deficiency ?? 'Deficiency recorded') : 'NO (Compliant)' }}
                @else
                    N/A
                @endif
            </td>
        </tr>
    </table>

    {{-- SECTION 3: LOAD STABLE --}}
    <div class="section-header red">2. Load Stable Details</div>
    <table class="tbl-module">
        <tr>
            <th>Load Type</th>
            <td>{{ $isLoadStable ? ($ambush->load_type ?? '-') : 'N/A' }}</td>
            <th>Total No. of Loads</th>
            <td>{{ $isLoadStable ? ($ambush->total_number_of_load ?? '-') : 'N/A' }}</td>
        </tr>
        <tr>
            <th>Running Line / Non-Running Line</th>
            <td>{{ $isLoadStable ? ($ambush->running_line ?? '-') : 'N/A' }}</td>
            <th>Stabling Line No.</th>
            <td>{{ $isLoadStable ? ($ambush->stabling_line_no ?? '-') : 'N/A' }}</td>
        </tr>
        <tr>
            <th>Any Major Deficiency</th>
            <td colspan="3">
                @if($isLoadStable)
                    {{ $hasDeficiency ? 'YES - ' . ($ambush->write_deficiency ?? 'Deficiency recorded') : 'NO (Compliant)' }}
                @else
                    N/A
                @endif
            </td>
        </tr>
    </table>

    {{-- SECTION 4: ENGINEERING BLOCK --}}
    <div class="section-header red">3. Engineering Block Details</div>
    <table class="tbl-module">
        <tr>
            <th>Location of Ambush Check</th>
            <td>{{ $isEngBlock ? ($ambush->station->name ?? ($ambush->block_section ?? '-')) : 'N/A' }}</td>
            <th>Block Taken By</th>
            <td>{{ $isEngBlock ? ($ambush->block_taken_by ?? '-') : 'N/A' }}</td>
        </tr>
        <tr>
            <th>Any Major Deficiency</th>
            <td colspan="3">
                @if($isEngBlock)
                    {{ $hasDeficiency ? 'YES - ' . ($ambush->write_deficiency ?? 'Deficiency recorded') : 'NO (Compliant)' }}
                @else
                    N/A
                @endif
            </td>
        </tr>
    </table>

    {{-- SECTION 5: BA TEST --}}
    <div class="section-header red">4. BA Test Details (Breath Analyser)</div>
    <table class="tbl-module">
        <tr>
            <th>Lobby Name</th>
            <td>{{ $isBaTest ? ($ambush->lobby->name ?? '-') : 'N/A' }}</td>
            <th>Station Name</th>
            <td>{{ $isBaTest ? ($ambush->station->name ?? '-') : 'N/A' }}</td>
        </tr>
        <tr>
            <th>Staff Name & Designation</th>
            <td>{{ $isBaTest ? (($ambush->staffName->name ?? '-') . ' (' . $ambush->ba_staff_designation_name . ')') : 'N/A' }}</td>
            <th>Alcohol Test Result / %</th>
            <td>{{ $isBaTest ? (($ambush->alcohol_test ?? '-') . ($ambush->alcohol_percentage ? ' (' . $ambush->alcohol_percentage . '%)' : '')) : 'N/A' }}</td>
        </tr>
        <tr>
            <th>Any Major Deficiency</th>
            <td colspan="3">
                @if($isBaTest)
                    {{ $hasDeficiency ? 'YES - ' . ($ambush->write_deficiency ?? 'Deficiency recorded') : 'NO (Compliant)' }}
                @else
                    N/A
                @endif
            </td>
        </tr>
    </table>

    {{-- SECTION 6: NON-INTERLOCKED LC VOICE RECORDING --}}
    <div class="section-header red">5. Non-Interlocked LC Voice Recording Details</div>
    <table class="tbl-module">
        <tr>
            <th>Date of Voice Recording</th>
            <td>{{ $isLcVoice ? ($ambush->date_of_voice_recording ?? '-') : 'N/A' }}</td>
            <th>Block Section & LC Gate No.</th>
            <td>{{ $isLcVoice ? (($ambush->block_section ?? '-') . ' / LC Gate #' . ($ambush->lc_gate_no ?? '-')) : 'N/A' }}</td>
        </tr>
        <tr>
            <th>Train No.</th>
            <td>{{ $isLcVoice ? ($ambush->train_no ?? '-') : 'N/A' }}</td>
            <th>Voice Recording Times</th>
            <td>{{ $isLcVoice ? ('Train: ' . ($ambush->voice_recording_time_train ?? '-') . ' | LC: ' . ($ambush->voice_recording_time_lc ?? '-')) : 'N/A' }}</td>
        </tr>
        <tr>
            <th>Train Movement Times</th>
            <td>{{ $isLcVoice ? ('Dep: ' . ($ambush->actual_departure_time ?? '-') . ' | Arr: ' . ($ambush->actual_arrival_time ?? '-')) : 'N/A' }}</td>
            <th>Any Major Deficiency</th>
            <td>
                @if($isLcVoice)
                    {{ $hasDeficiency ? 'YES - ' . ($ambush->write_deficiency ?? 'Deficiency recorded') : 'NO (Compliant)' }}
                @else
                    N/A
                @endif
            </td>
        </tr>
    </table>

    {{-- Signatures --}}
    <div style="margin-top: 35px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; font-size: 8px; color: #718096;">
                    Verified & Audited by Saathi Safety Module<br>
                    Railway Administration Portal
                </td>
                <td style="width: 50%; text-align: right; font-size: 8px; color: #718096;">
                    ___________________________<br>
                    Inspecting Officer Signature
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        MODULE 2: Ambush Check Record Dossier &bull; Record #{{ $ambush->id }} &bull; Confidential
    </div>

</body>
</html>
