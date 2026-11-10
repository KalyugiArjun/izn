<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\LineNo;
use Illuminate\Http\Request;

class LineNoController extends Controller
{
    // =========================
    // WEB METHODS
    // =========================

    public function index()
    {
        $lineNos = LineNo::latest()->get();

        return view('admin.master.line-no.index', compact('lineNos'));
    }

    public function create()
    {
        return view('admin.master.line-no.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        LineNo::create([
            'name' => $request->name,
            'status' => $request->status ?? 1,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Line No. created successfully');
    }

    public function edit($id)
    {
        $lineNo = LineNo::findOrFail(decrypt($id));

        return view('admin.master.line-no.edit', compact('lineNo'));
    }

    public function update(Request $request, $id)
    {
        $lineNo = LineNo::findOrFail(decrypt($id));

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $lineNo->update([
            'name' => $request->name,
            'status' => $request->status ?? 1,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Line No. updated successfully');
    }

    public function destroy($id)
    {
        $lineNo = LineNo::findOrFail(decrypt($id));

        $lineNo->delete();

        return redirect()->back()->with('success', 'Line No. deleted successfully');
    }

    // =========================
    // API METHODS
    // =========================

    public function apiIndex()
    {
        $lineNos = LineNo::latest()->get();

        return response()->json([
            'status' => true,
            'data' => $lineNos
        ]);
    }

    public function apiStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $lineNo = LineNo::create([
            'name' => $request->name,
            'status' => $request->status ?? 1,
            'created_by' => 1,
            'updated_by' => 1,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Line No created successfully',
            'data' => $lineNo
        ]);
    }

    public function apiShow($id)
    {
        $lineNo = LineNo::find($id);

        if (!$lineNo) {
            return response()->json([
                'status' => false,
                'message' => 'Data not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $lineNo
        ]);
    }

    public function apiUpdate(Request $request, $id)
    {
        $lineNo = LineNo::find($id);

        if (!$lineNo) {
            return response()->json([
                'status' => false,
                'message' => 'Data not found'
            ], 404);
        }

        $lineNo->update([
            'name' => $request->name,
            'status' => $request->status ?? 1,
            'updated_by' => 1,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Updated successfully',
            'data' => $lineNo
        ]);
    }

    public function apiDestroy($id)
    {
        $lineNo = LineNo::find($id);

        if (!$lineNo) {
            return response()->json([
                'status' => false,
                'message' => 'Data not found'
            ], 404);
        }

        $lineNo->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully'
        ]);
    }
}