<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use App\Models\ExpenseSubCategory;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseSubCategoryController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('manage expense sub category')) {
            $subCategories = ExpenseSubCategory::where('parent_id', parentId())->orderBy('id', 'desc')->get();
        return view('expense_subcategory.index', compact('subCategories'));
        } else {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }

    public function create()
    {
        $categories = ExpenseCategory::where('parent_id', parentId())->pluck('name', 'id');
        return view('expense_subcategory.create', compact('categories'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->can('create expense sub category')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required'
            ]);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $subCategory = new ExpenseSubCategory();
            $subCategory->name = $request->name;
            $subCategory->category_id = $request->category_id;
            $subCategory->parent_id = parentId();
            $subCategory->save();

            return redirect()->back()->with('success', 'Expense sub category created successfully');
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
        if (Auth::user()->can('edit expense sub category')) {
            $id = decrypt($id);
            $subCategory = ExpenseSubCategory::find($id);
            $categories = ExpenseCategory::where('parent_id', parentId())->pluck('name', 'id');
            return view('expense_subcategory.edit', compact('subCategory'));
        } else {
            return redirect()->back()->with('error', 'Permission denied');
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->can('edit expense sub category')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required'
            ]);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $id = decrypt($id);
            $subCategory = ExpenseSubCategory::find($id);
            $subCategory->name = $request->name;
            $subCategory->category_id = $request->category_id;
            $subCategory->parent_id = parentId();
            $subCategory->save();

            return redirect()->back()->with('success', 'Expense sub category updated successfully');
        } else {
            return redirect()->back()->with('error', 'Permission denied');
        }
    }

    public function destroy($id)
    {
        if(Auth::user()->can('delete expense sub category')) {
            $id = decrypt($id);
            $subCategory = ExpenseSubCategory::find($id);
            $subCategory->delete();

            return redirect()->back()->with('success', 'Expense sub category deleted successfully');
        } else {
             return redirect()->back()->with('error', 'Permission denied');
        }
    }
}
