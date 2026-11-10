<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\AreaOfProficiency;
use Illuminate\Http\Request;

class AreaOfProficiencyController extends Controller
{
    public function index()
    {
        $records = AreaOfProficiency::get();

        return view('admin.master.area_of_proficiencie.index', compact('records'));
    }

    public function create()
    {
        return view('admin.master.area_of_proficiencie.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:area_of_proficiencies,name',
            'status' => 'required|in:1,0',
        ]);

        AreaOfProficiency::create([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('area-of-proficiency.index')
            ->with('success', 'Area of Proficiency created successfully.');
    }

    public function edit($id)
    {
        $record = AreaOfProficiency::findOrFail(decrypt($id));

        return view('admin.master.area_of_proficiencie.edit', compact('record'));
    }

    public function update(Request $request, $id)
    {
        $record = AreaOfProficiency::findOrFail(decrypt($id));

        $request->validate([
            'name' => 'required|string|max:255|unique:area_of_proficiencies,name,' . $record->id,
            'status' => 'required|in:1,0',
        ]);

        $record->update([
            'name' => $request->name,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('area-of-proficiency.index')
            ->with('success', 'Area of Proficiency updated successfully.');
    }

    public function destroy($id)
    {
        $record = AreaOfProficiency::findOrFail(decrypt($id));
        $record->delete();

        return redirect()
            ->route('area-of-proficiency.index')
            ->with('success', 'Area of Proficiency deleted successfully.');
    }
}
