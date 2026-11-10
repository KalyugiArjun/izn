<?php

namespace App\Http\Controllers\Admin\WorksPaperworkProgressRegister;

use App\Http\Controllers\Controller;
use App\Models\Esp;
use App\Models\Sip;
use App\Models\Tendering;
use App\Models\IdentifyNewWork;
use App\Models\FinanceVettingSanction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class EspSipController extends Controller
{
    /* ==============================================================
     |  MILESTONES & REMARKS
     |==============================================================*/

    private const ESP_MILESTONES = [
        'ESP finalized — division level',
        'ESP finalized — HQ level',
    ];

    private const SIP_MILESTONES = [
        'SIP finalized — division level',
        'SIP finalized — HQ level',
    ];

    public const REMARKS = [
        'Not Required',
        'Awaited',
        'Pending',
        'Awaited from Concerned Department',
        'Awaited from HQ',
        'Approval Awaited',
    ];

    private const FINANCE_VETTING_MILESTONE = 'Detailed estimate finalized';

    private const REQUIRED_TENDERING_MILESTONES_PER_DEPARTMENT = 3;

    private const TENDERING_DEPARTMENTS = [
        'Engineering',
        'TRD',
        'Signal',
        'Electrical/G',
    ];

    /* ==============================================================
     |  ESP — INDEX
     |==============================================================*/

    public function indexEsp(Request $request)
    {
        $query = Esp::with('identifyNewWork');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('milestone', 'like', '%' . $search . '%')
                    ->orWhere('remark', 'like', '%' . $search . '%')
                    ->orWhereHas('identifyNewWork', function ($workQuery) use ($search) {
                        $workQuery->where(
                            'work_description_identification',
                            'like',
                            '%' . $search . '%'
                        );
                    });
            });
        }

        $records = $query->latest('id')->paginate(10)->withQueryString();

        return view(
            'admin.master.works_paperwork_progress_register.esp-sip.esp.index',
            compact('records')
        );
    }

    /* ==============================================================
     |  SIP — INDEX
     |==============================================================*/

    public function indexSip(Request $request)
    {
        $query = Sip::with('identifyNewWork');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('milestone', 'like', '%' . $search . '%')
                    ->orWhere('remark', 'like', '%' . $search . '%')
                    ->orWhereHas('identifyNewWork', function ($workQuery) use ($search) {
                        $workQuery->where(
                            'work_description_identification',
                            'like',
                            '%' . $search . '%'
                        );
                    });
            });
        }

        $records = $query->latest('id')->paginate(10)->withQueryString();

        return view(
            'admin.master.works_paperwork_progress_register.esp-sip.sip.index',
            compact('records')
        );
    }

    /* ==============================================================
     |  ELIGIBILITY
     |==============================================================*/

    private function eligibleWorkIdsForEsp()
    {
        $totalDepartments = count(self::TENDERING_DEPARTMENTS);

        $completedDepartmentsPerWork = Tendering::selectRaw('identify_new_work_id, department, COUNT(DISTINCT milestone) as total')
            ->whereIn('department', self::TENDERING_DEPARTMENTS)
            ->groupBy('identify_new_work_id', 'department')
            ->having('total', '>=', self::REQUIRED_TENDERING_MILESTONES_PER_DEPARTMENT)
            ->get();

        return $completedDepartmentsPerWork
            ->groupBy('identify_new_work_id')
            ->filter(function ($departments) use ($totalDepartments) {
                return $departments->count() >= $totalDepartments;
            })
            ->keys();
    }

    private function eligibleWorkIdsForSip()
    {
        $totalMilestones = count(self::ESP_MILESTONES);

        return Esp::whereIn('milestone', self::ESP_MILESTONES)
            ->selectRaw('identify_new_work_id, COUNT(DISTINCT milestone) as total')
            ->groupBy('identify_new_work_id')
            ->having('total', '>=', $totalMilestones)
            ->pluck('identify_new_work_id');
    }

    /* ==============================================================
     |  COMMON HELPERS
     |==============================================================*/

    private function getNextMilestone(string $modelClass, array $milestones, $workId, $ignoreId = null)
    {
        $query = $modelClass::where('identify_new_work_id', $workId);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $existingMilestones = $query->pluck('milestone')->toArray();

        foreach ($milestones as $index => $milestone) {
            if (!in_array($milestone, $existingMilestones, true)) {
                return [
                    'index' => $index,
                    'milestone' => $milestone,
                ];
            }
        }

        return null;
    }

    private function validateMilestoneSequence(string $modelClass, array $milestones, $workId, $milestone, $ignoreId = null)
    {
        $milestoneIndex = array_search($milestone, $milestones, true);

        if ($milestoneIndex === false) {
            return 'Invalid milestone selected.';
        }

        $nextMilestone = $this->getNextMilestone($modelClass, $milestones, $workId, $ignoreId);

        if (!$nextMilestone) {
            return 'All milestones have already been registered for this work.';
        }

        if ($milestoneIndex !== $nextMilestone['index']) {
            return 'Please register "' . $nextMilestone['milestone'] . '" first.';
        }

        return null;
    }

    /* ==============================================================
     |  ESP — BASE DATE LOGIC
     |==============================================================*/

    private function getBaseDateForEsp($workId, $milestoneIndex, $ignoreId = null)
    {
        if ($milestoneIndex === 0) {
            $financeDate = FinanceVettingSanction::where('identify_new_work_id', $workId)
                ->where('milestone', self::FINANCE_VETTING_MILESTONE)
                ->max('date');

            if (!$financeDate) {
                $work = IdentifyNewWork::find($workId);
                $financeDate = $work?->date_of_identification_of_work;
            }

            return $financeDate ? Carbon::parse($financeDate) : Carbon::now();
        }

        $previousMilestone = self::ESP_MILESTONES[$milestoneIndex - 1];

        $query = Esp::where('identify_new_work_id', $workId)
            ->where('milestone', $previousMilestone);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $previousRecord = $query->latest('id')->first();

        return $previousRecord ? Carbon::parse($previousRecord->date) : Carbon::now();
    }

    /* ==============================================================
     |  SIP — BASE DATE LOGIC
     |==============================================================*/

    private function getBaseDateForSip($workId, $milestoneIndex, $ignoreId = null)
    {
        if ($milestoneIndex === 0) {
            $espMaxDate = Esp::where('identify_new_work_id', $workId)->max('date');

            if (!$espMaxDate) {
                $work = IdentifyNewWork::find($workId);
                $espMaxDate = $work?->date_of_identification_of_work;
            }

            return $espMaxDate ? Carbon::parse($espMaxDate) : Carbon::now();
        }

        $previousMilestone = self::SIP_MILESTONES[$milestoneIndex - 1];

        $query = Sip::where('identify_new_work_id', $workId)
            ->where('milestone', $previousMilestone);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $previousRecord = $query->latest('id')->first();

        return $previousRecord ? Carbon::parse($previousRecord->date) : null;
    }

    /* ==============================================================
     |  ESP — CRUD
     |==============================================================*/

    public function createEsp()
    {
        $works = IdentifyNewWork::where('status', 1)
            ->orderBy('work_description_identification')
            ->get(['id', 'work_description_identification']);

        $remarks = self::REMARKS;

        return view(
            'admin.master.works_paperwork_progress_register.esp-sip.esp.create',
            compact('works', 'remarks')
        );
    }

    public function milestoneStatusEsp(Request $request, $workId)
    {
        $query = Esp::where('identify_new_work_id', $workId);

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

        $financeVettingDate = FinanceVettingSanction::where('identify_new_work_id', $workId)
            ->where('milestone', self::FINANCE_VETTING_MILESTONE)
            ->max('date');

        if (!$financeVettingDate) {
            $work = IdentifyNewWork::find($workId);
            $financeVettingDate = $work?->date_of_identification_of_work ? $work->date_of_identification_of_work->format('Y-m-d') : date('Y-m-d');
        }

        return response()->json([
            'saved'      => $saved,
            'savedDates' => $savedDates,
            'baseDate'   => $financeVettingDate,
            'milestones' => self::ESP_MILESTONES,
        ]);
    }

    public function storeEsp(Request $request)
    {
        $isCompleted = $request->boolean('is_completed');

        $rules = [
            'identify_new_work_id' => 'required|exists:identify_new_works,id',
            'milestone'            => ['required', 'string', Rule::in(self::ESP_MILESTONES)],
            'is_completed'         => 'nullable|boolean',
            'supporting_document'  => 'nullable|mimes:pdf|max:10240',
        ];

        if ($isCompleted) {
            $rules['date']   = 'required|date_format:Y-m-d';
            $rules['remark'] = 'nullable|string';
        } else {
            $rules['date']   = 'nullable|date_format:Y-m-d';
            $rules['remark'] = 'required|in:' . implode(',', self::REMARKS);
        }

        $request->validate($rules, [
            'date.required'   => __('Date is required when milestone is marked completed.'),
            'remark.required' => __('Please select a remark when milestone is not completed.'),
            'remark.in'       => __('Please select a valid remark from the list.'),
        ]);

        $milestoneError = $this->validateMilestoneSequence(
            Esp::class,
            self::ESP_MILESTONES,
            $request->identify_new_work_id,
            $request->milestone
        );

        if ($milestoneError) {
            return back()
                ->withInput()
                ->with('error', $milestoneError);
        }

        $document = null;

        if ($isCompleted && $request->hasFile('supporting_document')) {
            $document = $request
                ->file('supporting_document')
                ->store('esp', 'public');
        }

        Esp::create([
            'identify_new_work_id' => $request->identify_new_work_id,
            'milestone'            => $request->milestone,
            'is_completed'         => $isCompleted ? 1 : 0,
            'remark'               => $isCompleted ? null : $request->remark,
            'date'                 => $isCompleted ? $request->date : null,
            'days_taken'           => null,
            'supporting_document'  => $document,
        ]);

        return redirect()
            ->route('works-paperwork-progress-register.esp-sip.esp.index')
            ->with('success', 'ESP added successfully.');
    }

    public function editEsp($id)
    {
        $id = decrypt($id);

        $record = Esp::with('identifyNewWork')->findOrFail($id);

        $works = IdentifyNewWork::where('status', 1)
            ->orderBy('work_description_identification')
            ->get(['id', 'work_description_identification']);

        $remarks = self::REMARKS;

        return view(
            'admin.master.works_paperwork_progress_register.esp-sip.esp.edit',
            compact('record', 'works', 'remarks')
        );
    }

    public function updateEsp(Request $request, $id)
    {
        $id = decrypt($id);

        $record = Esp::findOrFail($id);

        $isCompleted = $request->boolean('is_completed');

        $rules = [
            'identify_new_work_id' => 'required|exists:identify_new_works,id',
            'milestone'            => ['required', 'string', Rule::in(self::ESP_MILESTONES)],
            'is_completed'         => 'nullable|boolean',
            'supporting_document'  => 'nullable|mimes:pdf|max:10240',
        ];

        if ($isCompleted) {
            $rules['date']   = 'required|date_format:Y-m-d';
            $rules['remark'] = 'nullable|string';
        } else {
            $rules['date']   = 'nullable|date_format:Y-m-d';
            $rules['remark'] = 'required|in:' . implode(',', self::REMARKS);
        }

        $request->validate($rules, [
            'date.required'   => __('Date is required when milestone is marked completed.'),
            'remark.required' => __('Please select a remark when milestone is not completed.'),
            'remark.in'       => __('Please select a valid remark from the list.'),
        ]);

        $milestoneError = $this->validateMilestoneSequence(
            Esp::class,
            self::ESP_MILESTONES,
            $request->identify_new_work_id,
            $request->milestone,
            $record->id
        );

        if ($milestoneError) {
            return back()
                ->withInput()
                ->with('error', $milestoneError);
        }

        $document = $record->supporting_document;

        if ($isCompleted && $request->hasFile('supporting_document')) {
            if ($record->supporting_document) {
                Storage::disk('public')->delete(
                    $record->supporting_document
                );
            }

            $document = $request
                ->file('supporting_document')
                ->store('esp', 'public');
        }

        $record->update([
            'identify_new_work_id' => $request->identify_new_work_id,
            'milestone'            => $request->milestone,
            'is_completed'         => $isCompleted ? 1 : 0,
            'remark'               => $isCompleted ? null : $request->remark,
            'date'                 => $isCompleted ? $request->date : null,
            'days_taken'           => null,
            'supporting_document'  => $document,
        ]);

        return redirect()
            ->route('works-paperwork-progress-register.esp-sip.esp.index')
            ->with('success', 'ESP updated successfully.');
    }

    public function destroyEsp($id)
    {
        $id = decrypt($id);

        $record = Esp::findOrFail($id);

        if ($record->supporting_document) {
            Storage::disk('public')->delete($record->supporting_document);
        }

        $record->delete();

        return redirect()
            ->route('works-paperwork-progress-register.esp-sip.esp.index')
            ->with('success', 'ESP deleted successfully.');
    }

    /* ==============================================================
     |  SIP — CRUD
     |==============================================================*/

    public function createSip()
    {
        $works = IdentifyNewWork::where('status', 1)
            ->orderBy('work_description_identification')
            ->get(['id', 'work_description_identification']);

        $remarks = self::REMARKS;

        return view(
            'admin.master.works_paperwork_progress_register.esp-sip.sip.create',
            compact('works', 'remarks')
        );
    }

    public function milestoneStatusSip(Request $request, $workId)
    {
        $query = Sip::where('identify_new_work_id', $workId);

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

        $espMaxDate = Esp::where('identify_new_work_id', $workId)->max('date');
        if (!$espMaxDate) {
            $work = IdentifyNewWork::find($workId);
            $espMaxDate = $work?->date_of_identification_of_work ? $work->date_of_identification_of_work->format('Y-m-d') : date('Y-m-d');
        }

        return response()->json([
            'saved'      => $saved,
            'savedDates' => $savedDates,
            'baseDate'   => $espMaxDate,
            'milestones' => self::SIP_MILESTONES,
        ]);
    }

    public function storeSip(Request $request)
    {
        $isCompleted = $request->boolean('is_completed');

        $rules = [
            'identify_new_work_id' => 'required|exists:identify_new_works,id',
            'milestone'            => ['required', 'string', Rule::in(self::SIP_MILESTONES)],
            'is_completed'         => 'nullable|boolean',
            'supporting_document'  => 'nullable|mimes:pdf|max:10240',
        ];

        if ($isCompleted) {
            $rules['date']   = 'required|date_format:Y-m-d';
            $rules['remark'] = 'nullable|string';
        } else {
            $rules['date']   = 'nullable|date_format:Y-m-d';
            $rules['remark'] = 'required|in:' . implode(',', self::REMARKS);
        }

        $request->validate($rules, [
            'date.required'   => __('Date is required when milestone is marked completed.'),
            'remark.required' => __('Please select a remark when milestone is not completed.'),
            'remark.in'       => __('Please select a valid remark from the list.'),
        ]);

        $milestoneError = $this->validateMilestoneSequence(
            Sip::class,
            self::SIP_MILESTONES,
            $request->identify_new_work_id,
            $request->milestone
        );

        if ($milestoneError) {
            return back()
                ->withInput()
                ->with('error', $milestoneError);
        }

        $document = null;

        if ($isCompleted && $request->hasFile('supporting_document')) {
            $document = $request
                ->file('supporting_document')
                ->store('sip', 'public');
        }

        Sip::create([
            'identify_new_work_id' => $request->identify_new_work_id,
            'milestone'            => $request->milestone,
            'is_completed'         => $isCompleted ? 1 : 0,
            'remark'               => $isCompleted ? null : $request->remark,
            'date'                 => $isCompleted ? $request->date : null,
            'days_taken'           => null,
            'supporting_document'  => $document,
        ]);

        return redirect()
            ->route('works-paperwork-progress-register.esp-sip.sip.index')
            ->with('success', 'SIP added successfully.');
    }

    public function editSip($id)
    {
        $id = decrypt($id);

        $record = Sip::with('identifyNewWork')->findOrFail($id);

        $works = IdentifyNewWork::where('status', 1)
            ->orderBy('work_description_identification')
            ->get(['id', 'work_description_identification']);

        $remarks = self::REMARKS;

        return view(
            'admin.master.works_paperwork_progress_register.esp-sip.sip.edit',
            compact('record', 'works', 'remarks')
        );
    }

    public function updateSip(Request $request, $id)
    {
        $id = decrypt($id);

        $record = Sip::findOrFail($id);

        $isCompleted = $request->boolean('is_completed');

        $rules = [
            'identify_new_work_id' => 'required|exists:identify_new_works,id',
            'milestone'            => ['required', 'string', Rule::in(self::SIP_MILESTONES)],
            'is_completed'         => 'nullable|boolean',
            'supporting_document'  => 'nullable|mimes:pdf|max:10240',
        ];

        if ($isCompleted) {
            $rules['date']   = 'required|date_format:Y-m-d';
            $rules['remark'] = 'nullable|string';
        } else {
            $rules['date']   = 'nullable|date_format:Y-m-d';
            $rules['remark'] = 'required|in:' . implode(',', self::REMARKS);
        }

        $request->validate($rules, [
            'date.required'   => __('Date is required when milestone is marked completed.'),
            'remark.required' => __('Please select a remark when milestone is not completed.'),
            'remark.in'       => __('Please select a valid remark from the list.'),
        ]);

        $milestoneError = $this->validateMilestoneSequence(
            Sip::class,
            self::SIP_MILESTONES,
            $request->identify_new_work_id,
            $request->milestone,
            $record->id
        );

        if ($milestoneError) {
            return back()
                ->withInput()
                ->with('error', $milestoneError);
        }

        $document = $record->supporting_document;

        if ($isCompleted && $request->hasFile('supporting_document')) {
            if ($record->supporting_document) {
                Storage::disk('public')->delete(
                    $record->supporting_document
                );
            }

            $document = $request
                ->file('supporting_document')
                ->store('sip', 'public');
        }

        $record->update([
            'identify_new_work_id' => $request->identify_new_work_id,
            'milestone'            => $request->milestone,
            'is_completed'         => $isCompleted ? 1 : 0,
            'remark'               => $isCompleted ? null : $request->remark,
            'date'                 => $isCompleted ? $request->date : null,
            'days_taken'           => null,
            'supporting_document'  => $document,
        ]);

        return redirect()
            ->route('works-paperwork-progress-register.esp-sip.sip.index')
            ->with('success', 'SIP updated successfully.');
    }

    public function destroySip($id)
    {
        $id = decrypt($id);

        $record = Sip::findOrFail($id);

        if ($record->supporting_document) {
            Storage::disk('public')->delete($record->supporting_document);
        }

        $record->delete();

        return redirect()
            ->route('works-paperwork-progress-register.esp-sip.sip.index')
            ->with('success', 'SIP deleted successfully.');
    }
}
