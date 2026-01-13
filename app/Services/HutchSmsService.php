<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class HutchSmsService
{
    private static function authenticate()
    {
        $username = env('HUTCH_SMS_USERNAME');
        $password = env('HUTCH_SMS_PASSWORD');
        $accessToken = env('HUTCH_SMS_ACCESS_TOKEN');
        $refreshToken = env('HUTCH_SMS_REFRESH_TOKEN');

        if ($username && $password) {
            return $accessToken
                ? self::tokenRenew($refreshToken)
                : self::login($username, $password);
        }

        return false;
    }

    private static function login($username, $password)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => '*/*',
            'X-API-VERSION' => 'v1',
        ])->post('https://bsms.hutch.lk/api/login', [
            'username' => $username,
            'password' => $password,
        ]);

        if (env('HUTCH_DEBUG')) {
            Log::debug('Hutch SMS Login Response', ['response' => $response->json()]);
        }

        if ($response->successful()) {
            $data = $response->json()['data'];

            setEnv([
                'HUTCH_SMS_ACCESS_TOKEN' => $data['accessToken'],
                'HUTCH_SMS_REFRESH_TOKEN' => $data['refreshToken'],
            ]);

            return $data['accessToken'];
        }

        return false;
    }

    private static function tokenRenew($refreshToken)
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$refreshToken}",
            'Content-Type' => 'application/json',
            'Accept' => '*/*',
            'X-API-VERSION' => 'v1',
        ])->get('https://bsms.hutch.lk/api/token/accessToken');

        if (env('HUTCH_DEBUG')) {
            Log::debug('Hutch SMS Token Renew Response', ['response' => $response->json()]);
        }

        if ($response->successful()) {
            $data = $response->json()['data'];

            setEnv([
                'HUTCH_SMS_ACCESS_TOKEN' => $data['accessToken']
            ]);

            return $data['accessToken'];
        }

        return self::login(env('HUTCH_SMS_USERNAME'), env('HUTCH_SMS_PASSWORD'));
    }

    public static function sendSms($numbers, $content = '', $accessToken = null)
    {
        if (!env('SMS_SERVICE')) {
            return true;
        }

        $accessToken = $accessToken ?: env('HUTCH_SMS_ACCESS_TOKEN') ?? self::authenticate();

        if (!$accessToken) {
            return false;
        }

        $numbers = self::validateNumber($numbers);

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}",
            'Content-Type' => 'application/json',
            'Accept' => '*/*',
            'X-API-VERSION' => 'v1',
        ])->post('https://bsms.hutch.lk/api/sendsms', [
            'campaignName' => env('HUTCH_CAMPAIGN_NAME'),
            'mask' => env('HUTCH_MASK'),
            'numbers' => $numbers,
            'content' => $content,
        ]);

        if ($response->successful()) {
            return $response->json()['data']['serverRef'];
        } elseif ($response->status() === 401) {
            $newToken = self::authenticate();
            return $newToken ? self::sendSms($numbers, $content, $newToken) : false;
        }

        return false;
    }

    private static function validateNumber($numbers)
    {
        if (is_string($numbers)) {
            $numbers = explode(',', $numbers);
        }

        return array_map(function ($number) {
            return preg_replace('/[^0-9]/', '', trim($number));
        }, $numbers);
    }
}
