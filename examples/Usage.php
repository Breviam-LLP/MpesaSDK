<?php

/**
 * M-Pesa SDK Usage Examples
 * 
 * This file demonstrates various ways to use the M-Pesa Laravel SDK
 * Copy the relevant examples to your Laravel application
 */

use Breviam\MpesaSdk\Facades\Mpesa;
use Breviam\MpesaSdk\Contracts\StkInterface;
use Breviam\MpesaSdk\Contracts\AuthInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class MpesaExampleController extends Controller
{
    /**
     * Example 1: Basic STK Push
     */
    public function initiatePayment(Request $request)
    {
        try {
            $response = Mpesa::stkPush(
                phone: $request->phone,
                amount: $request->amount,
                reference: 'ORDER_' . time(),
                description: 'Payment for Order #' . $request->order_id
            );

            if ($response['ResponseCode'] == '0') {
                // Store the CheckoutRequestID for later status checking
                return response()->json([
                    'success' => true,
                    'message' => 'Payment request sent successfully',
                    'checkout_request_id' => $response['CheckoutRequestID']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment request failed'
            ], 400);

        } catch (\Exception $e) {
            Log::error('STK Push failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Payment request failed'
            ], 500);
        }
    }

    /**
     * Example 2: Check STK Push Status
     */
    public function checkPaymentStatus(Request $request)
    {
        try {
            $response = Mpesa::stkQuery($request->checkout_request_id);

            return response()->json([
                'success' => true,
                'status' => $response
            ]);

        } catch (\Exception $e) {
            Log::error('STK Query failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Status check failed'
            ], 500);
        }
    }

    /**
     * Example 3: B2C Payment (Send Money)
     */
    public function sendMoney(Request $request)
    {
        try {
            $response = Mpesa::sendMoney(
                phone: $request->phone,
                amount: $request->amount,
                commandId: 'BusinessPayment', // or 'SalaryPayment', 'PromotionPayment'
                remarks: $request->remarks,
                occasion: $request->occasion ?? ''
            );

            return response()->json([
                'success' => true,
                'message' => 'Money sent successfully',
                'transaction_id' => $response['ConversationID'] ?? null
            ]);

        } catch (\Exception $e) {
            Log::error('B2C Payment failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Payment failed'
            ], 500);
        }
    }

    /**
     * Example 4: Check Account Balance
     */
    public function checkBalance()
    {
        try {
            $response = Mpesa::checkBalance('Balance inquiry from web app');

            return response()->json([
                'success' => true,
                'message' => 'Balance inquiry sent',
                'transaction_id' => $response['ConversationID'] ?? null
            ]);

        } catch (\Exception $e) {
            Log::error('Balance inquiry failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Balance inquiry failed'
            ], 500);
        }
    }

    /**
     * Example 5: Register C2B URLs (Usually done once during setup)
     */
    public function registerC2BUrls()
    {
        try {
            $response = Mpesa::c2b()->registerUrls(
                confirmationUrl: config('app.url') . '/mpesa/webhooks/c2b/confirmation',
                validationUrl: config('app.url') . '/mpesa/webhooks/c2b/validation'
            );

            return response()->json([
                'success' => true,
                'message' => 'C2B URLs registered successfully',
                'response' => $response
            ]);

        } catch (\Exception $e) {
            Log::error('C2B URL registration failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'URL registration failed'
            ], 500);
        }
    }

    /**
     * Example 6: Simulate C2B Payment (Sandbox only)
     */
    public function simulateC2BPayment(Request $request)
    {
        if (config('mpesa.env') !== 'sandbox') {
            return response()->json([
                'success' => false,
                'message' => 'Simulation only available in sandbox'
            ], 400);
        }

        try {
            $response = Mpesa::c2b()->simulate(
                phone: $request->phone,
                amount: $request->amount,
                reference: $request->reference,
                commandId: 'CustomerPayBillOnline'
            );

            return response()->json([
                'success' => true,
                'message' => 'Payment simulation successful',
                'response' => $response
            ]);

        } catch (\Exception $e) {
            Log::error('C2B simulation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Simulation failed'
            ], 500);
        }
    }
}

/**
 * Event Listeners Example
 * Add these to your EventServiceProvider
 */
