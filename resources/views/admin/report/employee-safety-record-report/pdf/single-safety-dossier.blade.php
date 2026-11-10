<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Employee Safety Record Dossier</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 10px;
            line-height: 1.4;
            margin: 0;
            padding: 15px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1e3c72;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .railway-title {
            font-size: 15px;
            font-weight: bold;
            color: #1e3c72;
            text-transform: uppercase;
        }
        .division-title {
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            margin-top: 2px;
        }
        .report-badge {
            background-color: #1e3c72;
            color: #ffffff;
            padding: 3px 10px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 4px;
            display: inline-block;
            margin-top: 4px;
        }
        .section-title {
            background-color: #1e3c72;
            color: #ffffff;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            margin-top: 10px;
            margin-bottom: 0px;
        }
        table.info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        table.info-table td, table.info-table th {
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            vertical-align: top;
        }
        .lbl {
            font-weight: bold;
            color: #475569;
            width: 25%;
            background-color: #f8fafc;
        }
        .val {
            color: #0f172a;
            width: 25%;
        }
        .footer {
            margin-top: 15px;
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
        <div class="report-badge">MODULE 6: EMPLOYEE SAFETY DOSSIER &bull; {{ strtoupper($type) }}</div>
    </div>

    @php
        $staffName = $employee ? $employee->name : 'N/A';
        $desigName = $employee && $employee->designation ? $employee->designation->name : 'N/A';
        $st = $employee && $employee->stations->isNotEmpty() ? $employee->stations->first() : null;
        $stName = $st ? ($st->name . ' (' . $st->code . ')') : '-';
        $usmid = $employee ? $employee->usmid : 'N/A';
        $phone = $employee ? $employee->mobile : '-';
        $dept = $employee && $employee->department ? $employee->department->name : 'Operating Department';
    @endphp

    {{-- SECTION 1: EMPLOYEE PARTICULARS --}}
    <div class="section-title">1. Employee Particulars</div>
    <table class="info-table">
        <tr>
            <td class="lbl">Staff Full Name:</td>
            <td class="val"><strong>{{ $staffName }}</strong></td>
            <td class="lbl">USMID / Staff ID:</td>
            <td class="val"><strong>{{ $usmid }}</strong></td>
        </tr>
        <tr>
            <td class="lbl">Designation:</td>
            <td class="val">{{ $desigName }}</td>
            <td class="lbl">Department:</td>
            <td class="val">{{ $dept }}</td>
        </tr>
        <tr>
            <td class="lbl">Station / Posting:</td>
            <td class="val">{{ $stName }}</td>
            <td class="lbl">Mobile Number:</td>
            <td class="val">{{ $phone }}</td>
        </tr>
    </table>

    {{-- SECTION 2: CURRENT SAFETY RECORD --}}
    <div class="section-title">2. Safety Record Details ({{ strtoupper($type) }})</div>
    <table class="info-table">
        @if(strtolower($type) === 'medical')
            <tr>
                <td class="lbl">Last PME Date:</td>
                <td class="val">{{ $record->last_pme_date ? \Carbon\Carbon::parse($record->last_pme_date)->format('d M, Y') : '-' }}</td>
                <td class="lbl">Next Due Date:</td>
                <td class="val"><strong>{{ $record->next_due_date ? \Carbon\Carbon::parse($record->next_due_date)->format('d M, Y') : '-' }}</strong></td>
            </tr>
            <tr>
                <td class="lbl">Certificate Number:</td>
                <td class="val" colspan="3"><strong>{{ $record->certificate_no ?: '-' }}</strong></td>
            </tr>
        @elseif(strtolower($type) === 'refresher')
            <tr>
                <td class="lbl">Last Refresher Date:</td>
                <td class="val">{{ $record->last_refresher_date ? \Carbon\Carbon::parse($record->last_refresher_date)->format('d M, Y') : '-' }}</td>
                <td class="lbl">Next Due Date:</td>
                <td class="val"><strong>{{ $record->next_due_date ? \Carbon\Carbon::parse($record->next_due_date)->format('d M, Y') : '-' }}</strong></td>
            </tr>
            <tr>
                <td class="lbl">Certificate Number:</td>
                <td class="val"><strong>{{ $record->certificate_no ?: '-' }}</strong></td>
                <td class="lbl">Training Institution:</td>
                <td class="val">{{ $record->institution_name ?: '-' }}</td>
            </tr>
        @elseif(strtolower($type) === 'gradation')
            <tr>
                <td class="lbl">Gradation Date:</td>
                <td class="val">{{ $record->gradation_date ? \Carbon\Carbon::parse($record->gradation_date)->format('d M, Y') : '-' }}</td>
                <td class="lbl">Awarded Grade:</td>
                <td class="val"><strong>Grade {{ $record->grade ?: '-' }}</strong></td>
            </tr>
            <tr>
                <td class="lbl">Total Marks:</td>
                <td class="val" colspan="3"><strong>{{ $record->total_marks !== null ? number_format($record->total_marks, 2) : '-' }} / 100</strong></td>
            </tr>
        @endif
    </table>

    {{-- SECTION 3: SAFETY HISTORY SUMMARY --}}
    <div class="section-title">3. Safety History Summary</div>
    <table class="info-table" style="font-size: 8.5px;">
        <thead>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <th>Category</th>
                <th>Last Exam / Check Date</th>
                <th>Next Due Date</th>
                <th>Certificate / Score</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Medical (PME)</strong></td>
                <td>{{ $pmeHistory->first() && $pmeHistory->first()->last_pme_date ? \Carbon\Carbon::parse($pmeHistory->first()->last_pme_date)->format('d M, Y') : 'No record' }}</td>
                <td><strong>{{ $pmeHistory->first() && $pmeHistory->first()->next_due_date ? \Carbon\Carbon::parse($pmeHistory->first()->next_due_date)->format('d M, Y') : '-' }}</strong></td>
                <td>{{ $pmeHistory->first() ? $pmeHistory->first()->certificate_no : '-' }}</td>
            </tr>
            <tr>
                <td><strong>Refresher Course</strong></td>
                <td>{{ $refHistory->first() && $refHistory->first()->last_refresher_date ? \Carbon\Carbon::parse($refHistory->first()->last_refresher_date)->format('d M, Y') : 'No record' }}</td>
                <td><strong>{{ $refHistory->first() && $refHistory->first()->next_due_date ? \Carbon\Carbon::parse($refHistory->first()->next_due_date)->format('d M, Y') : '-' }}</strong></td>
                <td>{{ $refHistory->first() ? $refHistory->first()->certificate_no : '-' }}</td>
            </tr>
            <tr>
                <td><strong>Staff Gradation</strong></td>
                <td>{{ $gradHistory->first() && $gradHistory->first()->gradation_date ? \Carbon\Carbon::parse($gradHistory->first()->gradation_date)->format('d M, Y') : 'No record' }}</td>
                <td>-</td>
                <td>{{ $gradHistory->first() ? ($gradHistory->first()->total_marks . ' / 100 (Grade: ' . $gradHistory->first()->grade . ')') : '-' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Generated from Saathi Portal &bull; North Eastern Railway, Izzatnagar Division &bull; {{ date('d M, Y h:i A') }}
    </div>

</body>
</html>
