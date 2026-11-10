<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\SelfNomination;
use App\Models\Station;
use App\Models\AreaOfProficiency;
use App\Models\Designation;
use App\Models\MasterEmployee;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AwardRecordReportController extends Controller
{
    /**
     * Authorize user access to Award Record Report.
     */
    protected function authorizeAccess(): void
    {
        $user = auth()->user();
        if (!$user) {
            abort(403, 'Unauthorized.');
        }

        $type = strtolower($user->type ?? '');
        if (!in_array($type, ['owner', 'super admin', 'admin', 'department admin'])) {
            abort(403, 'Unauthorized. Award Record Report is restricted to Administrators.');
        }
    }

    /**
     * Build the filtered query for award nominations.
     */
    protected function getFilteredQuery(Request $request)
    {
        $query = SelfNomination::with([
            'staff.department',
            'designation',
            'station',
            'proficiency',
            'proficiency_cert',
            'ti',
            'srdom'
        ]);

        // 1. Filter by Status (0: Pending, 1: Recommended, 2: Awarded, 3: Rejected)
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', (int) $request->status);
        }

        // 2. Filter by Station
        if ($request->filled('station_id') && $request->station_id !== 'all') {
            $query->where('station_id', (int) $request->station_id);
        }

        // 3. Filter by Area of Proficiency
        if ($request->filled('proficiency_id') && $request->proficiency_id !== 'all') {
            $query->where('proficiency_id', (int) $request->proficiency_id);
        }

        // 4. Filter by Date Range (checked_date or nomination date)
        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        // 5. Keyword Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('certificate_no', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('dom_comment', 'like', "%{$search}%")
                  ->orWhere('ti_comment', 'like', "%{$search}%")
                  ->orWhereHas('station', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('designation', function ($dq) use ($search) {
                      $dq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('proficiency', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        return $query->orderBy('date', 'desc')->orderBy('time', 'desc');
    }

    /**
     * Display Award Record Report Index with KPI summary & filters.
     */
    public function index(Request $request)
    {
        $this->authorizeAccess();

        $query = $this->getFilteredQuery($request);
        $records = $query->get();

        // 4 KPI Stats
        $stats = [
            'total'       => SelfNomination::count(),
            'awarded'     => SelfNomination::where('status', 2)->count(),
            'rejected'    => SelfNomination::where('status', 3)->count(),
            'pending'     => SelfNomination::whereIn('status', [0, 1])->count(),
        ];

        // Filter Options
        $stations = Station::where('status', 1)->orderBy('name')->get();
        $proficiencies = AreaOfProficiency::where('status', 1)->orderBy('name')->get();

        return view('admin.report.award-record-report.index', compact(
            'records',
            'stats',
            'stations',
            'proficiencies'
        ));
    }

    /**
     * Return modal content (Dossier) for an Award Nomination record.
     */
    public function show($id)
    {
        $this->authorizeAccess();

        $record = SelfNomination::with([
            'staff.department',
            'designation',
            'station',
            'proficiency',
            'proficiency_cert',
            'ti',
            'srdom'
        ])->findOrFail($id);

        return view('admin.report.award-record-report.partials.show-modal', compact('record'));
    }

    /**
     * View/Print Award Certificate.
     */
    public function certificate($id)
    {
        $this->authorizeAccess();

        $nomination = SelfNomination::with([
            'staff.department',
            'designation',
            'station',
            'proficiency_cert',
            'proficiency',
            'srdom'
        ])->findOrFail($id);

        return view('admin.authority.award-nomination.certificate', compact('nomination'));
    }

    /**
     * Export single nomination dossier as PDF.
     */
    public function exportNominationPdf($id)
    {
        $this->authorizeAccess();

        $record = SelfNomination::with([
            'staff.department',
            'designation',
            'station',
            'proficiency',
            'proficiency_cert',
            'ti',
            'srdom'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('admin.report.award-record-report.pdf.single-award-dossier', compact('record'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'sans-serif',
            ]);

        $fileName = 'Award_Nomination_Dossier_' . ($record->certificate_no ? str_replace('/', '_', $record->certificate_no) : 'REC_' . $record->id) . '_' . date('Ymd_His') . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Export filtered award records to Excel (CSV format).
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $this->authorizeAccess();

        $records = $this->getFilteredQuery($request)->get();
        $fileName = 'Award_Record_Report_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($records) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            // CSV Header Row matching Columns
            fputcsv($handle, [
                '#',
                'Nomination Date',
                'Nomination Time',
                'Nominee Name',
                'Nominee Designation',
                'Station',
                'Area of Proficiency',
                'Description',
                'Uploaded File',
                'Status',
                'Awarded Date',
                'Rejected Date',
                'Certificate No',
                'Sr. DOM Remarks',
                'TI Remarks',
            ]);

            $index = 1;
            foreach ($records as $r) {
                $statusText = match ((int)$r->status) {
                    0 => 'Pending (TI)',
                    1 => 'Recommended by TI',
                    2 => 'Awarded',
                    3 => 'Rejected',
                    default => 'Unknown'
                };

                $nomDate = $r->date ? Carbon::parse($r->date)->format('d-m-Y') : 'N/A';
                $nomTime = $r->time ? Carbon::parse($r->time)->format('h:i A') : 'N/A';
                $awardedDate = ($r->status == 2 && $r->checked_date) ? Carbon::parse($r->checked_date)->format('d-m-Y') : '-';
                $rejectedDate = ($r->status == 3 && $r->checked_date) ? Carbon::parse($r->checked_date)->format('d-m-Y') : '-';

                $fileStatus = $r->file ? 'Yes (' . ($r->file_type ?: 'Attachment') . ')' : 'None';

                fputcsv($handle, [
                    $index++,
                    $nomDate,
                    $nomTime,
                    $r->name ?: ($r->staff->name ?? 'N/A'),
                    $r->designation->name ?? ($r->staff->designation->name ?? 'N/A'),
                    $r->station->name ?? ($r->station->code ?? 'N/A'),
                    $r->proficiency->name ?? 'N/A',
                    $r->description ?? '',
                    $fileStatus,
                    $statusText,
                    $awardedDate,
                    $rejectedDate,
                    $r->certificate_no ?? '-',
                    $r->dom_comment ?? '',
                    $r->ti_comment ?? '',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export filtered award records to Comprehensive PDF Report.
     */
    public function exportPdf(Request $request)
    {
        $this->authorizeAccess();

        $records = $this->getFilteredQuery($request)->get();

        $stats = [
            'total'       => $records->count(),
            'awarded'     => $records->where('status', 2)->count(),
            'rejected'    => $records->where('status', 3)->count(),
            'pending'     => $records->whereIn('status', [0, 1])->count(),
        ];

        $pdf = Pdf::loadView('admin.report.award-record-report.pdf.comprehensive-report', [
            'records' => $records,
            'stats'   => $stats,
            'filters' => $request->all(),
        ])->setPaper('a4', 'landscape')
          ->setOptions([
              'isHtml5ParserEnabled' => true,
              'isRemoteEnabled'      => true,
              'defaultFont'          => 'sans-serif',
          ]);

        $fileName = 'Award_Record_Report_' . date('Ymd_His') . '.pdf';
        return $pdf->download($fileName);
    }
}
