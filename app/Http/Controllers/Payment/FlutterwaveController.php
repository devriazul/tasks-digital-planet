<?php

namespace App\Http\Controllers\Payment;

use App\Helper\Reply;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\OrderItems;
use App\Models\InvoiceItems;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use KingFlamez\Rave\Facades\Rave as Flutterwave;
use App\Http\Requests\PaymentGateway\FlutterwaveRequest;

class FlutterwaveController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.flutterwave');
    }

    public function paymentWithFlutterwavePublic(FlutterwaveRequest $request, $id)
    {

        switch ($request->type) {
        case 'invoice':
            $invoice = Invoice::find($id);
            $client = $invoice->client_id ? $invoice->client : $invoice->project->client;
            $description = __('app.invoice') . ' ' . $invoice->id;
            $amount = $invoice->amountDue();
            $currency = $invoice->currency ? $invoice->currency->currency_code : 'NGN';
            $callback_url = route('flutterwave.callback', [$id, 'invoice']);
            break;

        case 'order':
            $order = Order::find($id);
            $client = $order->client;
            $description = __('app.order') . ' ' . $order->id;
            $amount = $order->total;
            $currency = $order->currency ? $order->currency->currency_code : 'NGN';
            $callback_url = route('flutterwave.callback', [$id, 'order']);
            break;

        default:
            return Reply::error(__('messages.paymentTypeNotFound'));
        }

        try {
            // This generates a payment reference
            /** @phpstan-ignore-next-line */
            $reference = Flutterwave::generateReference();
            // Enter the details of the payment
            $data = [
                'payment_options' => 'card,banktransfer',
                'amount' => $amount,
                'email' => $request->email,
                'tx_ref' => $reference,
                'currency' => $currency,
                'redirect_url' => $callback_url,
                'customer' => [
                    'email' => $request->email,
                    'phone_number' => $request->phone,
                    'name' => $request->name
                ],

                'customizations' => [
                    'title' => $client,
                    'description' => $description
                ]
            ];

            /** @phpstan-ignore-next-line */
            $payment = Flutterwave::initializePayment($data);

            if ($payment['status'] !== 'success') {
                return Reply::error(__('modules.flutterwave.somethingWentWrong'));
            }

            return Reply::redirect($payment['data']['link']);
        } catch (\Throwable $th) {

            return Reply::error($th->getMessage());
        }
    }

    public function handleGatewayCallback(Request $request, $id, $type)
    {
        $status = $request->status;
        $transactionId = $request->transaction_id ?: $request->tx_ref;
        /** @phpstan-ignore-next-line */
        $data = Flutterwave::verifyTransaction($request->transaction_id);
        $amount = $data ? $data['data']['amount'] : 0;

        switch ($type) {
        case 'invoice':
            $invoice = Invoice::findOrFail($id);
            $invoice->status = ($status == 'successful') ? 'paid' : 'unpaid';
            $invoice->save();

            $this->makePayment($amount ?: $invoice->amountDue(), $invoice, (($status == 'successful') ? 'complete' : 'failed'), $transactionId);

            return redirect(route('front.invoice', $invoice->hash));

        case 'order':

            if ($status == 'successful') {
                $invoice = $this->makeOrderInvoice($id);
                $this->makePayment($amount, $invoice, 'complete', $transactionId);
            }

            return redirect()->route('orders.show', $id);

        default:
            return redirect()->route('dashboard');
        }
    }

    public function makePayment($amount, $invoice, $status = 'pending', $transactionId = null, $gateway = 'Flutterwave')
    {

        $payment = Payment::where('transaction_id', $transactionId)->whereNotNull('transaction_id')->first();

        $payment = ($payment && $transactionId) ? $payment : new Payment();
        $payment->project_id = $invoice->project_id;
        $payment->invoice_id = $invoice->id;
        $payment->order_id = $invoice->order_id;
        $payment->gateway = $gateway;
        $payment->transaction_id = $transactionId;
        $payment->event_id = $transactionId;
        $payment->currency_id = $invoice->currency_id;
        $payment->amount = $amount;
        $payment->paid_on = Carbon::now();
        $payment->status = $status;
        $payment->save();

        return $payment;
    }

    public function makeOrderInvoice($orderId)
    {
        $order = Order::find($orderId);
        $order->status = 'paid';
        $order->save();

        if($order->invoice)
        {
            return $order->invoice;
        }

        /* Step2 - make an invoice related to recently paid order_id */
        $invoice = new Invoice();
        $invoice->order_id = $orderId;
        $invoice->client_id = $order->client_id;
        $invoice->sub_total = $order->sub_total;
        $invoice->total = $order->total;
        $invoice->currency_id = $order->currency_id;
        $invoice->status = 'paid';
        $invoice->note = $order->note;
        $invoice->issue_date = Carbon::now();
        $invoice->send_status = 1;
        $invoice->invoice_number = Invoice::lastInvoiceNumber() + 1;
        $invoice->due_amount = 0;
        $invoice->save();

        /* Make invoice items */
        $orderItems = OrderItems::where('order_id', $order->id)->get();

        foreach ($orderItems as $item){
            InvoiceItems::create(
                [
                    'invoice_id'   => $invoice->id,
                    'item_name'    => $item->item_name,
                    'item_summary' => $item->item_summary,
                    'type'         => 'item',
                    'quantity'     => $item->quantity,
                    'unit_price'   => $item->unit_price,
                    'amount'       => $item->amount,
                    'taxes'        => $item->taxes
                ]
            );
        }

        return $invoice;
    }

}
