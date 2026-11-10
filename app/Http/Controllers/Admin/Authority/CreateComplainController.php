<?php

namespace App\Http\Controllers\Admin\Authority;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Department;
use App\Models\Designation;
use App\Models\MasterEmployee;
use Illuminate\Http\Request;

class CreateComplainController extends Controller
{
    /**
     * Display all complaint authority records.
     */
    public function index()
    {
        $complaints = Complaint::with(['department', 'designation'])
            ->latest()
            ->get();

        return view('admin.authority.complain.index', compact('complaints'));
    }

    /**
     * Show the create complaint authority modal.
     */
    public function create()
    {
        $departments = Department::where('status', 1)
            ->pluck('name', 'id');

        $designations = Designation::where('status', 1)
            ->pluck('name', 'id');

        return view(
            'admin.authority.complain.create',
            compact('departments', 'designations')
        );
    }

    /**
     * Store complaint authority records.
     */
    public function store(Request $request)
    {
        $request->validate([
            'department_id'   => 'required|array|min:1',
            'department_id.*' => 'required|exists:departments,id',

            'designation_id'   => 'nullable|array',
            'designation_id.*' => 'required|exists:designations,id',
        ]);

        foreach ($request->department_id as $deptId) {
            if (!empty($request->designation_id)) {
                foreach ($request->designation_id as $desigId) {
                    Complaint::create([
                        'department_id'  => $deptId,
                        'designation_id' => $desigId,
                        'created_by'     => auth()->id(),
                        'updated_by'     => auth()->id(),
                    ]);
                }
            } else {
                Complaint::create([
                    'department_id'  => $deptId,
                    'designation_id' => null,
                    'created_by'     => auth()->id(),
                    'updated_by'     => auth()->id(),
                ]);
            }
        }

        return redirect()->back()
            ->with('success', 'Complaint created successfully.');
    }

    /**
     * Show the edit complaint authority modal.
     */
    public function edit($id)
    {
        $complaint = Complaint::findOrFail(decrypt($id));

        $departments = Department::where('status', 1)
            ->pluck('name', 'id');

        $designations = Designation::where('status', 1)
            ->pluck('name', 'id');

        $employees = MasterEmployee::where('status', 1)
            ->pluck('name', 'id');

        return view(
            'admin.authority.complain.edit',
            compact('departments', 'designations', 'employees', 'complaint')
        );
    }

    /**
     * Update complaint authority.
     */
    public function update(Request $request, $id)
    {
        $complaint = Complaint::findOrFail(decrypt($id));

        $request->validate([
            'department_id'  => 'required|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
        ]);

        $complaint->update([
            'department_id'  => $request->department_id,
            'designation_id' => $request->designation_id,
            'updated_by'     => auth()->id(),
        ]);

        return redirect()->back()
            ->with('success', 'Complaint updated successfully.');
    }

    /**
     * Delete a complaint authority record.
     */
    public function destroy($id)
    {
        $complaint = Complaint::findOrFail(decrypt($id));
        $complaint->delete();

        return redirect()->back()
            ->with('success', 'Complaint deleted successfully.');
    }

    /**
     * Get active designations for one or multiple departments.
     * Used by the authority.complain.designations AJAX route.
     */
    public function getDesignationsByDepartment(Request $request)
    {
        $request->validate([
            'department_id'   => 'required|string',
        ]);

        $deptIds = array_filter(
            explode(',', $request->query('department_id')),
            fn ($id) => ctype_digit($id)
        );

        if (empty($deptIds)) {
            return response()->json([]);
        }

        $designations = Designation::whereIn('department_id', $deptIds)
            ->where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        return response()->json($designations);
    }

    /**
     * Get active designations for one or multiple departments.
     */
    public function getDesignations(Request $request)
    {
        if (!$request->filled('department_id')) {
            return response()->json([]);
        }

        $deptIds = array_filter(
            explode(',', $request->query('department_id')),
            fn ($id) => ctype_digit($id)
        );

        if (empty($deptIds)) {
            return response()->json([]);
        }

        $designations = Designation::whereIn('department_id', $deptIds)
            ->where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        return response()->json($designations);
    }
}