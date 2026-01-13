<?php

namespace App\Utilities;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HutchSmsUtility
{
    private static function authenticate()
    {
        $username = env('HUTCH_SMS_USERNAME');
        $password = env('HUTCH_SMS_PASSWORD');

        if (!$username || !$password) {
            Log::warning("HUTCH credentials not set in env.");
            return false;
        }

        $accessToken = Cache::get('hutch_access_token');
        $refreshToken = Cache::get('hutch_refresh_token');



        if (!$accessToken && $refreshToken) {
            return self::tokenRenew($refreshToken);
        } elseif (!$accessToken && !$refreshToken) {
            return self::login($username, $password);
        }

        return $accessToken;
    }

    private static function login($username, $password)
    {
        $url = "https://bsms.hutch.lk/api/login";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => '*/*',
            'X-API-VERSION' => 'v1'
        ])->post($url, [
            'username' => $username,
            'password' => $password
        ]);

        $res = $response->json();

        if ($response->successful()) {
            $accessToken = $res['accessToken'];
            $refreshToken = $res['refreshToken'];

            Cache::put('hutch_access_token', $accessToken, now()->addMinutes(55));
            Cache::put('hutch_refresh_token', $refreshToken, now()->addDays(7));

            return $accessToken;
        }

        Log::error('Hutch login failed', ['response' => $res]);
        return false;
    }

    private static function tokenRenew($refreshToken)
    {
        $url = "https://bsms.hutch.lk/api/token/accessToken";

        $response = Http::withHeaders([
            'Authorization' => "Bearer $refreshToken",
            'Content-Type' => 'application/json',
            'Accept' => '*/*',
            'X-API-VERSION' => 'v1'
        ])->get($url);

        $res = $response->json();

        if ($response->successful()) {
            $accessToken = $res['accessToken'];
            Cache::put('hutch_access_token', $accessToken, now()->addMinutes(55));
            return $accessToken;
        }

        // fallback to login
        return self::login(env('HUTCH_SMS_USERNAME'), env('HUTCH_SMS_PASSWORD'));
    }

    public static function sendSms($number, $content = "", $token = "")
    {
        if (!env('SMS_SERVICE', true)) {
            return true;
        }

        $accessToken = $token ?: Cache::get('hutch_access_token') ?: self::authenticate();

        if (!$accessToken) {
            return false;
        }

        $number = self::validateNumber($number);

        if (!$number) {
            Log::warning("Invalid number provided: $number");
            return false;
        }

        $url = "https://bsms.hutch.lk/api/sendsms";
        $payload = [
            'campaignName' => env('HUTCH_CAMPAIGN_NAME'),
            'mask' => env('HUTCH_MASK'),
            'numbers' => $number,
            'content' => $content
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer $accessToken",
            'Content-Type' => 'application/json',
            'Accept' => '*/*',
            'X-API-VERSION' => 'v1'
        ])->post($url, $payload);

        $res = $response->json();

        if ($response->successful()) {
            return $res['serverRef'] ?? true;
        } elseif ($response->status() === 401) {
            // token expired → retry
            $newToken = self::authenticate();
            return $newToken ? self::sendSms($number, $content, $newToken) : false;
        }

        Log::error("SMS send failed", ['response' => $res]);
        return false;
    }

    private static function validateNumber($msisdn = null)
    {
        if (!$msisdn) return false;

        $msisdn = str_replace("tel:", "", $msisdn);
        $msisdn = ltrim($msisdn, '+0');
        if (self::startsWith($msisdn, "7")) {
            $msisdn = "94" . $msisdn;
        }

        if (strlen($msisdn) != 11 || !self::startsWith($msisdn, "94")) {
            return false;
        }

        return $msisdn;
    }

    private static function startsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && substr($haystack, 0, strlen($needle)) === $needle) {
                return true;
            }
        }

        return false;
    }
}
