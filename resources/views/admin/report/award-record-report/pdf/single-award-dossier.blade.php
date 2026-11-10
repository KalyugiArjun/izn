<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Award Nomination Dossier - #{{ $record->id }}</title>
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
            border-bottom: 2px solid #0b2545;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .railway-title {
            font-size: 15px;
            font-weight: bold;
            color: #0b2545;
            text-transform: uppercase;
        }
        .division-title {
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            margin-top: 2px;
        }
        .report-badge {
            background-color: #0b2545;
            color: #ffffff;
            padding: 3px 10px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 4px;
            display: inline-block;
            margin-top: 4px;
        }
        .section-title {
            background-color: #0b2545;
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
        table.info-table td {
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
        .box-desc {
            border: 1px solid #cbd5e1;
            padding: 8px;
            background-color: #ffffff;
            font-size: 9.5px;
            line-height: 1.4;
            margin-bottom: 8px;
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
        <div class="report-badge">MODULE 5: AWARD NOMINATION DOSSIER &bull; RECORD #{{ $record->id }}</div>
    </div>

    @php
        $nomDate = $record->date ? \Carbon\Carbon::parse($record->date)->format('d M, Y') : '-';
        $nomTime = $record->time ? \Carbon\Carbon::parse($record->time)->format('h:i A') : '-';
        $nomName = $record->name ?: ($record->staff->name ?? 'N/A');
        $nomDesig = $record->designation->name ?? ($record->staff->designation->name ?? 'N/A');
        $deptName = $record->staff->department->name ?? 'Operating Department';
        $stName = $record->station->name ?? ($record->station->code ?? '-');

        $statusText = match ((int)$record->status) {
            0 => 'Pending TI Review',
            1 => 'Recommended by TI',
            2 => 'Awarded & Approved',
            3 => 'Rejected',
            default => 'Unknown'
        };
    @endphp

    {{-- SECTION 1: NOMINEE INFORMATION --}}
    <div class="section-title">1. Nominee & Station Particulars</div>
    <table class="info-table">
        <tr>
            <td class="lbl">Nominee Full Name:</td>
            <td class="val"><strong>{{ $nomName }}</strong></td>
            <td class="lbl">USMID / Staff ID:</td>
            <td class="val">{{ $record->staff->usmid ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="lbl">Designation:</td>
            <td class="val">{{ $nomDesig }}</td>
            <td class="lbl">Department:</td>
            <td class="val">{{ $deptName }}</td>
        </tr>
        <tr>
            <td class="lbl">Station / Posting:</td>
            <td class="val">{{ $stName }}</td>
            <td class="lbl">Area of Proficiency:</td>
            <td class="val"><strong>{{ $record->proficiency->name ?? 'General' }}</strong></td>
        </tr>
        <tr>
            <td class="lbl">Nomination Date:</td>
            <td class="val">{{ $nomDate }}</td>
            <td class="lbl">Nomination Time:</td>
            <td class="val">{{ $nomTime }}</td>
        </tr>
    </table>

    {{-- SECTION 2: JUSTIFICATION / DESCRIPTION --}}
    <div class="section-title">2. Justification & Details of Merit</div>
    <div class="box-desc">
        {{ $record->description ?: 'No detailed description provided.' }}
    </div>

    {{-- SECTION 3: DECISION & APPROVAL WORKFLOW --}}
    <div class="section-title">3. Official Decision & Certificate Details</div>
    <table class="info-table">
        <tr>
            <td class="lbl">Current Status:</td>
            <td class="val"><strong>{{ $statusText }}</strong></td>
            <td class="lbl">Certificate Number:</td>
            <td class="val"><strong>{{ $record->certificate_no ?: 'N/A' }}</strong></td>
        </tr>
        <tr>
            <td class="lbl">Forwarded by (TI):</td>
            <td class="val">{{ $record->ti->name ?? ($record->ti_id ? 'TI #' . $record->ti_id : 'Pending') }}</td>
            <td class="lbl">TI Forward Date/Time:</td>
            <td class="val">
                {{ $record->ti_forward_date ? \Carbon\Carbon::parse($record->ti_forward_date)->format('d M, Y') : '-' }}
                {{ $record->ti_forward_time ? \Carbon\Carbon::parse($record->ti_forward_time)->format('h:i A') : '' }}
            </td>
        </tr>
        <tr>
            <td class="lbl">TI Remarks:</td>
            <td class="val" colspan="3">{{ $record->ti_comment ?: 'No remarks recorded.' }}</td>
        </tr>
        <tr>
            <td class="lbl">Decided by (Sr. DOM):</td>
            <td class="val">{{ $record->srdom->name ?? ($record->sr_dom_id ? 'Sr. DOM #' . $record->sr_dom_id : 'Pending') }}</td>
            <td class="lbl">Decision Date/Time:</td>
            <td class="val">
                {{ $record->checked_date ? \Carbon\Carbon::parse($record->checked_date)->format('d M, Y') : '-' }}
                {{ $record->checked_time ? \Carbon\Carbon::parse($record->checked_time)->format('h:i A') : '' }}
            </td>
        </tr>
        <tr>
            <td class="lbl">Sr. DOM Decision / Cash Award:</td>
            <td class="val" colspan="3"><strong>{{ $record->dom_comment ?: 'No remarks recorded.' }}</strong></td>
        </tr>
    </table>

    <div class="footer">
        Generated from Saathi Portal &bull; North Eastern Railway, Izzatnagar Division &bull; {{ date('d M, Y h:i A') }}
    </div>

</body>
</html>
