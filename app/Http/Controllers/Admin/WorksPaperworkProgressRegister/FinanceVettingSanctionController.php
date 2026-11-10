<?php

namespace App\Http\Controllers\Admin\WorksPaperworkProgressRegister;

use App\Http\Controllers\Controller;
use App\Models\FinanceVettingSanction;
use App\Models\ApprovalListing;
use App\Models\IdentifyNewWork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class FinanceVettingSanctionController extends Controller
{
    private const MILESTONES = [
        'Divisional finance vetting',
        'HQ finance vetting',
        'Work sanctioned',
        'Detailed estimate finalized',
    ];

    public const REMARKS = [
        'Not Required',
        'Awaited',
        'Pending',
        'Awaited from Concerned Department',
        'Awaited from HQ',
        'Approval Awaited',
    ];

    private const REQUIRED_APPROVAL_MILESTONES = 5;

    public function index(Request $request)
    {
        $query = FinanceVettingSanction::with('identifyNewWork');

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

        $records = $query
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.master.works_paperwork_progress_register.finance-vetting-sanction.index',
            compact('records')
        );
    }

    private function eligibleWorkIds()
    {
        return ApprovalListing::selectRaw('identify_new_work_id, COUNT(*) as total')
            ->groupBy('identify_new_work_id')
            ->having('total', '>=', self::REQUIRED_APPROVAL_MILESTONES)
            ->pluck('identify_new_work_id');
    }

    public function create()
    {
        $works = IdentifyNewWork::where('status', 1)
            ->orderBy('work_description_identification')
            ->get(['id', 'work_description_identification']);

        $remarks = self::REMARKS;

        return view(
            'admin.master.works_paperwork_progress_register.finance-vetting-sanction.create',
            compact('works', 'remarks')
        );
    }

    private function getNextMilestone($workId, $ignoreId = null)
    {
        $query = FinanceVettingSanction::where('identify_new_work_id', $workId);

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
            return 'All Finance Vetting & Sanction milestones have already been registered for this work.';
        }

        if ($milestoneIndex !== $nextMilestone['index']) {
            return 'Please register "' .
                $nextMilestone['milestone'] .
                '" first.';
        }

        return null;
    }

    private function getBaseDateForWork($workId, $milestoneIndex, $ignoreId = null)
    {
        if ($milestoneIndex === 0) {
            $maxDate = ApprovalListing::where('identify_new_work_id', $workId)->max('date');
            if (!$maxDate) {
                $work = IdentifyNewWork::find($workId);
                $maxDate = $work?->date_of_identification_of_work;
            }
            return $maxDate ? Carbon::parse($maxDate) : Carbon::now();
        }

        $previousMilestone = self::MILESTONES[$milestoneIndex - 1];

        $query = FinanceVettingSanction::where('identify_new_work_id', $workId)
            ->where('milestone', $previousMilestone);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $previousRecord = $query->latest('id')->first();

        return $previousRecord ? Carbon::parse($previousRecord->date) : Carbon::now();
    }

    public function milestoneStatus(Request $request, $workId)
    {
        $query = FinanceVettingSanction::where('identify_new_work_id', $workId);

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

        $approvalMaxDate = ApprovalListing::where('identify_new_work_id', $workId)->max('date');
        if (!$approvalMaxDate) {
            $work = IdentifyNewWork::find($workId);
            $approvalMaxDate = $work?->date_of_identification_of_work ? $work->date_of_identification_of_work->format('Y-m-d') : date('Y-m-d');
        }

        return response()->json([
            'saved'           => $saved,
            'savedDates'      => $savedDates,
            'approvalMaxDate' => $approvalMaxDate,
            'milestones'      => self::MILESTONES,
        ]);
    }

    public function store(Request $request)
    {
        $isCompleted = $request->boolean('is_completed');

        $rules = [
            'identify_new_work_id' => 'required|exists:identify_new_works,id',
            'milestone'            => 'required|string|max:255',
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
            $document = $request->file('supporting_document')
                ->store('finance-vetting-sanction', 'public');
        }

        FinanceVettingSanction::create([
            'identify_new_work_id' => $request->identify_new_work_id,
            'milestone'            => $request->milestone,
            'is_completed'         => $isCompleted ? 1 : 0,
            'remark'               => $isCompleted ? null : $request->remark,
            'date'                 => $isCompleted ? $request->date : null,
            'days_taken'           => null,
            'supporting_document'  => $document,
        ]);

        return redirect()
            ->route('works-paperwork-progress-register.finance-vetting-sanction.index')
            ->with('success', 'Finance Vetting & Sanction added successfully.');
    }

    public function edit($id)
    {
        $id = decrypt($id);

        $record = FinanceVettingSanction::with('identifyNewWork')
            ->findOrFail($id);

        $works = IdentifyNewWork::where('status', 1)
            ->orderBy('work_description_identification')
            ->get(['id', 'work_description_identification']);

        $remarks = self::REMARKS;

        return view(
            'admin.master.works_paperwork_progress_register.finance-vetting-sanction.edit',
            compact('record', 'works', 'remarks')
        );
    }

    public function update(Request $request, $id)
    {
        $id = decrypt($id);

        $record = FinanceVettingSanction::findOrFail($id);

        $isCompleted = $request->boolean('is_completed');

        $rules = [
            'identify_new_work_id' => 'required|exists:identify_new_works,id',
            'milestone'            => 'required|string|max:255',
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

            $document = $request->file('supporting_document')
                ->store('finance-vetting-sanction', 'public');
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
            ->route('works-paperwork-progress-register.finance-vetting-sanction.index')
            ->with('success', 'Finance Vetting & Sanction updated successfully.');
    }

    public function destroy($id)
    {
        $id = decrypt($id);

        $record = FinanceVettingSanction::findOrFail($id);

        if ($record->supporting_document) {
            Storage::disk('public')->delete(
                $record->supporting_document
            );
        }

        $record->delete();

        return redirect()
            ->route('works-paperwork-progress-register.finance-vetting-sanction.index')
            ->with('success', 'Finance Vetting & Sanction deleted successfully.');
    }
}
