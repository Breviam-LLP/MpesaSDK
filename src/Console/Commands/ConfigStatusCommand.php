<?php

namespace Breviam\MpesaSdk\Console\Commands;

use Illuminate\Console\Command;

class ConfigStatusCommand extends Command
{
    protected $signature = 'mpesa:config {--service= : Check specific service configuration}';
    protected $description = 'Check M-Pesa configuration status';

    public function handle()
    {
        $this->info('M-Pesa Configuration Status');
        $this->line('=================================');

        $config = config('mpesa');
        $service = $this->option('service');

        if ($service) {
            $this->checkServiceConfig($service, $config);
        } else {
            $this->checkGeneralConfig($config);
            $this->line('');
            $this->checkAllServiceConfigs($config);
        }
    }

    private function checkGeneralConfig(array $config): void
    {
        $this->line('General Configuration:');
        $this->checkConfigValue('Environment', $config['env'] ?? null);
        $this->checkConfigValue('Callback URL', $config['callback_url'] ?? null);
        $this->checkConfigValue('Cache TTL', $config['cache']['ttl'] ?? null);
        $this->checkConfigValue('Timeout', $config['timeout'] ?? null);
        $this->checkConfigValue('Logging Enabled', $config['logging']['enabled'] ?? null);
    }

    private function checkAllServiceConfigs(array $config): void
    {
        $services = ['stk', 'c2b', 'b2c', 'b2b', 'balance', 'reversal', 'withdrawal'];

        $this->line('Service-Specific Configurations:');

        foreach ($services as $service) {
            $this->checkServiceConfig($service, $config, false);
        }
    }

    private function checkServiceConfig(string $service, array $config, bool $detailed = true): void
    {
        if ($detailed) {
            $this->line("Configuration for service: {$service}");
            $this->line(str_repeat('-', 40));
        }

        $credentials = $this->getServiceCredentials($service, $config);
        $status = $this->validateServiceCredentials($service, $credentials);

        if ($detailed) {
            foreach ($credentials as $key => $value) {
                $this->checkConfigValue(ucfirst(str_replace('_', ' ', $key)), $value);
            }

            // Check callback URLs
            $callbackUrl = $this->getCallbackUrl($service, $config);
            $this->checkConfigValue('Callback URL', $callbackUrl);

            $this->line('');
            if ($status['valid']) {
                $this->info("✓ Configuration for {$service} is valid");
            } else {
                $this->error("✗ Configuration for {$service} has issues:");
                foreach ($status['errors'] as $error) {
                    $this->line("  - {$error}");
                }
            }
        } else {
            $statusIcon = $status['valid'] ? '✓' : '✗';
            $statusColor = $status['valid'] ? 'green' : 'red';
            $this->line("  {$service}: <fg={$statusColor}>{$statusIcon}</>");
        }
    }

    private function getServiceCredentials(string $service, array $config): array
    {
        $defaultCredentials = [
            'consumer_key' => $config['consumer_key'] ?? null,
            'consumer_secret' => $config['consumer_secret'] ?? null,
            'shortcode' => $config['shortcode'] ?? null,
            'passkey' => $config['passkey'] ?? null,
            'initiator' => $config['initiator'] ?? null,
            'security_credential' => $config['security_credential'] ?? null,
        ];

        // New profile-based structure
        if (isset($config['services'][$service])) {
            $serviceConfig = $config['services'][$service];
            $profileName = $serviceConfig['profile'] ?? 'default';

            $profileCredentials = $this->resolveProfile($profileName, $config);

            // Merge with service-specific credential overrides
            if (isset($serviceConfig['credentials'])) {
                $profileCredentials = array_merge($profileCredentials, $serviceConfig['credentials']);
            }

            return $profileCredentials;
        }

        // Fallback to old credentials structure
        if (isset($config['credentials'][$service])) {
            return array_merge($defaultCredentials, $config['credentials'][$service]);
        }

        return $defaultCredentials;
    }

    private function resolveProfile(string $profileName, array $config): array
    {
        if (!isset($config['profiles'][$profileName])) {
            return [];
        }

        $profile = $config['profiles'][$profileName];

        // Handle profile inheritance
        if (isset($profile['extends'])) {
            $parentProfile = $this->resolveProfile($profile['extends'], $config);
            unset($profile['extends']);
            return array_merge($parentProfile, $profile);
        }

        return $profile;
    }

    private function validateServiceCredentials(string $service, array $credentials): array
    {
        $required = ['consumer_key', 'consumer_secret', 'shortcode'];
        $errors = [];

        // Add service-specific required fields
        switch ($service) {
            case 'stk':
                $required[] = 'passkey';
                break;
            case 'b2c':
            case 'b2b':
            case 'balance':
            case 'reversal':
            case 'withdrawal':
                $required = array_merge($required, ['initiator', 'security_credential']);
                break;
        }

        foreach ($required as $field) {
            if (empty($credentials[$field])) {
                $errors[] = "Missing required field: {$field}";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    private function getCallbackUrl(string $service, array $config): ?string
    {
        // Check new callback structure first
        if (isset($config['callbacks']['services'][$service])) {
            return $config['callbacks']['services'][$service];
        }

        // Check old structure for backward compatibility
        if (isset($config['callback_urls'][$service])) {
            return $config['callback_urls'][$service];
        }

        // Fallback to base URL
        return $config['callbacks']['base_url'] ?? $config['callback_url'] ?? null;
    }

    private function checkConfigValue(string $name, $value): void
    {
        if (!empty($value)) {
            $displayValue = is_bool($value) ? ($value ? 'true' : 'false') : $value;
            $this->line("  {$name}: <fg=green>✓</> {$displayValue}");
        } else {
            $this->line("  {$name}: <fg=red>✗ Not configured</>");
        }
    }
}
