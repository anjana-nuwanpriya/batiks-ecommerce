<?php

namespace App\Utilities;

class CurlUtility
{
    public static function http($url, $headers = [], $method = "GET", $payload = [])
    {
        $curl = curl_init();

        // Basic curl options
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        // Add payload for POST requests
        if ($method === "POST" && !empty($payload)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);

        curl_close($curl);

        if ($error) {
            // Return as JSON string for HutchSmsUtility compatibility
            return json_encode([
                'httpcode' => 500,
                'error' => $error,
                'message' => 'cURL Error: ' . $error
            ]);
        }

        // Parse response and add HTTP code
        $decodedResponse = json_decode($response, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedResponse)) {
            $decodedResponse['httpcode'] = $httpCode;
            // Return as JSON string for HutchSmsUtility compatibility
            return json_encode($decodedResponse);
        }

        // If response is not JSON, return as JSON string with HTTP code
        return json_encode([
            'httpcode' => $httpCode,
            'data' => $response,
            'raw_response' => $response
        ]);
    }
}
