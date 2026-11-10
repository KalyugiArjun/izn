<?php

namespace App\Http\Controllers;

use App\Models\ClothType;
use App\Models\Custom;
use App\Models\Customer;
use App\Models\Measurement;
use App\Models\Notification;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class CustomerController extends Controller
{

    public function index()
    {
        if (\Auth::user()->can('manage customer')) {
            $customers = User::where('parent_id', parentId())->where('type', 'customer')
                ->orderBy('id', 'desc')
                ->get();
            return view('customer.index', compact('customers'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        $clothType = ClothType::where('parent_id', parentId())->get()->pluck('title', 'id');
        $clothType->prepend(__('Select Cloth Type'), '');

        $measurementNumber = measurementNumber();

        $user = User::where('parent_id', parentId())->where('type', '!=', 'customer')->get()->pluck('name', 'id');
        $user->prepend(__('Select User'), '');
        return view('customer.create', compact('clothType', 'measurementNumber', 'user'));
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('create customer')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => 'required',
                    'phone_number' => 'required',
                    'address' => 'required',

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
            $ids = parentId();
            $authUser = User::find($ids);
            $totalCustomer = $authUser->totalCustomer();
            $subscription = Subscription::find($authUser->subscription);
            if ($totalCustomer >= $subscription->customer_limit && $subscription->customer_limit != 0) {
                return redirect()->back()->with('error', __('Your customer limit is over, please upgrade your subscription.'));
            }
            $userRole = Role::where('parent_id', parentId())->where('name', 'customer')->first();
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = \Hash::make($request->password);
            $user->phone_number = $request->phone_number;
            $user->type = $userRole->name;
            $user->parent_id = parentId();
            $user->email_verified_at = now();
            $user->save();
            $user->assignRole($userRole);

            if (!empty($user)) {
                $customer = new Customer();
                $customer->user_id = $user->id;
                $customer->customer_id = $this->customerNumber();
                $customer->city = $request->city;
                $customer->address = $request->address;
                $customer->notes = $request->notes;
                $customer->parent_id = parentId();
                $customer->save();
            }
            $measurementDetail = [];
            foreach ($request->type as $index => $type) {
                $measurementDetail[] = [
                    'type' => $type,
                    'measurement' => $request->measurement[$index],
                    'unit' => $request->unit[$index],
                ];
            }
            $measurement = new Measurement();
            $measurement->measurement_id = $request->measurement_id;
            $measurement->customer = $user->id;
            $measurement->date = $request->date;
            $measurement->cloth_type = $request->cloth_type;
            $measurement->responsible = $request->responsible;
            $measurement->measurement_detail = json_encode($measurementDetail);
            $measurement->parent_id = parentId();
            $measurement->save();

            $measurementDetails = "";
            foreach ($measurementDetail as $m) {
                $measurementDetails .= $m['type'] . ': ' . $m['measurement'] . ", Unit: " . $m['unit'] . "<br>";
            }
            $module = 'customer_create';
            $notification = Notification::where('parent_id', parentId())->where('module', $module)->first();
            $setting = settings();
            $errorMessage = '';

            if (!empty($notification) && $notification->enabled_email == 1) {
                $notificationResponse = MessageReplace($notification, $user->id);


                $data['subject'] = $notificationResponse['subject'];
                $data['message'] = $notificationResponse['message'];
                $data['module'] = $module;
                $data['logo'] = $setting['company_logo'];
                $to = $request->email;

                if (!empty($notification) && $notification->enabled_email == 1) {
                    $response = commonEmailSend($to, $data);
                    if ($response['status'] == 'error') {
                        $errorMessage = $response['message'];
                    }
                }

                if (!empty($notification) && $notification->enabled_sms == 1 && !empty($notification->sms_message)) {
                    $twilio_sid = getSettingsValByName('twilio_sid');
                    if (!empty($twilio_sid)) {
                        send_twilio_msg($request->phone_number, $notificationResponse['sms_message']);
                    }
                }
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
                $to = $user->email;

                $response = commonEmailSend($to, $data);

                if (!empty($notification) && $notification->enabled_email == 1) {
                    $response = commonEmailSend($to, $data);
                    if ($response['status'] == 'error') {
                        $errorMessage = $response['message'];
                    }
                }

                if (!empty($notification) && $notification->enabled_sms == 1 && !empty($notification->sms_message)) {
                    $twilio_sid = getSettingsValByName('twilio_sid');
                    if (!empty($twilio_sid)) {
                        send_twilio_msg($user->phone_number, $notificationResponse['sms_message']);
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
                $to = $user->email;

                if (!empty($notification) && $notification->enabled_email == 1) {
                    $response = commonEmailSend($to, $data);
                    if ($response['status'] == 'error') {
                        $errorMessage = $response['message'];
                    }
                }

                if (!empty($notification) && $notification->enabled_sms == 1 && !empty($notification->sms_message)) {
                    $twilio_sid = getSettingsValByName('twilio_sid');
                    if (!empty($twilio_sid)) {
                        send_twilio_msg($user->phone_number, $notificationResponse['sms_message']);
                    }
                }
            }
            return redirect()->route('customer.index')->with('success', __('Customer successfully created.') . '</br>' . $errorMessage);

        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function show(Customer $customer)
    {
        //
    }


    public function edit($id)
    {
        $id = decrypt($id);
        $user = User::find($id);
        return view('customer.edit', compact('user'));
    }


    public function update(Request $request, $id)
    {
        $id = decrypt($id);
        if (\Auth::user()->can('edit customer')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => 'required',
                    'phone_number' => 'required',
                    'city' => 'required',
                    'address' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $user = User::find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone_number = $request->phone_number;
            $user->save();
            if (!empty($user)) {
                $customer = Customer::where('user_id', $id)->first();
                $customer->city = $request->city;
                $customer->address = $request->address;
                $customer->notes = $request->notes;
                $customer->save();
            }
            return redirect()->back()->with('success', __('Customer successfully updated.'));

        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }


    public function destroy($id)
    {
        if (\Auth::user()->can('delete customer')) {
            $id = decrypt($id);
            $user = User::find($id);
            $user->delete();
            Customer::where('user_id', $id)->delete();
            return redirect()->back()->with('success', 'Customer successfully deleted.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function customerNumber()
    {
        $latestCustomer = Customer::where('parent_id', parentId())->latest()->first();
        if (!$latestCustomer) {
            return 1;
        } else {
            return $latestCustomer->customer_id + 1;
        }

    }
}
