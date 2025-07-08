<?php

namespace Breviam\MpesaSdk\Contracts;

interface ReversalInterface
{
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
    ): array;
}
