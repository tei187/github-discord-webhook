<?php

namespace tei187\GitDisWebhook\Factories;

use tei187\GitDisWebhook\Helpers\UrlParser;
use tei187\GitDisWebhook\ValueObjects\Config;
use tei187\GitDisWebhook\Helpers\WebhookDetector;
use tei187\GitDisWebhook\Interfaces\WebhookInterface;

/**
 * Provides a factory for creating webhook instances based on the provided name.
 * 
 * The WebhookFactory class is responsible for creating webhook instances either
 * by class name, registry key, or through auto-detection based on the request URI.
 * It utilizes the application configuration to resolve the appropriate webhook class.
 * 
 * @package tei187\GitDisWebhook\Factories
 */
class WebhookFactory {
    protected Config $config;

    public function __construct(?Config $config = null) {
        $this->config = $config ?? new Config();
    }

    /**
     * Creates a new webhook instance based on the provided name.
     * 
     * $name can be either a class name or a registry key. If null or no match is found, auto-detection is attempted,
     * per the rules defined in the `webhooks.php` configuration array, 'detectors' subarray.
     *
     * @param string|null $profileName The profile name to use for the webhook configuration.
     * @param string|null $webhookName The name of the webhook to create. Can be a class name, registry key, or null for auto-detection.
     * @return WebhookInterface|null The webhook instance, or null if no matching webhook was found.
     */
    public function createWebhook(?string $profileName = null, ?string $webhookName = null): ?WebhookInterface {
        // determine profile name
        if($profileName === null) {
            $profileName = UrlParser::extractWebhookName($_SERVER['REQUEST_URI'] ?? '');
        }

        // determine webhook class by class name, registry key or auto-detection
        $webhook = match(true) {
            class_exists($webhookName) && in_array($webhookName, $this->config->webhooks['registry']) => $webhookName,
            key_exists($webhookName, $this->config->webhooks['registry']) => $this->config->webhooks['registry'][$webhookName],
            default => WebhookDetector::detect($this->config->profiles[$profileName]['webhook']['url'] ?? '', $this->config),
        };

        // instantiate webhook if class exists
        if ($webhook && class_exists($webhook)) {
            return new $webhook($profileName, $this->config);
        }

        // no matching webhook found
        return null;
    }
}
