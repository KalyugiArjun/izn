<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    public function index()
    {
        $messages = Message::latest('sort_order')->get();

        return view('admin.master.message.index', compact('messages'));
    }

    public function create()
    {
        return view('admin.master.message.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'message'     => 'required|string',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('messages', 'public');
        }

        Message::create([
            'title'       => $request->title,
            'designation' => $request->designation,
            'image'       => $imagePath,
            'message'     => $request->message,
            'sort_order'  => $request->sort_order ?? 0,
            'status'      => $request->status ?? 1,
            'created_by'  => auth()->id(),
            'updated_by'  => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Message created successfully.');
    }

    public function edit($id)
    {
        $message = Message::findOrFail(decrypt($id));

        return view('admin.master.message.edit', compact('message'));
    }

    public function update(Request $request, $id)
    {
        $message = Message::findOrFail(decrypt($id));

        $request->validate([
            'title'       => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'message'     => 'required|string',
            'sort_order'  => 'nullable|integer|min:0',
        ]);

        $imagePath = $message->image;

        if ($request->hasFile('image')) {
            // Delete old image
            if ($message->image && Storage::disk('public')->exists($message->image)) {
                Storage::disk('public')->delete($message->image);
            }
            $imagePath = $request->file('image')->store('messages', 'public');
        }

        // Remove image manually if checkbox checked
        if ($request->remove_image) {
            if ($message->image && Storage::disk('public')->exists($message->image)) {
                Storage::disk('public')->delete($message->image);
            }
            $imagePath = null;
        }

        $message->update([
            'title'       => $request->title,
            'designation' => $request->designation,
            'image'       => $imagePath,
            'message'     => $request->message,
            'sort_order'  => $request->sort_order ?? 0,
            'status'      => $request->status ?? 1,
            'updated_by'  => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Message updated successfully.');
    }

    public function destroy($id)
    {
        $message = Message::findOrFail(decrypt($id));

        if ($message->image && Storage::disk('public')->exists($message->image)) {
            Storage::disk('public')->delete($message->image);
        }

        $message->delete();

        return redirect()->back()->with('success', 'Message deleted successfully.');
    }
}
