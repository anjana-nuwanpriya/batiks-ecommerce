<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Weight;
use App\Services\SmsService;
use App\Services\NotificationService;
use App\Utilities\HutchSmsUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SettingController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Shipping Setting
    |--------------------------------------------------------------------------
    */
    public function shippingSetting()
    {
        $shipping_type = get_setting('shipping_type');
        $shipping_additional_cost = get_setting('shipping_additional_cost');
        $shipping_weights = Weight::all();
        return view('admin.site_settings.shipping_setting', compact('shipping_type', 'shipping_additional_cost', 'shipping_weights'));
    }


    /*
    |--------------------------------------------------------------------------
    | Payment Setting
    |--------------------------------------------------------------------------
    */
    public function paymentSetting()
    {
        return view('admin.site_settings.payment_setting');
    }

    /*
    |--------------------------------------------------------------------------
    | Site Setting
    |--------------------------------------------------------------------------
    */
    public function siteSetting()
    {
        $general_settings = [
            'email' => get_setting('email'),
            'phone' => get_setting('phone'),
            'whatsapp' => get_setting('whatsapp'),
            'address' => get_setting('address'),
            'map_link' => get_setting('map_link'),
            'map_preview' => get_setting('map_preview'),
            'facebook' => get_setting('facebook'),
            'linkedin' => get_setting('linkedin'),
            'instagram' => get_setting('instagram'),
            'tiktok' => get_setting('tiktok'),
            'secondary_phone' => get_setting('secondary_phone'),
            'other_address_one' => get_setting('other_address_one'),
            'other_address_two' => get_setting('other_address_two'),
            'admin_notification_phones' => get_setting('admin_notification_phones'),
        ];
        return view('admin.site_settings.site_setting', compact('general_settings'));
    }


    /*
    |--------------------------------------------------------------------------
    | Shipping Setting Update
    |--------------------------------------------------------------------------
    */
    public function shippingSettingUpdate(Request $request)
    {

        try {
            foreach ($request->except('_token') as $key => $value) {
                store_setting($key, $value);
            }

            return response()->json([
                'success' => true,
                'message' => 'Shipping settings updated successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating shipping settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating shipping settings'
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Shipping Weight Update
    |--------------------------------------------------------------------------
    */
    public function shippingWeightUpdate(Request $request)
    {
        try {
            $datalist = [];
            if ($request->has('weight') && is_array($request->weight)) {
                foreach ($request->weight as $key => $value) {
                    $datalist[] = [
                        'weight' => $value,
                        'price' => ($request->price[$key]) ? $request->price[$key] : 0
                    ];
                }
            }

            Weight::truncate();
            Weight::insert($datalist);

            // Additional Cost
            if ($request->has('additional_cost') && !empty($request->additional_cost)) {
                store_setting('shipping_additional_cost', $request->additional_cost);
            }

            return response()->json([
                'success' => true,
                'message' => 'Shipping weights updated successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating shipping weights: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating shipping weights'
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Save Settings
    |--------------------------------------------------------------------------
    */
    public function saveSettings(Request $request)
    {
        $data = $request->except('_token'); // Exclude CSRF token

        try {
            setEnv($data);
            toast()->success('Settings saved successfully');
            return back();
        } catch (\Exception $e) {
            Log::error('Error saving settings: ' . $e->getMessage());
            toast()->error('Error saving settings');
            return back();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Save General Settings
    |--------------------------------------------------------------------------
    */
    public function saveGeneralSettings(Request $request)
    {
        try {
            foreach ($request->except('_token') as $key => $value) {
                if (empty($value)) {
                    continue;
                }
                store_setting($key, $value);
            }

            return response()->json([
                'success' => true,
                'message' => 'General settings updated successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving general settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                // 'message' => 'Error saving general settings'
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Save Social Settings
    |--------------------------------------------------------------------------
    */
    public function saveSocialSettings(Request $request)
    {
        try {
            foreach ($request->except('_token') as $key => $value) {
                store_setting($key, $value);
            }

            return response()->json([
                'success' => true,
                'message' => 'Social media settings updated successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving social settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Save Site Settings
    |--------------------------------------------------------------------------
    */
    public function siteSettingsUpdate(Request $request)
    {
        try {
            foreach ($request->except('_token') as $key => $value) {
                if (empty($value)) {
                    continue;
                }
                store_setting($key, $value);
            }



            return response()->json([
                'success' => true,
                'message' => 'Updated successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving site settings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Test Email
    |--------------------------------------------------------------------------
    */
    public function testEmail(Request $request)
    {
        try {
            Mail::raw('This is a test email', function ($message) use ($request) {
                $message->to($request->test_email)
                    ->subject('Test Email')
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending test email: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Test SMS
    |--------------------------------------------------------------------------
    */
    public function testSms(Request $request)
    {
        $request->validate([
            'test_phone' => 'required|string',
            'test_message' => 'nullable|string|max:160'
        ]);

        try {
            $phoneNumber = $request->test_phone;
            $message = $request->test_message ?: "Test SMS from " . config('app.name') . " - " . now()->format('Y-m-d H:i:s');

            $smsService = new SmsService();
            $result = $smsService->sendCustomSms($phoneNumber, $message);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test SMS sent successfully to ' . $phoneNumber . '!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send test SMS. Please check your SMS configuration.'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Error sending test SMS: ' . $e->getMessage(), [
                'phone' => $request->test_phone,
                'message' => $request->test_message
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error sending test SMS: ' . $e->getMessage()
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SMS Service Status Page
    |--------------------------------------------------------------------------
    */
    public function smsStatusPage()
    {
        return view('admin.site_settings.sms_status');
    }


    /*
    |--------------------------------------------------------------------------
    | SMS Service Status API
    |--------------------------------------------------------------------------
    */
    public function smsStatus()
    {
        try {
            $notificationService = new NotificationService();
            $serviceStatus = $notificationService->getServiceStatus();

            // Get token status from cache
            $accessToken = Cache::get('hutch_access_token');
            $refreshToken = Cache::get('hutch_refresh_token');

            $tokenStatus = [
                'access_token' => $accessToken ? 'Valid' : 'Not Found',
                'refresh_token' => $refreshToken ? 'Valid' : 'Not Found',
                'access_token_expires' => $accessToken ? 'Available' : 'N/A',
                'refresh_token_expires' => $refreshToken ? 'Available' : 'N/A'
            ];

            // Check if all required environment variables are set
            $requiredEnvVars = ['HUTCH_SMS_USERNAME', 'HUTCH_SMS_PASSWORD', 'HUTCH_CAMPAIGN_NAME', 'HUTCH_MASK'];
            $missingEnvVars = [];

            foreach ($requiredEnvVars as $var) {
                if (!env($var)) {
                    $missingEnvVars[] = $var;
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'service_status' => $serviceStatus['sms'] ?? ['enabled' => false, 'error' => 'Service status unavailable'],
                    'token_status' => $tokenStatus,
                    'configuration_status' => [
                        'all_required_vars_set' => empty($missingEnvVars),
                        'missing_variables' => $missingEnvVars
                    ],
                    'environment_variables' => [
                        'SMS_SERVICE' => env('SMS_SERVICE') ? 'Enabled' : 'Disabled',
                        'HUTCH_SMS_USERNAME' => env('HUTCH_SMS_USERNAME') ? 'Set' : 'Not Set',
                        'HUTCH_SMS_PASSWORD' => env('HUTCH_SMS_PASSWORD') ? 'Set' : 'Not Set',
                        'HUTCH_CAMPAIGN_NAME' => env('HUTCH_CAMPAIGN_NAME') ? 'Set' : 'Not Set',
                        'HUTCH_MASK' => env('HUTCH_MASK') ? 'Set' : 'Not Set',
                        'HUTCH_DEBUG' => env('HUTCH_DEBUG') ? 'Enabled' : 'Disabled',
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting SMS status: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error getting SMS status: ' . $e->getMessage(),
                'data' => [
                    'service_status' => ['enabled' => false, 'error' => 'Service check failed'],
                    'token_status' => ['error' => 'Unable to check token status'],
                    'environment_variables' => [
                        'SMS_SERVICE' => env('SMS_SERVICE') ? 'Enabled' : 'Disabled',
                        'HUTCH_SMS_USERNAME' => env('HUTCH_SMS_USERNAME') ? 'Set' : 'Not Set',
                        'HUTCH_SMS_PASSWORD' => env('HUTCH_SMS_PASSWORD') ? 'Set' : 'Not Set',
                        'HUTCH_CAMPAIGN_NAME' => env('HUTCH_CAMPAIGN_NAME') ? 'Set' : 'Not Set',
                        'HUTCH_MASK' => env('HUTCH_MASK') ? 'Set' : 'Not Set',
                        'HUTCH_DEBUG' => env('HUTCH_DEBUG') ? 'Enabled' : 'Disabled',
                    ]
                ]
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Clear SMS Tokens
    |--------------------------------------------------------------------------
    */
    public function clearSmsTokens()
    {
        try {
            Cache::forget('hutch_access_token');
            Cache::forget('hutch_refresh_token');

            return response()->json([
                'success' => true,
                'message' => 'SMS tokens cleared successfully! Next SMS will require fresh authentication.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error clearing SMS tokens: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error clearing SMS tokens: ' . $e->getMessage()
            ], 500);
        }
    }
}
