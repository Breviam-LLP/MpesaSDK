<?php

namespace Breviam\MpesaSdk\Tests\Feature;

use Breviam\MpesaSdk\Tests\TestCase;
use Breviam\MpesaSdk\Facades\Mpesa;
use Illuminate\Support\Facades\Http;

class StkPushTest extends TestCase
{
    /** @test */
    public function it_can_initiate_stk_push()
    {
        Http::fake([
            'sandbox.safaricom.co.ke/oauth/*' => Http::response([
                'access_token' => 'test_access_token',
                'expires_in' => 3599,
            ], 200),
            'sandbox.safaricom.co.ke/mpesa/stkpush/*' => Http::response([
                'MerchantRequestID' => 'test_merchant_request_id',
                'CheckoutRequestID' => 'test_checkout_request_id',
                'ResponseCode' => '0',
                'ResponseDescription' => 'Success. Request accepted for processing',
                'CustomerMessage' => 'Success. Request accepted for processing',
            ], 200),
        ]);

        $response = Mpesa::stkPush('0712345678', 100, 'TEST123', 'Test payment');

        $this->assertEquals('0', $response['ResponseCode']);
        $this->assertEquals('test_checkout_request_id', $response['CheckoutRequestID']);
    }

    /** @test */
    public function it_can_query_stk_push_status()
    {
        Http::fake([
            'sandbox.safaricom.co.ke/oauth/*' => Http::response([
                'access_token' => 'test_access_token',
                'expires_in' => 3599,
            ], 200),
            'sandbox.safaricom.co.ke/mpesa/stkpushquery/*' => Http::response([
                'ResponseCode' => '0',
                'ResponseDescription' => 'The service request has been accepted successfully',
                'MerchantRequestID' => 'test_merchant_request_id',
                'CheckoutRequestID' => 'test_checkout_request_id',
                'ResultCode' => '0',
                'ResultDesc' => 'The service request is processed successfully.',
            ], 200),
        ]);

        $response = Mpesa::stkQuery('test_checkout_request_id');

        $this->assertEquals('0', $response['ResponseCode']);
        $this->assertEquals('0', $response['ResultCode']);
    }
}
