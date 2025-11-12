<?php

namespace tei187\GitDisWebhook\Interfaces;

use tei187\GitDisWebhook\Interfaces\PayloadInterface;

/**
 * Defines the interface for a factory that creates payload instances.
 * 
 * @package tei187\GitDisWebhook\Interfaces
 */
interface PayloadFactoryInterface {
    public function createPayload(string $payload): PayloadInterface;
}