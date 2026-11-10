<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\OperatingSafetyDrive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OperatingSafetyDriveController extends Controller
{
    public function index()
    {
        $drives = OperatingSafetyDrive::latest()->get();

        return view('admin.master.operating_safety_documents.operating_safety_drive.index', compact('drives'));
    }

    public function create()
    {
        return view('admin.master.operating_safety_documents.operating_safety_drive.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'drive_no' => 'required|string|max:100',
            'subject' => 'required|string|max:255',
            'pdf_file' => 'required|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
        ]);

        $pdf = $request->file('pdf_file')->store(
            'uploads/operating_safety_drives',
            'public'
        );

        OperatingSafetyDrive::create([
            'date' => $request->date,
            'drive_no' => $request->drive_no,
            'subject' => $request->subject,
            'pdf_file' => $pdf,
            'status' => $request->status,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Operating Safety Drive created successfully');
    }

    public function edit($id)
    {
        $drive = OperatingSafetyDrive::findOrFail(decrypt($id));

        return view('admin.master.operating_safety_documents.operating_safety_drive.edit', compact('drive'));
    }

    public function update(Request $request, $id)
    {
        $drive = OperatingSafetyDrive::findOrFail(decrypt($id));

        $request->validate([
            'date' => 'required|date',
            'drive_no' => 'required|string|max:100',
            'subject' => 'required|string|max:255',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
        ]);

        $data = [
            'date' => $request->date,
            'drive_no' => $request->drive_no,
            'subject' => $request->subject,
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ];

        if ($request->hasFile('pdf_file')) {
            if ($drive->pdf_file && Storage::disk('public')->exists($drive->pdf_file)) {
                Storage::disk('public')->delete($drive->pdf_file);
            }

            $data['pdf_file'] = $request->file('pdf_file')->store(
                'uploads/operating_safety_drives',
                'public'
            );
        }

        $drive->update($data);

        return redirect()->back()->with('success', 'Operating Safety Drive updated successfully');
    }

    public function destroy($id)
    {
        $drive = OperatingSafetyDrive::findOrFail(decrypt($id));

        if ($drive->pdf_file && Storage::disk('public')->exists($drive->pdf_file)) {
            Storage::disk('public')->delete($drive->pdf_file);
        }

        $drive->delete();

        return redirect()->back()->with('success', 'Operating Safety Drive deleted successfully');
    }
}
