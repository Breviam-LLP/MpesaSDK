<?php

namespace Breviam\MpesaSdk\Services;

use Breviam\MpesaSdk\Contracts\AuthInterface;
use Breviam\MpesaSdk\Contracts\StkInterface;
use Breviam\MpesaSdk\Contracts\C2bInterface;
use Breviam\MpesaSdk\Contracts\B2cInterface;
use Breviam\MpesaSdk\Contracts\B2bInterface;
use Breviam\MpesaSdk\Contracts\TransactionInterface;
use Breviam\MpesaSdk\Contracts\BalanceInterface;
use Breviam\MpesaSdk\Contracts\ReversalInterface;

class MpesaService
{
    protected AuthInterface $authService;
    protected StkInterface $stkService;
    protected C2bInterface $c2bService;
    protected B2cInterface $b2cService;
    protected B2bInterface $b2bService;
    protected TransactionInterface $transactionService;
    protected BalanceInterface $balanceService;
    protected ReversalInterface $reversalService;

    public function __construct(
        AuthInterface $authService,
        StkInterface $stkService,
        C2bInterface $c2bService,
        B2cInterface $b2cService,
        B2bInterface $b2bService,
        TransactionInterface $transactionService,
        BalanceInterface $balanceService,
        ReversalInterface $reversalService
    ) {
        $this->authService = $authService;
        $this->stkService = $stkService;
        $this->c2bService = $c2bService;
        $this->b2cService = $b2cService;
        $this->b2bService = $b2bService;
        $this->transactionService = $transactionService;
        $this->balanceService = $balanceService;
        $this->reversalService = $reversalService;
    }

    /**
     * Get auth service
     */
    public function auth(): AuthInterface
    {
        return $this->authService;
    }

    /**
     * Get STK service
     */
    public function stk(): StkInterface
    {
        return $this->stkService;
    }

    /**
     * Get C2B service
     */
    public function c2b(): C2bInterface
    {
        return $this->c2bService;
    }

    /**
     * Get B2C service
     */
    public function b2c(): B2cInterface
    {
        return $this->b2cService;
    }

    /**
     * Get B2B service
     */
    public function b2b(): B2bInterface
    {
        return $this->b2bService;
    }

    /**
     * Get transaction service
     */
    public function transaction(): TransactionInterface
    {
        return $this->transactionService;
    }

    /**
     * Get balance service
     */
    public function balance(): BalanceInterface
    {
        return $this->balanceService;
    }

    /**
     * Get reversal service
     */
    public function reversal(): ReversalInterface
    {
        return $this->reversalService;
    }

    /**
     * Shortcut method for STK Push
     */
    public function stkPush(string $phone, float $amount, string $reference, string $description): array
    {
        return $this->stkService->push($phone, $amount, $reference, $description);
    }

    /**
     * Shortcut method for STK Query
     */
    public function stkQuery(string $checkoutRequestId): array
    {
        return $this->stkService->query($checkoutRequestId);
    }

    /**
     * Shortcut method for B2C payment
     */
    public function sendMoney(string $phone, float $amount, string $commandId, string $remarks, string $occasion = ''): array
    {
        return $this->b2cService->send($phone, $amount, $commandId, $remarks, $occasion);
    }

    /**
     * Shortcut method for balance inquiry
     */
    public function checkBalance(string $remarks = 'Balance Inquiry'): array
    {
        return $this->balanceService->query($remarks);
    }

    /**
     * Shortcut method for transaction status
     */
    public function checkTransactionStatus(string $transactionId, string $partyA, string $remarks, string $occasion = ''): array
    {
        return $this->transactionService->status($transactionId, $partyA, $remarks, $occasion);
    }

    /**
     * Shortcut method for B2B payment
     */
    public function sendB2B(
        string $receiverShortcode,
        float $amount,
        string $commandId,
        string $accountReference,
        string $remarks,
        string $occasion = ''
    ): array {
        return $this->b2bService->send($receiverShortcode, $amount, $commandId, '4', '4', $accountReference, $remarks, $occasion);
    }

    /**
     * Shortcut method for transaction reversal
     */
    public function reverseTransaction(
        string $transactionId,
        float $amount,
        string $receiverParty,
        string $receiverIdentifierType,
        string $remarks,
        string $occasion = ''
    ): array {
        return $this->reversalService->reverse($transactionId, $amount, $receiverParty, $receiverIdentifierType, $remarks, $occasion);
    }
}
