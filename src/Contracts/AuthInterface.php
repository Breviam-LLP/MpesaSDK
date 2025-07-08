<?php

namespace Breviam\MpesaSdk\Contracts;

interface AuthInterface
{
    /**
     * Get a valid access token
     */
    public function getAccessToken(string $api = null): string;

    /**
     * Generate a new access token
     */
    public function generateToken(string $consumerKey = null, string $consumerSecret = null): string;

    /**
     * Clear cached token
     */
    public function clearCache(string $api = null): void;
}
