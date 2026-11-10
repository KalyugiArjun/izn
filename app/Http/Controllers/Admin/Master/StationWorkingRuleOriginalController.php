<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\StationWorkingRuleOriginal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StationWorkingRuleOriginalController extends Controller
{
    public function index()
    {
        $records = StationWorkingRuleOriginal::with('station')
            ->latest()
            ->get();

        return view(
            'admin.master.operating_safety_documents.station_working_rule.original.index',
            compact('records')
        );
    }

    public function create()
    {
        $stations = DB::table('stations')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view(
            'admin.master.operating_safety_documents.station_working_rule.original.create',
            compact('stations')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'station_id' => 'required|exists:stations,id',
            'original_name' => 'required|string|max:255',
            'original_pdf' => 'required|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
        ]);

        $pdf = $request->file('original_pdf')->store(
            'uploads/station_working_rule/original',
            'public'
        );

        StationWorkingRuleOriginal::create([
            'station_id' => $request->station_id,
            'original_name' => $request->original_name,
            'original_pdf' => $pdf,
            'status' => $request->status,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with(
            'success',
            'Station Working Rule Original created successfully'
        );
    }

    public function edit($id)
    {
        $record = StationWorkingRuleOriginal::findOrFail(decrypt($id));

        $stations = DB::table('stations')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view(
            'admin.master.operating_safety_documents.station_working_rule.original.edit',
            compact('record', 'stations')
        );
    }

    public function update(Request $request, $id)
    {
        $record = StationWorkingRuleOriginal::findOrFail(decrypt($id));

        $request->validate([
            'station_id' => 'required|exists:stations,id',
            'original_name' => 'required|string|max:255',
            'original_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
        ]);

        $data = [
            'station_id' => $request->station_id,
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
                'uploads/station_working_rule/original',
                'public'
            );
        }

        $record->update($data);

        return redirect()->back()->with(
            'success',
            'Station Working Rule Original updated successfully'
        );
    }

    public function destroy($id)
    {
        $record = StationWorkingRuleOriginal::findOrFail(decrypt($id));

        if (
            $record->original_pdf &&
            Storage::disk('public')->exists($record->original_pdf)
        ) {
            Storage::disk('public')->delete($record->original_pdf);
        }

        $record->delete();

        return redirect()->back()->with(
            'success',
            'Station Working Rule Original deleted successfully'
        );
    }
}
