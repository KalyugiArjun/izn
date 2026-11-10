<?php

namespace App\Http\Controllers\Admin\WorksPaperworkProgressRegister;

use App\Http\Controllers\Controller;
use App\Models\ApprovalListing;
use App\Models\IdentifyNewWork;
use App\Models\ReviewEstimate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ApprovalListingController extends Controller
{
    private const MILESTONES = [
        'DRM approval',
        'Work uploaded on IRPSM',
        'Shortlisted by division',
        'Shortlisted by HQ',
        'GM approval',
    ];

    public const REMARKS = [
        'Not Required',
        'Awaited',
        'Pending',
        'Awaited from Concerned Department',
        'Awaited from HQ',
        'Approval Awaited',
    ];

    public function index(Request $request)
    {
        $query = ApprovalListing::with('identifyNewWork');

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
            'admin.master.works_paperwork_progress_register.approval-listing.index',
            compact('records')
        );
    }

    public function create()
    {
        $works = IdentifyNewWork::where('status', 1)
            ->orderBy('work_description_identification')
            ->get(['id', 'work_description_identification', 'date_of_identification_of_work']);

        $remarks = self::REMARKS;

        return view(
            'admin.master.works_paperwork_progress_register.approval-listing.create',
            compact('works', 'remarks')
        );
    }

    private function getNextMilestone($workId, $ignoreId = null)
    {
        $query = ApprovalListing::where('identify_new_work_id', $workId);

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
            return 'All Approval & Listing milestones have already been registered for this work.';
        }

        if ($milestoneIndex !== $nextMilestone['index']) {
            return 'Please register "' .
                $nextMilestone['milestone'] .
                '" first.';
        }

        return null;
    }

    public function milestoneStatus(Request $request, $workId)
    {
        $query = ApprovalListing::where('identify_new_work_id', $workId);

        if ($request->filled('ignore_id')) {
            try {
                $query->where('id', '!=', decrypt($request->query('ignore_id')));
            } catch (\Exception $e) {
                // invalid ignore_id, ignore silently
            }
        }

        $saved = $query->pluck('milestone')->toArray();

        return response()->json([
            'saved'      => $saved,
            'milestones' => self::MILESTONES,
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
            'date.required'   => __('Approval Date is required when milestone is marked completed.'),
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
                ->store('approval-listing', 'public');
        }

        ApprovalListing::create([
            'identify_new_work_id' => $request->identify_new_work_id,
            'milestone'            => $request->milestone,
            'is_completed'         => $isCompleted ? 1 : 0,
            'remark'               => $isCompleted ? null : $request->remark,
            'date'                 => $isCompleted ? $request->date : null,
            'days_taken'           => null,
            'supporting_document'  => $document,
        ]);

        return redirect()
            ->route('works-paperwork-progress-register.approval-listing.index')
            ->with('success', 'Approval & Listing added successfully.');
    }

    public function edit($id)
    {
        $id = decrypt($id);

        $record = ApprovalListing::with('identifyNewWork')
            ->findOrFail($id);

        $works = IdentifyNewWork::where('status', 1)
            ->orderBy('work_description_identification')
            ->get(['id', 'work_description_identification', 'date_of_identification_of_work']);

        $remarks = self::REMARKS;

        return view(
            'admin.master.works_paperwork_progress_register.approval-listing.edit',
            compact('record', 'works', 'remarks')
        );
    }

    public function update(Request $request, $id)
    {
        $id = decrypt($id);

        $record = ApprovalListing::findOrFail($id);

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
            'date.required'   => __('Approval Date is required when milestone is marked completed.'),
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
                ->store('approval-listing', 'public');
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
            ->route('works-paperwork-progress-register.approval-listing.index')
            ->with('success', 'Approval & Listing updated successfully.');
    }

    public function destroy($id)
    {
        $id = decrypt($id);

        $record = ApprovalListing::findOrFail($id);

        if ($record->supporting_document) {
            Storage::disk('public')->delete(
                $record->supporting_document
            );
        }

        $record->delete();

        return redirect()
            ->route('works-paperwork-progress-register.approval-listing.index')
            ->with('success', 'Approval & Listing deleted successfully.');
    }
}
