<?php

namespace tei187\GitDisWebhook\Interfaces;

use tei187\GitDisWebhook\Interfaces\Payload as PayloadInterface;

/**
 * Defines the interface for a webhook handler.
 */
interface Webhook {
    /**
     * Validates the profile of the incoming webhook request.
     *
     * This method should check that the request is coming from a trusted source, such as by verifying the sender's
     * identity or checking that the request is coming from an expected domain.
     *
     * @return bool True if the profile is valid, false otherwise.
     */
    public function validateProfile(): bool;

    /**
     * Validates the signature of the incoming webhook request.
     *
     * This method should check that the request has a valid signature, such as by verifying a digital signature or
     * a shared secret.
     *
     * @return bool True if the signature is valid, false otherwise.
     */
    public function validateSignature(): bool;

    /**
     * Validates the event type of the incoming webhook request.
     *
     * This method should check that the request is for an expected event type, such as by checking the event name or
     * type in the payload data.
     *
     * @return bool True if the event type is valid, false otherwise.
     */
    public function validateEvent(): bool;

    /**
     * Sets the payload data for the incoming webhook request.
     *
     * This method should extract the relevant data from the request and store it in a `PayloadInterface` object, which
     * can then be used by other parts of the application to process the webhook event.
     *
     * @param PayloadInterface $payload The payload data for the incoming webhook request.
     */
    public function setPayload(PayloadInterface $payload): self;
}