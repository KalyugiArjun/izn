<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Station;
use App\Models\StationWorkingRuleOriginal;
use App\Models\StationWorkingRuleCorrection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Models\GsrOriginal;
use App\Models\GsrCorrection;
use App\Models\OperatingManualOriginal;
use App\Models\OperatingManualCorrection;
use App\Models\AccidentManualOriginal;
use App\Models\AccidentManualCorrection;

use App\Models\OperatingSafetyAlert;
use App\Models\OperatingSafetyEpisode;
use App\Models\OperatingSafetyDrive;
use App\Models\CaseStudy;

use App\Models\SafetyCircular;
use App\Models\OperatingSafetyCircular;
use App\Models\Jpo;
use App\Models\OfficerInspectionNote;
use App\Models\TrafficInspectorInspectionNote;
use Illuminate\Support\Facades\DB;


class OperatingSafetyDocumentsController extends Controller
{
    // Station list page
    public function stationWorkingRuleStations(Request $request)
    {
        $query = Station::whereHas('workingRuleOriginals') // only stations that have at least one original
            ->withCount([
                'workingRuleOriginals as originals_count' => function ($q) {
                    $q->where('status', 1);
                },
            ])
            ->with(['workingRuleOriginals' => function ($q) {
                $q->where('status', 1)->withCount(['corrections as corrections_count' => function ($q2) {
                    $q2->where('status', 1);
                }]);
            }]);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        $stations = $query->orderBy('name')->get()->map(function ($station) {
            $station->total_slips = $station->workingRuleOriginals->sum('corrections_count');
            return $station;
        });

        return view('admin.master.operating_safety_documents.station_working_rule.index', compact('stations'));
    }
    public function stationWorkingRuleShow(Station $station)
    {
        $originals = StationWorkingRuleOriginal::with(['corrections' => function ($q) {
            $q->where('status', 1);
        }])
            ->where('station_id', $station->id)
            ->where('status', 1)
            ->orderByDesc('created_at')
            ->get();

        // flat list of all corrections across all originals of this station (for Correction Slips tab)
        $allCorrections = $originals->flatMap->corrections->values();

        return view('admin.master.operating_safety_documents.station_working_rule.show', compact('station', 'originals', 'allCorrections'));
    }
    public function stationWorkingRuleStoreOriginal(Request $request, Station $station)
    {
        $request->validate([
            'original_name' => 'required|string|max:255',
            'pdf'            => 'required|file|mimes:pdf|max:20480',
        ]);

        $file = $request->file('pdf');
        $path = $file->store('uploads/station_working_rule/original', 'public');

        StationWorkingRuleOriginal::create([
            'station_id'     => $station->id,
            'original_name'  => $request->original_name,
            'original_pdf'   => $path,
            'status'         => 1,
            'created_by'     => Auth::id(),
            'updated_by'     => Auth::id(),
        ]);

        return back()->with('success', __('Original PDF uploaded successfully.'));
    }
    public function stationWorkingRuleStoreSlip(Request $request, StationWorkingRuleOriginal $original)
    {
        $request->validate([
            'correction_slip_no'   => 'required|string',
            'issue_date'           => 'nullable|date',
            'imposition_date'      => 'nullable|date',
            'pdf'                  => 'required|file|mimes:pdf|max:20480',
        ]);

        $file = $request->file('pdf');
        $path = $file->store('uploads/station_working_rule/corrections', 'public');

        StationWorkingRuleCorrection::create([
            'original_id'           => $original->id,
            'correction_slip_no'    => $request->correction_slip_no,
            'issue_date'            => $request->issue_date,
            'imposition_date'       => $request->imposition_date,
            'correction_slip_pdf'   => $path,
            'status'                => 1,
            'created_by'            => Auth::id(),
            'updated_by'            => Auth::id(),
        ]);

        return back()->with('success', __('Correction slip added successfully.'));
    }


