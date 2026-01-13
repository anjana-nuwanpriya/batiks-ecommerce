<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminActivityLogController extends Controller
{
    /**
     * Display all admin activity logs in one centralized place.
     */
    public function index(Request $request)
    {
        $query = Activity::with(['causer', 'subject'])
            ->whereIn('log_name', $this->getAdminLogNames())
            ->orderBy('created_at', 'desc');

        // Apply filters
        $this->applyFilters($query, $request);

        $activities = $query->paginate(50);

        // Get filter data
        $filterData = $this->getFilterData();

        return view('admin.activity-logs.index', compact('activities', 'filterData'));
    }

    /**
     * Get activity statistics for dashboard.
     */
    public function getStats(Request $request)
    {
        $baseQuery = Activity::whereIn('log_name', $this->getAdminLogNames());

        // Apply date filter if provided
        if ($request->filled('period')) {
            $baseQuery = $this->applyPeriodFilter($baseQuery, $request->period);
        }

        $stats = [
            'total_activities' => (clone $baseQuery)->count(),
            'today_activities' => (clone $baseQuery)->whereDate('created_at', today())->count(),
            'this_week_activities' => (clone $baseQuery)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month_activities' => (clone $baseQuery)->whereMonth('created_at', now()->month)->count(),
        ];

        // Activities by module
        $moduleStats = (clone $baseQuery)
            ->select('log_name', DB::raw('count(*) as count'))
            ->groupBy('log_name')
            ->pluck('count', 'log_name')
            ->toArray();

        // Activities by user (top 10)
        $userStats = (clone $baseQuery)
            ->select('causer_id', DB::raw('count(*) as count'))
            ->whereNotNull('causer_id')
            ->groupBy('causer_id')
            ->with('causer:id,name')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'user_name' => $item->causer->name ?? 'Unknown',
                    'count' => $item->count
                ];
            });

        // Recent activities by operation type
        $operationStats = (clone $baseQuery)
            ->select(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(properties, '$.operation_type')) as operation_type"), DB::raw('count(*) as count'))
            ->whereNotNull(DB::raw("JSON_EXTRACT(properties, '$.operation_type')"))
            ->groupBy('operation_type')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'operation_type')
            ->toArray();

        return response()->json([
            'stats' => $stats,
            'module_stats' => $moduleStats,
            'user_stats' => $userStats,
            'operation_stats' => $operationStats
        ]);
    }

    /**
     * Export activity logs to CSV.
     */
    public function export(Request $request)
    {
        $query = Activity::with(['causer', 'subject'])
            ->whereIn('log_name', $this->getAdminLogNames())
            ->orderBy('created_at', 'desc');

        $this->applyFilters($query, $request);

        $activities = $query->get();

        $filename = 'admin_activity_logs_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($activities) {
            $file = fopen('php://output', 'w');

            // CSV Header
            fputcsv($file, [
                'ID', 'Date & Time', 'User', 'Module', 'Operation Type',
                'Description', 'Subject Type', 'Subject ID', 'Batch UUID'
            ]);

            foreach ($activities as $activity) {
                $properties = $activity->properties ?? collect();

                fputcsv($file, [
                    $activity->id,
                    $activity->created_at->format('Y-m-d H:i:s'),
                    $activity->causer->name ?? 'System',
                    $this->getModuleName($activity->log_name),
                    $properties->get('operation_type', 'N/A'),
                    $activity->description,
                    $activity->subject_type,
                    $activity->subject_id,
                    $properties->get('batch_uuid', 'N/A')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get activity details for modal view.
     */
    public function show(Activity $activity)
    {
        $activity->load(['causer', 'subject']);

        return response()->json([
            'activity' => $activity,
            'module_name' => $this->getModuleName($activity->log_name),
            'formatted_properties' => $this->formatProperties($activity->properties ?? collect())
        ]);
    }

    /**
     * Delete old activity logs (cleanup).
     */
    public function cleanup(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        $cutoffDate = now()->subDays($request->days);

        $deletedCount = Activity::whereIn('log_name', $this->getAdminLogNames())
            ->where('created_at', '<', $cutoffDate)
            ->delete();

        // Log the cleanup operation
        activity('system_management')
            ->causedBy(auth()->user())
            ->withProperties([
                'operation_type' => 'activity_log_cleanup',
                'deleted_count' => $deletedCount,
                'cutoff_date' => $cutoffDate->toDateString(),
                'days_retained' => $request->days
            ])
            ->log("Cleaned up {$deletedCount} activity log records older than {$request->days} days");

        return response()->json([
            'success' => true,
            'message' => "Successfully deleted {$deletedCount} old activity records.",
            'deleted_count' => $deletedCount
        ]);
    }

    /**
     * Get all admin log names for filtering.
     */
    private function getAdminLogNames(): array
    {
        return [
            'stock_management',
            'product_management',
            'category_management',
            'order_management',
            'user_management',
            'blog_management',
            'banner_management',
            'flash_deal_management',
            'inquiry_management',
            'overseas_order_management',
            'system_management',
            'settings_management',
            'permission_management'
        ];
    }

    /**
     * Apply filters to the query.
     */
    private function applyFilters($query, Request $request)
    {
        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id);
        }

        // Filter by module (log_name)
        if ($request->filled('module')) {
            $query->where('log_name', $request->module);
        }

        // Filter by operation type
        if ($request->filled('operation_type')) {
            $query->whereJsonContains('properties->operation_type', $request->operation_type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by batch UUID
        if ($request->filled('batch_uuid')) {
            $query->whereJsonContains('properties->batch_uuid', $request->batch_uuid);
        }

        // Filter by subject type
        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        // Search in description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        // Filter by period (quick filters)
        if ($request->filled('period')) {
            $query = $this->applyPeriodFilter($query, $request->period);
        }
    }

    /**
     * Apply period filter to query.
     */
    private function applyPeriodFilter($query, string $period)
    {
        switch ($period) {
            case 'today':
                return $query->whereDate('created_at', today());
            case 'yesterday':
                return $query->whereDate('created_at', now()->subDay()->toDateString());
            case 'this_week':
                return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            case 'last_week':
                return $query->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
            case 'this_month':
                return $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
            case 'last_month':
                return $query->whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year);
            case 'this_year':
                return $query->whereYear('created_at', now()->year);
            default:
                return $query;
        }
    }

    /**
     * Get filter data for dropdowns.
     */
    private function getFilterData(): array
    {
        // Get users who have performed activities
        $users = User::whereHas('activities', function ($q) {
            $q->whereIn('log_name', $this->getAdminLogNames());
        })->select('id', 'name', 'email')->orderBy('name')->get();

        // Get available modules
        $modules = collect($this->getAdminLogNames())->map(function ($logName) {
            return [
                'value' => $logName,
                'label' => $this->getModuleName($logName)
            ];
        })->sortBy('label')->values();

        // Get operation types
        $operationTypes = Activity::whereIn('log_name', $this->getAdminLogNames())
            ->select(DB::raw("DISTINCT JSON_UNQUOTE(JSON_EXTRACT(properties, '$.operation_type')) as operation_type"))
            ->whereNotNull(DB::raw("JSON_EXTRACT(properties, '$.operation_type')"))
            ->pluck('operation_type')
            ->filter()
            ->sort()
            ->values();

        // Get subject types
        $subjectTypes = Activity::whereIn('log_name', $this->getAdminLogNames())
            ->select('subject_type')
            ->distinct()
            ->whereNotNull('subject_type')
            ->pluck('subject_type')
            ->sort()
            ->values();

        return [
            'users' => $users,
            'modules' => $modules,
            'operation_types' => $operationTypes,
            'subject_types' => $subjectTypes
        ];
    }

    /**
     * Get human-readable module name.
     */
    private function getModuleName(string $logName): string
    {
        $moduleNames = [
            'stock_management' => 'Stock Management',
            'product_management' => 'Product Management',
            'category_management' => 'Category Management',
            'order_management' => 'Order Management',
            'user_management' => 'User Management',
            'blog_management' => 'Blog Management',
            'banner_management' => 'Banner Management',
            'flash_deal_management' => 'Flash Deal Management',
            'inquiry_management' => 'Inquiry Management',
            'overseas_order_management' => 'Overseas Order Management',
            'system_management' => 'System Management',
            'settings_management' => 'Settings Management',
            'permission_management' => 'Permission Management'
        ];

        return $moduleNames[$logName] ?? ucfirst(str_replace('_', ' ', $logName));
    }

    /**
     * Format properties for display.
     */
    private function formatProperties($properties): array
    {
        if (!$properties || $properties->isEmpty()) {
            return [];
        }

        $formatted = [];

        foreach ($properties as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $formatted[$key] = json_encode($value, JSON_PRETTY_PRINT);
            } else {
                $formatted[$key] = $value;
            }
        }

        return $formatted;
    }
}
