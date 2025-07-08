# M-Pesa SDK Configuration Guide

This guide explains how to configure the M-Pesa SDK for Laravel using the new profile-based configuration system.

## Table of Contents

- [Overview](#overview)
- [Environment Variables](#environment-variables)
- [Profile System](#profile-system)
- [Service Configuration](#service-configuration)
- [Callback URLs](#callback-urls)
- [Advanced Configuration](#advanced-configuration)
- [Migration Guide](#migration-guide)

## Overview

The M-Pesa SDK uses a profile-based configuration system that reduces redundancy and provides flexibility for different service configurations. Instead of defining credentials separately for each service, you define reusable profiles that services can inherit from.

## Environment Variables

### Required Variables

Copy `.env.example` to `.env` and configure the following:

```bash
# Environment (sandbox or production)
MPESA_ENV=sandbox

# Core M-Pesa Credentials
MPESA_CONSUMER_KEY=your_consumer_key
MPESA_CONSUMER_SECRET=your_consumer_secret
MPESA_SHORTCODE=123456

# Lipa na M-Pesa Online (STK Push)
MPESA_PASSKEY=your_lipa_na_mpesa_passkey

# Business Operations (B2C, B2B, Balance, Reversal)
MPESA_INITIATOR_NAME=your_initiator_name
MPESA_SECURITY_CREDENTIAL=your_security_credential

# Callback URLs
MPESA_CALLBACK_URL=https://yourdomain.com/mpesa/callbacks
```

### Optional Variables

```bash
# STK Push Configuration
MPESA_STK_PUSH_TYPE=CustomerPayBillOnline  # or CustomerBuyGoodsOnline

# Service-specific Callback URLs (optional)
MPESA_STK_CALLBACK_URL=https://yourdomain.com/mpesa/stk/callback
MPESA_C2B_CALLBACK_URL=https://yourdomain.com/mpesa/c2b/callback
MPESA_B2C_CALLBACK_URL=https://yourdomain.com/mpesa/b2c/callback
MPESA_B2B_CALLBACK_URL=https://yourdomain.com/mpesa/b2b/callback
MPESA_BALANCE_CALLBACK_URL=https://yourdomain.com/mpesa/balance/callback
MPESA_REVERSAL_CALLBACK_URL=https://yourdomain.com/mpesa/reversal/callback
MPESA_TRANSACTION_STATUS_CALLBACK_URL=https://yourdomain.com/mpesa/transaction/callback

# Withdrawal Service (if using separate credentials)
MPESA_W_CONSUMER_KEY=your_withdrawal_consumer_key
MPESA_W_CONSUMER_SECRET=your_withdrawal_consumer_secret
MPESA_W_SHORTCODE=your_withdrawal_shortcode
MPESA_INITIATOR_W_NAME=your_withdrawal_initiator
MPESA_INITIATOR_W_PASS=your_withdrawal_security_credential
```

## Profile System

### How Profiles Work

Profiles define credential templates that can be inherited and extended:

```php
'profiles' => [
    'default' => [
        'consumer_key' => env('MPESA_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
        'shortcode' => env('MPESA_SHORTCODE'),
    ],
    'lipa_na_mpesa' => [
        'extends' => 'default',  // Inherits from default
        'passkey' => env('MPESA_PASSKEY'),
    ],
    'business_operations' => [
        'extends' => 'default',  // Inherits from default
        'initiator' => env('MPESA_INITIATOR_NAME'),
        'security_credential' => env('MPESA_SECURITY_CREDENTIAL'),
    ],
    'withdrawal' => [
        // Standalone profile (no inheritance)
        'consumer_key' => env('MPESA_W_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_W_CONSUMER_SECRET'),
        'shortcode' => env('MPESA_W_SHORTCODE'),
        'initiator' => env('MPESA_INITIATOR_W_NAME'),
        'security_credential' => env('MPESA_INITIATOR_W_PASS'),
    ],
]
```

### Profile Inheritance

When a profile uses `extends`, it inherits all credentials from the parent profile and can override or add new ones:

```php
// Parent profile
'default' => [
    'consumer_key' => 'key1',
    'consumer_secret' => 'secret1',
    'shortcode' => '123456',
],

// Child profile inherits and extends
'lipa_na_mpesa' => [
    'extends' => 'default',
    'passkey' => 'passkey123',  // Added
],

// Resolved profile becomes:
// [
//     'consumer_key' => 'key1',      // From parent
//     'consumer_secret' => 'secret1', // From parent
//     'shortcode' => '123456',       // From parent
//     'passkey' => 'passkey123',     // From child
// ]
```

## Service Configuration

### Service-to-Profile Mapping

Services are mapped to profiles with optional service-specific configuration:

```php
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
]
```

### Service Configuration Options

#### STK Push Service

```php
'stk' => [
    'profile' => 'lipa_na_mpesa',
    'config' => [
        'type' => 'CustomerPayBillOnline',  // or CustomerBuyGoodsOnline
    ],
],
```

The `type` determines the STK Push transaction type:
- `CustomerPayBillOnline` - For paybill transactions
- `CustomerBuyGoodsOnline` - For buy goods transactions

## Callback URLs

### Base URL Configuration

Set a base callback URL that all services can use:

```php
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
]
```

### Callback URL Resolution

The SDK resolves callback URLs in this order:

1. **Service-specific URL** - If `MPESA_STK_CALLBACK_URL` is set
2. **Base URL + service path** - `{MPESA_CALLBACK_URL}/stk`
3. **Base URL** - `MPESA_CALLBACK_URL` (fallback)

Example:
```bash
MPESA_CALLBACK_URL=https://example.com/mpesa/callbacks
# STK callback becomes: https://example.com/mpesa/callbacks/stk

MPESA_STK_CALLBACK_URL=https://example.com/custom/stk/callback
# STK callback becomes: https://example.com/custom/stk/callback (overrides base)
```

## Advanced Configuration

### Multiple Environments

Configure different credentials for different environments:

```php
// config/mpesa.php
'profiles' => [
    'default' => [
        'consumer_key' => env('MPESA_ENV') === 'production' 
            ? env('MPESA_PROD_CONSUMER_KEY')
            : env('MPESA_SANDBOX_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_ENV') === 'production'
            ? env('MPESA_PROD_CONSUMER_SECRET')
            : env('MPESA_SANDBOX_CONSUMER_SECRET'),
        'shortcode' => env('MPESA_ENV') === 'production'
            ? env('MPESA_PROD_SHORTCODE')
            : env('MPESA_SANDBOX_SHORTCODE'),
    ],
    // ... other profiles
]
```

### Custom Profiles

You can create custom profiles for specific use cases:

```php
'profiles' => [
    // ... existing profiles
    
    'high_volume' => [
        'extends' => 'business_operations',
        'timeout' => 60,  // Custom timeout
        'retry_attempts' => 3,  // Custom retry logic
    ],
    
    'partner_integration' => [
        'consumer_key' => env('PARTNER_CONSUMER_KEY'),
        'consumer_secret' => env('PARTNER_CONSUMER_SECRET'),
        'shortcode' => env('PARTNER_SHORTCODE'),
    ],
]
```

### Runtime Configuration

You can override configuration at runtime:

```php
use Breviam\MpesaSdk\Facades\Mpesa;

// Override profile for specific request
$result = Mpesa::stk()->push([
    'amount' => 100,
    'phone' => '254700000000',
    'reference' => 'REF123',
    'description' => 'Payment',
], 'custom_profile');

// Override specific credentials
$result = Mpesa::stk()->setCredentials([
    'consumer_key' => 'custom_key',
    'consumer_secret' => 'custom_secret',
    'shortcode' => 'custom_shortcode',
    'passkey' => 'custom_passkey',
])->push([
    'amount' => 100,
    'phone' => '254700000000',
    'reference' => 'REF123',
    'description' => 'Payment',
]);
```

## Migration Guide

### From v1.0 to v1.1

The profile-based system is backward compatible, but you can migrate for better organization:

#### Old Configuration (v1.0)
```php
'credentials' => [
    'stk' => [
        'consumer_key' => env('MPESA_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
        'shortcode' => env('MPESA_SHORTCODE'),
        'passkey' => env('MPESA_PASSKEY'),
    ],
    'c2b' => [
        'consumer_key' => env('MPESA_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
        'shortcode' => env('MPESA_SHORTCODE'),
    ],
    // ... more repeated credentials
]
```

#### New Configuration (v1.1+)
```php
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
],
'services' => [
    'stk' => ['profile' => 'lipa_na_mpesa'],
    'c2b' => ['profile' => 'default'],
    // ... clean service mappings
]
```

### Migration Benefits

1. **67% Less Configuration** - Eliminate duplicate credentials
2. **Profile Inheritance** - Reuse and extend credential sets
3. **Better Organization** - Clear separation of profiles and services
4. **Easier Maintenance** - Update credentials in one place
5. **Enhanced Flexibility** - Service-specific overrides when needed

## Validation

### Configuration Status Command

Check your configuration status:

```bash
php artisan mpesa:config-status
```

This command validates:
- Required environment variables
- Profile inheritance chains
- Service mappings
- Credential completeness

### Manual Validation

```php
use Breviam\MpesaSdk\Facades\Mpesa;

// Test authentication for each service
$services = ['stk', 'c2b', 'b2c', 'b2b', 'balance', 'reversal'];

foreach ($services as $service) {
    try {
        $auth = Mpesa::auth($service);
        $token = $auth->getToken();
        echo "✅ {$service}: Authentication successful\n";
    } catch (Exception $e) {
        echo "❌ {$service}: {$e->getMessage()}\n";
    }
}
```

## Troubleshooting

### Common Issues

#### Missing Profile Error
```
Profile 'profile_name' not found in configuration
```
**Solution**: Ensure the profile exists in `config/mpesa.php` profiles array.

#### Circular Inheritance
```
Circular inheritance detected in profile chain
```
**Solution**: Check that profiles don't create inheritance loops (A extends B, B extends A).

#### Missing Credentials
```
Required credential 'passkey' missing for service 'stk'
```
**Solution**: Ensure the profile has all required credentials for the service.

#### Authentication Failed
```
Authentication failed: Invalid consumer key/secret
```
**Solution**: Verify credentials are correct and environment variables are set.

### Debug Mode

Enable debug mode for detailed configuration information:

```bash
MPESA_DEBUG=true
```

This will log:
- Profile resolution chains
- Final resolved credentials (masked)
- API request/response details
- Configuration validation results

## Support

For configuration help:

1. Check the [examples](examples/) directory
2. Review the [test files](tests/) for usage patterns
3. Create an issue on GitHub with your configuration (remove sensitive data)
4. Consult the [API documentation](README.md)

---

*Last updated: July 8, 2025*
