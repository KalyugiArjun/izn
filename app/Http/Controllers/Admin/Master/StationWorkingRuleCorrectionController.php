<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\StationWorkingRuleCorrection;
use App\Models\StationWorkingRuleOriginal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StationWorkingRuleCorrectionController extends Controller
{
    public function index()
    {
        $records = StationWorkingRuleCorrection::with([
            'original.station'
        ])
            ->latest()
            ->get();

        return view(
            'admin.master.operating_safety_documents.station_working_rule.correction.index',
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
            'admin.master.operating_safety_documents.station_working_rule.correction.create',
            compact('stations')
        );
    }

    public function getOriginals($stationId)
    {

        $originals = StationWorkingRuleOriginal::where('station_id', $stationId)
            ->where('status', 1)
            ->select(
                'id',
                'original_name',
                'original_pdf'
            )
            ->orderBy('original_name', 'asc')
            ->get();

        return response()->json($originals);
    }

    public function getOriginal($id)
    {
        $original = StationWorkingRuleOriginal::findOrFail($id);

        return response()->json([
            'id' => $original->id,
            'original_name' => $original->original_name,
            'original_pdf' => $original->original_pdf,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'station_id' => 'required|exists:stations,id',
            'original_id' => 'required|exists:station_working_rule_originals,id',
            'correction_slip_no' => 'required|string|max:100',
            'correction_slip_pdf' => 'required|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
            'issue_date' => 'required|date',
            'imposition_date' => 'required|date',
        ]);

        $original = StationWorkingRuleOriginal::where('id', $request->original_id)
            ->where('station_id', $request->station_id)
            ->where('status', 1)
            ->firstOrFail();

        $pdf = $request->file('correction_slip_pdf')->store(
            'uploads/station_working_rule/corrections',
            'public'
        );

        StationWorkingRuleCorrection::create([
            'original_id' => $original->id,
            'correction_slip_no' => $request->correction_slip_no,
            'issue_date' => $request->issue_date,
            'imposition_date' => $request->imposition_date,
            'correction_slip_pdf' => $pdf,
            'status' => $request->status,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with(
            'success',
            'Station Working Rule Correction created successfully'
        );
    }

    public function edit($id)
    {
        $record = StationWorkingRuleCorrection::with('original.station')
            ->findOrFail(decrypt($id));

        $stations = DB::table('stations')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        $selectedStationId = $record->original->station_id;

        $originals = StationWorkingRuleOriginal::where(
            'station_id',
            $selectedStationId
        )
            ->where('status', 1)
            ->orderBy('original_name')
            ->get();

        return view(
            'admin.master.operating_safety_documents.station_working_rule.correction.edit',
            compact(
                'record',
                'stations',
                'originals',
                'selectedStationId'
            )
        );
    }

    public function update(Request $request, $id)
    {
        $record = StationWorkingRuleCorrection::with('original')
            ->findOrFail(decrypt($id));

        $request->validate([
            'station_id' => 'required|exists:stations,id',
            'original_id' => 'required|exists:station_working_rule_originals,id',
            'correction_slip_no' => 'required|string|max:100',
            'correction_slip_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
            'issue_date' => 'required|date',
            'imposition_date' => 'required|date',
        ]);

        $original = StationWorkingRuleOriginal::where('id', $request->original_id)
            ->where('station_id', $request->station_id)
            ->where('status', 1)
            ->firstOrFail();

        $data = [
            'original_id' => $original->id,
            'correction_slip_no' => $request->correction_slip_no,
            'issue_date' => $request->issue_date,
            'imposition_date' => $request->imposition_date,
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
                'uploads/station_working_rule/corrections',
                'public'
            );
        }

        $record->update($data);

        return redirect()->back()->with(
            'success',
            'Station Working Rule Correction updated successfully'
        );
    }

    public function destroy($id)
    {
        $record = StationWorkingRuleCorrection::findOrFail(decrypt($id));

        if (
            $record->correction_slip_pdf &&
            Storage::disk('public')->exists($record->correction_slip_pdf)
        ) {
            Storage::disk('public')->delete($record->correction_slip_pdf);
        }

        $record->delete();

        return redirect()->back()->with(
            'success',
            'Station Working Rule Correction deleted successfully'
        );
    }
}
