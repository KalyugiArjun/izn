<?php

namespace App\Http\Controllers\Admin\WorksPaperworkProgressRegister;

use App\Http\Controllers\Controller;
use App\Models\IdentifyNewWork;
use App\Models\Ph16Ti;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IdentifyNewWorkController extends Controller
{
    /**
     * Only designations saved in PH16 TI Authority.
     * Dropdown key = ph16_tis.id
     * Dropdown label = designation name
     */
    private function inspectorOptions(): array
    {
        return Ph16Ti::with('designation')
            ->get()
            ->filter(fn ($item) => $item->designation !== null)
            ->mapWithKeys(fn ($item) => [
                $item->id => $item->designation->name,
            ])
            ->toArray();
    }

    public function index()
    {
        $identifyNewWorks = IdentifyNewWork::with('ph16Ti.designation')
            ->latest('date_of_identification_of_work')
            ->get();

        return view(
            'admin.master.works_paperwork_progress_register.identify_new_work.index',
            compact('identifyNewWorks')
        );
    }

    public function create()
    {
        $inspectors = $this->inspectorOptions();

        return view(
            'admin.master.works_paperwork_progress_register.identify_new_work.create',
            compact('inspectors')
        );
    }

    public function store(Request $request)
    {
        // Support both multi-select array (ph16_ti_ids) and legacy single (ph16_ti_id)
        $tiIds = $request->input('ph16_ti_ids');
        if (empty($tiIds) && $request->filled('ph16_ti_id')) {
            $tiIds = [$request->input('ph16_ti_id')];
        }
        $request->merge(['ph16_ti_ids' => (array) $tiIds]);

        $request->validate([
            'work_description_identification' => 'required|string',
            'date_of_identification_of_work' => 'required|date',
            'ph16_ti_ids' => 'required|array|min:1',
            'ph16_ti_ids.*' => [
                'required',
                'integer',
                Rule::exists('ph16_tis', 'id'),
            ],
            'status' => 'required|in:0,1',
        ], [
            'ph16_ti_ids.required' => __('Please select at least one Concern Traffic Inspector.'),
            'ph16_ti_ids.min' => __('Please select at least one Concern Traffic Inspector.'),
        ]);

        $selectedTis = Ph16Ti::with('designation')
            ->whereIn('id', $request->ph16_ti_ids)
            ->get();

        $inspectorNames = $selectedTis
            ->map(fn ($item) => $item->designation?->name)
            ->filter()
            ->unique()
            ->implode(', ');

        $firstTiId = $selectedTis->first()?->id ?? null;
        $tiIdsList = array_values(array_map('intval', $request->ph16_ti_ids));

        IdentifyNewWork::create([
            'work_description_identification' =>
                $request->work_description_identification,

            'date_of_identification_of_work' =>
                $request->date_of_identification_of_work,

            'ph16_ti_id' => $firstTiId,
            'ph16_ti_ids' => $tiIdsList,

            // Keep the text column populated with comma-separated names for display and search.
            'concern_traffic_inspector' => $inspectorNames,

            'status' => $request->status,
        ]);

        return redirect()
            ->route('works-paperwork-progress-register.identify-new-work.index')
            ->with('success', __('New Work created successfully.'));
    }

    public function edit($id)
    {
        $identifyNewWork = IdentifyNewWork::findOrFail(decrypt($id));
        $inspectors = $this->inspectorOptions();

        $selectedInspectorIds = $identifyNewWork->inspector_ids;

        // Fallback from text matching if ph16_ti_ids is empty
        if (empty($selectedInspectorIds)) {
            $existingNames = array_filter(array_map('trim', explode(',', $identifyNewWork->concern_traffic_inspector ?? '')));
            foreach ($inspectors as $tiId => $name) {
                if (in_array($name, $existingNames, true)) {
                    $selectedInspectorIds[] = (int) $tiId;
                }
            }
        }

        return view(
            'admin.master.works_paperwork_progress_register.identify_new_work.edit',
            compact(
                'identifyNewWork',
                'inspectors',
                'selectedInspectorIds'
            )
        );
    }

    public function update(Request $request, $id)
    {
        $identifyNewWork = IdentifyNewWork::findOrFail(decrypt($id));

        $tiIds = $request->input('ph16_ti_ids');
        if (empty($tiIds) && $request->filled('ph16_ti_id')) {
            $tiIds = [$request->input('ph16_ti_id')];
        }
        $request->merge(['ph16_ti_ids' => (array) $tiIds]);

        $request->validate([
            'work_description_identification' => 'required|string',
            'date_of_identification_of_work' => 'required|date',
            'ph16_ti_ids' => 'required|array|min:1',
            'ph16_ti_ids.*' => [
                'required',
                'integer',
                Rule::exists('ph16_tis', 'id'),
            ],
            'status' => 'required|in:0,1',
        ], [
            'ph16_ti_ids.required' => __('Please select at least one Concern Traffic Inspector.'),
            'ph16_ti_ids.min' => __('Please select at least one Concern Traffic Inspector.'),
        ]);

        $selectedTis = Ph16Ti::with('designation')
            ->whereIn('id', $request->ph16_ti_ids)
            ->get();

        $inspectorNames = $selectedTis
            ->map(fn ($item) => $item->designation?->name)
            ->filter()
            ->unique()
            ->implode(', ');

        $firstTiId = $selectedTis->first()?->id ?? null;
        $tiIdsList = array_values(array_map('intval', $request->ph16_ti_ids));

        $identifyNewWork->update([
            'work_description_identification' =>
                $request->work_description_identification,

            'date_of_identification_of_work' =>
                $request->date_of_identification_of_work,

            'ph16_ti_id' => $firstTiId,
            'ph16_ti_ids' => $tiIdsList,

            'concern_traffic_inspector' => $inspectorNames,

            'status' => $request->status,
        ]);

        return redirect()
            ->route('works-paperwork-progress-register.identify-new-work.index')
            ->with('success', __('New Work updated successfully.'));
    }

    public function destroy($id)
    {
        $identifyNewWork = IdentifyNewWork::findOrFail(decrypt($id));

        $identifyNewWork->delete();

        return redirect()
            ->route('works-paperwork-progress-register.identify-new-work.index')
            ->with('success', __('New Work deleted successfully.'));
    }
}
