<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Payhere;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function payhereReturn(Request $request)
    {
        $checkoutController = new CheckoutController();
        $orderId = $request->order_id;
        $order = Order::find($orderId);
        if ($order->payment_status == 'paid') {
            return $checkoutController->orderComplete($orderId);
        } else {
            return $checkoutController->orderCancel($orderId);
        }
    }

    public function payhereCancel(Request $request)
    {
        $checkoutController = new CheckoutController();
        $orderId = $request->order_id;
        return $checkoutController->orderCancel($orderId);
    }

    public function payhereNotify(Request $request)
    {
        $data = $request->all();

        $payhere = new Payhere();
        $isPaymentValid = $payhere->verifyPayment($data);

        $orderId = $data['order_id'] ?? null;

        if (!$orderId) {
            Log::error('PayHere Notify: Missing order_id', ['request' => $data]);
            return response('Invalid Request', 400);
        }

        $order = Order::find($orderId);

        if (!$order) {
            Log::error('PayHere Notify: Order not found', ['order_id' => $orderId]);
            return response('Order Not Found', 404);
        }

        if ($isPaymentValid && $data['status_code'] == 2) {
            $order->payment_status = 'paid';
            $order->save();

            // Send payment confirmation SMS
            $this->sendPaymentConfirmationSms($order);


            Log::info('PayHere payment confirmed', ['order_id' => $orderId]);
        } else {
            $order->payment_status = 'failed';
            $order->save();

            //Reverse Stock
            $checkoutController = new CheckoutController();
            $checkoutController->reverseStock($orderId);

            Log::warning('PayHere payment failed', ['order_id' => $orderId]);
        }

        return response('OK', 200);
    }

    /**
     * Send payment confirmation notification
     *
     * @param Order $order
     * @return void
     */
    private function sendPaymentConfirmationSms(Order $order)
    {
        try {
            $notificationService = new NotificationService();
            $results = $notificationService->sendPaymentConfirmation($order);

            Log::info('Payment confirmation notifications sent', [
                'order_id' => $order->id,
                'email_sent' => $results['email'],
                'sms_sent' => $results['sms'],
                'user_data' => $results['user_data']
            ]);
        } catch (\Exception $e) {
            Log::error('Payment confirmation notification failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create delivery order with PromptAPT service after payment confirmation
     *
     * @param Order $order
     * @return void
     */
}