    // Display all GSR originals
    public function gsrOriginals()
    {
        $originals = GsrOriginal::where('status', 1)
            ->withCount('corrections')
            ->latest()
            ->get();

        return view('admin.master.operating_safety_documents.gsr.index', compact('originals'));
    }
    public function gsrShow(GsrOriginal $original)
    {
        $corrections = $original->corrections()
            ->where('status', 1)
            ->latest()
            ->get();

        return view('admin.master.operating_safety_documents.gsr.show', compact('original', 'corrections'));
    }
    public function operatingManualOriginals()
    {
        $originals = OperatingManualOriginal::where('status', 1)
            ->withCount('corrections')
            ->latest()
            ->get();

        return view('admin.master.operating_safety_documents.operating_manual.index', compact('originals'));
    }
    public function operatingManualShow(OperatingManualOriginal $original)
    {
        $corrections = $original->corrections()
            ->where('status', 1)
            ->latest()
            ->get();

        return view('admin.master.operating_safety_documents.operating_manual.show', compact('original', 'corrections'));
    }
    public function accidentManualOriginals()
    {
        $originals = AccidentManualOriginal::where('status', 1)
            ->withCount('corrections')
            ->latest()
            ->get();

        return view('admin.master.operating_safety_documents.accident_manual.index', compact('originals'));
    }
    public function accidentManualShow(AccidentManualOriginal $original)
    {
        $corrections = $original->corrections()
            ->where('status', 1)
            ->latest()
            ->get();

        return view('admin.master.operating_safety_documents.accident_manual.show', compact('original', 'corrections'));
    }


    // alert, episode, and drive statistics and lists

    public function alertShow()
    {
        $records = OperatingSafetyAlert::where('status', 1)->get();

        $stats = [
            'total'     => $records->count(),
            'thisYear'  => $records->filter(fn($r) => $r->date?->year == now()->year)->count(),
            'thisMonth' => $records->filter(fn($r) => $r->date?->year == now()->year && $r->date?->month == now()->month)->count(),
        ];

        return view('admin.master.operating_safety_documents.operating_safety_alert.show', compact('stats'));
    }
    public function alertList($filter)
    {
        $query = OperatingSafetyAlert::where('status', 1);

        if ($filter === 'year') {
            $query->whereYear('date', now()->year);
        } elseif ($filter === 'month') {
            $query->whereYear('date', now()->year)->whereMonth('date', now()->month);
        }

        $records = $query->orderByDesc('date')->get();

        $titles = [
            'all'   => __('Total Alerts'),
            'year'  => __('This Year Alerts'),
            'month' => __('This Month Alerts'),
        ];
        $title = $titles[$filter] ?? $titles['all'];

        return view('admin.master.operating_safety_documents.operating_safety_alert.list', compact('records', 'filter', 'title'));
    }

    public function episodeShow()
    {
        $records = OperatingSafetyEpisode::where('status', 1)->get();

        $stats = [
            'total'     => $records->count(),
            'thisYear'  => $records->filter(fn($r) => $r->date?->year == now()->year)->count(),
            'thisMonth' => $records->filter(fn($r) => $r->date?->year == now()->year && $r->date?->month == now()->month)->count(),
        ];

        return view('admin.master.operating_safety_documents.operating_safety_episode.show', compact('stats'));
    }
    public function episodeList($filter)
    {
        $query = OperatingSafetyEpisode::where('status', 1);

        if ($filter === 'year') {
            $query->whereYear('date', now()->year);
        } elseif ($filter === 'month') {
            $query->whereYear('date', now()->year)->whereMonth('date', now()->month);
        }

        $records = $query->orderByDesc('date')->get();

        $titles = [
            'all'   => __('Total Episodes'),
            'year'  => __('This Year Episodes'),
            'month' => __('This Month Episodes'),
        ];
        $title = $titles[$filter] ?? $titles['all'];

        return view('admin.master.operating_safety_documents.operating_safety_episode.list', compact('records', 'filter', 'title'));
    }

