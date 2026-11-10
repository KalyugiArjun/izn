<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ambush Check Records - Comprehensive Report</title>
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
        table.data-table th.shunting { background-color: #c53030; border-color: #c53030; }
        table.data-table th.loadstable { background-color: #2b6cb0; border-color: #2b6cb0; }
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
        .badge-info { background-color: #bee3f8; color: #2a4365; }
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

    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td style="width: 70%;">
                <div class="header-title">MODULE 2: AMBUSH CHECK RECORD - Comprehensive Report</div>
                <div class="header-subtitle">Saathi App &bull; Safety Ambush Inspection Records (Shunting, Load Stable, Eng Block, BA Test, LC Voice)</div>
            </td>
            <td class="meta-text" style="width: 30%;">
                <div><strong>Generated:</strong> {{ $generatedAt }}</div>
                <div><strong>Total Records:</strong> {{ $records->count() }}</div>
            </td>
        </tr>
    </table>

    {{-- Summary KPIs --}}
    <table class="summary-box">
        <tr>
            <td>
                <div class="num">{{ $records->count() }}</div>
                <div class="lbl">Total Filtered</div>
            </td>
            <td>
                <div class="num">{{ $records->where('type_of_ambush_id', 1)->count() }}</div>
                <div class="lbl">Shunting</div>
            </td>
            <td>
                <div class="num">{{ $records->where('type_of_ambush_id', 2)->count() }}</div>
                <div class="lbl">Load Stable</div>
            </td>
            <td>
                <div class="num">{{ $records->where('type_of_ambush_id', 3)->count() }}</div>
                <div class="lbl">Eng Block</div>
            </td>
            <td>
                <div class="num">{{ $records->where('type_of_ambush_id', 5)->count() }}</div>
                <div class="lbl">BA Test</div>
            </td>
            <td>
                <div class="num">{{ $records->where('type_of_ambush_id', 4)->count() }}</div>
                <div class="lbl">LC Voice</div>
            </td>
            <td>
                <div class="num">{{ $records->where('major_deficiency', 'YES')->count() }}</div>
                <div class="lbl">Deficiencies</div>
            </td>
            <td>
                <div class="num">{{ $records->where('major_deficiency', 'NO')->count() }}</div>
                <div class="lbl">Compliant</div>
            </td>
        </tr>
    </table>

    {{-- Data Table --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20px;" class="text-center">#</th>
                <th style="width: 60px;">Date & Time</th>
                <th style="width: 90px;">Inspected By</th>
                <th style="width: 80px;">Station / Section</th>
                <th style="width: 60px;">From - To</th>
                <th style="width: 35px;" class="text-center">Shift</th>
                <th style="width: 75px;">Ambush Type</th>
                <th style="width: 220px;">Inspection Observations & Specific Details</th>
                <th style="width: 70px;" class="text-center">Deficiency</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $idx => $r)
                @php
                    $inspDate = $r->inspection && $r->inspection->inspection_date 
                        ? \Carbon\Carbon::parse($r->inspection->inspection_date)->format('d/m/Y') 
                        : ($r->created_at ? $r->created_at->format('d/m/Y') : '-');

                    $inspTime = $r->from_time ?? ($r->created_at ? $r->created_at->format('H:i') : '');

                    $officerName = $r->inspection->officer_name 
                        ?? ($r->inspection->employee->name ?? '-');

                    $officerDesig = $r->inspection->designation 
                        ?? ($r->inspection->employee->designation->name ?? '-');

                    $isShunting = stripos($r->ambushType->name ?? '', 'SHUNTING') !== false || $r->type_of_ambush_id == 1;
                    $isLoadStable = stripos($r->ambushType->name ?? '', 'LOAD STABLE') !== false || $r->type_of_ambush_id == 2;
                    $isEngBlock = stripos($r->ambushType->name ?? '', 'ENGINEERING') !== false || $r->type_of_ambush_id == 3;
                    $isLcVoice = stripos($r->ambushType->name ?? '', 'LC VOICE') !== false || stripos($r->ambushType->name ?? '', 'NON-INTERLOCKED') !== false || $r->type_of_ambush_id == 4;
                    $isBaTest = stripos($r->ambushType->name ?? '', 'BA TEST') !== false || $r->type_of_ambush_id == 5;
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>{{ $inspDate }}<br><span style="color:#718096;">{{ $inspTime }}</span></td>
                    <td><strong>{{ $officerName }}</strong><br><span style="color:#718096;">{{ $officerDesig }}</span></td>
                    <td>
                        <strong>{{ $r->station->name ?? ($r->block_section ?? '-') }}</strong>
                        @if($r->station && $r->station->code)
                            <br><span style="color:#718096;">({{ $r->station->code }})</span>
                        @elseif($r->block_section)
                            <br><span style="color:#718096;">Sec: {{ $r->block_section }}</span>
                        @endif
                    </td>
                    <td>{{ $r->from_time ?? '-' }}<br><span style="color:#718096;">to {{ $r->to_time ?? '-' }}</span></td>
                    <td class="text-center">{{ $r->day_night ?? '-' }}</td>
                    <td>
                        <span class="badge badge-info">{{ $r->ambushType->name ?? '-' }}</span>
                    </td>

                    {{-- Specific Details according to Ambush Check Type --}}
                    <td>
                        @if($isShunting)
                            <div><strong>Shunting Obs:</strong> {{ $r->shunting_observation ?? '-' }}</div>
                            <div><strong>T/806 No:</strong> {{ $r->t_806_no ?? '-' }}</div>
                            @if($r->shunting_staff_details && $r->shunting_staff_details->count() > 0)
                                <div style="color:#4a5568; margin-top: 2px;">
                                    <strong>Staff:</strong>
                                    @foreach($r->shunting_staff_details as $stf)
                                        {{ $stf['name'] }} ({{ $stf['designation'] }}){{ !$loop->last ? '; ' : '' }}
                                    @endforeach
                                </div>
                            @elseif($r->staffName)
                                <div style="color:#4a5568;"><strong>Staff:</strong> {{ $r->staffName->name }}</div>
                            @endif

                        @elseif($isLoadStable)
                            <div><strong>Load Type:</strong> {{ $r->load_type ?? '-' }} &bull; <strong>Total Loads:</strong> {{ $r->total_number_of_load ?? '0' }}</div>
                            <div><strong>Stabling Line:</strong> {{ $r->stabling_line_no ?? '-' }} &bull; <strong>Running Line:</strong> {{ $r->running_line ?? '-' }}</div>

                        @elseif($isEngBlock)
                            <div><strong>Location:</strong> {{ $r->block_section ?? ($r->station->name ?? '-') }}</div>
                            <div><strong>Block Taken By:</strong> {{ $r->block_taken_by ?? '-' }}</div>

                        @elseif($isBaTest)
                            <div><strong>Lobby:</strong> {{ $r->lobby->name ?? '-' }} &bull; <strong>Station:</strong> {{ $r->station->name ?? '-' }}</div>
                            <div><strong>Staff:</strong> {{ $r->staff_name ?? '-' }} ({{ $r->ba_staff_designation_name ?? '-' }})</div>
                            <div>
                                <strong>Alcohol Test:</strong> 
                                @if(strtoupper($r->alcohol_test) === 'POSITIVE')
                                    <span class="badge badge-danger">POSITIVE ({{ $r->alcohol_percentage ?? 0 }}%)</span>
                                @else
                                    <span class="badge badge-success">NEGATIVE</span>
                                @endif
                            </div>

                        @elseif($isLcVoice)
                            <div><strong>Recording Date:</strong> {{ $r->date_of_voice_recording ? \Carbon\Carbon::parse($r->date_of_voice_recording)->format('d/m/Y') : '-' }} &bull; <strong>Section:</strong> {{ $r->block_section ?? '-' }}</div>
                            <div><strong>LC Gate No:</strong> {{ $r->lc_gate_no ?? '-' }} &bull; <strong>Train No:</strong> {{ $r->train_no ?? '-' }}</div>
                            <div style="color:#4a5568;">
                                <strong>Train Info Time:</strong> {{ $r->voice_recording_time_train ?? '-' }} {{ $r->voice_recording_train ? '(Clip Attached)' : '' }} |
                                <strong>LC Closure Time:</strong> {{ $r->voice_recording_time_lc ?? '-' }} {{ $r->voice_recording_lc ? '(Clip Attached)' : '' }}
                            </div>
                            <div style="color:#718096;">
                                <strong>Actual Dep:</strong> {{ $r->actual_departure_time ?? '-' }} &bull; <strong>Actual Arr:</strong> {{ $r->actual_arrival_time ?? '-' }}
                            </div>

                        @else
                            <div style="color:#718096;">-</div>
                        @endif
                    </td>

                    {{-- Deficiency --}}
                    <td class="text-center">
                        @if(strtoupper($r->major_deficiency) === 'YES')
                            <span class="badge badge-danger">DEFICIENCY</span>
                            @if($r->write_deficiency)
                                <div style="color:#c53030; font-size: 6.5px; margin-top: 2px;">{{ \Illuminate\Support\Str::limit($r->write_deficiency, 35) }}</div>
                            @endif
                        @else
                            <span class="badge badge-success">COMPLIANT</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px; color: #a0aec0;">No ambush check records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        MODULE 2: Ambush Check Records Report &bull; Page printed on {{ $generatedAt }} &bull; Confidential - For Internal Railway Administration Only
    </div>

</body>
</html>

