<?php

namespace Breviam\MpesaSdk\Tests\Feature;

use Breviam\MpesaSdk\Tests\TestCase;
use Breviam\MpesaSdk\Facades\Mpesa;
use Breviam\MpesaSdk\Services\MpesaService;
use Breviam\MpesaSdk\Contracts\AuthInterface;
use Breviam\MpesaSdk\Contracts\StkInterface;
use Breviam\MpesaSdk\Contracts\C2bInterface;
use Breviam\MpesaSdk\Contracts\B2cInterface;
use Breviam\MpesaSdk\Contracts\B2bInterface;
use Breviam\MpesaSdk\Contracts\TransactionInterface;
use Breviam\MpesaSdk\Contracts\BalanceInterface;
use Breviam\MpesaSdk\Contracts\ReversalInterface;

class MpesaFacadeTest extends TestCase
{
    public function test_facade_resolves_mpesa_service(): void
    {
        $mpesaService = Mpesa::getFacadeRoot();

        $this->assertInstanceOf(MpesaService::class, $mpesaService);
    }

    public function test_facade_provides_access_to_all_services(): void
    {
        $this->assertInstanceOf(AuthInterface::class, Mpesa::auth());
        $this->assertInstanceOf(StkInterface::class, Mpesa::stk());
        $this->assertInstanceOf(C2bInterface::class, Mpesa::c2b());
        $this->assertInstanceOf(B2cInterface::class, Mpesa::b2c());
        $this->assertInstanceOf(B2bInterface::class, Mpesa::b2b());
        $this->assertInstanceOf(TransactionInterface::class, Mpesa::transaction());
        $this->assertInstanceOf(BalanceInterface::class, Mpesa::balance());
        $this->assertInstanceOf(ReversalInterface::class, Mpesa::reversal());
    }

    public function test_facade_has_shortcut_methods(): void
    {
        $mpesaService = Mpesa::getFacadeRoot();

        $this->assertTrue(method_exists($mpesaService, 'stkPush'));
        $this->assertTrue(method_exists($mpesaService, 'stkQuery'));
        $this->assertTrue(method_exists($mpesaService, 'sendMoney'));
        $this->assertTrue(method_exists($mpesaService, 'sendB2B'));
        $this->assertTrue(method_exists($mpesaService, 'reverseTransaction'));
        $this->assertTrue(method_exists($mpesaService, 'checkBalance'));
        $this->assertTrue(method_exists($mpesaService, 'checkTransactionStatus'));
    }
}
