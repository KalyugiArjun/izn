<?php

namespace App\Http\Controllers\Admin\WorksPaperworkProgressRegister;

use App\Http\Controllers\Controller;
use App\Models\Estimate;
use App\Models\IdentifyNewWork;
use App\Models\JointSurvey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EstimateController extends Controller
{
    /**
     * Joint Survey ke saare milestones ka count.
     */
    private const REQUIRED_JOINT_SURVEY_MILESTONES = 4;

    /**
     * Department ka fixed order.
     */
    private const DEPARTMENTS = [
        'Engineering',
        'TRD',
        'Signal',
        'Electrical/G',
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
        $query = Estimate::with('identifyNewWork');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('department', 'like', '%' . $search . '%')
                    ->orWhere('remark', 'like', '%' . $search . '%')
                    ->orWhere('cost', 'like', '%' . $search . '%')
                    ->orWhere('estimate_called', 'like', '%' . $search . '%')
                    ->orWhere('estimate_received', 'like', '%' . $search . '%')
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
            'admin.master.works_paperwork_progress_register.estimates.index',
            compact('records')
        );
    }

    /**
     * Fully Joint Survey completed works.
     */
    private function fullyJointSurveyedWorkIds()
    {
        return JointSurvey::select('identify_new_work_id')
            ->groupBy('identify_new_work_id')
            ->havingRaw(
                'COUNT(DISTINCT milestone) = ?',
                [self::REQUIRED_JOINT_SURVEY_MILESTONES]
            )
            ->pluck('identify_new_work_id');
    }

    /**
     * Given work ke liye next expected department.
     */
    private function getNextDepartment($workId, $ignoreId = null)
    {
        $query = Estimate::where(
            'identify_new_work_id',
            $workId
        );

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $existingDepartments = $query
            ->pluck('department')
            ->toArray();

        foreach (self::DEPARTMENTS as $index => $department) {
            if (!in_array($department, $existingDepartments, true)) {
                return [
                    'index' => $index,
                    'department' => $department,
                ];
            }
        }

        return null;
    }

    /**
     * Department sequence validation.
     *
     * Order:
     * Engineering
     * TRD
     * Signal
     * Electrical/G
     */
    private function validateDepartmentSequence(
        $workId,
        $department,
        $ignoreId = null
    ) {
        $departmentIndex = array_search(
            $department,
            self::DEPARTMENTS,
            true
        );

        if ($departmentIndex === false) {
            return 'Invalid department selected.';
        }

        $query = Estimate::where(
            'identify_new_work_id',
            $workId
        );

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        $existingDepartments = $query
            ->pluck('department')
            ->toArray();

        if (in_array($department, $existingDepartments, true)) {
            return 'An estimate for this department has already been registered for the selected work.';
        }

        $nextDepartment = $this->getNextDepartment(
            $workId,
            $ignoreId
        );

        if (!$nextDepartment) {
            return 'Estimates for all departments have already been registered for this work.';
        }

        if ($departmentIndex !== $nextDepartment['index']) {
            return 'Please first submit the "' .
                $nextDepartment['department'] .
                '" department estimate before selecting this one.';
        }

        return null;
    }

    /**
     * AJAX:
     * Selected work ke liye saved departments return karega.
     */
    public function departmentStatus(Request $request, $workId)
    {
        $query = Estimate::where(
            'identify_new_work_id',
            $workId
        );

        if ($request->filled('ignore_id')) {
            try {
                $ignoreId = decrypt(
                    $request->query('ignore_id')
                );

                $query->where('id', '!=', $ignoreId);
            } catch (\Exception $e) {
                // Invalid ignore_id ko silently ignore karein.
            }
        }

        $saved = $query
            ->pluck('department')
            ->toArray();

        return response()->json([
            'saved' => $saved,
            'departments' => self::DEPARTMENTS,
        ]);
    }

    public function create()
    {
        $works = IdentifyNewWork::where('status', 1)
            ->orderBy('work_description_identification')
            ->get([
                'id',
                'work_description_identification',
            ]);

        $remarks = self::REMARKS;

        return view(
            'admin.master.works_paperwork_progress_register.estimates.create',
            compact('works', 'remarks')
        );
    }

    public function store(Request $request)
    {
        $isCompleted = $request->boolean('is_completed');

        $rules = [
            'identify_new_work_id' => 'required|exists:identify_new_works,id',
            'department'           => 'required|in:Engineering,TRD,Signal,Electrical/G',
            'is_completed'         => 'nullable|boolean',
            'cost'                 => 'nullable|numeric|min:0',
        ];

        if ($isCompleted) {
            $rules['estimate_called']   = 'required|date';
            $rules['call_letter']       = 'nullable|mimes:pdf|max:10240';
            $rules['estimate_received'] = 'nullable|date|after_or_equal:estimate_called';
            $rules['received_estimate'] = 'nullable|mimes:pdf|max:10240';
            $rules['remark']            = 'nullable|string';
        } else {
            $rules['estimate_called']   = 'nullable|date';
            $rules['call_letter']       = 'nullable|mimes:pdf|max:10240';
            $rules['estimate_received'] = 'nullable|date';
            $rules['received_estimate'] = 'nullable|mimes:pdf|max:10240';
            $rules['remark']            = 'required|in:' . implode(',', self::REMARKS);
        }

        $request->validate($rules, [
            'estimate_called.required' => __('Estimate Called date is required when milestone is marked completed.'),
            'remark.required'          => __('Please select a remark when milestone is not completed.'),
            'remark.in'                => __('Please select a valid remark from the list.'),
        ]);

        $departmentError = $this->validateDepartmentSequence(
            $request->identify_new_work_id,
            $request->department
        );

        if ($departmentError) {
            return back()
                ->withInput()
                ->with('error', $departmentError);
        }

        $callLetter = null;
        $receivedEstimate = null;

        if ($isCompleted && $request->hasFile('call_letter')) {
            $callLetter = $request->file('call_letter')
                ->store(
                    'estimates/call-letter',
                    'public'
                );
        }

        if ($isCompleted && $request->hasFile('received_estimate')) {
            $receivedEstimate = $request->file('received_estimate')
                ->store(
                    'estimates/received-estimate',
                    'public'
                );
        }

        Estimate::create([
            'identify_new_work_id' => $request->identify_new_work_id,
            'department'           => $request->department,
            'is_completed'         => $isCompleted ? 1 : 0,
            'remark'               => $isCompleted ? null : $request->remark,
            'cost'                 => $request->cost,
            'estimate_called'      => $isCompleted ? $request->estimate_called : null,
            'call_letter'          => $callLetter,
            'estimate_received'    => $isCompleted ? $request->estimate_received : null,
            'received_estimate'    => $receivedEstimate,
        ]);

        return redirect()
            ->route('works-paperwork-progress-register.estimates.index')
            ->with('success', 'Estimate added successfully.');
    }

    public function edit($id)
    {
        $record = Estimate::with('identifyNewWork')
            ->findOrFail(decrypt($id));

        $works = IdentifyNewWork::where('status', 1)
            ->orderBy('work_description_identification')
            ->get([
                'id',
                'work_description_identification',
            ]);

        $remarks = self::REMARKS;

        return view(
            'admin.master.works_paperwork_progress_register.estimates.edit',
            compact('record', 'works', 'remarks')
        );
    }

    public function update(Request $request, $id)
    {
        $record = Estimate::findOrFail(decrypt($id));

        $isCompleted = $request->boolean('is_completed');

        $rules = [
            'identify_new_work_id' => 'required|exists:identify_new_works,id',
            'department'           => 'required|in:Engineering,TRD,Signal,Electrical/G',
            'is_completed'         => 'nullable|boolean',
            'cost'                 => 'nullable|numeric|min:0',
        ];

        if ($isCompleted) {
            $rules['estimate_called']   = 'required|date';
            $rules['call_letter']       = 'nullable|mimes:pdf|max:10240';
            $rules['estimate_received'] = 'nullable|date|after_or_equal:estimate_called';
            $rules['received_estimate'] = 'nullable|mimes:pdf|max:10240';
            $rules['remark']            = 'nullable|string';
        } else {
            $rules['estimate_called']   = 'nullable|date';
            $rules['call_letter']       = 'nullable|mimes:pdf|max:10240';
            $rules['estimate_received'] = 'nullable|date';
            $rules['received_estimate'] = 'nullable|mimes:pdf|max:10240';
            $rules['remark']            = 'required|in:' . implode(',', self::REMARKS);
        }

        $request->validate($rules, [
            'estimate_called.required' => __('Estimate Called date is required when milestone is marked completed.'),
            'remark.required'          => __('Please select a remark when milestone is not completed.'),
            'remark.in'                => __('Please select a valid remark from the list.'),
        ]);

        $departmentError = $this->validateDepartmentSequence(
            $request->identify_new_work_id,
            $request->department,
            $record->id
        );

        if ($departmentError) {
            return back()
                ->withInput()
                ->with('error', $departmentError);
        }

        $callLetter = $record->call_letter;
        $receivedEstimate = $record->received_estimate;

        if ($isCompleted && $request->hasFile('call_letter')) {
            if ($record->call_letter) {
                Storage::disk('public')
                    ->delete($record->call_letter);
            }

            $callLetter = $request->file('call_letter')
                ->store(
                    'estimates/call-letter',
                    'public'
                );
        }

        if ($isCompleted && $request->hasFile('received_estimate')) {
            if ($record->received_estimate) {
                Storage::disk('public')
                    ->delete($record->received_estimate);
            }

            $receivedEstimate = $request->file('received_estimate')
                ->store(
                    'estimates/received-estimate',
                    'public'
                );
        }

        $record->update([
            'identify_new_work_id' => $request->identify_new_work_id,
            'department'           => $request->department,
            'is_completed'         => $isCompleted ? 1 : 0,
            'remark'               => $isCompleted ? null : $request->remark,
            'cost'                 => $request->cost,
            'estimate_called'      => $isCompleted ? $request->estimate_called : null,
            'call_letter'          => $isCompleted ? $callLetter : $record->call_letter,
            'estimate_received'    => $isCompleted ? $request->estimate_received : null,
            'received_estimate'    => $isCompleted ? $receivedEstimate : $record->received_estimate,
        ]);

        return redirect()
            ->route('works-paperwork-progress-register.estimates.index')
            ->with('success', 'Estimate updated successfully.');
    }

    public function destroy($id)
    {
        $record = Estimate::findOrFail(decrypt($id));

        if ($record->call_letter) {
            Storage::disk('public')
                ->delete($record->call_letter);
        }

        if ($record->received_estimate) {
            Storage::disk('public')
                ->delete($record->received_estimate);
        }

        $record->delete();

        return redirect()
            ->route('works-paperwork-progress-register.estimates.index')
            ->with('success', 'Estimate deleted successfully.');
    }
}
