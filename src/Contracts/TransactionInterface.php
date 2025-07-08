<?php

namespace Breviam\MpesaSdk\Contracts;

interface TransactionInterface
{
    /**
     * Query transaction status
     */
    public function status(string $transactionId, string $partyA, string $remarks, string $occasion = ''): array;
}
