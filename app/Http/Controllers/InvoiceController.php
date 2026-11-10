<?php

namespace App\Http\Controllers;

use App\Models\ClothType;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\Notification;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Srmklive\PayPal\Services\PayPal;
use Stripe\Charge;
use Stripe\Stripe;

class InvoiceController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('manage invoice')) {
            if (\Auth::user()->type == 'customer') {
                $invoices = Invoice::where('customer_id', \Auth::user()->id)
                    ->where('parent_id', parentId())
                    ->orderBy('id', 'desc')
                    ->get();
            }elseif (\Auth::user()->type == 'employee') {
                $invoices = Invoice::whereHas('orders', function ($query) {
                    $query->where('responsible', \Auth::user()->id);
                })
                    ->where('parent_id', parentId())
                    ->orderBy('id', 'desc')
                    ->get();
            } else {
                $invoices = Invoice::where('parent_id', parentId())
                    ->orderBy('id', 'desc')
                    ->get();
            }
            return view('invoice.index', compact('invoices'));

        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create(Request $request)
    {
        $customer = User::where('parent_id', parentId())
            ->where('type', 'customer')
            ->pluck('name', 'id');
        $customer->prepend(__('Select Customer'), '');

        $invoiceNumber = $this->invoiceNumber();
        $selectedCustomerId = $request->selectedCustomerId;

        // $orders = collect();

        return view('invoice.create', compact('customer', 'invoiceNumber', 'selectedCustomerId'));
        // return view('invoice.create', compact('customer', 'invoiceNumber', 'selectedCustomerId', 'order_id', 'orders'));
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('create invoice')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'invoice_id' => 'required',
                    'customer_id' => 'required',
                    'invoice_date' => 'required',
                    'due_date' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first())->withInput();
            }

            $orders = Order::where('customer_id', $request->customer_id)->whereNotIn('status', ['delivered', 'on_hold', 'cancelled'])->get();

            if (!empty($orders) && count($orders) > 0) {
                $invoice = new Invoice();
                $invoice->invoice_id = $request->invoice_id;
                $invoice->customer_id = $request->customer_id;
                $invoice->invoice_date = $request->invoice_date;
                $invoice->due_date = $request->due_date;
                $invoice->status = 'unpaid';
                $invoice->parent_id = parentId();
                $invoice->save();

                if (!empty($invoice)) {
                    foreach ($orders as $order) {
                        $clothType = ClothType::find($order->cloth_type);
                        if (!empty($clothType)) {
                            $invoiceItem = new InvoiceItem();
                            $invoiceItem->invoice_id = $invoice->id;
                            $invoiceItem->cloth_type_id = $order->cloth_type;
                            $invoiceItem->quantity = $order->quantity;
                            $invoiceItem->amount = !empty($clothType) ? $clothType->amount : 0;
                            $invoiceItem->tax = !empty($clothType) ? $clothType->taxes : null;
                            $invoiceItem->note = !empty($order) ? $order->note : null;
                            $invoiceItem->parent_id = parentId();
                            $invoiceItem->save();

                            $order->status = 'delivered';
                            $order->invoice = $invoice->id;
                            $order->save();
                        }
                    }
                }

                $module = 'invoice_create';
                $notification = Notification::where('parent_id', parentId())->where('module', $module)->first();
                $setting = settings();
                $errorMessage = '';

                if (!empty($notification) && $notification->enabled_email == 1) {
                    $notificationResponse = MessageReplace($notification, $invoice->id);


                    $data['subject'] = $notificationResponse['subject'];
                    $data['message'] = $notificationResponse['message'];
                    $data['module'] = $module;
                    $data['logo'] = $setting['company_logo'];
                    $to = $invoice->customers->email;

                    if (!empty($notification) && $notification->enabled_email == 1) {
                        $response = commonEmailSend($to, $data);
                        if ($response['status'] == 'error') {
                            $errorMessage = $response['message'];
                        }
                    }

                    if (!empty($notification) && $notification->enabled_sms == 1 && !empty($notification->sms_message)) {
                        $twilio_sid = getSettingsValByName('twilio_sid');
                        if (!empty($twilio_sid)) {
                            send_twilio_msg($invoice->customers->phone_number, $notificationResponse['sms_message']);
                        }
                    }
                }
            } else {
                return redirect()->back()->with('error', __('Customer order not found.'));
            }

            $errorMessage = !empty($errorMessage) ? $errorMessage : '';
            return redirect()->route('invoice.show', Crypt::encrypt($invoice->id))->with('success', __('Invoice successfully created.') . '</br>' . $errorMessage);
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function show($ids)
    {
        if (\Auth::user()->can('show order')) {
            $id = Crypt::decrypt($ids);
            $invoice = Invoice::find($id);
            $settings = settings();
            $invoicePaymentSettings = invoicePaymentSettings($invoice->parent_id);
            return view('invoice.show', compact('invoice', 'invoicePaymentSettings', 'settings'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function edit($id)
    {
        $id = decrypt($id);
        $invoice = Invoice::find($id);
        $customer = User::where('parent_id', parentId())->where('type', 'customer')->get()->pluck('name', 'id');
        $customer->prepend(__('Select Customer'), '');
        $invoiceNumber = $invoice->invoice_id;
        return view('invoice.edit', compact('customer', 'invoiceNumber', 'invoice'));
    }

    public function update(Request $request, $id)
    {
        if (\Auth::user()->can('edit invoice')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'invoice_id' => 'required',
                    'customer_id' => 'required',
                    'invoice_date' => 'required',
                    'due_date' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first())->withInput();
            }
            $id = decrypt($id);
            $invoice = Invoice::find($id);
            $invoice->invoice_id = $request->invoice_id;
            $invoice->customer_id = $request->customer_id;
            $invoice->invoice_date = $request->invoice_date;
            $invoice->due_date = $request->due_date;
            $invoice->save();
            return redirect()->route('invoice.index')->with('success', __('Invoice successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy($id)
    {
        $decryptedId = decrypt($id); // decrypt first

        if (\Auth::user()->can('delete invoice')) {
            $invoice = Invoice::find($decryptedId); // use decrypted ID
            if (!$invoice) {
                return redirect()->back()->with('error', 'Invoice not found.');
            }
            InvoiceItem::where('invoice_id', $invoice->id)->delete();
            // Delete invoice
            $invoice->delete();

            return redirect()->back()->with('success', 'Invoice successfully deleted.');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function invoiceNumber()
    {
        $latestInvoice = Invoice::where('parent_id', parentId())->latest()->first();
        if (!$latestInvoice) {
            return 1;
        } else {
            return $latestInvoice->invoice_id + 1;
        }
    }

    public function invoicePaymentCreate($invoice_id)
    {
        $invoice = Invoice::find($invoice_id);
        $paymentMethod = Invoice::$paymentMethod;
        $settings = settings();
        if (\Auth::user()->type == 'customer') {
            return view('invoice.customer_payment', compact('invoice_id', 'invoice', 'paymentMethod', 'settings'));
        } else {
            return view('invoice.payment', compact('invoice_id', 'invoice', 'paymentMethod'));
        }
    }

    public function invoicePaymentStore(Request $request, $invoice_id)
    {
        if (\Auth::user()->can('create invoice payment')) {

            $invoice = Invoice::find($invoice_id);
            $dueAmount = $invoice->getInvoiceDueAmount();
            $validator = \Validator::make(
                $request->all(),
                [
                    'payment_date' => 'required',
                    'amount' => 'required|numeric|min:1|max:' . $dueAmount,
                    'payment_type' => 'required',
                ],

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

            $payment = new InvoicePayment();
            $payment->invoice_id = $invoice_id;
            $payment->transaction_id = md5(time());
            $payment->payment_type = $request->payment_type;
            $payment->amount = $request->amount;
            $payment->payment_date = $request->payment_date;
            $payment->notes = $request->notes;
            $payment->payment_status = 'Success';
            $payment->parent_id = parentId();
            $payment->save();
            $invoice = Invoice::find($invoice_id);
            if ($invoice->getInvoiceDueAmount() <= 0) {
                $status = 'paid';
            } else {
                $status = 'partial_paid';
            }
            Invoice::statusChange($invoice->id, $status);
            return redirect()->back()->with('success', __('Invoice payment successfully added.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied!'));
        }
    }

    public function invoicePaymentDestroy($invoice_id, $id)
    {
        if (\Auth::user()->can('delete invoice payment')) {
            $payment = InvoicePayment::find($id);
            $payment->delete();

            $invoice = Invoice::find($invoice_id);
            if ($invoice->getInvoiceDueAmount() <= 0) {
                $status = 'paid';
            } elseif ($invoice->getInvoiceDueAmount() == $invoice->getInvoiceSubTotalAmount()) {
                $status = 'unpaid';
            } else {
                $status = 'partial_paid';
            }
            Invoice::statusChange($invoice->id, $status);
            return redirect()->back()->with('success', __('Invoice payment successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied!'));
        }
    }

    public function invoiceItemCreate($invoice_id)
    {
        $items = ClothType::where('parent_id', parentId())->get()->pluck('title', 'id');
        $items->prepend(__('Select Item'), '');
        return view('invoice.item_create', compact('items', 'invoice_id'));
    }

    public function invoiceItemStore(Request $request, $id)
    {

        if (\Auth::user()->can('create invoice item')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'cloth_type_id' => 'required',
                    'quantity' => 'required',
                    'amount' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $invoiceItem = new InvoiceItem();
            $invoiceItem->invoice_id = $id;
            $invoiceItem->cloth_type_id = $request->cloth_type_id;
            $invoiceItem->quantity = $request->quantity;
            $invoiceItem->amount = $request->amount;
            $invoiceItem->tax = $request->tax;
            $invoiceItem->note = $request->note;
            $invoiceItem->parent_id = parentId();
            $invoiceItem->save();

            $invoice = Invoice::find($id);
            if ($invoice->getInvoiceDueAmount() <= 0) {
                $status = 'paid';
            } elseif ($invoice->getInvoiceDueAmount() == $invoice->getInvoiceSubTotalAmount()) {
                $status = 'unpaid';
            } else {
                $status = 'partial_paid';
            }
            Invoice::statusChange($invoice->id, $status);

            return redirect()->back()->with('success', __('Invoice item successfully added.'));
        }
    }

    public function invoiceItemDestroy($invoiceId, $id)
    {
        if (\Auth::user()->can('delete invoice item')) {
            InvoiceItem::where('id', $id)->delete();

            $invoice = Invoice::find($invoiceId);
            if ($invoice->getInvoiceDueAmount() <= 0) {
                $status = 'paid';
            } elseif ($invoice->getInvoiceDueAmount() == $invoice->getInvoiceSubTotalAmount()) {
                $status = 'unpaid';
            } else {
                $status = 'partial_paid';
            }
            Invoice::statusChange($invoice->id, $status);
            return redirect()->back()->with('success', __('Invoice item successfully deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function getItemDetails(Request $request)
    {
        $itemDetails = ClothType::find($request->cloth_type_id)->toArray();
        return json_encode($itemDetails);
    }

    public function byCustomer(Request $request)
    {
        return Order::where('customer_id', $request->customer_id)
            ->whereNotIn('status', ['delivered', 'on_hold', 'cancelled'])
            ->get()
            ->mapWithKeys(function ($order) {
                return [$order->id => orderPrefix() . $order->id];
            });
    }
    public function banktransferPayment(Request $request, $id)
    {
        $invoiceId = decrypt($id);
        $validator = \Validator::make(
            $request->all(),
            [
                'receipt' => 'required',
                'amount' => 'required',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $invoice = Invoice::find($invoiceId);

        $transactionID = uniqid('', true);

        $payment['invoice_id'] = $invoice->id;
        $payment['transaction_id'] = $transactionID;
        $payment['payment_type'] = 'Bank Transfer';
        $payment['amount'] = $request->amount;
        $payment['notes'] = $request->notes;
        $payment['payment_status'] = 'pending';

        if (!empty($request->receipt)) {
            $tenantFilenameWithExt = $request->file('receipt')->getClientOriginalName();
            $tenantFilename = pathinfo($tenantFilenameWithExt, PATHINFO_FILENAME);
            $tenantExtension = $request->file('receipt')->getClientOriginalExtension();
            $tenantFileName = $tenantFilename . '_' . time() . '.' . $tenantExtension;
            $dir = storage_path('upload/receipt');
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            $request->file('receipt')->storeAs('upload/receipt/', $tenantFileName);
            $payment['receipt'] = $tenantFileName;
        }

        InvoicePayment::addPayment($payment);
        return redirect()->back()->with('success', __('Invoice payment successfully completed.'));
    }

    public function invoicePaymentStatus(Request $request, $id, $status)
    {
        $order = InvoicePayment::find($id);
        $invoice = Invoice::find($order->invoice_id);
        if ($status == 'accept') {
            $due = $invoice->getInvoiceDueAmount();
            $total = $invoice->getInvoiceTotalAmount();
            $status = $due <= 0 ? 'paid' : ($due == $total ? 'unpaid' : 'partial_paid');
            Invoice::statusChange($invoice->id, $status);
            $order->payment_status = 'Success';
            $order->save();
        } else {
            $order->payment_status = 'Reject';
            $order->save();
        }
        return redirect()->back()->with('success', __('Invoice payment status is ' . $status));
    }

    public function paymentSettings()
    {
        return invoicePaymentSettings(parentId());
    }

    /* Store Invoice Payment Strip */
    public function InvoiceStripePayment(Request $request, $ids)
    {
        $settings = $this->paymentSettings();
        $id = decrypt($ids);
        $invoice = Invoice::find($id);
        $amount = $request->amount;
        if ($invoice) {
            try {
                $transactionID = uniqid('', true);
                Stripe::setApiKey($settings['STRIPE_SECRET']);
                $data = Charge::create(
                    [
                        "amount" => 100 * $amount,
                        "currency" => $settings['CURRENCY'],
                        "source" => $request->stripeToken,
                        "description" => " Invoice - " . invoicePrefix() . $invoice->id,
                        "metadata" => ["order_id" => $transactionID],
                        'shipping' => [
                            'name' => $request->name,
                            'address' => [
                                'line1' => $request->state ?? 'NA',
                                'city' => $request->city ?? 'NA',
                                'postal_code' => $request->zipcode ?? '000000',
                                'country' => $request->country ?? 'NA',
                            ]
                        ],
                    ]
                );

                if ($data['amount_refunded'] == 0 && empty($data['failure_code']) && $data['paid'] == 1 && $data['captured'] == 1) {

                    if ($data['status'] == 'succeeded') {

                        $payment['invoice_id'] = $invoice->id;
                        $payment['transaction_id'] = $transactionID;
                        $payment['payment_type'] = 'Stripe';
                        $payment['amount'] = $amount;
                        $payment['notes'] = $request->notes;
                        $payment['payment_status'] = 'Success';
                        $payment['receipt'] = '';
                        InvoicePayment::addPayment($payment);
                        // Invoice status update
                        $due = $invoice->getInvoiceDueAmount();
                        $total = $invoice->getInvoiceTotalAmount();
                        $status = $due <= 0 ? 'paid' : ($due == $total ? 'unpaid' : 'partial_paid');
                        Invoice::statusChange($invoice->id, $status);

                        return redirect()->back()->with('success', __('Invoice payment successfully completed.'));
                    } else {
                        return redirect()->back()->with('error', __('Your payment has failed.'));
                    }
                } else {
                    return redirect()->back()->with('error', __('Transaction has been failed.'));
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', __($e->getMessage()));
            }

        } else {
            return redirect()->back()->with('error', __('Invoice is deleted.'));
        }
    }

    /* Store Invoice Payment Paypal */
    public function invoicePaypal(Request $request, $id)
    {
        $invoiceId = decrypt($id);
        $paypalSetting = $this->paymentSettings();

        if ($paypalSetting['paypal_mode'] == 'live') {
            config([
                'paypal.live.client_id' => isset($paypalSetting['paypal_client_id']) ? $paypalSetting['paypal_client_id'] : '',
                'paypal.live.client_secret' => isset($paypalSetting['paypal_secret_key']) ? $paypalSetting['paypal_secret_key'] : '',
                'paypal.mode' => isset($paypalSetting['paypal_mode']) ? $paypalSetting['paypal_mode'] : '',
                'paypal.currency' => isset($paypalSetting['CURRENCY']) ? $paypalSetting['CURRENCY'] : '',
            ]);
        } else {
            config([
                'paypal.sandbox.client_id' => isset($paypalSetting['paypal_client_id']) ? $paypalSetting['paypal_client_id'] : '',
                'paypal.sandbox.client_secret' => isset($paypalSetting['paypal_secret_key']) ? $paypalSetting['paypal_secret_key'] : '',
                'paypal.mode' => isset($paypalSetting['paypal_mode']) ? $paypalSetting['paypal_mode'] : '',
                'paypal.currency' => isset($paypalSetting['CURRENCY']) ? $paypalSetting['CURRENCY'] : '',
            ]);
        }

        $provider = new PayPal;
        $provider->setApiCredentials(config('paypal'));

        $paypalToken = $provider->getAccessToken();

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('invoice.paypal.status', [$invoiceId, 'success'], ['amount' => $request->amount]),
                "cancel_url" => route('invoice.paypal.status', [$invoiceId, 'cancel'], ['amount' => $request->amount]),
            ],
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => isset($paypalSetting['CURRENCY']) ? $paypalSetting['CURRENCY'] : '',
                        "value" => $request->amount
                    ]
                ]
            ]
        ]);
        if (isset($response['id']) && $response['id'] != null) {
            // redirect to approve href
            foreach ($response['links'] as $links) {
                if ($links['rel'] == 'approve') {
                    return redirect()->away($links['href']);
                }
            }
            return redirect()
                ->back()
                ->with('error', 'Something went wrong.');
        } else {
            return redirect()
                ->back()
                ->with('error', $response['message'] ?? 'Something went wrong.');
        }
    }

    public function invoicePaypalStatus(Request $request, $invoiceId, $status)
    {
        if ($status == 'success') {
            $provider = new PayPal();
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();
            $transactionID = uniqid('', true);
            $invoice = Invoice::find($invoiceId);
            $response = $provider->capturePaymentOrder($request['token']);
            if (isset($response['status']) && $response['status'] == 'COMPLETED') {

                $payment['invoice_id'] = $invoiceId;
                $payment['transaction_id'] = $transactionID;
                $payment['payment_type'] = 'Paypal';
                $payment['amount'] = $request->amount;
                $payment['notes'] = $request->notes;
                $payment['payment_status'] = 'Success';
                $payment['receipt'] = '';
                InvoicePayment::addPayment($payment);
                $due = $invoice->getInvoiceDueAmount();
                $total = $invoice->getInvoiceTotalAmount();
                $status = $due <= 0 ? 'paid' : ($due == $total ? 'unpaid' : 'partial_paid');
                Invoice::statusChange($invoice->id, $status);
                return redirect()->back()->with('success', __('Invoice payment successfully completed.'));
            } else {
                return redirect()
                    ->back()
                    ->with('error', $response['message'] ?? __('Something went wrong.'));
            }
        } else {
            return redirect()
                ->back()
                ->with('error', __('Transaction has been failed.'));
        }
    }

    /* Store Invoice Payment Flutterwave */
    public function invoiceFlutterwave(Request $request, $invoice_id, $pay_id)
    {
        $invoiceID = decrypt($invoice_id);
        $invoice = Invoice::find($invoiceID);
        $paymentSetting = $this->paymentSettings();

        if ($invoice) {
            try {
                $detail = [
                    'txref' => $pay_id,
                    'SECKEY' => $paymentSetting['flutterwave_secret_key'],
                ];
                $url = "https://api.ravepay.co/flwv3-pug/getpaidx/api/v2/verify";
                $headersData = ['Content-Type' => 'application/json'];
                $bodyData = \Unirest\Request\Body::json($detail);
                $responseData = \Unirest\Request::post($url, $headersData, $bodyData);

                if (!empty($responseData)) {
                    $responseData = json_decode($responseData->raw_body, true);
                }

                if (isset($responseData['status']) && $responseData['status'] == 'success') {
                    $amountPaid = $responseData['data']['amount'];
                    $expectedAmount = $request->query('amount'); // Get amount from request

                    if ($amountPaid < $expectedAmount) {
                        return redirect()->back()->with('error', __('Payment amount mismatch! Expected: ') . $expectedAmount);
                    }

                    $invoiceTransId = uniqid('', true);

                    $payment['invoice_id'] = $invoice->id;
                    $payment['transaction_id'] = $invoiceTransId;
                    $payment['payment_type'] = 'Flutterwave';
                    $payment['amount'] = $amountPaid;
                    $payment['notes'] = $request->notes;
                    $payment['payment_status'] = 'Success';
                    $payment['receipt'] = '';
                    InvoicePayment::addPayment($payment);
                    $due = $invoice->getInvoiceDueAmount();
                    $total = $invoice->getInvoiceTotalAmount();
                    $status = $due <= 0 ? 'paid' : ($due == $total ? 'unpaid' : 'partial_paid');
                    Invoice::statusChange($invoice->id, $status);
                    return redirect()->back()->with('success', __('Invoice payment successfully completed.'));
                } else {
                    return redirect()->back()->with('error', __('Transaction failed!'));
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', $e->getMessage());
            }
        }
    }

    /* Store Invoice Payment Paystack */
    public function invoicePaystack(Request $request, $ids)
    {
        $payment_setting = $this->paymentSettings();
        $currency = $payment_setting['CURRENCY'] ?? 'USD';
        $id = Crypt::decrypt($ids);
        $invoice = Invoice::find($id);

        if (!$invoice) {
            return response()->json([
                'flag' => 0,
                'message' => __('Invoice not found.')
            ]);
        }

        $amount = $request->amount;
        if ($amount <= 0) {
            return response()->json([
                'flag' => 0,
                'message' => __('Amount must be greater than 0.')
            ]);
        }

        return response()->json([
            'flag' => 1,
            'email' => auth()->user()->email,
            'total_price' => $amount,
            'currency' => $currency,
        ]);
    }


    public function invoicePaystackStatus(Request $request, $pay_id, $invoice_id_encrypted)
    {
        try {
            $invoice = Invoice::find(Crypt::decrypt($invoice_id_encrypted));
            if (!$invoice) {
                return redirect()->back()->with('error', __('Invoice not found.'));
            }

            $secretKey = $this->paymentSettings()['paystack_secret_key'] ?? '';
            $verifyUrl = "https://api.paystack.co/transaction/verify/$pay_id";

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $verifyUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $secretKey],
            ]);
            $response = curl_exec($ch);
            curl_close($ch);
            $result = $response ? json_decode($response, true) : [];

            if (!($result['status'] ?? false) || ($result['data']['status'] !== 'success')) {
                return redirect()->back()->with('error', __('Transaction failed or cancelled.'));
            }

            $payment['invoice_id'] = $invoice->id;
            $payment['transaction_id'] = uniqid('', true);
            $payment['payment_type'] = 'Paystack';
            $payment['amount'] = $result['data']['amount'] / 100;
            $payment['notes'] = $request->notes;
            $payment['payment_status'] = 'Success';
            $payment['receipt'] = '';
            InvoicePayment::addPayment($payment);
            $due = $invoice->getInvoiceDueAmount();
            $total = $invoice->getInvoiceTotalAmount();
            $status = $due <= 0 ? 'paid' : ($due == $total ? 'unpaid' : 'partial_paid');
            Invoice::statusChange($invoice->id, $status);
            return redirect()->back()->with('success', __('Invoice payment successfully completed.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Something went wrong while verifying the payment.'));
        }
    }
}
