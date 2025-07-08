<?php

return [
    /*
    |--------------------------------------------------------------------------
    | M-Pesa Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the M-Pesa environment your application is running in.
    | Accepted values are: 'sandbox' or 'production'
    |
    */
    'env' => env('MPESA_ENV', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Default M-Pesa API Credentials
    |--------------------------------------------------------------------------
    |
    | Default credentials for general M-Pesa operations
    |
    */
    'consumer_key' => env('MPESA_CONSUMER_KEY'),
    'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
    'shortcode' => env('MPESA_SHORTCODE'),
    'passkey' => env('MPESA_PASSKEY'),
    'initiator' => env('MPESA_INITIATOR_NAME'),
    'security_credential' => env('MPESA_SECURITY_CREDENTIAL'),

    /*
    |--------------------------------------------------------------------------
    | Credential Profiles
    |--------------------------------------------------------------------------
    |
    | Define credential templates that can be reused across services
    |
    */
    'profiles' => [
        'default' => [
            'consumer_key' => env('MPESA_CONSUMER_KEY'),
            'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
            'shortcode' => env('MPESA_SHORTCODE'),
        ],
        'lipa_na_mpesa' => [
            'extends' => 'default',
            'passkey' => env('MPESA_PASSKEY'),
        ],
        'business_operations' => [
            'extends' => 'default',
            'initiator' => env('MPESA_INITIATOR_NAME'),
            'security_credential' => env('MPESA_SECURITY_CREDENTIAL'),
        ],
        'withdrawal' => [
            'consumer_key' => env('MPESA_W_CONSUMER_KEY'),
            'consumer_secret' => env('MPESA_W_CONSUMER_SECRET'),
            'shortcode' => env('MPESA_W_SHORTCODE'),
            'initiator' => env('MPESA_INITIATOR_W_NAME'),
            'security_credential' => env('MPESA_INITIATOR_W_PASS'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Service Configuration
    |--------------------------------------------------------------------------
    |
    | Map services to credential profiles with service-specific overrides
    |
    */
    'services' => [
        'stk' => [
            'profile' => 'lipa_na_mpesa',
            'config' => [
                'type' => env('MPESA_STK_PUSH_TYPE', 'CustomerPayBillOnline'),
            ],
        ],
        'c2b' => [
            'profile' => 'default',
        ],
        'b2c' => [
            'profile' => 'business_operations',
        ],
        'b2b' => [
            'profile' => 'business_operations',
        ],
        'balance' => [
            'profile' => 'business_operations',
        ],
        'reversal' => [
            'profile' => 'business_operations',
        ],
        'withdrawal' => [
            'profile' => 'withdrawal',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Callback Configuration  
    |--------------------------------------------------------------------------
    |
    | URLs for M-Pesa to send callbacks. You can use the base_url
    | or specify individual URLs for each service
    |
    */
    'callbacks' => [
        'base_url' => env('MPESA_CALLBACK_URL'),
        'services' => [
            'stk' => env('MPESA_STK_CALLBACK_URL'),
            'c2b' => env('MPESA_C2B_CALLBACK_URL'),
            'b2c' => env('MPESA_B2C_CALLBACK_URL'),
            'b2b' => env('MPESA_B2B_CALLBACK_URL'),
            'balance' => env('MPESA_BALANCE_CALLBACK_URL'),
            'reversal' => env('MPESA_REVERSAL_CALLBACK_URL'),
            'transaction_status' => env('MPESA_TRANSACTION_STATUS_CALLBACK_URL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for caching OAuth tokens
    |
    */
    'cache' => [
        'prefix' => 'mpesa_',
        'ttl' => 3300, // 55 minutes (tokens expire in 1 hour)
    ],

    /*
    |--------------------------------------------------------------------------
    | API Endpoints
    |--------------------------------------------------------------------------
    |
    | M-Pesa API endpoints for different environments
    |
    */
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

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | Timeout for HTTP requests in seconds
    |
    */
    'timeout' => 30,

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Enable/disable request and response logging
    |
    */
    'logging' => [
        'enabled' => env('MPESA_LOGGING', true),
        'channel' => env('MPESA_LOG_CHANNEL', 'daily'),
    ],
];