<?php

namespace Breviam\MpesaSdk\Services;

use Breviam\MpesaSdk\Contracts\AuthInterface;
use Breviam\MpesaSdk\Exceptions\AuthenticationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class MpesaAuthService extends BaseService implements AuthInterface
{
    /**
     * Get a valid access token (from cache or generate new)
     */
    public function getAccessToken(string $api = null): string
    {
        $credentials = $this->getCredentials($api);
        $cacheKey = $this->config['cache']['prefix'] . 'access_token_' . md5($credentials['consumer_key'] . $credentials['consumer_secret']);

        return Cache::remember($cacheKey, $this->config['cache']['ttl'], function () use ($credentials) {
            return $this->generateToken($credentials['consumer_key'], $credentials['consumer_secret']);
        });
    }

    /**
     * Generate a new access token
     */
    public function generateToken(string $consumerKey = null, string $consumerSecret = null): string
    {
        $key = $consumerKey ?? $this->config['consumer_key'];
        $secret = $consumerSecret ?? $this->config['consumer_secret'];

        $credentials = base64_encode($key . ':' . $secret);

        $response = Http::timeout($this->config['timeout'])
            ->withHeaders([
                'Authorization' => 'Basic ' . $credentials,
                'Content-Type' => 'application/json',
            ])
            ->get($this->getAuthUrl());

        if ($response->failed()) {
            throw new AuthenticationException(
                'Failed to generate access token: ' . $response->body(),
                $response->status(),
                null,
                [
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]
            );
        }

        $data = $response->json();

        if (!isset($data['access_token'])) {
            throw new AuthenticationException(
                'Invalid response from authentication endpoint',
                500,
                null,
                ['response' => $data]
            );
        }

        return $data['access_token'];
    }

    /**
     * Clear cached token
     */
    public function clearCache(string $api = null): void
    {
        if ($api) {
            $credentials = $this->getCredentials($api);
            $cacheKey = $this->config['cache']['prefix'] . 'access_token_' . md5($credentials['consumer_key'] . $credentials['consumer_secret']);
            Cache::forget($cacheKey);
        } else {
            // Clear all access token caches by getting all possible cache keys
            $credentials = $this->getCredentials();
            $defaultCacheKey = $this->config['cache']['prefix'] . 'access_token_' . md5($credentials['consumer_key'] . $credentials['consumer_secret']);
            Cache::forget($defaultCacheKey);

            // Also clear cache for all configured APIs
            if (isset($this->config['credentials']) && is_array($this->config['credentials'])) {
                foreach ($this->config['credentials'] as $apiName => $apiCredentials) {
                    $apiCredentials = $this->getCredentials($apiName);
                    $apiCacheKey = $this->config['cache']['prefix'] . 'access_token_' . md5($apiCredentials['consumer_key'] . $apiCredentials['consumer_secret']);
                    Cache::forget($apiCacheKey);
                }
            }
        }
    }
}