class MpesaEventListeners
{
    public function boot()
    {
        // STK Push callback
        Event::listen('mpesa.stk.callback', function ($data) {
            Log::info('STK Push callback received', $data);

            // Extract callback data
            $stkCallback = $data['Body']['stkCallback'] ?? null;
            if ($stkCallback) {
                $resultCode = $stkCallback['ResultCode'];
                $checkoutRequestId = $stkCallback['CheckoutRequestID'];

                if ($resultCode == 0) {
                    // Payment successful
                    Log::info("Payment successful for checkout request: {$checkoutRequestId}");

                    // Update your order/payment status in database
                    // Order::where('checkout_request_id', $checkoutRequestId)
                    //     ->update(['status' => 'paid']);
                } else {
                    // Payment failed
                    Log::warning("Payment failed for checkout request: {$checkoutRequestId}");
                }
            }
        });

        // C2B confirmation
        Event::listen('mpesa.c2b.confirmation', function ($data) {
            Log::info('C2B confirmation received', $data);

            // Process the payment
            $transactionType = $data['TransType'] ?? null;
            $transId = $data['TransID'] ?? null;
            $transTime = $data['TransTime'] ?? null;
            $transAmount = $data['TransAmount'] ?? null;
            $businessShortCode = $data['BusinessShortCode'] ?? null;
            $billRefNumber = $data['BillRefNumber'] ?? null;
            $invoiceNumber = $data['InvoiceNumber'] ?? null;
            $orgAccountBalance = $data['OrgAccountBalance'] ?? null;
            $thirdPartyTransID = $data['ThirdPartyTransID'] ?? null;
            $msisdn = $data['MSISDN'] ?? null;
            $firstName = $data['FirstName'] ?? null;
            $middleName = $data['MiddleName'] ?? null;
            $lastName = $data['LastName'] ?? null;

            // Save payment to database
            // Payment::create([
            //     'transaction_id' => $transId,
            //     'phone' => $msisdn,
            //     'amount' => $transAmount,
            //     'reference' => $billRefNumber,
            //     'status' => 'confirmed'
            // ]);
        });

        // C2B validation
        Event::listen('mpesa.c2b.validation', function ($data) {
            Log::info('C2B validation request', $data);

            // Validate the payment
            $billRefNumber = $data['BillRefNumber'] ?? null;

            // Check if the bill reference exists in your system
            // $order = Order::where('reference', $billRefNumber)->first();
            // 
            // if (!$order) {
            //     return false; // Reject the payment
            // }

            return true; // Accept the payment
        });

        // B2C result
        Event::listen('mpesa.b2c.result', function ($data) {
            Log::info('B2C result received', $data);

            // Process B2C result
            $result = $data['Result'] ?? null;
            if ($result) {
                $resultCode = $result['ResultCode'];
                $conversationId = $result['ConversationID'];

                if ($resultCode == 0) {
                    Log::info("B2C payment successful: {$conversationId}");
                } else {
                    Log::warning("B2C payment failed: {$conversationId}");
                }
            }
        });

        // Balance result
        Event::listen('mpesa.balance.result', function ($data) {
            Log::info('Balance inquiry result', $data);

            // Process balance result
            $result = $data['Result'] ?? null;
            if ($result && $result['ResultCode'] == 0) {
                $resultParameters = $result['ResultParameters']['ResultParameter'] ?? [];

                foreach ($resultParameters as $param) {
                    if ($param['Key'] === 'AccountBalance') {
                        $balance = $param['Value'];
                        Log::info("Current account balance: {$balance}");
                        break;
                    }
                }
            }
        });
    }
}

/**
 * Custom Service Example
 * If you need more control, inject the services directly
 */
class CustomMpesaService
{
    public function __construct(
        private StkInterface $stkService,
        private AuthInterface $authService
    ) {
    }

    public function processPayment(array $paymentData)
    {
        // Custom business logic here

        // Validate payment data
        if (!$this->validatePaymentData($paymentData)) {
            throw new \InvalidArgumentException('Invalid payment data');
        }

        // Initiate payment
        $response = $this->stkService->push(
            $paymentData['phone'],
            $paymentData['amount'],
            $paymentData['reference'],
            $paymentData['description']
        );

        // Store payment request in database
        // PaymentRequest::create([
        //     'checkout_request_id' => $response['CheckoutRequestID'],
        //     'phone' => $paymentData['phone'],
        //     'amount' => $paymentData['amount'],
        //     'reference' => $paymentData['reference'],
        //     'status' => 'pending'
        // ]);

        return $response;
    }

    private function validatePaymentData(array $data): bool
    {
        return isset($data['phone'], $data['amount'], $data['reference'], $data['description']) &&
            $data['amount'] > 0 &&
            !empty($data['phone']);
    }
}
