<?php

namespace Breviam\MpesaSdk\Contracts;

interface B2bInterface
{
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
    ): array;
}
