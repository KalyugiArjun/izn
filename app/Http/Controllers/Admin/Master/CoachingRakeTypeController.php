<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\CoachingRakeType;
use Illuminate\Http\Request;

class CoachingRakeTypeController extends Controller
{
    public function index()
    {
        $records = CoachingRakeType::get();

        return view('admin.master.coaching_rake_type.index', compact('records'));
    }

    public function create()
    {
        return view('admin.master.coaching_rake_type.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:coaching_rake_types,name',
            'status' => 'required|in:1,0',
        ]);

        CoachingRakeType::create([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('coaching-rake-type.index')
            ->with('success', 'Coaching Rake Type created successfully.');
    }

    public function edit($id)
    {
        $record = CoachingRakeType::findOrFail(decrypt($id));

        return view('admin.master.coaching_rake_type.edit', compact('record'));
    }

    public function update(Request $request, $id)
    {
        $record = CoachingRakeType::findOrFail(decrypt($id));

        $request->validate([
            'name' => 'required|string|max:255|unique:coaching_rake_types,name,' . $record->id,
            'status' => 'required|in:1,0',
        ]);

        $record->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('coaching-rake-type.index')
            ->with('success', 'Coaching Rake Type updated successfully.');
    }

    public function destroy($id)
    {
        $record = CoachingRakeType::findOrFail(decrypt($id));
        $record->delete();

        return redirect()
            ->route('coaching-rake-type.index')
            ->with('success', 'Coaching Rake Type deleted successfully.');
    }
}
