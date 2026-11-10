<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\AccidentManual;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AccidentManualController extends Controller
{
    public function index()
    {
        $manuals = AccidentManual::latest()->get();

        return view('admin.master.operating_safety_documents.accident_manual.index', compact('manuals'));
    }

    public function create()
    {
        return view('admin.master.operating_safety_documents.accident_manual.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'original_pdf' => 'required|file|mimes:pdf|max:10240',
            'correction_slip_no' => 'nullable|string|max:100',
            'correction_slip_date' => 'nullable|date',
            'correction_slip_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
        ]);

        $originalPdf = $request->file('original_pdf')->store(
            'uploads/accident_manuals',
            'public'
        );

        $correctionPdf = null;

        if ($request->hasFile('correction_slip_pdf')) {
            $correctionPdf = $request->file('correction_slip_pdf')->store(
                'uploads/accident_manuals/correction_slips',
                'public'
            );
        }

        AccidentManual::create([
            'original_pdf' => $originalPdf,
            'correction_slip_no' => $request->correction_slip_no,
            'correction_slip_date' => $request->correction_slip_date,
            'correction_slip_pdf' => $correctionPdf,
            'status' => $request->status,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Accident Manual created successfully');
    }

    public function edit($id)
    {
        $manual = AccidentManual::findOrFail(decrypt($id));

        return view('admin.master.operating_safety_documents.accident_manual.edit', compact('manual'));
    }

    public function update(Request $request, $id)
    {
        $manual = AccidentManual::findOrFail(decrypt($id));

        $request->validate([
            'original_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'correction_slip_no' => 'nullable|string|max:100',
            'correction_slip_date' => 'nullable|date',
            'correction_slip_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
        ]);

        $data = [
            'correction_slip_no' => $request->correction_slip_no,
            'correction_slip_date' => $request->correction_slip_date,
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ];

        if ($request->hasFile('original_pdf')) {
            if ($manual->original_pdf && Storage::disk('public')->exists($manual->original_pdf)) {
                Storage::disk('public')->delete($manual->original_pdf);
            }

            $data['original_pdf'] = $request->file('original_pdf')->store(
                'uploads/accident_manuals',
                'public'
            );
        }

        if ($request->hasFile('correction_slip_pdf')) {
            if ($manual->correction_slip_pdf && Storage::disk('public')->exists($manual->correction_slip_pdf)) {
                Storage::disk('public')->delete($manual->correction_slip_pdf);
            }

            $data['correction_slip_pdf'] = $request->file('correction_slip_pdf')->store(
                'uploads/accident_manuals/correction_slips',
                'public'
            );
        }

        $manual->update($data);

        return redirect()->back()->with('success', 'Accident Manual updated successfully');
    }

    public function destroy($id)
    {
        $manual = AccidentManual::findOrFail(decrypt($id));

        if ($manual->original_pdf && Storage::disk('public')->exists($manual->original_pdf)) {
            Storage::disk('public')->delete($manual->original_pdf);
        }

        if ($manual->correction_slip_pdf && Storage::disk('public')->exists($manual->correction_slip_pdf)) {
            Storage::disk('public')->delete($manual->correction_slip_pdf);
        }

        $manual->delete();

        return redirect()->back()->with('success', 'Accident Manual deleted successfully');
    }
}
