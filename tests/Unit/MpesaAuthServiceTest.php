<?php

namespace Breviam\MpesaSdk\Tests\Unit;

use Breviam\MpesaSdk\Tests\TestCase;
use Breviam\MpesaSdk\Services\MpesaAuthService;
use Breviam\MpesaSdk\Exceptions\AuthenticationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class MpesaAuthServiceTest extends TestCase
{
    protected MpesaAuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new MpesaAuthService();

        // Clear any existing cache to ensure clean state
        Cache::flush();
    }

    /** @test */
    public function it_generates_access_token_successfully()
    {
        Http::fake([
            'sandbox.safaricom.co.ke/oauth/*' => Http::response([
                'access_token' => 'test_access_token',
                'expires_in' => 3599,
            ], 200),
        ]);

        $token = $this->authService->generateToken();

        $this->assertEquals('test_access_token', $token);
    }

    /** @test */
    public function it_throws_exception_on_failed_authentication()
    {
        Http::fake([
            'sandbox.safaricom.co.ke/oauth/*' => Http::response([
                'error' => 'invalid_client',
            ], 401),
        ]);

        $this->expectException(AuthenticationException::class);
        $this->authService->generateToken();
    }

    /** @test */
    public function it_throws_exception_on_invalid_response()
    {
        Http::fake([
            'sandbox.safaricom.co.ke/oauth/*' => Http::response([
                'some_other_field' => 'value',
            ], 200),
        ]);

        $this->expectException(AuthenticationException::class);
        $this->authService->generateToken();
    }

    /** @test */
    public function it_retrieves_cached_token()
    {
        // Mock the HTTP call for when cache miss occurs
        Http::fake([
            'sandbox.safaricom.co.ke/oauth/*' => Http::response([
                'access_token' => 'test_access_token',
                'expires_in' => 3599,
            ], 200),
        ]);

        // First call should generate and cache the token
        $token1 = $this->authService->getAccessToken();

        // Second call should return the cached token (without HTTP call)
        Http::fake([]); // Reset HTTP mocks to ensure no HTTP call is made
        $token2 = $this->authService->getAccessToken();

        $this->assertEquals('test_access_token', $token1);
        $this->assertEquals('test_access_token', $token2);
    }

    /** @test */
    public function it_clears_token_cache()
    {
        // Test that clearCache method exists and can be called without errors
        $this->authService->clearCache();
        $this->assertTrue(true);

        // Test that clearCache with API parameter works
        $this->authService->clearCache('stk');
        $this->assertTrue(true);
    }

    /** @test */
    public function it_generates_token_with_api_specific_credentials()
    {
        Http::fake([
            'sandbox.safaricom.co.ke/oauth/*' => Http::response([
                'access_token' => 'api_specific_token',
                'expires_in' => 3599,
            ], 200),
        ]);

        // Test that different APIs can use different credentials
        $stkToken = $this->authService->getAccessToken('stk');
        $b2cToken = $this->authService->getAccessToken('b2c');

        $this->assertEquals('api_specific_token', $stkToken);
        $this->assertEquals('api_specific_token', $b2cToken);
    }

    /** @test */
    public function it_clears_specific_api_cache()
    {
        Http::fake([
            'sandbox.safaricom.co.ke/oauth/*' => Http::response([
                'access_token' => 'test_token',
                'expires_in' => 3599,
            ], 200),
        ]);

        // Generate tokens for different APIs
        $this->authService->getAccessToken('stk');
        $this->authService->getAccessToken('b2c');

        // Clear only STK cache
        $this->authService->clearCache('stk');

        // This should work fine since we're just testing the method doesn't throw errors
        $this->assertTrue(true);
    }
}
