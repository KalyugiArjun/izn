<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\SafetyCircular;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SafetyCircularController extends Controller
{
    public function index()
    {
        $circulars = SafetyCircular::latest()->get();

        return view('admin.master.safety_circular.index', compact('circulars'));
    }

    public function create()
    {
        return view('admin.master.safety_circular.create');
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
            'uploads/safety_circulars',
            'public'
        );

        SafetyCircular::create([
            'circular_number' => $request->circular_number,
            'date_of_circular' => $request->date_of_circular,
            'subject' => $request->subject,
            'pdf_file' => $pdfFile,
            'status' => $request->status,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Safety Circular created successfully');
    }

    public function edit($id)
    {
        $circular = SafetyCircular::findOrFail(decrypt($id));

        return view('admin.master.safety_circular.edit', compact('circular'));
    }

    public function update(Request $request, $id)
    {
        $circular = SafetyCircular::findOrFail(decrypt($id));

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
                'uploads/safety_circulars',
                'public'
            );
        }

        $circular->update($data);

        return redirect()->back()->with('success', 'Safety Circular updated successfully');
    }

    public function destroy($id)
    {
        $circular = SafetyCircular::findOrFail(decrypt($id));

        if ($circular->pdf_file && Storage::disk('public')->exists($circular->pdf_file)) {
            Storage::disk('public')->delete($circular->pdf_file);
        }

        $circular->delete();

        return redirect()->back()->with('success', 'Safety Circular deleted successfully');
    }
}
