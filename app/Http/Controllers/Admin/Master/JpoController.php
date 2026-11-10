<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Jpo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JpoController extends Controller
{
    public function index()
    {
        $jpos = Jpo::latest()->get();

        return view('admin.master.jpo.index', compact('jpos'));
    }

    public function create()
    {
        return view('admin.master.jpo.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'jpo_number' => 'required|string|max:100',
            'date_of_jpo' => 'required|date',
            'subject' => 'required|string|max:255',
            'pdf_file' => 'required|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
        ]);

        $pdfFile = $request->file('pdf_file')->store(
            'uploads/jpos',
            'public'
        );

        Jpo::create([
            'jpo_number' => $request->jpo_number,
            'date_of_jpo' => $request->date_of_jpo,
            'subject' => $request->subject,
            'pdf_file' => $pdfFile,
            'status' => $request->status,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'JPO created successfully');
    }

    public function edit($id)
    {
        $jpo = Jpo::findOrFail(decrypt($id));

        return view('admin.master.jpo.edit', compact('jpo'));
    }

    public function update(Request $request, $id)
    {
        $jpo = Jpo::findOrFail(decrypt($id));

        $request->validate([
            'jpo_number' => 'required|string|max:100',
            'date_of_jpo' => 'required|date',
            'subject' => 'required|string|max:255',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
        ]);

        $data = [
            'jpo_number' => $request->jpo_number,
            'date_of_jpo' => $request->date_of_jpo,
            'subject' => $request->subject,
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ];

        if ($request->hasFile('pdf_file')) {
            if ($jpo->pdf_file && Storage::disk('public')->exists($jpo->pdf_file)) {
                Storage::disk('public')->delete($jpo->pdf_file);
            }

            $data['pdf_file'] = $request->file('pdf_file')->store(
                'uploads/jpos',
                'public'
            );
        }

        $jpo->update($data);

        return redirect()->back()->with('success', 'JPO updated successfully');
    }

    public function destroy($id)
    {
        $jpo = Jpo::findOrFail(decrypt($id));

        if ($jpo->pdf_file && Storage::disk('public')->exists($jpo->pdf_file)) {
            Storage::disk('public')->delete($jpo->pdf_file);
        }

        $jpo->delete();

        return redirect()->back()->with('success', 'JPO deleted successfully');
    }
}
