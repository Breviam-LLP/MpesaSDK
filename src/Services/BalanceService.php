<?php

namespace Breviam\MpesaSdk\Services;

use Breviam\MpesaSdk\Contracts\AuthInterface;
use Breviam\MpesaSdk\Contracts\BalanceInterface;

class BalanceService extends BaseService implements BalanceInterface
{
    protected AuthInterface $authService;

    public function __construct(AuthInterface $authService)
    {
        parent::__construct();
        $this->authService = $authService;
    }

    /**
     * Check account balance
     */
    public function query(string $remarks, string $commandId = 'AccountBalance', string $identifierType = '4'): array
    {
        $credentials = $this->getCredentials('balance');

        $payload = [
            'Initiator' => $credentials['initiator'],
            'SecurityCredential' => $credentials['security_credential'],
            'CommandID' => $commandId,
            'PartyA' => $credentials['shortcode'],
            'IdentifierType' => $identifierType,
            'Remarks' => $remarks,
            'QueueTimeOutURL' => $this->getCallbackUrl('balance', 'timeout'),
            'ResultURL' => $this->getCallbackUrl('balance', 'result'),
        ];

        $headers = [
            'Authorization' => 'Bearer ' . $this->authService->getAccessToken('balance'),
            'Content-Type' => 'application/json',
        ];

        return $this->makeRequest('POST', 'mpesa/accountbalance/v1/query', $payload, $headers);
    }
}
