@extends('layouts.app')
@section('page-title')
    {{ invoicePrefix() . $invoice->invoice_id }} {{ __('Details') }}
@endsection
@php
    $admin_logo = getSettingsValByName('company_logo');
@endphp
@push('script-page')
    <script>
        $(document).on('click', '.print', function() {
            $('.action').addClass('d-none');
            var printContents = document.getElementById('invoice-print').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            $('.action').removeClass('d-none');
        });
    </script>
@endpush

@section('breadcrumb')
    <ul class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('invoice.index') }}">{{ __('Invoice') }}</a>
        </li>
        <li class="breadcrumb-item active">
            <a href="#">
                {{ invoicePrefix() . $invoice->invoice_id }} {{ __('Details') }}
            </a>
        </li>
    </ul>
@endsection
@push('script-page')
    <script>
        $(document).on('click', '.print', function() {
            $('.action').addClass('d-none');
            var printContents = document.getElementById('invoice-print').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            $('.action').removeClass('d-none');
        });
    </script>

    <script src="https://js.stripe.com/v3/"></script>

    @if (
        $invoicePaymentSettings['STRIPE_PAYMENT'] == 'on' &&
            !empty($invoicePaymentSettings['STRIPE_KEY']) &&
            !empty($invoicePaymentSettings['STRIPE_SECRET']))
        <script type="text/javascript">
            let stripeCardInstance = null;

            $(document).on('click', '.stripe_payment_tab', function() {
                // Destroy old Stripe element if exists
                if (stripeCardInstance) {
                    stripeCardInstance.unmount();
                    $('#card-element').html(''); // Clear old element content
                }

                // Create new Stripe instance
                const stripe = Stripe('{{ $invoicePaymentSettings['STRIPE_KEY'] }}');
                const elements = stripe.elements();
                const style = {
                    base: {
                        fontSize: '14px',
                        color: '#32325d',
                    },
                };
                stripeCardInstance = elements.create('card', {
                    style: style
                });
                stripeCardInstance.mount('#card-element');

                // Attach submit handler only once
                const stripeForm = document.getElementById('stripe-payment');
                if (!stripeForm.dataset.handlerAttached) {
                    stripeForm.addEventListener('submit', function(event) {
                        event.preventDefault();

                        const billingDetails = {
                            line1: document.querySelector('[name="state"]')?.value || '',
                            city: document.querySelector('[name="city"]')?.value || '',
                            postal_code: document.querySelector('[name="zipcode"]')?.value || '',
                            country: document.querySelector('[name="country"]')?.value || ''
                        };

                        stripe.createToken(stripeCardInstance).then(function(result) {
                            if (result.error) {
                                $("#stripe_card_errors").html(result.error.message);
                                $.NotificationApp.send("Error", result.error.message, "top-right",
                                    "rgba(0,0,0,0.2)", "error");
                            } else {
                                const token = result.token;
                                const hiddenInput = document.createElement('input');
                                hiddenInput.setAttribute('type', 'hidden');
                                hiddenInput.setAttribute('name', 'stripeToken');
                                hiddenInput.setAttribute('value', token.id);
                                stripeForm.appendChild(hiddenInput);
                                stripeForm.submit();
                            }
                        });
                    });
                    stripeForm.dataset.handlerAttached = "true";
                }
            });
        </script>
    @endif


    {{-- ************************* flutterwave payment script ************************* --}}
    <script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
    <script>
        $(document).on("click", "#flutterwavePaymentBtn", function() {
            var amount = $('.amount').val().trim();
            if (!amount || amount <= 0) {
                alert('Please enter a valid amount');
                return;
            }

            var tx_ref = "RX1_" + Math.floor((Math.random() * 1000000000) + 1);
            var customer_email = '{{ \Auth::user()->email }}';
            var customer_name = '{{ \Auth::user()->name }}';
            var flutterwave_public_key = '{{ $invoicePaymentSettings['flutterwave_public_key'] }}';
            var currency = '{{ $invoicePaymentSettings['CURRENCY'] }}';

            var flutterwavePayment = getpaidSetup({
                txref: tx_ref,
                PBFPubKey: flutterwave_public_key,
                amount: amount, // Ensure amount is passed
                currency: currency,
                customer_email: customer_email,
                customer_name: customer_name,
                meta: [{
                    metaname: "payment_id",
                    metavalue: "id"
                }],
                onclose: function() {},
                callback: function(result) {
                    if (result.tx.chargeResponseCode == "00" || result.tx.chargeResponseCode == "0") {
                        var txRef = result.tx.txRef;
                        var redirectUrl =
                            "{{ url('invoice/flutterwave') }}/{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}/" +
                            txRef + "?amount=" + amount;
                        window.location.href = redirectUrl;
                    } else {
                        alert('Payment failed');
                    }
                    flutterwavePayment.close();
                }
            });
        });
    </script>

    {{-- ************************* paystack payment script ************************* --}}
    <script src="{{ asset('assets/js/plugins/jquery.form.min.js') }}"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    @if (isset($invoicePaymentSettings['paystack_payment']) && $invoicePaymentSettings['paystack_payment'] == 'on')
        <script>
            $(document).ready(function() {
                $(document).on("click", "#paystackPaymentBtn", function(e) {
                    e.preventDefault();

                    const $button = $(this);
                    const $paymentForm = $('#paystack-payment-form');
                    const formActionUrl = $paymentForm.attr('action');
                    const formMethod = $paymentForm.attr('method');
                    const formSerializedData = $paymentForm.serialize();

                    const paystackPublicKey = "{{ $invoicePaymentSettings['paystack_public_key'] }}";
                    const redirectBaseUrl = "{{ url('/invoice/paystack') }}";
                    const encryptedInvoiceId = "{{ encrypt($invoice->id) }}";

                    $button.prop('disabled', true).text('Processing...');

                    $.ajax({
                        url: formActionUrl,
                        method: formMethod,
                        data: formSerializedData,
                        dataType: 'json',
                        success: function(res) {
                            if (res.flag === 1) {
                                const transactionReference = 'pay_ref_id' + Math.floor(Math
                                    .random() * 1000000000 + 1);
                                const couponId = res.coupon;

                                const paystackOptions = {
                                    key: paystackPublicKey,
                                    email: res.email,
                                    amount: res.total_price * 100,
                                    currency: res.currency,
                                    ref: transactionReference,
                                    metadata: {
                                        custom_fields: [{
                                            display_name: "Email",
                                            variable_name: "email",
                                            value: res.email
                                        }]
                                    },
                                    callback: function(response) {
                                        window.location.href =
                                            `${redirectBaseUrl}/${response.reference}/${encryptedInvoiceId}?coupon_id=${couponId}`;
                                    },
                                    onClose: function() {
                                        alert(
                                            'Payment popup was closed without completing.'
                                        );
                                        $button.prop('disabled', false).text('Pay Now');
                                    }
                                };

                                const paymentHandler = PaystackPop.setup(paystackOptions);
                                paymentHandler.openIframe();
                            } else if (res.flag === 2) {
                                show_toastr('Warning', res.message, 'msg');
                                $button.prop('disabled', false).text('Pay Now');
                            } else {
                                show_toastr('Error', res.message, 'msg');
                                $button.prop('disabled', false).text('Pay Now');
                            }
                        },
                        error: function(xhr) {
                            console.error('AJAX Error:', xhr.responseText);
                            show_toastr('Error', 'An unexpected error occurred. Please try again.',
                                'msg');
                            $button.prop('disabled', false).text('Pay Now');
                        }
                    });
                });
            });
        </script>
    @endif
