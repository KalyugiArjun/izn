<?php

namespace App\Http\Controllers\Admin\WorksPaperworkProgressRegister;

use App\Http\Controllers\Controller;
use App\Models\Execution;
use App\Models\Sip;
use App\Models\IdentifyNewWork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ExecutionController extends Controller
{

    private const MILESTONES = [
        'Work commenced',
        'TDC — target date of completion',
    ];

    // ESP / SIP me total kitne milestones honi chahiye tabhi work yaha eligible hoga
    private const REQUIRED_ESP_SIP_MILESTONES = 2;

    public function index(Request $request)
    {
        $query = Execution::with('identifyNewWork');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('milestone', 'like', '%' . $search . '%')
                    ->orWhere('days_taken', 'like', '%' . $search . '%')
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
            'admin.master.works_paperwork_progress_register.execution.index',
            compact('records')
        );
    }

    /**
     * Sirf wahi works jinke ESP / SIP ke saare
     * milestones (4) store ho chuke hain.
     */
    private function eligibleWorkIds()
    {
        return Sip::selectRaw('identify_new_work_id, COUNT(*) as total')
            ->groupBy('identify_new_work_id')
            ->having('total', '>=', self::REQUIRED_ESP_SIP_MILESTONES)
            ->pluck('identify_new_work_id');
    }

    public function create()
    {
        $works = IdentifyNewWork::where('status', 1)
            ->orderBy('work_description_identification')
            ->get(['id', 'work_description_identification']);

        return view(
            'admin.master.works_paperwork_progress_register.execution.create',
            compact('works')
        );
    }

    private function getNextMilestone($workId, $ignoreId = null)
    {
        $query = Execution::where('identify_new_work_id', $workId);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $existingMilestones = $query
            ->pluck('milestone')
            ->toArray();

        foreach (self::MILESTONES as $index => $milestone) {
            if (!in_array($milestone, $existingMilestones, true)) {
                return [
                    'index' => $index,
                    'milestone' => $milestone,
                ];
            }
        }

        return null;
    }

    private function validateMilestoneSequence(
        $workId,
        $milestone,
        $ignoreId = null
    ) {
        $milestoneIndex = array_search(
            $milestone,
            self::MILESTONES,
            true
        );

        if ($milestoneIndex === false) {
            return 'Invalid milestone selected.';
        }

        $nextMilestone = $this->getNextMilestone(
            $workId,
            $ignoreId
        );

        if (!$nextMilestone) {
            return 'All Execution milestones have already been registered for this work.';
        }

        if ($milestoneIndex !== $nextMilestone['index']) {
            return 'Please register "' .
                $nextMilestone['milestone'] .
                '" first.';
        }

        return null;
    }

    /**
     * Milestone 0 (Work commenced) ke liye base date
     * = ESP / SIP table ki sabse aakhri (max) date.
     *
     * Milestone 1 (TDC) ke liye base date
     * = isi table ke pichhle (previous) milestone ki date.
     */
    private function getBaseDateForWork($workId, $milestoneIndex, $ignoreId = null)
    {
        if ($milestoneIndex === 0) {
            $maxDate = Sip::where('identify_new_work_id', $workId)->max('date');
            if (!$maxDate) {
                $work = IdentifyNewWork::find($workId);
                $maxDate = $work?->date_of_identification_of_work;
            }
            return $maxDate ? Carbon::parse($maxDate) : Carbon::now();
        }

        $previousMilestone = self::MILESTONES[$milestoneIndex - 1];

        $query = Execution::where('identify_new_work_id', $workId)
            ->where('milestone', $previousMilestone);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $previousRecord = $query->latest('id')->first();

        return $previousRecord ? Carbon::parse($previousRecord->date) : Carbon::now();
    }

    public function milestoneStatus(Request $request, $workId)
    {
        $query = Execution::where('identify_new_work_id', $workId);

        if ($request->filled('ignore_id')) {
            try {
                $query->where('id', '!=', decrypt($request->query('ignore_id')));
            } catch (\Exception $e) {
                // invalid ignore_id, ignore silently
            }
        }

        $recordsForWork = $query->get(['milestone', 'date']);

        $saved = $recordsForWork->pluck('milestone')->toArray();

        $savedDates = $recordsForWork->mapWithKeys(function ($row) {
            return [$row->milestone => $row->date ? $row->date->format('Y-m-d') : null];
        });

        $espSipMaxDate = Sip::where('identify_new_work_id', $workId)->max('date');
        if (!$espSipMaxDate) {
            $work = IdentifyNewWork::find($workId);
            $espSipMaxDate = $work?->date_of_identification_of_work ? $work->date_of_identification_of_work->format('Y-m-d') : date('Y-m-d');
        }

        return response()->json([
            'saved'         => $saved,
            'savedDates'    => $savedDates,
            'espSipMaxDate' => $espSipMaxDate,
            'milestones'    => self::MILESTONES,
        ]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'identify_new_work_id' => 'required|exists:identify_new_works,id',
            'milestone' => 'required|string|max:255',
            'date' => 'required|date_format:Y-m-d',
            'work_completion_date' => 'required|date_format:Y-m-d',
            'supporting_document' => 'nullable|mimes:pdf|max:10240',
        ]);

        $milestoneError = $this->validateMilestoneSequence(
            $request->identify_new_work_id,
            $request->milestone
        );

        if ($milestoneError) {
            return back()
                ->withInput()
                ->with('error', $milestoneError);
        }

        $milestoneIndex = array_search($request->milestone, self::MILESTONES, true);

        $baseDate = $this->getBaseDateForWork(
            $request->identify_new_work_id,
            $milestoneIndex
        );

        if (!$baseDate) {
            return back()
                ->withInput()
                ->with('error', 'Base date not found. Please ensure the previous stage is completed first.');
        }

        $currentDate = Carbon::createFromFormat(
            'Y-m-d',
            $request->date
        );

        if ($currentDate->lt($baseDate)) {
            return back()
                ->withInput()
                ->with('error', 'Date cannot be before ' . $baseDate->format('d-m-Y') . '.');
        }

        $daysTaken = $baseDate->diffInDays($currentDate);

        $document = null;

        if ($request->hasFile('supporting_document')) {
            $document = $request->file('supporting_document')
                ->store('execution', 'public');
        }

        Execution::create([
            'identify_new_work_id' => $request->identify_new_work_id,
            'milestone' => $request->milestone,
            'date' => $currentDate->format('Y-m-d'),
            'work_completion_date' => $request->work_completion_date,
            'days_taken' => $daysTaken,
            'supporting_document' => $document,
        ]);

        return redirect()
            ->route('works-paperwork-progress-register.execution.index')
            ->with('success', 'Execution added successfully.');
    }

    public function edit($id)
    {
        $id = decrypt($id);

        $record = Execution::with('identifyNewWork')
            ->findOrFail($id);

        $works = IdentifyNewWork::where('status', 1)
            ->orderBy('work_description_identification')
            ->get(['id', 'work_description_identification']);

        return view(
            'admin.master.works_paperwork_progress_register.execution.edit',
            compact('record', 'works')
        );
    }

    public function update(Request $request, $id)
    {
        $id = decrypt($id);

        $record = Execution::findOrFail($id);

        $request->validate([
            'identify_new_work_id' => 'required|exists:identify_new_works,id',
            'milestone' => 'required|string|max:255',
            'date' => 'required|date_format:Y-m-d',
            'work_completion_date' => 'required|date_format:Y-m-d',
            'supporting_document' => 'nullable|mimes:pdf|max:10240',
        ]);

        $milestoneError = $this->validateMilestoneSequence(
            $request->identify_new_work_id,
            $request->milestone,
            $record->id
        );

        if ($milestoneError) {
            return back()
                ->withInput()
                ->with('error', $milestoneError);
        }

        $milestoneIndex = array_search($request->milestone, self::MILESTONES, true);

        $baseDate = $this->getBaseDateForWork(
            $request->identify_new_work_id,
            $milestoneIndex,
            $record->id
        );

        if (!$baseDate) {
            return back()
                ->withInput()
                ->with('error', 'Base date not found. Please ensure the previous stage is completed first.');
        }

        $currentDate = Carbon::createFromFormat(
            'Y-m-d',
            $request->date
        );

        if ($currentDate->lt($baseDate)) {
            return back()
                ->withInput()
                ->with('error', 'Date cannot be before ' . $baseDate->format('d-m-Y') . '.');
        }

        $daysTaken = $baseDate->diffInDays($currentDate);

        $document = $record->supporting_document;

        if ($request->hasFile('supporting_document')) {

            if ($record->supporting_document) {
                Storage::disk('public')->delete(
                    $record->supporting_document
                );
            }

            $document = $request->file('supporting_document')
                ->store('execution', 'public');
        }

        $record->update([
            'identify_new_work_id' => $request->identify_new_work_id,
            'milestone' => $request->milestone,
            'date' => $currentDate->format('Y-m-d'),
            'work_completion_date' => $request->work_completion_date,
            'days_taken' => $daysTaken,
            'supporting_document' => $document,
        ]);

        return redirect()
            ->route('works-paperwork-progress-register.execution.index')
            ->with('success', 'Execution updated successfully.');
    }

    public function destroy($id)
    {
        $id = decrypt($id);

        $record = Execution::findOrFail($id);

        if ($record->supporting_document) {
            Storage::disk('public')->delete(
                $record->supporting_document
            );
        }

        $record->delete();

        return redirect()
            ->route('works-paperwork-progress-register.execution.index')
            ->with('success', 'Execution deleted successfully.');
    }
}
