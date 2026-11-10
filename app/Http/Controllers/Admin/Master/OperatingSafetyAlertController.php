<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\OperatingSafetyAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OperatingSafetyAlertController extends Controller
{
    public function index()
    {
        $alerts = OperatingSafetyAlert::latest()->get();

        return view('admin.master.operating_safety_documents.operating_safety_alert.index', compact('alerts'));
    }

    public function create()
    {
        return view('admin.master.operating_safety_documents.operating_safety_alert.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'alert_no' => 'required|string|max:100',
            'subject' => 'required|string|max:255',
            'pdf_file' => 'required|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
        ]);

        $pdf = $request->file('pdf_file')->store(
            'uploads/operating_safety_alerts',
            'public'
        );

        OperatingSafetyAlert::create([
            'date' => $request->date,
            'alert_no' => $request->alert_no,
            'subject' => $request->subject,
            'pdf_file' => $pdf,
            'status' => $request->status,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Operating Safety Alert created successfully');
    }

    public function edit($id)
    {
        $alert = OperatingSafetyAlert::findOrFail(decrypt($id));

        return view('admin.master.operating_safety_documents.operating_safety_alert.edit', compact('alert'));
    }

    public function update(Request $request, $id)
    {
        $alert = OperatingSafetyAlert::findOrFail(decrypt($id));

        $request->validate([
            'date' => 'required|date',
            'alert_no' => 'required|string|max:100',
            'subject' => 'required|string|max:255',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
        ]);

        $data = [
            'date' => $request->date,
            'alert_no' => $request->alert_no,
            'subject' => $request->subject,
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ];

        if ($request->hasFile('pdf_file')) {
            if ($alert->pdf_file && Storage::disk('public')->exists($alert->pdf_file)) {
                Storage::disk('public')->delete($alert->pdf_file);
            }

            $data['pdf_file'] = $request->file('pdf_file')->store(
                'uploads/operating_safety_alerts',
                'public'
            );
        }

        $alert->update($data);

        return redirect()->back()->with('success', 'Operating Safety Alert updated successfully');
    }

    public function destroy($id)
    {
        $alert = OperatingSafetyAlert::findOrFail(decrypt($id));

        if ($alert->pdf_file && Storage::disk('public')->exists($alert->pdf_file)) {
            Storage::disk('public')->delete($alert->pdf_file);
        }

        $alert->delete();

        return redirect()->back()->with('success', 'Operating Safety Alert deleted successfully');
    }
}
