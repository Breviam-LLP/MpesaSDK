<?php

namespace Breviam\MpesaSdk\Services;

use Breviam\MpesaSdk\Contracts\AuthInterface;
use Breviam\MpesaSdk\Contracts\B2cInterface;
use Breviam\MpesaSdk\Helpers\Utils;

class B2cService extends BaseService implements B2cInterface
{
    protected AuthInterface $authService;

    public function __construct(AuthInterface $authService)
    {
        parent::__construct();
        $this->authService = $authService;
    }

    /**
     * Send money to customer
     */
    public function send(string $phone, float $amount, string $commandId, string $remarks, string $occasion = ''): array
    {
        $credentials = $this->getCredentials('b2c');
        $phone = Utils::formatPhoneNumber($phone);

        $payload = [
            'InitiatorName' => $credentials['initiator'],
            'SecurityCredential' => $credentials['security_credential'],
            'CommandID' => $commandId,
            'Amount' => (int) $amount,
            'PartyA' => $credentials['shortcode'],
            'PartyB' => $phone,
            'Remarks' => $remarks,
            'QueueTimeOutURL' => $this->getCallbackUrl('b2c', 'timeout'),
            'ResultURL' => $this->getCallbackUrl('b2c', 'result'),
            'Occasion' => $occasion,
        ];

        $headers = [
            'Authorization' => 'Bearer ' . $this->authService->getAccessToken('b2c'),
            'Content-Type' => 'application/json',
        ];

        return $this->makeRequest('POST', 'mpesa/b2c/v1/paymentrequest', $payload, $headers);
    }
}
