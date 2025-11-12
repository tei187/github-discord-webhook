<?php

namespace tei187\GitDisWebhook\ValueObjects;

use tei187\GitDisWebhook\Handlers\ConfigHandler;
use tei187\GitDisWebhook\Traits\UsesMagicGetter;

/**
 * Represents the configuration for the application, loaded from the `config` directory.
 * Read-only value object.
 * 
 * @uses \tei187\GitDisWebhook\Traits\UsesMagicGetter
 * 
 * @package tei187\GitDisWebhook\ValueObjects
 */
readonly class Config {
    use UsesMagicGetter;

    public array $allowed;
    public array $messages;
    public array $payloads;
    public array $profiles;
    public array $profiles_defaults;
    public array $services;
    public array $webhooks;

    /**
     * Constructs a new Config instance by loading configuration data from `config` directory.
     */
    public function __construct() {
        $data = ConfigHandler::load();

        $this->allowed =  $data['allowed'];
        $this->messages = $data['messages'];
        $this->payloads = $data['payloads'];
        $this->profiles = $data['profiles'];
        $this->services = $data['services'];
        $this->webhooks = $data['webhooks'];
        $this->profiles_defaults = $data['profiles_defaults'];
    }

    /**
     * Gets a configuration value by key.
     *
     * @param string $key The configuration key.
     * @param mixed $default The default value to return if the key does not exist.
     * @return mixed The configuration value or the default value.
     */
    public function get(string $key, mixed $default = null): mixed {
        return match ($key) {
            'allowed' => $this->allowed,
            'messages' => $this->messages,
            'payloads' => $this->payloads,
            'profiles' => $this->profiles,
            'profiles_defaults' => $this->profiles_defaults,
            'services' => $this->services,
            'webhooks' => $this->webhooks,
            default => $default
        };
    }

    /**
     * Gets all configuration values as an associative array or object of associative arrays.
     *
     * @param bool $asObject Whether to return the values as an object (first level). Default is false.
     * @return array The configuration values.
     */
    public function all(bool $asObject = false): array|object {
        $temp = [
            'allowed' => $this->allowed,
            'messages' => $this->messages,
            'payloads' => $this->payloads,
            'profiles' => $this->profiles,
            'profiles_defaults' => $this->profiles_defaults,
            'services' => $this->services,
            'webhooks' => $this->webhooks,
        ];

        return $asObject ? (object) $temp : $temp;
    }

    /**
     * Validates the configuration data to ensure required keys and class references exist.
     * 
     * @return bool True if the configuration is valid.
     * @throws \ErrorException If required configuration files are missing.
     * @throws \InvalidArgumentException If class references in the configuration are invalid.
     */
    public function validate(): bool {
        // basic validation to ensure required keys exist
        $requiredKeys = [
            'allowed',
            'messages',
            'payloads',
            'profiles',
            'profiles_defaults',
            'services',
            'webhooks',
        ];

        // check for required keys
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $this->all())) {
                throw new \ErrorException("Missing required config key: {$key}. Check if appropriate config files exist in 'config' directory.");
            }
        }

        // validate services

            $servicesClasses = $this->services['registry'] ?? [];
            $servicesIdentifiers = array_keys($servicesClasses);

            // check if services registry classes exist
            foreach ($servicesClasses as $serviceName => $class) {
                if (!class_exists($class)) {
                    throw new \InvalidArgumentException("Service class does not exist: {$class} (registered as: {$serviceName})");
                }
            }

            // check if services detectors reference valid service identifiers
            foreach ($this->services['detectors'] as $serviceName => $config) {
                if (!in_array($serviceName, $servicesIdentifiers, true)) {
                    throw new \InvalidArgumentException("Service detector references undefined service identifier: {$serviceName}");
                }
            }

        // validate webhooks
            $webhooksClasses = $this->webhooks['registry'] ?? [];
            $webhooksIdentifiers = array_keys($webhooksClasses);

            // check if webhooks registry classes exist
            foreach ($webhooksClasses as $webhookName => $class) {
                if (!class_exists($class)) {
                    throw new \InvalidArgumentException("Webhook class does not exist: {$class} (registered as: {$webhookName})");
                }
            }

            // check if webhooks detectors reference valid webhook identifiers
            foreach ($this->webhooks['detectors'] as $webhookName => $config) {
                if (!in_array($webhookName, $webhooksIdentifiers, true)) {
                    throw new \InvalidArgumentException("Webhook detector references undefined webhook identifier: {$webhookName}");
                }
            }

        // validate profiles
            foreach ($this->profiles as $profileName => $profileConfig) {
                // check if services in profile reference valid service identifiers or is a service class name
                if (isset($profileConfig['services']) && is_array($profileConfig['services'])) {
                    foreach ($profileConfig['services'] as $serviceIdentifier) {
                        if (!in_array($serviceIdentifier, $servicesIdentifiers, true) && !in_array($serviceIdentifier, $servicesClasses, true)) {
                            throw new \InvalidArgumentException("Profile '{$profileName}' references undefined service identifier: {$serviceIdentifier}");
                        }
                    }
                }

                // check if webhook in profile references valid webhook identifier or is a webhook class name
                if (isset($profileConfig['webhook']['class'])) {
                    $webhookIdentifier = $profileConfig['webhook']['class'];

                    if (!in_array($webhookIdentifier, $webhooksIdentifiers, true) && !in_array($webhookIdentifier, $webhooksClasses, true)) {
                        throw new \InvalidArgumentException("Profile '{$profileName}' references undefined webhook identifier: {$webhookIdentifier}");
                    }
                }

                // check url validity
                if (isset($profileConfig['webhook']['url']) && !filter_var($profileConfig['webhook']['url'], FILTER_VALIDATE_URL)) {
                    throw new \InvalidArgumentException("Profile '{$profileName}' has invalid webhook URL: {$profileConfig['webhook']['url']}");
                }
            }

        // validate payloads
            $payloadsTemp = $this->payloads;
            $payloadsClasses = [];
            array_walk_recursive($payloadsTemp, function($value) use (&$payloadsClasses) {
                $payloadsClasses[] = $value;
            });
            $payloadsClasses = array_unique($payloadsClasses);

            // check if payload classes exist
            foreach ($payloadsClasses as $class) {
                if (!class_exists($class)) {
                    throw new \InvalidArgumentException("Payload class does not exist: {$class} (payloads config).");
                    return false;
                }
            }

        // validate messages
            $messagesTemp = $this->messages;
            $messagesClasses = [];
            array_walk_recursive($messagesTemp, function($value) use (&$messagesClasses) {
                $messagesClasses[] = $value;
            });
            $messagesClasses = array_filter(array_unique($messagesClasses));

            // check if message classes exist
            foreach ($messagesClasses as $class) {
                if (!class_exists($class)) {
                    throw new \InvalidArgumentException("Message class does not exist: {$class} (messages config).");
                    return false;
                }
            }
            
        // all validations passed
        return true;
    }
}
