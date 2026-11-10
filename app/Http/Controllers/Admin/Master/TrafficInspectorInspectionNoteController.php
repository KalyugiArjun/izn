<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\TrafficInspectorInspectionNote;
use App\Models\TypeOfInspection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrafficInspectorInspectionNoteController extends Controller
{
    public function index()
    {
        $notes = TrafficInspectorInspectionNote::with('inspection', 'inspectionType')->latest()->get();
        //dd($notes);
        return view('admin.master.traffic_inspector_inspection_note.index', compact('notes'));
    }

    public function create()
    {
        $type_of_inspections = TypeOfInspection::where('status', 1)->get();
        return view('admin.master.traffic_inspector_inspection_note.create', compact('type_of_inspections'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inspecting_traffic_inspector' => 'required|string|max:255',
            'type_of_inspection' => 'required',
            'place_of_inspection' => 'required|string|max:255',
            'date_of_inspection' => 'required|date',
            'inspection_note_pdf' => 'required|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
        ]);

        $pdfFile = $request->file('inspection_note_pdf')->store(
            'uploads/traffic_inspector_inspection_notes',
            'public'
        );

        TrafficInspectorInspectionNote::create([
            'inspecting_traffic_inspector' => $request->inspecting_traffic_inspector,
            'type_of_inspection' => $request->type_of_inspection,
            'place_of_inspection' => $request->place_of_inspection,
            'date_of_inspection' => $request->date_of_inspection,
            'inspection_note_pdf' => $pdfFile,
            'status' => $request->status,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Traffic Inspector Inspection Note created successfully');
    }

    public function edit($id)
    {
        $note = TrafficInspectorInspectionNote::findOrFail(decrypt($id));
    
        $type_of_inspections = TypeOfInspection::where('status', 1)->get();

        return view('admin.master.traffic_inspector_inspection_note.edit', compact('note', 'type_of_inspections'));
    }

    public function update(Request $request, $id)
    {
        $note = TrafficInspectorInspectionNote::findOrFail(decrypt($id));

        $request->validate([
            'inspecting_traffic_inspector' => 'required|string|max:255',
            'type_of_inspection' => 'required',
            'place_of_inspection' => 'required|string|max:255',
            'date_of_inspection' => 'required|date',
            'inspection_note_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
        ]);

        $data = [
            'inspecting_traffic_inspector' => $request->inspecting_traffic_inspector,
            'type_of_inspection' => $request->type_of_inspection,
            'place_of_inspection' => $request->place_of_inspection,
            'date_of_inspection' => $request->date_of_inspection,
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ];

        if ($request->hasFile('inspection_note_pdf')) {
            if ($note->inspection_note_pdf && Storage::disk('public')->exists($note->inspection_note_pdf)) {
                Storage::disk('public')->delete($note->inspection_note_pdf);
            }

            $data['inspection_note_pdf'] = $request->file('inspection_note_pdf')->store(
                'uploads/traffic_inspector_inspection_notes',
                'public'
            );
        }

        $note->update($data);

        return redirect()->back()->with('success', 'Traffic Inspector Inspection Note updated successfully');
    }

    public function destroy($id)
    {
        $note = TrafficInspectorInspectionNote::findOrFail(decrypt($id));

        if ($note->inspection_note_pdf && Storage::disk('public')->exists($note->inspection_note_pdf)) {
            Storage::disk('public')->delete($note->inspection_note_pdf);
        }

        $note->delete();

        return redirect()->back()->with('success', 'Traffic Inspector Inspection Note deleted successfully');
    }
}
