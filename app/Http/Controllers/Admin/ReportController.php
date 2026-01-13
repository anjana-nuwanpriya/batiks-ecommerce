<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use stdClass;

class ReportController extends Controller
{

    public function salesReport(Request $request)
    {
        //date_range=2025-05-10
        //date_range=2025-05-07 to 2025-06-09
        $dateRange = $request->date_range;

        // Default to current month range
        $startDate = Carbon::now()->startOfMonth()->startOfDay();
        $endDate = Carbon::now()->endOfMonth()->endOfDay();

        if (!empty($dateRange)) {
            if (strpos($dateRange, ' to ') !== false) {
                [$start, $end] = explode(' to ', $dateRange);
                $startDate = Carbon::parse($start)->startOfDay();
                $endDate = Carbon::parse($end)->endOfDay();
            } else {
                // Single date scenario
                $startDate = Carbon::parse($dateRange)->startOfDay();
                $endDate = Carbon::parse($dateRange)->endOfDay();
            }
        }

        // Query orders with order items and user
        $orders = Order::with(['items.product.categories', 'user'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $paidOrders = $orders->where('payment_status', 'paid');

        // Calculate summary statistics
        $summary = new stdClass();
        $summary->total_orders = $paidOrders->count();
        $summary->total_products = $paidOrders->flatMap(function ($order) {
            return $order->items;
        })->sum('quantity');
        $summary->total_amount = $paidOrders->sum('grand_total');
        $summary->average_order_value = $summary->total_orders > 0 ? $summary->total_amount / $summary->total_orders : 0;

        // Calculate new customers in the date range
        $summary->new_customers = User::whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('orders')
            ->count();

        // Get top performing products
        $topProducts = $paidOrders->flatMap(function ($order) {
            return $order->items;
        })->groupBy('product_id')->map(function ($items) {
            $product = $items->first()->product;
            return [
                'name' => $product->name,
                'category' => $product->categories->first()->name ?? 'Uncategorized',
                'revenue' => $items->sum(function ($item) {
                    return $item->quantity * $item->unit_price;
                }),
                'units_sold' => $items->sum('quantity')
            ];
        })->sortByDesc('revenue')->take(3)->values();

        // Get sales by category
        $categoryData = $paidOrders->flatMap(function ($order) {
            return $order->items;
        })->groupBy(function ($item) {
            return $item->product->categories->first()->name ?? 'Uncategorized';
        })->map(function ($items, $category) {
            return [
                'category' => $category,
                'revenue' => $items->sum(function ($item) {
                    return $item->quantity * $item->unit_price;
                }),
                'percentage' => 0 // Will be calculated below
            ];
        });

        // Calculate percentages for categories
        $totalCategoryRevenue = $categoryData->sum('revenue');
        if ($totalCategoryRevenue > 0) {
            $categoryData = $categoryData->map(function ($item) use ($totalCategoryRevenue) {
                $item['percentage'] = round(($item['revenue'] / $totalCategoryRevenue) * 100, 1);
                return $item;
            });
        }

        // Get monthly sales data for chart (last 12 months)
        $monthlyData = collect();
        for ($i = 11; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();

            $monthOrders = Order::with('items')
                ->where('payment_status', 'paid')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->get();

            $monthlyData->push([
                'month' => $monthStart->format('M'),
                'revenue' => round($monthOrders->sum('grand_total') / 1000, 1), // in thousands
                'units' => $monthOrders->flatMap(function ($order) {
                    return $order->items;
                })->sum('quantity')
            ]);
        }

        return view('admin.reports.sales', compact(
            'dateRange',
            'startDate',
            'endDate',
            'summary',
            'orders',
            'topProducts',
            'categoryData',
            'monthlyData'
        ));
    }

    public function productReport()
    {
        return view('admin.reports.products-sales');
    }

    public function customerReport()
    {

        $customers = User::withoutUser()->get();

        return view('admin.reports.customer-report', compact('customers'));
    }

    public function productsStockReport()
    {
        $products = Product::with(['stocks', 'categories'])->get();

        // Calculate summary statistics
        $totalProducts = $products->count();
        $totalStockValue = $products->sum(function ($product) {
            return $product->stocks->sum(function ($stock) {
                // Ensure we have valid numeric values
                $qty = (float) ($stock->qty ?? 0);
                $price = (float) ($stock->selling_price ?? 0);
                return $qty * $price;
            });
        });

        $lowStockThreshold = 10; // Define low stock threshold
        $lowStockItems = $products->sum(function ($product) use ($lowStockThreshold) {
            return $product->stocks->where('qty', '<=', $lowStockThreshold)->where('qty', '>', 0)->count();
        });

        $outOfStockItems = $products->sum(function ($product) {
            return $product->stocks->where('qty', 0)->count();
        });

        $stockData = $products->flatMap(function ($product) {
            return $product->stocks->map(function ($stock) use ($product) {
                $stockValue = $stock->qty * $stock->selling_price;
                $status = $stock->qty == 0 ? 'Out of Stock' : ($stock->qty <= 10 ? 'Low Stock' : 'In Stock');
                $statusClass = $stock->qty == 0 ? 'danger' : ($stock->qty <= 10 ? 'warning' : 'success');

                return [
                    'product_name' => $product->name,
                    'sku' => $stock->sku ?? 'N/A',
                    'variant' => $stock->variant ?? 'Default',
                    'current_stock' => $stock->qty,
                    'unit_price' => $stock->selling_price,
                    'stock_value' => $stockValue,
                    'status' => $status,
                    'status_class' => $statusClass,
                    'categories' => $product->categories->pluck('name')->join(', ')
                ];
            });
        });

        return view('admin.reports.products-stock', compact(
            'stockData',
            'totalProducts',
            'totalStockValue',
            'lowStockItems',
            'outOfStockItems'
        ));
    }
}
