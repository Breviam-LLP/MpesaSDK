<?php

namespace Breviam\MpesaSdk\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Breviam\MpesaSdk\Contracts\AuthInterface auth()
 * @method static \Breviam\MpesaSdk\Contracts\StkInterface stk()
 * @method static \Breviam\MpesaSdk\Contracts\C2bInterface c2b()
 * @method static \Breviam\MpesaSdk\Contracts\B2cInterface b2c()
 * @method static \Breviam\MpesaSdk\Contracts\B2bInterface b2b()
 * @method static \Breviam\MpesaSdk\Contracts\TransactionInterface transaction()
 * @method static \Breviam\MpesaSdk\Contracts\BalanceInterface balance()
 * @method static \Breviam\MpesaSdk\Contracts\ReversalInterface reversal()
 * @method static array stkPush(string $phone, float $amount, string $reference, string $description)
 * @method static array stkQuery(string $checkoutRequestId)
 * @method static array sendMoney(string $phone, float $amount, string $commandId, string $remarks, string $occasion = '')
 * @method static array sendB2B(string $receiverShortcode, float $amount, string $commandId, string $accountReference, string $remarks, string $occasion = '')
 * @method static array reverseTransaction(string $transactionId, float $amount, string $receiverParty, string $receiverIdentifierType, string $remarks, string $occasion = '')
 * @method static array checkBalance(string $remarks = 'Balance Inquiry')
 * @method static array checkTransactionStatus(string $transactionId, string $partyA, string $remarks, string $occasion = '')
 */
class Mpesa extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'mpesa';
    }
}
