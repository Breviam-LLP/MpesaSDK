<?php

/**
 * M-Pesa SDK Advanced Usage Examples with Multiple Credentials
 * 
 * This file demonstrates how to use different credentials for different M-Pesa APIs
 */

use Breviam\MpesaSdk\Facades\Mpesa;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class AdvancedMpesaController extends Controller
{
    /**
     * Example 1: STK Push with default credentials
     */
    public function initiatePayment(Request $request)
    {
        try {
            // Uses credentials.stk configuration or falls back to default
            $response = Mpesa::stkPush(
                phone: $request->phone,
                amount: $request->amount,
                reference: 'ORDER_' . time(),
                description: 'Payment for Order #' . $request->order_id
            );

            return response()->json([
                'success' => true,
                'checkout_request_id' => $response['CheckoutRequestID']
            ]);

        } catch (\Exception $e) {
            Log::error('STK Push failed: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Example 2: B2C Payment using withdrawal credentials
     */
    public function withdrawMoney(Request $request)
    {
        try {
            // Uses credentials.withdrawal configuration for B2C
            $response = Mpesa::sendMoney(
                phone: $request->phone,
                amount: $request->amount,
                commandId: 'BusinessPayment',
                remarks: 'Withdrawal for customer',
                occasion: 'Customer withdrawal'
            );

            return response()->json([
                'success' => true,
                'conversation_id' => $response['ConversationID']
            ]);

        } catch (\Exception $e) {
            Log::error('B2C Withdrawal failed: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Example 3: C2B URL Registration with specific credentials
     */
    public function registerC2BUrls()
    {
        try {
            // Uses credentials.c2b configuration
            $response = Mpesa::registerC2BUrls(
                confirmationUrl: 'https://yourdomain.com/mpesa/c2b/confirmation',
                validationUrl: 'https://yourdomain.com/mpesa/c2b/validation'
            );

            return response()->json([
                'success' => true,
                'message' => 'URLs registered successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('C2B URL registration failed: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Example 4: Business to Business Payment
     */
    public function sendBusinessPayment(Request $request)
    {
        try {
            // Uses credentials.b2b configuration
            $response = Mpesa::sendBusinessPayment(
                receiverShortcode: $request->receiver_shortcode,
                amount: $request->amount,
                commandId: 'BusinessPayBill',
                accountReference: $request->account_reference,
                remarks: $request->remarks
            );

            return response()->json([
                'success' => true,
                'conversation_id' => $response['ConversationID']
            ]);

        } catch (\Exception $e) {
            Log::error('B2B Payment failed: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Example 5: Account Balance Query
     */
    public function checkBalance()
    {
        try {
            // Uses credentials.balance configuration
            $response = Mpesa::queryAccountBalance(
                remarks: 'Balance check at ' . now()->format('Y-m-d H:i:s')
            );

            return response()->json([
                'success' => true,
                'conversation_id' => $response['ConversationID']
            ]);

        } catch (\Exception $e) {
            Log::error('Balance query failed: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Example 6: Transaction Reversal
     */
    public function reverseTransaction(Request $request)
    {
        try {
            // Uses credentials.reversal configuration
            $response = Mpesa::reverseTransaction(
                transactionId: $request->transaction_id,
                amount: $request->amount,
                receiverParty: $request->receiver_party,
                receiverIdentifierType: '11', // MSISDN
                remarks: 'Transaction reversal'
            );

            return response()->json([
                'success' => true,
                'conversation_id' => $response['ConversationID']
            ]);

        } catch (\Exception $e) {
            Log::error('Transaction reversal failed: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Example 7: Using different credentials programmatically
     */
    public function handleMultipleOperations(Request $request)
    {
        try {
            // Each operation can use different credentials automatically
            // based on the credentials configuration in config/mpesa.php

            // 1. STK Push (uses stk credentials)
            $stkResponse = Mpesa::stkPush(
                phone: $request->phone,
                amount: 100,
                reference: 'TEST_' . time(),
                description: 'Test payment'
            );

            // 2. Balance check (uses balance credentials)
            $balanceResponse = Mpesa::queryAccountBalance(
                remarks: 'Balance check'
            );

            // 3. B2C using withdrawal credentials
            $b2cResponse = Mpesa::sendMoney(
                phone: $request->phone,
                amount: 50,
                commandId: 'BusinessPayment',
                remarks: 'Test withdrawal'
            );

            return response()->json([
                'success' => true,
                'operations' => [
                    'stk_push' => $stkResponse['CheckoutRequestID'] ?? null,
                    'balance_check' => $balanceResponse['ConversationID'] ?? null,
                    'b2c_payment' => $b2cResponse['ConversationID'] ?? null
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Multiple operations failed: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }
}

/**
 * Configuration Example for multiple credentials
 * 
 * Add this to your config/mpesa.php file:
 */

/*
'credentials' => [
    'stk' => [
        'consumer_key' => env('MPESA_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
        'shortcode' => env('MPESA_SHORTCODE'),
        'passkey' => env('MPESA_PASSKEY'),
    ],
    'c2b' => [
        'consumer_key' => env('MPESA_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
        'shortcode' => env('MPESA_SHORTCODE'),
    ],
    'b2c' => [
        'consumer_key' => env('MPESA_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
        'shortcode' => env('MPESA_SHORTCODE'),
        'initiator' => env('MPESA_INITIATOR_NAME'),
        'security_credential' => env('MPESA_SECURITY_CREDENTIAL'),
    ],
    'withdrawal' => [
        'consumer_key' => env('MPESA_W_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_W_CONSUMER_SECRET'),
        'shortcode' => env('MPESA_W_SHORTCODE'),
        'initiator' => env('MPESA_INITIATOR_W_NAME'),
        'security_credential' => env('MPESA_INITIATOR_W_PASS'),
    ],
    // ... other API-specific credentials
],
*/

/**
 * Environment Variables Example (.env file)
 */

/*
# Default credentials
MPESA_CONSUMER_KEY=cX0kPsvawLre3D7Yhf5clImbCoOTDKfM
MPESA_CONSUMER_SECRET=u2MfnNdDn5XPA9CA
MPESA_SHORTCODE=4077381
MPESA_PASSKEY=d033e2ba8ac59d1c301aad487c2c1a516b5716c3a10701e25ca3bddec704814e
MPESA_INITIATOR_NAME=balanceapi
MPESA_SECURITY_CREDENTIAL=your_security_credential

# Withdrawal-specific credentials
MPESA_W_CONSUMER_KEY=CrzTNefOaezlT5cbIDtqJedZbibMa5go
MPESA_W_CONSUMER_SECRET=H2c7LNQrQmXMl5jE
MPESA_W_SHORTCODE=3029755
MPESA_INITIATOR_W_NAME=wita
MPESA_INITIATOR_W_PASS=Macker90!

# Other settings
MPESA_ENV=sandbox
MPESA_CALLBACK_URL=https://yourdomain.com/mpesa/callbacks
*/
