<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\AmenityCategory;
use App\Models\APIComplaint;
use App\Models\Department;
use App\Models\Station;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ComplaintsReportController extends Controller
{
    /**
     * Ensure only Admin / Owner can access Complaints Report.
     */
    protected function authorizeAdmin(): void
    {
        $user = auth()->user();
        if (!$user || !in_array(strtolower($user->type ?? ''), ['owner', 'super admin'])) {
            abort(403, 'Unauthorized. Complaints Report is restricted to Admin only.');
        }
    }

    /**
     * Build base query with filters.
     */
    protected function getFilteredQuery(Request $request)
    {
        $query = APIComplaint::with([
            'department',
            'station',
            'amenityCategory',
            'assignedEmployee.designation',
            'forwardedToDesignation',
            'controller',
            'creator',
            'rectifiedByUser',
            'reopenedByUser',
            'transferredComplaints.toDepartment',
            'transferredComplaints.fromDepartment',
            'rectificationHistories',
        ]);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('station_id')) {
            $query->where('station_id', $request->station_id);
        }

        if ($request->filled('amenity_category_id')) {
            $query->where('amenity_category_id', $request->amenity_category_id);
        }

        if ($request->has('status') && $request->status !== null && $request->status !== '' && $request->status !== 'all') {
            $query->where('status', (int) $request->status);
        }

        if ($request->has('is_safety_critical') && $request->is_safety_critical !== null && $request->is_safety_critical !== '' && $request->is_safety_critical !== 'all') {
            $query->where('is_safety_critical', (int) $request->is_safety_critical);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->filled('search')) {
            $search = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('complaint_num', 'like', $search)
                  ->orWhere('description', 'like', $search)
                  ->orWhere('rectifier_name', 'like', $search)
                  ->orWhere('rectifier_designation', 'like', $search)
                  ->orWhere('reopen_reason', 'like', $search)
                  ->orWhereHas('station', function ($sq) use ($search) {
                      $sq->where('name', 'like', $search)->orWhere('code', 'like', $search);
                  })
                  ->orWhereHas('department', function ($dq) use ($search) {
                      $dq->where('name', 'like', $search);
                  });
            });
        }

        return $query->latest('created_at');
    }

    /**
     * Display Complaints Report page.
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        // Summary KPI Counts
        $totalComplaints     = APIComplaint::count();
        $pendingComplaints   = APIComplaint::where('status', 0)->count();
        $assignedComplaints  = APIComplaint::where('status', 4)->count();
        $rectifiedComplaints = APIComplaint::where('status', 1)->count();
        $reopenedComplaints  = APIComplaint::where('status', 2)->count();
        $closedComplaints    = APIComplaint::where('status', 3)->count();
        $criticalComplaints  = APIComplaint::where('is_safety_critical', 1)->count();
        $transferredCount    = APIComplaint::where('status', 6)->count();

        // Dropdown Lists
        $departments = Department::where('status', 1)->orderBy('name')->get();
        $stations    = Station::where('status', 1)->orderBy('name')->get();
        $categories  = AmenityCategory::where('status', 1)->orderBy('name')->get();

        // Filtered records
        $complaints = $this->getFilteredQuery($request)->get();

        return view('admin.report.complaints-report.index', compact(
            'complaints',
            'departments',
            'stations',
            'categories',
            'totalComplaints',
            'pendingComplaints',
            'assignedComplaints',
            'rectifiedComplaints',
            'reopenedComplaints',
            'closedComplaints',
            'criticalComplaints',
            'transferredCount'
        ));
    }

    /**
     * Show complaint detail modal with all 3 modules.
     */
    public function show($id)
    {
        $this->authorizeAdmin();

        $complaint = APIComplaint::with([
            'department',
            'station',
            'amenityCategory',
            'assignedEmployee.designation',
            'forwardedToDesignation',
            'controller',
            'creator',
            'rectifiedByUser',
            'reopenedByUser',
            'transferredComplaints.toDepartment',
            'transferredComplaints.fromDepartment',
            'rectificationHistories',
        ])->findOrFail($id);

        return view('admin.report.complaints-report.partials.show-modal', compact('complaint'));
    }

    /**
     * Export all filtered complaints to CSV / Excel.
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $this->authorizeAdmin();

        $complaints = $this->getFilteredQuery($request)->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="station_complaints_records_' . date('Y-m-d_His') . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($complaints) {
            $handle = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // CSV Header Row (Matching Module 1, Module 2, Module 3 from Reference Document)
            fputcsv($handle, [
                '#',
                'Complaint No',
                'Status',
                // Module 1: Register Complaint
                'Complaint Date',
                'Complaint Time',
                'Station Name',
                'Station Code',
                'Department',
                'Complaint Type (Category)',
                'Issue / Description',
                'Rectification Timeline',
                'Critical / Non-Critical',
                'Forwarded To (Designation)',
                'Transferred Department',
                // Module 2: Assign, Rectify & Close
                'Assigned Date',
                'Assigned Time',
                'Assigned To (Name)',
                'Assigned To (Designation)',
                'Rectified Date',
                'Rectified Time',
                'Rectifier Name',
                'Rectifier Designation',
                'Rectification Remark',
                'Closed Date',
                'Closed Time',
                // Module 3: Re-open, Re-rectify & Close
                'Reopened Date',
                'Reopened Time',
                'Reopen Reason',
                'Re-Rectified Date',
                'Re-Rectified Time',
                'Feedback Description',
                'Feedback Rating',
            ]);

            $statusLabels = [
                0 => 'Pending / Open',
                1 => 'Rectified',
                2 => 'Reopened',
                3 => 'Closed',
                4 => 'Assigned',
                5 => 'Reassigned',
                6 => 'Transferred',
            ];

            foreach ($complaints as $idx => $c) {
                // Latest transfer department if any
                $transferDept = '-';
                if ($c->transferredComplaints && $c->transferredComplaints->count() > 0) {
                    $firstTransfer = $c->transferredComplaints->first();
                    $transferDept = $firstTransfer->toDepartment->name ?? '-';
                }

                // Dates & Times
                $regDate = $c->created_at ? $c->created_at->format('d-m-Y') : '-';
                $regTime = $c->created_at ? $c->created_at->format('h:i A') : '-';

                $assignDate = $c->assigned_employee_at ? Carbon::parse($c->assigned_employee_at)->format('d-m-Y') : '-';
                $assignTime = $c->assigned_employee_at ? Carbon::parse($c->assigned_employee_at)->format('h:i A') : '-';

                $rectDate = $c->rectified_at ? Carbon::parse($c->rectified_at)->format('d-m-Y') : ($c->rectification_date ? Carbon::parse($c->rectification_date)->format('d-m-Y') : '-');
                $rectTime = $c->rectified_at ? Carbon::parse($c->rectified_at)->format('h:i A') : '-';

                $closedDate = ($c->status == 3 && $c->feedback_given_at) ? Carbon::parse($c->feedback_given_at)->format('d-m-Y') : ($c->status == 3 ? Carbon::parse($c->updated_at)->format('d-m-Y') : '-');
                $closedTime = ($c->status == 3 && $c->feedback_given_at) ? Carbon::parse($c->feedback_given_at)->format('h:i A') : ($c->status == 3 ? Carbon::parse($c->updated_at)->format('h:i A') : '-');

                $reopenDate = $c->reopened_at ? Carbon::parse($c->reopened_at)->format('d-m-Y') : '-';
                $reopenTime = $c->reopened_at ? Carbon::parse($c->reopened_at)->format('h:i A') : '-';

                fputcsv($handle, [
                    $idx + 1,
                    $c->complaint_num,
                    $statusLabels[$c->status] ?? 'Unknown',
                    // Module 1
                    $regDate,
                    $regTime,
                    $c->station->name ?? '-',
                    $c->station->code ?? '-',
                    $c->department->name ?? '-',
                    $c->amenityCategory->name ?? '-',
                    $c->description ?? '-',
                    $c->timeline ? ($c->timeline . ' Hours') : '-',
                    $c->is_safety_critical ? 'Critical' : 'Non-Critical',
                    $c->forwardedToDesignation->name ?? ($c->assigned_to ?? '-'),
                    $transferDept,
                    // Module 2
                    $assignDate,
                    $assignTime,
                    $c->assignedEmployee->name ?? '-',
                    $c->assignedEmployee->designation->name ?? '-',
                    $rectDate,
                    $rectTime,
                    $c->rectifier_name ?? '-',
                    $c->rectifier_designation ?? '-',
                    $c->rectification_remark ?? '-',
                    $closedDate,
                    $closedTime,
                    // Module 3
                    $reopenDate,
                    $reopenTime,
                    $c->reopen_reason ?? '-',
                    ($c->reopened_at && $c->rectified_at && $c->rectified_at > $c->reopened_at) ? Carbon::parse($c->rectified_at)->format('d-m-Y') : '-',
                    ($c->reopened_at && $c->rectified_at && $c->rectified_at > $c->reopened_at) ? Carbon::parse($c->rectified_at)->format('h:i A') : '-',
                    $c->feedback_description ?? '-',
                    $c->feedback_rating ? ($c->feedback_rating . ' / 5') : '-',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export all filtered complaints to Landscape PDF.
     */
    public function exportPdf(Request $request)
    {
        $this->authorizeAdmin();

        $complaints = $this->getFilteredQuery($request)->get();
        $generatedAt = Carbon::now()->format('d M Y, h:i A');

        $pdf = Pdf::loadView('admin.report.complaints-report.pdf.comprehensive-report', compact('complaints', 'generatedAt'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'sans-serif',
            ]);

        return $pdf->download('Station_Complaints_Comprehensive_Report_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export single complaint dossier PDF.
     */
    public function exportComplaintPdf($id)
    {
        $this->authorizeAdmin();

        $complaint = APIComplaint::with([
            'department',
            'station',
            'amenityCategory',
            'assignedEmployee.designation',
            'forwardedToDesignation',
            'controller',
            'creator',
            'rectifiedByUser',
            'reopenedByUser',
            'transferredComplaints.toDepartment',
            'transferredComplaints.fromDepartment',
            'rectificationHistories',
        ])->findOrFail($id);

        $generatedAt = Carbon::now()->format('d M Y, h:i A');

        $pdf = Pdf::loadView('admin.report.complaints-report.pdf.single-complaint-dossier', compact('complaint', 'generatedAt'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'sans-serif',
            ]);

        return $pdf->download('Complaint_Dossier_' . $complaint->complaint_num . '.pdf');
    }
}

