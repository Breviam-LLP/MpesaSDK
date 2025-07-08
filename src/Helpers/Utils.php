<?php

namespace Breviam\MpesaSdk\Helpers;

class Utils
{
    /**
     * Generate M-Pesa timestamp
     */
    public static function generateTimestamp(): string
    {
        return date('YmdHis');
    }

    /**
     * Generate M-Pesa password for STK Push
     */
    public static function generatePassword(string $shortcode, string $passkey, ?string $timestamp = null): string
    {
        $timestamp = $timestamp ?: self::generateTimestamp();
        return base64_encode($shortcode . $passkey . $timestamp);
    }

    /**
     * Format phone number to required format
     */
    public static function formatPhoneNumber(string $phone): string
    {
        // Remove any non-digit characters
        $phone = preg_replace('/\D/', '', $phone);

        // Handle different formats
        if (str_starts_with($phone, '0')) {
            return '254' . substr($phone, 1);
        }

        if (str_starts_with($phone, '+254')) {
            return substr($phone, 1);
        }

        if (str_starts_with($phone, '254')) {
            return $phone;
        }

        // Assume Kenyan number if no country code
        if (strlen($phone) === 9) {
            return '254' . $phone;
        }

        return $phone;
    }

    /**
     * Validate phone number
     */
    public static function isValidPhoneNumber(string $phone): bool
    {
        $formatted = self::formatPhoneNumber($phone);
        return preg_match('/^254[71][0-9]{8}$/', $formatted);
    }

    /**
     * Generate transaction reference
     */
    public static function generateReference(string $prefix = 'TXN'): string
    {
        return $prefix . time() . rand(1000, 9999);
    }

    /**
     * Mask sensitive data for logging
     */
    public static function maskSensitiveData(array $data): array
    {
        $masked = $data;
        $sensitiveKeys = ['Password', 'SecurityCredential', 'CommandID'];

        foreach ($sensitiveKeys as $key) {
            if (isset($masked[$key])) {
                $masked[$key] = '***MASKED***';
            }
        }

        return $masked;
    }
}
