<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderInfo;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users and products to work with
        $users = User::all();
        $products = Product::all();

        // Generate 50 orders
        for ($i = 0; $i < 50; $i++) {
            $user = $users->random();

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'shipping_address' => json_encode([
                    'name' => fake()->name(),
                    'phone' => fake()->phoneNumber(),
                    'address' => fake()->streetAddress(),
                    'city' => fake()->city(),
                    'state' => fake()->state(),
                    'postal_code' => fake()->postcode(),
                ]),
                'code' => 'ORD-' . strtoupper(Str::random(8)),
                'payment_method' => fake()->randomElement(['credit_card', 'paypal', 'bank_transfer']),
                'payment_status' => fake()->randomElement(['pending', 'paid', 'failed']),
                'shipping_cost' => fake()->randomFloat(2, 5, 20),
                'coupon_discount' => fake()->randomFloat(2, 0, 50),
                'handling_fee' => fake()->randomFloat(2, 1, 5),
                'delivery_status' => fake()->randomElement(['pending', 'processing', 'delivered', 'cancelled']),
                'note' => fake()->optional()->sentence(),
            ]);

            // Generate 1-5 order items for each order
            $numItems = rand(1, 5);
            $grandTotal = 0;

            for ($j = 0; $j < $numItems; $j++) {
                $product = $products->random();
                $quantity = rand(1, 5);
                $unitPrice = $product->price;
                $couponDiscount = fake()->randomFloat(2, 0, 10);
                $totalPrice = ($quantity * $unitPrice) - $couponDiscount;
                $weight = $product->weight ?? fake()->randomFloat(2, 100, 1000);
                $weightCost = $weight * 0.01; // Example weight cost calculation

                OrderInfo::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'variant' => fake()->optional()->word(),
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'coupon_discount' => $couponDiscount,
                    'total_price' => $totalPrice,
                    'weight' => $weight,
                    'weight_cost' => $weightCost,
                ]);

                $grandTotal += $totalPrice;
            }

            // Update order's grand total
            $order->update([
                'grand_total' => $grandTotal + $order->shipping_cost + $order->handling_fee - $order->coupon_discount
            ]);
        }
    }
}
