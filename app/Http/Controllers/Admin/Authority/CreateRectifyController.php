<?php

namespace App\Http\Controllers\Admin\Authority;

use App\Http\Controllers\Controller;
use App\Models\Rectify;
use App\Models\Department;
use App\Models\Designation;
use App\Models\MasterEmployee;
use Illuminate\Http\Request;

class CreateRectifyController extends Controller
{
    /**
     * Display all rectify records.
     */
    public function index()
    {
        $rectifies = Rectify::with([
            'department',
            'designation',
            'employee',
        ])->latest()->get();

        return view(
            'admin.authority.rectify.index',
            compact('rectifies')
        );
    }

    /**
     * Show create rectify form.
     */
    public function create()
    {
        $departments = Department::where('status', 1)
            ->pluck('name', 'id');

        $designations = Designation::where('status', 1)
            ->pluck('name', 'id');

        $employees = MasterEmployee::where('status', 1)
            ->pluck('name', 'id');

        return view(
            'admin.authority.rectify.create',
            compact('departments', 'designations', 'employees')
        );
    }

    /**
     * Store rectify records.
     */
    public function store(Request $request)
    {
        $request->validate([
            'department_id'    => 'required|array|min:1',
            'department_id.*'  => 'required|integer|exists:departments,id',
            'designation_id'   => 'nullable|array',
            'designation_id.*' => 'nullable|integer|exists:designations,id',
        ]);

        $departmentIds = $request->input('department_id', []);
        $designationIds = $request->input('designation_id', []);

        foreach ($departmentIds as $departmentId) {
            if (!empty($designationIds)) {
                foreach ($designationIds as $designationId) {
                    Rectify::create([
                        'department_id'  => $departmentId,
                        'designation_id' => $designationId ?: null,
                        'created_by'     => auth()->id(),
                        'updated_by'     => auth()->id(),
                    ]);
                }
            } else {
                Rectify::create([
                    'department_id'  => $departmentId,
                    'designation_id' => null,
                    'created_by'     => auth()->id(),
                    'updated_by'     => auth()->id(),
                ]);
            }
        }

        return redirect()->back()
            ->with('success', 'Rectify created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit($id)
    {
        $rectify = Rectify::findOrFail(decrypt($id));

        $departments = Department::where('status', 1)
            ->pluck('name', 'id');

        $designations = Designation::where('status', 1)
            ->pluck('name', 'id');

        $employees = MasterEmployee::where('status', 1)
            ->pluck('name', 'id');

        return view(
            'admin.authority.rectify.edit',
            compact('rectify', 'departments', 'designations', 'employees')
        );
    }

    /**
     * Update rectify record.
     */
    public function update(Request $request, $id)
    {
        $rectify = Rectify::findOrFail(decrypt($id));

        $request->validate([
            'department_id'  => 'required|integer|exists:departments,id',
            'designation_id' => 'nullable|integer|exists:designations,id',
        ]);

        $rectify->update([
            'department_id'  => $request->department_id,
            'designation_id' => $request->designation_id,
            'updated_by'     => auth()->id(),
        ]);

        return redirect()->back()
            ->with('success', 'Rectify updated successfully.');
    }

    /**
     * Delete rectify record.
     */
    public function destroy($id)
    {
        $rectify = Rectify::findOrFail(decrypt($id));

        $rectify->delete();

        return redirect()->back()
            ->with('success', 'Rectify deleted successfully.');
    }

    /**
     * AJAX: Get active designations for selected departments.
     */
    public function getDesignationsByDepartment(Request $request)
    {
        $departmentIds = $request->query('department_id', '');

        if (is_string($departmentIds)) {
            $departmentIds = explode(',', $departmentIds);
        }

        if (!is_array($departmentIds)) {
            return response()->json([]);
        }

        $departmentIds = array_values(array_filter(
            $departmentIds,
            function ($id) {
                return is_scalar($id)
                    && ctype_digit((string) $id)
                    && (int) $id > 0;
            }
        ));

        if (empty($departmentIds)) {
            return response()->json([]);
        }

        $designations = Designation::query()
            ->whereIn('department_id', $departmentIds)
            ->where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        return response()->json($designations);
    }
}