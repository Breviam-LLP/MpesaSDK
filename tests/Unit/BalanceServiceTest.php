<?php

namespace Breviam\MpesaSdk\Tests\Unit;

use Breviam\MpesaSdk\Tests\TestCase;
use Breviam\MpesaSdk\Services\BalanceService;
use Breviam\MpesaSdk\Contracts\AuthInterface;
use Mockery;

class BalanceServiceTest extends TestCase
{
    private BalanceService $balanceService;
    private $authMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authMock = Mockery::mock(AuthInterface::class);
        $this->balanceService = new BalanceService($this->authMock);
    }

    public function test_query_balance(): void
    {
        // Test that the service is properly instantiated
        $this->assertInstanceOf(BalanceService::class, $this->balanceService);

        // Test that the service has the expected method
        $this->assertTrue(method_exists($this->balanceService, 'query'));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
