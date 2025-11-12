<?php

namespace tei187\GitDisWebhook\Interfaces;

use tei187\GitDisWebhook\Interfaces\Payload as PayloadInterface;

/**
 * Defines the interface for a factory that creates payload instances.
 * 
 * @package tei187\GitDisWebhook\Interfaces
 */
interface PayloadFactory {
    public function createPayload(string $payload): PayloadInterface;
}