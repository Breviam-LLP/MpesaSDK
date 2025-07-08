<?php

namespace Breviam\MpesaSdk\Console\Commands;

use Breviam\MpesaSdk\Contracts\AuthInterface;
use Illuminate\Console\Command;

class TokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'mpesa:token {--clear : Clear cached token}';

    /**
     * The console command description.
     */
    protected $description = 'Manage M-Pesa access tokens';

    /**
     * Execute the console command.
     */
    public function handle(AuthInterface $authService): int
    {
        if ($this->option('clear')) {
            $authService->clearCache();
            $this->info('M-Pesa access token cache cleared.');
            return self::SUCCESS;
        }

        try {
            $token = $authService->getAccessToken();
            $this->info('M-Pesa access token retrieved successfully.');
            $this->line('Token: ' . substr($token, 0, 20) . '...');
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to retrieve access token: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