@endpush
@section('content')
    <div id="invoice-print" class="row">


        <div class="col-sm-12">
            <div class="d-print-none card mb-3">
                <div class="card-body p-3">
                    <ul class="list-inline ms-auto mb-0 d-flex justify-content-end flex-wrap">
                        <li class="list-inline-item align-bottom me-2">
                            @can('create invoice payment')
                                <a href="#" class="avtar avtar-s btn-link-secondary customModal" data-bs-toggle="tooltip"
                                    data-bs-original-title="{{ __('Add Item') }}" data-size="md"
                                    data-url="{{ route('invoice.item.create', $invoice->id) }}"
                                    data-title="{{ __('Add Item') }}">
                                    <i class="ph-duotone ph-text-indent f-22"></i>
                                </a>
                            @endcan
                        </li>
                        <li class="list-inline-item align-bottom me-2">
                            @can('create invoice payment')
                                @if ($invoice->getInvoiceDueAmount() > 0)
                                    <a href="#" class="avtar avtar-s btn-link-secondary customModal"
                                        data-bs-toggle="tooltip" data-bs-original-title="{{ __('Add Payment') }}"
                                        data-size="md" data-url="{{ route('invoice.payment.create', $invoice->id) }}"
                                        data-title="{{ __('Add Payment') }}">
                                        <i class="ph-duotone ph-credit-card f-22"></i>
                                    </a>
                                @endif
                            @endcan
                        </li>
                        <li class="list-inline-item align-bottom me-2">
                            <a href="javascript:void(0);" class="avtar avtar-s btn-link-secondary print"
                                data-bs-toggle="tooltip" data-bs-original-title="{{ __('Download') }}">
                                <i class="ph-duotone ph-printer f-22"></i>
                            </a>
                        </li>

                    </ul>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="row align-items-center g-3">
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center mb-2 navbar-brand img-fluid invoice-logo">
                                        <img src="{{ asset(Storage::url('upload/logo/')) . '/' . (isset($admin_logo) && !empty($admin_logo) ? $admin_logo : 'logo.png') }}"
                                            class="img-fluid brand-logo" alt="images" />
                                    </div>
                                    <p class="mb-0">{{ $invoice ? invoicePrefix() . $invoice->invoice_id : '' }}</p>
                                </div>
                                <div class="col-sm-6 text-sm-end">

                                    <h6>
                                        {{ __('Invoice Date') }}
                                        <span class="text-muted f-w-400">{{ dateFormat($invoice->invoice_date) }}</span>
                                    </h6>
                                    <h6>
                                        {{ __('Due Date') }}
                                        <span class="text-muted f-w-400">{{ dateFormat($invoice->due_date) }}</span>
                                    </h6>
                                    <h6>
                                        {{ __('Status') }}
                                        <span class="text-muted f-w-400">
                                            @if ($invoice->status == 'paid')
                                                <span
                                                    class="badge text-bg-success">{{ \App\Models\Invoice::$status[$invoice->status] }}</span>
                                            @elseif($invoice->status == 'partial_paid')
                                                <span
                                                    class="badge text-bg-warning">{{ \App\Models\Invoice::$status[$invoice->status] }}</span>
                                            @else
                                                <span
                                                    class="badge text-bg-danger">{{ \App\Models\Invoice::$status[$invoice->status] }}</span>
                                            @endif
                                        </span>
                                    </h6>

                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="border rounded p-3">
                                <h6 class="mb-0">{{ __('From') }} :</h6>
                                <h5>{{ $settings['company_name'] }}</h5>
                                <p class="mb-0">{{ $settings['company_phone'] }}</p>
                                <p class="mb-0">{{ $settings['company_email'] }}</p>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="border rounded p-3">
                                <h6 class="mb-0">{{ __('To') }} :</h6>
                                <h5>{{ !empty($invoice->customers) ? $invoice->customers->name : '' }}
                                </h5>
                                <p class="mb-0">
                                    {{ !empty($invoice->customers) ? $invoice->customers->phone_number : '' }}
                                </p>
                                <p class="mb-0">
                                    {{ !empty($invoice->customers) && !empty($invoice->customers->customers) ? $invoice->customers->customers->address : '' }}
                                    ,
                                    <br>
                                    {{ !empty($invoice->customers) && !empty($invoice->customers->customers) ? $invoice->customers->customers->city : '' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Item') }}</th>
                                            <th>{{ __('Quantity') }}</th>
                                            <th>{{ __('Tax') }}</th>
                                            <th>{{ __('Note') }}</th>
                                            <th>{{ __('Amount') }}</th>
                                            @can('delete invoice item')
                                                <th class="action">{{ __('Action') }}</th>
                                            @endcan
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $itemTaxData = [];
                                        @endphp
                                        @foreach ($invoice->items as $itemDetails)
                                            @php
                                                $itemTax = !empty($itemDetails) ? $itemDetails->tax : 0;
                                                $itemTaxes = getTax($itemTax);
                                                foreach ($itemTaxes as $taxe) {
                                                    $taxDataPrice = taxRate(
                                                        $taxe->rate,
                                                        $itemDetails->amount,
                                                        $itemDetails->quantity,
                                                    );
                                                    if (array_key_exists($taxe->title, $itemTaxData)) {
                                                        $itemTaxData[$taxe->tax] =
                                                            $itemTaxData[$taxe->tax] + $taxDataPrice;
                                                    } else {
                                                        $itemTaxData[$taxe->tax] = $taxDataPrice;
                                                    }
                                                }

                                            @endphp
                                            <tr>
                                                <td>{{ !empty($itemDetails->clothTypes) ? $itemDetails->clothTypes->title : '-' }}
                                                </td>
                                                <td>{{ $itemDetails->quantity }}</td>
                                                <td>
                                                    @foreach ($itemTaxes as $taxData)
                                                        @php
                                                            $taxAmount = taxRate(
                                                                $taxData->rate,
                                                                $itemDetails->amount,
                                                                $itemDetails->quantity,
                                                            );
                                                        @endphp
                                                        {{ __('Tax') }} : {{ $taxData->tax }} |
                                                        {{ __('Rate') }} : {{ $taxData->rate . '%' }} |
                                                        {{ __('Price') }} : {{ priceFormat($taxAmount) }}
                                                        <br>
                                                    @endforeach
                                                </td>
                                                <td>{{ !empty($itemDetails->note) ? $itemDetails->note : '-' }}</td>
                                                <td>{{ priceFormat($itemDetails->amount * $itemDetails->quantity) }}</td>
                                                @can('delete invoice item')
                                                    <td class="text-right action">
                                                        <div class="cart-action">
                                                            {!! Form::open(['method' => 'DELETE', 'route' => ['invoice.item.destroy', $invoice->id, $itemDetails->id]]) !!}
                                                            <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                                                data-bs-toggle="tooltip"
                                                                data-bs-original-title="{{ __('Detete') }}" href="#"> <i
                                                                    data-feather="trash-2"></i></a>
                                                            {!! Form::close() !!}
                                                        </div>
                                                    </td>
                                                @endcan
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-start">
                                <hr class="mb-2 mt-1 border-secondary border-opacity-50" />
                            </div>
                        </div>

                        <div class="card-body p-3">
                            <div class="rounded p-3 bg-light-secondary">
                                <div class="row justify-content-end">
                                    <div class="col-auto">
                                        <div class="table-responsive">
                                            <table class="table table-borderless text-end mb-0">
                                                <tbody>
                                                    <tr>
                                                        <th>{{ __('Sub Total') }} :</th>
                                                        <td>{{ priceFormat($invoice->getInvoiceSubTotalAmount()) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ __('Total Tax') }} :</th>
                                                        <td>{{ priceFormat($invoice->getInvoiceTotalTax()) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="pe-0 pt-0">
                                                            <hr class="mb-3 mt-0" />
                                                            <h5 class="text-primary m-r-10">{{ __('Total Amount') }} :</h5>
                                                        </td>
                                                        <td class="ps-0 pt-0">
                                                            <hr class="mb-3 mt-0" />
                                                            <h5 class="text-primary">
                                                                {{ priceFormat($invoice->getInvoiceTotalAmount()) }}</h5>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="pe-0 pt-0">
                                                            <h5 class="text-primary m-r-10">{{ __('Due Amount') }} :</h5>
                                                        </td>
                                                        <td class="ps-0 pt-0">

                                                            <h5 class="text-primary">
                                                                {{ priceFormat($invoice->getInvoiceDueAmount()) }}</h5>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>{{ __('Payment History') }}</h5>
                </div>
                <div class="card-body pt-0">
                    <div class="dt-responsive table-responsive">
                        <table class="table table-hover ">
                            <thead>
                                <tr>
                                    <th>{{ __('Transaction Id') }}</th>
                                    <th>{{ __('Payment Date') }}</th>
                                    <th>{{ __('Payment Method') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Notes') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    @can('delete invoice payment')
                                        <th class="text-right action">{{ __('Action') }}</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoice->payments as $payment)
                                    <tr role="row">
                                        <td>{{ $payment->transaction_id }} </td>
                                        <td>{{ dateFormat($payment->payment_date) }} </td>
                                        <td>{{ \App\Models\Invoice::$paymentMethodnew[$payment->payment_type] }} </td>
                                        <td>{{ priceFormat($payment->amount) }} </td>
                                        <td>{{ !empty($payment->notes) ? $payment->notes : '-' }} </td>
                                        <td>
                                            @if ($payment->payment_status == 'pending')
                                                <span
                                                    class="d-inline badge text-bg-warning text-capitalize ">{{ $payment->payment_status }}</span>
                                            @elseif($payment->payment_status == 'succeeded' || $payment->payment_status == 'Success')
                                                <span
                                                    class="d-inline badge text-bg-success text-capitalize">{{ $payment->payment_status }}</span>
                                            @else
                                                <span
                                                    class="d-inline badge text-bg-danger text-capitalize">{{ $payment->payment_status }}</span>
                                            @endif
                                        </td>
                                        @can('delete invoice payment')
                                            <td class="text-right action">
                                                <div class="cart-action">
                                                    @if (\Auth::user()->type == 'owner' && $payment->payment_status == 'pending')
                                                        <a class="avtar avtar-xs btn-link-success text-success"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-original-title="{{ __('Accept') }}"
                                                            href="{{ route('invoice.bank.transfer.action', [$payment->id, 'accept']) }}">
                                                            <i data-feather="user-check"></i></a>

                                                        <a class="avtar avtar-xs btn-link-danger text-danger"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-original-title="{{ __('Reject') }}"
                                                            href="{{ route('invoice.bank.transfer.action', [$payment->id, 'reject']) }}">
                                                            <i data-feather="user-x"></i></a>
                                                    @endif
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['invoice.payment.destroy', $invoice->id, $payment->id]]) !!}
                                                    <a class="avtar avtar-xs btn-link-danger text-danger confirm_dialog"
                                                        data-bs-toggle="tooltip" data-bs-original-title="{{ __('Detete') }}"
                                                        href="#"> <i data-feather="trash-2"></i></a>
                                                    {!! Form::close() !!}
                                                </div>
                                            </td>
                                        @endcan
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

