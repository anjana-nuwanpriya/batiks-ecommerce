<?php

namespace App\Http\Controllers;

use App\Models\PhoneVerification;
use App\Utilities\HutchSmsUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PhoneVerificationController extends Controller
{
    /**
     * Send OTP to phone number
     */
    public function sendOTP(Request $request)
    {
        $request->validate([
            'phone' => ['required', function ($attr, $value, $fail) {
                if (!phoneNumberValidation($value)) {
                    $fail('Please enter a valid Sri Lankan phone number.');
                }
            }],
        ]);

        $phone = phoneNumberValidation($request->phone);

        try {
            // Generate OTP
            $verification = PhoneVerification::generateOTP($phone);

            // Here you would integrate with your SMS service
            // For now, we'll log the OTP (remove this in production)
            Log::info("OTP for phone {$phone}: {$verification->otp_code}");

            // In production, send SMS here:
            $this->sendSMS($phone, "Your verification code is: {$verification->otp_code}. Valid for 2 minutes.");

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to your phone number.',
                'expires_in' => 120 // 2 minutes in seconds
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send OTP: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.'
            ], 500);
        }
    }

    /**
     * Verify OTP code
     */
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'phone' => ['required', function ($attr, $value, $fail) {
                if (!phoneNumberValidation($value)) {
                    $fail('Please enter a valid Sri Lankan phone number.');
                }
            }],
            'otp_code' => 'required|string|size:6'
        ]);

        $phone = phoneNumberValidation($request->phone);

        if (PhoneVerification::verifyOTP($phone, $request->otp_code)) {

            if (Auth::check()) {
                $user = Auth::user();
                $user->update(['phone' => $phone, 'phone_verified_at' => now()]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Phone number verified successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid or expired OTP code.'
        ], 400);
    }

    /**
     * Check if phone is verified
     */
    public function checkVerification(Request $request)
    {
        $request->validate([
            'phone' => ['required', function ($attr, $value, $fail) {
                if (!phoneNumberValidation($value)) {
                    $fail('Please enter a valid Sri Lankan phone number.');
                }
            }],
        ]);

        $phone = phoneNumberValidation($request->phone);
        $isVerified = PhoneVerification::isPhoneVerified($phone);

        return response()->json([
            'verified' => $isVerified
        ]);
    }

    /**
     * Send SMS (integrate with your SMS service)
     */
    private function sendSMS($phone, $message)
    {
        HutchSmsUtility::sendSms($phone, $message);
        return true;
    }
}
