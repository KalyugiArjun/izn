<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\OperatingSafetyEpisode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OperatingSafetyEpisodeController extends Controller
{
    public function index()
    {
        $episodes = OperatingSafetyEpisode::latest()->get();

        return view('admin.master.operating_safety_documents.operating_safety_episode.index', compact('episodes'));
    }

    public function create()
    {
        return view('admin.master.operating_safety_documents.operating_safety_episode.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'episode_no' => 'required|string|max:100',
            'subject' => 'required|string|max:255',
            'pdf_file' => 'required|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
        ]);

        $pdf = $request->file('pdf_file')->store(
            'uploads/operating_safety_episodes',
            'public'
        );

        OperatingSafetyEpisode::create([
            'date' => $request->date,
            'episode_no' => $request->episode_no,
            'subject' => $request->subject,
            'pdf_file' => $pdf,
            'status' => $request->status,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Operating Safety Episode created successfully');
    }

    public function edit($id)
    {
        $episode = OperatingSafetyEpisode::findOrFail(decrypt($id));

        return view('admin.master.operating_safety_documents.operating_safety_episode.edit', compact('episode'));
    }

    public function update(Request $request, $id)
    {
        $episode = OperatingSafetyEpisode::findOrFail(decrypt($id));

        $request->validate([
            'date' => 'required|date',
            'episode_no' => 'required|string|max:100',
            'subject' => 'required|string|max:255',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
        ]);

        $data = [
            'date' => $request->date,
            'episode_no' => $request->episode_no,
            'subject' => $request->subject,
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ];

        if ($request->hasFile('pdf_file')) {
            if ($episode->pdf_file && Storage::disk('public')->exists($episode->pdf_file)) {
                Storage::disk('public')->delete($episode->pdf_file);
            }

            $data['pdf_file'] = $request->file('pdf_file')->store(
                'uploads/operating_safety_episodes',
                'public'
            );
        }

        $episode->update($data);

        return redirect()->back()->with('success', 'Operating Safety Episode updated successfully');
    }

    public function destroy($id)
    {
        $episode = OperatingSafetyEpisode::findOrFail(decrypt($id));

        if ($episode->pdf_file && Storage::disk('public')->exists($episode->pdf_file)) {
            Storage::disk('public')->delete($episode->pdf_file);
        }

        $episode->delete();

        return redirect()->back()->with('success', 'Operating Safety Episode deleted successfully');
    }
}
