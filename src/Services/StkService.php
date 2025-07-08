<?php

namespace Breviam\MpesaSdk\Services;

use Breviam\MpesaSdk\Contracts\AuthInterface;
use Breviam\MpesaSdk\Contracts\StkInterface;
use Breviam\MpesaSdk\Helpers\Utils;
use Carbon\Carbon;

class StkService extends BaseService implements StkInterface
{
    protected AuthInterface $authService;

    public function __construct(AuthInterface $authService)
    {
        parent::__construct();
        $this->authService = $authService;
    }

    /**
     * Initiate STK Push payment
     */
    public function push(string $phone, float $amount, string $reference, string $description, string $transactionType = null): array
    {
        $this->validateConfig('stk');

        $credentials = $this->getCredentials('stk');
        $phone = Utils::formatPhoneNumber($phone);
        $timestamp = Utils::generateTimestamp();
        $password = Utils::generatePassword($credentials['shortcode'], $credentials['passkey'], $timestamp);

        // Use configured transaction type if not provided
        $transactionType = $transactionType ?? $this->getServiceConfig('stk', 'type', 'CustomerPayBillOnline');

        $payload = [
            'BusinessShortCode' => $credentials['shortcode'],
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => $transactionType,
            'Amount' => (int) $amount,
            'PartyA' => $phone,
            'PartyB' => $credentials['shortcode'],
            'PhoneNumber' => $phone,
            'CallBackURL' => $this->getCallbackUrl('stk', 'callback'),
            'AccountReference' => $reference,
            'TransactionDesc' => $description,
        ];

        $headers = [
            'Authorization' => 'Bearer ' . $this->authService->getAccessToken('stk'),
            'Content-Type' => 'application/json',
        ];

        return $this->makeRequest('POST', 'mpesa/stkpush/v1/processrequest', $payload, $headers);
    }

    /**
     * Query STK Push transaction status
     */
    public function query(string $checkoutRequestId): array
    {
        $credentials = $this->getCredentials('stk');
        $timestamp = Utils::generateTimestamp();
        $password = Utils::generatePassword($credentials['shortcode'], $credentials['passkey'], $timestamp);

        $payload = [
            'BusinessShortCode' => $credentials['shortcode'],
            'Password' => $password,
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkoutRequestId,
        ];

        $headers = [
            'Authorization' => 'Bearer ' . $this->authService->getAccessToken('stk'),
            'Content-Type' => 'application/json',
        ];

        return $this->makeRequest('POST', 'mpesa/stkpushquery/v1/query', $payload, $headers);
    }
}
