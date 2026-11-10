<?php

namespace App\Http\Controllers;

use App\Models\ClothMeasureType;
use App\Models\ClothType;
use App\Models\MeasurementUnit;
use App\Models\Subscription;
use App\Models\Tax;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class ClothTypeController extends Controller
{

    public function index()
    {
        if (\Auth::user()->can('manage cloth type')) {
            $clothTypes = ClothType::where('parent_id', parentId())->orderBy('id', 'desc')->get();
            return view('cloth_type.index', compact('clothTypes'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        if (\Auth::user()->can('create cloth type')) {
            $gender = ClothType::$gender;
            $unit = MeasurementUnit::where('parent_id', parentId())->get()->pluck('unit', 'id');
            $taxes = Tax::where('parent_id', parentId())->get()->pluck('tax', 'id');
            return view('cloth_type.create', compact('gender', 'unit', 'taxes'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function store(Request $request)
    {

        if (\Auth::user()->can('create cloth type')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'gender' => 'required',
                    'amount' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first())->withInput();
            }
            $ids = parentId();
            $authUser = User::find($ids);
            $totalClothType = $authUser->totalClothType();
            $subscription = Subscription::find($authUser->subscription);
            if ($totalClothType >= $subscription->cloth_type_limit && $subscription->cloth_type_limit != 0) {
                return redirect()->back()->with('error', __('Your cloth type limit is over, please upgrade your subscription.'));
            }
            $clothType = new ClothType();
            $clothType->title = $request->title;
            $clothType->gender = $request->gender;
            $clothType->amount = $request->amount;
            $clothType->taxes = !empty($request->taxes) ? implode(',', $request->taxes) : null;
            $clothType->note = !empty($request->note) ? $request->note : null;
            $clothType->parent_id = parentId();
            $clothType->save();

            if (count($request->measurement_title) > 0 && count($request->measurement_unit) > 0 && count($request->order) > 0) {
                $measurement_title = $request->measurement_title;
                $measurement_unit = $request->measurement_unit;
                $order = $request->order;

                foreach ($measurement_title as $key => $title) {
                    $clothMeasureType = new ClothMeasureType();
                    $clothMeasureType->cloth_type_id = $clothType->id;
                    $clothMeasureType->title = $title;
                    $clothMeasureType->unit = $measurement_unit[$key];
                    $clothMeasureType->order = $order[$key];
                    $clothMeasureType->save();
                }
            }


            return redirect()->route('cloth-type.index')->with('success', __('Cloth Type successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function show($ids)
    {
        if (\Auth::user()->can('show cloth type')) {
            $id = Crypt::decrypt($ids);
            $clothType = ClothType::find($id);
            return view('cloth_type.show', compact('clothType'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function edit($ids)
    {
        if (\Auth::user()->can('edit cloth type')) {
            $id = Crypt::decrypt($ids);
            $clothType = ClothType::find($id);
            $gender = ClothType::$gender;
            $unit = MeasurementUnit::where('parent_id', parentId())->get()->pluck('unit', 'id');
            $taxes = Tax::where('parent_id', parentId())->get()->pluck('tax', 'id');
            return view('cloth_type.edit', compact('clothType', 'gender', 'unit', 'taxes'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit cloth type')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'title' => 'required',
                    'gender' => 'required',
                    'amount' => 'required',
                ]
            );
            $id = decrypt($id);
            $clothType = ClothType::find($id);
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }
            $clothType = ClothType::find($id);
            $clothType->title = $request->title;
            $clothType->gender = $request->gender;
            $clothType->amount = $request->amount;
            $clothType->taxes = !empty($request->taxes) ? implode(',', $request->taxes) : null;
            $clothType->note = !empty($request->note) ? $request->note : null;
            $clothType->save();

            if (count($request->measurement_title) > 0 && count($request->measurement_unit) > 0 && count($request->order) > 0) {
                $id = $request->id;
                $measurement_title = $request->measurement_title;
                $measurement_unit = $request->measurement_unit;
                $order = $request->order;

                foreach ($measurement_title as $key => $title) {
                    if (isset($id[$key]) && !empty($id[$key])) {
                        $clothMeasureType = ClothMeasureType::find($id[$key]);
                    } else {
                        $clothMeasureType = new ClothMeasureType();
                        $clothMeasureType->cloth_type_id = $clothType->id;
                    }
                    $clothMeasureType->title = $title;
                    $clothMeasureType->unit = $measurement_unit[$key];
                    $clothMeasureType->order = $order[$key];
                    $clothMeasureType->save();
                }
            }

            return redirect()->route('cloth-type.index')->with('success', __('Cloth Type successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy($id)
    {
        if (\Auth::user()->can('delete cloth type')) {
            $id = decrypt($id);
            $clothType = ClothType::find($id);
            ClothMeasureType::where('cloth_type_id', $clothType->id)->delete();
            $clothType->delete();
            return redirect()->back()->with('success', 'Cloth Type successfully deleted.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function clothTypeMeasureDestroy(Request $request)
    {
        if (!empty($request->id)) {
            $measure = ClothMeasureType::find($request->id);
            $measure->delete();
        }
        return 1;
    }
}
