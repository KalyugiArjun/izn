<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Gsr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GsrController extends Controller
{
    public function index()
    {
        $gsrs = Gsr::latest()->get();

        return view('admin.master.operating_safety_documents.gsr.index', compact('gsrs'));
    }

    public function create()
    {
        return view('admin.master.operating_safety_documents.gsr.create');
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
            'uploads/gsr',
            'public'
        );

        $correctionPdf = null;

        if ($request->hasFile('correction_slip_pdf')) {
            $correctionPdf = $request->file('correction_slip_pdf')->store(
                'uploads/gsr/correction_slips',
                'public'
            );
        }

        Gsr::create([
            'original_pdf' => $originalPdf,
            'correction_slip_no' => $request->correction_slip_no,
            'correction_slip_date' => $request->correction_slip_date,
            'correction_slip_pdf' => $correctionPdf,
            'status' => $request->status,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'G&SR created successfully');
    }

    public function edit($id)
    {
        $gsr = Gsr::findOrFail(decrypt($id));

        return view('admin.master.operating_safety_documents.gsr.edit', compact('gsr'));
    }

    public function update(Request $request, $id)
    {
        $gsr = Gsr::findOrFail(decrypt($id));

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
            if ($gsr->original_pdf && Storage::disk('public')->exists($gsr->original_pdf)) {
                Storage::disk('public')->delete($gsr->original_pdf);
            }

            $data['original_pdf'] = $request->file('original_pdf')->store(
                'uploads/gsr',
                'public'
            );
        }

        if ($request->hasFile('correction_slip_pdf')) {
            if ($gsr->correction_slip_pdf && Storage::disk('public')->exists($gsr->correction_slip_pdf)) {
                Storage::disk('public')->delete($gsr->correction_slip_pdf);
            }

            $data['correction_slip_pdf'] = $request->file('correction_slip_pdf')->store(
                'uploads/gsr/correction_slips',
                'public'
            );
        }

        $gsr->update($data);

        return redirect()->back()->with('success', 'G&SR updated successfully');
    }

    public function destroy($id)
    {
        $gsr = Gsr::findOrFail(decrypt($id));

        if ($gsr->original_pdf && Storage::disk('public')->exists($gsr->original_pdf)) {
            Storage::disk('public')->delete($gsr->original_pdf);
        }

        if ($gsr->correction_slip_pdf && Storage::disk('public')->exists($gsr->correction_slip_pdf)) {
            Storage::disk('public')->delete($gsr->correction_slip_pdf);
        }

        $gsr->delete();

        return redirect()->back()->with('success', 'G&SR deleted successfully');
    }
}
