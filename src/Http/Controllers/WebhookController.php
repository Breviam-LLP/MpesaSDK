<?php

namespace Breviam\MpesaSdk\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle STK Push callback
     */
    public function stkCallback(Request $request): JsonResponse
    {
        $data = $request->all();

        $this->logWebhook('stk_callback', $data);

        // Fire event for application to handle
        event('mpesa.stk.callback', $data);

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Success'
        ]);
    }

    /**
     * Handle C2B validation
     */
    public function c2bValidation(Request $request): JsonResponse
    {
        $data = $request->all();

        $this->logWebhook('c2b_validation', $data);

        // Fire event for application to handle
        $result = event('mpesa.c2b.validation', $data, true);

        // If event returns false, reject the transaction
        if ($result === false) {
            return response()->json([
                'ResultCode' => 'C2B00011',
                'ResultDesc' => 'Invalid Account'
            ]);
        }

        return response()->json([
            'ResultCode' => '0',
            'ResultDesc' => 'Success'
        ]);
    }

    /**
     * Handle C2B confirmation
     */
    public function c2bConfirmation(Request $request): JsonResponse
    {
        $data = $request->all();

        $this->logWebhook('c2b_confirmation', $data);

        // Fire event for application to handle
        event('mpesa.c2b.confirmation', $data);

        return response()->json([
            'ResultCode' => '0',
            'ResultDesc' => 'Success'
        ]);
    }

    /**
     * Handle B2C result
     */
    public function b2cResult(Request $request): JsonResponse
    {
        $data = $request->all();

        $this->logWebhook('b2c_result', $data);

        // Fire event for application to handle
        event('mpesa.b2c.result', $data);

        return response()->json([
            'ResultCode' => '0',
            'ResultDesc' => 'Success'
        ]);
    }

    /**
     * Handle B2C timeout
     */
    public function b2cTimeout(Request $request): JsonResponse
    {
        $data = $request->all();

        $this->logWebhook('b2c_timeout', $data);

        // Fire event for application to handle
        event('mpesa.b2c.timeout', $data);

        return response()->json([
            'ResultCode' => '0',
            'ResultDesc' => 'Success'
        ]);
    }

    /**
     * Handle balance result
     */
    public function balanceResult(Request $request): JsonResponse
    {
        $data = $request->all();

        $this->logWebhook('balance_result', $data);

        // Fire event for application to handle
        event('mpesa.balance.result', $data);

        return response()->json([
            'ResultCode' => '0',
            'ResultDesc' => 'Success'
        ]);
    }

    /**
     * Handle balance timeout
     */
    public function balanceTimeout(Request $request): JsonResponse
    {
        $data = $request->all();

        $this->logWebhook('balance_timeout', $data);

        // Fire event for application to handle
        event('mpesa.balance.timeout', $data);

        return response()->json([
            'ResultCode' => '0',
            'ResultDesc' => 'Success'
        ]);
    }

    /**
     * Handle transaction result
     */
    public function transactionResult(Request $request): JsonResponse
    {
        $data = $request->all();

        $this->logWebhook('transaction_result', $data);

        // Fire event for application to handle
        event('mpesa.transaction.result', $data);

        return response()->json([
            'ResultCode' => '0',
            'ResultDesc' => 'Success'
        ]);
    }

    /**
     * Handle transaction timeout
     */
    public function transactionTimeout(Request $request): JsonResponse
    {
        $data = $request->all();

        $this->logWebhook('transaction_timeout', $data);

        // Fire event for application to handle
        event('mpesa.transaction.timeout', $data);

        return response()->json([
            'ResultCode' => '0',
            'ResultDesc' => 'Success'
        ]);
    }

    /**
     * Handle B2B result
     */
    public function b2bResult(Request $request): JsonResponse
    {
        $data = $request->all();

        $this->logWebhook('b2b_result', $data);

        // Fire event for application to handle
        event('mpesa.b2b.result', $data);

        return response()->json([
            'ResultCode' => '0',
            'ResultDesc' => 'Success'
        ]);
    }

    /**
     * Handle B2B timeout
     */
    public function b2bTimeout(Request $request): JsonResponse
    {
        $data = $request->all();

        $this->logWebhook('b2b_timeout', $data);

        // Fire event for application to handle
        event('mpesa.b2b.timeout', $data);

        return response()->json([
            'ResultCode' => '0',
            'ResultDesc' => 'Success'
        ]);
    }

    /**
     * Handle reversal result
     */
    public function reversalResult(Request $request): JsonResponse
    {
        $data = $request->all();

        $this->logWebhook('reversal_result', $data);

        // Fire event for application to handle
        event('mpesa.reversal.result', $data);

        return response()->json([
            'ResultCode' => '0',
            'ResultDesc' => 'Success'
        ]);
    }

    /**
     * Handle reversal timeout
     */
    public function reversalTimeout(Request $request): JsonResponse
    {
        $data = $request->all();

        $this->logWebhook('reversal_timeout', $data);

        // Fire event for application to handle
        event('mpesa.reversal.timeout', $data);

        return response()->json([
            'ResultCode' => '0',
            'ResultDesc' => 'Success'
        ]);
    }

    /**
     * Log webhook data
     */
    protected function logWebhook(string $type, array $data): void
    {
        $config = config('mpesa.logging');

        if (!$config['enabled']) {
            return;
        }

        Log::channel($config['channel'])->info("M-Pesa Webhook: {$type}", [
            'type' => $type,
            'data' => $data,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
