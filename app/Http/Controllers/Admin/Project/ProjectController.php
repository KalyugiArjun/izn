<?php

namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('department')->latest()->get();
        return view('admin.master.project.index', compact('projects'));
    }

    public function create()
    {
        $departments    = Department::where('status', 1)->pluck('name', 'id');
        $financial_years = $this->getFinancialYears();
        return view('admin.master.project.create',
            compact('departments', 'financial_years'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cost'            => 'required|numeric|min:0',
            'working_agencies'=> 'nullable|string|max:255',
            'sanction_date'   => 'nullable|date',
            'tdc_date'        => 'nullable|date',
            'financial_year'  => 'nullable|string|max:10',
            'plan_head'       => 'nullable|string|max:255',
            'department_id'   => 'nullable|exists:departments,id',
        ]);

        Project::create([
            'cost'            => $request->cost,
            'working_agencies'=> $request->working_agencies,
            'sanction_date'   => $request->sanction_date,
            'tdc_date'        => $request->tdc_date,
            'financial_year'  => $request->financial_year,
            'plan_head'       => $request->plan_head,
            'department_id'   => $request->department_id,
            'status'          => $request->status ?? 1,
            'created_by'      => auth()->id(),
            'updated_by'      => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Project created successfully.');
    }

    public function edit($id)
    {
        $project        = Project::findOrFail(decrypt($id));
        $departments    = Department::where('status', 1)->pluck('name', 'id');
        $financial_years = $this->getFinancialYears();
        return view('admin.master.project.edit',
            compact('project', 'departments', 'financial_years'));
    }

    public function update(Request $request, $id)
    {
        $project = Project::findOrFail(decrypt($id));

        $request->validate([
            'cost'            => 'required|numeric|min:0',
            'working_agencies'=> 'nullable|string|max:255',
            'sanction_date'   => 'nullable|date',
            'tdc_date'        => 'nullable|date',
            'financial_year'  => 'nullable|string|max:10',
            'plan_head'       => 'nullable|string|max:255',
            'department_id'   => 'nullable|exists:departments,id',
        ]);

        $project->update([
            'cost'            => $request->cost,
            'working_agencies'=> $request->working_agencies,
            'sanction_date'   => $request->sanction_date,
            'tdc_date'        => $request->tdc_date,
            'financial_year'  => $request->financial_year,
            'plan_head'       => $request->plan_head,
            'department_id'   => $request->department_id,
            'status'          => $request->status ?? 1,
            'updated_by'      => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Project updated successfully.');
    }

    public function destroy($id)
    {
        $project = Project::findOrFail(decrypt($id));
        $project->delete();
        return redirect()->back()->with('success', 'Project deleted successfully.');
    }

    /* ── Helper: Generate financial year list ── */

    private function getFinancialYears(): array
    {
        $years = [];
        $start = 2020;
        $end   = (int) date('Y') + 2;
        for ($y = $start; $y <= $end; $y++) {
            $label         = $y . '-' . ($y + 1);
            $years[$label] = $label;
        }
        return $years;
    }
}
