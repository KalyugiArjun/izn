<?php

namespace App\Http\Controllers\Admin\StaffVisibility;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Designation;
use App\Models\StaffVisRefresher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefresherController extends Controller
{
    /**
     * Display all refresher visibility records.
     */
    public function index()
    {
        $records = StaffVisRefresher::with(['department', 'designation'])
            ->latest()
            ->get();

        return view('admin.staff-visibility.refresher.index', compact('records'));
    }

    /**
     * Show create modal.
     */
    public function create()
    {
        $departments = Department::where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('admin.staff-visibility.refresher.create', compact('departments'));
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
                StaffVisRefresher::firstOrCreate(
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
            ->with('success', 'Refresher visibility added successfully.');
    }

    /**
     * Show edit modal.
     */
    public function edit($id)
    {
        $record = StaffVisRefresher::findOrFail(decrypt($id));

        $departments = Department::where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        $designations = Designation::where('department_id', $record->department_id)
            ->where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('admin.staff-visibility.refresher.edit', compact(
            'record',
            'departments',
            'designations'
        ));
    }

    /**
     * Update record.
     */
    public function update(Request $request, $id)
    {
        $record = StaffVisRefresher::findOrFail(decrypt($id));

        $request->validate([
            'department_id'  => 'required|integer|exists:departments,id',
            'designation_id' => 'required|integer|exists:designations,id',
        ]);

        $record->update([
            'department_id'  => $request->department_id,
            'designation_id' => $request->designation_id,
            'updated_by'     => Auth::id(),
        ]);

        return redirect()->back()
            ->with('success', 'Refresher visibility updated successfully.');
    }

    /**
     * Delete record.
     */
    public function destroy($id)
    {
        $record = StaffVisRefresher::findOrFail(decrypt($id));
        $record->delete();

        return redirect()->back()
            ->with('success', 'Refresher visibility deleted successfully.');
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

