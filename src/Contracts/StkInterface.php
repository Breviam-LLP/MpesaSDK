<?php

namespace Breviam\MpesaSdk\Contracts;

interface StkInterface
{
    /**
     * Initiate STK Push payment
     */
    public function push(string $phone, float $amount, string $reference, string $description): array;

    /**
     * Query STK Push transaction status
     */
    public function query(string $checkoutRequestId): array;
}
