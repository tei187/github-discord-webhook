<?php

namespace tei187\GitDisWebhook\Factories;

use tei187\GitDisWebhook\Handlers\PayloadResolver;
use tei187\GitDisWebhook\Handlers\ResponseHandler;
use tei187\GitDisWebhook\Interfaces\Payload as PayloadInterface;

class PayloadFactory
{
    /**
     * Holds the configuration for the payload classes.
     *
     * @var array
     */
    private $config;

    public function __construct() {
        $this->config = require __DIR__ . '/../../config/payloads.php';
    }

    /**
     * Creates a new payload instance based on the provided event.
     *
     * @param string|null $event   The event for which to create the payload.
     * @param string      $payload The data to be included in the payload.
     * @return PayloadInterface|void The payload instance typical for given event, otherwise a ResponseHandler void with error message.
     */
    public function createPayload(?string $event, string $payload) {
        $event ?: ResponseHandler::send("No event provided in HTTP_X_GITHUB_EVENT header.", "error", 400);

        // resolve payload type here
        $payloadClass = PayloadResolver::find($payload, $this->config);

        return $payloadClass !== null
            ? new $payloadClass($payload)
            : ResponseHandler::send("No matching payload class found for event: $event.\n ", "error", 422); 
    }
}