# M-Pesa Laravel SDK

[![Latest Version on Packagist](https://img.shields.io/packagist/v/breviam/mpesa-sdk.svg?style=flat-square)](https://packagist.org/packages/breviam/mpesa-sdk)
[![Total Downloads](https://img.shields.io/packagist/dt/breviam/mpesa-sdk.svg?style=flat-square)](https://packagist.org/packages/breviam/mpesa-sdk)
[![Tests](https://img.shields.io/github/workflow/status/breviam/mpesa-sdk/run-tests?label=tests)](https://github.com/Breviam-LLP/MpesaSDK/actions/workflows/ci.yml)

A comprehensive Laravel SDK for integrating with Safaricom's M-Pesa Daraja API. This package provides a clean, well-documented interface for all major M-Pesa services including STK Push, C2B, B2C, Transaction Status, and Account Balance.

## Features

- ✅ **STK Push** (Lipa na M-Pesa Online)
- ✅ **C2B** (Customer to Business) payments
- ✅ **B2C** (Business to Customer) payments
- ✅ **Transaction Status** queries
- ✅ **Account Balance** inquiries
- ✅ **Webhook handling** with automatic route registration
- ✅ **Token management** with automatic caching
- ✅ **Laravel Facade** support
- ✅ **Artisan commands** for testing and debugging
- ✅ **Comprehensive logging**
- ✅ **Event-driven architecture**
- ✅ **Full test coverage**

## Requirements

- PHP 8.1+
- Laravel 11.x (recommended)
- Laravel 10.x (experimental support)

> **Note**: For detailed Laravel version compatibility information, see [LARAVEL_COMPATIBILITY.md](LARAVEL_COMPATIBILITY.md)

## Installation

Install the package via Composer:

```bash
composer require breviam/mpesa-sdk
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag=mpesa-config
```

## Configuration

Add your M-Pesa credentials to your `.env` file:

```env
MPESA_ENV=sandbox
MPESA_CONSUMER_KEY=your_consumer_key
MPESA_CONSUMER_SECRET=your_consumer_secret
MPESA_SHORTCODE=your_shortcode
MPESA_PASSKEY=your_passkey
MPESA_INITIATOR_NAME=your_initiator_name
MPESA_SECURITY_CREDENTIAL=your_security_credential
MPESA_CALLBACK_URL=https://yourdomain.com/mpesa/webhooks

# Optional: Withdrawal-specific credentials (if different)
MPESA_W_CONSUMER_KEY=your_withdrawal_consumer_key
MPESA_W_CONSUMER_SECRET=your_withdrawal_consumer_secret
MPESA_W_SHORTCODE=your_withdrawal_shortcode
MPESA_INITIATOR_W_NAME=your_withdrawal_initiator
MPESA_INITIATOR_W_PASS=your_withdrawal_security_credential

# Optional: Service-specific callback URLs
MPESA_STK_CALLBACK_URL=https://yourdomain.com/mpesa/stk
MPESA_C2B_CALLBACK_URL=https://yourdomain.com/mpesa/c2b
MPESA_B2C_CALLBACK_URL=https://yourdomain.com/mpesa/b2c
```

## Basic Usage

### STK Push (Lipa na M-Pesa Online)

```php
use Breviam\MpesaSdk\Facades\Mpesa;

// Initiate STK Push
$response = Mpesa::stkPush(
    phone: '254712345678',
    amount: 100,
    reference: 'ORDER123',
    description: 'Payment for order #123'
);

// Query STK Push status
$status = Mpesa::stkQuery($response['CheckoutRequestID']);
```

### C2B (Customer to Business)

```php
// Register C2B URLs (usually done once)
$response = Mpesa::c2b()->registerUrls(
    confirmationUrl: 'https://yourdomain.com/mpesa/webhooks/c2b/confirmation',
    validationUrl: 'https://yourdomain.com/mpesa/webhooks/c2b/validation'
);

// Simulate C2B payment (sandbox only)
$response = Mpesa::c2b()->simulate(
    phone: '254712345678',
    amount: 100,
    reference: 'BILL123'
);
```

### B2C (Business to Customer)

```php
$response = Mpesa::sendMoney(
    phone: '254712345678',
    amount: 1000,
    commandId: 'BusinessPayment',
    remarks: 'Salary payment'
);
```

### Account Balance

```php
$response = Mpesa::checkBalance('Balance inquiry');
```

### Transaction Status

```php
$response = Mpesa::checkTransactionStatus(
    transactionId: 'ABC123XYZ',
    partyA: '254712345678',
    remarks: 'Transaction status check'
);
```

## Advanced Usage

### Using Individual Services

```php
use Breviam\MpesaSdk\Contracts\StkInterface;
use Breviam\MpesaSdk\Contracts\AuthInterface;

class PaymentController extends Controller
{
    public function __construct(
        private StkInterface $stkService,
        private AuthInterface $authService
    ) {}

    public function initiatePayment()
    {
        $response = $this->stkService->push(
            '254712345678',
            100,
            'ORDER123',
            'Payment description'
        );
        
        return response()->json($response);
    }
}
```

### Handling Webhooks

The package automatically registers webhook routes. You can listen to events:

```php
use Illuminate\Support\Facades\Event;

// In your EventServiceProvider
Event::listen('mpesa.stk.callback', function ($data) {
    // Handle STK Push callback
    Log::info('STK Push callback received', $data);
});

Event::listen('mpesa.c2b.confirmation', function ($data) {
    // Handle C2B confirmation
    $payment = Payment::where('reference', $data['BillRefNumber'])->first();
    $payment->update(['status' => 'confirmed']);
});
```

### Artisan Commands

Check token status:
```bash
php artisan mpesa:token
```

Clear token cache:
```bash
php artisan mpesa:token --clear
```

Simulate payment (sandbox only):
```bash
php artisan mpesa:simulate-payment 254712345678 100 --reference=TEST123
```

## Testing

Run the tests:

```bash
composer test
```

Run tests with coverage:

```bash
composer test:coverage
```

## Events

The package fires the following events:

| Event | Description |
|-------|-------------|
| `mpesa.stk.callback` | STK Push callback received |
| `mpesa.c2b.validation` | C2B validation request |
| `mpesa.c2b.confirmation` | C2B confirmation received |
| `mpesa.b2c.result` | B2C transaction result |
| `mpesa.b2c.timeout` | B2C transaction timeout |
| `mpesa.balance.result` | Balance inquiry result |
| `mpesa.balance.timeout` | Balance inquiry timeout |
| `mpesa.transaction.result` | Transaction status result |
| `mpesa.transaction.timeout` | Transaction status timeout |

## Configuration Options

The package supports extensive configuration through the `config/mpesa.php` file:

```php
return [
    'env' => 'sandbox', // or 'production'
    'consumer_key' => env('MPESA_CONSUMER_KEY'),
    'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
    'shortcode' => env('MPESA_SHORTCODE'),
    'passkey' => env('MPESA_PASSKEY'),
    'initiator' => env('MPESA_INITIATOR'),
    'security_credential' => env('MPESA_SECURITY_CREDENTIAL'),
    'callback_url' => env('MPESA_CALLBACK_URL'),
    
    'cache' => [
        'prefix' => 'mpesa_',
        'ttl' => 3300, // 55 minutes
    ],
    
    'timeout' => 30,
    
    'logging' => [
        'enabled' => true,
        'channel' => 'daily',
    ],
];
```

## Security

- All sensitive data is masked in logs
- OAuth tokens are securely cached
- HTTPS is enforced for all API calls
- Webhook requests are logged with IP and user agent

## Error Handling

The package throws specific exceptions:

```php
use Breviam\MpesaSdk\Exceptions\MpesaException;
use Breviam\MpesaSdk\Exceptions\AuthenticationException;

try {
    $response = Mpesa::stkPush('254712345678', 100, 'REF123', 'Description');
} catch (AuthenticationException $e) {
    // Handle authentication errors
    Log::error('M-Pesa authentication failed: ' . $e->getMessage());
} catch (MpesaException $e) {
    // Handle general M-Pesa API errors
    Log::error('M-Pesa API error: ' . $e->getMessage());
    $context = $e->getContext(); // Get additional context
}
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email vikgachewa@hotmail.com instead of using the issue tracker.

## Credits

- [Victor Kariuki](https://github.com/breviam)
- [All Contributors](../../contributors)

## License

The GNU General Public License v3.0. Please see [License File](LICENSE.md) for more information.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.
