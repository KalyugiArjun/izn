<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\StationWorkingRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StationWorkingRuleController extends Controller
{
    public function index()
    {
        $rules = DB::table('station_working_rules')
            ->leftJoin('stations', 'stations.id', '=', 'station_working_rules.station_id')
            ->select(
                'station_working_rules.*',
                'stations.name as station_name'
            )
            ->latest('station_working_rules.id')
            ->get();

        return view(
            'admin.master.operating_safety_documents.station_working_rule.index',
            compact('rules')
        );
    }

    public function create()
    {
        $stations = DB::table('stations')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view(
            'admin.master.operating_safety_documents.station_working_rule.create',
            compact('stations')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'station_id' => 'required|exists:stations,id',
            'original_pdf' => 'required|file|mimes:pdf|max:10240',
            'correction_slip_no' => 'nullable|string|max:100',
            'correction_slip_date' => 'nullable|date',
            'correction_slip_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
        ]);

        $originalPdf = $request->file('original_pdf')->store(
            'uploads/station_working_rules',
            'public'
        );

        $correctionPdf = null;

        if ($request->hasFile('correction_slip_pdf')) {
            $correctionPdf = $request->file('correction_slip_pdf')->store(
                'uploads/station_working_rules/correction_slips',
                'public'
            );
        }

        StationWorkingRule::create([
            'station_id' => $request->station_id,
            'original_pdf' => $originalPdf,
            'correction_slip_no' => $request->correction_slip_no,
            'correction_slip_date' => $request->correction_slip_date,
            'correction_slip_pdf' => $correctionPdf,
            'status' => $request->status,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with(
            'success',
            'Station Working Rule created successfully'
        );
    }

    public function edit($id)
    {
        $rule = StationWorkingRule::findOrFail(decrypt($id));

        $stations = DB::table('stations')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view(
            'admin.master.operating_safety_documents.station_working_rule.edit',
            compact('rule', 'stations')
        );
    }

    public function update(Request $request, $id)
    {
        $rule = StationWorkingRule::findOrFail(decrypt($id));

        $request->validate([
            'station_id' => 'required|exists:stations,id',
            'original_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'correction_slip_no' => 'nullable|string|max:100',
            'correction_slip_date' => 'nullable|date',
            'correction_slip_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
        ]);

        $data = [
            'station_id' => $request->station_id,
            'correction_slip_no' => $request->correction_slip_no,
            'correction_slip_date' => $request->correction_slip_date,
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ];

        if ($request->hasFile('original_pdf')) {
            if (
                $rule->original_pdf &&
                Storage::disk('public')->exists($rule->original_pdf)
            ) {
                Storage::disk('public')->delete($rule->original_pdf);
            }

            $data['original_pdf'] = $request->file('original_pdf')->store(
                'uploads/station_working_rules',
                'public'
            );
        }

        if ($request->hasFile('correction_slip_pdf')) {
            if (
                $rule->correction_slip_pdf &&
                Storage::disk('public')->exists($rule->correction_slip_pdf)
            ) {
                Storage::disk('public')->delete($rule->correction_slip_pdf);
            }

            $data['correction_slip_pdf'] = $request->file('correction_slip_pdf')->store(
                'uploads/station_working_rules/correction_slips',
                'public'
            );
        }

        $rule->update($data);

        return redirect()->back()->with(
            'success',
            'Station Working Rule updated successfully'
        );
    }

    public function destroy($id)
    {
        $rule = StationWorkingRule::findOrFail(decrypt($id));

        if (
            $rule->original_pdf &&
            Storage::disk('public')->exists($rule->original_pdf)
        ) {
            Storage::disk('public')->delete($rule->original_pdf);
        }

        if (
            $rule->correction_slip_pdf &&
            Storage::disk('public')->exists($rule->correction_slip_pdf)
        ) {
            Storage::disk('public')->delete($rule->correction_slip_pdf);
        }

        $rule->delete();

        return redirect()->back()->with(
            'success',
            'Station Working Rule deleted successfully'
        );
    }
}
