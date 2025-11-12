<?php

namespace tei187\GitDisWebhook\Interfaces;

use tei187\GitDisWebhook\Interfaces\Service as ServiceInterface;
use tei187\GitDisWebhook\Interfaces\Payload as PayloadInterface;

/**
 * Defines the interface for a webhook handler.
 * 
 * @package tei187\GitDisWebhook\Interfaces
 */
interface Webhook {
    /**
     * Checks if the webhook supports the given service.
     * 
     * @param ServiceInterface|string $service The service to check.
     * @return bool True if the service is supported, false otherwise.
     */
    public function supportsService(ServiceInterface|string $service): bool;

    /**
     * Checks if the webhook supports the given repository.
     * 
     * @param string $repository The repository to check.
     * @return bool True if the repository is supported, false otherwise.
     */
    public function supportsRepository(string $repository): bool;

    /**
     * Sends the payload to the webhook URL.
     * 
     * @param array $payload The payload to send.
     * @return bool True if the payload was sent successfully, false otherwise.
     */
    public function send(array $payload): bool;

    /**
     * Sets the payload for the webhook.
     * 
     * @param PayloadInterface|null $payload The payload to set.
     * @return self The current instance.
     */
    public function setPayload(?PayloadInterface $payload): self;

    /**
     * Validates the profile of the webhook.
     * 
     * This should check if the webhook profile is valid according to its configuration.
     * 
     * If no validation is needed, fill with `return true;`.
     * 
     * @return bool True if the profile is valid, false otherwise.
     */
    public function validateProfile(): bool;

    /**
     * Validates the signature of the payload.
     * 
     * This should compare the provided signature with a computed signature based
     * on the payload/request and a secret, probably in unison with the controlling
     * service.
     * 
     * If no validation is needed, fill with `return true;`.
     * 
     * @return bool True if the signature is valid, false otherwise.
     */
    public function validateSignature(): bool;
}
