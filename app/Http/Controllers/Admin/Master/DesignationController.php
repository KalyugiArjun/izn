<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Models\Division;
use App\Models\Department;
use Illuminate\Http\Request;
use Auth;

class DesignationController extends Controller
{
    public function index()
    {
        if (Auth::user()->type == 'owner') {
            $designations = Designation::with(['division', 'department'])->latest()->get();
            return view('admin.master.designation.index', compact('designations'));
        } else {
            $user = Auth::user();
            $designations = Designation::with(['division', 'department'])
                ->where('department_id', $user->department_id)
                ->latest()
                ->get();

            return view('admin.master.designation.index', compact('designations'));
        }
    }

    public function create()
    {
        if (Auth::user()->type == 'owner') {
            $divisions   = Division::where('status', 1)->get();
            $departments = Department::where('status', 1)->get();
            return view('admin.master.designation.create', compact('divisions', 'departments'));
        } else {


            $user = Auth::user();

            $divisions   = Division::where('status', 1)->get();
            $departments = Department::where('status', 1)
                ->where('id', $user->department_id)
                ->get();


            return view('admin.master.designation.create', compact('divisions', 'departments'));
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'division_id'   => 'required|exists:divisions,id',
            'department_id' => 'required|exists:departments,id',
            'name'          => 'required',
            'position'      => 'nullable|string|max:255', // ✅ ADD
            // 'mobile'        => 'nullable|digits_between:10,20',
            // 'email'         => 'nullable|email'
        ]);

        Designation::create([
            'division_id'   => $request->division_id,
            'department_id' => $request->department_id,
            'name'          => $request->name,
            'position'      => $request->position, // ✅ ADD
            // 'mobile'        => $request->mobile,
            // 'email'         => $request->email,
            'status'        => $request->status ?? 1,
            'created_by'    => auth()->id(),
            'updated_by'    => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Designation created successfully');
    }

    public function edit($id)
    {
        if (Auth::user()->type == 'owner') {
            $designation = Designation::findOrFail(decrypt($id));
            $divisions   = Division::where('status', 1)->get();
            $departments = Department::where('status', 1)
                ->where('division_id', $designation->division_id)
                ->get();
            return view('admin.master.designation.edit', compact('designation', 'divisions', 'departments'));
        } else {


            $user = Auth::user();

            $divisions   = Division::where('status', 1)->get();
            $departments = Department::where('status', 1)
                ->where('id', $user->department_id)
                ->get();


            $designation = Designation::findOrFail(decrypt($id));

            return view('admin.master.designation.edit', compact('designation', 'divisions', 'departments'));
        }
    }

    public function update(Request $request, $id)
    {
        $designation = Designation::findOrFail(decrypt($id));

        $request->validate([
            'division_id'   => 'required|exists:divisions,id',
            'department_id' => 'required|exists:departments,id',
            'name'          => 'required',
            'position'      => 'nullable|string|max:255', // ✅ ADD
            // 'mobile'        => 'nullable|digits_between:10,20',
            // 'email'         => 'nullable|email'
        ]);

        $designation->update([
            'division_id'   => $request->division_id,
            'department_id' => $request->department_id,
            'name'          => $request->name,
            'position'      => $request->position, // ✅ ADD
            // 'mobile'        => $request->mobile,
            // 'email'         => $request->email,
            'status'        => $request->status ?? 1,
            'updated_by'    => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Designation updated successfully');
    }

    public function destroy($id)
    {
        $designation = Designation::findOrFail(decrypt($id));
        $designation->delete();

        return redirect()->back()->with('success', 'Designation deleted successfully');
    }
}
