<?php

namespace tei187\GitDisWebhook\Factories;

use tei187\GitDisWebhook\ValueObjects\Config;
use tei187\GitDisWebhook\Interfaces\PayloadFactoryInterface;
use tei187\GitDisWebhook\Interfaces\PayloadInterface;
use tei187\GitDisWebhook\Handlers\ResponseHandler;

/**
 * Provides a factory for creating payload instances based on the provided event.
 *
 * The PayloadFactory is responsible for resolving the appropriate payload class for a given event and creating an instance of
 * that class with the provided payload data.
 * 
 * @abstract
 * @package tei187\GitDisWebhook\Factories
 */
abstract class PayloadFactoryAbstract implements PayloadFactoryInterface
{
    /**
     * Holds the configuration for the app.
     *
     * @var Config
     */
    protected Config $config;

    public function __construct(?Config $config = null) {
        $this->config = $config ?? new Config();
    }

    /**
     * Creates a new payload instance based. Requires resolving the payload class first.
     * 
     * @see resolvePayloadClass()
     *
     * @param string      $payload The data to be included in the payload.
     * @return PayloadInterface|void The payload instance typical for given event, otherwise a ResponseHandler void with error message.
     */
    public function createPayload(string $payload): PayloadInterface {
        $config = $this->config->payloads;

        $payloadClass = $this->resolvePayloadClass($payload, $config);

        return $payloadClass !== null
            ? new $payloadClass($payload)
            : ResponseHandler::send("No matching payload class found for this event.\n ", "error", 422); 
    }

    /** 
     * Resolves the payload class based on the provided payload and configuration.
     *
     * This method should be implemented by concrete factories to provide the
     * logic for determining the appropriate payload class for a given event.
     *
     * @param string $payload The data to be included in the payload.
     * @abstract
     */
    abstract protected function resolvePayloadClass(string $payload): ?string;
}