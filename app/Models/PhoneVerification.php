<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PhoneVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'otp_code',
        'expires_at',
        'is_verified'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_verified' => 'boolean'
    ];

    /**
     * Generate and store OTP for phone number
     */
    public static function generateOTP($phone)
    {
        // Delete any existing OTP for this phone
        self::where('phone', $phone)->delete();

        // Generate 6-digit OTP
        $otpCode = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Create new OTP record with 2-minute expiry
        return self::create([
            'phone' => $phone,
            'otp_code' => $otpCode,
            'expires_at' => Carbon::now()->addMinutes(2),
            'is_verified' => false
        ]);
    }

    /**
     * Verify OTP code
     */
    public static function verifyOTP($phone, $otpCode)
    {
        $verification = self::where('phone', $phone)
            ->where('otp_code', $otpCode)
            ->where('expires_at', '>', Carbon::now())
            ->where('is_verified', false)
            ->first();

        if ($verification) {
            $verification->update(['is_verified' => true]);
            return true;
        }

        return false;
    }

    /**
     * Check if phone is verified
     */
    public static function isPhoneVerified($phone)
    {
        return self::where('phone', $phone)
            ->where('is_verified', true)
            ->where('expires_at', '>', Carbon::now()->subMinutes(30)) // Valid for 30 minutes after verification
            ->exists();
    }

    /**
     * Clean up expired OTPs
     */
    public static function cleanupExpired()
    {
        return self::where('expires_at', '<', Carbon::now())->delete();
    }
}
