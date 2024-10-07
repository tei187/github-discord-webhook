<?php

namespace tei187\GitDisWebhook\Interfaces;

/**
 * Defines the interface for parsing and accessing data from a webhook payload.
 */
interface Payload {
    /**
     * Parses the given webhook payload string and returns the Payload instance.
     *
     * @param string $payload The raw webhook payload string.
     * @return $this The Payload instance.
     */
    public function parse(string $payload): self;

    /**
     * Gets the dotted path representation of the webhook event.
     *
     * @return string The dotted path representation.
     */
    public function getDottedPath(): string;

    /**
     * Gets the array representation of the webhook event path.
     *
     * @return array The array representation of the event path.
     */
    public function getEventPath(): array;
}