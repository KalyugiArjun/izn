<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Complaint Dossier - {{ $complaint->complaint_num }}</title>
    <style>
        @page {
            margin: 15mm 12mm 15mm 12mm;
            size: a4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9.5px;
            color: #2d3748;
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #1e3c72;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header-title {
            font-size: 15px;
            font-weight: bold;
            color: #1e3c72;
            text-transform: uppercase;
        }
        .header-subtitle {
            font-size: 10px;
            color: #4a5568;
            font-weight: bold;
        }
        .meta-text {
            font-size: 8.5px;
            color: #718096;
            text-align: right;
        }
        .banner-box {
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            padding: 10px 12px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .banner-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e3c72;
            margin-bottom: 4px;
        }
        .section-header {
            background-color: #1e3c72;
            color: #ffffff;
            font-size: 10.5px;
            font-weight: bold;
            padding: 6px 10px;
            margin-top: 14px;
            margin-bottom: 0;
            text-transform: uppercase;
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
        }
        .section-header.m2 { background-color: #0d6efd; }
        .section-header.m3 { background-color: #2b3a4a; }
        table.tbl-module {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        table.tbl-module th {
            width: 25%;
            background-color: #edf2f7;
            font-size: 9px;
            font-weight: bold;
            padding: 6px 8px;
            border: 1px solid #cbd5e0;
            text-align: left;
            color: #4a5568;
        }
        table.tbl-module td {
            width: 25%;
            font-size: 9px;
            padding: 6px 8px;
            border: 1px solid #cbd5e0;
            vertical-align: top;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 8px;
            font-weight: bold;
            border-radius: 3px;
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
            height: 20px;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
            font-size: 8px;
            color: #a0aec0;
            text-align: center;
        }
    </style>
</head>
<body>

    @php
        $regDate = $complaint->created_at ? $complaint->created_at->format('d M, Y') : '-';
        $regTime = $complaint->created_at ? $complaint->created_at->format('h:i A') : '-';

        $assignDate = $complaint->assigned_employee_at ? \Carbon\Carbon::parse($complaint->assigned_employee_at)->format('d M, Y') : '-';
        $assignTime = $complaint->assigned_employee_at ? \Carbon\Carbon::parse($complaint->assigned_employee_at)->format('h:i A') : '-';

        $rectDate = $complaint->rectified_at ? \Carbon\Carbon::parse($complaint->rectified_at)->format('d M, Y') : ($complaint->rectification_date ? \Carbon\Carbon::parse($complaint->rectification_date)->format('d M, Y') : '-');
        $rectTime = $complaint->rectified_at ? \Carbon\Carbon::parse($complaint->rectified_at)->format('h:i A') : '-';

        $isClosed = ($complaint->status == 3);
        $closedDate = ($isClosed && $complaint->feedback_given_at) ? \Carbon\Carbon::parse($complaint->feedback_given_at)->format('d M, Y') : ($isClosed ? \Carbon\Carbon::parse($complaint->updated_at)->format('d M, Y') : '-');
        $closedTime = ($isClosed && $complaint->feedback_given_at) ? \Carbon\Carbon::parse($complaint->feedback_given_at)->format('h:i A') : ($isClosed ? \Carbon\Carbon::parse($complaint->updated_at)->format('h:i A') : '-');

        $isReopened = ($complaint->status == 2) || !empty($complaint->reopened_at) || !empty($complaint->reopen_reason);
        $reopenDate = $complaint->reopened_at ? \Carbon\Carbon::parse($complaint->reopened_at)->format('d M, Y') : '-';
        $reopenTime = $complaint->reopened_at ? \Carbon\Carbon::parse($complaint->reopened_at)->format('h:i A') : '-';

        $isReRectified = $complaint->reopened_at && $complaint->rectified_at && ($complaint->rectified_at > $complaint->reopened_at);
        $reRectDate = $isReRectified ? \Carbon\Carbon::parse($complaint->rectified_at)->format('d M, Y') : '-';
        $reRectTime = $isReRectified ? \Carbon\Carbon::parse($complaint->rectified_at)->format('h:i A') : '-';

        $transfer = $complaint->transferredComplaints ? $complaint->transferredComplaints->first() : null;
    @endphp

    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td style="width: 70%;">
                <div class="header-title">Station Complaints Records &bull; Official Dossier</div>
                <div class="header-subtitle">Saathi App &bull; Complaint Dossier Report</div>
            </td>
            <td class="meta-text" style="width: 30%;">
                <div><strong>Generated:</strong> {{ $generatedAt }}</div>
                <div><strong>Status:</strong>
                    @if($complaint->status == 0) Pending
                    @elseif($complaint->status == 1) Rectified
                    @elseif($complaint->status == 2) Reopened
                    @elseif($complaint->status == 3) Closed
                    @elseif($complaint->status == 4) Assigned
                    @elseif($complaint->status == 6) Transferred
                    @else {{ $complaint->status }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- Banner Box --}}
    <div class="banner-box">
        <table style="width: 100%;">
            <tr>
                <td>
                    <div class="banner-title">{{ $complaint->complaint_num }}</div>
                    <div style="font-size: 10px; color: #4a5568;">
                        <strong>Station:</strong> {{ $complaint->station->name ?? 'N/A' }} ({{ $complaint->station->code ?? '-' }}) &bull;
                        <strong>Department:</strong> {{ $complaint->department->name ?? 'N/A' }} &bull;
                        <strong>Category:</strong> {{ $complaint->amenityCategory->name ?? 'General' }}
                    </div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    @if($complaint->is_safety_critical)
                        <span class="badge badge-critical" style="font-size: 9px; padding: 3px 8px;">SAFETY CRITICAL</span>
                    @else
                        <span class="badge" style="background:#e2e8f0; color:#4a5568; font-size: 9px; padding: 3px 8px;">Non-Critical</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- MODULE 1 --}}
    <div class="section-header">Module 1: Register Complaint</div>
    <table class="tbl-module">
        <tr>
            <th>Complaint Date & Time</th>
            <td>{{ $regDate }} {{ $regTime }}</td>
            <th>Station (Name & Code)</th>
            <td>{{ $complaint->station->name ?? '-' }} ({{ $complaint->station->code ?? '-' }})</td>
        </tr>
        <tr>
            <th>Department</th>
            <td>{{ $complaint->department->name ?? '-' }}</td>
            <th>Complaint Type (Category)</th>
            <td>{{ $complaint->amenityCategory->name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Rectification Timeline</th>
            <td>{{ $complaint->timeline ? ($complaint->timeline . ' Hours') : '-' }}</td>
            <th>Safety Critical / Classification</th>
            <td>{{ $complaint->is_safety_critical ? 'Critical (Safety Critical)' : 'Non-Critical' }}</td>
        </tr>
        <tr>
            <th>Forwarded To (Designation)</th>
            <td>{{ $complaint->forwardedToDesignation->name ?? ($complaint->assigned_to ?? '-') }}</td>
            <th>Transferred Department</th>
            <td>{{ $transfer && $transfer->toDepartment ? $transfer->toDepartment->name : 'None' }}</td>
        </tr>
        <tr>
            <th>Issue / Description</th>
            <td colspan="3">{{ $complaint->description ?? '-' }}</td>
        </tr>
    </table>

    {{-- MODULE 2 --}}
    <div class="section-header m2">Module 2: Assign, Rectify and Close</div>
    <table class="tbl-module">
        <tr>
            <th>Complaint Assigned (Date & Time)</th>
            <td>{{ $assignDate }} {{ $assignTime }}</td>
            <th>Assigned To (Name & Designation)</th>
            <td>
                {{ $complaint->assignedEmployee->name ?? '-' }}
                @if($complaint->assignedEmployee && $complaint->assignedEmployee->designation)
                    ({{ $complaint->assignedEmployee->designation->name }})
                @endif
            </td>
        </tr>
        <tr>
            <th>Complaint Rectified (Date & Time)</th>
            <td>{{ $rectDate }} {{ $rectTime }}</td>
            <th>Rectifier Personnel</th>
            <td>{{ $complaint->rectifier_name ?? '-' }} {{ $complaint->rectifier_designation ? ('(' . $complaint->rectifier_designation . ')') : '' }}</td>
        </tr>
        <tr>
            <th>Rectification Remark</th>
            <td colspan="3">{{ $complaint->rectification_remark ?? '-' }}</td>
        </tr>
        <tr>
            <th>Complaint Closed (Date & Time)</th>
            <td colspan="3">{{ $closedDate }} {{ $closedTime }}</td>
        </tr>
    </table>

    {{-- MODULE 3 --}}
    <div class="section-header m3">Module 3: Re-open, Re-rectify and Close</div>
    <table class="tbl-module">
        <tr>
            <th>Complaint Re-open (Date & Time)</th>
            <td>{{ $reopenDate }} {{ $reopenTime }}</td>
            <th>Reopened By</th>
            <td>{{ $complaint->reopenedByUser->name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Re-Remark (Reopen Reason)</th>
            <td colspan="3" style="color: #c53030;">{{ $complaint->reopen_reason ?? 'None' }}</td>
        </tr>
        <tr>
            <th>Complaint Re-rectified (Date & Time)</th>
            <td>{{ $reRectDate }} {{ $reRectTime }}</td>
            <th>Final Closed (Date & Time)</th>
            <td>{{ $closedDate }} {{ $closedTime }}</td>
        </tr>
        <tr>
            <th>Feedback Rating & Remark</th>
            <td colspan="3">
                @if($complaint->feedback_rating)
                    <strong>Rating:</strong> {{ $complaint->feedback_rating }} / 5 &bull;
                @endif
                {{ $complaint->feedback_description ?? 'No feedback recorded.' }}
            </td>
        </tr>
    </table>

    <div style="margin-top: 40px;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; font-size: 8.5px; color: #718096;">
                    Verified & Audited by Saathi Complaint System<br>
                    Railway Administration Portal
                </td>
                <td style="width: 50%; text-align: right; font-size: 8.5px; color: #718096;">
                    ___________________________<br>
                    Authorized Officer Signature
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Station Complaints Records Dossier &bull; {{ $complaint->complaint_num }} &bull; Confidential
    </div>

</body>
</html>

