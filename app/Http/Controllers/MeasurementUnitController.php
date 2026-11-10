<?php

namespace App\Http\Controllers;

use App\Models\MeasurementUnit;
use Illuminate\Http\Request;

class MeasurementUnitController extends Controller
{

    public function index()
    {
        if (\Auth::user()->can('manage measurement unit')) {
            $units = MeasurementUnit::where('parent_id', parentId())->orderBy('id', 'desc')->get();
            return view('unit.index', compact('units'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        return view('unit.create');
    }


    public function store(Request $request)
    {
        if (\Auth::user()->can('create measurement unit')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'unit' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $measurementUnit = new MeasurementUnit();
            $measurementUnit->unit = $request->unit;
            $measurementUnit->parent_id = \Auth::user()->id;
            $measurementUnit->save();
            return redirect()->back()->with('success', __('Unit successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        $id = decrypt($id);
        $measurementUnit = MeasurementUnit::find($id);
        return view('unit.edit', compact('measurementUnit'));
    }


    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit measurement unit')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'unit' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }
            $id = decrypt($id);
            $measurementUnit = MeasurementUnit::find($id);
            $measurementUnit->unit = $request->unit;
            $measurementUnit->save();
            return redirect()->back()->with('success', __('Unit successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy($id)
    {
        if (\Auth::user()->can('delete measurement unit')) {
            $id = decrypt($id);
            $measurementUnit = MeasurementUnit::find($id);
            $measurementUnit->delete();
            return redirect()->back()->with('success', 'Unit successfully deleted.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
