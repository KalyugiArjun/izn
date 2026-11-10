<?php

namespace App\Http\Controllers\Admin\WorksPaperworkProgressRegister;

use App\Http\Controllers\Controller;
use App\Models\ApprovalListing;
use App\Models\IdentifyNewWork;
use App\Models\Variation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VariationController extends Controller
{

    private const TOTAL_APPROVAL_MILESTONES = 5;

    private function getEligibleWorkIds()
    {
        return ApprovalListing::select('identify_new_work_id')
            ->groupBy('identify_new_work_id')
            ->havingRaw('COUNT(*) >= ?', [self::TOTAL_APPROVAL_MILESTONES])
            ->pluck('identify_new_work_id');
    }

    public function index(Request $request)
    {
        $query = Variation::with('identifyNewWork');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nature_of_variation', 'like', '%' . $search . '%')
                    ->orWhere('revised_tdc', 'like', '%' . $search . '%')
                    ->orWhereHas('identifyNewWork', function ($workQuery) use ($search) {
                        $workQuery->where(
                            'work_description_identification',
                            'like',
                            '%' . $search . '%'
                        );
                    });
            });
        }

        $records = $query
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.master.works_paperwork_progress_register.variation.index',
            compact('records')
        );
    }

    public function create()
    {
        $works = IdentifyNewWork::where('status', 1)
            ->orderBy('work_description_identification')
            ->get(['id', 'work_description_identification']);

        return view(
            'admin.master.works_paperwork_progress_register.variation.create',
            compact('works')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'identify_new_work_id' => 'required|exists:identify_new_works,id',
            'date_of_variation' => 'required|date_format:Y-m-d',
            'nature_of_variation' => 'required|string',
            'revised_tdc' => 'required|date_format:Y-m-d',
            'file' => 'nullable|mimes:pdf|max:10240',
        ]);

        $file = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file')->store('variation', 'public');
        }

        Variation::create([
            'identify_new_work_id' => $request->identify_new_work_id,
            'date_of_variation' => $request->date_of_variation,
            'nature_of_variation' => $request->nature_of_variation,
            'revised_tdc' => $request->revised_tdc,
            'file' => $file,
        ]);

        return redirect()
            ->route('works-paperwork-progress-register.variation.index')
            ->with('success', 'Variation added successfully.');
    }

    public function edit($id)
    {
        $id = decrypt($id);

        $record = Variation::with('identifyNewWork')->findOrFail($id);

        $eligibleWorkIds = $this->getEligibleWorkIds();

        // Record ka apna work id hamesha dropdown me dikhna chahiye, chahe
        // (edge case me) wo list se hat gaya ho.
        $works = IdentifyNewWork::where('status', 1)
            ->orderBy('work_description_identification')
            ->get(['id', 'work_description_identification']);

        return view(
            'admin.master.works_paperwork_progress_register.variation.edit',
            compact('record', 'works')
        );
    }

    public function update(Request $request, $id)
    {
        $id = decrypt($id);

        $record = Variation::findOrFail($id);

        $request->validate([
            'identify_new_work_id' => 'required|exists:identify_new_works,id',
            'date_of_variation' => 'required|date_format:Y-m-d',
            'nature_of_variation' => 'required|string',
            'revised_tdc' => 'required|date_format:Y-m-d',
            'file' => 'nullable|mimes:pdf|max:10240',
        ]);

        $file = $record->file;

        if ($request->hasFile('file')) {

            if ($record->file) {
                Storage::disk('public')->delete($record->file);
            }

            $file = $request->file('file')->store('variation', 'public');
        }

        $record->update([
            'identify_new_work_id' => $request->identify_new_work_id,
            'date_of_variation' => $request->date_of_variation,
            'nature_of_variation' => $request->nature_of_variation,
            'revised_tdc' => $request->revised_tdc,
            'file' => $file,
        ]);

        return redirect()
            ->route('works-paperwork-progress-register.variation.index')
            ->with('success', 'Variation updated successfully.');
    }

    public function destroy($id)
    {
        $id = decrypt($id);

        $record = Variation::findOrFail($id);

        if ($record->file) {
            Storage::disk('public')->delete($record->file);
        }

        $record->delete();

        return redirect()
            ->route('works-paperwork-progress-register.variation.index')
            ->with('success', 'Variation deleted successfully.');
    }
}
