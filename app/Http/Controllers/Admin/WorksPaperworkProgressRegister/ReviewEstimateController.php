<?php

namespace App\Http\Controllers\Admin\WorksPaperworkProgressRegister;

use App\Http\Controllers\Controller;
use App\Models\Estimate;
use App\Models\IdentifyNewWork;
use App\Models\ReviewEstimate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReviewEstimateController extends Controller
{
    private const REQUIRED_DEPARTMENTS = [
        'Engineering',
        'TRD',
        'Signal',
        'Electrical/G',
    ];

    private const MILESTONE = 'Estimates finalized (after consolidating all departments)';

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
        $query = ReviewEstimate::with('identifyNewWork');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('milestone', 'like', '%' . $search . '%')
                    ->orWhere('remark', 'like', '%' . $search . '%')
                    ->orWhere('review_estimate_date', 'like', '%' . $search . '%')
                    ->orWhere('previous_department', 'like', '%' . $search . '%')
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
            'admin.master.works_paperwork_progress_register.review_estimate.index',
            compact('records')
        );
    }

    private function completedEstimateWorks($ignoreId = null)
    {
        $estimates = Estimate::select(
            'identify_new_work_id',
            'department',
            'estimate_received'
        )
            ->whereIn('department', self::REQUIRED_DEPARTMENTS)
            ->whereNotNull('estimate_received')
            ->where('estimate_received', '!=', '')
            ->get();

        $grouped = $estimates->groupBy('identify_new_work_id');
        $completedWorkIds = [];

        foreach ($grouped as $workId => $workEstimates) {
            $departments = $workEstimates
                ->pluck('department')
                ->unique()
                ->values()
                ->toArray();

            $hasAllDepartments = true;

            foreach (self::REQUIRED_DEPARTMENTS as $department) {
                if (!in_array($department, $departments, true)) {
                    $hasAllDepartments = false;
                    break;
                }
            }

            if (!$hasAllDepartments) {
                continue;
            }

            $reviewQuery = ReviewEstimate::where(
                'identify_new_work_id',
                $workId
            );

            if ($ignoreId) {
                $reviewQuery->where('id', '!=', $ignoreId);
            }

            if ($reviewQuery->exists()) {
                continue;
            }

            $latestEstimate = $workEstimates
                ->filter(function ($estimate) {
                    return !empty($estimate->estimate_received);
                })
                ->sortByDesc(function ($estimate) {
                    return Carbon::parse(
                        $estimate->estimate_received
                    )->startOfDay()->timestamp;
                })
                ->first();

            if (!$latestEstimate) {
                continue;
            }

            $completedWorkIds[$workId] = [
                'latest_estimate_date' => Carbon::parse(
                    $latestEstimate->estimate_received
                )->format('Y-m-d'),
                'latest_estimate_department' =>
                    $latestEstimate->department,
            ];
        }

        return $completedWorkIds;
    }

    public function create()
    {
        $completedWorks = $this->completedEstimateWorks();

        $works = IdentifyNewWork::where('status', 1)
            ->orderBy('work_description_identification')
            ->get([
                'id',
                'work_description_identification',
                'date_of_identification_of_work',
            ]);

        $works->each(function ($work) use ($completedWorks) {
            if (isset($completedWorks[$work->id])) {
                $work->latest_estimate_date =
                    $completedWorks[$work->id]['latest_estimate_date'];

                $work->latest_estimate_department =
                    $completedWorks[$work->id]['latest_estimate_department'];
            } else {
                $latestEstimate = Estimate::where('identify_new_work_id', $work->id)
                    ->whereNotNull('estimate_received')
                    ->where('estimate_received', '!=', '')
                    ->orderByDesc('estimate_received')
                    ->first();

                if ($latestEstimate) {
                    $work->latest_estimate_date = Carbon::parse($latestEstimate->estimate_received)->format('Y-m-d');
                    $work->latest_estimate_department = $latestEstimate->department;
                } else {
                    $work->latest_estimate_date = $work->date_of_identification_of_work ? $work->date_of_identification_of_work->format('Y-m-d') : date('Y-m-d');
                    $work->latest_estimate_department = 'Engineering';
                }
            }
        });

        $remarks = self::REMARKS;

        return view(
            'admin.master.works_paperwork_progress_register.review_estimate.create',
            compact('works', 'remarks')
        );
    }

    public function store(Request $request)
    {
        $isCompleted = $request->boolean('is_completed');

        $rules = [
            'identify_new_work_id' => 'required|exists:identify_new_works,id',
            'milestone'            => 'required|in:' . self::MILESTONE,
            'is_completed'         => 'nullable|boolean',
        ];

        if ($isCompleted) {
            $rules['review_estimate_date'] = 'required|date';
            $rules['remark']               = 'nullable|string';
        } else {
            $rules['review_estimate_date'] = 'nullable|date';
            $rules['remark']               = 'required|in:' . implode(',', self::REMARKS);
        }

        $request->validate($rules, [
            'review_estimate_date.required' => __('Review Estimate Date is required when milestone is marked completed.'),
            'remark.required'               => __('Please select a remark when milestone is not completed.'),
            'remark.in'                     => __('Please select a valid remark from the list.'),
        ]);

        $workId = $request->identify_new_work_id;

        $duplicate = ReviewEstimate::where(
            'identify_new_work_id',
            $workId
        )->exists();

        if ($duplicate) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Review Estimate has already been registered for the selected work.'
                );
        }

        $previousDepartment = null;
        $reviewDate = null;

        if ($isCompleted) {
            $latestEstimate = Estimate::where('identify_new_work_id', $workId)
                ->whereNotNull('estimate_received')
                ->where('estimate_received', '!=', '')
                ->orderByDesc('estimate_received')
                ->first();

            $previousDate = $latestEstimate && $latestEstimate->estimate_received
                ? Carbon::parse($latestEstimate->estimate_received)->startOfDay()
                : null;

            $previousDepartment = $latestEstimate ? $latestEstimate->department : null;

            $reviewDate = Carbon::parse(
                $request->review_estimate_date
            )->startOfDay();

            if (
                $previousDate &&
                $reviewDate->toDateString() < $previousDate->toDateString()
            ) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Review Estimate date cannot be before the latest department estimate received date (' . $previousDate->format('d-m-Y') . ').'
                    );
            }
        }

        ReviewEstimate::create([
            'identify_new_work_id' => $workId,
            'milestone'            => self::MILESTONE,
            'is_completed'         => $isCompleted ? 1 : 0,
            'remark'               => $isCompleted ? null : $request->remark,
            'review_estimate_date' => $isCompleted && $reviewDate ? $reviewDate->format('Y-m-d') : null,
            'days_taken'           => null,
            'previous_department'  => $previousDepartment,
        ]);

        return redirect()
            ->route(
                'works-paperwork-progress-register.review-estimate.index'
            )
            ->with(
                'success',
                'Review Estimate added successfully.'
            );
    }

    public function edit($id)
    {
        $record = ReviewEstimate::with('identifyNewWork')
            ->findOrFail(decrypt($id));

        $works = IdentifyNewWork::where('status', 1)
            ->orderBy('work_description_identification')
            ->get([
                'id',
                'work_description_identification',
                'date_of_identification_of_work',
            ]);

        $works->each(function ($work) use ($record) {
            $latestEstimate = Estimate::where(
                'identify_new_work_id',
                $work->id
            )
                ->whereNotNull('estimate_received')
                ->where('estimate_received', '!=', '')
                ->orderByDesc('estimate_received')
                ->first();

            if ($latestEstimate) {
                $work->latest_estimate_date =
                    Carbon::parse(
                        $latestEstimate->estimate_received
                    )->format('Y-m-d');

                $work->latest_estimate_department =
                    $latestEstimate->department;
            } else {
                $work->latest_estimate_date = $work->date_of_identification_of_work ? $work->date_of_identification_of_work->format('Y-m-d') : date('Y-m-d');
                $work->latest_estimate_department = 'Engineering';
            }
        });

        $remarks = self::REMARKS;

        return view(
            'admin.master.works_paperwork_progress_register.review_estimate.edit',
            compact('record', 'works', 'remarks')
        );
    }

    public function update(Request $request, $id)
    {
        $record = ReviewEstimate::findOrFail(
            decrypt($id)
        );

        $isCompleted = $request->boolean('is_completed');

        $rules = [
            'identify_new_work_id' => 'required|exists:identify_new_works,id',
            'milestone'            => 'required|in:' . self::MILESTONE,
            'is_completed'         => 'nullable|boolean',
        ];

        if ($isCompleted) {
            $rules['review_estimate_date'] = 'required|date';
            $rules['remark']               = 'nullable|string';
        } else {
            $rules['review_estimate_date'] = 'nullable|date';
            $rules['remark']               = 'required|in:' . implode(',', self::REMARKS);
        }

        $request->validate($rules, [
            'review_estimate_date.required' => __('Review Estimate Date is required when milestone is marked completed.'),
            'remark.required'               => __('Please select a remark when milestone is not completed.'),
            'remark.in'                     => __('Please select a valid remark from the list.'),
        ]);

        $workId = $request->identify_new_work_id;

        $duplicate = ReviewEstimate::where(
            'identify_new_work_id',
            $workId
        )
            ->where('id', '!=', $record->id)
            ->exists();

        if ($duplicate) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Review Estimate has already been registered for the selected work.'
                );
        }

        $previousDepartment = $record->previous_department;
        $reviewDate = null;

        if ($isCompleted) {
            $latestEstimate = Estimate::where(
                'identify_new_work_id',
                $workId
            )
                ->whereNotNull('estimate_received')
                ->where('estimate_received', '!=', '')
                ->orderByDesc('estimate_received')
                ->first();

            $previousDate = $latestEstimate && $latestEstimate->estimate_received
                ? Carbon::parse($latestEstimate->estimate_received)->startOfDay()
                : null;

            if ($latestEstimate) {
                $previousDepartment = $latestEstimate->department;
            }

            $reviewDate = Carbon::parse(
                $request->review_estimate_date
            )->startOfDay();

            if (
                $previousDate &&
                $reviewDate->toDateString() < $previousDate->toDateString()
            ) {
                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Review Estimate date cannot be before the latest department estimate received date (' . $previousDate->format('d-m-Y') . ').'
                    );
            }
        }

        $record->update([
            'identify_new_work_id' => $workId,
            'milestone'            => self::MILESTONE,
            'is_completed'         => $isCompleted ? 1 : 0,
            'remark'               => $isCompleted ? null : $request->remark,
            'review_estimate_date' => $isCompleted && $reviewDate ? $reviewDate->format('Y-m-d') : null,
            'days_taken'           => null,
            'previous_department'  => $previousDepartment,
        ]);

        return redirect()
            ->route(
                'works-paperwork-progress-register.review-estimate.index'
            )
            ->with(
                'success',
                'Review Estimate updated successfully.'
            );
    }

    public function destroy($id)
    {
        $record = ReviewEstimate::findOrFail(
            decrypt($id)
        );

        $record->delete();

        return redirect()
            ->route(
                'works-paperwork-progress-register.review-estimate.index'
            )
            ->with(
                'success',
                'Review Estimate deleted successfully.'
            );
    }
}
