<?php

namespace App\Http\Controllers\Admin\WorksPaperworkProgressRegister;

use App\Http\Controllers\Controller;
use App\Models\ApprovalListing;
use App\Models\IdentifyNewWork;
use App\Models\Ph16Ti;
use App\Models\InspectionLog;
use App\Models\InspectionLogPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InspectionLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            $isSuperOrOwner = $user && in_array(strtolower($user->type), ['super admin', 'owner']);
            if (!$isSuperOrOwner) {
                if (!$user->isPh16Authorized() && !$user->isPlanningWork()) {
                    abort(403, 'Unauthorized access to PH 16 Inspection Log.');
                }
            }
            return $next($request);
        });
    }

    private const DEPARTMENTS = [
        'Engineering',
        'TRD',
        'Signal',
        'Electrical/G',
    ];

    private const AUTHORITIES = [
        'Traffic Inspector',
        'Traffic Officer',
    ];

    /**
     * Get works assigned to the logged-in user / employee.
     */
    private function getAssignedWorksQuery()
    {
        $user = auth()->user();
        $query = IdentifyNewWork::where('status', 1);

        $isSuperOrOwner = $user && in_array(strtolower($user->type), ['super admin', 'owner']);

        // If not super admin/owner: filter works assigned to this TI/Inspector
        if (!$isSuperOrOwner && $user) {
            // If user is not TI Planning Work, only show works assigned to this TI
            if (!$user->isPlanningWork()) {
                $desigId = $user->getDesignationId();
                $desigName = '';
                if ($user->designationData) {
                    $desigName = $user->designationData->name;
                } elseif ($user->masterEmployeeName && $user->masterEmployeeName->designation) {
                    $desigName = $user->masterEmployeeName->designation->name;
                }

                $ph16TiIds = Ph16Ti::where('designation_id', $desigId)->pluck('id');

                $query->where(function ($q) use ($ph16TiIds, $desigName, $desigId) {
                    if ($ph16TiIds->isNotEmpty()) {
                        $q->where(function ($sub) use ($ph16TiIds) {
                            $sub->whereIn('ph16_ti_id', $ph16TiIds);
                            foreach ($ph16TiIds as $tiId) {
                                $sub->orWhereJsonContains('ph16_ti_ids', (int) $tiId);
                            }
                        });
                    }
                    if (!empty($desigName)) {
                        $q->orWhere('concern_traffic_inspector', 'like', '%' . trim($desigName) . '%');
                    }
                    $q->orWhereHas('ph16Ti', function ($tiQ) use ($desigId) {
                        $tiQ->where('designation_id', $desigId);
                    });
                });
            }
        }

        return $query;
    }

    public function index(Request $request)
    {
        $assignedWorkIds = $this->getAssignedWorksQuery()->pluck('id');

        $query = InspectionLog::with(['identifyNewWork', 'photos']);

        $user = auth()->user();
        $isSuperOrOwner = $user && in_array(strtolower($user->type), ['super admin', 'owner']);
        if (!$isSuperOrOwner && $user && !$user->isPlanningWork()) {
            $query->whereIn('identify_new_work_id', $assignedWorkIds);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('department_inspected', 'like', '%' . $search . '%')
                    ->orWhere('inspecting_authority', 'like', '%' . $search . '%')
                    ->orWhere('name_of_inspecting_official', 'like', '%' . $search . '%')
                    ->orWhere('work_done_detail', 'like', '%' . $search . '%')
                    ->orWhereHas('identifyNewWork', function ($workQuery) use ($search) {
                        $workQuery->where(
                            'work_description_identification',
                            'like',
                            '%' . $search . '%'
                        );
                    });
            });
        }

        $records = $query
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.master.works_paperwork_progress_register.inspection-log.index',
            compact('records')
        );
    }

    public function create()
    {
        $works = $this->getAssignedWorksQuery()
            ->orderBy('work_description_identification')
            ->get(['id', 'work_description_identification']);

        $departments = self::DEPARTMENTS;
        $authorities = self::AUTHORITIES;

        return view(
            'admin.master.works_paperwork_progress_register.inspection-log.create',
            compact('works', 'departments', 'authorities')
        );
    }

    public function store(Request $request)
    {
        $assignedWorkIds = $this->getAssignedWorksQuery()->pluck('id');

        $request->validate([
            'identify_new_work_id' => [
                'required',
                'exists:identify_new_works,id',
                function ($attribute, $value, $fail) use ($assignedWorkIds) {
                    if (!$assignedWorkIds->contains((int) $value)) {
                        $fail('Selected work is not assigned to you.');
                    }
                },
            ],
            'date_of_inspection' => 'required|date',
            'department_inspected' => 'required|in:' . implode(',', self::DEPARTMENTS),
            'inspecting_authority' => 'required|in:' . implode(',', self::AUTHORITIES),
            'name_of_inspecting_official' => 'required|string|max:255',
            'work_done_detail' => 'required|string',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,jpg,png|max:5120',
        ]);

        $record = InspectionLog::create([
            'identify_new_work_id' => $request->identify_new_work_id,
            'date_of_inspection' => date('Y-m-d', strtotime($request->date_of_inspection)),
            'department_inspected' => $request->department_inspected,
            'inspecting_authority' => $request->inspecting_authority,
            'name_of_inspecting_official' => $request->name_of_inspecting_official,
            'work_done_detail' => $request->work_done_detail,
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photoFile) {
                $path = $photoFile->store('inspection-log', 'public');

                InspectionLogPhoto::create([
                    'inspection_log_id' => $record->id,
                    'photo' => $path,
                ]);
            }
        }

        return redirect()
            ->route('works-paperwork-progress-register.inspection-log.index')
            ->with('success', 'Inspection Log added successfully.');
    }

    public function edit($id)
    {
        $id = decrypt($id);

        $record = InspectionLog::with(['identifyNewWork', 'photos'])->findOrFail($id);

        $assignedWorkIds = $this->getAssignedWorksQuery()->pluck('id');

        $works = IdentifyNewWork::where('status', 1)
            ->where(function ($q) use ($assignedWorkIds, $record) {
                $q->whereIn('id', $assignedWorkIds)
                    ->orWhere('id', $record->identify_new_work_id);
            })
            ->orderBy('work_description_identification')
            ->get(['id', 'work_description_identification']);

        $departments = self::DEPARTMENTS;
        $authorities = self::AUTHORITIES;

        return view(
            'admin.master.works_paperwork_progress_register.inspection-log.edit',
            compact('record', 'works', 'departments', 'authorities')
        );
    }

    public function update(Request $request, $id)
    {
        $id = decrypt($id);

        $record = InspectionLog::findOrFail($id);

        $assignedWorkIds = $this->getAssignedWorksQuery()->pluck('id');

        $request->validate([
            'identify_new_work_id' => [
                'required',
                'exists:identify_new_works,id',
                function ($attribute, $value, $fail) use ($assignedWorkIds, $record) {
                    if (
                        !$assignedWorkIds->contains((int) $value) &&
                        (int) $value !== (int) $record->identify_new_work_id
                    ) {
                        $fail('Selected work is not assigned to you.');
                    }
                },
            ],
            'date_of_inspection' => 'required|date',
            'department_inspected' => 'required|in:' . implode(',', self::DEPARTMENTS),
            'inspecting_authority' => 'required|in:' . implode(',', self::AUTHORITIES),
            'name_of_inspecting_official' => 'required|string|max:255',
            'work_done_detail' => 'required|string',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,jpg,png|max:5120',
        ]);

        $record->update([
            'identify_new_work_id' => $request->identify_new_work_id,
            'date_of_inspection' => date('Y-m-d', strtotime($request->date_of_inspection)),
            'department_inspected' => $request->department_inspected,
            'inspecting_authority' => $request->inspecting_authority,
            'name_of_inspecting_official' => $request->name_of_inspecting_official,
            'work_done_detail' => $request->work_done_detail,
        ]);

        // Naye photos hamesha ADD hote hain (purane delete nahi hote yahan se,
        // wo alag se photo delete button se hatate hain)
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photoFile) {
                $path = $photoFile->store('inspection-log', 'public');

                InspectionLogPhoto::create([
                    'inspection_log_id' => $record->id,
                    'photo' => $path,
                ]);
            }
        }

        return redirect()
            ->route('works-paperwork-progress-register.inspection-log.index')
            ->with('success', 'Inspection Log updated successfully.');
    }

    public function destroy($id)
    {
        $id = decrypt($id);

        $record = InspectionLog::with('photos')->findOrFail($id);

        foreach ($record->photos as $photo) {
            Storage::disk('public')->delete($photo->photo);
        }

        // inspection_log_photos rows FK cascade se apne aap delete ho jayengi
        $record->delete();

        return redirect()
            ->route('works-paperwork-progress-register.inspection-log.index')
            ->with('success', 'Inspection Log deleted successfully.');
    }

    /**
     * Ek single photo ko inspection log se delete karna (edit screen se).
     */
    public function destroyPhoto($id)
    {
        $id = decrypt($id);

        $photo = InspectionLogPhoto::findOrFail($id);

        Storage::disk('public')->delete($photo->photo);
        $photo->delete();

        return back()->with('success', 'Photo deleted successfully.');
    }
}
