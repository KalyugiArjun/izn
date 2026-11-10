<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\AmenityCategory;
use Illuminate\Http\Request;
use App\Models\Department;

class AmenityCategoryController extends Controller
{
    public function index()
    {
        $amenityCategories = AmenityCategory::latest()->get();

        return view('admin.master.amenity-category.index', compact('amenityCategories'));
    }

    public function create()
    {
        $departments = Department::pluck('name', 'id');
        return view('admin.master.amenity-category.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id', // ✅ added
        ]);

        AmenityCategory::create([
            'name' => $request->name,
            'department_id' => $request->department_id, // ✅ added
            'status' => $request->status ?? 1,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Amenity Category created successfully');
    }

    public function edit($id)
    {
        $amenityCategory = AmenityCategory::findOrFail(decrypt($id));
        $departments = Department::pluck('name', 'id');

        return view('admin.master.amenity-category.edit', compact('amenityCategory', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $amenityCategory = AmenityCategory::findOrFail(decrypt($id));

        $request->validate([
            'name' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id', // ✅ added
        ]);

        $amenityCategory->update([
            'name' => $request->name,
            'department_id' => $request->department_id, // ✅ added
            'status' => $request->status ?? 1,
            'updated_by' => auth()->id(),
        ]);
        return redirect()->back()->with('success', 'Amenity Category updated successfully');
    }

    public function destroy($id)
    {
        $amenityCategory = AmenityCategory::findOrFail(decrypt($id));
        $amenityCategory->delete();

        return redirect()->back()->with('success', 'Amenity Category deleted successfully');
    }
}
