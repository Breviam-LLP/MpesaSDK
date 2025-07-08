<?php

use Illuminate\Support\Facades\Route;
use Breviam\MpesaSdk\Http\Controllers\WebhookController;

Route::prefix('mpesa/webhooks')->name('mpesa.webhooks.')->group(function () {
    // STK Push callback
    Route::post('/stk/callback', [WebhookController::class, 'stkCallback'])->name('stk.callback');

    // C2B callbacks
    Route::post('/c2b/validation', [WebhookController::class, 'c2bValidation'])->name('c2b.validation');
    Route::post('/c2b/confirmation', [WebhookController::class, 'c2bConfirmation'])->name('c2b.confirmation');
    
    // B2C callbacks
    Route::post('/b2c/result', [WebhookController::class, 'b2cResult'])->name('b2c.result');
    Route::post('/b2c/timeout', [WebhookController::class, 'b2cTimeout'])->name('b2c.timeout');

    // B2B callbacks
    Route::post('/b2b/result', [WebhookController::class, 'b2bResult'])->name('b2b.result');
    Route::post('/b2b/timeout', [WebhookController::class, 'b2bTimeout'])->name('b2b.timeout');

    // Reversal callbacks
    Route::post('/reversal/result', [WebhookController::class, 'reversalResult'])->name('reversal.result');
    Route::post('/reversal/timeout', [WebhookController::class, 'reversalTimeout'])->name('reversal.timeout');

    // Balance callbacks
    Route::post('/balance/result', [WebhookController::class, 'balanceResult'])->name('balance.result');
    Route::post('/balance/timeout', [WebhookController::class, 'balanceTimeout'])->name('balance.timeout');

    // Transaction status callbacks
    Route::post('/transaction/result', [WebhookController::class, 'transactionResult'])->name('transaction.result');
    Route::post('/transaction/timeout', [WebhookController::class, 'transactionTimeout'])->name('transaction.timeout');
});