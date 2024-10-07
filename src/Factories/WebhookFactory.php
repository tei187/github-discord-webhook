<?php

namespace tei187\GitDisWebhook\Factories;

use tei187\GitDisWebhook\Helpers\UrlParser;
use tei187\GitDisWebhook\Handlers\ResponseHandler;
use tei187\GitDisWebhook\Interfaces\Payload as PayloadInterface;
use tei187\GitDisWebhook\Interfaces\Webhook as WebhookInterface;
use tei187\GitDisWebhook\Webhook;

/**
 * Provides a factory for creating webhook profiles based on configuration settings.
 *
 * The WebhookFactory class is responsible for creating instances of the `Profile` class, which represent the configuration
 * for a specific webhook. It reads the webhook configuration from a file and uses the current request URI to determine
 * the appropriate webhook configuration to use.
 */
class WebhookFactory
{
    /**
     * Webhook's configuration array from `config\webhooks.php`.
     * 
     * @var array
     */
    private $config;

    /**
     * Webhook's name extracted from request URI, same as the main item key in the `config\webhooks.php` file.
     * 
     * @var string
     */
    private $name;

    /**
     * Constructs a new WebhookFactory instance.
     */
    public function __construct()
    {
        // Get the webhook configuration from the config file
        $this->config = require __DIR__ . '/../../config/webhooks.php';

        // Get the webhook name from the URL path
        $this->name = UrlParser::extractWebhookName($_SERVER['REQUEST_URI']);

        if (!$this->name || $this->name === null) {
            ResponseHandler::send("No webhook name could be found in call path.", "error", 422);
        }
    }

    /**
     * Creates a new webhook profile based on the configured webhook settings.
     *
     * @param  PayloadInterface $payload The payload data of Payload interface to use for creating the webhook profile.
     * @return WebhookInterface|void The created webhook profile of Webhook interface or ResponseHandler void.
     */
    public function createWebhook(PayloadInterface $payload): WebhookInterface
    {
        if (!isset($this->config[$this->name])) {
            ResponseHandler::send("No webhook configuration found for name: {$this->name}. Check the passed URL.", "error", 422);
        }
        
        $webhookConfig = $this->config[$this->name];
        return new Webhook(
            $this->name,
            $webhookConfig['url'] ?? null,
            $webhookConfig['secret'] ?? null,
            $webhookConfig['repos'] ?? [],
            $payload
        );
    }
}