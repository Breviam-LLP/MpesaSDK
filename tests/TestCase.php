<?php

namespace Breviam\MpesaSdk\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Breviam\MpesaSdk\MpesaSdkServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('mpesa', [
            'env' => 'sandbox',
            'consumer_key' => 'test_consumer_key',
            'consumer_secret' => 'test_consumer_secret',
            'shortcode' => '174379',
            'passkey' => 'test_passkey',
            'initiator' => 'test_initiator',
            'security_credential' => 'test_security_credential',
            'callback_url' => 'https://example.com/callbacks',
            'credentials' => [
                'stk' => [
                    'consumer_key' => 'test_consumer_key',
                    'consumer_secret' => 'test_consumer_secret',
                    'shortcode' => '174379',
                    'passkey' => 'test_passkey',
                ],
                'c2b' => [
                    'consumer_key' => 'test_consumer_key',
                    'consumer_secret' => 'test_consumer_secret',
                    'shortcode' => '174379',
                ],
                'b2c' => [
                    'consumer_key' => 'test_consumer_key',
                    'consumer_secret' => 'test_consumer_secret',
                    'shortcode' => '174379',
                    'initiator' => 'test_initiator',
                    'security_credential' => 'test_security_credential',
                ],
                'b2b' => [
                    'consumer_key' => 'test_consumer_key',
                    'consumer_secret' => 'test_consumer_secret',
                    'shortcode' => '174379',
                    'initiator' => 'test_initiator',
                    'security_credential' => 'test_security_credential',
                ],
                'withdrawal' => [
                    'consumer_key' => 'test_w_consumer_key',
                    'consumer_secret' => 'test_w_consumer_secret',
                    'shortcode' => '3029755',
                    'initiator' => 'test_w_initiator',
                    'security_credential' => 'test_w_security_credential',
                ],
                'balance' => [
                    'consumer_key' => 'test_consumer_key',
                    'consumer_secret' => 'test_consumer_secret',
                    'shortcode' => '174379',
                    'initiator' => 'test_initiator',
                    'security_credential' => 'test_security_credential',
                ],
                'reversal' => [
                    'consumer_key' => 'test_consumer_key',
                    'consumer_secret' => 'test_consumer_secret',
                    'shortcode' => '174379',
                    'initiator' => 'test_initiator',
                    'security_credential' => 'test_security_credential',
                ],
            ],
            'certificate' => 'ProductionCertificate',
            'stk' => [
                'type' => 'CustomerPayBillOnline',
            ],
            'cache' => [
                'prefix' => 'mpesa_test_',
                'ttl' => 3300,
            ],
            'endpoints' => [
                'sandbox' => [
                    'auth' => 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials',
                    'base' => 'https://sandbox.safaricom.co.ke/',
                ],
                'production' => [
                    'auth' => 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials',
                    'base' => 'https://api.safaricom.co.ke/',
                ],
            ],
            'timeout' => 30,
            'logging' => [
                'enabled' => true,
                'channel' => 'single',
            ],
        ]);
    }

    protected function getPackageProviders($app): array
    {
        return [
            MpesaSdkServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Mpesa' => \Breviam\MpesaSdk\Facades\Mpesa::class,
        ];
    }
}
