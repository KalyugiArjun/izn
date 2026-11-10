<?php

namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Project;
use App\Models\ScopeOfWork;
use Illuminate\Http\Request;

class ScopeOfWorkController extends Controller
{
    public function index()
    {
        $scopes = ScopeOfWork::with(['project', 'department'])
            ->latest()
            ->get();

        return view('admin.master.scope_of_work.index', compact('scopes'));
    }

    public function create()
    {
        $projects    = Project::where('status', 1)->pluck('project_id' , 'id');
        $departments = Department::where('status', 1)->pluck('name', 'id');

        return view('admin.master.scope_of_work.create',
            compact('projects', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id'    => 'required|exists:projects,id',
            'scope_of_work' => 'required|string',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        ScopeOfWork::create([
            'project_id'    => $request->project_id,
            'scope_of_work' => $request->scope_of_work,
            'department_id' => $request->department_id,
            'status'        => $request->status ?? 1,
            'created_by'    => auth()->id(),
            'updated_by'    => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Scope of work created successfully.');
    }

    public function edit($id)
    {
        $scope       = ScopeOfWork::findOrFail(decrypt($id));
        $projects    = Project::where('status', 1)->pluck('project_id', 'id');
        $departments = Department::where('status', 1)->pluck('name', 'id');

        return view('admin.master.scope_of_work.edit',
            compact('scope', 'projects', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $scope = ScopeOfWork::findOrFail(decrypt($id));

        $request->validate([
            'project_id'    => 'required|exists:projects,id',
            'scope_of_work' => 'required|string',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $scope->update([
            'project_id'    => $request->project_id,
            'scope_of_work' => $request->scope_of_work,
            'department_id' => $request->department_id,
            'status'        => $request->status ?? 1,
            'updated_by'    => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Scope of work updated successfully.');
    }

    public function destroy($id)
    {
        $scope = ScopeOfWork::findOrFail(decrypt($id));
        $scope->delete();

        return redirect()->back()->with('success', 'Scope of work deleted successfully.');
    }
}
