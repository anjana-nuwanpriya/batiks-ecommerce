<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OverseasOrder;
use App\Models\OverseasOrderItem;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OverseasOrderController extends Controller
{
    public function index()
    {
        $orders = OverseasOrder::with(['user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.overseas-orders.index', compact('orders'));
    }

    public function create()
    {
        $users = User::withoutUser()->get();
        $products = Product::with('stocks')->get();

        return view('admin.overseas-orders.create', compact('users', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'shipping_cost' => 'required|numeric|min:0',
            'shipping_address' => 'required|array',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.variant' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $order = null;
        DB::transaction(function () use ($request, &$order) {
            $totalAmount = collect($request->items)->sum(function ($item) {
                return $item['quantity'] * $item['unit_price'];
            });

            $order = OverseasOrder::create([
                'user_id' => $request->user_id,
                'total_amount' => $totalAmount,
                'shipping_cost' => $request->shipping_cost,
                'shipping_address' => $request->shipping_address,
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $item) {
                OverseasOrderItem::create([
                    'overseas_order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'variant' => $item['variant'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            // Log order creation
            activity('overseas_order_management')
                ->causedBy(auth()->user())
                ->performedOn($order)
                ->withProperties([
                    'operation_type' => 'create',
                    'order_id' => $order->id,
                    'customer_id' => $order->user_id,
                    'customer_name' => $order->user->name,
                    'total_amount' => $order->total_amount,
                    'shipping_cost' => $order->shipping_cost,
                    'items_count' => count($request->items),
                    'items' => collect($request->items)->map(function ($item) {
                        $product = Product::find($item['product_id']);
                        return [
                            'product_name' => $product->name,
                            'variant' => $item['variant'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['unit_price']
                        ];
                    })->toArray()
                ])
                ->log("Created overseas order #{$order->id} for customer {$order->user->name}");
        });

        return redirect()->route('admin.overseas-orders.index')
            ->with('success', 'Overseas order created successfully.');
    }

    public function show(OverseasOrder $overseasOrder)
    {
        $overseasOrder->load(['user', 'items.product']);
        return view('admin.overseas-orders.show', compact('overseasOrder'));
    }

    public function edit(OverseasOrder $overseasOrder)
    {
        $overseasOrder->load(['items.product']);
        $users = User::withoutUser()->get();
        $products = Product::with('stocks')->get();

        return view('admin.overseas-orders.edit', compact('overseasOrder', 'users', 'products'));
    }

    public function update(Request $request, OverseasOrder $overseasOrder)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'shipping_cost' => 'required|numeric|min:0',
            'shipping_address' => 'required|array',
            'notes' => 'nullable|string',
        ]);

        // Store original values for logging
        $originalStatus = $overseasOrder->status;
        $originalShippingCost = $overseasOrder->shipping_cost;
        $originalNotes = $overseasOrder->notes;

        if ($request->status == 'shipped' && $overseasOrder->status != "shipped") {
            $overseasItems = $overseasOrder->items;
            foreach ($overseasItems as $overseasItem) {
                $productStock = ProductStock::where('product_id', $overseasItem->product_id)->where('variant', $overseasItem->variant)->first();

                if ($productStock) {
                    $oldQty = $productStock->qty;
                    $productStock->decrement('qty', $overseasItem->quantity);

                    // Log the stock update
                    activity('overseas_order_management')
                        ->causedBy(auth()->user())
                        ->performedOn($productStock)
                        ->withProperties([
                            'operation_type' => 'stock_decrement',
                            'overseas_order_id' => $overseasOrder->id,
                            'old_quantity' => $oldQty,
                            'new_quantity' => $productStock->qty,
                            'decremented_by' => $overseasItem->quantity
                        ])
                        ->log("Decremented stock for overseas order shipment");
                }
            }
        }

        $overseasOrder->update([
            'status' => $request->status,
            'shipping_cost' => $request->shipping_cost,
            'shipping_address' => $request->shipping_address,
            'notes' => $request->notes,
        ]);

        // Log order update
        $changes = [];
        if ($originalStatus !== $request->status) {
            $changes['status'] = ['from' => $originalStatus, 'to' => $request->status];
        }
        if ($originalShippingCost != $request->shipping_cost) {
            $changes['shipping_cost'] = ['from' => $originalShippingCost, 'to' => $request->shipping_cost];
        }
        if ($originalNotes !== $request->notes) {
            $changes['notes'] = ['from' => $originalNotes, 'to' => $request->notes];
        }

        if (!empty($changes)) {
            activity('overseas_order_management')
                ->causedBy(auth()->user())
                ->performedOn($overseasOrder)
                ->withProperties([
                    'operation_type' => 'update',
                    'order_id' => $overseasOrder->id,
                    'customer_name' => $overseasOrder->user->name,
                    'changes' => $changes
                ])
                ->log("Updated overseas order #{$overseasOrder->id} - " . implode(', ', array_keys($changes)) . " changed");
        }

        toast('Updated Successfully', 'success');
        return redirect()->route('admin.overseas-orders.index')
            ->with('success', 'Overseas order updated successfully.');
    }

    public function destroy(OverseasOrder $overseasOrder)
    {
        DB::transaction(function () use ($overseasOrder) {
            // If the order was shipped, we need to reverse the stock changes
            if ($overseasOrder->status == 'shipped') {
                $overseasItems = $overseasOrder->items;
                foreach ($overseasItems as $overseasItem) {
                    $productStock = ProductStock::where('product_id', $overseasItem->product_id)
                        ->where('variant', $overseasItem->variant)
                        ->first();

                    if ($productStock) {
                        $oldQty = $productStock->qty;
                        $productStock->increment('qty', $overseasItem->quantity);

                        // Log the stock restoration
                        activity('overseas_order_management')
                            ->causedBy(auth()->user())
                            ->performedOn($productStock)
                            ->withProperties([
                                'operation_type' => 'stock_increment',
                                'overseas_order_id' => $overseasOrder->id,
                                'old_quantity' => $oldQty,
                                'new_quantity' => $productStock->qty,
                                'incremented_by' => $overseasItem->quantity,
                                'reason' => 'order_deletion'
                            ])
                            ->log("Restored stock after overseas order deletion");
                    }
                }
            }

            // Log the order deletion
            activity('overseas_order_management')
                ->causedBy(auth()->user())
                ->performedOn($overseasOrder)
                ->withProperties([
                    'operation_type' => 'delete',
                    'order_id' => $overseasOrder->id,
                    'order_status' => $overseasOrder->status,
                    'total_amount' => $overseasOrder->total_amount,
                    'items_count' => $overseasOrder->items->count()
                ])
                ->log("Deleted overseas order #{$overseasOrder->id}");

            $overseasOrder->delete();
        });

        toast('Deleted Successfully', 'success');
        return redirect()->route('admin.overseas-orders.index')
            ->with('success', 'Overseas order deleted successfully.');
    }

    public function getProductVariants(Request $request)
    {
        $product = Product::with('stocks')->find($request->product_id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ]);
        }

        return response()->json([
            'success' => true,
            'variants' => $product->stocks,
        ]);
    }

    public function createCustomer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt('password123'), // Default password
            'is_active' => 1,
        ]);

        // Log customer creation
        activity('customer_management')
            ->causedBy(auth()->user())
            ->performedOn($user)
            ->withProperties([
                'operation_type' => 'create',
                'customer_id' => $user->id,
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone,
                'created_from' => 'overseas_order_form',
                'default_password_set' => true
            ])
            ->log("Created new customer {$user->name} ({$user->email}) from overseas order form");

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }
}
