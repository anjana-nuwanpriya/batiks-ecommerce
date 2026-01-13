<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\AdminNotificationService;
use stdClass;

class DashboardController extends Controller
{
    protected AdminNotificationService $notificationService;

    public function __construct(AdminNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(){

        $dashboard = new stdClass();

        $total_sales = Order::where('payment_status', 'paid')->sum('grand_total');
        $total_orders = Order::count();
        $total_products = Product::count();
        $total_customers = User::withoutUser()->count();

        // Get latest 10 registered users
        $latest_users = User::withoutUser()
            ->latest()
            ->take(3)
            ->get();

        // Get latest 10 orders
        $latest_orders = Order::with(['user'])
            ->latest()
            ->take(10)
            ->get();

        $dashboard->latest_users = $latest_users;
        $dashboard->latest_orders = $latest_orders;

        $dashboard->total_sales = $total_sales;
        $dashboard->total_orders = $total_orders;
        $dashboard->total_products = $total_products;
        $dashboard->total_customers = $total_customers;


        // Get start and end dates for current week (Sunday to Saturday)
        $startOfWeek = now()->startOfWeek(0); // 0 = Sunday
        $endOfWeek = now()->endOfWeek(6); // 6 = Saturday

        // Get daily sales for current week
        $weeklySales = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->selectRaw('DATE(created_at) as date, SUM(grand_total) as total')
            ->groupBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        // Initialize sales data array with 0 for all days
        $salesData = [];
        $dayNames = [];
        for($date = $startOfWeek->copy(); $date <= $endOfWeek; $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $dayNames[] = $date->format('l'); // 'l' format gives full day name (Sunday, Monday, etc.)
            $salesData[$dateStr] = $weeklySales[$dateStr] ?? 0;
        }

        $dashboard->weekly_sales = [
            'labels' => $dayNames,
            'data' => array_values($salesData),
            'colors' => [
                '#FF6384', // Sunday
                '#36A2EB', // Monday
                '#FFCE56', // Tuesday
                '#4BC0C0', // Wednesday
                '#9966FF', // Thursday
                '#FF9F40', // Friday
                '#C9CBCF'  // Saturday
            ]
        ];

        // Get notification data
        $notifications = $this->notificationService->getDashboardSummary();

        return view('admin.dashboard', compact('dashboard', 'latest_users', 'latest_orders', 'notifications'));
    }

}
