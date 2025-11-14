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
        // validate individual sections
        // throw errors on any failure
        $this->validateKeys();
        $this->validateServices();
        $this->validateWebhooks();
        $this->validateProfiles();
        $this->validatePayloads();
        $this->validateMessages();
            
        // all validations passed
        return true;
    }

    /**
     * Validates that all required configuration keys are present.
     * 
     * @param bool $throw Whether to throw exceptions on validation errors. Default is true.
     * @return bool True if all required keys are present.
     * @throws \ErrorException If any required configuration key is missing.
     */
    public function validateKeys($throw = true): bool {
        $requiredKeys = [
            'allowed',
            'messages',
            'payloads',
            'profiles',
            'profiles_defaults',
            'services',
            'webhooks',
        ];

        foreach ($requiredKeys as $key) {
            if (!property_exists($this, $key)) {
                if($throw) {
                    throw new \ErrorException("Missing required configuration key: {$key}. Check if appropriate config files exist in 'config' directory.");
                }
                return false;
            }
        }

        return true;
    }

    /**
     * Validates the services configuration to ensure required keys and class references exist.
     * 
     * @param bool $throw Whether to throw exceptions on validation errors. Default is true.
     * @return bool True if the services configuration is valid.
     * @throws \InvalidArgumentException If class references in the services configuration are invalid.
     */
    public function validateServices($throw = true): bool {
        $servicesClasses = $this->services['registry'] ?? [];
        $servicesIdentifiers = array_keys($servicesClasses);

        // check if services registry classes exist
        foreach ($servicesClasses as $serviceName => $class) {
            if (!class_exists($class)) {
                if($throw) {
                    throw new \InvalidArgumentException("Service class does not exist: {$class} (registered as: {$serviceName})");
                }
                return false;
            }
        }

        // check if services detectors reference valid service identifiers
        foreach ($this->services['detectors'] as $serviceName => $config) {
            if (!in_array($serviceName, $servicesIdentifiers, true)) {
                if($throw) {
                    throw new \InvalidArgumentException("Service detector references undefined service identifier: {$serviceName}");
                }
                return false;
            }
        }

        return true;
    }

    /**
     * Validates the profiles configuration to ensure referenced services and webhooks exist.
     * 
     * @param bool $throw Whether to throw exceptions on validation errors. Default is true.
     * @return bool True if the profiles configuration is valid.
     * @throws \InvalidArgumentException If referenced services or webhooks in the profiles configuration are invalid.
     */
    public function validateProfiles($throw = true): bool {
        $servicesClasses = $this->services['registry'] ?? [];
        $servicesIdentifiers = array_keys($servicesClasses);

        $webhooksClasses = $this->webhooks['registry'] ?? [];
        $webhooksIdentifiers = array_keys($webhooksClasses);

        foreach ($this->profiles as $profileName => $profileConfig) {
            // check if services in profile reference valid service identifiers or is a service class name
            if (isset($profileConfig['services']) && is_array($profileConfig['services'])) {
                foreach ($profileConfig['services'] as $serviceIdentifier) {
                    if (!in_array($serviceIdentifier, $servicesIdentifiers, true) && !in_array($serviceIdentifier, $servicesClasses, true)) {
                        if($throw) {
                            throw new \InvalidArgumentException("Profile '{$profileName}' references undefined service identifier: {$serviceIdentifier}");
                        }
                        return false;
                    }
                }
            }

            // check if webhook in profile references valid webhook identifier or is a webhook class name
            if (isset($profileConfig['webhook']['class'])) {
                $webhookIdentifier = $profileConfig['webhook']['class'];

                if (!in_array($webhookIdentifier, $webhooksIdentifiers, true) && !in_array($webhookIdentifier, $webhooksClasses, true)) {
                    if($throw) {
                        throw new \InvalidArgumentException("Profile '{$profileName}' references undefined webhook identifier: {$webhookIdentifier}");
                    }
                    return false;
                }
            }

            // check url validity
            if (isset($profileConfig['webhook']['url']) && !filter_var($profileConfig['webhook']['url'], FILTER_VALIDATE_URL)) {
                if($throw) {
                    throw new \InvalidArgumentException("Profile '{$profileName}' has invalid webhook URL: {$profileConfig['webhook']['url']}");
                }
                return false;
            }
        }

        return true;
    }

    /**
     * Validates the webhooks configuration to ensure required keys and class references exist.
     * 
     * @param bool $throw Whether to throw exceptions on validation errors. Default is true.
     * @return bool True if the webhooks configuration is valid.
     * @throws \InvalidArgumentException If class references in the webhooks configuration are invalid.
     */
    public function validateWebhooks($throw = true): bool {
        $webhooksClasses = $this->webhooks['registry'] ?? [];
        $webhooksIdentifiers = array_keys($webhooksClasses);

        // check if webhooks registry classes exist
        foreach ($webhooksClasses as $webhookName => $class) {
            if (!class_exists($class)) {
                if($throw) {
                    throw new \InvalidArgumentException("Webhook class does not exist: {$class} (registered as: {$webhookName})");
                }
                return false;
            }
        }

        // check if webhooks detectors reference valid webhook identifiers
        foreach ($this->webhooks['detectors'] as $webhookName => $config) {
            if (!in_array($webhookName, $webhooksIdentifiers, true)) {
                if($throw) {
                    throw new \InvalidArgumentException("Webhook detector references undefined webhook identifier: {$webhookName}");
                }
                return false;
            }
        }

        return true;
    }

    /**
     * Validates the payloads configuration to ensure class references exist.
     * 
     * @param bool $throw Whether to throw exceptions on validation errors. Default is true.
     * @return bool True if the payloads configuration is valid.
     * @throws \InvalidArgumentException If class references in the payloads configuration are invalid.
     */
    public function validatePayloads($throw = true): bool {
        $payloadsTemp = $this->payloads;
        $payloadsClasses = [];
        array_walk_recursive($payloadsTemp, function($value) use (&$payloadsClasses) {
            $payloadsClasses[] = $value;
        });
        $payloadsClasses = array_unique($payloadsClasses);

        // check if payload classes exist
        foreach ($payloadsClasses as $class) {
            if (!class_exists($class)) {
                if ($throw) {
                    throw new \InvalidArgumentException("Payload class does not exist: {$class} (payloads config).");
                }
                return false;
            }
        }

        return true;
    }

    /**
     * Validates the messages configuration to ensure class references exist.
     * 
     * @param bool $throw Whether to throw exceptions on validation errors. Default is true.
     * @return bool True if the messages configuration is valid.
     * @throws \InvalidArgumentException If class references in the messages configuration are invalid.
     */
    public function validateMessages($throw = true): bool {
        $messagesTemp = $this->messages;
        $messagesClasses = [];
        array_walk_recursive($messagesTemp, function($value) use (&$messagesClasses) {
            $messagesClasses[] = $value;
        });
        $messagesClasses = array_filter(array_unique($messagesClasses));

        // check if message classes exist
        foreach ($messagesClasses as $class) {
            if (!class_exists($class)) {
                if ($throw) {
                    throw new \InvalidArgumentException("Message class does not exist: {$class} (messages config).");
                }
                return false;
            }
        }

        return true;
    }
}
