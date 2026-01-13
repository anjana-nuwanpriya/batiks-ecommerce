<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackingController extends Controller
{

    /**
     * Show tracking page
     */
    public function index()
    {
        return view('frontend.tracking.index');
    }

    /**
     * Track order by waybill number
     */
    public function track(Request $request)
    {
        $request->validate([
            'waybill_no' => 'required|string'
        ]);

        $waybillNo = $request->waybill_no;

        // Find order by waybill number
        $order = Order::where('waybill_no', $waybillNo)->first();

        if (!$order) {
            return back()->with('error', 'Order not found with this waybill number.');
        }

        // Simple tracking data without courier service integration
        $trackingData = [
            [
                'status' => ucfirst($order->delivery_status),
                'statusDate' => $order->updated_at->format('Y-m-d H:i:s'),
                'dateTime' => $order->updated_at->format('Y-m-d H:i:s'),
                'location' => 'Order Processing Center',
                'description' => 'Order status updated',
                'branchName' => ''
            ]
        ];

        return view('frontend.tracking.show', compact('order', 'trackingData') + ['waybillNo' => $waybillNo]);
    }

    /**
     * Show order tracking for authenticated users
     */
    public function orderTracking($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Check if user owns this order
        if (Auth::check() && $order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to order.');
        }

        if (!$order->waybill_no) {
            return back()->with('error', 'Tracking information not available for this order.');
        }

        // Simple tracking data without courier service integration
        $trackingData = [
            [
                'status' => ucfirst($order->delivery_status),
                'statusDate' => $order->updated_at->format('Y-m-d H:i:s'),
                'dateTime' => $order->updated_at->format('Y-m-d H:i:s'),
                'location' => 'Order Processing Center',
                'description' => 'Order status updated',
                'branchName' => ''
            ]
        ];

        return view('frontend.tracking.show', compact('order', 'trackingData') + ['waybillNo' => $order->waybill_no]);
    }

    /**
     * AJAX endpoint for real-time tracking updates
     */
    public function getTrackingStatus(Request $request)
    {
        $request->validate([
            'waybill_no' => 'required|string'
        ]);

        // Find order by waybill number to determine courier service
        $order = Order::where('waybill_no', $request->waybill_no)->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'error' => 'Order not found'
            ]);
        }

        // Simple tracking data without courier service integration
        $trackingData = [
            [
                'status' => ucfirst($order->delivery_status),
                'statusDate' => $order->updated_at->format('Y-m-d H:i:s'),
                'dateTime' => $order->updated_at->format('Y-m-d H:i:s'),
                'location' => 'Order Processing Center',
                'description' => 'Order status updated',
                'branchName' => ''
            ]
        ];

        return response()->json([
            'success' => !isset($trackingData['error']),
            'data' => $trackingData
        ]);
    }
}
