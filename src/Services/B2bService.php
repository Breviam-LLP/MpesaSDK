<?php

namespace Breviam\MpesaSdk\Services;

use Breviam\MpesaSdk\Contracts\AuthInterface;
use Breviam\MpesaSdk\Contracts\B2bInterface;

class B2bService extends BaseService implements B2bInterface
{
    protected AuthInterface $authService;

    public function __construct(AuthInterface $authService)
    {
        parent::__construct();
        $this->authService = $authService;
    }

    /**
     * Send money from business to business
     */
    public function send(
        string $receiverShortcode,
        float $amount,
        string $commandId,
        string $senderIdentifierType = '4',
        string $recieverIdentifierType = '4',
        string $accountReference,
        string $remarks,
        string $occasion = ''
    ): array {
        $credentials = $this->getCredentials('b2b');

        $payload = [
            'Initiator' => $credentials['initiator'],
            'SecurityCredential' => $credentials['security_credential'],
            'CommandID' => $commandId,
            'SenderIdentifierType' => $senderIdentifierType,
            'RecieverIdentifierType' => $recieverIdentifierType,
            'Amount' => (int) $amount,
            'PartyA' => $credentials['shortcode'],
            'PartyB' => $receiverShortcode,
            'AccountReference' => $accountReference,
            'Remarks' => $remarks,
            'QueueTimeOutURL' => $this->getCallbackUrl('b2b', 'timeout'),
            'ResultURL' => $this->getCallbackUrl('b2b', 'result'),
            'Occasion' => $occasion,
        ];

        $headers = [
            'Authorization' => 'Bearer ' . $this->authService->getAccessToken('b2b'),
            'Content-Type' => 'application/json',
        ];

        return $this->makeRequest('POST', 'mpesa/b2b/v1/paymentrequest', $payload, $headers);
    }
}
