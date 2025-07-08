<?php

namespace Breviam\MpesaSdk\Tests\Unit;

use Breviam\MpesaSdk\Tests\TestCase;
use Breviam\MpesaSdk\Services\B2bService;
use Breviam\MpesaSdk\Contracts\AuthInterface;
use Mockery;

class B2bServiceTest extends TestCase
{
    private B2bService $b2bService;
    private $authMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authMock = Mockery::mock(AuthInterface::class);
        $this->b2bService = new B2bService($this->authMock);
    }

    public function test_send_b2b_payment(): void
    {
        // Test that the service is properly instantiated
        $this->assertInstanceOf(B2bService::class, $this->b2bService);

        // Test that the service has the expected method
        $this->assertTrue(method_exists($this->b2bService, 'send'));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
