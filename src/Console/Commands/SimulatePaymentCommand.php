<?php

namespace Breviam\MpesaSdk\Console\Commands;

use Breviam\MpesaSdk\Contracts\C2bInterface;
use Illuminate\Console\Command;

class SimulatePaymentCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'mpesa:simulate-payment 
                            {phone : Phone number to simulate payment from}
                            {amount : Amount to simulate}
                            {--reference= : Payment reference}
                            {--command=CustomerPayBillOnline : Command ID}';

    /**
     * The console command description.
     */
    protected $description = 'Simulate C2B payment (sandbox only)';

    /**
     * Execute the console command.
     */
    public function handle(C2bInterface $c2bService): int
    {
        if (config('mpesa.env') !== 'sandbox') {
            $this->error('Payment simulation is only available in sandbox environment.');
            return self::FAILURE;
        }

        $phone = $this->argument('phone');
        $amount = (float) $this->argument('amount');
        $reference = $this->option('reference') ?: 'SIM' . time();
        $command = $this->option('command');

        try {
            $response = $c2bService->simulate($phone, $amount, $reference, $command);

            $this->info('Payment simulation request sent successfully.');
            $this->table(
                ['Field', 'Value'],
                [
                    ['Phone', $phone],
                    ['Amount', $amount],
                    ['Reference', $reference],
                    ['Command', $command],
                    ['Response Code', $response['ResponseCode'] ?? 'N/A'],
                    ['Response Description', $response['ResponseDescription'] ?? 'N/A'],
                ]
            );

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Payment simulation failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
