<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Diesellobby;
use Illuminate\Http\Request;

class DiesellobbyController extends Controller
{
    public function index()
    {
        $diesellobbies = Diesellobby::latest()->get();
        return view('admin.master.diesel_lobby.index', compact('diesellobbies'));
    }

    public function create()
    {
        return view('admin.master.diesel_lobby.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required',
            'code'    => 'nullable|unique:diesellobbies,code',
            'pincode' => 'nullable|digits:6'
        ]);

        Diesellobby::create([
            'code'    => $request->code,
            'name'    => $request->name,
            'created_by'        => auth()->id(), // ✅ Added
            'updated_by'        => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Diesel Lobby Location created successfully');
    }

    public function edit($id)
    {
        // dd('diesellobby edit');
        $diesellobby = Diesellobby::findOrFail(decrypt($id));
        return view('admin.master.diesel_lobby.edit', compact('diesellobby'));
    }

    public function update(Request $request, $id)
    {
        $diesellobby = Diesellobby::findOrFail(decrypt($id));

        $request->validate([
            'name'    => 'required',
            'code'    => 'nullable|unique:diesellobbies,code,' . $diesellobby->id,
        ]);

        $diesellobby->update([
            'code'    => $request->code,
            'name'    => $request->name,
            'status'  => $request->status ?? 1,
             'updated_by'  => auth()->id(), // ✅ added
        ]);

        return redirect()->back()->with('success', 'Diesel Lobby Location updated successfully');
    }

    public function destroy($id)
    {
        $diesellobby = Diesellobby::findOrFail(decrypt($id));
        $diesellobby->delete();

        return redirect()->back()->with('success', 'Diesel Lobby Location deleted successfully');
    }
}
