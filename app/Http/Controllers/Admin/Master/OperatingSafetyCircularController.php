<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\OperatingSafetyCircular;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OperatingSafetyCircularController extends Controller
{
    public function index()
    {
        $circulars = OperatingSafetyCircular::latest()->get();

        return view('admin.master.operating_safety_circular.index', compact('circulars'));
    }

    public function create()
    {
        return view('admin.master.operating_safety_circular.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'circular_number' => 'required|string|max:100',
            'date_of_circular' => 'required|date',
            'subject' => 'required|string|max:255',
            'pdf_file' => 'required|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
        ]);

        $pdfFile = $request->file('pdf_file')->store(
            'uploads/operating_safety_circulars',
            'public'
        );

        OperatingSafetyCircular::create([
            'circular_number' => $request->circular_number,
            'date_of_circular' => $request->date_of_circular,
            'subject' => $request->subject,
            'pdf_file' => $pdfFile,
            'status' => $request->status,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Operating Safety Circular created successfully');
    }

    public function edit($id)
    {
        $circular = OperatingSafetyCircular::findOrFail(decrypt($id));

        return view('admin.master.operating_safety_circular.edit', compact('circular'));
    }

    public function update(Request $request, $id)
    {
        $circular = OperatingSafetyCircular::findOrFail(decrypt($id));

        $request->validate([
            'circular_number' => 'required|string|max:100',
            'date_of_circular' => 'required|date',
            'subject' => 'required|string|max:255',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
        ]);

        $data = [
            'circular_number' => $request->circular_number,
            'date_of_circular' => $request->date_of_circular,
            'subject' => $request->subject,
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ];

        if ($request->hasFile('pdf_file')) {
            if ($circular->pdf_file && Storage::disk('public')->exists($circular->pdf_file)) {
                Storage::disk('public')->delete($circular->pdf_file);
            }

            $data['pdf_file'] = $request->file('pdf_file')->store(
                'uploads/operating_safety_circulars',
                'public'
            );
        }

        $circular->update($data);

        return redirect()->back()->with('success', 'Operating Safety Circular updated successfully');
    }

    public function destroy($id)
    {
        $circular = OperatingSafetyCircular::findOrFail(decrypt($id));

        if ($circular->pdf_file && Storage::disk('public')->exists($circular->pdf_file)) {
            Storage::disk('public')->delete($circular->pdf_file);
        }

        $circular->delete();

        return redirect()->back()->with('success', 'Operating Safety Circular deleted successfully');
    }
}
