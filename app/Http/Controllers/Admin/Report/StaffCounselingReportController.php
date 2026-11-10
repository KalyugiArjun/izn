<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\StaffCounseling;
use App\Models\Station;
use App\Models\Diesellobby;
use App\Models\Designation;
use App\Models\MasterEmployee;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StaffCounselingReportController extends Controller
{
    /**
     * Authorize user access to Staff Counseling Report.
     */
    protected function authorizeAccess(): void
    {
        $user = auth()->user();
        if (!$user) {
            abort(403, 'Unauthorized.');
        }

        $type = strtolower($user->type ?? '');
        if (!in_array($type, ['owner', 'super admin', 'admin', 'department admin'])) {
            abort(403, 'Unauthorized. Staff Counseling Report is restricted to Administrators.');
        }
    }

    /**
     * Build the filtered query for staff counseling records.
     */
    protected function getFilteredQuery(Request $request)
    {
        $query = StaffCounseling::with(['station', 'dieselLobby']);

        // 1. Filter by Mode of Counseling (online / offline)
        if ($request->filled('counseling_mode') && $request->counseling_mode !== 'all') {
            $query->where('counseling_mode', strtolower($request->counseling_mode));
        }

        // 2. Filter by Counseling Location (station / diesel lobby / running train)
        if ($request->filled('counseling_location') && $request->counseling_location !== 'all') {
            $query->where('counseling_location', strtolower($request->counseling_location));
        }

        // 3. Filter by Counseling Type (Individual / Group)
        if ($request->filled('counseling_type') && $request->counseling_type !== 'all') {
            $query->where('counseling_type', $request->counseling_type);
        }

        // 4. Filter by Counselor Name
        if ($request->filled('counselor_name') && $request->counselor_name !== 'all') {
            $query->where('counselor_name', $request->counselor_name);
        }

        // 5. Filter by Station
        if ($request->filled('station_id') && $request->station_id !== 'all') {
            $query->where('station_id', $request->station_id);
        }

        // 6. Filter by Date Range
        if ($request->filled('from_date')) {
            $query->whereDate('counseling_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('counseling_date', '<=', $request->to_date);
        }

        // 7. Keyword Search (counselor, subject, train no, lc gate, or staff name)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('counselor_name', 'like', "%{$search}%")
                  ->orWhere('subject_of_counseling', 'like', "%{$search}%")
                  ->orWhere('train_no', 'like', "%{$search}%")
                  ->orWhere('lc_gate', 'like', "%{$search}%")
                  ->orWhere('calling_mode', 'like', "%{$search}%")
                  ->orWhereHas('station', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('dieselLobby', function ($lq) use ($search) {
                      $lq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        return $query->orderBy('counseling_date', 'desc')->orderBy('counseling_time', 'desc');
    }

    /**
     * Display Staff Counseling Report Index with KPI summary & filters.
     */
    public function index(Request $request)
    {
        $this->authorizeAccess();

        // Summary KPI Metrics
        $totalCounselings   = StaffCounseling::count();
        $onlineCounselings  = StaffCounseling::where('counseling_mode', 'like', '%online%')->count();
        $offlineCounselings = StaffCounseling::where('counseling_mode', 'like', '%offline%')->count();
        $groupCounselings   = StaffCounseling::where('counseling_type', 'like', '%group%')->count();

        // Distinct counselor names for filter
        $counselors = StaffCounseling::select('counselor_name')
            ->whereNotNull('counselor_name')
            ->where('counselor_name', '!=', '')
            ->distinct()
            ->orderBy('counselor_name')
            ->pluck('counselor_name');

        // Dropdown data for filter dropdowns
        $stations = Station::select('id', 'name', 'code')->orderBy('name')->get();
        $locations = ['Station', 'Diesel Lobby', 'Running Train'];

        // Get all filtered records
        $records = $this->getFilteredQuery($request)->get();

        return view('admin.report.staff-counseling-report.index', compact(
            'records',
            'counselors',
            'stations',
            'locations',
            'totalCounselings',
            'onlineCounselings',
            'offlineCounselings',
            'groupCounselings'
        ));
    }

    /**
     * Show single counseling record detail modal.
     */
    public function show($id)
    {
        $this->authorizeAccess();

        $record = StaffCounseling::with(['station', 'dieselLobby'])->findOrFail($id);

        return view('admin.report.staff-counseling-report.partials.show-modal', compact('record'));
    }

    /**
     * Export all filtered counseling records to Excel (CSV).
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $this->authorizeAccess();

        $records = $this->getFilteredQuery($request)->get();
        $filename = 'staff_counseling_records_' . date('Y-m-d_His') . '.csv';

        return response()->stream(function () use ($records) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility with Hindi characters
            fputs($handle, "\xEF\xBB\xBF");

            // CSV Header matching Module 4 Field Reference
            fputcsv($handle, [
                '#',
                'Counseling Date',
                'Counseling Time',
                'Counselor Name',
                'Counselor Designation',
                'Counseled Staff Name(s)',
                'Counseled Staff Designation(s)',
                'Mode of Counseling',
                'Calling Mode',
                'Location',
                'Station / Lobby / Section',
                'LC Gate No',
                'Train No',
                'Counseling Type',
                'Subject of Counseling',
                'GPS Coordinates',
                'Created At'
            ]);

            foreach ($records as $index => $rec) {
                $staffNames = [];
                $staffDesigs = [];
                foreach ($rec->resolved_staff_list as $st) {
                    $staffNames[] = $st['staff_name'];
                    $staffDesigs[] = $st['designation_name'];
                }

                $locDetails = '-';
                $mode = strtolower($rec->counseling_mode ?? '');
                $loc  = strtolower($rec->counseling_location ?? '');

                if ($loc === 'station' && $rec->station) {
                    $locDetails = $rec->station->name . ($rec->station->code ? ' (' . $rec->station->code . ')' : '');
                } elseif (($loc === 'diesel lobby' || $loc === 'running train') && $rec->dieselLobby) {
                    $locDetails = $rec->dieselLobby->name;
                }

                fputcsv($handle, [
                    $index + 1,
                    $rec->counseling_date ? Carbon::parse($rec->counseling_date)->format('d-m-Y') : '-',
                    $rec->counseling_time ?: '-',
                    $rec->counselor_name ?: '-',
                    $rec->counselor_designation_name,
                    implode('; ', $staffNames) ?: '-',
                    implode('; ', $staffDesigs) ?: '-',
                    strtoupper($rec->counseling_mode ?? '-'),
                    $rec->calling_mode ?: '-',
                    ucfirst($rec->counseling_location ?: ($mode === 'online' ? '—' : '-')),
                    $locDetails,
                    $rec->lc_gate ?: '-',
                    $rec->train_no ?: '-',
                    $rec->counseling_type ?: '-',
                    $rec->subject_of_counseling ?: '-',
                    $rec->gps_location ?: '-',
                    $rec->created_at ? $rec->created_at->format('d-m-Y H:i:s') : '-'
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Export filtered counseling report to Comprehensive PDF.
     */
    public function exportPdf(Request $request)
    {
        $this->authorizeAccess();

        $records = $this->getFilteredQuery($request)->get();
        $totalCounselings   = $records->count();
        $onlineCount        = $records->where('counseling_mode', 'online')->count();
        $offlineCount       = $records->where('counseling_mode', 'offline')->count();
        $groupCount         = $records->where('counseling_type', 'Group')->count();

        $pdf = Pdf::loadView('admin.report.staff-counseling-report.pdf.comprehensive-report', compact(
            'records',
            'totalCounselings',
            'onlineCount',
            'offlineCount',
            'groupCount'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('staff_counseling_report_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export single staff counseling dossier to PDF.
     */
    public function exportCounselingPdf($id)
    {
        $this->authorizeAccess();

        $record = StaffCounseling::with(['station', 'dieselLobby'])->findOrFail($id);

        $pdf = Pdf::loadView('admin.report.staff-counseling-report.pdf.single-counseling-dossier', compact('record'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('staff_counseling_dossier_' . $record->id . '_' . date('Y-m-d') . '.pdf');
    }
}

