<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::latest('sort_order')->get();

        // dd($sliders);

        return view('admin.master.slider.index', compact('sliders'));
    }

    public function create()
    {
        return view('admin.master.slider.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'nullable|string|max:255',
            'subtitle'    => 'nullable|string',
            'image'       => 'required|image',
            'button_text' => 'nullable|string|max:100',
            'button_url'  => 'nullable|string|max:255',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        // Store image
        $imagePath = $request->file('image')->store('sliders', 'public');



        Slider::create([
            'title'       => $request->title,
            'subtitle'    => $request->subtitle,
            'image'       => $imagePath,
            'button_text' => $request->button_text,
            'button_url'  => $request->button_url,
            'sort_order'  => $request->sort_order ?? 0,
            'status'      => $request->status ?? 1,
            'created_by'  => auth()->id(),
            'updated_by'  => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Slider created successfully.');
    }

    public function edit($id)
    {
        $slider = Slider::findOrFail(decrypt($id));

        return view('admin.master.slider.edit', compact('slider'));
    }

    public function update(Request $request, $id)
    {
        $slider = Slider::findOrFail(decrypt($id));

        $request->validate([
            'title'       => 'nullable|string|max:255',
            'subtitle'    => 'nullable|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'button_text' => 'nullable|string|max:100',
            'button_url'  => 'nullable|string|max:255',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        $imagePath = $slider->image;

        // Replace image if new one uploaded
        if ($request->hasFile('image')) {
            // Delete old image from storage
            if ($slider->image && Storage::disk('public')->exists($slider->image)) {
                Storage::disk('public')->delete($slider->image);
            }
            $imagePath = $request->file('image')->store('sliders', 'public');
        }

        $slider->update([
            'title'       => $request->title,
            'subtitle'    => $request->subtitle,
            'image'       => $imagePath,
            'button_text' => $request->button_text,
            'button_url'  => $request->button_url,
            'sort_order'  => $request->sort_order ?? 0,
            'status'      => $request->status ?? 1,
            'updated_by'  => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Slider updated successfully.');
    }

    public function destroy($id)
    {
        $slider = Slider::findOrFail(decrypt($id));

        // Delete image from storage
        if ($slider->image && Storage::disk('public')->exists($slider->image)) {
            Storage::disk('public')->delete($slider->image);
        }

        $slider->delete();

        return redirect()->back()->with('success', 'Slider deleted successfully.');
    }
}
