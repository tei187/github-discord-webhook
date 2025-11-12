<?php

namespace tei187\GitDisWebhook\Interfaces;

/**
 * Defines the interface for parsing and accessing data from a webhook payload.
 * 
 * @package tei187\GitDisWebhook\Interfaces
 */
interface PayloadInterface {
    /**
     * Gets the dotted path representation of the webhook event.
     *
     * @return string The dotted path representation.
     */
    public function getDottedEventPath(): string;

    /**
     * Gets the array representation of the webhook event path.
     *
     * @return array The array representation of the event path.
     */
    public function getEventPath(): array;

    /**
     * Gets the value of a specific header from the webhook payload.
     *
     * @param string $header The name of the header to retrieve.
     * @return string|null The value of the header, or null if not found.
     */
    public function getHeader(string $header): ?string;

    /**
     * Gets the raw body of the webhook payload.
     *
     * @return string The raw body of the payload.
     */
    public function getRawBody(): string;

    /**
     * Sets the raw payload data and parses it.
     *
     * @param string $payload The raw JSON payload string.
     * @return $this The current instance of the Payload class.
     */
    public function setData(string $payload): self;

    /**
     * Sets the origin of the payload, if applicable.
     *
     * @return $this The current instance of the Payload class.
     */
    public function setOrigin(): self;

    /**
     * Sets the event path components.
     *
     * @param array $event An array containing the event, subject, and action components.
     * @return $this The current instance of the Payload class.
     */
    public function setEvent(array $event): self;
}