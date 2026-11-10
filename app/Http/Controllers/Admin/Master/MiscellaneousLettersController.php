<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\MiscellaneousLetters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MiscellaneousLettersController extends Controller
{
    public function index()
    {
        $letters = DB::table('miscellaneous_letters')
            ->leftJoin('departments', 'departments.id', '=', 'miscellaneous_letters.department_id')
            ->select(
                'miscellaneous_letters.*',
                'departments.name as department_name'
            )
            ->latest('miscellaneous_letters.id')
            ->get();

        return view('admin.master.operating_safety_documents.miscellaneous_letters.index', compact('letters'));
    }

    public function create()
    {
        $departments = DB::table('departments')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view('admin.master.operating_safety_documents.miscellaneous_letters.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'letter_no' => 'required|string|max:100',
            'subject' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'pdf_file' => 'required|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
        ]);

        $pdf = $request->file('pdf_file')->store(
            'uploads/miscellaneous_letters',
            'public'
        );

        MiscellaneousLetters::create([
            'date' => $request->date,
            'letter_no' => $request->letter_no,
            'subject' => $request->subject,
            'department_id' => $request->department_id,
            'pdf_file' => $pdf,
            'status' => $request->status,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Miscellaneous Letter created successfully');
    }

    public function edit($id)
    {
        $letter = MiscellaneousLetters::findOrFail(decrypt($id));

        $departments = DB::table('departments')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view('admin.master.operating_safety_documents.miscellaneous_letters.edit', compact('letter', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $letter = MiscellaneousLetters::findOrFail(decrypt($id));

        $request->validate([
            'date' => 'required|date',
            'letter_no' => 'required|string|max:100',
            'subject' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
            'status' => 'required|in:0,1',
        ]);

        $data = [
            'date' => $request->date,
            'letter_no' => $request->letter_no,
            'subject' => $request->subject,
            'department_id' => $request->department_id,
            'status' => $request->status,
            'updated_by' => auth()->id(),
        ];

        if ($request->hasFile('pdf_file')) {
            if ($letter->pdf_file && Storage::disk('public')->exists($letter->pdf_file)) {
                Storage::disk('public')->delete($letter->pdf_file);
            }

            $data['pdf_file'] = $request->file('pdf_file')->store(
                'uploads/miscellaneous_letters',
                'public'
            );
        }

        $letter->update($data);

        return redirect()->back()->with('success', 'Miscellaneous Letter updated successfully');
    }

    public function destroy($id)
    {
        $letter = MiscellaneousLetters::findOrFail(decrypt($id));

        if ($letter->pdf_file && Storage::disk('public')->exists($letter->pdf_file)) {
            Storage::disk('public')->delete($letter->pdf_file);
        }

        $letter->delete();

        return redirect()->back()->with('success', 'Miscellaneous Letter deleted successfully');
    }


    // Filtered/paginated public list page (dashboard "View" ke liye)
    public function letterList(Request $request)
    {
        $query = MiscellaneousLetters::with('department')
            ->where('status', 1);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('letter_no', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $letters = $query->latest('date')->paginate(12)->withQueryString();

        $departments = DB::table('departments')
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        return view('admin.master.operating_safety_documents.miscellaneous_letters.show', compact('letters', 'departments'));
    }
}
