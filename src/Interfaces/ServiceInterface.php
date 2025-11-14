<?php

namespace tei187\GitDisWebhook\Interfaces;

use tei187\GitDisWebhook\ValueObjects\Config;
use tei187\GitDisWebhook\Interfaces\WebhookInterface;
use tei187\GitDisWebhook\Interfaces\PayloadInterface;
use tei187\GitDisWebhook\Interfaces\MessageInterface;
use tei187\GitDisWebhook\Interfaces\MessageFactoryInterface;

/**
 * Defines the interface for a service that handles webhook requests.
 * 
 * @package tei187\GitDisWebhook\Interfaces
 */
interface ServiceInterface {
    /**
    * Validates the provided payload.
    *
    * This method checks if the given payload is valid according to the service's or platform's specific rules.
    * If you really don't want to validate anything, just return true (bad idea, seen worse).
    *
    * @param string $payload The payload to validate.
    * @return bool True if the payload is valid, false otherwise.
    */
    public function validatePayload(string $payload): bool;
    
    /**
     * Validates the event based on the compiled allowed events from configuration and overrides.
     * Requires that the service name, webhook, and payload are set.
     *
     * @return bool True if the event is valid, false otherwise.
     */
    public function validateEvent(): bool;

    /**
     * Sets the payload for the service.
     *
     * @param PayloadInterface|string|null $payload The payload to set. Directly assigns if PayloadInterface, or instantiates if string.
     * @param string|null $payloadClass The class name to instantiate if payload is a string. Required if `$payload` is string, otherwise irrelevant.
     * @return self
     */
    public function setPayload(PayloadInterface|string|null $payload, ?string $payloadClass = null): self;

    /**
    * Sets the webhook for the service.
    *
    * @param WebhookInterface|null $webhook The webhook to set.
    * @return self
    */
    public function setWebhook(?WebhookInterface $webhook): self;

    /**
     * Sets the configuration for the service.
     *
     * @param array|object $config The configuration to set.
     * @return self
     */
    public function setConfig(Config $config): self;

    /**
     * Sets the payload factory for the service.
     *
     * @param string|null $payloadFactoryClass The payload factory class to set.
     * @return self
     */
    public function setPayloadFactory(?string $payloadFactoryClass = null): self;

    /**
     * Sets the payload through the payload factory.
     *
     * @param string $payload The raw payload data.
     * @return self
     */
    public function setPayloadThroughFactory(string $payload): self;

    /**
     * Sets the message instance.
     *
     * @param MessageInterface|null $message The message instance to set.
     * @return $this
     */
    public function setMessage(?MessageInterface $message = null): self;

    /**
     * Sets the message factory instance.
     *
     * @param MessageFactoryInterface|null $messageFactory The message factory instance to set.
     * @return $this
     */
    public function setMessageFactory(?MessageFactoryInterface $messageFactory = null): self;

    /**
     * Creates and sets the message instance through the message factory.
     *
     * @param string $event The event that triggered the message.
     * @return self
     */
    public function setMessageThroughFactory(string $event): self;

    /**
     * Gets the repository name from the payload in config.
     * 
     * Adaptable for each service/platform implementation. If such structure is not applicable, return `null`.
     *
     * @return string|null The repository name, or null if not available.
     */
    public function getRepositoryName(): ?string;

    /**
     * Gets the repository branch from the payload in config.
     * 
     * Adaptable for each service/platform implementation. If such structure is not applicable, return `null`.
     *
     * @return string|null The repository branch, or null if not available.
     */
    public function getRepositoryBranch(): ?string;
}
