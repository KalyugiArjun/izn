<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\AmbushInspection;
use App\Models\Station;
use App\Models\TypeOfAmbush;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AmbushCheckReportController extends Controller
{
    /**
     * Restrict Ambush Check Report strictly to Admin / Super Admin (Owner).
     */
    protected function authorizeAdmin(): void
    {
        $user = auth()->user();
        if (!$user || !in_array(strtolower($user->type ?? ''), ['owner', 'super admin'])) {
            abort(403, 'Unauthorized. Ambush Check Report is restricted to Admin only.');
        }
    }

    /**
     * Build base query with filters.
     */
    protected function getFilteredQuery(Request $request)
    {
        $query = AmbushInspection::with([
            'station',
            'ambushType',
            'lobby',
            'staffName',
            'inspection.employee.designation',
        ]);

        if ($request->filled('station_id')) {
            $query->where('station_id', $request->station_id);
        }

        if ($request->filled('type_of_ambush_id') && $request->type_of_ambush_id !== 'all') {
            $query->where('type_of_ambush_id', $request->type_of_ambush_id);
        }

        if ($request->filled('day_night') && $request->day_night !== 'all') {
            $query->where('day_night', strtoupper($request->day_night));
        }

        if ($request->filled('major_deficiency') && $request->major_deficiency !== 'all') {
            $query->where('major_deficiency', strtoupper($request->major_deficiency));
        }

        if ($request->filled('from_date')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->from_date)
                  ->orWhereHas('inspection', function ($iq) use ($request) {
                      $iq->whereDate('inspection_date', '>=', $request->from_date);
                  });
            });
        }

        if ($request->filled('to_date')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->to_date)
                  ->orWhereHas('inspection', function ($iq) use ($request) {
                      $iq->whereDate('inspection_date', '<=', $request->to_date);
                  });
            });
        }

        if ($request->filled('search')) {
            $search = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('shunting_observation', 'like', $search)
                  ->orWhere('t_806_no', 'like', $search)
                  ->orWhere('train_no', 'like', $search)
                  ->orWhere('load_type', 'like', $search)
                  ->orWhere('stabling_line_no', 'like', $search)
                  ->orWhere('running_line', 'like', $search)
                  ->orWhere('block_section', 'like', $search)
                  ->orWhere('block_taken_by', 'like', $search)
                  ->orWhere('lc_gate_no', 'like', $search)
                  ->orWhere('alcohol_test', 'like', $search)
                  ->orWhere('staff_name', 'like', $search)
                  ->orWhere('write_deficiency', 'like', $search)
                  ->orWhereHas('station', function ($sq) use ($search) {
                      $sq->where('name', 'like', $search)->orWhere('code', 'like', $search);
                  })
                  ->orWhereHas('ambushType', function ($tq) use ($search) {
                      $tq->where('name', 'like', $search);
                  })
                  ->orWhereHas('lobby', function ($lq) use ($search) {
                      $lq->where('name', 'like', $search);
                  })
                  ->orWhereHas('inspection', function ($iq) use ($search) {
                      $iq->where('officer_name', 'like', $search)
                         ->orWhere('designation', 'like', $search)
                         ->orWhereHas('employee', function ($eq) use ($search) {
                             $eq->where('name', 'like', $search);
                         });
                  });
            });
        }

        return $query->latest('created_at');
    }

    /**
     * Display Ambush Check Records Report.
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        // Summary KPI Counts (All 5 modules)
        $totalChecks        = AmbushInspection::count();
        $shuntingChecks     = AmbushInspection::whereHas('ambushType', function ($q) {
            $q->where('name', 'like', '%SHUNTING%');
        })->orWhere('type_of_ambush_id', 1)->count();

        $loadStableChecks   = AmbushInspection::whereHas('ambushType', function ($q) {
            $q->where('name', 'like', '%LOAD STABLE%');
        })->orWhere('type_of_ambush_id', 2)->count();

        $engBlockChecks     = AmbushInspection::whereHas('ambushType', function ($q) {
            $q->where('name', 'like', '%ENGINEERING BLOCK%');
        })->orWhere('type_of_ambush_id', 3)->count();

        $lcVoiceChecks      = AmbushInspection::whereHas('ambushType', function ($q) {
            $q->where('name', 'like', '%VOICE%');
        })->orWhere('type_of_ambush_id', 4)->count();

        $baTestChecks       = AmbushInspection::whereHas('ambushType', function ($q) {
            $q->where('name', 'like', '%BA TEST%');
        })->orWhere('type_of_ambush_id', 5)->count();

        $dayChecks          = AmbushInspection::where('day_night', 'DAY')->count();
        $nightChecks        = AmbushInspection::where('day_night', 'NIGHT')->count();
        $deficiencyChecks   = AmbushInspection::where('major_deficiency', 'YES')->count();
        $normalChecks       = AmbushInspection::where('major_deficiency', 'NO')->count();

        // Dropdown Lists
        $stations    = Station::where('status', 1)->orderBy('name')->get();
        $ambushTypes = TypeOfAmbush::where('status', 1)->orderBy('name')->get();

        // Filtered records
        $records = $this->getFilteredQuery($request)->get();

        return view('admin.report.ambush-check-report.index', compact(
            'records',
            'stations',
            'ambushTypes',
            'totalChecks',
            'shuntingChecks',
            'loadStableChecks',
            'engBlockChecks',
            'baTestChecks',
            'lcVoiceChecks',
            'dayChecks',
            'nightChecks',
            'deficiencyChecks',
            'normalChecks'
        ));
    }

    /**
     * Show single ambush record detail modal.
     */
    public function show($id)
    {
        $this->authorizeAdmin();

        $ambush = AmbushInspection::with([
            'station',
            'ambushType',
            'lobby',
            'staffName',
            'inspection.employee.designation',
        ])->findOrFail($id);

        return view('admin.report.ambush-check-report.partials.show-modal', compact('ambush'));
    }

    /**
     * Export all filtered records to CSV / Excel matching Page 3 & Page 4.
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $this->authorizeAdmin();

        $records = $this->getFilteredQuery($request)->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="ambush_check_records_' . date('Y-m-d_His') . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($records) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            // CSV Header Row matching Page 3 & Page 4
            fputcsv($handle, [
                '#',
                'Ambush Check Date',
                'Ambush Check Time',
                'Inspected By (Name)',
                'Inspected By (Designation)',
                'Station Name',
                'Station Code',
                'From Time',
                'To Time',
                'Day / Night',
                'Type of Ambush Check',
                // 1. SHUNTING
                'Shunting Observation Of',
                'T/806 No',
                'Shunting Staff Details',
                'Shunting Any Major Deficiency',
                // 2. LOAD STABLE
                'Load Type',
                'Total No. of Loads',
                'Running Line / Non-Running Line',
                'Stabling Line No.',
                'Load Stable Any Major Deficiency',
                // 3. ENGINEERING BLOCK
                'Location of Ambush Check',
                'Block Taken By',
                'Engineering Block Any Major Deficiency',
                // 4. BA TEST
                'Lobby Name',
                'BA Station Name',
                'BA Staff Name',
                'BA Staff Designation',
                'Alcohol Test',
                'Alcohol Percentage',
                'BA Test Any Major Deficiency',
                // 5. NON-INTERLOCKED LC VOICE RECORDING
                'Date of Voice Recording',
                'Block Section',
                'LC Gate No',
                'Train No',
                'Voice Recording Time (Train)',
                'Voice Clip (Train)',
                'Voice Recording Time (LC)',
                'Voice Clip (LC)',
                'Actual Departure Time',
                'Actual Arrival Time At Next Station',
                'LC Voice Any Major Deficiency',
                // Remarks
                'Major Deficiency Remarks',
            ]);

            foreach ($records as $idx => $r) {
                $inspDate = $r->inspection && $r->inspection->inspection_date 
                    ? Carbon::parse($r->inspection->inspection_date)->format('d-m-Y') 
                    : ($r->created_at ? $r->created_at->format('d-m-Y') : '-');

                $inspTime = $r->from_time ?? ($r->created_at ? $r->created_at->format('h:i A') : '-');

                $officerName = $r->inspection->officer_name 
                    ?? ($r->inspection->employee->name ?? '-');

                $officerDesig = $r->inspection->designation 
                    ?? ($r->inspection->employee->designation->name ?? '-');

                // Shunting Staff list string
                $shuntingStaffText = '-';
                if ($r->shunting_staff_details && $r->shunting_staff_details->count() > 0) {
                    $shuntingStaffText = $r->shunting_staff_details->map(function ($s) {
                        return $s['name'] . ' (' . $s['designation'] . ')';
                    })->implode('; ');
                }

                $typeName = strtoupper($r->ambushType->name ?? '');
                $typeId = $r->type_of_ambush_id;

                $isShunting = str_contains($typeName, 'SHUNTING') || $typeId == 1;
                $isLoadStable = str_contains($typeName, 'LOAD STABLE') || $typeId == 2;
                $isEngBlock = str_contains($typeName, 'ENGINEERING') || $typeId == 3;
                $isLcVoice = str_contains($typeName, 'VOICE') || str_contains($typeName, 'LC') || $typeId == 4;
                $isBaTest = str_contains($typeName, 'BA TEST') || $typeId == 5;

                fputcsv($handle, [
                    $idx + 1,
                    $inspDate,
                    $inspTime,
                    $officerName,
                    $officerDesig,
                    $r->station->name ?? '-',
                    $r->station->code ?? '-',
                    $r->from_time ?? '-',
                    $r->to_time ?? '-',
                    $r->day_night ?? '-',
                    $r->ambushType->name ?? '-',
                    // 1. Shunting
                    $isShunting ? ($r->shunting_observation ?? '-') : 'N/A',
                    $isShunting ? ($r->t_806_no ?? '-') : 'N/A',
                    $isShunting ? $shuntingStaffText : 'N/A',
                    $isShunting ? ($r->major_deficiency ?? 'NO') : 'N/A',
                    // 2. Load Stable
                    $isLoadStable ? ($r->load_type ?? '-') : 'N/A',
                    $isLoadStable ? ($r->total_number_of_load ?? '-') : 'N/A',
                    $isLoadStable ? ($r->running_line ?? '-') : 'N/A',
                    $isLoadStable ? ($r->stabling_line_no ?? '-') : 'N/A',
                    $isLoadStable ? ($r->major_deficiency ?? 'NO') : 'N/A',
                    // 3. Engineering Block
                    $isEngBlock ? ($r->station->name ?? ($r->block_section ?? '-')) : 'N/A',
                    $isEngBlock ? ($r->block_taken_by ?? '-') : 'N/A',
                    $isEngBlock ? ($r->major_deficiency ?? 'NO') : 'N/A',
                    // 4. BA Test
                    $isBaTest ? ($r->lobby->name ?? '-') : 'N/A',
                    $isBaTest ? ($r->station->name ?? '-') : 'N/A',
                    $isBaTest ? ($r->staffName->name ?? '-') : 'N/A',
                    $isBaTest ? $r->ba_staff_designation_name : 'N/A',
                    $isBaTest ? ($r->alcohol_test ?? '-') : 'N/A',
                    $isBaTest ? ($r->alcohol_percentage ?? '-') : 'N/A',
                    $isBaTest ? ($r->major_deficiency ?? 'NO') : 'N/A',
                    // 5. LC Voice Recording
                    $isLcVoice ? ($r->date_of_voice_recording ?? '-') : 'N/A',
                    $isLcVoice ? ($r->block_section ?? '-') : 'N/A',
                    $isLcVoice ? ($r->lc_gate_no ?? '-') : 'N/A',
                    $isLcVoice ? ($r->train_no ?? '-') : 'N/A',
                    $isLcVoice ? ($r->voice_recording_time_train ?? '-') : 'N/A',
                    $isLcVoice ? ($r->voice_recording_train ? 'Available' : 'None') : 'N/A',
                    $isLcVoice ? ($r->voice_recording_time_lc ?? '-') : 'N/A',
                    $isLcVoice ? ($r->voice_recording_lc ? 'Available' : 'None') : 'N/A',
                    $isLcVoice ? ($r->actual_departure_time ?? '-') : 'N/A',
                    $isLcVoice ? ($r->actual_arrival_time ?? '-') : 'N/A',
                    $isLcVoice ? ($r->major_deficiency ?? 'NO') : 'N/A',
                    // Remarks
                    $r->write_deficiency ?? '-',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export all filtered records to Landscape PDF.
     */
    public function exportPdf(Request $request)
    {
        $this->authorizeAdmin();

        $records = $this->getFilteredQuery($request)->get();
        $generatedAt = Carbon::now()->format('d M Y, h:i A');

        $pdf = Pdf::loadView('admin.report.ambush-check-report.pdf.comprehensive-report', compact('records', 'generatedAt'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'sans-serif',
            ]);

        return $pdf->download('Ambush_Check_Comprehensive_Report_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export single ambush check record dossier PDF.
     */
    public function exportAmbushPdf($id)
    {
        $this->authorizeAdmin();

        $ambush = AmbushInspection::with([
            'station',
            'ambushType',
            'lobby',
            'staffName',
            'inspection.employee.designation',
        ])->findOrFail($id);

        $generatedAt = Carbon::now()->format('d M Y, h:i A');

        $pdf = Pdf::loadView('admin.report.ambush-check-report.pdf.single-ambush-dossier', compact('ambush', 'generatedAt'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'sans-serif',
            ]);

        return $pdf->download('Ambush_Check_Dossier_#' . $ambush->id . '.pdf');
    }
}
