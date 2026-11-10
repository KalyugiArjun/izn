<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\IdentifyNewWork;
use App\Models\Ph16Ti;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Ph16ReportController extends Controller
{
    /**
     * Ensure only Admin / Owner can access PH 16 Reports
     */
    protected function authorizeAdmin(): void
    {
        $user = auth()->user();
        if (!$user || !in_array(strtolower($user->type ?? ''), ['owner', 'super admin'])) {
            abort(403, 'Unauthorized. PH 16 Works Report is restricted to Admin only.');
        }
    }

    /**
     * Build base query with filters
     */
    protected function getFilteredQuery(Request $request)
    {
        $query = IdentifyNewWork::with([
            'ph16Ti.department',
            'ph16Ti.designation',
            'jointSurveys',
            'estimates',
            'reviewEstimates',
            'approvalListings',
            'financeVettingSanctions',
            'tenderings',
            'esps',
            'sips',
            'executions',
            'executionends',
            'variations',
            'inspectionLogs.photos'
        ]);

        if ($request->filled('ph16_ti_id')) {
            $tiId = (int) $request->ph16_ti_id;
            $query->where(function ($q) use ($tiId) {
                $q->where('ph16_ti_id', $tiId)
                  ->orWhereJsonContains('ph16_ti_ids', $tiId);
            });
        }

        if ($request->filled('department_id')) {
            $deptId = $request->department_id;
            $deptObj = Department::find($deptId);
            $deptName = $deptObj ? $deptObj->name : null;

            $query->where(function ($q) use ($deptId, $deptName) {
                // 1. TI's Department
                $q->whereHas('ph16Ti', function ($sub) use ($deptId) {
                    $sub->where('department_id', $deptId);
                });

                if ($deptName) {
                    // 2. Estimates Department
                    $q->orWhereHas('estimates', function ($sub) use ($deptName) {
                        $sub->where('department', 'like', "%{$deptName}%");
                    })
                    // 3. Inspection Logs Department Inspected
                    ->orWhereHas('inspectionLogs', function ($sub) use ($deptName) {
                        $sub->where('department_inspected', 'like', "%{$deptName}%");
                    })
                    // 4. Tendering Department
                    ->orWhereHas('tenderings', function ($sub) use ($deptName) {
                        $sub->where('department', 'like', "%{$deptName}%");
                    });
                }
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', (int) $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('date_of_identification_of_work', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('date_of_identification_of_work', '<=', $request->to_date);
        }

        if ($request->filled('search')) {
            $search = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('work_description_identification', 'like', $search)
                  ->orWhere('concern_traffic_inspector', 'like', $search);
            });
        }

        return $query->latest('date_of_identification_of_work');
    }

    /**
     * Display the main PH 16 comprehensive report page
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin();
        $query = $this->getFilteredQuery($request);
        $works = $query->get();

        // Calculate summary metrics
        $allWorks = IdentifyNewWork::with(['executions', 'executionends', 'variations', 'inspectionLogs'])->get();
        $totalWorks = $allWorks->count();
        $completedWorks = $allWorks->filter(fn ($w) => $w->executionends->count() > 0 || $w->status === 1)->count();
        $activeWorks = $totalWorks - $completedWorks;
        $totalInspections = $allWorks->sum(fn ($w) => $w->inspectionLogs->count());
        $totalVariations = $allWorks->sum(fn ($w) => $w->variations->count());

        // Filter options
        $ph16Tis = Ph16Ti::with(['department', 'designation'])
            ->get()
            ->filter(fn ($ti) => $ti->designation !== null);

        $departments = Department::orderBy('name')->get();

        return view('admin.report.ph16-report.index', compact(
            'works',
            'totalWorks',
            'activeWorks',
            'completedWorks',
            'totalInspections',
            'totalVariations',
            'ph16Tis',
            'departments'
        ));
    }

    /**
     * Show full A-to-Z details of a single PH 16 work (Modal / Drawer view)
     */
    public function show($id)
    {
        $this->authorizeAdmin();
        $work = IdentifyNewWork::with([
            'ph16Ti.department',
            'ph16Ti.designation',
            'jointSurveys',
            'estimates',
            'reviewEstimates',
            'approvalListings',
            'financeVettingSanctions',
            'tenderings',
            'esps',
            'sips',
            'executions',
            'executionends',
            'variations',
            'inspectionLogs.photos'
        ])->findOrFail($id);

        return view('admin.report.ph16-report.partials.show-modal', compact('work'));
    }

    /**
     * Export all filtered PH 16 works to a formatted Excel / CSV file
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $this->authorizeAdmin();
        $works = $this->getFilteredQuery($request)->get();
        $fileName = 'PH16_Works_Report_' . date('Y_m_d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($works) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM so Excel opens Hindi/special characters correctly
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // CSV Header Row
            fputcsv($handle, [
                'Work ID',
                'Work Description',
                'Identification Date',
                'Concerned TI / Designation',
                'Department',
                'Work Status',
                'Current Stage',
                'Progress %',
                'Joint Survey (Date | Milestone | Days)',
                'Estimates (Count & Status)',
                'Review Estimates (Date | Milestone)',
                'Approval & Listing (Date | Milestone)',
                'Finance Vetting & Sanction (Date | Milestone)',
                'Tendering (Date | Milestone | Dept)',
                'ESP (Date | Milestone)',
                'SIP (Date | Milestone)',
                'Execution Start (Date | Target Date)',
                'Execution End (Completion Date | Remarks)',
                'Variations Count',
                'Latest Variation TDC',
                'Total Inspection Logs',
                'Latest Inspection (Date | Official | Authority)'
            ]);

            foreach ($works as $w) {
                // Joint Survey
                $jsSummary = $w->jointSurveys->map(fn ($j) => 
                    ($j->date ? $j->date : 'N/A') . ' [' . ($j->milestone ?? '') . ', ' . ($j->days_taken ? $j->days_taken . 'd' : '') . ']'
                )->implode('; ');

                // Estimates
                $estSummary = $w->estimates->map(fn ($e) => 
                    ($e->department ?? '') . ' (Called: ' . ($e->estimate_called ?? 'N/A') . ', Recv: ' . ($e->estimate_received ?? 'N/A') . ')'
                )->implode('; ');

                // Review Estimates
                $reSummary = $w->reviewEstimates->map(fn ($r) => 
                    ($r->review_estimate_date ?? 'N/A') . ' [' . ($r->milestone ?? '') . ']'
                )->implode('; ');

                // Approval & Listing
                $appSummary = $w->approvalListings->map(fn ($a) => 
                    ($a->date ?? 'N/A') . ' [' . ($a->milestone ?? '') . ']'
                )->implode('; ');

                // Finance Vetting
                $fvSummary = $w->financeVettingSanctions->map(fn ($f) => 
                    ($f->date ?? 'N/A') . ' [' . ($f->milestone ?? '') . ']'
                )->implode('; ');

                // Tendering
                $tndSummary = $w->tenderings->map(fn ($t) => 
                    ($t->date ?? 'N/A') . ' [' . ($t->milestone ?? '') . ' - ' . ($t->department ?? '') . ']'
                )->implode('; ');

                // ESP
                $espSummary = $w->esps->map(fn ($e) => 
                    ($e->date ?? 'N/A') . ' [' . ($e->milestone ?? '') . ']'
                )->implode('; ');

                // SIP
                $sipSummary = $w->sips->map(fn ($s) => 
                    ($s->date ?? 'N/A') . ' [' . ($s->milestone ?? '') . ']'
                )->implode('; ');

                // Execution Start
                $execStart = $w->executions->first();
                $execStartStr = $execStart 
                    ? ($execStart->date ?? 'N/A') . ' (Target: ' . ($execStart->work_completion_date ?? 'N/A') . ')' 
                    : 'Not Started';

                // Execution End
                $execEnd = $w->executionends->first();
                $execEndStr = $execEnd 
                    ? ($execEnd->execution_completion_date ?? 'N/A') . ' - ' . ($execEnd->remarks ?? '') 
                    : 'Pending';

                // Variations
                $varCount = $w->variations->count();
                $latestVar = $w->variations->last();
                $latestTdc = $latestVar ? ($latestVar->revised_tdc ?? $latestVar->date_of_variation ?? 'N/A') : 'N/A';

                // Inspection Logs
                $inspCount = $w->inspectionLogs->count();
                $latestInsp = $w->inspectionLogs->last();
                $latestInspStr = $latestInsp 
                    ? ($latestInsp->date_of_inspection ?? 'N/A') . ' by ' . ($latestInsp->name_of_inspecting_official ?? 'Official') . ' (' . ($latestInsp->inspecting_authority ?? 'Authority') . ')' 
                    : 'None';

                $tiName = $w->ph16Ti && $w->ph16Ti->designation 
                    ? $w->ph16Ti->designation->name 
                    : ($w->concern_traffic_inspector ?? 'N/A');

                $deptName = $w->ph16Ti && $w->ph16Ti->department 
                    ? $w->ph16Ti->department->name 
                    : 'N/A';

                fputcsv($handle, [
                    '#2026-' . str_pad($w->id, 2, '0', STR_PAD_LEFT),
                    $w->work_description_identification,
                    $w->date_of_identification_of_work ? $w->date_of_identification_of_work->format('d/m/Y') : 'N/A',
                    $tiName,
                    $deptName,
                    $w->status === 1 ? 'Completed' : 'In Progress',
                    $w->current_stage,
                    $w->progress_percent . '%',
                    $jsSummary ?: 'Pending',
                    $estSummary ?: 'Pending',
                    $reSummary ?: 'Pending',
                    $appSummary ?: 'Pending',
                    $fvSummary ?: 'Pending',
                    $tndSummary ?: 'Pending',
                    $espSummary ?: 'Pending',
                    $sipSummary ?: 'Pending',
                    $execStartStr,
                    $execEndStr,
                    $varCount,
                    $latestTdc,
                    $inspCount,
                    $latestInspStr
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export all filtered PH 16 works to a comprehensive Landscape PDF
     */
    public function exportPdf(Request $request)
    {
        $this->authorizeAdmin();
        $works = $this->getFilteredQuery($request)->get();
        $generatedAt = now()->format('d/m/Y h:i A');

        $pdf = Pdf::loadView('admin.report.ph16-report.pdf.comprehensive-report', compact('works', 'generatedAt'))
            ->setPaper('a4', 'landscape')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);

        return $pdf->download('PH16_Works_Comprehensive_Report_' . date('Y_m_d_His') . '.pdf');
    }

    /**
     * Export a complete single Work Dossier to PDF
     */
    public function exportWorkPdf($id)
    {
        $this->authorizeAdmin();
        $work = IdentifyNewWork::with([
            'ph16Ti.department',
            'ph16Ti.designation',
            'jointSurveys',
            'estimates',
            'reviewEstimates',
            'approvalListings',
            'financeVettingSanctions',
            'tenderings',
            'esps',
            'sips',
            'executions',
            'executionends',
            'variations',
            'inspectionLogs.photos'
        ])->findOrFail($id);

        $generatedAt = now()->format('d/m/Y h:i A');

        $pdf = Pdf::loadView('admin.report.ph16-report.pdf.single-work-dossier', compact('work', 'generatedAt'))
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true);

        return $pdf->download('PH16_Work_Dossier_' . $work->id . '_' . date('Y_m_d') . '.pdf');
    }
}

