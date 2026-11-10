<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StationWorkingRuleOriginal;
use App\Models\StationWorkingRuleCorrection;
use App\Models\GsrOriginal;
use App\Models\GsrCorrection;
use App\Models\OperatingManualOriginal;
use App\Models\OperatingManualCorrection;
use App\Models\AccidentManualOriginal;
use App\Models\AccidentManualCorrection;
use App\Models\MiscellaneousLetters;
use App\Models\OperatingSafetyAlert;
use App\Models\OperatingSafetyEpisode;
use App\Models\OperatingSafetyDrive;
use App\Models\CaseStudy;
use App\Models\SafetyCircular;
use App\Models\OperatingSafetyCircular;
use App\Models\Jpo;
use App\Models\OfficerInspectionNote;
use App\Models\TrafficInspectorInspectionNote;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;


class CircularController extends Controller
{

    public function operatingDocDetails(Request $request): JsonResponse
    {
        $stationOriginalCount = StationWorkingRuleOriginal::count();
        $stationCorrectionCount = StationWorkingRuleCorrection::count();

        $gsrOriginal  = GsrOriginal::count();
        $gsrCorrection = GsrCorrection::count();

        $OperatingManualOriginal = OperatingManualOriginal::count();
        $OperatingManualCorrection = OperatingManualCorrection::count();

        $AccidentManualOriginal = AccidentManualOriginal::count();
        $AccidentManualCorrection = AccidentManualCorrection::count();

        $MiscellaneousLetters = MiscellaneousLetters::count();
        $OperatingSafetyAlert = OperatingSafetyAlert::count();
        $OperatingSafetyEpisode = OperatingSafetyEpisode::count();
        $OperatingSafetyDrive = OperatingSafetyDrive::count();
        $CaseStudy = CaseStudy::count();
        $SafetyCircular = SafetyCircular::count();
        $OperatingSafetyCircular = OperatingSafetyCircular::count();
        $Jpo = Jpo::count();
        $OfficerInspectionNote = OfficerInspectionNote::count();
        $TrafficInspectorInspectionNote = TrafficInspectorInspectionNote::count();

        return response()->json([
            'status' => true,
            'message' => 'Operating document details fetched successfully.',
            'data' => [
                'station_rule' => [
                    'station' => $stationOriginalCount,
                    'correction' => $stationCorrectionCount,
                ],
                'gsr' => [
                    'original' => $gsrOriginal,
                    'correction' => $gsrCorrection,
                ],
                'operating_manual' => [
                    'original' => $OperatingManualOriginal,
                    'correction' => $OperatingManualCorrection,
                ],
                'accident_manual' => [
                    'original' => $AccidentManualOriginal,
                    'correction' => $AccidentManualCorrection,
                ],
                'miscellaneous_letters' => $MiscellaneousLetters,
                'operating_safety_alert' => $OperatingSafetyAlert,
                'operating_safety_episode' => $OperatingSafetyEpisode,
                'operating_safety_drive' => $OperatingSafetyDrive,
                'case_study' => $CaseStudy,
                'safety_circular' => $SafetyCircular,
                'operating_safety_circular' => $OperatingSafetyCircular,
                'jpo' => $Jpo,
                'officer_inspection_note' => $OfficerInspectionNote,
                'traffic_inspection_note' => $TrafficInspectorInspectionNote,
                
            ],
        ], 200);
    }



    private function buildFilteredResponse($baseQuery, string $dateColumn, ?string $filter, int $perPage): JsonResponse
    {
        // Base query = search applied, filter NOT applied
        $totalCount     = (clone $baseQuery)->count();

        $thisMonthCount = (clone $baseQuery)
            ->whereMonth($dateColumn, now()->month)
            ->whereYear($dateColumn, now()->year)
            ->count();

        $thisYearCount  = (clone $baseQuery)
            ->whereYear($dateColumn, now()->year)
            ->count();

        // Apply filter on main query for pagination
        $query = $baseQuery;

        if ($filter === 'this_month') {
            $query->whereMonth($dateColumn, now()->month)
                ->whereYear($dateColumn, now()->year);
        } elseif ($filter === 'this_year') {
            $query->whereYear($dateColumn, now()->year);
        }

        // ✅ Latest date pehle, purani date last me
        $result = $query->orderByDesc($dateColumn)
                        ->orderByDesc('id')
                        ->paginate($perPage);

        return response()->json([
            'message' => 'OK',
            'counts'  => [
                'total'      => $totalCount,
                'this_month' => $thisMonthCount,
                'this_year'  => $thisYearCount,
            ],
            'filter'  => $filter ?: 'total',
            'data'    => $result->items(),
            'pagination' => [
                'current_page' => $result->currentPage(),
                'last_page'    => $result->lastPage(),
                'per_page'     => $result->perPage(),
                'total'        => $result->total(),
                'from'         => $result->firstItem(),
                'to'           => $result->lastItem(),
            ],
        ]);
    }

