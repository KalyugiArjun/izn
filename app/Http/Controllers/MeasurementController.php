<?php

namespace App\Http\Controllers;

use App\Models\ClothMeasureType;
use App\Models\ClothType;
use App\Models\Measurement;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class MeasurementController extends Controller
{

    public function index()
    {
        if (\Auth::user()->can('manage measurement')) {
            if (\Auth::user()->type == 'customer') {
                $measurements = Measurement::where('customer', \Auth::user()->id)->orderBy('id', 'desc')->get();
            } elseif (\Auth::user()->type == 'employee') {
                $measurements = Measurement::where('responsible', \Auth::user()->id)->orderBy('id', 'desc')->get();
            } else {
                $measurements = Measurement::where('parent_id', parentId())->orderBy('id', 'desc')->get();
            }
            return view('measurement.index', compact('measurements'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('create measurement')) {
            $customer = User::where('parent_id', parentId())->where('type', 'customer')->get()->pluck('name', 'id');
            $customer->prepend(__('Select Customer'), '');

            $user = User::where('parent_id', parentId())->where('type', '!=', 'customer')->get()->pluck('name', 'id');
            $user->prepend(__('Select User'), '');

            $clothType = ClothType::where('parent_id', parentId())->get()->pluck('title', 'id');
            $clothType->prepend(__('Select Cloth Type'), '');
            $measurementNumber = measurementNumber();
            return view('measurement.create', compact('clothType', 'measurementNumber', 'clothType', 'user', 'customer'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function store(Request $request)
    {

        if (\Auth::user()->can('create measurement')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'customer' => 'required',
                    'responsible' => 'required',
                    'date' => 'required',
                    'cloth_type' => 'required',
                    'type.*' => 'required',
                    'measurement.*' => 'required',
                    'unit.*' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first())->withInput();
            }

            $types = $request->input('type');
            $measurements = $request->input('measurement');
            $units = $request->input('unit');
            $measurementDetail = [];
            foreach ($types as $index => $type) {
                $measurementDetail[] = [
                    'type' => $type,
                    'measurement' => $measurements[$index],
                    'unit' => $units[$index],
                ];
            }

            $measurement = new Measurement();
            $measurement->measurement_id = $request->measurement_id;
            $measurement->customer = $request->customer;
            $measurement->date = $request->date;
            $measurement->cloth_type = $request->cloth_type;
            $measurement->responsible = !empty($request->responsible) ? $request->responsible : 0;
            $measurement->measurement_detail = json_encode($measurementDetail);
            $measurement->parent_id = parentId();
            $measurement->save();


            // Initialize the measurement details string
            $measurementDetails = "";

            // Loop through the measurement details and create the string
            foreach ($measurementDetail as $service) {
                $measurementDetails .= $service['type'] . ':' . ' ' . $service['measurement'] . ", Unit: " . $service['unit'] . "<br>";
            }


            $module = 'measurement_create';
            $notification = Notification::where('parent_id', parentId())->where('module', $module)->first();
            $notification->measurement = $measurementDetails;
            $setting = settings();
            $errorMessage = '';

            if (!empty($notification) && $notification->enabled_email == 1) {
                $notificationResponse = MessageReplace($notification, $measurement->id);


                $data['subject'] = $notificationResponse['subject'];
                $data['message'] = $notificationResponse['message'];
                $data['module'] = $module;
                $data['logo'] = $setting['company_logo'];
                $to = $measurement->customers->email;

                if (!empty($notification) && $notification->enabled_email == 1) {
                    $response = commonEmailSend($to, $data);
                    if ($response['status'] == 'error') {
                        $errorMessage = $response['message'];
                    }
                }

                if (!empty($notification) && $notification->enabled_sms == 1 && !empty($notification->sms_message)) {
                    $twilio_sid = getSettingsValByName('twilio_sid');
                    if (!empty($twilio_sid)) {
                        send_twilio_msg($measurement->customers->phone_number, $notificationResponse['sms_message']);
                    }
                }
            }


            $module = 'measurement_assign';
            $notification = Notification::where('parent_id', parentId())->where('module', $module)->first();
            $notification->measurement = $measurementDetails;
            $setting = settings();
            $errorMessage = '';

            if (!empty($notification) && $notification->enabled_email == 1) {
                $notificationResponse = MessageReplace($notification, $measurement->id);


                $data['subject'] = $notificationResponse['subject'];
                $data['message'] = $notificationResponse['message'];
                $data['module'] = $module;
                $data['logo'] = $setting['company_logo'];
                $to = $measurement->users->email;

                if (!empty($notification) && $notification->enabled_email == 1) {
                    $response = commonEmailSend($to, $data);
                    if ($response['status'] == 'error') {
                        $errorMessage = $response['message'];
                    }
                }

                if (!empty($notification) && $notification->enabled_sms == 1 && !empty($notification->sms_message)) {
                    $twilio_sid = getSettingsValByName('twilio_sid');
                    if (!empty($twilio_sid)) {
                        send_twilio_msg($measurement->users->phone_number, $notificationResponse['sms_message']);
                    }
                }
            }

            return redirect()->route('measurement.index')->with('success', __('Measurement successfully created.') . '</br>' . $errorMessage);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function show($ids)
    {
        if (\Auth::user()->can('show measurement')) {
            $id = Crypt::decrypt($ids);
            $measurement = Measurement::find($id);
            $measurement->measurement_detail = json_decode($measurement->measurement_detail);
            return view('measurement.show', compact('measurement'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function edit($ids)
    {
        if (\Auth::user()->can('edit measurement')) {
            $id = Crypt::decrypt($ids);
            $measurement = Measurement::find($id);
            $customer = User::where('parent_id', parentId())->where('type', 'customer')->get()->pluck('name', 'id');
            $customer->prepend(__('Select Customer'), '');

            $user = User::where('parent_id', parentId())->where('type', '!=', 'customer')->get()->pluck('name', 'id');
            $user->prepend(__('Select User'), '');

            $clothType = ClothType::where('parent_id', parentId())->get()->pluck('title', 'id');
            $clothType->prepend(__('Select Cloth Type'), '');
            $measurementNumber = $measurement->measurement_id;
            $measurement->measurement_detail = json_decode($measurement->measurement_detail);
            return view('measurement.edit', compact('measurement', 'customer', 'user', 'clothType', 'measurementNumber'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function update(Request $request, $id)
    {

        if (\Auth::user()->can('edit measurement')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'customer' => 'required',
                    'responsible' => 'required',
                    'date' => 'required',
                    'cloth_type' => 'required',
                    'type.*' => 'required',
                    'measurement.*' => 'required',
                    'unit.*' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $types = $request->input('type');
            $measurements = $request->input('measurement');
            $units = $request->input('unit');
            $measurementDetail = [];
            foreach ($types as $index => $type) {
                $measurementDetail[] = [
                    'type' => $type,
                    'measurement' => $measurements[$index],
                    'unit' => $units[$index],
                ];
            }
            $id = decrypt($id);

            $measurement = Measurement::find($id);
            $measurement->measurement_id = $request->measurement_id;
            $measurement->customer = $request->customer;
            $measurement->date = $request->date;
            $measurement->cloth_type = $request->cloth_type;
            $measurement->responsible = !empty($request->responsible) ? $request->responsible : 0;
            $measurement->measurement_detail = json_encode($measurementDetail);
            $measurement->save();
            return redirect()->route('measurement.index')->with('success', __('Measurement successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy($id)
    {
        if (\Auth::user()->can('delete measurement')) {
            $id = decrypt($id);
            $measurement = Measurement::find($id);
            $measurement->delete();
            return redirect()->back()->with('success', 'Measurement successfully deleted.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function measurementType(Request $request)
    {
        $types = ClothMeasureType::where('cloth_type_id', $request->cloty_type_id)->orderBy('order', 'asc')->with('units')->get();

        return json_encode($types, true);
    }
}
