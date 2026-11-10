<?php

namespace App\Http\Controllers\Admin\Authority;

use App\Http\Controllers\Controller;
use App\Models\Ph16Ti;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class Ph16TiController extends Controller
{
    public function index()
    {
        $ph16Tis = Ph16Ti::with(['department', 'designation'])
            ->latest()
            ->get();

        return view('admin.authority.ph16-ti.index', compact('ph16Tis'));
    }

    public function create()
    {
        $departments = Department::where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('admin.authority.ph16-ti.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'department_id' => ['required', 'array', 'min:1'],
            'department_id.*' => ['required', 'integer', 'exists:departments,id'],
            'designation_id' => ['required', 'array', 'min:1'],
            'designation_id.*' => ['required', 'integer', 'exists:designations,id'],
        ]);

        foreach ($data['department_id'] as $departmentId) {
            foreach ($data['designation_id'] as $designationId) {
                $validDesignation = Designation::where('id', $designationId)
                    ->where('department_id', $departmentId)
                    ->exists();

                if (!$validDesignation) {
                    return back()->withInput()->withErrors([
                        'designation_id' => 'Selected designation department se match nahi karti.',
                    ]);
                }

                Ph16Ti::firstOrCreate(
                    [
                        'department_id' => $departmentId,
                        'designation_id' => $designationId,
                    ],
                    [
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]
                );
            }
        }

        return redirect()->route('authority.ph16-ti.index')
            ->with('success', 'PH16 TI authority created successfully.');
    }

    public function edit($id)
    {
        $ph16Ti = Ph16Ti::findOrFail(decrypt($id));

        $departments = Department::where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        $designations = Designation::where('department_id', $ph16Ti->department_id)
            ->where('status', 1)
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('admin.authority.ph16-ti.edit', compact(
            'ph16Ti', 'departments', 'designations'
        ));
    }

    public function update(Request $request, $id)
    {
        $ph16Ti = Ph16Ti::findOrFail(decrypt($id));

        $data = $request->validate([
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'designation_id' => [
                'required',
                'integer',
                Rule::exists('designations', 'id')
                    ->where('department_id', $request->department_id),
            ],
        ]);

        $ph16Ti->update([
            'department_id' => $data['department_id'],
            'designation_id' => $data['designation_id'],
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('authority.ph16-ti.index')
            ->with('success', 'PH16 TI authority updated successfully.');
    }

    public function destroy($id)
    {
        Ph16Ti::findOrFail(decrypt($id))->delete();

        return redirect()->route('authority.ph16-ti.index')
            ->with('success', 'PH16 TI authority deleted successfully.');
    }

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