    public function driveShow()
    {
        $records = OperatingSafetyDrive::where('status', 1)->get();

        $stats = [
            'total'     => $records->count(),
            'thisYear'  => $records->filter(fn($r) => $r->date?->year == now()->year)->count(),
            'thisMonth' => $records->filter(fn($r) => $r->date?->year == now()->year && $r->date?->month == now()->month)->count(),
        ];

        return view('admin.master.operating_safety_documents.operating_safety_drive.show', compact('stats'));
    }
    public function driveList($filter)
    {
        $query = OperatingSafetyDrive::where('status', 1);

        if ($filter === 'year') {
            $query->whereYear('date', now()->year);
        } elseif ($filter === 'month') {
            $query->whereYear('date', now()->year)->whereMonth('date', now()->month);
        }

        $records = $query->orderByDesc('date')->get();

        $titles = [
            'all'   => __('Total Drives'),
            'year'  => __('This Year Drives'),
            'month' => __('This Month Drives'),
        ];
        $title = $titles[$filter] ?? $titles['all'];

        return view('admin.master.operating_safety_documents.operating_safety_drive.list', compact('records', 'filter', 'title'));
    }


    // 

    public function caseStudyShow()
    {
        $caseStudies = CaseStudy::with('department')
            ->where('status', 1)
            ->latest()
            ->get();

        return view(
            'admin.master.operating_safety_documents.case_study.show',
            compact('caseStudies')
        );
    }

    public function caseStudyDetail($id)
    {
        $caseStudy = CaseStudy::with('department')
            ->findOrFail($id);

        return view(
            'admin.master.operating_safety_documents.case_study.list',
            compact('caseStudy')
        );
    }

    // 