    public function safetyCircular(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 10), 50);
        $search  = trim($request->get('search', ''));
        $filter  = $request->get('filter');

        $baseQuery = SafetyCircular::select([
                'id', 'circular_number', 'date_of_circular', 'subject',
                'pdf_file', 'status', 'created_by', 'updated_by',
                'created_at', 'updated_at',
            ])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('circular_number', 'like', "%{$search}%")
                        ->orWhere('date_of_circular', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%");
                });
            });

        return $this->buildFilteredResponse($baseQuery, 'date_of_circular', $filter, $perPage);
    }

    public function operatingSafetyCircular(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 10), 50);
        $search  = trim($request->get('search', ''));
        $filter  = $request->get('filter');

        $baseQuery = OperatingSafetyCircular::select([
                'id', 'circular_number', 'date_of_circular', 'subject',
                'pdf_file', 'status', 'created_by', 'updated_by',
                'created_at', 'updated_at',
            ])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('circular_number', 'like', "%{$search}%")
                        ->orWhere('date_of_circular', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%");
                });
            });

        return $this->buildFilteredResponse($baseQuery, 'date_of_circular', $filter, $perPage);
    }

    public function operatingSafetyDrive(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 10), 50);
        $search  = trim($request->get('search', ''));
        $filter  = $request->get('filter');

        $baseQuery = OperatingSafetyDrive::select([
                'id', 'date', 'drive_no', 'subject',
                'pdf_file', 'status', 'created_by', 'updated_by',
                'created_at', 'updated_at',
            ])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('drive_no', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('date', 'like', "%{$search}%");
                });
            });

        return $this->buildFilteredResponse($baseQuery, 'date', $filter, $perPage);
    }

    public function operatingSafetyAlert(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 10), 50);
        $search  = trim($request->get('search', ''));
        $filter  = $request->get('filter');

        $baseQuery = OperatingSafetyAlert::select([
                'id', 'date', 'alert_no', 'subject',
                'pdf_file', 'status', 'created_by', 'updated_by',
                'created_at', 'updated_at',
            ])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('alert_no', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('date', 'like', "%{$search}%");
                });
            });

        return $this->buildFilteredResponse($baseQuery, 'date', $filter, $perPage);
    }

    public function operatingSafetyEpisode(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 10), 50);
        $search  = trim($request->get('search', ''));
        $filter  = $request->get('filter');

        $baseQuery = OperatingSafetyEpisode::select([
                'id', 'date', 'episode_no', 'subject',
                'pdf_file', 'status', 'created_by', 'updated_by',
                'created_at', 'updated_at',
            ])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('episode_no', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('date', 'like', "%{$search}%");
                });
            });

        return $this->buildFilteredResponse($baseQuery, 'date', $filter, $perPage);
    }

    public function jpoIndex(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 10), 50);
        $search  = trim($request->get('search', ''));
        $filter  = $request->get('filter');

        $baseQuery = Jpo::select([
                'id', 'jpo_number', 'date_of_jpo', 'subject',
                'pdf_file', 'status', 'created_by', 'updated_by',
                'created_at', 'updated_at',
            ])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('jpo_number', 'like', "%{$search}%")
                        ->orWhere('date_of_jpo', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%");
                });
            });

        return $this->buildFilteredResponse($baseQuery, 'date_of_jpo', $filter, $perPage);
    }

    public function caseStudy(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 10), 50);
        $search  = trim($request->get('search', ''));
        $filter  = $request->get('filter');

        $baseQuery = CaseStudy::query()
            ->with(['department:id,name'])
            ->select([
                'id', 'case_study_title', 'incident_date', 'location',
                'department_id', 'type_of_irregularity', 'risk_level',
                'key_learning', 'pdf_file', 'status', 'created_by',
                'updated_by', 'created_at', 'updated_at',
            ])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('case_study_title', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhere('type_of_irregularity', 'like', "%{$search}%")
                        ->orWhere('incident_date', 'like', "%{$search}%")
                        ->orWhereHas('department', function ($deptQuery) use ($search) {
                            $deptQuery->where('name', 'like', "%{$search}%");
                        });
                });
            });

        return $this->buildFilteredResponse($baseQuery, 'incident_date', $filter, $perPage);
    }

    public function miscellaneousLetters(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 10), 50);
        $search  = trim($request->get('search', ''));
        $filter  = $request->get('filter');

        $baseQuery = MiscellaneousLetters::query()
            ->with(['department:id,name'])
            ->select([
                'id', 'date', 'letter_no', 'subject', 'department_id',
                'pdf_file', 'status', 'created_by', 'updated_by',
                'created_at', 'updated_at',
            ])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('letter_no', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('date', 'like', "%{$search}%")
                        ->orWhereHas('department', function ($deptQuery) use ($search) {
                            $deptQuery->where('name', 'like', "%{$search}%");
                        });
                });
            });

        return $this->buildFilteredResponse($baseQuery, 'date', $filter, $perPage);
    }




    public function stationWorkingRule(Request $request)
    {
        $stationId = $request->station_id;
        $tab       = $request->tab; // original | correction
        $search    = $request->search;
        $perPage   = $request->per_page ?? 10;

        if (!$stationId) {
            return response()->json([
                'status'  => false,
                'message' => 'Station id is required.'
            ], 422);
        }

        if ($tab === 'correction') {

            $query = StationWorkingRuleCorrection::with('original')
                ->whereHas('original', function ($q) use ($stationId) {
                    $q->where('station_id', $stationId);
                });

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('correction_slip_no', 'like', "%{$search}%")
                    ->orWhere('issue_date', 'like', "%{$search}%")
                    ->orWhere('imposition_date', 'like', "%{$search}%");
                });
            }

            // ✅ Latest correction date pehle
            $data = $query
                ->orderByDesc('imposition_date')
                ->orderByDesc('id')
                ->paginate($perPage);

        } else {

            $query = StationWorkingRuleOriginal::query()
                ->with(['station', 'corrections'])   // ✅ corrections ordered
                ->where('station_id', $stationId);

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('original_name', 'like', "%{$search}%");
                });
            }

            $data = $query
                ->withCount('corrections')
                ->latest()
                ->paginate($perPage);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Data fetched successfully.',
            'data'    => $data
        ]);
    }

    public function GsrOriginal(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 10), 50);
        $search = trim($request->get('search', ''));

        $query = GsrOriginal::select([
            'id',
            'original_name',
            'original_pdf',
            'status',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ])
        ->when($search !== '', function ($q) use ($search) {
            $q->where(function ($query) use ($search) {
                $query->where('original_name', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        })
        ->orderByDesc('created_at')
        ->paginate($perPage);

        return response()->json([
            'message' => 'OK',
            'data' => $query->items(),
            'pagination' => [
                'current_page' => $query->currentPage(),
                'last_page' => $query->lastPage(),
                'per_page' => $query->perPage(),
                'total' => $query->total(),
                'from' => $query->firstItem(),
                'to' => $query->lastItem(),
            ],
        ]);
    }

    public function GsrCorrection(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 10), 50);
        $search = trim($request->get('search', ''));
        $originalId = $request->get('original_id');

        $query = GsrCorrection::query()
            ->select([
                'id',
                'original_id',
                'correction_slip_no',
                'correction_slip_date',
                'correction_slip_pdf',
                'status',
                'created_by',
                'updated_by',
                'created_at',
                'updated_at',
            ])
            ->when($originalId, function ($q) use ($originalId) {
                $q->where('original_id', $originalId);
            })
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('correction_slip_no', 'like', "%{$search}%")
                        ->orWhere('correction_slip_date', 'like', "%{$search}%");
                        // ->orWhereHas('original', function ($originalQuery) use ($search) {
                        //     $originalQuery->where('original_name', 'like', "%{$search}%")
                        //         ->orWhere('original_id', 'like', "%{$search}%");
                        // });
                });
            })
            ->orderByDesc('correction_slip_date')
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json([
            'message' => 'OK',
            'data' => $query->items(),
            'pagination' => [
                'current_page' => $query->currentPage(),
                'last_page' => $query->lastPage(),
                'per_page' => $query->perPage(),
                'total' => $query->total(),
                'from' => $query->firstItem(),
                'to' => $query->lastItem(),
            ],
        ]);
    }

    public function operatingManualOriginal(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 10), 50);
        $search = trim($request->get('search', ''));

        $query = OperatingManualOriginal::select([
            'id',
            'original_name',
            'original_pdf',
            'status',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ])
        ->when($search !== '', function ($q) use ($search) {
            $q->where(function ($query) use ($search) {
                $query->where('original_name', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        })
        ->orderByDesc('created_at')
        ->paginate($perPage);

        return response()->json([
            'message' => 'OK',
            'data' => $query->items(),
            'pagination' => [
                'current_page' => $query->currentPage(),
                'last_page' => $query->lastPage(),
                'per_page' => $query->perPage(),
                'total' => $query->total(),
                'from' => $query->firstItem(),
                'to' => $query->lastItem(),
            ],
        ]);
    }

    public function operatingManualCorrection(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 10), 50);
        $search = trim($request->get('search', ''));
        $originalId = $request->get('original_id');

        $query = OperatingManualCorrection::query()
            ->select([
                'id',
                'original_id',
                'correction_slip_no',
                'correction_slip_date',
                'correction_slip_pdf',
                'status',
                'created_by',
                'updated_by',
                'created_at',
                'updated_at',
            ])
            ->when($originalId, function ($q) use ($originalId) {
                $q->where('original_id', $originalId);
            })
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('correction_slip_no', 'like', "%{$search}%")
                        ->orWhere('correction_slip_date', 'like', "%{$search}%");
                        // ->orWhereHas('original', function ($originalQuery) use ($search) {
                        //     $originalQuery->where('original_name', 'like', "%{$search}%")
                        //         ->orWhere('original_id', 'like', "%{$search}%");
                        // });
                });
            })
            ->orderByDesc('correction_slip_date')
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json([
            'message' => 'OK',
            'data' => $query->items(),
            'pagination' => [
                'current_page' => $query->currentPage(),
                'last_page' => $query->lastPage(),
                'per_page' => $query->perPage(),
                'total' => $query->total(),
                'from' => $query->firstItem(),
                'to' => $query->lastItem(),
            ],
        ]);
    }

    public function accidentManualOriginal(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 10), 50);
        $search = trim($request->get('search', ''));


        $query = AccidentManualOriginal::select([
            'id',
            'original_name',
            'original_pdf',
            'status',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ])
        ->when($search !== '', function ($q) use ($search) {
            $q->where(function ($query) use ($search) {
                $query->where('original_name', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            });
        })
        ->orderByDesc('created_at')
        ->paginate($perPage);

        return response()->json([
            'message' => 'OK',
            'data' => $query->items(),
            'pagination' => [
                'current_page' => $query->currentPage(),
                'last_page' => $query->lastPage(),
                'per_page' => $query->perPage(),
                'total' => $query->total(),
                'from' => $query->firstItem(),
                'to' => $query->lastItem(),
            ],
        ]);
    }

    public function accidentManualCorrection(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 10), 50);
        $search = trim($request->get('search', ''));
        $originalId = $request->get('original_id');

        $query = AccidentManualCorrection::query()
            ->select([
                'id',
                'original_id',
                'correction_slip_no',
                'correction_slip_date',
                'correction_slip_pdf',
                'status',
                'created_by',
                'updated_by',
                'created_at',
                'updated_at',
            ])
            ->when($originalId, function ($q) use ($originalId) {
                $q->where('original_id', $originalId);
            })
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('correction_slip_no', 'like', "%{$search}%")
                        ->orWhere('correction_slip_date', 'like', "%{$search}%");
                        // ->orWhereHas('original', function ($originalQuery) use ($search) {
                        //     $originalQuery->where('original_name', 'like', "%{$search}%")
                        //         ->orWhere('original_id', 'like', "%{$search}%");
                        // });
                });
            })
            ->orderByDesc('correction_slip_date')
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json([
            'message' => 'OK',
            'data' => $query->items(),
            'pagination' => [
                'current_page' => $query->currentPage(),
                'last_page' => $query->lastPage(),
                'per_page' => $query->perPage(),
                'total' => $query->total(),
                'from' => $query->firstItem(),
                'to' => $query->lastItem(),
            ],
        ]);
    }



    public function OfficerInspectionNote(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 10), 50);

        $inspectingOfficers = trim($request->get('inspecting_officers', ''));
        $typeOfInspection = $request->get('type_of_inspection');
        $search = trim($request->get('search', ''));
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');


        $records = OfficerInspectionNote::query()
            ->with(['inspectionType:id,name'])
            ->when($dateFrom && $dateTo, function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('date_of_inspection', [
                    Carbon::parse($dateFrom)->startOfDay(),
                    Carbon::parse($dateTo)->endOfDay(),
                ]);
            })
            ->select([
                'id',
                'inspecting_officers',
                'type_of_inspection',
                'place_of_inspection',
                'date_of_inspection',
                'inspection_note_pdf',
                'status',
                'created_by',
                'updated_by',
                'created_at',
                'updated_at',
            ])

            // FIXED FILTERS
            ->when($inspectingOfficers !== '', function ($q) use ($inspectingOfficers) {
                $q->where('inspecting_officers', $inspectingOfficers);
            })

            ->when($typeOfInspection !== null && $typeOfInspection !== '', function ($q) use ($typeOfInspection) {
                $q->where('type_of_inspection', $typeOfInspection);
            })

            // SEARCH ONLY INSIDE FILTERED DATA
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('inspecting_officers', 'like', "%{$search}%")
                        ->orWhere('place_of_inspection', 'like', "%{$search}%")
                        ->orWhere('date_of_inspection', 'like', "%{$search}%");
                });
            })

            ->orderByDesc('date_of_inspection')
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json([
            'message' => 'OK',
            'data' => $records->items(),
            'pagination' => [
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'per_page' => $records->perPage(),
                'total' => $records->total(),
                'from' => $records->firstItem(),
                'to' => $records->lastItem(),
            ],
        ]);
    }
    public function OfficerInspectionNoteSummary(Request $request): JsonResponse
    {
        $search = trim($request->get('search', ''));
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = OfficerInspectionNote::query()
            ->with('inspectionType:id,name')

            ->when($dateFrom && $dateTo, function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('date_of_inspection', [
                    Carbon::parse($dateFrom)->startOfDay(),
                    Carbon::parse($dateTo)->endOfDay(),
                ]);
            })

            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($query) use ($search) {

                    $query->where(
                        'inspecting_officers',
                        'like',
                        "%{$search}%"
                    )

                    ->orWhereHas('inspectionType', function ($typeQuery) use ($search) {
                        $typeQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    });
                });
            })
            ->select([
                'id',
                'inspecting_officers',
                'type_of_inspection',
            ])
            ->get();

        $totalData = $query->count(); // total notes in selected range

        $officers = $query
        ->groupBy('inspecting_officers')
        ->map(function ($notes, $officerName) {

            $inspectionTypes = $notes
                ->groupBy('type_of_inspection')
                ->map(function ($typeNotes) {

                    $first = $typeNotes->first();

                    return [
                        'id' => $first->inspectionType?->id,
                        'name' => $first->inspectionType?->name,
                        'total_notes' => $typeNotes->count(),
                    ];
                })
                ->values();

            return [
                'inspecting_officer' => $officerName,
                'total_notes' => $notes->count(), // <-- officer ke total records
                'total_types' => $inspectionTypes->count(),
                'inspection_types' => $inspectionTypes,
            ];
        })
        ->values();

        $totalTypes = $officers
            ->flatMap(function ($officer) {
                return $officer['inspection_types'];
            })
            ->pluck('id')
            ->unique()
            ->count();

        return response()->json([
            'message' => 'Officer Inspection Note Summary',

            'date_from' => $dateFrom,
            'date_to' => $dateTo,

            'total_data' => $totalData, // selected date range notes count

            'data' => $officers,

            'total_officers' => $officers->count(),

            'total_types' => $totalTypes,
        ]);
    }

    public function TrafficInspectorInspectionNote(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 10), 50);

        $inspectingOfficers = trim($request->get('inspecting_traffic_inspector', ''));
        $typeOfInspection   = $request->get('type_of_inspection');
        $search             = trim($request->get('search', ''));
        $dateFrom           = $request->get('date_from');
        $dateTo             = $request->get('date_to');

        $records = TrafficInspectorInspectionNote::query()
            ->with(['inspectionType:id,name'])
            ->when($dateFrom && $dateTo, function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('date_of_inspection', [
                    Carbon::parse($dateFrom)->startOfDay(),
                    Carbon::parse($dateTo)->endOfDay(),
                ]);
            })
            ->select([
                'id',
                'inspecting_traffic_inspector',
                'type_of_inspection',
                'place_of_inspection',
                'date_of_inspection',
                'inspection_note_pdf',
                'status',
                'created_by',
                'updated_by',
                'created_at',
                'updated_at',
            ])

            // FIXED FILTERS
            ->when($inspectingOfficers !== '', function ($q) use ($inspectingOfficers) {
                $q->where('inspecting_traffic_inspector', $inspectingOfficers);
            })

            ->when($typeOfInspection !== null && $typeOfInspection !== '', function ($q) use ($typeOfInspection) {
                $q->where('type_of_inspection', $typeOfInspection);
            })

            // SEARCH ONLY INSIDE FILTERED DATA
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($query) use ($search) {
                    $query->where('inspecting_traffic_inspector', 'like', "%{$search}%")
                        ->orWhere('place_of_inspection', 'like', "%{$search}%")
                        ->orWhere('date_of_inspection', 'like', "%{$search}%");
                });
            })

            ->orderByDesc('date_of_inspection')
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json([
            'message' => 'OK',
            'data' => $records->items(),
            'pagination' => [
                'current_page' => $records->currentPage(),
                'last_page'    => $records->lastPage(),
                'per_page'     => $records->perPage(),
                'total'        => $records->total(),
                'from'         => $records->firstItem(),
                'to'           => $records->lastItem(),
            ],
        ]);
    }
    public function TrafficInspectorInspectionNoteSummary(Request $request): JsonResponse
    {
        $search   = trim($request->get('search', ''));
        $dateFrom = $request->get('date_from');
        $dateTo   = $request->get('date_to');

        $query = TrafficInspectorInspectionNote::query()
            ->with('inspectionType:id,name')

            ->when($dateFrom && $dateTo, function ($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('date_of_inspection', [
                    Carbon::parse($dateFrom)->startOfDay(),
                    Carbon::parse($dateTo)->endOfDay(),
                ]);
            })

            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($query) use ($search) {

                    $query->where(
                        'inspecting_traffic_inspector',
                        'like',
                        "%{$search}%"
                    )

                    ->orWhereHas('inspectionType', function ($typeQuery) use ($search) {
                        $typeQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    });
                });
            })
            ->select([
                'id',
                'inspecting_traffic_inspector',
                'type_of_inspection',
            ])
            ->get();

        // Total notes in selected date range (after applying filters)
        $totalData = $query->count();

        $officers = $query
            ->groupBy('inspecting_traffic_inspector')
            ->map(function ($notes, $officerName) {

                $inspectionTypes = $notes
                    ->groupBy('type_of_inspection')
                    ->map(function ($typeNotes) {

                        $first = $typeNotes->first();

                        return [
                            'id'          => $first->inspectionType?->id,
                            'name'        => $first->inspectionType?->name,
                            'total_notes' => $typeNotes->count(),
                        ];
                    })
                    ->values();

                return [
                    'inspecting_traffic_inspector' => $officerName,
                    'total_notes'               => $notes->count(), // <-- officer ke total records
                    'total_types'                  => $inspectionTypes->count(),
                    'inspection_types'             => $inspectionTypes,
                ];
            })
            ->values();

        $totalTypes = $officers
            ->flatMap(function ($officer) {
                return $officer['inspection_types'];
            })
            ->pluck('id')
            ->unique()
            ->count();

        return response()->json([
            'message' => 'Officer Inspection Note Summary',

            'date_from' => $dateFrom,
            'date_to'   => $dateTo,

            'total_data' => $totalData, // selected date range notes count

            'data' => $officers,

            'total_officers' => $officers->count(),

            'total_types' => $totalTypes,
        ]);
    }


}