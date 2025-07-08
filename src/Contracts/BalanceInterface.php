<?php

namespace Breviam\MpesaSdk\Contracts;

interface BalanceInterface
{
    /**
     * Check account balance
     */
    public function query(string $remarks): array;
}
