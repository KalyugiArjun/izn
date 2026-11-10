<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\OperatingManualOriginal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OperatingManualOriginalController extends Controller
{
    public function index()
    {
        $records = OperatingManualOriginal::latest()->get();

        return view(
            'admin.master.operating_safety_documents.operating_manual.original.index',
            compact('records')
        );
    }

    public function create()
    {
        return view(
            'admin.master.operating_safety_documents.operating_manual.original.create'
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'original_name' => 'required|string|max:255',
            'original_pdf' => 'required|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
        ]);

        $pdf = $request->file('original_pdf')->store(
            'uploads/operating_manual/original',
            'public'
        );

        OperatingManualOriginal::create([
            'original_name' => $request->original_name,
            'original_pdf' => $pdf,
            'status' => $request->status,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with(
            'success',
            'Operating Manual Original created successfully'
        );
    }

    public function edit($id)
    {
        $record = OperatingManualOriginal::findOrFail(decrypt($id));

        return view(
            'admin.master.operating_safety_documents.operating_manual.original.edit',
            compact('record')
        );
    }

    public function update(Request $request, $id)
    {
        $record = OperatingManualOriginal::findOrFail(decrypt($id));

        $request->validate([
            'original_name' => 'required|string|max:255',
            'original_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
        ]);

        $data = [
            'original_name' => $request->original_name,
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ];

        if ($request->hasFile('original_pdf')) {
            if (
                $record->original_pdf &&
                Storage::disk('public')->exists($record->original_pdf)
            ) {
                Storage::disk('public')->delete($record->original_pdf);
            }

            $data['original_pdf'] = $request->file('original_pdf')->store(
                'uploads/operating_manual/original',
                'public'
            );
        }

        $record->update($data);

        return redirect()->back()->with(
            'success',
            'Operating Manual Original updated successfully'
        );
    }

    public function destroy($id)
    {
        $record = OperatingManualOriginal::findOrFail(decrypt($id));

        if (
            $record->original_pdf &&
            Storage::disk('public')->exists($record->original_pdf)
        ) {
            Storage::disk('public')->delete($record->original_pdf);
        }

        $record->delete();

        return redirect()->back()->with(
            'success',
            'Operating Manual Original deleted successfully'
        );
    }
}
