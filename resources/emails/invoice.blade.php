<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nature's Virtue Order Invoice - {{ $order->code }}</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f8f5; color: #333333;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table role="presentation" style="border-collapse: collapse; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <!-- Header with Logo -->
                    <tr>
                        <td align="center" style="padding: 30px 30px 20px 30px; border-bottom: 1px solid #e0e0e0;">
                            <img src="{{ asset('assets/logo/nv_logo.svg') }}" alt="Nature's Virtue Logo" style="max-width: 200px; height: auto;" />
                        </td>
                    </tr>

                    <!-- Thank You Message -->
                    <tr>
                        <td style="padding: 30px 30px 20px 30px;">
                            <h1 style="margin: 0 0 20px 0; font-size: 24px; line-height: 1.2; color: #2e7d32; text-align: center; font-weight: 700;">Thank You for Your Order!</h1>

                            @if(!empty($order->user))
                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6;">Dear {{ $order->user->name }},</p>
                            @else
                            @php
                                $shippingData = is_string($order->shipping_address)
                                    ? json_decode($order->shipping_address, true)
                                    : $order->shipping_address;
                            @endphp
                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6;">Dear {{ $shippingData['name'] ?? 'Customer' }},</p>
                            @endif

                            <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 1.6;">Thank you for shopping with Nature's Virtue. Your order has been confirmed and is being processed. Please find your invoice details below:</p>
                        </td>
                    </tr>

                    <!-- Order Summary -->
                    <tr>
                        <td style="padding: 0 30px 20px 30px;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f0f7f0; border-radius: 6px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <h2 style="margin: 0 0 15px 0; font-size: 18px; color: #2e7d32;">Order Summary</h2>

                                        <table role="presentation" style="width: 100%; border-collapse: collapse;">
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px;">Order Number:</td>
                                                <td style="padding: 8px 0; font-size: 14px; font-weight: bold; text-align: right;">{{ $order->code }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px;">Order Date:</td>
                                                <td style="padding: 8px 0; font-size: 14px; text-align: right;">{{ $order->created_at->format('d/m/Y') }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; font-size: 14px;">Payment Method:</td>
                                                <td style="padding: 8px 0; font-size: 14px; text-align: right;">{{ Str::title($order->payment_method) }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Shipping Address -->
                    <tr>
                        @php
                            $shippingAddress = is_string($order->shipping_address)
                                ? json_decode($order->shipping_address, true)
                                : $order->shipping_address;
                        @endphp
                        <td style="padding: 0 30px 20px 30px;">
                            <table role="presentation" style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="width: 48%; vertical-align: top; padding-right: 2%;">
                                        <h3 style="margin: 0 0 10px 0; font-size: 16px; color: #2e7d32;">Shipping Address</h3>
                                        <p style="margin: 0; font-size: 14px; line-height: 1.5;">
                                            @if(!empty($order->user))
                                            {{ $order->user->name }}<br>
                                            @else
                                            {{ $shippingAddress['name'] }} (Guest)<br>
                                            @endif
                                            {{ $shippingAddress['address'] }}<br>
                                            {{ $shippingAddress['city'] }}, {{ $shippingAddress['state'] }} {{ $shippingAddress['postal_code'] }}<br>
                                            {{ $shippingAddress['country'] }} <br>
                                            {{ $shippingAddress['phone'] }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Order Details -->
                    <tr>
                        <td style="padding: 0 30px 30px 30px;">
                            <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #2e7d32;">Order Details</h3>

                            <table role="presentation" style="width: 100%; border-collapse: collapse; border: 1px solid #e0e0e0;">
                                <!-- Table Header -->
                                <tr style="background-color: #f0f7f0;">
                                    <th style="padding: 12px 15px; text-align: left; font-size: 14px; border-bottom: 1px solid #e0e0e0;">Product</th>
                                    <th style="padding: 12px 15px; text-align: center; font-size: 14px; border-bottom: 1px solid #e0e0e0;">Quantity</th>
                                    <th style="padding: 12px 15px; text-align: right; font-size: 14px; border-bottom: 1px solid #e0e0e0;">Price</th>
                                    <th style="padding: 12px 15px; text-align: right; font-size: 14px; border-bottom: 1px solid #e0e0e0;">Total</th>
                                </tr>

                                @foreach ($order->items as $item)
                                    <tr>
                                        <td style="padding: 12px 15px; font-size: 14px; border-bottom: 1px solid #e0e0e0;">{{ $item->product->name }} @if($item->variant != "Standard" || $item->variant != null) ({{ $item->variant }}) @endif</td>
                                        <td style="padding: 12px 15px; text-align: center; font-size: 14px; border-bottom: 1px solid #e0e0e0;">{{ $item->quantity }}</td>
                                        <td style="padding: 12px 15px; text-align: right; font-size: 14px; border-bottom: 1px solid #e0e0e0;">{{ formatCurrency($item->unit_price) }}</td>
                                        <td style="padding: 12px 15px; text-align: right; font-size: 14px; border-bottom: 1px solid #e0e0e0;">{{ formatCurrency($item->total_price) }}</td>
                                    </tr>
                                @endforeach

                                <!-- You can add more product rows here -->

                                <!-- Subtotal Row -->
                                <tr>
                                    <td colspan="3" style="padding: 12px 15px; text-align: right; font-size: 14px; border-bottom: 1px solid #e0e0e0;">Subtotal:</td>
                                    <td style="padding: 12px 15px; text-align: right; font-size: 14px; border-bottom: 1px solid #e0e0e0;">{{ formatCurrency($order->grand_total - $order->shipping_cost) }}</td>
                                </tr>

                                <!-- Shipping Row -->
                                <tr>
                                    <td colspan="3" style="padding: 12px 15px; text-align: right; font-size: 14px; border-bottom: 1px solid #e0e0e0;">Shipping:</td>
                                    <td style="padding: 12px 15px; text-align: right; font-size: 14px; border-bottom: 1px solid #e0e0e0;">{{ formatCurrency($order->shipping_cost) }}</td>
                                </tr>

                                {{-- <!-- Tax Row -->
                                <tr>
                                    <td colspan="3" style="padding: 12px 15px; text-align: right; font-size: 14px; border-bottom: 1px solid #e0e0e0;">Tax:</td>
                                    <td style="padding: 12px 15px; text-align: right; font-size: 14px; border-bottom: 1px solid #e0e0e0;">{{ $order->tax }}</td>
                                </tr> --}}

                                <!-- Total Row -->
                                <tr style="background-color: #f0f7f0;">
                                    <td colspan="3" style="padding: 12px 15px; text-align: right; font-weight: bold; font-size: 16px;">Total:</td>
                                    <td style="padding: 12px 15px; text-align: right; font-weight: bold; font-size: 16px;">{{ formatCurrency($order->grand_total) }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
