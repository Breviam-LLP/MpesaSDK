<?php

namespace Breviam\MpesaSdk\Contracts;

interface B2cInterface
{
    /**
     * Send money to customer
     */
    public function send(string $phone, float $amount, string $commandId, string $remarks, string $occasion = ''): array;
}
