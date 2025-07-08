<?php

namespace Breviam\MpesaSdk\Tests\Unit;

use Breviam\MpesaSdk\Tests\TestCase;
use Breviam\MpesaSdk\Services\ReversalService;
use Breviam\MpesaSdk\Contracts\AuthInterface;
use Mockery;

class ReversalServiceTest extends TestCase
{
    private ReversalService $reversalService;
    private $authMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authMock = Mockery::mock(AuthInterface::class);
        $this->reversalService = new ReversalService($this->authMock);
    }

    public function test_reverse_transaction(): void
    {
        // Test that the service is properly instantiated
        $this->assertInstanceOf(ReversalService::class, $this->reversalService);

        // Test that the service has the expected method
        $this->assertTrue(method_exists($this->reversalService, 'reverse'));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
