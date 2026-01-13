<?php

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

/**
 * Generate status switch
 *
 * @param Model $value
 * @param string $routeName
 * @param string $field
 * @param string $action
 * @return string
 */



if (!function_exists('generateStatusSwitch')) {
    function generateStatusSwitch($value, $routeName = null, $field = 'status', $action = 'publish')
    {
        $checked = $value->{$field} ? 'checked' : '';
        // Generate URL if not provided
        $url = $routeName ? route($routeName, $value) : null;
        return <<<HTML
        <div class="custom-control custom-switch status-switch">
            <input type="checkbox" class="custom-control-input" id="{$field}{$value->id}" data-id="{$value->id}" data-url="{$url}" {$checked}>
            <label class="custom-control-label" for="{$field}{$value->id}"></label>
        </div>
        HTML;
    }
}

/**
 * Store setting
 *
 * @param string $key
 * @param string $value
 * @return void
 */

function store_setting($key, $value)
{
    // Store setting in DB
    DB::table('settings')->updateOrInsert(
        ['key' => $key],
        ['value' => $value ?? null]
    );

    // Clear cache and update it
    Cache::forget("setting_$key");
    Cache::put("setting_$key", $value, 3600);
}

/**
 * Get setting
 *
 * @param string $key
 * @return string
 */
function get_setting($key)
{
    return Cache::remember("setting_$key", 3600, function () use ($key) {
        return DB::table('settings')->where('key', $key)->value('value');
    });
}

/**
 * Get cart total amount
 *
 * @return int
 */
function getCartTotalAmount()
{
    $totalAmount = 0;
    if (session()->has('cart')) {
        foreach (session()->get('cart') as $productId => $item) {
            $product = Product::find($productId);
            $variant = $product->stocks()->where('id', $item['variant'])->first();
            $totalAmount += $product->cartPrice($variant->id, $item['quantity']);
        }
    }
    return $totalAmount;
}



/**
 * Validate and normalize Sri Lankan phone number
 *
 * @param string $input Phone number to validate
 * @return string|false Validated and normalized phone number or false if invalid
 */
function phoneNumberValidation($input)
{
    $cleaned = preg_replace('/\D/', '', $input);

    if (strpos($input, '+94') === 0) {
        $cleaned = substr($cleaned, 1);
    }

    if (preg_match('/^(?:94|0)?(\d{9})$/', $cleaned, $matches)) {
        return $matches[1];
    }

    return false;
}


/**
 * Format number as currency with optional K formatting for thousands
 *
 * @param float|int $amount Amount to format
 * @param string $currency Currency code (default: LKR)
 * @param bool $useKFormat Whether to use K format for thousands (default: false)
 * @return string Formatted currency string
 */
function formatCurrency($amount, $currency = 'LKR', $useKFormat = false)
{
    if ($useKFormat && $amount >= 1000) {
        $amount = round($amount / 1000, 1) . 'K';
    } else {
        $amount = number_format($amount, 2);
    }

    switch (strtoupper($currency)) {
        case 'USD':
            return '$' . $amount;
        case 'EUR':
            return '€' . $amount;
        case 'GBP':
            return '£' . $amount;
        case 'LKR':
        default:
            return 'Rs. ' . $amount;
    }
}


/**
 * Set environment variables
 *
 * @param array $data
 * @return bool
 */
function setEnv(array $data)
{
    $envPath = base_path('.env');

    if (!File::exists($envPath)) {
        return false;
    }

    $env = File::get($envPath);

    foreach ($data as $key => $value) {
        $key = strtoupper($key); // force uppercase for consistency
        $pattern = "/^{$key}=.*/m";

        // Add quotes if value contains spaces
        if (strpos($value, ' ') !== false) {
            $value = '"' . $value . '"';
        }

        if (preg_match($pattern, $env)) {
            $env = preg_replace($pattern, "{$key}={$value}", $env);
        } else {
            $env .= "\n{$key}={$value}";
        }
    }

    File::put($envPath, $env);
    return true;
}
