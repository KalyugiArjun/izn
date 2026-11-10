<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\CaseStudy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CaseStudyController extends Controller
{
    public function index()
    {
        $caseStudies = CaseStudy::latest()->get();

        return view(
            'admin.master.operating_safety_documents.case_study.index',
            compact('caseStudies')
        );
    }

    public function create()
    {
        $departments = DB::table('departments')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view(
            'admin.master.operating_safety_documents.case_study.create',
            compact('departments')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'case_study_title' => 'required|string|max:255',
            'incident_date' => 'required|date',
            'location' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'type_of_irregularity' => 'required|string|max:255',
            'risk_level' => 'required|in:LOW,MEDIUM,HIGH,CRITICAL',
            'key_learning' => 'required|string',
            'pdf_file' => 'required|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
        ]);

        $pdfFile = $request->file('pdf_file')->store(
            'uploads/case_study',
            'public'
        );

        CaseStudy::create([
            'case_study_title' => $request->case_study_title,
            'incident_date' => $request->incident_date,
            'location' => $request->location,
            'department_id' => $request->department_id,
            'type_of_irregularity' => $request->type_of_irregularity,
            'risk_level' => $request->risk_level,
            'key_learning' => $request->key_learning,
            'pdf_file' => $pdfFile,
            'status' => $request->status,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with(
            'success',
            'Case Study created successfully'
        );
    }

    public function edit($id)
    {
        $caseStudy = CaseStudy::findOrFail(decrypt($id));

        $departments = DB::table('departments')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view(
            'admin.master.operating_safety_documents.case_study.edit',
            compact('caseStudy', 'departments')
        );
    }

    public function update(Request $request, $id)
    {
        $caseStudy = CaseStudy::findOrFail(decrypt($id));

        $request->validate([
            'case_study_title' => 'required|string|max:255',
            'incident_date' => 'required|date',
            'location' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'type_of_irregularity' => 'required|string|max:255',
            'risk_level' => 'required|in:LOW,MEDIUM,HIGH,CRITICAL',
            'key_learning' => 'required|string',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
        ]);

        $data = [
            'case_study_title' => $request->case_study_title,
            'incident_date' => $request->incident_date,
            'location' => $request->location,
            'department_id' => $request->department_id,
            'type_of_irregularity' => $request->type_of_irregularity,
            'risk_level' => $request->risk_level,
            'key_learning' => $request->key_learning,
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ];

        if ($request->hasFile('pdf_file')) {
            if (
                $caseStudy->pdf_file &&
                Storage::disk('public')->exists($caseStudy->pdf_file)
            ) {
                Storage::disk('public')->delete($caseStudy->pdf_file);
            }

            $data['pdf_file'] = $request->file('pdf_file')->store(
                'uploads/case_study',
                'public'
            );
        }

        $caseStudy->update($data);

        return redirect()->back()->with(
            'success',
            'Case Study updated successfully'
        );
    }

    public function destroy($id)
    {
        $caseStudy = CaseStudy::findOrFail(decrypt($id));

        if (
            $caseStudy->pdf_file &&
            Storage::disk('public')->exists($caseStudy->pdf_file)
        ) {
            Storage::disk('public')->delete($caseStudy->pdf_file);
        }

        $caseStudy->delete();

        return redirect()->back()->with(
            'success',
            'Case Study deleted successfully'
        );
    }
}
