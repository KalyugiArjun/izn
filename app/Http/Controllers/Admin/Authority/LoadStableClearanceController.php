<?php

namespace App\Http\Controllers\Admin\Authority;

use App\Http\Controllers\Controller;
use App\Models\AuthLoadStableClearance;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoadStableClearanceController extends Controller
{
    /**
     * Display all load stable & clearance authority records.
     */
    public function index()
    {
        $loadStableClearances = AuthLoadStableClearance::with(['department', 'designation'])
            ->latest()
            ->get();

        return view('admin.authority.load-stable-clearance.index', compact('loadStableClearances'));
    }

    /**
     * Show create modal.
     */
    public function create()
    {
        $departments = Department::where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('admin.authority.load-stable-clearance.create', compact('departments'));
    }

    /**
     * Store records.
     */
    public function store(Request $request)
    {
        $request->validate([
            'department_id'   => 'required|array|min:1',
            'department_id.*' => 'required|integer|exists:departments,id',
            'designation_id'   => 'required|array|min:1',
            'designation_id.*' => 'required|integer|exists:designations,id',
        ]);

        foreach ($request->department_id as $departmentId) {
            foreach ($request->designation_id as $designationId) {
                AuthLoadStableClearance::firstOrCreate(
                    [
                        'department_id'  => $departmentId,
                        'designation_id' => $designationId,
                    ],
                    [
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]
                );
            }
        }

        return redirect()->back()
            ->with('success', 'Load Stable & Clearance authority created successfully.');
    }

    /**
     * Show edit modal.
     */
    public function edit($id)
    {
        $loadStableClearance = AuthLoadStableClearance::findOrFail(decrypt($id));

        $departments = Department::where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        $designations = Designation::where('department_id', $loadStableClearance->department_id)
            ->where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('admin.authority.load-stable-clearance.edit', compact(
            'loadStableClearance',
            'departments',
            'designations'
        ));
    }

    /**
     * Update record.
     */
    public function update(Request $request, $id)
    {
        $loadStableClearance = AuthLoadStableClearance::findOrFail(decrypt($id));

        $request->validate([
            'department_id'  => 'required|integer|exists:departments,id',
            'designation_id' => 'required|integer|exists:designations,id',
        ]);

        $loadStableClearance->update([
            'department_id'  => $request->department_id,
            'designation_id' => $request->designation_id,
            'updated_by'     => Auth::id(),
        ]);

        return redirect()->back()
            ->with('success', 'Load Stable & Clearance authority updated successfully.');
    }

    /**
     * Delete record.
     */
    public function destroy($id)
    {
        $loadStableClearance = AuthLoadStableClearance::findOrFail(decrypt($id));
        $loadStableClearance->delete();

        return redirect()->back()
            ->with('success', 'Load Stable & Clearance authority deleted successfully.');
    }

    /**
     * Dynamic AJAX designations.
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

        return response()->json(
            Designation::whereIn('department_id', $ids)
                ->where('status', 1)
                ->orderBy('name')
                ->pluck('name', 'id')
        );
    }
}
