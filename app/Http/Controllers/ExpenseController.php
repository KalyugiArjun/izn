<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExpenseSubCategory;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('manage expense')) {
            $expenses = Expense::where('parent_id', parentId())->orderBy('id', 'desc')->get();
            return view('expense.index', compact('expenses'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create expense')) {

            $billNumber = $this->expenseNumber();
            $categories = ExpenseCategory::where('parent_id', parentId())->pluck('name', 'id');
            $subCategories = ExpenseSubCategory::where('parent_id', parentId())->pluck('name', 'id');
            return view('expense.create', compact('billNumber', 'categories', 'subCategories'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('create expense')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'amount' => 'required',
                    'date' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            if (!empty($request->receipt)) {
                $receiptFilenameWithExt = $request->file('receipt')->getClientOriginalName();
                $receiptFilename = pathinfo($receiptFilenameWithExt, PATHINFO_FILENAME);
                $receiptExtension = $request->file('receipt')->getClientOriginalExtension();
                $receiptFileName = $receiptFilename . '_' . time() . '.' . $receiptExtension;
                $dir = storage_path('upload/receipt');
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                $request->file('receipt')->storeAs('upload/receipt/', $receiptFileName);

            }

            $expense = new Expense();
            $expense->title = $request->title;
            $expense->expense_id = $request->expense_id;
            $expense->category_id = $request->category_id;
            $expense->sub_category_id = $request->sub_category_id;
            $expense->amount = $request->amount;
            $expense->date = $request->date;
            $expense->receipt = !empty($request->receipt) ? $receiptFileName : '';
            $expense->notes = $request->notes;
            $expense->parent_id = parentId();
            $expense->save();

            return redirect()->back()->with('success', __('Expense successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied!'));
        }
    }

    public function show($id)
    {
        if (\Auth::user()->can('show expense')) {
            $id = decrypt($id);
            $expense = Expense::find($id);
            return view('expense.show', compact('expense'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied!'));
        }
    }

    public function edit($id)
    {
        if (\Auth::user()->can('edit expense')) {
            $id = decrypt($id);
            $expense = Expense::find($id);
            $billNumber = $expense->expense_id;
            $categories = ExpenseCategory::where('parent_id', parentId())->pluck('name', 'id');
            $subCategories = ExpenseSubCategory::where('parent_id', parentId())->pluck('name', 'id');
            return view('expense.edit', compact('billNumber', 'expense', 'categories', 'subCategories'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit expense')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'amount' => 'required',
                    'date' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $id = decrypt($id);
            $expense = Expense::find($id);
            if (!empty($request->receipt)) {
                $receiptFilenameWithExt = $request->file('receipt')->getClientOriginalName();
                $receiptFilename = pathinfo($receiptFilenameWithExt, PATHINFO_FILENAME);
                $receiptExtension = $request->file('receipt')->getClientOriginalExtension();
                $receiptFileName = $receiptFilename . '_' . time() . '.' . $receiptExtension;
                $dir = storage_path('upload/receipt');
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                $request->file('receipt')->storeAs('upload/receipt/', $receiptFileName);
                $expense->receipt = !empty($request->receipt) ? $receiptFileName : '';
            }

            $expense->title = $request->title;
            $expense->expense_id = $request->expense_id;
            $expense->category_id = $request->category_id;
            $expense->sub_category_id = $request->sub_category_id;
            $expense->amount = $request->amount;
            $expense->date = $request->date;
            $expense->notes = $request->notes;
            $expense->save();

            return redirect()->back()->with('success', __('Expense successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function destroy($id)
    {
        if (\Auth::user()->can('delete expense')) {
            $id = decrypt($id);
            $expense = Expense::find($id);
            $expense->delete();
            return redirect()->back()->with('success', __('Expense successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied!'));
        }
    }

    public function expenseNumber()
    {
        $latest = Expense::where('parent_id', parentId())->latest()->first();
        if ($latest == null) {
            return 1;
        } else {
            return $latest->expense_id + 1;
        }
    }

    public function getSubcategory(Request $request)
    {
        $subCategories = ExpenseSubCategory::where('category_id', $request->category_id)->get();
        return response()->json($subCategories);
    }
}
