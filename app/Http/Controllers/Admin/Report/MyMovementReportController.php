<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\InspectionMovement;
use App\Models\Movement;
use App\Models\RegularInspection;
use App\Models\AmbushInspection;
use App\Models\Station;
use App\Models\TypeOfInspection;
use App\Models\TypeOfAmbush;
use App\Models\SafetyDrive;
use App\Models\AttendBlock;
use App\Models\TypeOfLeave;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MyMovementReportController extends Controller
{
    /**
     * Authorize user access to My Movement Report.
     */
    protected function authorizeAccess(): void
    {
        $user = auth()->user();
        if (!$user) {
            abort(403, 'Unauthorized.');
        }

        $type = strtolower($user->type ?? '');
        if (!in_array($type, ['owner', 'super admin', 'admin', 'department admin'])) {
            abort(403, 'Unauthorized. My Movement Records Report is restricted to Administrators.');
        }
    }

    /**
     * Build base query with filters.
     */
    protected function getFilteredQuery(Request $request)
    {
        $query = InspectionMovement::with([
            'employee.department',
            'employee.designation',
            'movement',
            'station',
            'safetyDrive',
            'attendBlock',
            'typeOfLeave',
            'regularInspection.station',
            'regularInspection.type',
            'regularInspection.dieselLobby',
            'regularInspection.runningRoom',
            'ambushInspection.station',
            'ambushInspection.ambushType',
        ]);

        // Filter by Officer Name
        if ($request->filled('officer_name') && $request->officer_name !== 'all') {
            $query->where('officer_name', $request->officer_name);
        }

        // Filter by Movement Type
        if ($request->filled('movement_id') && $request->movement_id !== 'all') {
            $query->where('movement_id', $request->movement_id);
        }

        // Filter by Regular Inspection Type (if applicable)
        if ($request->filled('type_of_inspection_id') && $request->type_of_inspection_id !== 'all') {
            $query->whereHas('regularInspection', function ($rq) use ($request) {
                $rq->where('type_of_inspection_id', $request->type_of_inspection_id);
            });
        }

        // Filter by Station
        if ($request->filled('station_id') && $request->station_id !== 'all') {
            $stationId = $request->station_id;
            $query->where(function ($q) use ($stationId) {
                $q->where('station_id', $stationId)
                  ->orWhereHas('regularInspection', function ($rq) use ($stationId) {
                      $rq->where('station_id', $stationId);
                  })
                  ->orWhereHas('ambushInspection', function ($aq) use ($stationId) {
                      $aq->where('station_id', $stationId);
                  });
            });
        }

        // Filter by Deficiency
        if ($request->filled('deficiency') && $request->deficiency !== 'all') {
            $defVal = strtoupper($request->deficiency);
            $query->where(function ($q) use ($defVal) {
                $q->whereHas('regularInspection', function ($rq) use ($defVal) {
                    $rq->where('major_deficiency', $defVal);
                })->orWhereHas('ambushInspection', function ($aq) use ($defVal) {
                    $aq->where('major_deficiency', $defVal);
                });
            });
        }

        // Filter by Date Range
        if ($request->filled('from_date')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('inspection_date', '>=', $request->from_date)
                  ->orWhereDate('created_at', '>=', $request->from_date);
            });
        }
        if ($request->filled('to_date')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('inspection_date', '<=', $request->to_date)
                  ->orWhereDate('created_at', '<=', $request->to_date);
            });
        }

        // Search keyword
        if ($request->filled('search')) {
            $search = '%' . trim($request->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('officer_name', 'like', $search)
                  ->orWhere('designation', 'like', $search)
                  ->orWhere('train_no', 'like', $search)
                  ->orWhere('block_section', 'like', $search)
                  ->orWhere('reason', 'like', $search)
                  ->orWhereHas('movement', function ($mq) use ($search) {
                      $mq->where('name', 'like', $search);
                  })
                  ->orWhereHas('station', function ($sq) use ($search) {
                      $sq->where('name', 'like', $search)->orWhere('code', 'like', $search);
                  })
                  ->orWhereHas('regularInspection', function ($rq) use ($search) {
                      $rq->where('gate_no', 'like', $search)
                         ->orWhere('block_section', 'like', $search)
                         ->orWhere('train_no', 'like', $search)
                         ->orWhere('engg_traffic', 'like', $search)
                         ->orWhere('write_deficiency', 'like', $search)
                         ->orWhereHas('station', function ($rsq) use ($search) {
                             $rsq->where('name', 'like', $search)->orWhere('code', 'like', $search);
                         });
                  })
                  ->orWhereHas('ambushInspection', function ($aq) use ($search) {
                      $aq->where('lc_gate_no', 'like', $search)
                         ->orWhere('block_section', 'like', $search)
                         ->orWhere('train_no', 'like', $search)
                         ->orWhere('shunting_observation', 'like', $search)
                         ->orWhere('write_deficiency', 'like', $search)
                         ->orWhereHas('station', function ($asq) use ($search) {
                             $asq->where('name', 'like', $search)->orWhere('code', 'like', $search);
                         });
                  });
            });
        }

        return $query->latest('inspection_date')->latest('id');
    }

    /**
     * Display My Movement Records Report listing.
     */
    public function index(Request $request)
    {
        $this->authorizeAccess();

        // Summary KPI Metrics
        $totalMovements         = InspectionMovement::count();
        $regularInspections     = InspectionMovement::whereHas('movement', fn($q) => $q->where('name', 'like', '%REGULAR%'))->count();
        $safetyDrives           = InspectionMovement::whereHas('movement', fn($q) => $q->where('name', 'like', '%SAFETY DRIVE%'))->count();
        $majorDeficiencies      = RegularInspection::where('major_deficiency', 'YES')->count() + AmbushInspection::where('major_deficiency', 'YES')->count();

        // Distinct officers for filter
        $officers = InspectionMovement::select('officer_name')
            ->whereNotNull('officer_name')
            ->where('officer_name', '!=', '')
            ->distinct()
            ->orderBy('officer_name')
            ->pluck('officer_name');

        // Dropdown data for filters
        $movements       = Movement::where('status', 1)->orderBy('name')->pluck('name', 'id');
        $inspectionTypes = TypeOfInspection::where('status', 1)->orderBy('name')->pluck('name', 'id');
        $stations        = Station::select('id', 'name', 'code')->orderBy('name')->get();

        // Get all filtered records for advance-datatable
        $records = $this->getFilteredQuery($request)->get();

        return view('admin.report.my-movement-report.index', compact(
            'records',
            'movements',
            'inspectionTypes',
            'stations',
            'officers',
            'totalMovements',
            'regularInspections',
            'safetyDrives',
            'majorDeficiencies'
        ));
    }

    /**
     * Show single record detail modal.
     */
    public function show($id)
    {
        $this->authorizeAccess();

        $record = InspectionMovement::with([
            'employee.department',
            'employee.designation',
            'movement',
            'station',
            'safetyDrive',
            'attendBlock',
            'typeOfLeave',
            'regularInspection.station',
            'regularInspection.type',
            'regularInspection.dieselLobby',
            'regularInspection.runningRoom',
            'ambushInspection.station',
            'ambushInspection.ambushType',
        ])->findOrFail($id);

        return view('admin.report.my-movement-report.partials.show-modal', compact('record'));
    }

    /**
     * Export all filtered records to Excel (CSV).
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $this->authorizeAccess();

        $records = $this->getFilteredQuery($request)->get();

        $filename = 'my_movement_records_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($records) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel
            fputs($handle, "\xEF\xBB\xBF");

            // Header row
            fputcsv($handle, [
                '#',
                'Movement Date',
                'Movement Time',
                'Movement By (Name)',
                'Movement By (Designation)',
                'Type of Movement',
                'Type of Inspection (if Regular/Ambush)',
                'Details Captured (Summary)',
                'Station Name',
                'LC Gate No',
                'Engg / Traffic',
                'Train No',
                'Block Section',
                'Inspection By (Goods/Passenger)',
                'Location (Lobby / Running Room)',
                'Specific Type (Safety Drive / Attend Block / Leave)',
                'Reason / Description',
                'PDF Attached Link',
                'Major Deficiency',
                'Deficiency Details',
                'GPS Coordinates'
            ]);

            foreach ($records as $index => $rec) {
                $mName = strtoupper($rec->movement->name ?? '');
                $reg = $rec->regularInspection;
                $amb = $rec->ambushInspection;
                $tName = $reg && $reg->type ? strtoupper($reg->type->name) : '';

                $date = $rec->inspection_date 
                    ? Carbon::parse($rec->inspection_date)->format('d/m/Y') 
                    : ($rec->created_at ? $rec->created_at->format('d/m/Y') : '-');
                $time = $rec->created_at ? $rec->created_at->format('h:i A') : '-';

                $name = $rec->officer_name ?: ($rec->employee->name ?? '-');
                $desig = $rec->designation ?: ($rec->employee->designation->name ?? '-');

                $stationName = '';
                $lcGate = '';
                $enggTraffic = '';
                $trainNo = $rec->train_no ?? '';
                $blockSec = $rec->block_section ?? '';
                $goodsPass = $rec->goods_passenger ?? '';
                $location = '';
                $typeSpecial = '';
                $reason = $rec->reason ?? '';
                $pdfDoc = '';
                $majDef = 'NO';
                $defNotes = '';
                $detailsCaptured = '';

                if (str_contains($mName, 'REGULAR') && $reg) {
                    $tName = $reg->type->name ?? ($tName ?: 'Regular Inspection');
                    $stationName = $reg->station ? $reg->station->name . ' (' . $reg->station->code . ')' : '';
                    $lcGate = $reg->gate_no ?? '';
                    $enggTraffic = $reg->engg_traffic ?? '';
                    $trainNo = $reg->train_no ?? '';
                    $blockSec = $reg->block_section ?? '';
                    $goodsPass = $reg->goods_passenger ?? '';
                    $location = $reg->dieselLobby->name ?? ($reg->runningRoom->name ?? ($reg->location_id ?? ''));
                    $majDef = strtoupper($reg->major_deficiency ?? 'NO');
                    $defNotes = $reg->write_deficiency ?? '';

                    $detailsCaptured = "Regular: {$tName}";
                    if ($stationName) $detailsCaptured .= " | Station: {$stationName}";
                    if ($lcGate) $detailsCaptured .= " | Gate: {$lcGate}";
                    if ($blockSec) $detailsCaptured .= " | Section: {$blockSec}";
                    if ($trainNo) $detailsCaptured .= " | Train: {$trainNo}";
                    if ($goodsPass) $detailsCaptured .= " | By: {$goodsPass}";
                } elseif (str_contains($mName, 'AMBUSH') && $amb) {
                    $tName = $amb->ambushType->name ?? 'Ambush Check';
                    $stationName = $amb->station ? $amb->station->name . ' (' . $amb->station->code . ')' : '';
                    $lcGate = $amb->lc_gate_no ?? '';
                    $trainNo = $amb->train_no ?? '';
                    $blockSec = $amb->block_section ?? '';
                    $majDef = strtoupper($amb->major_deficiency ?? 'NO');
                    $defNotes = $amb->write_deficiency ?? '';
                    $detailsCaptured = "Ambush: {$tName} | " . ($amb->shunting_observation ?: ($stationName ?: 'Recorded'));
                } elseif (str_contains($mName, 'SAFETY DRIVE')) {
                    $typeSpecial = $rec->safetyDrive->name ?? '-';
                    $detailsCaptured = "Safety Drive: " . $typeSpecial;
                } elseif (str_contains($mName, 'JOINT SIGNAL SIGHTING')) {
                    $goodsPass = $rec->goods_passenger ?? '-';
                    $trainNo = $rec->train_no ?? '-';
                    $blockSec = $rec->block_section ?? '-';
                    $detailsCaptured = "Inspection By: {$goodsPass}, Train: {$trainNo}, Block Section: {$blockSec}";
                } elseif (str_contains($mName, 'NI DUTY')) {
                    $stationName = $rec->station ? $rec->station->name . ' (' . $rec->station->code . ')' : '-';
                    $detailsCaptured = "Station: " . $stationName;
                } elseif (str_contains($mName, 'JOINT NOTE')) {
                    $stationName = $rec->station ? $rec->station->name . ' (' . $rec->station->code . ')' : '-';
                    $pdfDoc = $rec->joint_note_pdf ? asset('storage/' . $rec->joint_note_pdf) : '-';
                    $detailsCaptured = "Station: {$stationName}, Reason: {$reason}, PDF Attached: " . ($rec->joint_note_pdf ? 'Yes' : 'No');
                } elseif (str_contains($mName, 'SURVEY')) {
                    $stationName = $rec->station ? $rec->station->name . ' (' . $rec->station->code . ')' : '-';
                    $pdfDoc = $rec->survey_pdf ? asset('storage/' . $rec->survey_pdf) : '-';
                    $detailsCaptured = "Station: {$stationName}, Reason: {$reason}, PDF Attached: " . ($rec->survey_pdf ? 'Yes' : 'No');
                } elseif (str_contains($mName, 'ATTEND BLOCK')) {
                    $typeSpecial = $rec->attendBlock->name ?? '-';
                    $detailsCaptured = "Type of Attend Block: " . $typeSpecial;
                } elseif (str_contains($mName, 'ON LEAVE')) {
                    $typeSpecial = $rec->typeOfLeave->name ?? '-';
                    $detailsCaptured = "Type of Leave: " . $typeSpecial;
                } elseif (str_contains($mName, 'OFFICE') || str_contains($mName, 'OTHER')) {
                    $detailsCaptured = "Description: " . ($reason ?: '-');
                } elseif (str_contains($mName, 'REST') || str_contains($mName, 'COUNSELING')) {
                    $detailsCaptured = "No additional fields";
                } else {
                    $detailsCaptured = $reason ?: '-';
                }

                fputcsv($handle, [
                    $index + 1,
                    $date,
                    $time,
                    $name,
                    $desig,
                    $rec->movement->name ?? '-',
                    $tName ?: '-',
                    $detailsCaptured,
                    $stationName ?: '-',
                    $lcGate ?: '-',
                    $enggTraffic ?: '-',
                    $trainNo ?: '-',
                    $blockSec ?: '-',
                    $goodsPass ?: '-',
                    $location ?: '-',
                    $typeSpecial ?: '-',
                    $reason ?: '-',
                    $pdfDoc ?: '-',
                    $majDef,
                    $defNotes,
                    $rec->gps_location ?: '-'
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export all filtered records to comprehensive PDF.
     */
    public function exportPdf(Request $request)
    {
        $this->authorizeAccess();

        $records = $this->getFilteredQuery($request)->get();

        $totalMovements     = $records->count();
        $regularCount       = $records->filter(fn($r) => str_contains(strtoupper($r->movement->name ?? ''), 'REGULAR'))->count();
        $deficiencyCount    = $records->filter(fn($r) => ($r->regularInspection && strtoupper($r->regularInspection->major_deficiency ?? '') === 'YES') || ($r->ambushInspection && strtoupper($r->ambushInspection->major_deficiency ?? '') === 'YES'))->count();

        $pdf = Pdf::loadView('admin.report.my-movement-report.pdf.comprehensive-report', compact(
            'records',
            'totalMovements',
            'regularCount',
            'deficiencyCount'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('my_movement_records_report_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export single movement record dossier to PDF.
     */
    public function exportMovementPdf($id)
    {
        $this->authorizeAccess();

        $record = InspectionMovement::with([
            'employee.department',
            'employee.designation',
            'movement',
            'station',
            'safetyDrive',
            'attendBlock',
            'typeOfLeave',
            'regularInspection.station',
            'regularInspection.type',
            'regularInspection.dieselLobby',
            'regularInspection.runningRoom',
            'ambushInspection.station',
            'ambushInspection.ambushType',
        ])->findOrFail($id);

        $pdf = Pdf::loadView('admin.report.my-movement-report.pdf.single-movement-dossier', compact('record'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('movement_record_dossier_' . $record->id . '.pdf');
    }

    /**
     * Show edit form modal for a movement record.
     */
    public function edit($id)
    {
        $this->authorizeAccess();

        $record = InspectionMovement::with([
            'employee',
            'movement',
            'station',
            'regularInspection.station',
            'regularInspection.type',
            'ambushInspection.station',
            'ambushInspection.ambushType',
            'safetyDrive',
            'attendBlock',
            'typeOfLeave',
        ])->findOrFail($id);

        $movements       = Movement::where('status', 1)->orderBy('name')->pluck('name', 'id');
        $inspectionTypes = TypeOfInspection::where('status', 1)->orderBy('name')->pluck('name', 'id');
        $stations        = Station::select('id', 'name', 'code')->orderBy('name')->get();
        $safetyDrives    = SafetyDrive::where('status', 1)->orderBy('name')->pluck('name', 'id');
        $attendBlocks    = AttendBlock::where('status', 1)->orderBy('name')->pluck('name', 'id');
        $typeOfLeaves    = TypeOfLeave::where('status', 1)->orderBy('name')->pluck('name', 'id');

        return view('admin.report.my-movement-report.partials.edit-modal', compact(
            'record',
            'movements',
            'inspectionTypes',
            'stations',
            'safetyDrives',
            'attendBlocks',
            'typeOfLeaves'
        ));
    }

    /**
     * Update movement record and its associated inspections.
     */
    public function update(Request $request, $id)
    {
        $this->authorizeAccess();

        $record = InspectionMovement::with(['regularInspection', 'ambushInspection'])->findOrFail($id);

        $request->validate([
            'inspection_date' => 'nullable|date',
            'officer_name'    => 'nullable|string|max:255',
            'designation'     => 'nullable|string|max:255',
            'movement_id'     => 'nullable|integer',
            'station_id'      => 'nullable|integer',
            'block_section'   => 'nullable|string|max:255',
            'train_no'        => 'nullable|string|max:100',
            'goods_passenger' => 'nullable|string|max:50',
            'reason'          => 'nullable|string',
            'gps_location'    => 'nullable|string|max:255',
        ]);

        $record->update([
            'inspection_date' => $request->filled('inspection_date') ? $request->inspection_date : $record->inspection_date,
            'officer_name'    => $request->filled('officer_name') ? $request->officer_name : $record->officer_name,
            'designation'     => $request->filled('designation') ? $request->designation : $record->designation,
            'movement_id'     => $request->filled('movement_id') ? $request->movement_id : $record->movement_id,
            'station_id'      => $request->filled('station_id') ? $request->station_id : $record->station_id,
            'block_section'   => $request->filled('block_section') ? $request->block_section : $record->block_section,
            'train_no'        => $request->filled('train_no') ? $request->train_no : $record->train_no,
            'goods_passenger' => $request->filled('goods_passenger') ? $request->goods_passenger : $record->goods_passenger,
            'reason'          => $request->filled('reason') ? $request->reason : $record->reason,
            'gps_location'    => $request->filled('gps_location') ? $request->gps_location : $record->gps_location,
            'safety_drive_id' => $request->filled('safety_drive_id') ? $request->safety_drive_id : $record->safety_drive_id,
            'attend_block_id' => $request->filled('attend_block_id') ? $request->attend_block_id : $record->attend_block_id,
            'type_of_leave_id'=> $request->filled('type_of_leave_id') ? $request->type_of_leave_id : $record->type_of_leave_id,
        ]);

        // Synchronize with Regular Inspection if present
        if ($record->regularInspection) {
            $regUpdate = [];
            if ($request->filled('station_id')) {
                $regUpdate['station_id'] = $request->station_id;
            }
            if ($request->filled('type_of_inspection_id')) {
                $regUpdate['type_of_inspection_id'] = $request->type_of_inspection_id;
            }
            if ($request->filled('major_deficiency')) {
                $regUpdate['major_deficiency'] = strtoupper($request->major_deficiency) === 'YES' ? 'YES' : 'NO';
            }
            if ($request->filled('deficiency_note')) {
                $regUpdate['deficiency_note'] = $request->deficiency_note;
            }
            if (!empty($regUpdate)) {
                $record->regularInspection->update($regUpdate);
            }
        }

        // Synchronize with Ambush Inspection if present
        if ($record->ambushInspection) {
            $ambUpdate = [];
            if ($request->filled('station_id')) {
                $ambUpdate['station_id'] = $request->station_id;
            }
            if ($request->filled('block_section')) {
                $ambUpdate['block_section'] = $request->block_section;
            }
            if ($request->filled('major_deficiency')) {
                $ambUpdate['major_deficiency'] = strtoupper($request->major_deficiency) === 'YES' ? 'YES' : 'NO';
            }
            if (!empty($ambUpdate)) {
                $record->ambushInspection->update($ambUpdate);
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Movement record updated successfully.')
            ]);
        }

        return redirect()->back()->with('success', __('Movement record updated successfully.'));
    }

    /**
     * Delete movement record and its associated inspections.
     */
    public function destroy(Request $request, $id)
    {
        $this->authorizeAccess();

        $record = InspectionMovement::with(['regularInspection', 'ambushInspection'])->findOrFail($id);

        if ($record->regularInspection) {
            $record->regularInspection->delete();
        }

        if ($record->ambushInspection) {
            $record->ambushInspection->delete();
        }

        $record->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Movement record deleted successfully.')
            ]);
        }

        return redirect()->back()->with('success', __('Movement record deleted successfully.'));
    }

}
