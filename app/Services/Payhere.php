<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Payhere
{
    private string $merchantId;
    private string $merchantSecret;
    private string $baseUrl;
    private bool $isTestMode;
    private string $returnUrl;
    private string $cancelUrl;
    private string $notifyUrl;
    private string $currency;

    public function __construct()
    {

        $this->merchantId = config('services.payhere.merchant_id');
        $this->merchantSecret = config('services.payhere.merchant_secret');
        $this->isTestMode = config('services.payhere.test_mode', true);
        $this->returnUrl = route('payment.payhere.return');
        $this->cancelUrl = route('payment.payhere.cancel');
        $this->notifyUrl = route('payment.payhere.notify');
        $this->currency = config('services.payhere.currency');
        $this->baseUrl = $this->isTestMode
            ? 'https://sandbox.payhere.lk/pay/checkout'
            : 'https://www.payhere.lk/pay/checkout';
    }

    public function createPayment(array $orderData): array
    {


        try {
            $hash = $this->generateHash($orderData);

            Log::info('Generated PayHere hash', ['hash' => $hash]);

            return [
                'url' => $this->baseUrl,
                'params' => array_merge($orderData, [
                    'merchant_id' => $this->merchantId,
                    'hash' => $hash,
                    'return_url' => $this->returnUrl,
                    'cancel_url' => $this->cancelUrl,
                    'notify_url' => $this->notifyUrl,
                    'currency' => $this->currency,
                ])
            ];
        } catch (\Exception $e) {
            Log::error('PayHere payment creation failed', [
                'error' => $e->getMessage(),
                'order_data' => $orderData
            ]);

            return [
                'error' => true,
                'message' => 'Payment initialization failed'
            ];
        }
    }

    public function verifyPayment(array $data): bool
    {
        try {
            $merchantId = $data['merchant_id'];
            $orderId = $data['order_id'];
            // $paymentId = $data['payment_id'];
            $paymentAmount = $data['payhere_amount'];
            $paymentCurrency = $data['payhere_currency'];
            $status = $data['status_code'];
            $receivedHash = $data['md5sig'];

            $localHash = strtoupper(
                md5(
                    $merchantId .
                    $orderId .
                    $paymentAmount .
                    $paymentCurrency .
                    $status .
                    strtoupper(md5($this->merchantSecret))
                )
            );


            return $receivedHash === $localHash;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function generateHash(array $orderData): string
    {
        return strtoupper(
            md5(
                $this->merchantId .
                $orderData['order_id'] .
                number_format($orderData['amount'], 2, '.', '') .
                $this->currency .
                strtoupper(md5($this->merchantSecret))
            )
        );
    }

    public function getForm(array $orderData): string
    {
        $payment = $this->createPayment($orderData);
        return view('frontend.payments.payhere-form', ['payment' => $payment])->render();
    }

}
