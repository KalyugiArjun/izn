<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\OperatingManualCorrection;
use App\Models\OperatingManualOriginal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OperatingManualCorrectionController extends Controller
{
    public function index()
    {
        $records = OperatingManualCorrection::with('original')
            ->latest()
            ->get();

        return view(
            'admin.master.operating_safety_documents.operating_manual.correction.index',
            compact('records')
        );
    }

    public function create()
    {
        $originals = OperatingManualOriginal::where('status', 1)
            ->orderBy('original_name')
            ->get();

        return view(
            'admin.master.operating_safety_documents.operating_manual.correction.create',
            compact('originals')
        );
    }

    public function getOriginal($id)
    {
        $original = OperatingManualOriginal::findOrFail($id);

        return response()->json([
            'id' => $original->id,
            'original_name' => $original->original_name,
            'original_pdf' => $original->original_pdf,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'original_id' => 'required|exists:operating_manual_originals,id',
            'correction_slip_no' => 'required|string|max:100',
            'correction_slip_date' => 'required|date',
            'correction_slip_pdf' => 'required|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
        ]);

        $pdf = $request->file('correction_slip_pdf')->store(
            'uploads/operating_manual/corrections',
            'public'
        );

        OperatingManualCorrection::create([
            'original_id' => $request->original_id,
            'correction_slip_no' => $request->correction_slip_no,
            'correction_slip_date' => $request->correction_slip_date,
            'correction_slip_pdf' => $pdf,
            'status' => $request->status,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with(
            'success',
            'Operating Manual Correction created successfully'
        );
    }

    public function edit($id)
    {
        $record = OperatingManualCorrection::with('original')
            ->findOrFail(decrypt($id));

        $originals = OperatingManualOriginal::where('status', 1)
            ->orderBy('original_name')
            ->get();

        return view(
            'admin.master.operating_safety_documents.operating_manual.correction.edit',
            compact('record', 'originals')
        );
    }

    public function update(Request $request, $id)
    {
        $record = OperatingManualCorrection::findOrFail(decrypt($id));

        $request->validate([
            'original_id' => 'required|exists:operating_manual_originals,id',
            'correction_slip_no' => 'required|string|max:100',
            'correction_slip_date' => 'required|date',
            'correction_slip_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
        ]);

        $data = [
            'original_id' => $request->original_id,
            'correction_slip_no' => $request->correction_slip_no,
            'correction_slip_date' => $request->correction_slip_date,
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ];

        if ($request->hasFile('correction_slip_pdf')) {
            if (
                $record->correction_slip_pdf &&
                Storage::disk('public')->exists($record->correction_slip_pdf)
            ) {
                Storage::disk('public')->delete($record->correction_slip_pdf);
            }

            $data['correction_slip_pdf'] = $request->file(
                'correction_slip_pdf'
            )->store(
                'uploads/operating_manual/corrections',
                'public'
            );
        }

        $record->update($data);

        return redirect()->back()->with(
            'success',
            'Operating Manual Correction updated successfully'
        );
    }

    public function destroy($id)
    {
        $record = OperatingManualCorrection::findOrFail(decrypt($id));

        if (
            $record->correction_slip_pdf &&
            Storage::disk('public')->exists($record->correction_slip_pdf)
        ) {
            Storage::disk('public')->delete($record->correction_slip_pdf);
        }

        $record->delete();

        return redirect()->back()->with(
            'success',
            'Operating Manual Correction deleted successfully'
        );
    }
}
