<?php

namespace App\Http\Controllers\Admin\Authority;

use App\Http\Controllers\Controller;
use App\Models\AuthOfficerInspectionNote;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfficerInspectionNotesController extends Controller
{
    /**
     * Display all officer inspection notes authority records.
     */
    public function index()
    {
        $officerInspectionNotes = AuthOfficerInspectionNote::with(['department', 'designation'])
            ->latest()
            ->get();

        return view('admin.authority.officer-inspection-notes.index', compact('officerInspectionNotes'));
    }

    /**
     * Show create modal.
     */
    public function create()
    {
        $departments = Department::where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('admin.authority.officer-inspection-notes.create', compact('departments'));
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
                AuthOfficerInspectionNote::firstOrCreate(
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
            ->with('success', 'Officer Inspection Notes authority created successfully.');
    }

    /**
     * Show edit modal.
     */
    public function edit($id)
    {
        $officerInspectionNote = AuthOfficerInspectionNote::findOrFail(decrypt($id));

        $departments = Department::where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        $designations = Designation::where('department_id', $officerInspectionNote->department_id)
            ->where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('admin.authority.officer-inspection-notes.edit', compact(
            'officerInspectionNote',
            'departments',
            'designations'
        ));
    }

    /**
     * Update record.
     */
    public function update(Request $request, $id)
    {
        $officerInspectionNote = AuthOfficerInspectionNote::findOrFail(decrypt($id));

        $request->validate([
            'department_id'  => 'required|integer|exists:departments,id',
            'designation_id' => 'required|integer|exists:designations,id',
        ]);

        $officerInspectionNote->update([
            'department_id'  => $request->department_id,
            'designation_id' => $request->designation_id,
            'updated_by'     => Auth::id(),
        ]);

        return redirect()->back()
            ->with('success', 'Officer Inspection Notes authority updated successfully.');
    }

    /**
     * Delete record.
     */
    public function destroy($id)
    {
        $officerInspectionNote = AuthOfficerInspectionNote::findOrFail(decrypt($id));
        $officerInspectionNote->delete();

        return redirect()->back()
            ->with('success', 'Officer Inspection Notes authority deleted successfully.');
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
