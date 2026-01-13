<?php

namespace App\Services;

use App\Models\Order;
use App\Utilities\HutchSmsUtility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send order confirmation SMS
     *
     * @param Order $order
     * @param string $phoneNumber
     * @return bool
     */
    public function sendOrderConfirmationSms(Order $order, string $phoneNumber): bool
    {
        try {
            $message = $this->buildOrderConfirmationMessage($order);
            $result = HutchSmsUtility::sendSms($phoneNumber, $message);

            if ($result) {
                Log::info('Order confirmation SMS sent successfully', [
                    'order_id' => $order->id,
                    'phone' => $phoneNumber,
                    'server_ref' => $result
                ]);
                return true;
            }

            Log::warning('Order confirmation SMS failed', [
                'order_id' => $order->id,
                'phone' => $phoneNumber
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Order confirmation SMS exception', [
                'order_id' => $order->id,
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send order status update SMS
     *
     * @param Order $order
     * @param string $phoneNumber
     * @param string $status
     * @return bool
     */
    public function sendOrderStatusSms(Order $order, string $phoneNumber, string $status): bool
    {
        try {
            $message = $this->buildOrderStatusMessage($order, $status);
            $result = HutchSmsUtility::sendSms($phoneNumber, $message);

            if ($result) {
                Log::info('Order status SMS sent successfully', [
                    'order_id' => $order->id,
                    'phone' => $phoneNumber,
                    'status' => $status,
                    'server_ref' => $result
                ]);
                return true;
            }

            Log::warning('Order status SMS failed', [
                'order_id' => $order->id,
                'phone' => $phoneNumber,
                'status' => $status
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Order status SMS exception', [
                'order_id' => $order->id,
                'phone' => $phoneNumber,
                'status' => $status,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send payment confirmation SMS
     *
     * @param Order $order
     * @param string $phoneNumber
     * @return bool
     */
    public function sendPaymentConfirmationSms(Order $order, string $phoneNumber): bool
    {
        try {
            $message = $this->buildPaymentConfirmationMessage($order);
            $result = HutchSmsUtility::sendSms($phoneNumber, $message);

            if ($result) {
                Log::info('Payment confirmation SMS sent successfully', [
                    'order_id' => $order->id,
                    'phone' => $phoneNumber,
                    'server_ref' => $result
                ]);
                return true;
            }

            Log::warning('Payment confirmation SMS failed', [
                'order_id' => $order->id,
                'phone' => $phoneNumber
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Payment confirmation SMS exception', [
                'order_id' => $order->id,
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send custom SMS
     *
     * @param string|array $phoneNumbers
     * @param string $message
     * @return bool
     */
    public function sendCustomSms($phoneNumbers, string $message): bool
    {
        try {

            $result = HutchSmsUtility::sendSms($phoneNumbers, $message);
            if ($result) {
                Log::info('Custom SMS sent successfully', [
                    'phones' => is_array($phoneNumbers) ? $phoneNumbers : [$phoneNumbers],
                    'server_ref' => $result
                ]);
                return true;
            }

            Log::warning('Custom SMS failed', [
                'phones' => is_array($phoneNumbers) ? $phoneNumbers : [$phoneNumbers]
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Custom SMS exception', [
                'phones' => is_array($phoneNumbers) ? $phoneNumbers : [$phoneNumbers],
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Build order confirmation message
     *
     * @param Order $order
     * @return string
     */
    private function buildOrderConfirmationMessage(Order $order): string
    {
        $trackingUrl = Auth::check()
            ? route('user.order.view', $order->id)
            : route('cart.order.complete', $order->id);

        $message = "Your order #{$order->code} has been confirmed. ";

        if (!empty($order->waybill_no)) {
            $message .= "Waybill No: {$order->waybill_no}. ";
            $message .= "Track: https://bit.ly/4n7rRft?waybill_no={$order->waybill_no}. ";
        }

        $message .= "Thank you for choosing Nature's Virtue!";

        return $message;
    }

    /**
     * Build order status update message
     *
     * @param Order $order
     * @param string $status
     * @return string
     */
    private function buildOrderStatusMessage(Order $order, string $status): string
    {
        $statusMessages = [
            'processing' => 'is being processed',
            'shipped' => 'has been shipped',
            'delivered' => 'has been delivered',
            'cancelled' => 'has been cancelled',
            'refunded' => 'has been refunded',
        ];

        $statusText = $statusMessages[$status] ?? "status has been updated to {$status}";

        $message = "Your order #{$order->code} {$statusText}. ";

        if (!empty($order->waybill_no)) {
            $message .= "Waybill No: {$order->waybill_no}. ";
            $message .= "Track: https://bit.ly/4n7rRft?waybill_no={$order->waybill_no}. ";
        }

        $message .= "Thank you for choosing Nature's Virtue!";

        return $message;
    }

    /**
     * Build payment confirmation message
     *
     * @param Order $order
     * @return string
     */
    private function buildPaymentConfirmationMessage(Order $order): string
    {

        $message = "Payment confirmed for order #" . str_pad($order->id, 4, '0', STR_PAD_LEFT) . " ";

        if (!empty($order->waybill_no)) {
            $message .= "Waybill No: {$order->waybill_no}. ";
            $message .= "Track: https://bit.ly/4n7rRft?waybill_no={$order->waybill_no}. ";
        }

        $message .= "Thank you for choosing Nature's Virtue! Your order is now being processed and will be on its way to you shortly. ";

        return $message;
    }

    /**
     * Check if SMS service is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool) env('SMS_SERVICE', false);
    }

    /**
     * Get SMS service status
     *
     * @return array
     */
    public function getServiceStatus(): array
    {
        return [
            'enabled' => $this->isEnabled(),
            'username_configured' => !empty(env('HUTCH_SMS_USERNAME')),
            'password_configured' => !empty(env('HUTCH_SMS_PASSWORD')),
            'campaign_configured' => !empty(env('HUTCH_CAMPAIGN_NAME')),
            'mask_configured' => !empty(env('HUTCH_MASK')),
            'debug_mode' => (bool) env('HUTCH_DEBUG', false),
        ];
    }
}
