<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\EmployeePME;
use App\Models\EmployeeRefresherCourse;
use App\Models\EmployeeGradation;
use App\Models\MasterEmployee;
use App\Models\Station;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployeeSafetyRecordReportController extends Controller
{
    /**
     * Authorize user access to Employee Safety Record Report.
     */
    protected function authorizeAccess(): void
    {
        $user = auth()->user();
        if (!$user) {
            abort(403, 'Unauthorized.');
        }

        $type = strtolower($user->type ?? '');
        if (!in_array($type, ['owner', 'super admin', 'admin', 'department admin', 'emp', 'employee'])) {
            abort(403, 'Unauthorized. Employee Safety Record Report is restricted to Administrators.');
        }
    }

    /**
     * Helper to compute due status (Overdue, Due Soon, Valid).
     */
    protected function computeDueStatus($dueDate): array
    {
        if (empty($dueDate)) {
            return ['text' => 'NO DATE', 'class' => 'bg-secondary-subtle text-secondary border border-secondary-subtle', 'icon' => 'ti-minus'];
        }

        $due = Carbon::parse($dueDate)->startOfDay();
        $today = Carbon::today();

        if ($due->isPast()) {
            return ['text' => 'OVERDUE', 'class' => 'bg-danger-subtle text-danger border border-danger-subtle', 'icon' => 'ti-alert-triangle'];
        }

        $diffDays = $today->diffInDays($due, false);
        if ($diffDays <= 30) {
            return ['text' => 'DUE SOON (' . $diffDays . 'd)', 'class' => 'bg-warning-subtle text-warning border border-warning-subtle', 'icon' => 'ti-clock'];
        }

        return ['text' => 'VALID', 'class' => 'bg-success-subtle text-success border border-success-subtle', 'icon' => 'ti-circle-check'];
    }

    /**
     * Fetch unified list of safety records based on filters.
     */
    protected function getUnifiedSafetyRecords(Request $request)
    {
        $recordType = strtolower($request->get('record_type', 'all'));
        $stationId  = $request->get('station_id');
        $fromDate   = $request->get('from_date');
        $toDate     = $request->get('to_date');
        $search     = trim($request->get('search', ''));

        $items = collect();

        // 1. Fetch Medical (PME) records
        if ($recordType === 'all' || $recordType === 'medical') {
            $pmeQuery = EmployeePME::with([
                'staff.stations',
                'staff.designation',
                'masterEmployee.stations',
                'masterEmployee.designation'
            ]);

            if (!empty($stationId) && $stationId !== 'all') {
                $pmeQuery->where(function ($q) use ($stationId) {
                    $q->whereHas('staff.stations', fn ($sq) => $sq->where('stations.id', $stationId))
                      ->orWhereHas('masterEmployee.stations', fn ($sq) => $sq->where('stations.id', $stationId));
                });
            }

            if (!empty($fromDate)) {
                $pmeQuery->whereDate('next_due_date', '>=', $fromDate);
            }
            if (!empty($toDate)) {
                $pmeQuery->whereDate('next_due_date', '<=', $toDate);
            }

            if (!empty($search)) {
                $pmeQuery->where(function ($q) use ($search) {
                    $q->where('certificate_no', 'like', "%{$search}%")
                      ->orWhereHas('staff', fn ($sq) => $sq->where('name', 'like', "%{$search}%"))
                      ->orWhereHas('masterEmployee', fn ($sq) => $sq->where('name', 'like', "%{$search}%"));
                });
            }

            foreach ($pmeQuery->latest('id')->get() as $pme) {
                $emp = $pme->resolved_employee;
                $st = $emp && $emp->stations->isNotEmpty() ? $emp->stations->first() : null;
                $items->push((object)[
                    'type'           => 'Medical',
                    'type_badge'     => 'bg-info-subtle text-info border border-info-subtle',
                    'type_icon'      => 'ti-stethoscope',
                    'id'             => $pme->id,
                    'record_id'      => $pme->id,
                    'staff_name'     => $emp ? $emp->name : 'N/A',
                    'usmid'          => $emp ? $emp->usmid : null,
                    'designation'    => $emp && $emp->designation ? $emp->designation->name : 'N/A',
                    'station'        => $st ? ($st->name . ' (' . $st->code . ')') : '-',
                    'station_id'     => $st ? $st->id : null,
                    'exam_date'      => $pme->last_pme_date,
                    'due_date'       => $pme->next_due_date,
                    'certificate_no' => $pme->certificate_no ?: '-',
                    'total_marks'    => null,
                    'grade'          => null,
                    'due_status'     => $this->computeDueStatus($pme->next_due_date),
                    'raw'            => $pme,
                    'sort_date'      => $pme->next_due_date ?: $pme->last_pme_date ?: $pme->created_at,
                ]);
            }
        }

        // 2. Fetch Refresher Course records
        if ($recordType === 'all' || $recordType === 'refresher') {
            $refQuery = EmployeeRefresherCourse::with([
                'staff.stations',
                'staff.designation',
                'masterEmployee.stations',
                'masterEmployee.designation'
            ]);

            if (!empty($stationId) && $stationId !== 'all') {
                $refQuery->where(function ($q) use ($stationId) {
                    $q->whereHas('staff.stations', fn ($sq) => $sq->where('stations.id', $stationId))
                      ->orWhereHas('masterEmployee.stations', fn ($sq) => $sq->where('stations.id', $stationId));
                });
            }

            if (!empty($fromDate)) {
                $refQuery->whereDate('next_due_date', '>=', $fromDate);
            }
            if (!empty($toDate)) {
                $refQuery->whereDate('next_due_date', '<=', $toDate);
            }

            if (!empty($search)) {
                $refQuery->where(function ($q) use ($search) {
                    $q->where('certificate_no', 'like', "%{$search}%")
                      ->orWhere('institution_name', 'like', "%{$search}%")
                      ->orWhereHas('staff', fn ($sq) => $sq->where('name', 'like', "%{$search}%"))
                      ->orWhereHas('masterEmployee', fn ($sq) => $sq->where('name', 'like', "%{$search}%"));
                });
            }

            foreach ($refQuery->latest('id')->get() as $ref) {
                $emp = $ref->resolved_employee;
                $st = $emp && $emp->stations->isNotEmpty() ? $emp->stations->first() : null;
                $items->push((object)[
                    'type'           => 'Refresher',
                    'type_badge'     => 'bg-success-subtle text-success border border-success-subtle',
                    'type_icon'      => 'ti-refresh',
                    'id'             => $ref->id,
                    'record_id'      => $ref->id,
                    'staff_name'     => $emp ? $emp->name : 'N/A',
                    'usmid'          => $emp ? $emp->usmid : null,
                    'designation'    => $emp && $emp->designation ? $emp->designation->name : 'N/A',
                    'station'        => $st ? ($st->name . ' (' . $st->code . ')') : '-',
                    'station_id'     => $st ? $st->id : null,
                    'exam_date'      => $ref->last_refresher_date,
                    'due_date'       => $ref->next_due_date,
                    'certificate_no' => $ref->certificate_no ?: '-',
                    'total_marks'    => null,
                    'grade'          => null,
                    'institution'    => $ref->institution_name,
                    'due_status'     => $this->computeDueStatus($ref->next_due_date),
                    'raw'            => $ref,
                    'sort_date'      => $ref->next_due_date ?: $ref->last_refresher_date ?: $ref->created_at,
                ]);
            }
        }

        // 3. Fetch Gradation records
        if ($recordType === 'all' || $recordType === 'gradation') {
            $gradQuery = EmployeeGradation::with([
                'staff.stations',
                'staff.designation',
                'masterEmployee.stations',
                'masterEmployee.designation'
            ]);

            if (!empty($stationId) && $stationId !== 'all') {
                $gradQuery->where(function ($q) use ($stationId) {
                    $q->whereHas('staff.stations', fn ($sq) => $sq->where('stations.id', $stationId))
                      ->orWhereHas('masterEmployee.stations', fn ($sq) => $sq->where('stations.id', $stationId));
                });
            }

            if (!empty($fromDate)) {
                $gradQuery->whereDate('gradation_date', '>=', $fromDate);
            }
            if (!empty($toDate)) {
                $gradQuery->whereDate('gradation_date', '<=', $toDate);
            }

            if (!empty($search)) {
                $gradQuery->where(function ($q) use ($search) {
                    $q->where('grade', 'like', "%{$search}%")
                      ->orWhereHas('staff', fn ($sq) => $sq->where('name', 'like', "%{$search}%"))
                      ->orWhereHas('masterEmployee', fn ($sq) => $sq->where('name', 'like', "%{$search}%"));
                });
            }

            foreach ($gradQuery->latest('id')->get() as $grad) {
                $emp = $grad->resolved_employee;
                $st = $emp && $emp->stations->isNotEmpty() ? $emp->stations->first() : null;
                $items->push((object)[
                    'type'           => 'Gradation',
                    'type_badge'     => 'bg-warning-subtle text-warning border border-warning-subtle',
                    'type_icon'      => 'ti-chart-bar',
                    'id'             => $grad->id,
                    'record_id'      => $grad->id,
                    'staff_name'     => $emp ? $emp->name : 'N/A',
                    'usmid'          => $emp ? $emp->usmid : null,
                    'designation'    => $emp && $emp->designation ? $emp->designation->name : 'N/A',
                    'station'        => $st ? ($st->name . ' (' . $st->code . ')') : '-',
                    'station_id'     => $st ? $st->id : null,
                    'exam_date'      => $grad->gradation_date,
                    'due_date'       => $grad->gradation_date, // Gradation test date
                    'certificate_no' => '-',
                    'total_marks'    => $grad->total_marks !== null ? number_format($grad->total_marks, 2) : '-',
                    'grade'          => $grad->grade ?: '-',
                    'due_status'     => ['text' => 'GRADED: ' . ($grad->grade ?: 'A'), 'class' => 'bg-primary-subtle text-primary border border-primary-subtle', 'icon' => 'ti-award'],
                    'raw'            => $grad,
                    'sort_date'      => $grad->gradation_date ?: $grad->created_at,
                ]);
            }
        }

        // Sort items by sort_date descending
        return $items->sortByDesc('sort_date')->values();
    }

    /**
     * Display Employee Safety Record Report Index with KPI summary & filters.
     */
    public function index(Request $request)
    {
        $this->authorizeAccess();

        $records = $this->getUnifiedSafetyRecords($request);

        // 4 KPI Stats
        $stats = [
            'total'     => EmployeePME::count() + EmployeeRefresherCourse::count() + EmployeeGradation::count(),
            'medical'   => EmployeePME::count(),
            'refresher' => EmployeeRefresherCourse::count(),
            'gradation' => EmployeeGradation::count(),
        ];

        // Filter Options
        $stations = Station::where('status', 1)->orderBy('name')->get();

        return view('admin.report.employee-safety-record-report.index', compact(
            'records',
            'stats',
            'stations'
        ));
    }

    /**
     * Show Modal Dossier for a specific Safety Record.
     */
    public function show($type, $id)
    {
        $this->authorizeAccess();

        $record = null;
        $employee = null;

        if (strtolower($type) === 'medical') {
            $record = EmployeePME::with(['staff.designation', 'staff.stations', 'masterEmployee.designation', 'masterEmployee.stations'])->findOrFail($id);
            $employee = $record->resolved_employee;
        } elseif (strtolower($type) === 'refresher') {
            $record = EmployeeRefresherCourse::with(['staff.designation', 'staff.stations', 'masterEmployee.designation', 'masterEmployee.stations'])->findOrFail($id);
            $employee = $record->resolved_employee;
        } elseif (strtolower($type) === 'gradation') {
            $record = EmployeeGradation::with(['staff.designation', 'staff.stations', 'masterEmployee.designation', 'masterEmployee.stations'])->findOrFail($id);
            $employee = $record->resolved_employee;
        } else {
            abort(404, 'Invalid record type.');
        }

        // Fetch full employee safety history
        $empId = $employee ? $employee->id : 0;
        $pmeHistory = EmployeePME::where('staff_id', $empId)->orWhere('master_employee_id', $empId)->latest()->get();
        $refHistory = EmployeeRefresherCourse::where('staff_id', $empId)->orWhere('master_employee_id', $empId)->latest()->get();
        $gradHistory = EmployeeGradation::where('staff_id', $empId)->orWhere('master_employee_id', $empId)->latest()->get();

        return view('admin.report.employee-safety-record-report.partials.show-modal', [
            'type'        => ucfirst($type),
            'record'      => $record,
            'employee'    => $employee,
            'pmeHistory'  => $pmeHistory,
            'refHistory'  => $refHistory,
            'gradHistory' => $gradHistory,
        ]);
    }

    /**
     * Export Single Employee Safety Record as PDF.
     */
    public function exportSinglePdf($type, $id)
    {
        $this->authorizeAccess();

        $record = null;
        $employee = null;

        if (strtolower($type) === 'medical') {
            $record = EmployeePME::with(['staff.designation', 'staff.stations', 'masterEmployee.designation', 'masterEmployee.stations'])->findOrFail($id);
            $employee = $record->resolved_employee;
        } elseif (strtolower($type) === 'refresher') {
            $record = EmployeeRefresherCourse::with(['staff.designation', 'staff.stations', 'masterEmployee.designation', 'masterEmployee.stations'])->findOrFail($id);
            $employee = $record->resolved_employee;
        } elseif (strtolower($type) === 'gradation') {
            $record = EmployeeGradation::with(['staff.designation', 'staff.stations', 'masterEmployee.designation', 'masterEmployee.stations'])->findOrFail($id);
            $employee = $record->resolved_employee;
        } else {
            abort(404, 'Invalid record type.');
        }

        $empId = $employee ? $employee->id : 0;
        $pmeHistory = EmployeePME::where('staff_id', $empId)->orWhere('master_employee_id', $empId)->latest()->get();
        $refHistory = EmployeeRefresherCourse::where('staff_id', $empId)->orWhere('master_employee_id', $empId)->latest()->get();
        $gradHistory = EmployeeGradation::where('staff_id', $empId)->orWhere('master_employee_id', $empId)->latest()->get();

        $pdf = Pdf::loadView('admin.report.employee-safety-record-report.pdf.single-safety-dossier', [
            'type'        => ucfirst($type),
            'record'      => $record,
            'employee'    => $employee,
            'pmeHistory'  => $pmeHistory,
            'refHistory'  => $refHistory,
            'gradHistory' => $gradHistory,
        ])->setPaper('a4', 'portrait')
          ->setOptions([
              'isHtml5ParserEnabled' => true,
              'isRemoteEnabled'      => true,
              'defaultFont'          => 'sans-serif',
          ]);

        $fileName = 'Employee_Safety_' . ucfirst($type) . '_' . ($employee ? str_replace(' ', '_', $employee->name) : 'REC_' . $id) . '_' . date('Ymd_His') . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Export Filtered Employee Safety Records to Excel (CSV).
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        $this->authorizeAccess();

        $records = $this->getUnifiedSafetyRecords($request);
        $fileName = 'Employee_Safety_Record_Report_' . date('Ymd_His') . '.csv';

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

            // CSV Header Row matching Image columns
            fputcsv($handle, [
                '#',
                'Record Type',
                'Staff Name',
                'USMID',
                'Designation',
                'Station',
                'Exam / Test Date',
                'Due Date',
                'Certificate No',
                'Total Marks',
                'Grade',
                'Status',
            ]);

            $index = 1;
            foreach ($records as $r) {
                $examDate = $r->exam_date ? Carbon::parse($r->exam_date)->format('d-m-Y') : '-';
                $dueDate = $r->due_date ? Carbon::parse($r->due_date)->format('d-m-Y') : '-';

                fputcsv($handle, [
                    $index++,
                    $r->type,
                    $r->staff_name,
                    $r->usmid ?? 'N/A',
                    $r->designation,
                    $r->station,
                    $examDate,
                    $dueDate,
                    $r->certificate_no,
                    $r->total_marks ?? '-',
                    $r->grade ?? '-',
                    $r->due_status['text'],
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Filtered Employee Safety Records to Comprehensive PDF.
     */
    public function exportPdf(Request $request)
    {
        $this->authorizeAccess();

        $records = $this->getUnifiedSafetyRecords($request);

        $stats = [
            'total'     => $records->count(),
            'medical'   => $records->where('type', 'Medical')->count(),
            'refresher' => $records->where('type', 'Refresher')->count(),
            'gradation' => $records->where('type', 'Gradation')->count(),
        ];

        $pdf = Pdf::loadView('admin.report.employee-safety-record-report.pdf.comprehensive-report', [
            'records' => $records,
            'stats'   => $stats,
            'filters' => $request->all(),
        ])->setPaper('a4', 'landscape')
          ->setOptions([
              'isHtml5ParserEnabled' => true,
              'isRemoteEnabled'      => true,
              'defaultFont'          => 'sans-serif',
          ]);

        $fileName = 'Employee_Safety_Record_Report_' . date('Ymd_His') . '.pdf';
        return $pdf->download($fileName);
    }
}
