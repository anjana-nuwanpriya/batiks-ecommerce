<?php

// Temporary stub functions to prevent errors while cleaning up courier services

if (!function_exists('isCourierApiAvailable')) {
    function isCourierApiAvailable($courier)
    {
        return false; // Always return false since courier services are disabled
    }
}

if (!function_exists('getAvailableCourierServices')) {
    function getAvailableCourierServices()
    {
        return []; // Return empty array since courier services are disabled
    }
}
