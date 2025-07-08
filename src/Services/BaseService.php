<?php

namespace Breviam\MpesaSdk\Services;

use Breviam\MpesaSdk\Exceptions\MpesaException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class BaseService
{
    protected array $config;

    public function __construct()
    {
        $this->config = config('mpesa');
    }

    /**
     * Get the base URL for the current environment
     */
    protected function getBaseUrl(): string
    {
        $env = $this->config['env'];
        return $this->config['endpoints'][$env]['base'];
    }

    /**
     * Get the auth URL for the current environment
     */
    protected function getAuthUrl(): string
    {
        $env = $this->config['env'];
        return $this->config['endpoints'][$env]['auth'];
    }

    /**
     * Get credentials for a specific API using profiles
     */
    protected function getCredentials(string $api = null): array
    {
        $defaultCredentials = [
            'consumer_key' => $this->config['consumer_key'],
            'consumer_secret' => $this->config['consumer_secret'],
            'shortcode' => $this->config['shortcode'],
            'passkey' => $this->config['passkey'],
            'initiator' => $this->config['initiator'],
            'security_credential' => $this->config['security_credential'],
        ];

        if ($api && isset($this->config['services'][$api])) {
            $serviceConfig = $this->config['services'][$api];

            // Get profile credentials
            $profileName = $serviceConfig['profile'] ?? 'default';
            $profileCredentials = $this->resolveProfile($profileName);

            // Merge with service-specific credential overrides
            if (isset($serviceConfig['credentials'])) {
                $profileCredentials = array_merge($profileCredentials, $serviceConfig['credentials']);
            }

            return $profileCredentials;
        }

        // Fallback to old credentials structure for backward compatibility
        if ($api && isset($this->config['credentials'][$api])) {
            return array_merge($defaultCredentials, $this->config['credentials'][$api]);
        }

        return $defaultCredentials;
    }

    /**
     * Resolve a profile with inheritance support
     */
    protected function resolveProfile(string $profileName): array
    {
        if (!isset($this->config['profiles'][$profileName])) {
            throw new MpesaException("Profile '{$profileName}' not found in configuration");
        }

        $profile = $this->config['profiles'][$profileName];

        // Handle profile inheritance
        if (isset($profile['extends'])) {
            $parentProfile = $this->resolveProfile($profile['extends']);
            unset($profile['extends']); // Remove extends key from final result
            return array_merge($parentProfile, $profile);
        }

        return $profile;
    }

    /**
     * Validate configuration for a specific service
     */
    protected function validateConfig(string $service): void
    {
        $credentials = $this->getCredentials($service);

        $required = ['consumer_key', 'consumer_secret', 'shortcode'];

        // Add service-specific required fields
        switch ($service) {
            case 'stk':
                $required[] = 'passkey';
                break;
            case 'c2b':
                break;
            case 'b2c':
            case 'b2b':
            case 'balance':
            case 'reversal':
                $required = array_merge($required, ['initiator', 'security_credential']);
                break;
            case 'withdrawal':
                $required = array_merge($required, ['initiator', 'security_credential']);
                break;
        }

        foreach ($required as $field) {
            if (empty($credentials[$field])) {
                throw new MpesaException("Missing required configuration: {$field} for service: {$service}");
            }
        }

        // Validate callback URL configuration
        $baseUrl = $this->config['callbacks']['base_url'] ?? $this->config['callback_url'] ?? null;
        $serviceUrl = $this->config['callbacks']['services'][$service] ?? null;

        if (!$baseUrl && !$serviceUrl) {
            throw new MpesaException("No callback URL configured for service: {$service}");
        }
    }

    /**
     * Get callback URL for a specific service
     */
    protected function getCallbackUrl(string $service, string $endpoint = ''): string
    {
        // Check for service-specific callback URL first
        $serviceUrl = $this->config['callbacks']['services'][$service] ?? null;

        if ($serviceUrl) {
            return rtrim($serviceUrl, '/') . ($endpoint ? '/' . ltrim($endpoint, '/') : '');
        }

        // Fallback to base callback URL
        $baseUrl = $this->config['callbacks']['base_url'] ?? $this->config['callback_url'] ?? null;
        if (!$baseUrl) {
            throw new MpesaException("No callback URL configured for service: {$service}");
        }

        return rtrim($baseUrl, '/') . ($endpoint ? '/' . ltrim($endpoint, '/') : '');
    }

    /**
     * Get service-specific configuration value
     */
    protected function getServiceConfig(string $service, string $key, $default = null)
    {
        // New structure: services.{service}.config.{key}
        if (isset($this->config['services'][$service]['config'][$key])) {
            return $this->config['services'][$service]['config'][$key];
        }

        // Fallback to old structure for backward compatibility
        if (isset($this->config[$service][$key])) {
            return $this->config[$service][$key];
        }

        return $default;
    }

    /**
     * Make an HTTP request
     */
    protected function makeRequest(string $method, string $endpoint, array $data = [], array $headers = []): array
    {
        $url = $this->getBaseUrl() . ltrim($endpoint, '/');

        $this->logRequest($method, $url, $data);

        $response = Http::timeout($this->config['timeout'])
                    ->withHeaders($headers)
            ->{strtolower($method)}($url, $data);

        $this->logResponse($response);

        if ($response->failed()) {
            throw new MpesaException(
                'M-Pesa API request failed: ' . $response->body(),
                $response->status(),
                null,
                [
                    'url' => $url,
                    'method' => $method,
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]
            );
        }

        return $response->json();
    }

    /**
     * Log request details
     */
    protected function logRequest(string $method, string $url, array $data): void
    {
        if (!$this->config['logging']['enabled']) {
            return;
        }

        Log::channel($this->config['logging']['channel'])->info('M-Pesa API Request', [
            'method' => $method,
            'url' => $url,
            'data' => $this->maskSensitiveData($data),
        ]);
    }

    /**
     * Log response details
     */
    protected function logResponse(Response $response): void
    {
        if (!$this->config['logging']['enabled']) {
            return;
        }

        Log::channel($this->config['logging']['channel'])->info('M-Pesa API Response', [
            'status' => $response->status(),
            'response' => $response->json(),
        ]);
    }

    /**
     * Mask sensitive data for logging
     */
    protected function maskSensitiveData(array $data): array
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
