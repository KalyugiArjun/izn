<?php

namespace App\Http\Controllers;

use App\Models\ClothType;
use App\Models\Measurement;
use App\Models\Notification;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class OrderController extends Controller
{
    private function getUserOrders()
    {
        if (\Auth::user()->type == 'customer') {
            return Order::where('customer_id', \Auth::user()->id)
                ->where('parent_id', parentId())
                ->orderBy('id', 'desc')
                ->get();
        } elseif (\Auth::user()->type == 'employee') {
            return Order::where('responsible', \Auth::user()->id)
                ->where('parent_id', parentId())
                ->orderBy('id', 'desc')
                ->get();
        } else {
            return Order::where('parent_id', parentId())
                ->orderBy('id', 'desc')
                ->get();
        }
    }

    public function index()
    {
        if (!\Auth::user()->can('manage order')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $orders = $this->getUserOrders();
        return view('order.index', compact('orders'));
    }

    // Kanban view
    public function kanban()
    {
        if (!\Auth::user()->can('manage order')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
        $orders = $this->getUserOrders();

        // Group orders by status
        $statuses = Order::$status;
        $kanbanData = [];
        foreach ($statuses as $key => $label) {
            $kanbanData[$key] = $orders->where('status', $key);
        }
        return view('order.kanban', compact('kanbanData', 'statuses'));
    }

    public function create()
    {
        if (\Auth::user()->can('create order')) {
            $customer = User::where('parent_id', parentId())->where('type', 'customer')->get()->pluck('name', 'id');
            $customer->prepend(__('Select Customer'), '');

            $user = User::where('parent_id', parentId())->where('type', '!=', 'customer')->get()->pluck('name', 'id');
            $user->prepend(__('Select User'), '');

            $clothType = ClothType::where('parent_id', parentId())->get()->pluck('title', 'id');
            $clothType->prepend(__('Select Cloth Type'), '');
            $orderNumber = $this->orderNumber();
            $status = Order::$status;
            $gender = ClothType::$gender;
            return view('order.create', compact('clothType', 'orderNumber', 'clothType', 'user', 'customer', 'status', 'gender'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('create order')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'order_id' => 'required',
                    'customer_id' => 'required',
                    'order_date' => 'required',
                    'deadline_date' => 'required',
                    'quantity' => 'required',
                    'febric' => 'required',
                    'febric_color' => 'required',
                    'responsible' => 'required',
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
            $order = new Order();
            $order->order_id = $request->order_id;
            $order->customer_id = $request->customer_id;
            $order->order_date = $request->order_date;
            $order->deadline_date = $request->deadline_date;
            $order->quantity = $request->quantity;
            $order->febric = $request->febric;
            $order->febric_color = $request->febric_color;
            $order->gender = $request->gender;
            $order->cloth_type = $request->cloth_type;
            $order->status = $request->status;
            $order->notes = $request->notes;
            $order->responsible = !empty($request->responsible) ? $request->responsible : 0;
            $order->measurement = json_encode($measurementDetail);
            $order->parent_id = parentId();
            $order->save();


            // Initialize the measurement details string
            $measurementDetails = "";

            // Loop through the measurement details and create the string
            foreach ($measurementDetail as $service) {
                $measurementDetails .= $service['type'] . ':' . ' ' . $service['measurement'] . ", Unit: " . $service['unit'] . "<br>";
            }

            if (!empty($request->fabric_attachment)) {
                $tenantFilenameWithExt = $request->file('fabric_attachment')->getClientOriginalName();
                $tenantFilename = pathinfo($tenantFilenameWithExt, PATHINFO_FILENAME);
                $tenantExtension = $request->file('fabric_attachment')->getClientOriginalExtension();
                $tenantFileName = $tenantFilename . '_' . time() . '.' . $tenantExtension;
                $dir = storage_path('upload/fabric_attachment');
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                $request->file('fabric_attachment')->storeAs('upload/fabric_attachment/', $tenantFileName);
                $order->fabric_attachment = $tenantFileName;
                $order->save();
            }
            if (!empty($request->sewing_pattern)) {
                $tenantFilenameWithExt = $request->file('sewing_pattern')->getClientOriginalName();
                $tenantFilename = pathinfo($tenantFilenameWithExt, PATHINFO_FILENAME);
                $tenantExtension = $request->file('sewing_pattern')->getClientOriginalExtension();
                $tenantFileName = $tenantFilename . '_' . time() . '.' . $tenantExtension;
                $dir = storage_path('upload/sewing_pattern');
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                $request->file('sewing_pattern')->storeAs('upload/sewing_pattern/', $tenantFileName);
                $order->sewing_pattern = $tenantFileName;
                $order->save();
            }

            $module = 'order_create';
            $notification = Notification::where('parent_id', parentId())->where('module', $module)->first();
            $notification->measurement = $measurementDetails;
            $setting = settings();
            $errorMessage = '';

            if (!empty($notification) && $notification->enabled_email == 1) {
                $notificationResponse = MessageReplace($notification, $order->id);

                $data['subject'] = $notificationResponse['subject'];
                $data['message'] = $notificationResponse['message'];
                $data['module'] = $module;
                $data['logo'] = $setting['company_logo'];
                $to = $order->customers->email;

                if (!empty($notification) && $notification->enabled_email == 1) {
                    $response = commonEmailSend($to, $data);
                    if ($response['status'] == 'error') {
                        $errorMessage = $response['message'];
                    }
                }

                if (!empty($notification) && $notification->enabled_sms == 1 && !empty($notification->sms_message)) {
                    $twilio_sid = getSettingsValByName('twilio_sid');
                    if (!empty($twilio_sid)) {
                        send_twilio_msg($order->customers->phone_number, $notificationResponse['sms_message']);
                    }
                }
            }

            $module = 'order_assign';
            $notification = Notification::where('parent_id', parentId())->where('module', $module)->first();
            $notification->measurement = $measurementDetails;
            $setting = settings();
            $errorMessage = '';

            if (!empty($notification) && $notification->enabled_email == 1) {
                $notificationResponse = MessageReplace($notification, $order->id);

                $data['subject'] = $notificationResponse['subject'];
                $data['message'] = $notificationResponse['message'];
                $data['module'] = $module;
                $data['logo'] = $setting['company_logo'];
                $to = $order->users->email;

                if (!empty($notification) && $notification->enabled_email == 1) {
                    $response = commonEmailSend($to, $data);
                    if ($response['status'] == 'error') {
                        $errorMessage = $response['message'];
                    }
                }

                if (!empty($notification) && $notification->enabled_sms == 1 && !empty($notification->sms_message)) {
                    $twilio_sid = getSettingsValByName('twilio_sid');
                    if (!empty($twilio_sid)) {
                        send_twilio_msg($order->users->phone_number, $notificationResponse['sms_message']);
                    }
                }
            }

            return redirect()->route('order.index')->with('success', __('Order successfully created.') . '</br>' . $errorMessage);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show($ids)
    {
        if (\Auth::user()->can('show order')) {
            $id = Crypt::decrypt($ids);
            $order = Order::find($id);
            $order->measurement = json_decode($order->measurement);
            return view('order.show', compact('order'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit($ids)
    {
        if (\Auth::user()->can('edit order')) {
            $id = Crypt::decrypt($ids);
            $order = Order::find($id);
            $order->measurement = json_decode($order->measurement);
            $customer = User::where('parent_id', parentId())->where('type', 'customer')->get()->pluck('name', 'id');
            $customer->prepend(__('Select Customer'), '');

            $user = User::where('parent_id', parentId())->where('type', '!=', 'customer')->get()->pluck('name', 'id');
            $user->prepend(__('Select User'), '');

            $clothType = ClothType::where('parent_id', parentId())->get()->pluck('title', 'id');
            $clothType->prepend(__('Select Cloth Type'), '');
            $orderNumber = $order->order_id;
            $status = Order::$status;
            $gender = ClothType::$gender;
            return view('order.edit', compact('clothType', 'orderNumber', 'clothType', 'user', 'customer', 'order', 'status', 'gender'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit order')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'order_id' => 'required',
                    'customer_id' => 'required',
                    'order_date' => 'required',
                    'deadline_date' => 'required',
                    'quantity' => 'required',
                    'febric' => 'required',
                    'responsible' => 'required',
                    'measurement' => 'required',
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
            $order = Order::find($id);
            $oldStatus = $order->status;
            $order->order_id = $request->order_id;
            $order->customer_id = $request->customer_id;
            $order->order_date = $request->order_date;
            $order->deadline_date = $request->deadline_date;
            $order->quantity = $request->quantity;
            $order->febric = $request->febric;
            $order->febric_color = $request->febric_color;
            $order->gender = $request->gender;
            $order->cloth_type = $request->cloth_type;
            $order->status = $request->status;
            $order->notes = $request->notes;
            $order->responsible = !empty($request->responsible) ? $request->responsible : 0;
            $order->measurement = json_encode($measurementDetail);
            $order->save();
            if (!empty($request->fabric_attachment)) {
                $file = $request->file('fabric_attachment');
                $tenantFilenameWithExt = $file->getClientOriginalName();
                $tenantFilename = pathinfo($tenantFilenameWithExt, PATHINFO_FILENAME);
                $tenantExtension = $request->file('fabric_attachment')->getClientOriginalExtension();
                $tenantFileName = $tenantFilename . '_' . time() . '.' . $tenantExtension;
                $dir = storage_path('upload/fabric_attachment');
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                $request->file('fabric_attachment')->storeAs('upload/fabric_attachment/', $tenantFileName);
                $order->fabric_attachment = $tenantFileName;
                $order->save();
            }
            if (!empty($request->sewing_pattern)) {
                $tenantFilenameWithExt = $request->file('sewing_pattern')->getClientOriginalName();
                $tenantFilename = pathinfo($tenantFilenameWithExt, PATHINFO_FILENAME);
                $tenantExtension = $request->file('sewing_pattern')->getClientOriginalExtension();
                $tenantFileName = $tenantFilename . '_' . time() . '.' . $tenantExtension;
                $dir = storage_path('upload/sewing_pattern');
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                $request->file('sewing_pattern')->storeAs('upload/sewing_pattern/', $tenantFileName);
                $order->sewing_pattern = $tenantFileName;
                $order->save();
            }
            if ($oldStatus != $request->status) {
                $module = 'order_status_update';
                $notification = Notification::where('parent_id', parentId())->where('module', $module)->first();
                $notification->measurement = $measurementDetail;
                $setting = settings();
                $errorMessage = '';

                if (!empty($notification) && $notification->enabled_email == 1) {
                    $notificationResponse = MessageReplace($notification, $order->id);

                    $data['subject'] = $notificationResponse['subject'];
                    $data['message'] = $notificationResponse['message'];
                    $data['module'] = $module;
                    $data['logo'] = $setting['company_logo'];
                    $to = $order->customers->email;

                    if (!empty($notification) && $notification->enabled_email == 1) {
                        $response = commonEmailSend($to, $data);
                        if ($response['status'] == 'error') {
                            $errorMessage = $response['message'];
                        }
                    }

                    if (!empty($notification) && $notification->enabled_sms == 1 && !empty($notification->sms_message)) {
                        $twilio_sid = getSettingsValByName('twilio_sid');
                        if (!empty($twilio_sid)) {
                            send_twilio_msg($order->customers->phone_number, $notificationResponse['sms_message']);
                        }
                    }
                }
            }
            return redirect()->route('order.index')->with('success', __('Order successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (\Auth::user()->can('delete order')) {
            $id = decrypt($id);
            $order = Order::find($id);
            $order->delete();
            return redirect()->back()->with('success', 'Order successfully deleted.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function orderNumber()
    {
        $latestOrder = Order::where('parent_id', parentId())->latest()->first();
        if (!$latestOrder) {
            return 1;
        } else {
            return $latestOrder->order_id + 1;
        }

    }

    public function customerMeasurement(Request $request)
    {
        $measurement = Measurement::where('cloth_type', $request->cloth_type_id)->where('customer', $request->customer_id)->first();

        if ($measurement && !empty($measurement->measurement_detail)) {
            return response()->json(json_decode($measurement->measurement_detail, true));
        } else {
            return response()->json([]);
        }
    }

    public function todayOrder()
    {
        if (\Auth::user()->can('manage today order')) {
            $orders = Order::where('parent_id', parentId())->where('order_date', date('Y-m-d'))->get();
            return view('order.today_order', compact('orders'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function todayDelivery()
    {
        if (\Auth::user()->can('manage today order delivery')) {
            $orders = Order::where('parent_id', parentId())->where('deadline_date', date('Y-m-d'))->get();
            return view('order.today_delivery', compact('orders'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function calendar()
    {
        if (\Auth::user()->can('manage order calendar')) {
            $orders = Order::where('parent_id', parentId())->get();
            $eventData = $currentMonth = [];
            foreach ($orders as $order) {
                $customer = !empty($order->customers) ? $order->customers->name : '';
                $orderId = orderPrefix() . $order->order_id;
                $event = [
                    'title' => $orderId . ' - ' . $customer,
                    'start' => date("Y-m-d", strtotime($order->order_date)),
                    'urls' => route('order.show', Crypt::encrypt($order->id)),
                    'registration_date' => dateFormat($order->deadline_date),
                    'status' => !empty($order->status) ? $order->status : '-',
                ];
                $eventData[] = $event;

                $currentMonthEvent = [
                    'title' => $orderId . ' - ' . $customer,
                    'order_date' => dateFormat($order->order_date),
                    'registration_date' => dateFormat($order->deadline_date),
                    'url' => route('order.show', Crypt::encrypt($order->id)),
                    'status' => !empty($order->status) ? $order->status : '-',
                ];
                $currentMonth[] = $currentMonthEvent;
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied!'));
        }
        return view('order.calendar', compact('orders', 'eventData', 'currentMonth'));
    }

    public function statusUpdate(Request $request)
    {
        if(\Auth::user()->can('update order status'))
        $order = Order::find($request->orderId);
        if (strtolower($order->status) === 'delivered') {
            return response()->json([
                'error' => 'This order has already been marked as Delivered and cannot be updated.'
            ]);
        }

        if (strtolower($request->status) === 'delivered') {
            return response()->json([
                'error' => 'This order cannot be manually marked as Delivered. Please create the invoice first.'
            ]);
        }

        $order->status = $request->status;
        $order->save();
        $module = 'order_status_update';
        $notification = Notification::where('parent_id', parentId())->where('module', $module)->first();
        $setting = settings();
        $errorMessage = '';

        if (!empty($notification) && $notification->enabled_email == 1) {
            $notificationResponse = MessageReplace($notification, $order->id);

            $data['subject'] = $notificationResponse['subject'];
            $data['message'] = $notificationResponse['message'];
            $data['module'] = $module;
            $data['logo'] = $setting['company_logo'];
            $to = $order->customers->email;

            if (!empty($notification) && $notification->enabled_email == 1) {
                $response = commonEmailSend($to, $data);
                if ($response['status'] == 'error') {
                    $errorMessage = $response['message'];
                }
            }

            if (!empty($notification) && $notification->enabled_sms == 1 && !empty($notification->sms_message)) {
                $twilio_sid = getSettingsValByName('twilio_sid');
                if (!empty($twilio_sid)) {
                    send_twilio_msg($order->customers->phone_number, $notificationResponse['sms_message']);
                }
            }
        }
        return response()->json(['success' => true]);
    }
}
