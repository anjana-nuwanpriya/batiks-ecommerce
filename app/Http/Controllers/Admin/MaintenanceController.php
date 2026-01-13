<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class MaintenanceController extends Controller
{
    public function index()
    {
        $isDown = $this->isMaintenanceMode();
        $maintenanceFile = storage_path('framework/down');
        $maintenanceData = null;

        if ($isDown && File::exists($maintenanceFile)) {
            $maintenanceData = json_decode(File::get($maintenanceFile), true);
        }

        return view('admin.maintenance.index', compact('isDown', 'maintenanceData'));
    }

    public function enable(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string|max:255',
            'retry' => 'nullable|integer|min:1',
            'allowed_ips' => 'nullable|string',
            'secret' => 'nullable|string|max:50',
        ]);

        $options = [];

        if ($request->message) {
            $options['--message'] = $request->message;
        }

        if ($request->retry) {
            $options['--retry'] = $request->retry;
        }

        if ($request->allowed_ips) {
            $ips = explode(',', $request->allowed_ips);
            $allowedIps = [];
            foreach ($ips as $ip) {
                $allowedIps[] = trim($ip);
            }
            $options['--allow'] = $allowedIps;
        }

        if ($request->secret) {
            $options['--secret'] = $request->secret;
        }

        Artisan::call('down', $options);

        return response()->json([
            'success' => true,
            'message' => 'Maintenance mode enabled successfully!'
        ]);
    }

    public function disable()
    {
        Artisan::call('up');

        return response()->json([
            'success' => true,
            'message' => 'Maintenance mode disabled successfully!'
        ]);
    }

    public function status()
    {
        return response()->json([
            'is_down' => $this->isMaintenanceMode(),
            'data' => $this->getMaintenanceData()
        ]);
    }

    private function isMaintenanceMode()
    {
        return File::exists(storage_path('framework/down'));
    }

    private function getMaintenanceData()
    {
        $maintenanceFile = storage_path('framework/down');

        if (!File::exists($maintenanceFile)) {
            return null;
        }

        return json_decode(File::get($maintenanceFile), true);
    }
}
