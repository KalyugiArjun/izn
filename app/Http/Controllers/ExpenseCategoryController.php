<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage expense category')) {
            $expenseCategories = ExpenseCategory::where('parent_id', parentId())->orderBy('id', 'desc')->get();
            return view('expense_category.index', compact('expenseCategories'));
        } else {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }

    public function create()
    {
        return view('expense_category.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->can('create expense category')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required'
            ]);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $expenseCategory = new ExpenseCategory();
            $expenseCategory->name = $request->name;
            $expenseCategory->parent_id = parentId();
            $expenseCategory->save();

            return redirect()->back()->with('success', 'Expense category created successfully');
        } else {
            return redirect()->back()->with('error', 'Permission denied');
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        if (Auth::user()->can('edit expense category')) {
            $id = decrypt($id);
            $expenseCategory = ExpenseCategory::find($id);
            return view('expense_category.edit', compact('expenseCategory'));
        } else {
            return redirect()->back()->with('error', 'Permission denied');
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->can('edit expense category')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required'
            ]);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $id = decrypt($id);
            $expenseCategory = ExpenseCategory::find($id);
            $expenseCategory->name = $request->name;
            $expenseCategory->parent_id = parentId();
            $expenseCategory->save();

            return redirect()->back()->with('success', 'Expense category updated successfully');
        } else {
            return redirect()->back()->with('error', 'Permission denied');
        }
    }

    public function destroy($id)
    {
        if(Auth::user()->can('delete expense category')) {
            $id = decrypt($id);
            $expenseCategory = ExpenseCategory::find($id);
            $expenseCategory->delete();

            return redirect()->back()->with('success', 'Expense category deleted successfully');
        } else {
             return redirect()->back()->with('error', 'Permission denied');
        }
    }
}
