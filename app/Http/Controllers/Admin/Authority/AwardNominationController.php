<?php

namespace App\Http\Controllers\Admin\Authority;

use App\Http\Controllers\Controller;
use App\Models\AreaOfProficiency;
use App\Models\AuthAwardNomination;
use App\Models\Department;
use App\Models\Designation;
use App\Models\MasterEmployee;
use App\Models\SelfNomination;
use App\Models\Station;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AwardNominationController extends Controller
{
    /**
     * Display all award nominations and authority records.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = in_array(strtolower($user->type ?? ''), ['super admin', 'owner', 'admin']);

        $isTiSafety = $user->isTiSafety();
        $isSrDom = $user->isSrDom();
        $isStaffUser = !$isAdmin && !$isTiSafety && !$isSrDom;

        if ($isTiSafety) {
            $defaultTab = 'pending';
            $allowedTabs = ['pending', 'forwarded'];
        } elseif ($isAdmin) {
            $defaultTab = 'authorities';
            $allowedTabs = ['authorities', 'approved', 'rejected'];
        } elseif ($isSrDom) {
            $defaultTab = 'forwarded';
            $allowedTabs = ['forwarded', 'approved', 'rejected'];
        } else {
            // Regular authorized staff member: only sees their personal submissions
            $defaultTab = 'my_nominations';
            $allowedTabs = ['my_nominations'];
        }

        $activeTab = $request->get('tab', session('active_dom_tab', $defaultTab));
        if (!in_array($activeTab, $allowedTabs)) {
            $activeTab = $defaultTab;
        }

        // Nominations Query
        $nominationsQuery = SelfNomination::with([
            'staff.department',
            'designation',
            'station',
            'proficiency',
            'proficiency_cert',
            'ti',
            'srdom',
            'creator'
        ]);

        // DATA ISOLATION & HIERARCHICAL WORKFLOW FILTERING:
        if ($isAdmin) {
            // Admin sees all nominations across division
        } elseif ($isTiSafety) {
            // TI Safety (Desig 78): Sees all nominations submitted by staff across division
        } elseif ($isSrDom) {
            // Sr. DOM (Desig 7): Sees nominations forwarded by TI Safety (Status 1, 2, 3) + any created by Sr DOM
            $nominationsQuery->where(function ($q) use ($user) {
                $q->whereIn('status', [1, 2, 3])
                    ->orWhere('created_by', $user->id)
                    ->orWhere('sr_dom_id', $user->resolved_employee?->id);
            });
        } else {
            // Normal Submitting User: sees nominations created by themselves OR for themselves
            $nominationsQuery->where(function ($q) use ($user) {
                $q->where('created_by', $user->id);
                if ($user->resolved_employee?->id) {
                    $q->orWhere('staff_id', $user->resolved_employee->id);
                }
            });
        }

        if ($request->filled('status')) {
            $nominationsQuery->where('status', (int) $request->status);
        }

        if ($request->filled('station_id')) {
            $nominationsQuery->where('station_id', (int) $request->station_id);
        }

        if ($request->filled('proficiency_id')) {
            $nominationsQuery->where('proficiency_id', (int) $request->proficiency_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $nominationsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('certificate_no', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('station', fn ($sq) => $sq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('designation', fn ($dq) => $dq->where('name', 'like', "%{$search}%"));
            });
        }

        $awardNominations = $nominationsQuery->latest('id')->get();

        // Authorities Query (only accessible to Admin)
        $authorities = $isAdmin
            ? AuthAwardNomination::with(['department', 'designation', 'employee'])->latest('id')->get()
            : collect();

        // Scoped Stats: User sees their own counts, Admin/TI see relevant counts
        $statsBase = SelfNomination::query();
        if ($isAdmin || $isTiSafety) {
            // Overall stats
        } elseif ($isSrDom) {
            $statsBase->where(function ($q) use ($user) {
                $q->whereIn('status', [1, 2, 3])
                    ->orWhere('created_by', $user->id);
            });
        } else {
            $statsBase->where(function ($q) use ($user) {
                $q->where('created_by', $user->id);
                if ($user->resolved_employee?->id) {
                    $q->orWhere('staff_id', $user->resolved_employee->id);
                }
            });
        }

        $stats = [
            'total_nominations'  => (clone $statsBase)->count(),
            'pending'            => (clone $statsBase)->where('status', 0)->count(),
            'recommended'        => (clone $statsBase)->where('status', 1)->count(),
            'forwarded'          => (clone $statsBase)->where('status', '>=', 1)->count(),
            'awarded'            => (clone $statsBase)->where('status', 2)->count(),
            'rejected'           => (clone $statsBase)->where('status', 3)->count(),
            'total_authorities'  => $isAdmin ? AuthAwardNomination::count() : 0,
        ];

        // Filter Options
        $stations = Station::when(
            Station::where('status', 1)->exists(),
            fn ($q) => $q->where('status', 1)
        )->orderBy('name')->pluck('name', 'id');
        if ($stations->isEmpty()) {
            $stations = Station::orderBy('name')->pluck('name', 'id');
        }

        $proficiencies = AreaOfProficiency::when(
            AreaOfProficiency::where('status', 1)->exists(),
            fn ($q) => $q->where('status', 1)
        )->orderBy('name')->pluck('name', 'id');
        if ($proficiencies->isEmpty()) {
            $proficiencies = AreaOfProficiency::orderBy('name')->pluck('name', 'id');
        }

        $departments = Department::when(
            Department::where('status', 1)->exists(),
            fn ($q) => $q->where('status', 1)
        )->orderBy('name')->pluck('name', 'id');
        if ($departments->isEmpty()) {
            $departments = Department::orderBy('name')->pluck('name', 'id');
        }

        $tiPendingNominations = $awardNominations->where('status', 0);
        $tiForwardedNominations = $awardNominations->where('status', '>=', 1);

        $pendingNominations = $awardNominations->whereIn('status', [0, 1]);
        $approvedNominations = $awardNominations->where('status', 2);
        $rejectedNominations = $awardNominations->where('status', 3);

        return view('admin.authority.award-nomination.index', compact(
            'awardNominations',
            'tiPendingNominations',
            'tiForwardedNominations',
            'pendingNominations',
            'approvedNominations',
            'rejectedNominations',
            'authorities',
            'stats',
            'stations',
            'proficiencies',
            'departments',
            'activeTab',
            'isTiSafety',
            'isSrDom',
            'isStaffUser'
        ));
    }

    /**
     * Show Create Award Nomination Modal (Nominate Staff)
     */
    public function createNomination()
    {
        $user = Auth::user();
        $isAdmin = in_array(strtolower($user->type ?? ''), ['super admin', 'owner', 'admin']);
        $isTiSafety = $user->isTiSafety();
        $isSrDom = $user->isSrDom();
        $isStaffUser = !$isAdmin && !$isTiSafety && !$isSrDom;

        $currentUserEmployee = null;
        $defaultTiId = null;
        if ($isStaffUser) {
            $currentUserEmployee = $user->resolved_employee;
            if ($currentUserEmployee && $currentUserEmployee->stations && $currentUserEmployee->stations->count() > 0) {
                $stId = $currentUserEmployee->stations->first()->id;
                $matchedTi = MasterEmployee::where('designation_id', 78)
                    ->whereHas('stations', fn ($q) => $q->where('station_id', $stId))
                    ->first();
                if ($matchedTi) {
                    $defaultTiId = $matchedTi->id;
                }
            }
        }

        $employees = MasterEmployee::where('status', 1)
            ->with(['designation', 'department', 'stations'])
            ->orderBy('name')
            ->get();

        $stations = Station::when(
            Station::where('status', 1)->exists(),
            fn ($q) => $q->where('status', 1)
        )->orderBy('name')->pluck('name', 'id');
        if ($stations->isEmpty()) {
            $stations = Station::orderBy('name')->pluck('name', 'id');
        }

        $designations = Designation::when(
            Designation::where('status', 1)->exists(),
            fn ($q) => $q->where('status', 1)
        )->orderBy('name')->pluck('name', 'id');
        if ($designations->isEmpty()) {
            $designations = Designation::orderBy('name')->pluck('name', 'id');
        }

        $proficiencies = AreaOfProficiency::when(
            AreaOfProficiency::where('status', 1)->exists(),
            fn ($q) => $q->where('status', 1)
        )->orderBy('name')->pluck('name', 'id');
        if ($proficiencies->isEmpty()) {
            $proficiencies = AreaOfProficiency::orderBy('name')->pluck('name', 'id');
        }

        $tis = MasterEmployee::where(function ($q) {
                $q->where('designation_id', 78)
                    ->orWhere('name', 'like', '%TI%')
                    ->orWhereHas('designation', fn ($dq) => $dq->where('name', 'like', '%TI%'));
            })
            ->where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        $srDoms = MasterEmployee::where(function ($q) {
                $q->where('designation_id', 7)
                    ->orWhere('name', 'like', '%DOM%')
                    ->orWhereHas('designation', fn ($dq) => $dq->where('name', 'like', '%DOM%'));
            })
            ->where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('admin.authority.award-nomination.nominate-create', compact(
            'employees',
            'stations',
            'designations',
            'proficiencies',
            'tis',
            'srDoms',
            'isAdmin',
            'isStaffUser',
            'currentUserEmployee',
            'defaultTiId'
        ));
    }

    /**
     * Get employee details dynamically via AJAX.
     */
    public function getEmployeeDetails($id)
    {
        $employee = MasterEmployee::with(['designation', 'department', 'stations'])->find($id);

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Employee not found'], 404);
        }

        // Determine station
        $stationId = null;
        $stationName = null;
        if ($employee->stations && $employee->stations->count() > 0) {
            $station = $employee->stations->first();
            $stationId = $station->id;
            $stationName = $station->name;
        } elseif (!empty($employee->posting_present_station)) {
            $matchStation = Station::where('name', 'like', '%' . trim($employee->posting_present_station) . '%')->first();
            if ($matchStation) {
                $stationId = $matchStation->id;
                $stationName = $matchStation->name;
            }
        }

        // Determine TI for that station
        $tiId = null;
        $tiName = null;
        if ($stationId) {
            $ti = MasterEmployee::where('designation_id', 78)
                ->whereHas('stations', fn ($q) => $q->where('station_id', $stationId))
                ->first();
            if ($ti) {
                $tiId = $ti->id;
                $tiName = $ti->name;
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id'              => $employee->id,
                'name'            => $employee->name,
                'usmid'           => $employee->usmid,
                'mobile'          => $employee->mobile,
                'department_id'   => $employee->department_id,
                'department_name' => $employee->department->name ?? '-',
                'designation_id'  => $employee->designation_id,
                'designation_name'=> $employee->designation->name ?? '-',
                'station_id'      => $stationId,
                'station_name'    => $stationName,
                'ti_id'           => $tiId,
                'ti_name'         => $tiName,
            ]
        ]);
    }

    /**
     * Store a new nomination in self_nominations.
     */
    public function storeNomination(Request $request)
    {
        $user = Auth::user();
        $isAdmin = in_array(strtolower($user->type ?? ''), ['super admin', 'owner', 'admin']);
        $isTiSafety = $user->isTiSafety();
        $isSrDom = $user->isSrDom();
        $isStaffUser = !$isAdmin && !$isTiSafety && !$isSrDom;

        if ($isStaffUser) {
            $currentUserEmployee = $user->resolved_employee;
            if ($currentUserEmployee) {
                $empStation = $currentUserEmployee->stations->first();
                $stationId = $empStation ? $empStation->id : $request->station_id;
                $request->merge([
                    'staff_id'         => $currentUserEmployee->id,
                    'designation_id'   => $currentUserEmployee->designation_id ?: $request->designation_id,
                    'station_id'       => $stationId ?: $request->station_id,
                    'nomination_stage' => 'pending'
                ]);
            } else {
                $request->merge(['nomination_stage' => 'pending']);
            }
        }

        $request->validate([
            'staff_id'         => 'required|integer|exists:master_employees,id',
            'station_id'       => 'required|integer|exists:stations,id',
            'designation_id'   => 'required|integer|exists:designations,id',
            'proficiency_id'   => 'required|integer|exists:area_of_proficiencies,id',
            'date'             => 'required|date',
            'time'             => 'required',
            'description'      => 'required|string|min:5',
            'file'             => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'nomination_stage' => 'required|in:pending,recommend,award',
        ], [
            'description.required' => 'Meritorious Work Description is compulsory.',
            'description.min'      => 'Please provide a detailed description (at least 5 characters).',
        ]);

        $employee = MasterEmployee::findOrFail($request->staff_id);

        $filePath = null;
        $fileType = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('nominations', 'public');
            $fileType = strtolower($file->getClientOriginalExtension());

            // Ensure file is instantly reachable via web server
            $publicDestDir = public_path('storage/nominations');
            if (!file_exists($publicDestDir)) {
                @mkdir($publicDestDir, 0777, true);
            }
            $publicDest = $publicDestDir . DIRECTORY_SEPARATOR . basename($filePath);
            if (!file_exists($publicDest)) {
                @copy(storage_path('app/public/' . $filePath), $publicDest);
            }
        }

        // Auto-detect TI if not explicitly provided
        $tiId = $request->ti_id;
        if (!$tiId && $request->station_id) {
            $ti = MasterEmployee::where('designation_id', 78)
                ->whereHas('stations', fn ($q) => $q->where('station_id', $request->station_id))
                ->first();
            $tiId = $ti ? $ti->id : 513; // Fallback to TI Amrinder Singh if unassigned
        }

        $status = 0;
        $tiComment = null;
        $tiForwardDate = null;
        $tiForwardTime = null;
        $srDomId = null;
        $domComment = null;
        $certProficiency = null;
        $checkedDate = null;
        $checkedTime = null;
        $certificateNo = null;

        if ($request->nomination_stage === 'award') {
            $status = 2;
            $tiComment = $request->ti_comment ?: 'Directly Approved by Authority';
            $tiForwardDate = date('Y-m-d');
            $tiForwardTime = date('H:i:s');
            $srDomId = $request->sr_dom_id ?: 510;
            $domComment = $request->dom_comment ?: 'Award granted by Admin';
            $certProficiency = $request->proficiency_id;
            $checkedDate = date('Y-m-d');
            $checkedTime = date('H:i:s');

            // Generate Certificate No
            $lastCert = SelfNomination::whereNotNull('certificate_no')->orderBy('id', 'desc')->first();
            $nextNum = 1;
            if ($lastCert && preg_match('/(\d+)$/', $lastCert->certificate_no, $matches)) {
                $nextNum = (int) $matches[1] + 1;
            }
            $certificateNo = 'NER/IZN/OPTG/' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        } elseif ($request->nomination_stage === 'recommend') {
            $status = 1;
            $tiComment = $request->ti_comment ?: 'Recommended for Award';
            $tiForwardDate = date('Y-m-d');
            $tiForwardTime = date('H:i:s');
            $srDomId = $request->sr_dom_id ?: 510;
        }

        SelfNomination::create([
            'staff_id'         => $employee->id,
            'name'             => $employee->name,
            'station_id'       => $request->station_id,
            'designation_id'   => $request->designation_id,
            'proficiency_id'   => $request->proficiency_id,
            'date'             => $request->date,
            'time'             => $request->time,
            'description'      => $request->description,
            'file'             => $filePath,
            'file_type'        => $fileType,
            'ti_id'            => $tiId,
            'ti_comment'       => $tiComment,
            'ti_forward_date'  => $tiForwardDate,
            'ti_forward_time'  => $tiForwardTime,
            'sr_dom_id'        => $srDomId,
            'dom_comment'      => $domComment,
            'cert_proficiency' => $certProficiency,
            'checked_date'     => $checkedDate,
            'checked_time'     => $checkedTime,
            'certificate_no'   => $certificateNo,
            'status'           => $status,
            'created_by'       => Auth::id(),
        ]);

        return redirect()->back()
            ->with('success', 'Award Nomination created successfully.' . ($certificateNo ? ' Certificate No: ' . $certificateNo : ''));
    }

    /**
     * Show nomination details dossier modal.
     */
    public function showNomination($id)
    {
        $nomination = SelfNomination::with([
            'staff.department',
            'designation',
            'station',
            'proficiency',
            'proficiency_cert',
            'ti',
            'srdom',
            'creator'
        ])->findOrFail(decrypt($id));

        $user = Auth::user();
        $isAdmin = in_array(strtolower($user->type ?? ''), ['super admin', 'owner', 'admin']);
        $isTiSafety = $user->isTiSafety();
        $isSrDom = $user->isSrDom();

        // Allowed if Admin, TI Safety, Sr DOM, creator, or nominee
        if (!$isAdmin && !$isTiSafety && !$isSrDom && $nomination->created_by != $user->id && $nomination->staff_id != $user->resolved_employee?->id) {
            abort(403, 'Unauthorized access: You can only view nominations submitted by or for you.');
        }

        return view('admin.authority.award-nomination.nominate-show', compact('nomination'));
    }

    /**
     * Show modal to review, recommend, award, or reject nomination.
     */
    public function actionNomination($id)
    {
        $user = Auth::user();
        $isAdmin = in_array(strtolower($user->type ?? ''), ['super admin', 'owner', 'admin']);
        $isTiSafety = $user->isTiSafety();
        $isSrDom = $user->isSrDom();
        $isStaffUser = !$isAdmin && !$isTiSafety && !$isSrDom;

        if (!$isAdmin && !$isTiSafety && !$isSrDom) {
            abort(403, 'Only administrators, TI Safety, or Sr. DOM can review nominations.');
        }

        $nomination = SelfNomination::with([
            'staff.department',
            'designation',
            'station',
            'proficiency',
            'ti',
            'srdom',
            'creator'
        ])->findOrFail(decrypt($id));

        $tis = MasterEmployee::where(function ($q) {
                $q->where('designation_id', 78)
                    ->orWhere('name', 'like', '%TI%')
                    ->orWhereHas('designation', fn ($dq) => $dq->where('name', 'like', '%TI%'));
            })
            ->where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        $srDoms = MasterEmployee::where(function ($q) {
                $q->where('designation_id', 7)
                    ->orWhere('name', 'like', '%DOM%')
                    ->orWhereHas('designation', fn ($dq) => $dq->where('name', 'like', '%DOM%'));
            })
            ->where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        $proficiencies = AreaOfProficiency::where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('admin.authority.award-nomination.nominate-action', compact(
            'nomination',
            'tis',
            'srDoms',
            'proficiencies',
            'isAdmin',
            'isTiSafety',
            'isSrDom',
            'isStaffUser'
        ));
    }

    /**
     * Process review/action on nomination.
     */
    public function updateNominationAction(Request $request, $id)
    {
        $user = Auth::user();
        $isAdmin = in_array(strtolower($user->type ?? ''), ['super admin', 'owner', 'admin']);
        $isTiSafety = $user->isTiSafety();
        $isSrDom = $user->isSrDom();

        if (!$isAdmin && !$isTiSafety && !$isSrDom) {
            abort(403, 'Only administrators or authorized approvers can update nomination status.');
        }

        $nomination = SelfNomination::findOrFail(decrypt($id));

        $request->validate([
            'status' => 'required|in:0,1,2,3',
        ]);

        $status = (int) $request->status;

        // Resolve reject comment if sent as dom_comment_reject
        $rejectComment = trim($request->dom_comment_reject ?: ($request->dom_comment ?? ''));

        // TI Safety enforces recommendation & forward to Sr. DOM
        if ($isTiSafety && !$isAdmin) {
            $request->validate([
                'ti_comment' => 'required|string|min:3',
            ], [
                'ti_comment.required' => 'TI Safety recommendation comments are compulsory.',
            ]);
            $status = 1; // Forward to Sr DOM
        }

        // Sr DOM enforces final award or reject
        if ($isSrDom && !$isAdmin) {
            if (!in_array($status, [2, 3])) {
                abort(403, 'Sr. DOM can only Award Certificate or Reject nomination.');
            }
            if ($status === 2) {
                $request->validate([
                    'dom_comment' => 'required|string|min:3',
                ], [
                    'dom_comment.required' => 'Sr. DOM award remarks / order is compulsory.',
                ]);
            } elseif ($status === 3) {
                if (empty($rejectComment)) {
                    return redirect()->back()->with('error', 'Reason for rejection is compulsory.');
                }
            }
        }

        // Admin override checks
        if ($isAdmin) {
            if ($status === 1) {
                $request->validate([
                    'ti_comment' => 'required|string|min:3',
                ], ['ti_comment.required' => 'TI recommendation remarks are compulsory.']);
            } elseif ($status === 2) {
                $request->validate([
                    'dom_comment' => 'required|string|min:3',
                ], ['dom_comment.required' => 'Award remarks / order is compulsory.']);
            } elseif ($status === 3) {
                if (empty($rejectComment)) {
                    return redirect()->back()->with('error', 'Reason for rejection is compulsory.');
                }
            }
        }

        $updateData = ['status' => $status];

        if ($status === 1) { // Forwarded / Recommended by TI Safety
            $updateData['ti_comment']      = $request->ti_comment ?: 'Recommended for Award';
            $updateData['ti_forward_date'] = date('Y-m-d');
            $updateData['ti_forward_time'] = date('H:i:s');
            $updateData['ti_id']           = $user->resolved_employee?->id ?? 513; // Amrinder Singh
            $updateData['sr_dom_id']       = $request->sr_dom_id ?: ($nomination->sr_dom_id ?: 510); // Vijay Kr Yadav
        } elseif ($status === 2) { // Awarded by Sr. DOM / Admin
            $updateData['dom_comment']      = $request->dom_comment ?: 'Award granted by Sr. DOM';
            $updateData['cert_proficiency'] = $request->cert_proficiency ?: ($nomination->proficiency_id ?: 1);
            $updateData['checked_date']     = date('Y-m-d');
            $updateData['checked_time']     = date('H:i:s');
            $updateData['sr_dom_id']       = $user->resolved_employee?->id ?? ($nomination->sr_dom_id ?: 510);
            if (empty($nomination->certificate_no)) {
                $lastCert = SelfNomination::whereNotNull('certificate_no')->orderBy('id', 'desc')->first();
                $nextNum = 1;
                if ($lastCert && preg_match('/(\d+)$/', $lastCert->certificate_no, $matches)) {
                    $nextNum = (int) $matches[1] + 1;
                }
                $updateData['certificate_no'] = 'NER/IZN/OPTG/' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
            }
        } elseif ($status === 3) { // Rejected by Sr. DOM / Admin
            $updateData['dom_comment']  = $rejectComment ?: 'Nomination rejected';
            $updateData['checked_date'] = date('Y-m-d');
            $updateData['checked_time'] = date('H:i:s');
            $updateData['sr_dom_id']   = $user->resolved_employee?->id ?? ($nomination->sr_dom_id ?: 510);
        }

        $nomination->update($updateData);

        // Flash active tab for Sr. DOM dashboard so user returns to the right tab
        if ($status === 2) {
            session()->flash('active_dom_tab', 'approved');
        } elseif ($status === 3) {
            session()->flash('active_dom_tab', 'rejected');
        } else {
            session()->flash('active_dom_tab', 'pending');
        }

        $msg = 'Nomination status updated successfully.';
        if ($status === 1) {
            $msg = 'Nomination with recommendation comment has been successfully forwarded to Sr. DOM.';
        } elseif ($status === 2) {
            $msg = 'Award Certificate successfully approved.' . (isset($updateData['certificate_no']) ? ' Certificate No: ' . $updateData['certificate_no'] : '');
        } elseif ($status === 3) {
            $msg = 'Nomination has been marked as Rejected.';
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Delete a nomination record.
     */
    public function destroyNomination($id)
    {
        $nomination = SelfNomination::findOrFail(decrypt($id));

        $user = Auth::user();
        $isAdmin = in_array(strtolower($user->type ?? ''), ['super admin', 'owner', 'admin']);
        if (!$isAdmin && ($nomination->created_by != $user->id || $nomination->status != 0)) {
            abort(403, 'Unauthorized action: You can only delete pending nominations submitted by you.');
        }

        if (!empty($nomination->file) && Storage::disk('public')->exists($nomination->file)) {
            Storage::disk('public')->delete($nomination->file);
        }

        $nomination->delete();

        return redirect()->back()
            ->with('success', 'Nomination record deleted successfully.');
    }

    /**
     * View/Print Award Certificate.
     */
    public function certificate($id)
    {
        $nomination = SelfNomination::with([
            'staff.department',
            'designation',
            'station',
            'proficiency_cert',
            'proficiency',
            'srdom'
        ])->findOrFail(decrypt($id));

        return view('admin.authority.award-nomination.certificate', compact('nomination'));
    }

    /**
     * Show modal to grant authority (to Departments & Designations or Specific Employees).
     */
    public function create()
    {
        $departments = Department::when(
            Department::where('status', 1)->exists(),
            fn ($q) => $q->where('status', 1)
        )->orderBy('name')->pluck('name', 'id');
        if ($departments->isEmpty()) {
            $departments = Department::orderBy('name')->pluck('name', 'id');
        }

        return view('admin.authority.award-nomination.create', compact('departments'));
    }

    /**
     * Store Award Nomination Authority (assigning to Department & Designation only).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'department_id'   => ['required', 'array', 'min:1'],
            'department_id.*' => ['required', 'integer', 'exists:departments,id'],
            'designation_id'   => ['required', 'array', 'min:1'],
            'designation_id.*' => ['required', 'integer', 'exists:designations,id'],
        ]);

        foreach ($data['department_id'] as $departmentId) {
            foreach ($data['designation_id'] as $designationId) {
                AuthAwardNomination::firstOrCreate(
                    [
                        'department_id'  => $departmentId,
                        'designation_id' => $designationId,
                    ],
                    [
                        'employee_id' => null,
                        'created_by'  => Auth::id(),
                        'updated_by'  => Auth::id(),
                    ]
                );
            }
        }

        return redirect()->route('authority.award-nomination.index')
            ->with('success', 'Award Nomination authority created successfully.');
    }

    /**
     * Show edit authority modal.
     */
    public function edit($id)
    {
        $awardNomination = AuthAwardNomination::with(['designation', 'department'])->findOrFail(decrypt($id));

        $departments = Department::when(
            Department::where('status', 1)->exists(),
            fn ($q) => $q->where('status', 1)
        )->orderBy('name')->pluck('name', 'id');
        if ($departments->isEmpty()) {
            $departments = Department::orderBy('name')->pluck('name', 'id');
        }

        $designations = Designation::where('department_id', $awardNomination->department_id)
            ->when(
                Designation::where('department_id', $awardNomination->department_id)->where('status', 1)->exists(),
                fn ($q) => $q->where('status', 1)
            )->orderBy('name')->pluck('name', 'id');
        if ($designations->isEmpty()) {
            $designations = Designation::where('department_id', $awardNomination->department_id)
                ->orderBy('name')
                ->pluck('name', 'id');
        }

        return view('admin.authority.award-nomination.edit', compact(
            'awardNomination',
            'departments',
            'designations'
        ));
    }

    /**
     * Update authority record.
     */
    public function update(Request $request, $id)
    {
        $awardNomination = AuthAwardNomination::findOrFail(decrypt($id));

        $data = $request->validate([
            'department_id'  => ['required', 'integer', 'exists:departments,id'],
            'designation_id' => ['required', 'integer', 'exists:designations,id'],
        ]);

        $awardNomination->update([
            'department_id'  => $data['department_id'],
            'designation_id' => $data['designation_id'],
            'employee_id'    => null,
            'updated_by'     => Auth::id(),
        ]);

        return redirect()->route('authority.award-nomination.index')
            ->with('success', 'Award Nomination authority updated successfully.');
    }

    /**
     * Delete authority record.
     */
    public function destroy($id)
    {
        $awardNomination = AuthAwardNomination::findOrFail(decrypt($id));
        $awardNomination->delete();

        return redirect()->route('authority.award-nomination.index')
            ->with('success', 'Award Nomination authority deleted successfully.');
    }

    /**
     * Dynamic AJAX designations by department.
     */
    public function getDesignationsByDepartment(Request $request)
    {
        $ids = $request->input('department_id', []);

        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        $ids = collect((array) $ids)
            ->filter(fn ($id) => ctype_digit((string) $id) && (int) $id > 0)
            ->map(fn ($id) => (int) $id)
            ->unique();

        if ($ids->isEmpty()) {
            return response()->json([]);
        }

        $query = Designation::whereIn('department_id', $ids);
        if ((clone $query)->where('status', 1)->exists()) {
            $query->where('status', 1);
        }

        return response()->json(
            $query->orderBy('name')->pluck('name', 'id')
        );
    }

    /**
     * Dynamic AJAX employees for authority grant modal.
     */
    public function getEmployeesByDepartment(Request $request)
    {
        $deptIds = $request->input('department_id', []);
        if (is_string($deptIds)) {
            $deptIds = array_filter(explode(',', $deptIds));
        }

        $query = MasterEmployee::with(['designation', 'department'])
            ->where('status', 1);

        if (!empty($deptIds)) {
            $query->whereIn('department_id', $deptIds);
        }

        $employees = $query->orderBy('name')->get();

        $result = [];
        foreach ($employees as $emp) {
            $desigName = $emp->designation->name ?? 'N/A';
            $usmid = $emp->usmid ? " (USMID: {$emp->usmid})" : '';
            $result[$emp->id] = "{$emp->name}{$usmid} - {$desigName}";
        }

        return response()->json($result);
    }
}
