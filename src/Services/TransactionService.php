<?php

namespace Breviam\MpesaSdk\Services;

use Breviam\MpesaSdk\Contracts\AuthInterface;
use Breviam\MpesaSdk\Contracts\TransactionInterface;

class TransactionService extends BaseService implements TransactionInterface
{
    protected AuthInterface $authService;

    public function __construct(AuthInterface $authService)
    {
        parent::__construct();
        $this->authService = $authService;
    }

    /**
     * Query transaction status
     */
    public function status(string $transactionId, string $partyA, string $remarks, string $occasion = '', string $identifierType = '1'): array
    {
        $credentials = $this->getCredentials();

        $payload = [
            'Initiator' => $credentials['initiator'],
            'SecurityCredential' => $credentials['security_credential'],
            'CommandID' => 'TransactionStatusQuery',
            'TransactionID' => $transactionId,
            'PartyA' => $partyA,
            'IdentifierType' => $identifierType,
            'ResultURL' => $this->getCallbackUrl('transaction_status', 'result'),
            'QueueTimeOutURL' => $this->config['callback_url'] . '/transaction/timeout',
            'Remarks' => $remarks,
            'Occasion' => $occasion,
        ];

        $headers = [
            'Authorization' => 'Bearer ' . $this->authService->getAccessToken(),
            'Content-Type' => 'application/json',
        ];

        return $this->makeRequest('POST', 'mpesa/transactionstatus/v1/query', $payload, $headers);
    }
}
