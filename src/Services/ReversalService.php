<?php

namespace Breviam\MpesaSdk\Services;

use Breviam\MpesaSdk\Contracts\AuthInterface;
use Breviam\MpesaSdk\Contracts\ReversalInterface;

class ReversalService extends BaseService implements ReversalInterface
{
    protected AuthInterface $authService;

    public function __construct(AuthInterface $authService)
    {
        parent::__construct();
        $this->authService = $authService;
    }

    /**
     * Reverse a transaction
     */
    public function reverse(
        string $transactionId,
        float $amount,
        string $receiverParty,
        string $receiverIdentifierType,
        string $remarks,
        string $occasion = ''
    ): array {
        $credentials = $this->getCredentials('reversal');

        $payload = [
            'Initiator' => $credentials['initiator'],
            'SecurityCredential' => $credentials['security_credential'],
            'CommandID' => 'TransactionReversal',
            'TransactionID' => $transactionId,
            'Amount' => (int) $amount,
            'ReceiverParty' => $receiverParty,
            'RecieverIdentifierType' => $receiverIdentifierType,
            'ResultURL' => $this->getCallbackUrl('reversal', 'result'),
            'QueueTimeOutURL' => $this->getCallbackUrl('reversal', 'timeout'),
            'Remarks' => $remarks,
            'Occasion' => $occasion,
        ];

        $headers = [
            'Authorization' => 'Bearer ' . $this->authService->getAccessToken('reversal'),
            'Content-Type' => 'application/json',
        ];

        return $this->makeRequest('POST', 'mpesa/reversal/v1/request', $payload, $headers);
    }
}
