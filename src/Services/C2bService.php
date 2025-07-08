<?php

namespace Breviam\MpesaSdk\Services;

use Breviam\MpesaSdk\Contracts\AuthInterface;
use Breviam\MpesaSdk\Contracts\C2bInterface;
use Breviam\MpesaSdk\Helpers\Utils;

class C2bService extends BaseService implements C2bInterface
{
    protected AuthInterface $authService;

    public function __construct(AuthInterface $authService)
    {
        parent::__construct();
        $this->authService = $authService;
    }

    /**
     * Register C2B URLs
     */
    public function registerUrls(string $confirmationUrl, string $validationUrl, string $responseType = 'Completed'): array
    {
        $credentials = $this->getCredentials('c2b');

        $payload = [
            'ShortCode' => $credentials['shortcode'],
            'ResponseType' => $responseType,
            'ConfirmationURL' => $confirmationUrl,
            'ValidationURL' => $validationUrl,
        ];

        $headers = [
            'Authorization' => 'Bearer ' . $this->authService->getAccessToken('c2b'),
            'Content-Type' => 'application/json',
        ];

        return $this->makeRequest('POST', 'mpesa/c2b/v1/registerurl', $payload, $headers);
    }

    /**
     * Simulate C2B payment (sandbox only)
     */
    public function simulate(string $phone, float $amount, string $reference, string $commandId = 'CustomerPayBillOnline'): array
    {
        if ($this->config['env'] !== 'sandbox') {
            throw new \InvalidArgumentException('C2B simulation is only available in sandbox environment');
        }

        $credentials = $this->getCredentials('c2b');
        $phone = Utils::formatPhoneNumber($phone);

        $payload = [
            'ShortCode' => $credentials['shortcode'],
            'CommandID' => $commandId,
            'Amount' => (int) $amount,
            'Msisdn' => $phone,
            'BillRefNumber' => $reference,
        ];

        $headers = [
            'Authorization' => 'Bearer ' . $this->authService->getAccessToken('c2b'),
            'Content-Type' => 'application/json',
        ];

        return $this->makeRequest('POST', 'mpesa/c2b/v1/simulate', $payload, $headers);
    }
}