    public function safetyCircularList(Request $request)
    {
        $query = SafetyCircular::where('status', 1);

        if ($request->filled('date')) {
            $query->whereDate('date_of_circular', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('circular_no', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $records = $query->latest('date_of_circular')
            ->paginate(12)
            ->withQueryString();

        return view(
            'admin.master.safety_circular.show',
            compact('records')
        );
    }

    public function operatingSafetyCircularList(Request $request)
    {
        $query = OperatingSafetyCircular::where('status', 1);

        if ($request->filled('date')) {
            $query->whereDate('date_of_circular', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('circular_no', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $records = $query->latest('date_of_circular')
            ->paginate(12)
            ->withQueryString();

        return view(
            'admin.master.operating_safety_circular.show',
            compact('records')
        );
    }

    public function jpoList(Request $request)
    {
        $query = Jpo::where('status', 1);

        if ($request->filled('date')) {
            $query->whereDate('date_of_jpo', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('jpo_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $records = $query->latest('date_of_jpo')
            ->paginate(12)
            ->withQueryString();


        return view(
            'admin.master.jpo.show',
            compact('records')
        );
    }

    public function officerInspectionNotesOfficers()
    {
        $officers = DB::table('officer_inspection_notes')
            ->select(
                'inspecting_officers',
                DB::raw('COUNT(DISTINCT type_of_inspection) as types_count')
            )
            ->where('status', 1)
            ->whereNotNull('inspecting_officers')
            ->where('inspecting_officers', '!=', '')
            ->groupBy('inspecting_officers')
            ->get();

        return view(
            'admin.master.officer_inspection_note.officers',
            compact('officers')
        );
    }

    public function officerInspectionNotesTypes($officer)
    {
        $officerName = urldecode($officer);

        $exists = DB::table('officer_inspection_notes')
            ->where('status', 1)
            ->where('inspecting_officers', $officerName)
            ->exists();

        abort_if(!$exists, 404);

        $types = DB::table('officer_inspection_notes as oin')
            ->join('type_of_inspections as toi', 'toi.id', '=', 'oin.type_of_inspection')
            ->select(
                'toi.id',
                'toi.name',
                DB::raw('COUNT(oin.id) as notes_count')
            )
            ->where('oin.status', 1)
            ->where('toi.status', 1)
            ->where('oin.inspecting_officers', $officerName)
            ->groupBy('toi.id', 'toi.name')
            // ->orderBy('toi.name')
            ->get();

        return view(
            'admin.master.officer_inspection_note.types',
            compact('types', 'officerName')
        );
    }

    public function officerInspectionNotesList(Request $request, $officer, $typeId)
    {
        $officerName = urldecode($officer);

        $type = DB::table('type_of_inspections')
            ->where('id', $typeId)
            ->where('status', 1)
            ->first();

        abort_if(!$type, 404);

        $query = DB::table('officer_inspection_notes as oin')
            ->where('oin.status', 1)
            ->where('oin.type_of_inspection', $typeId)
            ->where('oin.inspecting_officers', $officerName);

        if ($request->filled('date')) {
            $query->whereDate('oin.date_of_inspection', $request->date);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('oin.subject', 'like', '%' . $search . '%')
                    ->orWhere('oin.place_of_inspection', 'like', '%' . $search . '%');
            });
        }

        $records = $query
            ->select('oin.*')
            ->paginate(12)
            ->withQueryString();

        return view(
            'admin.master.officer_inspection_note.show',
            compact('records', 'type', 'officerName')
        );
    }



    public function trafficInspectorInspectionNotesInspectors()
    {
        $inspectors = DB::table('traffic_inspector_inspection_notes')
            ->select(
                'inspecting_traffic_inspector',
                DB::raw('COUNT(DISTINCT type_of_inspection) as types_count')
            )
            ->where('status', 1)
            ->whereNotNull('inspecting_traffic_inspector')
            ->where('inspecting_traffic_inspector', '!=', '')
            ->groupBy('inspecting_traffic_inspector')
            ->get();

        return view(
            'admin.master.traffic_inspector_inspection_note.inspectors',
            compact('inspectors')
        );
    }

    public function trafficInspectorInspectionNotesTypes($inspector)
    {
        $inspectorName = urldecode($inspector);

        $exists = DB::table('traffic_inspector_inspection_notes')
            ->where('status', 1)
            ->where('inspecting_traffic_inspector', $inspectorName)
            ->exists();

        abort_if(!$exists, 404);

        $types = DB::table('traffic_inspector_inspection_notes as tiin')
            ->join('type_of_inspections as toi', 'toi.id', '=', 'tiin.type_of_inspection')
            ->select(
                'toi.id',
                'toi.name',
                DB::raw('COUNT(tiin.id) as notes_count')
            )
            ->where('tiin.status', 1)
            ->where('toi.status', 1)
            ->where('tiin.inspecting_traffic_inspector', $inspectorName)
            ->groupBy('toi.id', 'toi.name')
            ->get();

        return view(
            'admin.master.traffic_inspector_inspection_note.types',
            compact('types', 'inspectorName')
        );
    }

    public function trafficInspectorInspectionNotesList(Request $request, $inspector, $typeId)
    {
        $inspectorName = urldecode($inspector);

        $type = DB::table('type_of_inspections')
            ->where('id', $typeId)
            ->where('status', 1)
            ->first();

        abort_if(!$type, 404);

        $query = TrafficInspectorInspectionNote::where('status', 1)
            ->where('type_of_inspection', $typeId)
            ->where('inspecting_traffic_inspector', $inspectorName);

        if ($request->filled('date')) {
            $query->whereDate('date_of_inspection', $request->date);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('place_of_inspection', 'like', '%' . $search . '%')
                    ->orWhere('subject', 'like', '%' . $search . '%');
            });
        }

        $records = $query
            ->latest('date_of_inspection')
            ->get();

        return view(
            'admin.master.traffic_inspector_inspection_note.show',
            compact('records', 'type', 'inspectorName')
        );
    }
}
