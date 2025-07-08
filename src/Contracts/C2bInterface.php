<?php

namespace Breviam\MpesaSdk\Contracts;

interface C2bInterface
{
    /**
     * Register C2B URLs
     */
    public function registerUrls(string $confirmationUrl, string $validationUrl): array;

    /**
     * Simulate C2B payment (sandbox only)
     */
    public function simulate(string $phone, float $amount, string $reference, string $commandId = 'CustomerPayBillOnline'): array;
}
