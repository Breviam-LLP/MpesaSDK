<?php

namespace Breviam\MpesaSdk;

use Breviam\MpesaSdk\Contracts\AuthInterface;
use Breviam\MpesaSdk\Contracts\StkInterface;
use Breviam\MpesaSdk\Contracts\C2bInterface;
use Breviam\MpesaSdk\Contracts\B2cInterface;
use Breviam\MpesaSdk\Contracts\B2bInterface;
use Breviam\MpesaSdk\Contracts\TransactionInterface;
use Breviam\MpesaSdk\Contracts\BalanceInterface;
use Breviam\MpesaSdk\Contracts\ReversalInterface;
use Breviam\MpesaSdk\Services\MpesaAuthService;
use Breviam\MpesaSdk\Services\StkService;
use Breviam\MpesaSdk\Services\C2bService;
use Breviam\MpesaSdk\Services\B2cService;
use Breviam\MpesaSdk\Services\B2bService;
use Breviam\MpesaSdk\Services\TransactionService;
use Breviam\MpesaSdk\Services\BalanceService;
use Breviam\MpesaSdk\Services\ReversalService;
use Breviam\MpesaSdk\Services\MpesaService;
use Breviam\MpesaSdk\Console\Commands\TokenCommand;
use Breviam\MpesaSdk\Console\Commands\SimulatePaymentCommand;
use Breviam\MpesaSdk\Console\Commands\ConfigStatusCommand;
use Illuminate\Support\ServiceProvider;

class MpesaSdkServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/Config/mpesa.php',
            'mpesa'
        );

        // Bind contracts to implementations
        $this->app->bind(AuthInterface::class, MpesaAuthService::class);
        $this->app->bind(StkInterface::class, StkService::class);
        $this->app->bind(C2bInterface::class, C2bService::class);
        $this->app->bind(B2cInterface::class, B2cService::class);
        $this->app->bind(B2bInterface::class, B2bService::class);
        $this->app->bind(TransactionInterface::class, TransactionService::class);
        $this->app->bind(BalanceInterface::class, BalanceService::class);
        $this->app->bind(ReversalInterface::class, ReversalService::class);

        // Register main service
        $this->app->singleton('mpesa', function ($app) {
            return new MpesaService(
                $app->make(AuthInterface::class),
                $app->make(StkInterface::class),
                $app->make(C2bInterface::class),
                $app->make(B2cInterface::class),
                $app->make(B2bInterface::class),
                $app->make(TransactionInterface::class),
                $app->make(BalanceInterface::class),
                $app->make(ReversalInterface::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/Config/mpesa.php' => config_path('mpesa.php'),
            ], 'mpesa-config');

            $this->commands([
                TokenCommand::class,
                SimulatePaymentCommand::class,
                ConfigStatusCommand::class,
            ]);
        }

        $this->loadRoutesFrom(__DIR__ . '/routes/webhooks.php');
    }
}