<?php

namespace App\Http\Controllers\Admin\WorksPaperworkProgressRegister;

use App\Http\Controllers\Controller;
use App\Models\Esp;
use App\Models\Sip;
use App\Models\Tendering;
use App\Models\IdentifyNewWork;
use App\Models\Executionend;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class ExecutionendController extends Controller
{
    private const MILESTONES = [
        'Work commenced',
        'TDC — target date of completion',
    ];

    public function index()
    {
        $records = Executionend::with('identifyNewWork')
            ->get();

        return view('admin.master.works_paperwork_progress_register.executionend.index', compact('records'));
    }

    public function create()
    {
        $works = $this->getEligibleWorks();

        return view('admin.master.works_paperwork_progress_register.executionend.create', compact('works'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'identify_new_work_id' => 'required|exists:identify_new_works,id',
            'milestone' => 'required|in:' . implode(',', self::MILESTONES),
            'date' => 'required|date',
            'supporting_document' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $workId = $request->identify_new_work_id;
        $milestone = $request->milestone;
        $date = Carbon::parse($request->date);

        $status = $this->getMilestoneStatus($workId);

        if (in_array($milestone, $status['saved'], true)) {
            return back()
                ->withInput()
                ->withErrors([
                    'milestone' => 'This milestone has already been registered for the selected work.',
                ]);
        }

        $expected = null;

        foreach (self::MILESTONES as $item) {
            if (!in_array($item, $status['saved'], true)) {
                $expected = $item;
                break;
            }
        }

        if (!$expected) {
            return back()
                ->withInput()
                ->withErrors([
                    'milestone' => 'All Execution End milestones have already been registered for this work.',
                ]);
        }

        if ($milestone !== $expected) {
            return back()
                ->withInput()
                ->withErrors([
                    'milestone' => 'Please register "' . $expected . '" first.',
                ]);
        }

        $baseDate = $this->getBaseDate($milestone, $status);

        if ($baseDate && $date->lt(Carbon::parse($baseDate))) {
            return back()
                ->withInput()
                ->withErrors([
                    'date' => 'Date cannot be before the previous stage date.',
                ]);
        }

        $daysTaken = $baseDate
            ? Carbon::parse($baseDate)->diffInDays($date)
            : null;

        $documentPath = null;

        if ($request->hasFile('supporting_document')) {
            $documentPath = $request->file('supporting_document')
                ->store('executionend', 'public');
        }

        Executionend::create([
            'identify_new_work_id' => $workId,
            'milestone' => $milestone,
            'date' => $date->format('Y-m-d'),
            'execution_start_date' => $date->format('Y-m-d'),
            'execution_completion_date' => $milestone === 'TDC — target date of completion' ? $date->format('Y-m-d') : null,
            'days_taken' => $daysTaken,
            'supporting_document' => $documentPath,
        ]);

        return redirect()
            ->route('works-paperwork-progress-register.executionend.index')
            ->with('success', 'Execution End created successfully.');
    }

    public function edit($id)
    {
        $record = Executionend::findOrFail(decrypt($id));

        $works = $this->getEligibleWorks($record->identify_new_work_id);

        return view(
            'admin.master.works_paperwork_progress_register.executionend.edit',
            compact('record', 'works')
        );
    }

    public function update(Request $request, $id)
    {
        $record = Executionend::findOrFail(decrypt($id));

        $request->validate([
            'identify_new_work_id' => 'required|exists:identify_new_works,id',
            'milestone' => 'required|in:' . implode(',', self::MILESTONES),
            'date' => 'required|date',
            'supporting_document' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $workId = $request->identify_new_work_id;
        $milestone = $request->milestone;
        $date = Carbon::parse($request->date);

        $status = $this->getMilestoneStatus($workId, $record->id);

        if (in_array($milestone, $status['saved'], true)) {
            return back()
                ->withInput()
                ->withErrors([
                    'milestone' => 'This milestone has already been registered for the selected work.',
                ]);
        }

        $expected = null;

        foreach (self::MILESTONES as $item) {
            if (!in_array($item, $status['saved'], true)) {
                $expected = $item;
                break;
            }
        }

        if (!$expected) {
            $expected = $milestone;
        }

        if ($milestone !== $expected) {
            return back()
                ->withInput()
                ->withErrors([
                    'milestone' => 'Please register "' . $expected . '" first.',
                ]);
        }

        $baseDate = $this->getBaseDate($milestone, $status);

        if ($baseDate && $date->lt(Carbon::parse($baseDate))) {
            return back()
                ->withInput()
                ->withErrors([
                    'date' => 'Date cannot be before the previous stage date.',
                ]);
        }

        $daysTaken = $baseDate
            ? Carbon::parse($baseDate)->diffInDays($date)
            : null;

        $data = [
            'identify_new_work_id' => $workId,
            'milestone' => $milestone,
            'date' => $date->format('Y-m-d'),
            'execution_start_date' => $date->format('Y-m-d'),
            'execution_completion_date' => $milestone === 'TDC — target date of completion' ? $date->format('Y-m-d') : null,
            'days_taken' => $daysTaken,
        ];

        if ($request->hasFile('supporting_document')) {
            if ($record->supporting_document) {
                Storage::disk('public')->delete($record->supporting_document);
            }

            $data['supporting_document'] = $request->file('supporting_document')
                ->store('executionend', 'public');
        }

        $record->update($data);

        return redirect()
            ->route('works-paperwork-progress-register.executionend.index')
            ->with('success', 'Execution End updated successfully.');
    }

    public function destroy($id)
    {
        $record = Executionend::findOrFail(decrypt($id));

        if ($record->supporting_document) {
            Storage::disk('public')->delete($record->supporting_document);
        }

        $record->delete();

        return redirect()
            ->route('works-paperwork-progress-register.executionend.index')
            ->with('success', 'Execution End deleted successfully.');
    }

    public function milestoneStatus($workId, Request $request)
    {
        $ignoreId = null;

        if ($request->filled('ignore_id')) {
            try {
                $ignoreId = decrypt($request->ignore_id);
            } catch (\Throwable $e) {
                $ignoreId = null;
            }
        }

        return response()->json(
            $this->getMilestoneStatus($workId, $ignoreId)
        );
    }

    private function getMilestoneStatus($workId, $ignoreId = null)
    {
        $query = Executionend::where('identify_new_work_id', $workId);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $records = $query
            ->orderBy('id')
            ->get();

        $saved = $records
            ->pluck('milestone')
            ->values()
            ->toArray();

        $savedDates = [];

        foreach ($records as $record) {
            if ($record->milestone && $record->date) {
                $savedDates[$record->milestone] = Carbon::parse($record->date)->format('Y-m-d');
            }
        }

        $espSipMaxDate = $this->getEspSipMaxDate($workId);

        return [
            'saved' => $saved,
            'savedDates' => $savedDates,
            'espSipMaxDate' => $espSipMaxDate,
        ];
    }

    private function getBaseDate($milestone, $status)
    {
        $index = array_search($milestone, self::MILESTONES, true);

        if ($index === false) {
            return null;
        }

        if ($index === 0) {
            return $status['espSipMaxDate'];
        }

        $previousMilestone = self::MILESTONES[$index - 1];

        return $status['savedDates'][$previousMilestone] ?? null;
    }

    private function getEspSipMaxDate($workId)
    {
        $dates = [];

        $esp = Esp::where('identify_new_work_id', $workId)
            ->get();

        foreach ($esp as $record) {
            foreach ([
                'date',
                'call_letter_date',
                'completion_date',
            ] as $field) {
                if (!empty($record->{$field})) {
                    try {
                        $dates[] = Carbon::parse($record->{$field});
                    } catch (\Throwable $e) {
                    }
                }
            }
        }

        $sip = Sip::where('identify_new_work_id', $workId)
            ->get();

        foreach ($sip as $record) {
            foreach ([
                'date',
                'call_letter_date',
                'completion_date',
            ] as $field) {
                if (!empty($record->{$field})) {
                    try {
                        $dates[] = Carbon::parse($record->{$field});
                    } catch (\Throwable $e) {
                    }
                }
            }
        }

        if (empty($dates)) {
            $work = IdentifyNewWork::find($workId);
            return $work && $work->date_of_identification_of_work ? $work->date_of_identification_of_work->format('Y-m-d') : date('Y-m-d');
        }

        return collect($dates)
            ->sort()
            ->last()
            ->format('Y-m-d');
    }

    private function getEligibleWorks($currentWorkId = null)
    {
        return IdentifyNewWork::where('status', 1)
            ->orderBy('work_description_identification')
            ->get();
    }
}