<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\EOEmployee;
use App\Models\Diesellobby;
use App\Models\Designation;
use Illuminate\Http\Request;

class EOEmployeeController extends Controller
{
    public function index()
    {
        $employees = EOEmployee::with([
            'dieselLobby',
            'designation'
        ])->latest()->get();

        return view('admin.master.eo_employee.index', compact('employees'));
    }

    public function create()
    {
        $stations = Diesellobby::where('status', 1)
            ->orderBy('name')
            ->get();

        $designations = Designation::where('status', 1)
            ->orderBy('name')
            ->get();

        return view('admin.master.eo_employee.create', compact(
            'stations',
            'designations'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'diesel_lobby_id' => 'required|exists:diesellobbies,id',
            'hrms_id' => 'required|string|max:100',
            'name' => 'required|string|max:255',
            'designation_id' => 'required|exists:designations,id',
            'status' => 'required|in:0,1',
        ]);

        EOEmployee::create([
            'diesel_lobby_id' => $request->diesel_lobby_id,
            'hrms_id' => $request->hrms_id,
            'name' => $request->name,
            'designation_id' => $request->designation_id,
            'status' => $request->status,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'EO Employee created successfully');
    }

    public function edit($id)
    {
        $employee = EOEmployee::findOrFail(decrypt($id));

        $stations = Diesellobby::where('status', 1)
            ->orderBy('name')
            ->get();

        $designations = Designation::where('status', 1)
            ->orderBy('name')
            ->get();

        return view('admin.master.eo_employee.edit', compact(
            'employee',
            'stations',
            'designations'
        ));
    }

    public function update(Request $request, $id)
    {
        $employee = EOEmployee::findOrFail(decrypt($id));

        $request->validate([
            'diesel_lobby_id' => 'required|exists:diesellobbies,id',
            'hrms_id' => 'required|string|max:100',
            'name' => 'required|string|max:255',
            'designation_id' => 'required|exists:designations,id',
            'status' => 'required|in:0,1',
        ]);

        $employee->update([
            'diesel_lobby_id' => $request->diesel_lobby_id,
            'hrms_id' => $request->hrms_id,
            'name' => $request->name,
            'designation_id' => $request->designation_id,
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'EO Employee updated successfully');
    }

    public function destroy($id)
    {
        $employee = EOEmployee::findOrFail(decrypt($id));

        $employee->delete();

        return redirect()->back()->with('success', 'EO Employee deleted successfully');
    }
}
